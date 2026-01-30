<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Zodiac extends Model
{
  use Translatable;
  protected $table = 'zodiac_signs';
  protected $guarded;
  protected $fillable = [
    'Zodiac_Signs',
    'deleted_at',
    'created_at',
    'id'
  ];
  protected array $translatable = ['Zodiac_Signs'];
}
