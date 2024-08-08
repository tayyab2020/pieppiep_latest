<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class notes_tags extends Model
{
    protected $primaryKey = 'id';
	public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}