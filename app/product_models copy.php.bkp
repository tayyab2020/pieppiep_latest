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

    public function curtain_variables()
    {
        return $this->hasMany('App\curtain_variables','model_id','id');
    }

    public function features()
    {
        return $this->belongsToMany(product_features::class, 'model_features', 'model_id', 'product_feature_id', 'id');
    }

}

