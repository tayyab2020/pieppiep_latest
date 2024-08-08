<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class curtain_variables extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function model()
    {
        return $this->belongsTo('App\product_models');
    }

}
