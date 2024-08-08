<?php

// here we define the relationship between the models and features ? You are using this table to keep track of the relations between features and models right?
// You didnt create the relations here but okay. Just to be sure, are the relations below well defined?

namespace App;

use Illuminate\Database\Eloquent\Model;

class model_features extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function model()
    {
        return $this->belongsTo('App\product_models', 'model_id');
    }
    
    public function feature()
    {
        return $this->belongsTo('App\product_features', 'product_feature_id');
    }
    
    // Scope for filtering linked features
    // public function scopeLinked($query)
    // {
    //     return $query->where('linked', 1);
    // }
    
}


