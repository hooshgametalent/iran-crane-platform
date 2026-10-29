<?php

namespace App\Livewire\Owner;

use App\Models\City;
use App\Models\Crane;
use App\Models\CraneServiceArea;
use App\Models\CraneType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class CraneForm extends Component
{
    public ?Crane $crane = null;

    public ?int $organizationId = null;

    public ?int $craneTypeId = null;

    public string $ownerReference = '';

    public string $manufacturer = '';

    public string $model = '';

    public ?int $manufactureYear = null;

    public ?int $capacityKg = null;

    public ?int $boomLengthMm = null;

    public string $description = '';

    /** @var array<int, int> */
    public array $serviceCityIds = [];

    public int $lockVersion = 1;

    /**
     * Load a crane for editing, or configure a new crane form.
     */
    public function mount(?Crane $crane = null): void
    {
        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);

        if ($crane === null) {
            return;
        }

        abort_unless($user->organizations()->whereKey($crane->organization_id)->exists(), 403);

        $this->crane = $crane;
        $this->organizationId = $crane->organization_id;
        $this->craneTypeId = $crane->crane_type_id;
        $this->ownerReference = $crane->owner_reference;
        $this->manufacturer = $crane->manufacturer ?? '';
        $this->model = $crane->model ?? '';
        $this->manufactureYear = $crane->manufacture_year;
        $this->capacityKg = $crane->capacity_kg;
        $this->boomLengthMm = $crane->boom_length_mm;
        $this->description = $crane->description ?? '';
        $this->serviceCityIds = $crane->serviceAreas()->whereNotNull('city_id')->pluck('city_id')->all();
        $this->lockVersion = $crane->lock_version;
    }

    /**
     * Create or update an owned crane and its city service areas.
     */
    public function save(): void
    {
        $validated = $this->validate([
            'organizationId' => ['required', 'integer', 'exists:organizations,id'],
            'craneTypeId' => ['required', 'integer', 'exists:crane_types,id'],
            'ownerReference' => ['required', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'manufactureYear' => ['nullable', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'capacityKg' => ['required', 'integer', 'min:1'],
            'boomLengthMm' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:5000'],
            'serviceCityIds' => ['required', 'array', 'min:1'],
            'serviceCityIds.*' => ['integer', 'distinct', 'exists:cities,id'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);
        abort_unless($user->organizations()->whereKey($validated['organizationId'])->exists(), 403);

        try {
            DB::transaction(function () use ($validated, $user): void {
                $attributes = [
                    'organization_id' => $validated['organizationId'],
                    'crane_type_id' => $validated['craneTypeId'],
                    'owner_reference' => $validated['ownerReference'],
                    'manufacturer' => $validated['manufacturer'] ?: null,
                    'model' => $validated['model'] ?: null,
                    'manufacture_year' => $validated['manufactureYear'],
                    'capacity_kg' => $validated['capacityKg'],
                    'boom_length_mm' => $validated['boomLengthMm'],
                    'description' => $validated['description'] ?: null,
                    'status' => 'active',
                    'updated_at' => now(),
                ];

                abort_unless($user->organizations()->whereKey($validated['organizationId'])->exists(), 403);

                if ($this->crane === null) {
                    $this->crane = Crane::query()->create($attributes);
                    $this->lockVersion = 1;
                } else {
                    $currentCrane = Crane::query()->lockForUpdate()->findOrFail($this->crane->id);
                    abort_unless($user->organizations()->whereKey($currentCrane->organization_id)->exists(), 403);

                    $updated = Crane::query()
                        ->whereKey($currentCrane->id)
                        ->where('lock_version', $this->lockVersion)
                        ->update([...$attributes, 'lock_version' => DB::raw('lock_version + 1')]);

                    if ($updated !== 1) {
                        throw ValidationException::withMessages([
                            'ownerReference' => 'این جرثقیل هم‌زمان تغییر کرده است. صفحه را تازه کنید و دوباره تلاش کنید.',
                        ]);
                    }

                    $this->lockVersion++;
                }

                $cities = City::query()->whereIn('id', $validated['serviceCityIds'])->get()->keyBy('id');
                CraneServiceArea::query()
                    ->where('crane_id', $this->crane->id)
                    ->whereNotNull('city_id')
                    ->delete();

                foreach ($validated['serviceCityIds'] as $cityId) {
                    $city = $cities->get($cityId);

                    CraneServiceArea::query()->create([
                        'crane_id' => $this->crane->id,
                        'province_id' => $city->province_id,
                        'city_id' => $city->id,
                        'area_key' => "city:{$city->id}",
                    ]);
                }
            });
        } catch (ValidationException $exception) {
            throw $exception;
        }

        $this->redirectRoute('owner.dashboard', navigate: true);
    }

    /**
     * Render the crane form and allowed lookup data.
     */
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);

        return view('livewire.owner.crane-form', [
            'organizations' => $user->organizations()->orderBy('name')->get(),
            'craneTypes' => CraneType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'cities' => City::query()->with('province:id,name')->orderBy('name')->get(),
        ])->title($this->crane === null ? 'ثبت جرثقیل' : 'ویرایش جرثقیل');
    }
}
