<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{
    public function Profile(){

        $activeMenu = "";
        $activeDropdown = "";

        return view('frontend.auth.profile', compact('activeMenu', 'activeDropdown'));

    }

    public function Manual(){

        $activeMenu = "guide";
        $activeDropdown = "";

        return view('frontend.auth.manual', compact('activeMenu', 'activeDropdown'));

    }

    public function signatureDemo(){

        $activeMenu = "guide";
        $activeDropdown = "";

        return view('frontend.auth.signature', compact('activeMenu', 'activeDropdown'));

    }

    public function updateProfile(Request $request){

        $alert = User::where('email', $request->email)->where('id', '!=', Auth::id())->first();

        if($alert != Null){

            $notification = array(
                'message' => 'Email ID already exists',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);

        }

        $user = User::find(Auth::id());

        $user->name = $request->name;
        $user->emp_id = $request->emp_id;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        $notification = array(
            'message' => 'Profile Updated',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);

    }
}
