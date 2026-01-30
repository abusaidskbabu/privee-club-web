<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class City extends Model
{
  use Translatable;
  protected $guarded;
  protected $table = 'citys';
  protected array $translatable = ['city'];

  public function region()
  {
    return $this->belongsTo(Region::class, 'region_id');
  }
}
