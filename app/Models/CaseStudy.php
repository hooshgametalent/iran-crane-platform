<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'customer_name', 'summary', 'body', 'slug', 'is_published', 'is_demo', 'published_at'])]
class CaseStudy extends Model
{
    use HasLocalizedFields;

    protected function casts(): array
    {
        return ['title' => 'array', 'customer_name' => 'array', 'summary' => 'array', 'body' => 'array', 'is_published' => 'boolean', 'is_demo' => 'boolean', 'published_at' => 'datetime'];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopePubliclyVisible($query)
    {
        return $query->where('is_published', true)->when(! app(SiteSettings::class)->demoMode(), fn ($items) => $items->where('is_demo', false));
    }
}
