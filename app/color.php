<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class color extends Model
{
    protected $fillable = ['title', 'color_code', 'max_height', 'product_id', 'table_id'];
	public $incrementing = false;
	public $timestamps = false;

	public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(color_images::class, 'color_id');
    }

    public function priceTable()
    {
        return $this->belongsTo('App\price_tables', 'table_id', 'id');
    }

}

