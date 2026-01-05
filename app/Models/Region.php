<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{

  protected $table = 'region';
  protected $guarded;

  public function country()
  {
    return $this->belongsTo(Country::class, 'country_id');
  }

  public function cities()
  {
    return $this->hasMany(City::class, 'region_id')->whereNull('deleted_at');
  }
}
