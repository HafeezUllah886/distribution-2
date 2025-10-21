<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class authController extends Controller
{
    public function index()
    {
        return view('auth.index');
    }

    public function login(Request $req)
    {
        
        
       if(auth()->attempt($req->only('name', 'password')))
       {
            $req->session()->regenerate();
            $status = Auth()->user()->status;
            if($status != 'Active')
            {
                auth()->logout();
                return to_route('login')->with('error', 'Account Blocked');
            }
            return redirect()->intended('/');
       }
       return back()->with('error', 'Wrong User Name or Password');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
