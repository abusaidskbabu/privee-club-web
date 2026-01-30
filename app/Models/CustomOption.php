<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Translatable;

class CustomOption extends Model
{
    use Translatable;
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

    protected array $translatable = ['value'];
}
