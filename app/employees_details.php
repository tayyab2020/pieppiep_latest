<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\user_organizations;
use Illuminate\Database\Eloquent\SoftDeletes;

class employees_details extends Model
{
    use SoftDeletes;
    protected $casts = ['deleted_at'];
    protected $table = 'employees_details';
    protected $fillable = [
        'user_id', 
        'contract',
        'name',
        'email',
        'function',
        'street',
        'house_number',
        'affix_house_number',
        'postcode',
        'city',
        'phone',
        'IBAN_number',
        'address',
        'profile_type',
        'business_name',
        'tax_number',
        'bank_account',
        'personal_number',
        'contract_number',
        'freelancer_registration_number'
    ];

    // Define the belongs-to relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
