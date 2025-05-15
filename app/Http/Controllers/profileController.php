<?php

namespace App\Http\Controllers;

use App\Models\branches;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class profileController extends Controller
{
    public function index()
    {
        return view('auth.profile');
    }

    public function  update(request $req)
    {
        $req->validate([
            'name'  => 'required|unique:users,name,'.auth()->user()->id,
            'email'  => 'required|unique:users,email,'.auth()->user()->id,
        ]);

        User::find(auth()->user()->id)->update($req->only('name', 'email'));

        return back()->with('success', "Profile Updated");
    }

    public function changePassword(request $req)
    {
        $req->validate([
            'old_password'  => 'required|current_password:web',
            'new_password'  => 'required|min:8|confirmed',
        ],
    [
        'old_password.current_password' => "Old Password is Incorrect",
        ]); 

        $user = auth()->user();
        $user->password = Hash::make($req->new_password);
        $user->save();

        return back()->with('success', 'Password Updated');
    }

    public function changeHeader(request $req)
    {
        $req->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $branch = branches::find(auth()->user()->branchID);
        $filePath = null;

            $oldImg = asset('assets/header/' . $branch->header);
            if (file_exists($oldImg)) {
                unlink($oldImg);
            }

            $image = $req->file('img');
            $filename = $branch->id . '.' . $image->getClientOriginalExtension();

          $image->move('assets/header', $filename);

          $branch->header = 'assets/header/' . $filename;
        $branch->save();

        return back()->with('success', 'Header Updated');
    }
}
