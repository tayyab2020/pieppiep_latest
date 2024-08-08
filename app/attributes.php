<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class attributes extends Model
{
	use SoftDeletes;
    public $timestamps = false;
    protected $casts = ['deleted_at'];

    public function users()
    {
    	return $this->hasMany('App\User');

    }


}