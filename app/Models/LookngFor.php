<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class LookngFor extends Model
{
  use Translatable;
  protected $table = 'looking_for';

  protected $fillable = [
    'looking_for',
    'deleted_at',
    'created_at',
    'id'
  ];

  protected array $translatable = ['looking_for'];
}
