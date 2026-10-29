<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Province extends Model
{
    /**
     * Cities within this province.
     *
     * @return HasMany<City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Crane service areas that cover this province.
     *
     * @return HasMany<CraneServiceArea, $this>
     */
    public function craneServiceAreas(): HasMany
    {
        return $this->hasMany(CraneServiceArea::class);
    }
}
