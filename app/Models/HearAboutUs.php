<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class HearAboutUs extends Model
{
    use Translatable;
    protected $guarded;
    protected $table = 'hear_about_us';
    protected $fillable = [
        'platform',
        'deleted_at',
        'created_at',
        'updated_at',
        'id'
    ];
    protected array $translatable = ['platform'];
}
