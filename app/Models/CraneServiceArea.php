<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['crane_id', 'province_id', 'city_id', 'area_key'])]
class CraneServiceArea extends Model
{
    /**
     * Crane that serves this area.
     *
     * @return BelongsTo<Crane, $this>
     */
    public function crane(): BelongsTo
    {
        return $this->belongsTo(Crane::class);
    }

    /**
     * Province covered by this area.
     *
     * @return BelongsTo<Province, $this>
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * City covered by this area, when coverage is city-specific.
     *
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
