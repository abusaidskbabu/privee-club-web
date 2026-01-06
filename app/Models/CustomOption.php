<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
