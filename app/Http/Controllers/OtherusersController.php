<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\branches;
use App\Models\User;
use App\Models\userAccounts;
use App\Models\users_transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtherusersController extends Controller
{
    public function index($type)
    {
        $checks = ['Order Booker','Operator', 'Accountant', 'Branch Admin'];
        if(!in_array($type, $checks))
        {
            return back()->with('error', 'Invalid Request');
        }

        $users = User::currentBranch()->where('role', $type)->get();

        if(auth()->user()->role == 'Admin')
        {
            $branches = branches::all();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }
        return view('users.index', compact('users', 'type', 'branches'));
    }

    public function store(request $request, $type)
    {
        try
        {
        DB::beginTransaction();
        $request->validate(
            [
                'name' => "required",
            ],
        );

        if($type == 'Order Booker' || $type == 'Operator')
        {
            $cashable = 'no';
        }
        else
        {
            $cashable = 'yes';
        }
        $user = User::create(
            [
                'name'      => $request->name,
                'branchID'  => $request->branchID,
                'contact'   => $request->contact,
                'role'      => $type,
                'cashable'  => $cashable,
                'password'  => Hash::make($request->password),
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

    public function self_statement(request $request)
    {
        $user = Auth()->user();
        $from = $request->from;
        $to = $request->to;
        
        $transactions = users_transactions::where('userID', $user->id)->whereBetween('date', [$from, $to])->orderBy('date', 'asc')->orderBy('refID', 'asc')->get();

        $pre_cr = users_transactions::where('userID', $user->id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = users_transactions::where('userID', $user->id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = users_transactions::where('userID', $user->id)->sum('cr');
        $cur_db = users_transactions::where('userID', $user->id)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('users.self_statment', compact('transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }

    public function statement($id, $from, $to)
    {
        $user = User::find($id);

        $transactions = users_transactions::where('userID', $id)->whereBetween('date', [$from, $to])->orderBy('date', 'asc')->get();

        $pre_cr = users_transactions::where('userID', $id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = users_transactions::where('userID', $id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = users_transactions::where('userID', $id)->sum('cr');
        $cur_db = users_transactions::where('userID', $id)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('users.statment', compact('user', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }

    public function update(request $request, $id)
    {

        try
        {

        DB::beginTransaction();


        $user = User::find($id);
        $user->update(
            [
                'name'      => $request->name,
                'contact'      => $request->contact,
                'branchID'      => $request->branchID,
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
