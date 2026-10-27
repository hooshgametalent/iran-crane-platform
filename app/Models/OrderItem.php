<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'product_id', 'product_title', 'quantity', 'unit_price_irr', 'line_total_irr'])]
class OrderItem extends Model
{
    use HasLocalizedFields;

    protected function casts(): array
    {
        return ['product_title' => 'array', 'unit_price_irr' => 'integer', 'line_total_irr' => 'integer'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
