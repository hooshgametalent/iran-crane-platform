<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['crane_id', 'path', 'alt_text', 'sort_order', 'is_cover'])]
class CraneImage extends Model
{
    /**
     * Crane that owns this image.
     *
     * @return BelongsTo<Crane, $this>
     */
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Crane::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
        ];
    }
}
