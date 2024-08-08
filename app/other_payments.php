<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class other_payments extends Model
{
    use SoftDeletes;
    protected $appends = ['other_payments'];
    protected $primaryKey = 'id';

    public function getOtherPaymentsAttribute()
    {
        return 1;
    }
}
