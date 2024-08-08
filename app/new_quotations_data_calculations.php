<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class new_quotations_data_calculations extends Model
{
    protected $table = 'new_quotations_data_calculations';
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(new_quotations_data::class);
    }

    public function children()
    {
        return $this->hasMany(new_quotations_data_calculations::class, 'quotation_data_id', 'quotation_data_id')->where('parent_row', $this->calculator_row);
    }

    public function hasChildWithNonEmptyBoxQuantity()
    {
        return $this->children->contains(function ($child) {
            return !empty($child->box_quantity);
        });
    }
}
