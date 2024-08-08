<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class organizations extends Model
{
    use SoftDeletes;
    protected $table = 'organizations';
    protected $casts = ['deleted_at'];

    protected $fillable = ['company_name','Type','registration_number','photo','compressed_photo','web','city','address','phone','email','postcode','business_name','tax_number','bank_account','counter','counter_order','counter_invoice','quotation_prefix','order_prefix','invoice_prefix','quotation_client_id','order_client_id','invoice_client_id','live','counter_customer_number','service_general_ledger','prestashop_url','prestashop_access_key','terminal_zipcode','terminal_longitude','terminal_latitude','terminal_radius','terminal_city', 'shop_name', 'subdomain', 'shop_description'];

    // Define the one-to-many relationship with user_organizations
    public function userOrganizations()
    {
        return $this->hasMany(user_organizations::class);
    }

    // Define the many-to-one relationship with User through user_organizations
    public function users()
    {
        return $this->hasManyThrough(User::class, user_organizations::class, 'organization_id', 'id', 'id', 'user_id');
    }

    // Get requests of retailers organizations submitted to this supplier organization
    public function supplierRequests()
    {
        return $this->hasMany(retailers_requests::class, 'supplier_organization');
    }

    // Get requests of retailers organizations submitted by this retailer organization
    public function retailerRequests()
    {
        return $this->hasMany(retailers_requests::class, 'retailer_organization');
    }

    
}
