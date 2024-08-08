<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\user_organizations;

class user_organizations extends Model
{
    protected $table = 'user_organizations';
    protected $fillable = [
        'user_id', 
        'organization_id'
    ];
    public $timestamps = false;

    // Define the belongs-to relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the belongs-to relationship with organizations
    public function organization()
    {
        return $this->belongsTo(organizations::class);
    }
}
