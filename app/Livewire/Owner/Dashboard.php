<?php

namespace App\Livewire\Owner;

use App\Models\Crane;
use App\Models\RentalRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the owner's cranes and a bounded list of potentially matching requests.
     */
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'owner')->exists(), 403);

        $organizationIds = $user->organizations()->pluck('organizations.id');
        $cranes = Crane::query()
            ->with(['craneType', 'serviceAreas'])
            ->whereIn('organization_id', $organizationIds)
            ->latest()
            ->get();
        $openRequests = RentalRequest::query()
            ->with(['city.province', 'craneType', 'quotes:id,rental_request_id,crane_id'])
            ->where('status', 'open')
            ->latest()
            ->limit(30)
            ->get()
            ->filter(fn (RentalRequest $request): bool => $this->hasEligibleCrane($cranes, $request))
            ->take(8);

        return view('livewire.owner.dashboard', [
            'cranes' => $cranes,
            'openRequests' => $openRequests,
        ])->title('پنل مالک جرثقیل');
    }

    /**
     * Determine whether an owned active crane can service a request.
     *
     * @param  \Illuminate\Support\Collection<int, Crane>  $cranes
     */
    private function hasEligibleCrane($cranes, RentalRequest $request): bool
    {
        return $cranes->contains(function (Crane $crane) use ($request): bool {
            if ($crane->status !== 'active') {
                return false;
            }

            if ($request->crane_type_id !== null && $crane->crane_type_id !== $request->crane_type_id) {
                return false;
            }

            if ($request->required_capacity_kg !== null && $crane->capacity_kg < $request->required_capacity_kg) {
                return false;
            }

            return $crane->serviceAreas->contains(function ($area) use ($request, $crane): bool {
                $hasExistingQuote = $request->quotes->contains('crane_id', $crane->id);

                return ! $hasExistingQuote
                    && ($area->city_id === $request->city_id
                        || ($area->city_id === null && $area->province_id === $request->city->province_id));
            });
        });
    }
}
