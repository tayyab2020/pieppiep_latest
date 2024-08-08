<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class product_features extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable = [
        'product_id', 'heading_id', 'main_id', 'sub_feature', 'feature_value_id', 'title', 'value', 'max_size',
        'price_impact', 'impact_type', 'variable', 'm2_impact', 'factor', 'factor_value', 'status', 'deleted_at'
    ];
    public $timestamps = false;

    public function heading()
    {
        return $this->belongsTo('App\features',  'heading_id', 'default_feature_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Products', 'product_id', 'id');
        
    }

    public function options()
    {
        return $this->hasMany(self::class, 'main_id', 'id');
    }

    

    public function models()
    {
        return $this->belongsToMany('App\product_models', 'model_features', 'product_feature_id', 'model_id');
    }


}

