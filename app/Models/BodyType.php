<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class BodyType extends Model
{
  use Translatable;
  protected $table = 'body_types';
  protected $guarded;
  protected $fillable = [
    'body_type',
    'deleted_at',
    'created_at',
    'updated_at',
    'id'
  ];
  protected array $translatable = ['body_type'];
}
