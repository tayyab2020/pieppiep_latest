<?php

namespace App;

use Cartalyst\Stripe\Api\Prices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class price_tables extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $casts = ['deleted_at'];
    protected $table = 'price_tables';

    public function prices()
    {
        return $this->hasMany('App\prices', 'table_id', 'id');
    }
}
