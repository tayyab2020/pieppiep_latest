<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class color_images extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['product_id', 'color_id', 'image'];

    public function color()
    {
        return $this->belongsTo(colors::class, 'color_id', 'id');
    }
    
    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
