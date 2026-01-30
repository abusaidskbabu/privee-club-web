<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class SexOrientation extends Model
{

  use Translatable;
  protected $table = 'sexual_orientations';
  protected $guarded;
  protected $fillable = [
    'sex_orientation',
    'deleted_at',
    'created_at',
    'id'
  ];

  protected array $translatable = ['sex_orientation'];
}
