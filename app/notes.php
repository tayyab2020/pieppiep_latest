<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class notes extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
}
