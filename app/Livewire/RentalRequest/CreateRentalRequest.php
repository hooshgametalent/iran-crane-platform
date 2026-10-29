<?php

namespace App\Livewire\RentalRequest;

use App\Models\City;
use App\Models\CraneType;
use App\Models\RentalRequest;
use App\Support\JalaliDate;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Component;

class CreateRentalRequest extends Component
{
    public string $title = '';

    public string $description = '';

    public ?int $cityId = null;

    public ?int $craneTypeId = null;

    public ?int $requiredCapacityKg = null;

    public string $startsAt = '';

    public string $endsAt = '';

    public string $submissionKey = '';

    /**
     * Create a server-generated idempotency key for this form instance.
     */
    public function mount(): void
    {
        $this->submissionKey = (string) Str::uuid();
    }

    /**
     * Persist a new rental request, once per submission key.
     */
    public function submit(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'cityId' => ['required', 'integer', 'exists:cities,id'],
            'craneTypeId' => ['nullable', 'integer', 'exists:crane_types,id'],
            'requiredCapacityKg' => ['nullable', 'integer', 'min:1'],
            'startsAt' => ['required', 'string', 'max:25'],
            'endsAt' => ['required', 'string', 'max:25'],
            'submissionKey' => ['required', 'uuid'],
        ]);

        try {
            $startsAt = JalaliDate::parseForStorage($validated['startsAt']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'startsAt' => $exception->getMessage(),
            ]);
        }

        try {
            $endsAt = JalaliDate::parseForStorage($validated['endsAt']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'endsAt' => $exception->getMessage(),
            ]);
        }

        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            throw ValidationException::withMessages([
                'endsAt' => 'زمان پایان باید بعد از زمان شروع باشد.',
            ]);
        }

        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'customer')->exists(), 403);

        DB::transaction(function () use ($user, $validated, $startsAt, $endsAt): void {
            $timestamp = now();

            DB::table('rental_requests')->insertOrIgnore([
                'user_id' => $user->id,
                'organization_id' => null,
                'crane_type_id' => $validated['craneTypeId'],
                'city_id' => $validated['cityId'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?: null,
                'required_capacity_kg' => $validated['requiredCapacityKg'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'submission_key' => $validated['submissionKey'],
                'status' => 'open',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            RentalRequest::query()
                ->where('user_id', $user->id)
                ->where('submission_key', $validated['submissionKey'])
                ->firstOrFail();
        });

        $this->redirectRoute('requests.mine', navigate: true);
    }

    /**
     * Render the rental-request form.
     */
    public function render(): View
    {
        return view('livewire.rental-request.create-rental-request', [
            'cities' => City::query()->with('province:id,name')->orderBy('name')->get(),
            'craneTypes' => CraneType::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ])->title('ثبت درخواست اجاره');
    }
}
