<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

#[Fillable(['parent_id', 'title', 'slug', 'is_active', 'is_demo', 'sort_order'])]
class Category extends Model
{
    use HasLocalizedFields;

    protected function casts(): array
    {
        return ['title' => 'array', 'is_active' => 'boolean', 'is_demo' => 'boolean'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->active()->when(! app(SiteSettings::class)->demoMode(), fn (Builder $items) => $items->where('is_demo', false));
    }

    /** @return Collection<int, self> */
    public static function publicNavigation(): Collection
    {
        if (! Schema::hasTable('categories')) {
            return collect();
        }

        $categories = self::query()->publiclyVisible()->orderBy('sort_order')->orderBy('id')->get();
        $visibleIds = $categories->modelKeys();
        $childrenByParent = $categories->groupBy(fn (self $category) => $category->parent_id !== null && in_array($category->parent_id, $visibleIds, true) ? $category->parent_id : 'root');

        $build = function (?int $parentId, array $ancestors = [], int $depth = 0) use (&$build, $childrenByParent): Collection {
            if ($depth >= 32) {
                return collect();
            }

            return $childrenByParent->get($parentId ?? 'root', collect())
                ->reject(fn (self $category) => in_array($category->id, $ancestors, true))
                ->map(function (self $category) use ($ancestors, $depth, &$build): self {
                    $category->setRelation('children', $build($category->id, [...$ancestors, $category->id], $depth + 1));

                    return $category;
                })->values();
        };

        return $build(null);
    }
}
