<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class universal_customers_details extends Model
{
    use SoftDeletes;
    protected $table = 'universal_customers_details';

    protected $fillable = ['city','address','phone','postcode','business_name','tax_number','quote_req_counter'];
}
