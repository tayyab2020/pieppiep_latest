<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;
    protected $table = 'products';
    protected $fillable = ['delivery_days','margin','retailer_margin','user_id','organization_id','excel_id','title','slug','model_number','size','additional_info','floor_type','floor_type2','supplier','color','category_id','sub_category_id','brand_id','model_id','photo','description','ladderband','ladderband_value','ladderband_price_impact','ladderband_impact_type','price_based_option','base_price'];
    protected $casts = ['deleted_at'];

    public function colors()
    {
        return $this->hasMany(colors::class, 'product_id', 'id');
    }

    public function getColorImageAttribute()
    {
        $firstColor = $this->colors->first();
        if ($firstColor && $firstColor->images->isNotEmpty()) {
            return $firstColor->images->first()->image;
        }
        return null;
    }

    public function models()
    {
        return $this->hasMany('App\product_models','product_id','id')->leftjoin("predefined_models_details","predefined_models_details.id","=","product_models.size_id")->leftjoin("default_predefined_models_details","default_predefined_models_details.id","=","predefined_models_details.default_model_detail_id")->select("product_models.*","predefined_models_details.default_model_detail_id","default_predefined_models_details.model as size_title");
    }

    public function features()
    {
        return $this->hasMany(product_features::class, 'product_id', 'id')->whereNull("main_id");
    }
    

    public function modelFeatures()
    {
        return $this->hasManyThrough(product_features::class, model_features::class, 'model_id', 'id', 'id', 'product_feature_id');
    }
    
    public function retailersRequests()
    {
        return $this->hasMany(retailers_requests::class, 'supplier_organization', 'organization_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');

    }

    public function subCategory()
    {
        return $this->belongsTo(sub_categories::class, 'sub_category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
}
