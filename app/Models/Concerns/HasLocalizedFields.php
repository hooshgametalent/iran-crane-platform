<?php

namespace App\Models\Concerns;

trait HasLocalizedFields
{
    public function localized(string $field, ?string $locale = null): string
    {
        $values = $this->getAttribute($field);
        $locale ??= app()->getLocale();

        if (! is_array($values)) {
            return (string) $values;
        }

        foreach (array_unique([$locale, 'fa', 'en', 'ar']) as $candidate) {
            if (filled($values[$candidate] ?? null)) {
                return (string) $values[$candidate];
            }
        }

        return '';
    }
}
