<?php

namespace App\Http\Controllers;

use App\Models\currencymgmt;
use App\Models\staffAmountAdjustment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffAmountAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');
        $staffID = $request->staffID ?? 'All';
        $type = $request->type ?? 'All';
        $staffAdjustments = staffAmountAdjustment::currentBranch()->whereBetween('date', [$start, $end]);
        if ($staffID != 'All') {
            $staffAdjustments->where('staffID', $staffID);
        }
        if ($type != 'All') {
            $staffAdjustments->where('type', $type);
        }
        $staffAdjustments = $staffAdjustments->get();
        $staffs = User::currentBranch()->active()->get();
        $currencies = currencymgmt::all();

        return view('Finance.staff_amount_adjustments.index', compact('staffAdjustments', 'staffs', 'currencies', 'start', 'end', 'staffID', 'type'));
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
            DB::beginTransaction();
            $ref = getRef();
            staffAmountAdjustment::create(
                [
                    'staffID' => $request->staffID,
                    'userID' => auth()->user()->id,
                    'branchID' => auth()->user()->branchID,
                    'date' => $request->date,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );

            $staff = User::find($request->staffID);
            $user = auth()->user()->name;

            if ($request->type == 'credit') {
                createMethodTransaction($staff->id, $request->method, $request->amount, 0, $request->date, $request->number, $request->bank, $request->cheque_date, $request->notes, $ref);

                createUserTransaction($staff->id, $request->date, $request->amount, 0, 'Staff Amount Adjusted - '.$request->notes, $ref);

                if ($request->method == 'Cash') {
                    createCurrencyTransaction($staff->id, $request->currencyID, $request->qty, 'cr', $request->date, 'Staff Amount Adjusted - '.$request->notes, $ref);
                }
            } else {
                createMethodTransaction($staff->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $request->notes, $ref);

                createUserTransaction($staff->id, $request->date, 0, $request->amount, 'Staff Amount Adjusted - '.$request->notes, $ref);

                if ($request->method == 'Cash') {
                    createCurrencyTransaction($staff->id, $request->currencyID, $request->qty, 'db', $request->date, 'Staff Amount Adjusted - '.$request->notes, $ref);
                }
            }
            if ($request->has('file')) {
                createAttachment($request->file('file'), $ref);
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
    public function show(staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, staffAmountAdjustment $staffAmountAdjustment)
    {
        //
    }

    public function delete($ref)
    {
        $adj = staffAmountAdjustment::where('refID', $ref)->first();
        $staff = User::find($adj->staffID);
        $bank = $adj->bank;
        $number = $adj->number;
        $cheque_date = $adj->cheque_date;
        $notes = "Staff Amount Adjustment Date: $adj->date | Staff: $staff->name | Bank: $bank | Number: $number | Cheque Date: $cheque_date | Type: $adj->type | Amount: $adj->amount | Notes: $adj->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $adj->branchID, $adj->refID, 'staff_amount_adjustment', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('staff_amounts_adjustments.index')->with('success', 'Staff Amount Adjustment Delete Request Sent to Branch Admin');
    }
}
