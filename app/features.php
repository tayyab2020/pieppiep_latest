<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class features extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'id';

    public function features()
    {
        return $this->hasMany('App\product_features','heading_id','id')->leftjoin("features_details","features_details.id","=","product_features.feature_value_id")->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->select("product_features.*","features_details.default_value_id","default_features_details.title");
    }

    public function feature_details()
    {
        return $this->hasMany('App\features_details','feature_id','id')->where('features_details.sub_feature',0);
    }

    public function sub_features()
    {
        return $this->hasMany('App\features_details','feature_id','id')->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where('features_details.sub_feature',1)->select("features_details.*","default_features_details.title");
    }
}
