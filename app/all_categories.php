<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class all_categories extends Model
{
	use SoftDeletes;
    protected $table = 'categories';
    protected $fillable = ['cat_name','cat_slug','quotation_layout','photo','description'];
    public $timestamps = false;
    protected $casts = ['deleted_at'];
}
