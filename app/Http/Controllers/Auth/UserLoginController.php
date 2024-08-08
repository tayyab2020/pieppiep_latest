<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class UserLoginController extends Controller
{
    public function __construct()
    {
      $this->middleware('guest:user', ['except' => ['logout']]);
    }

 	public function showLoginForm()
    {
      return view('user.login');
    }

    public function login(Request $request)
    {

      // Validate the form data

		  $this->validate($request,[
		    'email' => 'required|email',
		    'password' => 'required',
		  ]);

      // Attempt to log the user in
      if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'allowed' => 1])) {

        $user = Auth::guard('user')->user();
        $previousRoleId = (int)Cookie::get('last_role_id');

        Cookie::queue('last_role_id', $user->role_id, 60 * 24 * 30); // Store for 30 days
        
        if($user->role_id == 2 || $user->role_id == 4)
        {
            if($user->active == 1)
            {
              if($previousRoleId != $user->role_id)
              {
                return redirect()->route('user-dashboard');
              }
              else
              {
                return redirect()->intended(route('user-dashboard'));
              }
            }
            else
            {
              if(Auth::guard('user')->user()->verified == 0)
              {
                Session::flash('unsuccess',"You account is not verified by admin!");
              }
              else
              {
                Session::flash('unsuccess',"You account is inactivated by admin!");
              }
                
              Auth::guard('user')->logout();
              return redirect()->back()->withInput($request->only('email'));
            }

        }
        else
        {

          if($previousRoleId != $user->role_id)
          {
            return redirect()->route('client-new-quotations');
          }
          else
          {
            return redirect()->intended(route('client-new-quotations'));
          }

        }


      }

      // if unsuccessful, then redirect back to the login with the form data
      Session::flash('message',"Failed!");
      return redirect()->back()->withInput($request->only('email'));
    }

    public function logout()
    {
        Auth::guard('user')->logout();
        // Cookie::queue(Cookie::forget('last_role_id')); // Clear the cookie
        return redirect('/');
    }
}
