<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Region extends Model
{
  use Translatable;

  protected $table = 'region';
  protected $guarded = [];
  protected array $translatable = ['region'];


  public function country()
  {
    return $this->belongsTo(Country::class, 'country_id');
  }

  public function cities()
  {
    return $this->hasMany(City::class, 'region_id')->whereNull('deleted_at');
  }
}
