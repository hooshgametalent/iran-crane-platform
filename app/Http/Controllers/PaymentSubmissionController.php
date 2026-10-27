<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\PaymentSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentSubmissionController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $isOwner = $order->user_id !== null && $request->user()?->id === $order->user_id;
        abort_unless($isOwner || session('order_access.'.$order->id), 403);
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:120'], 'payer_name' => ['nullable', 'string', 'max:120'],
            'method' => ['required', 'in:bank_transfer,cash_deposit'], 'amount_irr' => ['required', 'integer', 'min:1'], 'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($order, $data, $request): void {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! $order->canTransitionTo('payment_submitted') || PaymentSubmission::query()->where('order_id', $order->id)->exists()) {
                throw ValidationException::withMessages(['payment' => __('site.validation.payment_not_allowed')]);
            }
            $payment = PaymentSubmission::query()->create([...$data, 'order_id' => $order->id, 'status' => 'submitted']);
            $from = $order->status;
            $order->update(['status' => 'payment_submitted']);
            OrderEvent::query()->create(['order_id' => $order->id, 'payment_submission_id' => $payment->id, 'actor_id' => $request->user()?->id, 'event' => 'payment_submitted', 'from_status' => $from, 'to_status' => $order->status]);
        });

        return back()->with('success', __('site.messages.payment_submitted'));
    }
}
