<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class messages extends Model
{
    use SoftDeletes;
    protected $table = 'messages';

}
