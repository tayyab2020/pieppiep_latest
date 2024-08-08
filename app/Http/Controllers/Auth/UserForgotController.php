<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\user_languages;
use App\Language;
use Illuminate\Support\Str;

class UserForgotController extends Controller
{

    public $lang;

    public function __construct()
    {
      $this->middleware('guest:user', ['except' => ['logout']]);
      $locale = \App::getLocale() == "en" ? "eng" : "du";
      $this->lang = Language::where('lang', '=', $locale)->first();
    }

    public function showforgotform()
    {
    	return view('user.forgot');
    }

    public function forgot(Request $request)
    {
    	$input =  $request->all();
        if (User::where('email', '=', $request->email)->count() > 0) {
            // user found
            $user = User::where('email', '=', $request->email)->firstOrFail();
            $autopass = Str::random(8);
            $input['password'] = Hash::make($autopass);

            $user->update($input);

            $name = $user->name. ' ' .$user->family_name;


            if($this->lang->lang == 'eng') // English Email Template
            {

                \Mail::send(array(), array(), function ($message) use ($name, $autopass, $request) {
                    $message->to($request->email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                        ->subject('Reset Password Request')
                        ->html("Dear Mr/Mrs ". $name .",<br><br>Your New Password is : ".$autopass."<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
                });

            }
            else // Dutch Email Template
            {

                \Mail::send(array(), array(), function ($message) use ($name, $autopass, $request) {
                    $message->to($request->email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                        ->subject('Wachtwoord wijzigen')
                        ->html("Beste ". $name .",<br><br>Je hebt zojuist een nieuw wachtwoord aangevraagd, indien jij niet de aanvrager bent neem contact met ons. Hierbij je nieuw wachtwoord : ".$autopass."<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
                });

            }


            Session::flash('success', $this->lang->prst);
    		    return redirect()->route('user-forgot');

        }
        else{
            // user not found
            Session::flash('unsuccess', $this->lang->naft);
    		return redirect()->route('user-forgot');
        }



    }
}
