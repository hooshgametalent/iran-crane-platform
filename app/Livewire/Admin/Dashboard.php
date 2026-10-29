<?php

namespace App\Livewire\Admin;

use App\Models\Crane;
use App\Models\RentalBooking;
use App\Models\RentalQuote;
use App\Models\RentalRequest;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the current marketplace totals and newest rental requests.
     */
    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'activeCraneCount' => Crane::query()->where('status', 'active')->count(),
            'openRequestCount' => RentalRequest::query()->where('status', 'open')->count(),
            'pendingQuoteCount' => RentalQuote::query()->where('status', 'sent')->count(),
            'confirmedBookingCount' => RentalBooking::query()->where('status', 'confirmed')->count(),
            'latestRequests' => RentalRequest::query()
                ->with(['city.province', 'craneType'])
                ->latest()
                ->limit(8)
                ->get(),
        ])->title('پنل مدیریت');
    }
}
