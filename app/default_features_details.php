<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class default_features_details extends Model
{
    use SoftDeletes;
    public $timestamps = false;
}