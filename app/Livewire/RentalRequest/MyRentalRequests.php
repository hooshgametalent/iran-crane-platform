<?php

namespace App\Livewire\RentalRequest;

use App\Models\RentalRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyRentalRequests extends Component
{
    use WithPagination;

    /**
     * Render requests created by the authenticated customer.
     */
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null && $user->roles()->where('name', 'customer')->exists(), 403);

        return view('livewire.rental-request.my-rental-requests', [
            'requests' => RentalRequest::query()
                ->with(['city.province', 'craneType'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(12),
        ])->title('درخواست‌های من');
    }
}
