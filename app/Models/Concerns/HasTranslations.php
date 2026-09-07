<?php

namespace App\Models\Concerns;

trait HasTranslations
{
    /**
     * Get translated value for an attribute
     *
     * @param string $attribute The base attribute name (e.g., 'name')
     * @param string|null $locale The locale to get translation for (defaults to current)
     * @return string|null
     */
    public function getTranslation(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $translationsAttribute = $attribute . '_translations';
        
        // Get translations from the JSON column
        $translations = $this->{$translationsAttribute};
        
        if (is_string($translations)) {
            $translations = json_decode($translations, true);
        }
        
        // Return translation if exists
        if (is_array($translations) && isset($translations[$locale]) && !empty($translations[$locale])) {
            return $translations[$locale];
        }
        
        // Fallback to default language
        $defaultLocale = \App\Helpers\LanguageHelper::getDefaultLanguage();
        if (is_array($translations) && isset($translations[$defaultLocale]) && !empty($translations[$defaultLocale])) {
            return $translations[$defaultLocale];
        }
        
        // Fallback to original attribute
        return $this->{$attribute};
    }

    /**
     * Set translation for an attribute
     *
     * @param string $attribute The base attribute name (e.g., 'name')
     * @param string $locale The locale to set translation for
     * @param string $value The translated value
     * @return self
     */
    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        $translationsAttribute = $attribute . '_translations';
        
        $translations = $this->{$translationsAttribute};
        
        if (is_string($translations)) {
            $translations = json_decode($translations, true);
        }
        
        if (!is_array($translations)) {
            $translations = [];
        }
        
        $translations[$locale] = $value;
        $this->{$translationsAttribute} = $translations;
        
        return $this;
    }

    /**
     * Set all translations for an attribute at once
     *
     * @param string $attribute The base attribute name (e.g., 'name')
     * @param array $translations Array of locale => value pairs
     * @return self
     */
    public function setTranslations(string $attribute, array $translations): self
    {
        $translationsAttribute = $attribute . '_translations';
        $this->{$translationsAttribute} = $translations;
        
        return $this;
    }

    /**
     * Get all translations for an attribute
     *
     * @param string $attribute The base attribute name (e.g., 'name')
     * @return array
     */
    public function getTranslations(string $attribute): array
    {
        $translationsAttribute = $attribute . '_translations';
        $translations = $this->{$translationsAttribute};
        
        if (is_string($translations)) {
            $translations = json_decode($translations, true);
        }
        
        return is_array($translations) ? $translations : [];
    }

    /**
     * Check if translation exists for an attribute and locale
     *
     * @param string $attribute The base attribute name
     * @param string $locale The locale to check
     * @return bool
     */
    public function hasTranslation(string $attribute, string $locale): bool
    {
        $translations = $this->getTranslations($attribute);
        return isset($translations[$locale]) && !empty($translations[$locale]);
    }

    /**
     * Get translated name (shortcut method)
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getTranslatedName(?string $locale = null): ?string
    {
        return $this->getTranslation('name', $locale);
    }

    /**
     * Get translated description (shortcut method)
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getTranslatedDescription(?string $locale = null): ?string
    {
        return $this->getTranslation('description', $locale);
    }

    /**
     * Scope to include translations in queries
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTranslations($query)
    {
        $translatable = $this->getTranslatableAttributes();
        $columns = ['*'];
        
        foreach ($translatable as $attribute) {
            $columns[] = $attribute . '_translations';
        }
        
        return $query->select($columns);
    }

    /**
     * Get list of translatable attributes
     * Override this in your model to customize
     *
     * @return array
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : ['name', 'description'];
    }
}

