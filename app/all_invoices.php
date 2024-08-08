<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class all_invoices extends Model
{
    use SoftDeletes;
    protected $table = 'new_invoices';

    public function data()
    {
        return $this->hasMany(new_invoices_data::class,'invoice_id','id');
    }

    public function payment_calculations()
    {
        return $this->hasMany(invoice_payment_calculations::class, 'invoice_id','id');
    }

}
