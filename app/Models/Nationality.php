<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translatable;

class Nationality extends Model
{
  use Translatable;
  protected $table = 'nationalitys';
  protected $guarded = [];
  protected array $translatable = ['nationality'];
}
