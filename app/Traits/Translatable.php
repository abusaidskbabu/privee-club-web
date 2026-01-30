<?php

namespace App\Traits;

use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

trait Translatable
{


    protected static $currentLang;

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslation($languageCode, $field)
    {
        $cacheKey = strtolower(class_basename($this)) . "_translation_{$this->id}_{$languageCode}_{$field}";

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($languageCode, $field) {
            return $this->translations()
                ->where('language_code', $languageCode)
                ->where('field', $field)
                ->value('value') ?? $this->$field;
        });
    }

    public function toArray()
    {
        $array = parent::toArray();
        $lang = static::$currentLang ?? 'da';

        foreach ($this->translatable as $field) {
            $array[$field] = $this->getTranslation($lang, $field);
        }

        return $array;
    }

    public static function setLang($lang)
    {
        static::$currentLang = $lang;
    }

    protected static function bootTranslatable()
    {
        static::retrieved(function ($model) {
            $model->makeHidden(['translations']);
        });
    }
}
