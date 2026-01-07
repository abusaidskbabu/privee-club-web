<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceInfo extends Model
{
    use SoftDeletes;
    protected $table = 'device_info';

    protected $guarded = [];
}
