<?php

namespace App\Livewire;

use App\Models\Crane;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CraneDetail extends Component
{
    public Crane $crane;

    /**
     * Load an active crane for public display.
     */
    public function mount(Crane $crane): void
    {
        abort_unless($crane->status === 'active', 404);

        $this->crane = $crane->load([
            'craneType',
            'organization',
            'images' => fn ($query) => $query->orderBy('sort_order'),
            'serviceAreas.city',
            'serviceAreas.province',
        ]);
    }

    /**
     * Render the public crane detail page.
     */
    public function render(): View
    {
        return view('livewire.crane-detail')->title($this->crane->model ?? 'جزئیات جرثقیل');
    }
}
