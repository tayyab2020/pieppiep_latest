<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product_models extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id', 'heading_id', 'main_id', 'sub_feature', 'feature_value_id', 'title', 'value', 'max_size',
        'price_impact', 'impact_type', 'variable', 'm2_impact', 'factor', 'factor_value', 'status', 'deleted_at'
    ];
    public $timestamps = false;

    protected $casts = [
        'estimated_price' => 'float',
        'estimated_price_per_box' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

  
    // 1. Original features method for general use
    public function features()
    {
        return $this->hasMany('App\model_features', 'model_id', 'id');
    }

     // 2. Specific method for Binnen zonwering blade
     public function linkedFeatures()
     {
        return $this->belongsToMany('App\product_features', 'model_features', 'model_id', 'product_feature_id')
        ->wherePivot('linked', 1)->withPivot('linked')
        ->leftJoin('features_details', 'features_details.id', '=', 'product_features.feature_value_id')
        ->select('product_features.*', 'model_features.linked', 'features_details.title');
     }

    public function curtain_variables()
    {
        return $this->hasMany('App\curtain_variables','model_id','id');
    }

}
