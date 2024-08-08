<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class customers_details extends Model
{
    use SoftDeletes;
    protected $table = 'customers_details';

    protected $fillable = ['mollie_customer_id','mollie_method'];
}
