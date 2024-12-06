<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\area;
use App\Models\customer_area;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($filter)
    {
        $accounts = accounts::where('type', $filter)->orderBy('title', 'asc')->get();
        if($filter == "Other")
        {
            $accounts = accounts::Other()->get();
        }

        return view('Finance.accounts.index', compact('accounts', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $areas = area::all();
        return view('Finance.accounts.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|unique:accounts,title'
            ],
            [
                'title.required' => "Please Enter Account Title",
                'title.unique'  => "Account with this title already exists"
            ]
        );

        try
        {
            DB::beginTransaction();

                $ref = getRef();
                if($request->type == "Customer")
                {
                    $account = accounts::create(
                        [
                            'title' => $request->title,
                            'type' => $request->type,
                            'category' => $request->category,
                            'contact' => $request->contact,
                            'c_type' => $request->c_type,
                            'cashable' => "no",
                            'areaID' =>  $request->area,
                        ]
                    );
                }
                else
                {
                   if($request->type == "Business")
                   {
                        if($request->category == "Cash")
                        {
                            $account = accounts::create(
                                [
                                    'title' => $request->title,
                                    'type' => $request->type,
                                    'contact' => $request->contact,
                                    'email' => $request->email,
                                    'category' => $request->category,
                                    'areaID' => 1,
                                ]
                            );
                        }
                        else
                        {
                            $account = accounts::create(
                                [
                                    'title' => $request->title,
                                    'type' => $request->type,
                                    'contact' => $request->contact,
                                    'email' => $request->email,
                                    'category' => $request->category,
                                    'cashable' => "no",
                                    'areaID' => 1,
                                ]
                            );
                        }
                   }
                   else
                   {
                    $account = accounts::create(
                        [
                            'title' => $request->title,
                            'type' => $request->type,
                            'contact' => $request->contact,
                            'email' => $request->email,
                            'category' => $request->category,
                            'cashable' => "no",
                            'areaID' => 1,
                        ]
                    );
                   }
                }
           DB::commit();
           return back()->with('success', "Account Created Successfully");
        }
        catch(\Exception $e)
        {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $from, $to)
    {
        $account = accounts::find($id);

        $transactions = transactions::where('accountID', $id)->whereBetween('date', [$from, $to])->get();

        $pre_cr = transactions::where('accountID', $id)->whereDate('date', '<', $from)->sum('cr');
        $pre_db = transactions::where('accountID', $id)->whereDate('date', '<', $from)->sum('db');
        $pre_balance = $pre_cr - $pre_db;

        $cur_cr = transactions::where('accountID', $id)->sum('cr');
        $cur_db = transactions::where('accountID', $id)->sum('db');

        $cur_balance = $cur_cr - $cur_db;

        return view('Finance.accounts.statment', compact('account', 'transactions', 'pre_balance', 'cur_balance', 'from', 'to'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(accounts $account)
    {
        return view('Finance.accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, accounts $account)
    {
        $request->validate(
            [
                'title' => "required|unique:accounts,title,". $request->accountID,
            ],
            [
                'title.required' => "Please Enter Account Title",
                'title.unique'  => "Account with this title already exists"
            ]
        );
        $account = accounts::find($request->accountID)->update(
            [
                'title' => $request->title,
                'category' => $request->category,
                'contact' => $request->contact ?? null,
                'c_type' => $request->c_type,
                'areaID' => $request->area ?? 1,
            ]
        );

        return redirect()->route('accountsList', $request->type)->with('success', "Account Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(accounts $accounts)
    {
        //
    }

    public function status($id)
    {
        $account = accounts::find($id);
        if($account->status == "Active")
        {
           $status = "Inactive";
        }
        else
        {
            $status = "Active";
        }

        $account->update(
            [
                'status' => $status,
            ]
        );

        return back()->with('success', "Status Updated");
    }
}
