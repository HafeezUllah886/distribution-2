<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\User;
use App\Models\userAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtherusersController extends Controller
{
    public function index($type)
    {
        $checks = ['Order Booker','Operator', 'Accountant'];
        if(!in_array($type, $checks))
        {
            return back()->with('error', 'Invalid Request');
        }

        $users = User::where('role', $type)->get();

        return view('users.index', compact('users', 'type'));
    }

    public function store(request $request, $type)
    {

        try
        {
        DB::beginTransaction();
        $request->validate(
            [
                'name' => "unique:users,name|required",
            ],
            [
                'name.unique' => "User Name Already Used",
            ]
        );

        $user = User::create(
            [
                'name'      => $request->name,
                'contact'   => $request->contact,
                'role'      => $type,
                'password'  => Hash::make($request->password),
            ]
        );

        $account = accounts::create(
            [
                'title' => $user->name,
                'type'  => $user->role,
            ]
        );

        userAccounts::create(
            [
                'userID'    => $user->id,
                'accountID' =>  $account->id
            ]
        );
        DB::commit();
        return back()->with('success', 'User Created');
    }
    catch(\Exception $e)
    {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }


    }

    public function show()
    {

    }

    public function update(request $request, $id)
    {

        try
        {

        DB::beginTransaction();


        $user = User::find($id);
        $user->update(
            [
                'contact'      => $request->contact,
            ]
        );
        if($request->password != "")
        {
            $user->update(
                [
                    'password'  => Hash::make($request->password),
                ]
            );
        }
        DB::commit();
        return back()->with('success', 'User Updated');
    }
    catch(\Exception $e)
    {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
    }


    public function status($id)
    {
        $user = User::find($id);

        if($user->status == "Active")
        {
            $user->status = "Blocked";
        }
        else
        {
            $user->status = "Active";
        }

        $user->save();

        return back()->with('success', "User Status Updated");
    }
}
