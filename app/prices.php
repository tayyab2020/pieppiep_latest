<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'prices';

    public function priceTable()
    {
        return $this->belongsTo('App\price_tables', 'table_id', 'id');
    }

}
