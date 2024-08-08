<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class new_orders_calculations extends Model
{
    protected $table = 'new_orders_calculations';
    public $timestamps = false;

    public function children()
    {
        return $this->hasMany(new_orders_calculations::class, 'order_id', 'order_id')->where('parent_row', $this->calculator_row);
    }

    public function hasChildWithNonEmptyBoxQuantity()
    {
        return $this->children->contains(function ($child) {
            return !empty($child->box_quantity);
        });
    }
}
