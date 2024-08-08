<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class retailers_requests extends Model
{
    protected $fillable = [
        'retailer_id', 'supplier_id', 'status', 'active', 'retailer_organization', 'supplier_organization'
    ];
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'organization_id', 'supplier_organization');

    }
}
