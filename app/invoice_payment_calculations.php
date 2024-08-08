<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class invoice_payment_calculations extends Model
{
    use SoftDeletes;
    protected $appends = ['invoice_payment_calculations'];
    protected $primaryKey = 'id';

    public function getInvoicePaymentCalculationsAttribute()
    {
        return 1;
    }
}
