<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['category_id', 'title', 'short_description', 'description', 'slug', 'price_irr', 'sales_mode', 'is_active', 'is_featured', 'is_demo', 'sort_order'])]
class Product extends Model
{
    use HasLocalizedFields;

    protected function casts(): array
    {
        return [
            'title' => 'array', 'short_description' => 'array', 'description' => 'array',
            'is_active' => 'boolean', 'is_featured' => 'boolean', 'is_demo' => 'boolean', 'price_irr' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function caseStudies(): BelongsToMany
    {
        return $this->belongsToMany(CaseStudy::class);
    }

    public function effectiveSalesMode(): string
    {
        return $this->sales_mode === 'inherit' ? app(SiteSettings::class)->salesMode() : $this->sales_mode;
    }

    public function supportsDirectSale(): bool
    {
        return in_array($this->effectiveSalesMode(), ['direct', 'both'], true) && $this->price_irr !== null && $this->price_irr > 0;
    }

    public function supportsQuote(): bool
    {
        return in_array($this->effectiveSalesMode(), ['quote', 'both'], true);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_active && (! $this->is_demo || app(SiteSettings::class)->demoMode());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->active()->when(! app(SiteSettings::class)->demoMode(), fn (Builder $items) => $items->where('is_demo', false));
    }
}
