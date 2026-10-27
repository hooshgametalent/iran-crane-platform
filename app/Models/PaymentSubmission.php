<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['order_id', 'reference', 'payer_name', 'method', 'amount_irr', 'note', 'status', 'reviewed_by', 'reviewed_at'])]
class PaymentSubmission extends Model
{
    protected function casts(): array
    {
        return ['amount_irr' => 'integer', 'reviewed_at' => 'datetime'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function events(): HasMany { return $this->hasMany(OrderEvent::class); }
}
