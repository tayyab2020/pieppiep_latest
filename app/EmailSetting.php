<?php

// app/Models/EmailSetting.php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EmailSetting extends Model
{
    // Remove the HasFactory trait
    // use HasFactory;

    protected $fillable = ['organization_id', 'host', 'port', 'encryption', 'username', 'password', 'dkim_private_key', 'dkim_selector', 'dkim_domain', 'dkim_public_key', 'imap_port'];

    public function organization()
    {
        return $this->belongsTo(organizations::class);
    }

    /**
     * Set the password attribute and automatically hash it.
     *
     * @param  string  $value
     * @return void
     */
}
