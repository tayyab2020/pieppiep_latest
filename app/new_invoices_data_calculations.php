<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class new_invoices_data_calculations extends Model
{
    protected $table = 'new_invoices_data_calculations';
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(new_invoices_data::class);
    }

    public function children()
    {
        return $this->hasMany(new_invoices_data_calculations::class, 'invoice_data_id', 'invoice_data_id')->where('parent_row', $this->calculator_row);
    }

    public function hasChildWithNonEmptyBoxQuantity()
    {
        return $this->children->contains(function ($child) {
            return !empty($child->box_quantity);
        });
    }
}
