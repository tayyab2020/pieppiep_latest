<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class new_quotations extends Model
{
    use SoftDeletes;
    protected $table = 'new_quotations';

    public function data()
    {
        return $this->hasMany(new_quotations_data::class, 'quotation_id','id');
    }

    public function orders()
    {
        return $this->hasMany(new_orders::class, 'quotation_id','id')
        ->leftJoin('organizations', 'organizations.id', '=', 'new_orders.supplier_id')->select("new_orders.*","organizations.company_name");
    }

    public function invoices()
    {
        return $this->hasMany(new_invoices::class, 'quotation_id','id');
    }

    public function unseen_messages()
    {
        return $this->hasMany(client_quotation_msgs::class, 'quotation_id','id')->where("seen",0);
    }

    public function payment_calculations()
    {
        return $this->hasMany(payment_calculations::class, 'quotation_id','id');
    }

}
