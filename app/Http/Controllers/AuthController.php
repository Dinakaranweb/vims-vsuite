<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->is_active) {
                
                $user = User::Find(Auth::id());
                
                $notification = [
                    'message' => 'Login successful',
                    'alert-type' => 'success'
                ];

                switch ($user->role) {
                    case 'HOD':
                        return redirect()->intended(route('admin_dashboard'))->with($notification);
                    case 'Staff':
                        return redirect()->intended(route('staff_dashboard'))->with($notification);
                    case 'SuperAdmin':
                        return redirect()->intended(route('super_admin_dashboard'))->with($notification);
                    default:
                        Auth::logout();
                        $notification = [
                            'message' => 'Oops, something went wrong',
                            'alert-type' => 'error'
                        ];
                        return redirect()->route('login')->with($notification);
                }
            } else {
                Auth::logout();
                $notification = [
                    'message' => 'Access Denied! Account Deactivated',
                    'alert-type' => 'error'
                ];
                return redirect()->route('login')->with($notification);
            }
        } else {
            $notification = [
                'message' => 'Invalid credentials',
                'alert-type' => 'error'
            ];
            return redirect()->route('login')->with($notification);
        }
    }


    public function showRegisterForm(){
        
        $departments = Department::all();

        return view('frontend.auth.register', compact('departments'));
    }

    public function registerUser(Request $request){
        
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'username' => 'required',
            'department' => 'required',
            'role' => 'required',
            'password' => 'required|min:6',
        ]);

        $alert = User::where('email', $request->email)->orWhere('username', $request->username)->get();

        if($alert){

            $notification = array(
                'message' => 'Email/Username already registered',
                'alert-type' => 'error'
            );
     
            return redirect()->back()->with($notification);

        }else{

            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'department' => $request->department,
                'role' => $request->role,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_at' => now()
            ]);

            $notification = array(
                'message' => 'Registration successful',
                'alert-type' => 'success'
            );
     
            return redirect()->route('login')->with($notification);
        }
 
    }

    public function logout(){
        Auth::logout();
        $notification = array(
            'message' => 'Logout successful',
            'alert-type' => 'success'
        );
        return redirect()->route('login')->with($notification);
    }
}
