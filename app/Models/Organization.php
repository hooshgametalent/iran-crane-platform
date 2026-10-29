<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'type', 'status', 'description'])]
class Organization extends Model
{
    /**
     * Users who belong to this organization.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Cranes owned by this organization.
     *
     * @return HasMany<Crane, $this>
     */
    public function cranes(): HasMany
    {
        return $this->hasMany(Crane::class);
    }

    /**
     * Quotes submitted by this organization.
     *
     * @return HasMany<RentalQuote, $this>
     */
    public function rentalQuotes(): HasMany
    {
        return $this->hasMany(RentalQuote::class);
    }
}
