<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileTimer extends Model
{
  protected $table = 'profiletimers';
  protected $fillable = [
    'time',
    'stickness_level'
  ];
}
