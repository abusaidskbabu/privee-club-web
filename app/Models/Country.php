<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $guarded;

    public function regions()
    {
        return $this->hasMany(Region::class, 'country_id')->whereNull('deleted_at');
    }
}
