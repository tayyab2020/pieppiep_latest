<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class features_details extends Model
{
    use SoftDeletes;
    public $timestamps = false;
}