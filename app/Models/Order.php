<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'number', 'customer_name', 'email', 'phone', 'address', 'subtotal_irr', 'total_irr', 'status'])]
class Order extends Model
{
    public const PAYMENT_TRANSITIONS = [
        'pending_payment' => ['payment_submitted'],
        'payment_submitted' => ['paid', 'cancelled'],
    ];

    protected function casts(): array
    {
        return ['subtotal_irr' => 'integer', 'total_irr' => 'integer'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function payment(): HasOne { return $this->hasOne(PaymentSubmission::class); }
    public function events(): HasMany { return $this->hasMany(OrderEvent::class); }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::PAYMENT_TRANSITIONS[$this->status] ?? [], true);
    }
}
