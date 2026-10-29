<?php

namespace App\Livewire;

use App\Models\City;
use App\Models\Crane;
use App\Models\CraneType;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class CraneCatalog extends Component
{
    use WithPagination;

    public string $cityId = '';

    public string $craneTypeId = '';

    public string $minimumCapacity = '';

    /**
     * Reset pagination when a filter changes.
     */
    public function updated(): void
    {
        $this->resetPage();
    }

    /**
     * Render the active crane catalogue and its filters.
     */
    public function render(): View
    {
        $selectedCity = ctype_digit($this->cityId)
            ? City::query()->find((int) $this->cityId, ['id', 'province_id'])
            : null;

        $cranes = Crane::query()
            ->with(['craneType', 'organization', 'images'])
            ->where('status', 'active')
            ->when(
                $selectedCity !== null,
                fn (Builder $query) => $query->whereHas(
                    'serviceAreas',
                    function (Builder $areaQuery) use ($selectedCity): void {
                        $areaQuery->where('city_id', $selectedCity->id)
                            ->orWhere(function (Builder $coverageQuery) use ($selectedCity): void {
                                $coverageQuery->where('province_id', $selectedCity->province_id)
                                    ->whereNull('city_id');
                            });
                    },
                ),
            )
            ->when(
                ctype_digit($this->craneTypeId),
                fn (Builder $query) => $query->where('crane_type_id', (int) $this->craneTypeId),
            )
            ->when(
                ctype_digit($this->minimumCapacity),
                fn (Builder $query) => $query->where('capacity_kg', '>=', (int) $this->minimumCapacity),
            )
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->paginate(12);

        return view('livewire.crane-catalog', [
            'cities' => City::query()->with('province:id,name')->orderBy('name')->get(),
            'craneTypes' => CraneType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'cranes' => $cranes,
        ])->title('اجاره جرثقیل');
    }
}
