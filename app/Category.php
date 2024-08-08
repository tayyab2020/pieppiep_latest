<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
	use SoftDeletes;
    protected $fillable = ['cat_name','cat_slug','quotation_layout','photo','description'];
    public $timestamps = false;
    protected $casts = ['deleted_at'];

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('parent_id','=', 0);
    }

    public function suppliers()
    {
        return $this->hasMany('App\supplier_categories','category_id','id')->leftjoin('organizations','organizations.id','=','supplier_categories.organization_id')->select('organizations.*');
    }


    public function sub_categories()
    {
        return $this->hasMany(sub_categories::class, 'parent_id', 'id');
    }


}
