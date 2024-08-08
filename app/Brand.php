<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\organizations;

class Brand extends Model
{
    use SoftDeletes;
    protected $table = 'brands';
    protected $fillable = ['user_id','other_suppliers','other_suppliers_organizations','cat_name','cat_slug','photo','description','trademark'];
    public $timestamps = false;
    protected $casts = ['deleted_at'];

    // Accessor for organization details
    public function getOrganizationDetailsAttribute()
    {
        $organizationIds = explode(',', $this->other_suppliers_organizations);
        return organizations::whereIn('id', $organizationIds)->get();
    }

    public function brand_edit_requests()
    {
        return $this->hasMany(brand_edit_requests::class, 'brand_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'brand_id');
    }
}
