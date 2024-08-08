<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class payment_calculations extends Model
{
    use SoftDeletes;
    protected $appends = ['payment_calculations'];
    protected $primaryKey = 'id';

    public function getPaymentCalculationsAttribute()
    {
        return 1;
    }
}
