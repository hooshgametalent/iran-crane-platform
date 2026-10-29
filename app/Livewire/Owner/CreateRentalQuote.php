<?php

namespace App\Livewire\Owner;

use App\Models\Crane;
use App\Models\RentalQuote;
use App\Models\RentalRequest;
use App\Support\JalaliDate;
use App\Support\PricingSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Component;

class CreateRentalQuote extends Component
{
    public RentalRequest $rentalRequest;

    public ?int $craneId = null;

    public ?int $amount = null;

    public string $billingUnit = 'day';

    public ?int $transportFee = null;

    public string $expiresAt = '';

    public string $terms = '';

    public string $submissionKey = '';

    /**
     * Load an open request and create an idempotency key for the offer form.
     */
    public function mount(RentalRequest $rentalRequest): void
    {
        abort_unless($rentalRequest->status === 'open', 404);

        $this->rentalRequest = $rentalRequest;
        $this->submissionKey = (string) Str::uuid();
    }

    /**
     * Submit one compatible quote for an open rental request.
     */
    public function submit(): void
    {
        $validated = $this->validate([
            'craneId' => ['required', 'integer', 'exists:cranes,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'billingUnit' => ['required', 'in:hour,day,project'],
            'transportFee' => ['nullable', 'integer', 'min:0'],
            'expiresAt' => ['required', 'string', 'max:25'],
            'terms' => ['nullable', 'string', 'max:5000'],
            'submissionKey' => ['required', 'uuid'],
        ]);

        try {
            $expiresAt = JalaliDate::parseForStorage($validated['expiresAt']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'expiresAt' => $exception->getMessage(),
            ]);
        }

        if ($expiresAt->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'expiresAt' => 'زمان انقضای پیشنهاد باید در آینده باشد.',
            ]);
        }

        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);

        DB::transaction(function () use ($user, $validated, $expiresAt): void {
            $request = RentalRequest::query()
                ->with('city')
                ->lockForUpdate()
                ->findOrFail($this->rentalRequest->id);

            if ($request->status !== 'open') {
                throw ValidationException::withMessages([
                    'craneId' => 'این درخواست دیگر برای دریافت پیشنهاد باز نیست.',
                ]);
            }

            $replayedQuote = RentalQuote::query()
                ->where('submitted_by_user_id', $user->id)
                ->where('submission_key', $validated['submissionKey'])
                ->lockForUpdate()
                ->first();

            if ($replayedQuote !== null) {
                if ($replayedQuote->rental_request_id !== $request->id || $replayedQuote->crane_id !== $validated['craneId']) {
                    throw ValidationException::withMessages([
                        'submissionKey' => 'این فرم پیش‌تر با اطلاعات دیگری ثبت شده است. صفحه را تازه کنید و دوباره تلاش کنید.',
                    ]);
                }

                return;
            }

            $crane = Crane::query()->lockForUpdate()->findOrFail($validated['craneId']);
            abort_unless(
                $crane->status === 'active'
                && $user->organizations()->whereKey($crane->organization_id)->exists()
                && $this->canServeRequest($crane, $request),
                403,
            );

            $existingQuote = RentalQuote::query()
                ->where('rental_request_id', $request->id)
                ->where('crane_id', $crane->id)
                ->first();

            if ($existingQuote !== null && $existingQuote->submission_key !== $validated['submissionKey']) {
                throw ValidationException::withMessages([
                    'craneId' => 'برای این درخواست با این جرثقیل پیش‌تر پیشنهاد ثبت شده است.',
                ]);
            }

            $inserted = DB::table('rental_quotes')->insertOrIgnore([
                'rental_request_id' => $request->id,
                'crane_id' => $crane->id,
                'organization_id' => $crane->organization_id,
                'submitted_by_user_id' => $user->id,
                'amount' => $validated['amount'],
                'currency' => app(PricingSettings::class)->defaultCurrency(),
                'billing_unit' => $validated['billingUnit'],
                'transport_fee' => $validated['transportFee'],
                'terms' => $validated['terms'] ?: null,
                'expires_at' => $expiresAt,
                'submission_key' => $validated['submissionKey'],
                'status' => 'sent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $quote = RentalQuote::query()
                ->where('rental_request_id', $request->id)
                ->where('crane_id', $crane->id)
                ->firstOrFail();

            if ($inserted === 0 && $quote->submission_key !== $validated['submissionKey']) {
                throw ValidationException::withMessages([
                    'craneId' => 'برای این درخواست با این جرثقیل پیش‌تر پیشنهاد ثبت شده است.',
                ]);
            }
        });

        $this->redirectRoute('owner.dashboard', navigate: true);
    }

    /**
     * Render compatible cranes and the target rental request.
     */
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);

        $request = $this->rentalRequest->loadMissing(['city.province', 'craneType', 'user']);

        return view('livewire.owner.create-rental-quote', [
            'eligibleCranes' => $this->eligibleCranes($user, $request),
            'currency' => app(PricingSettings::class)->defaultCurrency(),
            'rentalRequest' => $request,
        ])->title('ارسال پیشنهاد قیمت');
    }

    /**
     * Get active owned cranes that can serve a request.
     */
    private function eligibleCranes($user, RentalRequest $request)
    {
        $organizationIds = $user->organizations()->pluck('organizations.id');

        return Crane::query()
            ->with('craneType')
            ->whereIn('organization_id', $organizationIds)
            ->where('status', 'active')
            ->when($request->crane_type_id !== null, fn ($query) => $query->where('crane_type_id', $request->crane_type_id))
            ->when($request->required_capacity_kg !== null, fn ($query) => $query->where('capacity_kg', '>=', $request->required_capacity_kg))
            ->whereHas('serviceAreas', function ($query) use ($request): void {
                $query->where('city_id', $request->city_id)
                    ->orWhere(function ($coverageQuery) use ($request): void {
                        $coverageQuery->where('province_id', $request->city->province_id)
                            ->whereNull('city_id');
                    });
            })
            ->orderBy('capacity_kg')
            ->get();
    }

    /**
     * Confirm that one locked crane remains eligible for a locked request.
     */
    private function canServeRequest(Crane $crane, RentalRequest $request): bool
    {
        if ($request->crane_type_id !== null && $crane->crane_type_id !== $request->crane_type_id) {
            return false;
        }

        if ($request->required_capacity_kg !== null && $crane->capacity_kg < $request->required_capacity_kg) {
            return false;
        }

        return $crane->serviceAreas()
            ->where(function ($query) use ($request): void {
                $query->where('city_id', $request->city_id)
                    ->orWhere(function ($coverageQuery) use ($request): void {
                        $coverageQuery->where('province_id', $request->city->province_id)
                            ->whereNull('city_id');
                    });
            })
            ->exists();
    }
}
