<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\EmailSetting;
use App\User;

class DKIMController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
    }

    public function showGenerateForm()
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        if ($user_role == 3) {
            return redirect()->route('user-login');
        }

        return view('user.generate-dkim');
    }

    public function generateDKIMKeys(Request $request)
    {
        $user = Auth::guard('user')->user();
        $organization_id = $user->organization->id;

        $request->validate([
            'domain' => 'required|regex:/^(?=.{1,253}$)(?:(?!\d+\.)[a-zA-Z0-9-_]{1,63}\.?)+$/', // Validate domain format
        ]);

        $selector = 'default';  // You may want to allow users to specify this
        $domain = $request->input('domain');  // Get user's domain from input

        // Use the hardcoded private key from .env
        $privateKey = env('DKIM_PRIVATE_KEY');

        // Ensure the private key is correctly formatted
        $privateKey = str_replace("\\n", "\n", $privateKey);

        // Generate the public key
        $res = openssl_pkey_get_private($privateKey);

        if (!$res) {
            Log::error('Invalid private key format.');
            return redirect()->route('generate-dkim')->with('error', 'Invalid private key format.');
        }

        $keyDetails = openssl_pkey_get_details($res);
        $publicKey = $keyDetails['key'];

        // Save the private key and other details in your database
        $emailSetting = EmailSetting::updateOrCreate(
            ['organization_id' => $organization_id],
            ['dkim_private_key' => $privateKey, 'dkim_selector' => $selector, 'dkim_domain' => $domain, 'dkim_public_key' => $publicKey]
        );

        Log::info('DKIM keys generated successfully.', ['publicKey' => $publicKey]);

        return redirect()->route('dkim-keys');
    }

    public function showGeneratedKeys()
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        if ($user_role == 3) {
            return redirect()->route('user-login');
        }

        $organization_id = $user->organization->id;

        $emailSetting = EmailSetting::where('organization_id', $organization_id)->first();
        
        if (!$emailSetting) {
            Log::warning('DKIM keys not found.');
            return redirect()->route('generate-dkim')->with('error', 'DKIM keys not found.');
        }

        $publicKey = $emailSetting->dkim_public_key;
        $selector = $emailSetting->dkim_selector;
        $domain = $emailSetting->dkim_domain;

        return view('user.dkim-keys', compact('publicKey', 'selector', 'domain'));
    }
}
