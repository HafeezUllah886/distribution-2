<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\accountsAdjustment;
use App\Models\area;
use App\Models\transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? firstDayOfMonth();
        $end = $request->end ?? lastDayOfMonth();
        $type = $request->type ?? 'All';
        $area = $request->area ?? 'All';

        $accountsAdjustments = accountsAdjustment::currentBranch()->whereBetween('date', [$start, $end]);
        if ($type != 'All') {
            $accounts = accounts::where('type', $type)->currentBranch()->active()->get();
            $accountsAdjustments->whereIn('accountID', $accounts->pluck('id'));
            $type = [$type];
        } else {
            $type = ['Business', 'Vendor', 'Supply Man', 'Unloader', 'Customer', 'Personal', 'Freight'];
        }

        $accountsAdjustments = $accountsAdjustments->get();
        $accounts = accounts::whereIn('type', $type)->currentBranch()->active();
        if ($area != 'All') {
            $accounts = $accounts->where('areaID', $area);
        }
        $accounts = $accounts->get();

        $type = $request->type;
        $orderbookers = User::orderbookers()->currentBranch()->get();
        $areas = area::currentBranch()->get();

        return view('Finance.accounts_adjustments.index', compact('accountsAdjustments', 'accounts', 'start', 'end', 'type', 'area', 'orderbookers', 'areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /* $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]); */
            DB::beginTransaction();
            $ref = getRef();
            accountsAdjustment::create(
                [
                    'accountID' => $request->accountID,
                    'userID' => auth()->user()->id,
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            $account = accounts::find($request->accountID);
            $user = auth()->user()->name;

            if ($request->type == 'credit') {
                createTransaction($request->accountID, $request->date, $request->amount, 0, "Amount Adjusted: $request->notes", $ref, $request->orderbookerID);
            } else {
                createTransaction($request->accountID, $request->date, 0, $request->amount, "Amount Adjusted: $request->notes", $ref, $request->orderbookerID);
            }

            DB::commit();

            return back()->with('success', 'Adjustment Created');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, accountsAdjustment $accountsAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(accountsAdjustment $accountsAdjustment)
    {
        //
    }

    public function delete($ref)
    {
        $adj = accountsAdjustment::where('refID', $ref)->first();
        $account = $adj->account->title;
        $type = $adj->type;
        $adjustedBy = $adj->user->name;
        $area = $adj->account->area->name;
        $orderbookerID = transactions::where('refID', $ref)->first()->orderbookerID;
        $orderbooker = User::find($orderbookerID)->name;
        $notes = "Accounts Adjustment Date: $adj->date | Amount: $adj->amount | Account: $account | Type: $type | Notes: $adj->notes | Adjusted By: $adjustedBy | Area: $area | Orderbooker: $orderbooker";
        $delete = storeDeleteRequest(auth()->user()->id, $adj->branchID, $adj->refID, 'accounts_adjustment', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('accounts_adjustments.index')->with('success', 'Accounts Adjustment Delete Request Sent to Branch Admin');
    }
}
