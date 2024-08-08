<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\messages;
use App\notes;
use app\emailSettings;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use SoftDeletes;
    protected $guard_name = 'user';

    protected $fillable = ['name', 'family_name','category_id', 'role_id', 'photo', 'compressed_photo', 'description','experience_years','insurance','insurance_pod', 'language', 'age', 'education', 'residency', 'profession', 'fax', 'email','f_url','g_url','t_url','l_url','password','is_featured','status','active','featured','web','special'];

    protected $hidden = [
        'password'
    ];

    protected $remember_token = false;

    // Dynamically get the columns from the related organization
    public function __get($key)
    {
        $organizationAttributes = [
            'company_name', 'registration_number','counter','counter_order','counter_invoice',
            'quotation_prefix','order_prefix','invoice_prefix','quotation_client_id','order_client_id',
            'invoice_client_id','supplier_account_show'
        ]; // Add other organization columns here
        
        $universalAttributes = [
            'address', 'city', 'phone', 'postcode', 'business_name',
            'tax_number', 'bank_account', 'photo', 'compressed_photo'
        ];

        if (in_array($key, $universalAttributes)) {
            // Return specific columns from the customers_details table
            if($this["role_id"] == 3)
            {
                if($key != "photo" && $key != "compressed_photo")
                {
                    return $this->universal_customers_details ? $this->universal_customers_details->$key : NULL;
                }
            }
            else
            {
                return $this->organization ? $this->organization->$key : NULL;
            }
        }

        if (in_array($key, $organizationAttributes)) {
            return $this->organization ? $this->organization->$key : NULL;
        }

        return parent::__get($key);
    }

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }

    public function from_messages()
    {
        return $this->hasMany(messages::class, 'from_user_id','id');
    }

    public function last_from_message()
    {
        return $this->hasOne(messages::class, 'from_user_id','id')->latest("id");
    }

    public function to_messages()
    {
        return $this->hasMany(messages::class, 'to_user_id','id');
    }

    public function last_to_message()
    {
        return $this->hasMany(messages::class, 'to_user_id','id')->latest("id");
    }

    public function unseen_messages()
    {
        return $this->hasMany(messages::class, 'from_user_id','id')->where("seen",0);
    }
    
    // Define the relationship with notes
    public function notes()
    {
        return $this->hasMany(notes::class);
    }

    // Define the one-to-one relationship with user_organizations
    public function userOrganization()
    {
        return $this->hasOne(user_organizations::class);
    }

    // Define the one-to-one-through relationship with organizations
    public function organization()
    {
        return $this->hasOneThrough(organizations::class, user_organizations::class, 'user_id', 'id', 'id', 'organization_id');
    }

    public function employee()
    {
        return $this->hasOne(employees_details::class);
    }

    public function universal_customers_details()
    {
        return $this->hasOne(universal_customers_details::class);
    }

    public function organizations()
    {
        return $this->belongsTo(Organizations::class);
    }
         
    use Notifiable;

    // Other model methods and properties

    public function emailSettings()
    {
        return $this->hasOne(EmailSetting::class);
    }
}
