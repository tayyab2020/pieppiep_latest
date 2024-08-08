<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\EmailSetting;
use Illuminate\Support\Facades\Crypt;

class SetUserMailConfig
{
    public function handle($request, Closure $next)
    {
        $mailFromAddress = "";
        // $mailFromName = "";

        // Check if the user is authenticated and fetch the user-specific SMTP settings
        if (Auth::check()) {
            $user = Auth::guard('user')->user();
            $user_role = $user->role_id;

            if($user_role != 3)
            {
                $emailSettings = $this->getUserSmtpConfig($user);

                if ($emailSettings) {
                    // Override the mail configuration
                    $emailSettings->password = Crypt::decryptString($emailSettings->password);
            
                    config([
                        'mail.driver' => 'smtp',
                        'mail.host' => $emailSettings->host,
                        'mail.port' => $emailSettings->port,
                        'mail.encryption' => $emailSettings->encryption,
                        'mail.username' => $emailSettings->username,
                        'mail.password' => $emailSettings->password
                    ]);

                    $mailFromAddress = $emailSettings->username;
                    // $mailFromName = 'Your Application';
                }
            }
        }

        app()->instance('mailFromAddress', $mailFromAddress);
        // app()->instance('mailFromName', $mailFromName);
        return $next($request);
    }

    private function getUserSmtpConfig($user)
    {
        $organization_id = $user->organization->id;
        $emailSettings = EmailSetting::where('organization_id', $organization_id)->first();
        // Retrieve the SMTP configuration for the given user
        // This is just an example; implement according to your needs
        return $emailSettings;
    }
}
