<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon; 

use App\Models\User;

use Mail; 
use Hash;
use DB; 
use Auth;

class ForgetPasswordController extends Controller
{
    public function showForgetPasswordForm(){

       return view('auth.forgot-password');

    }

    public function showForgetPasswordFormAgain($email){

       return view('auth.forgot-password-again', compact('email'));

    }

    public function submitForgetPasswordFormAgain(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);

        Mail::send('frontend.auth.email.forget-password-mail', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        $notification = array(
              'message' => 'Reset link sent to your mail!',
              'alert-type' => 'success'
          );
      
      return redirect()->route('login')->with($notification);

    }
  
    public function submitForgetPasswordForm(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if($record != Null){

          return redirect()->route('forget.password.get.again', ['email' => $request->email]);

        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email, 
            'token' => $token, 
            'created_at' => Carbon::now()
          ]);

        Mail::send('frontend.auth.email.forget-password-mail', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        $notification = array(
              'message' => 'Reset link sent to your mail!',
              'alert-type' => 'success'
          );
      
      return redirect()->route('login')->with($notification);

    }
    
    public function showResetPasswordForm($token) { 

       return view('auth.reset-password', ['token' => $token]);
    }

    public function submitResetPasswordForm(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                              'email' => $request->email, 
                              'token' => $request->token
                            ])
                            ->first();

        if(!$updatePassword){

            $notification = array(
              'message' => 'Invalid Token | Check your email',
              'alert-type' => 'error'
          );

            return back()->with($notification);
        }

        $user = User::where('email', $request->email)
                    ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where(['email'=> $request->email])->delete();

        $notification = array(
              'message' => 'Password Reset Successful!',
              'alert-type' => 'success'
          );
      
      return redirect()->route('login')->with($notification);

    }
}
