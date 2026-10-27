<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    private function salesAccess(Request $request): void
    {
        abort_unless($request->user()->canManageSales(), 403);
    }

    public function index(Request $request): View
    {
        $this->salesAccess($request);

        return view('admin.orders.index', ['orders' => Order::query()->with('payment')->latest()->paginate(20)]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->salesAccess($request);
        $order->load(['items', 'payment.reviewer', 'events.actor']);

        return view('admin.orders.show', compact('order'));
    }

    public function decide(Request $request, Order $order): RedirectResponse
    {
        $this->salesAccess($request);
        $data = $request->validate(['decision' => ['required', 'in:approved,cancelled'], 'note' => ['nullable', 'string', 'max:2000']]);

        DB::transaction(function () use ($order, $data, $request): void {
            $order = Order::query()->lockForUpdate()->with('payment')->findOrFail($order->id);
            if (! $order->payment || ! $order->canTransitionTo($data['decision'] === 'approved' ? 'paid' : 'cancelled') || $order->payment->status !== 'submitted') {
                throw ValidationException::withMessages(['decision' => __('site.validation.payment_decision_invalid')]);
            }
            $from = $order->status;
            $order->payment->update(['status' => $data['decision'], 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            $order->update(['status' => $data['decision'] === 'approved' ? 'paid' : 'cancelled']);
            OrderEvent::query()->create(['order_id' => $order->id, 'payment_submission_id' => $order->payment->id, 'actor_id' => $request->user()->id, 'event' => 'payment_'.$data['decision'], 'from_status' => $from, 'to_status' => $order->status, 'note' => $data['note'] ?? null]);
        });

        return back()->with('success', __('site.messages.payment_decision_saved'));
    }
}
