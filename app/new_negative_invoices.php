<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class new_negative_invoices extends Model
{
    use SoftDeletes;
    protected $table = 'new_invoices';

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('negative_invoice','=', 1);
    }

    public function data()
    {
        return $this->hasMany(new_invoices_data::class,'invoice_id','id');
    }

    public function payment_calculations()
    {
        return $this->hasMany(invoice_payment_calculations::class, 'invoice_id','id');
    }

}
