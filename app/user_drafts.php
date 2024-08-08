<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class user_drafts extends Model
{
  protected $table = 'user_drafts';
  protected $fillable = ['user_id','email','name', 'family_name', 'photo', 'description', 'language', 'education',  'profession', 'city', 'address', 'phone', 'web','special','company_name','registration_number','tax_number','bank_account','postcode'];
}
