<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CustomOption extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'name',
        'value',
        'type',
        'serial',
        'status',
        'ancestor_id',
    ];

    // public function translatable()
    // {
    //     return $this->morphTo();
    // }

    // public function translations(): MorphMany
    // {
    //     return $this->morphMany(Translation::class, 'translatable');
    // }

    // // Helper to get translation by language & field
    // public function translation($languageCode, $field)
    // {
    //     return $this->translations()
    //         ->where('language_code', $languageCode)
    //         ->where('field', $field)
    //         ->first();
    // }

    // // Accessor for name by language
    // public function getNameByLang($languageCode)
    // {
    //     $translation = $this->translation($languageCode, 'name');
    //     return $translation ? $translation->value : $this->name; // fallback to default name field
    // }
}
