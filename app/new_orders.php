<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class new_orders extends Model
{
    use SoftDeletes;
    protected $table = 'new_orders';

    public function features()
    {
        return $this->hasMany(new_orders_features::class, 'order_data_id','id')->where('new_orders_features.sub_feature',0);
    }

    public function sub_features()
    {
        return $this->hasMany(new_orders_features::class, 'order_data_id','id')->where('new_orders_features.sub_feature',1);
    }

    public function calculations()
    {
        return $this->hasMany(new_orders_calculations::class, 'order_id','id');
    }

    public function hasCalculationWithNonEmptyBoxQuantity()
    {
        return $this->calculations->contains(function ($calculation) {
            return !empty($calculation->box_quantity);
        });
    }
}
