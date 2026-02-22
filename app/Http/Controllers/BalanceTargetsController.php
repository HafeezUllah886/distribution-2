<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\balance_targets;
use App\Models\transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceTargetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? firstDayOfCurrentYear();
        $end = $request->end ?? lastDayOfCurrentYear();

        $query = balance_targets::currentBranch()
            ->with(['orderbooker', 'customer', 'branch'])
            ->whereBetween('endDate', [$start, $end]);

        if ($request->filled('orderbookerID')) {
            $query->where('orderbookerID', $request->orderbookerID);
        }

        if ($request->filled('customerID')) {
            $query->where('customerID', $request->customerID);
        }

        if ($request->filled('status')) {
            if ($request->status == 'Open') {
                $query->where('endDate', '>=', now()->toDateString());
            } else {
                $query->where('endDate', '<', now()->toDateString());
            }
        }

        $targets = $query->orderBy('endDate', 'desc')->get();

        foreach ($targets as $target) {
            $credits = transactions::where('accountID', $target->customerID)
                ->where('orderbookerID', $target->orderbookerID)
                ->sum('cr') ?? 0;
            $debits = transactions::where('accountID', $target->customerID)
                ->where('orderbookerID', $target->orderbookerID)
                ->sum('db') ?? 0;

            $target->current_balance = $credits - $debits;

            $total_reduction_needed = $target->start_value - $target->target_value;
            $current_reduction = $target->start_value - $target->current_balance;

            if ($total_reduction_needed > 0) {
                $target->per = ($current_reduction / $total_reduction_needed) * 100;
            } else {
                $target->per = $target->current_balance <= $target->target_value ? 100 : 0;
            }

            $display_per = $target->per < 0 ? 0 : ($target->per > 100 ? 100 : $target->per);
            $target->totalPer = $display_per;

            if ($target->endDate >= now()->toDateString()) {
                $target->campain = 'Open';
                $target->campain_color = 'success';
            } else {
                $target->campain = 'Closed';
                $target->campain_color = 'warning';
            }

            if ($target->totalPer >= 100) {
                $target->goal = 'Target Achieved';
                $target->goal_color = 'success';
                $target->ach_status = 'Achieved';
            } elseif ($target->endDate >= now()->toDateString()) {
                $target->goal = 'In Progress';
                $target->goal_color = 'info';
                $target->ach_status = 'In Progress';
            } else {
                $target->goal = 'Not Achieved';
                $target->goal_color = 'danger';
                $target->ach_status = 'Not Achieved';
            }
        }

        if ($request->filled('achievement')) {
            $targets = $targets->filter(function ($target) use ($request) {
                return $target->ach_status == $request->achievement;
            });
        }

        $orderbookers = User::orderbookers()->currentBranch()->active()->get();
        $customers = accounts::customer()->currentBranch()->active()->get();

        return view('balance_target.index', compact('targets', 'start', 'end', 'orderbookers', 'customers'));
    }

    public function create(Request $request)
    {
        // Not used as create modal is in index
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            balance_targets::create(
                [
                    'branchID' => auth()->user()->branchID,
                    'orderbookerID' => $request->orderbookerID,
                    'customerID' => $request->customerID,
                    'start_value' => $request->start_value,
                    'target_value' => $request->target_value,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'notes' => $request->notes,
                ]
            );

            DB::commit();

            return back()->with('success', 'Balance Target Saved');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $target = balance_targets::with(['orderbooker', 'customer', 'branch'])->find($id);

        $credits = transactions::where('accountID', $target->customerID)
            ->where('orderbookerID', $target->orderbookerID)
            ->sum('cr') ?? 0;
        $debits = transactions::where('accountID', $target->customerID)
            ->where('orderbookerID', $target->orderbookerID)
            ->sum('db') ?? 0;

        $target->current_balance = $credits - $debits;

        $total_reduction_needed = $target->start_value - $target->target_value;
        $current_reduction = $target->start_value - $target->current_balance;

        if ($total_reduction_needed > 0) {
            $target->per = ($current_reduction / $total_reduction_needed) * 100;
        } else {
            $target->per = $target->current_balance <= $target->target_value ? 100 : 0;
        }

        $display_per = $target->per < 0 ? 0 : ($target->per > 100 ? 100 : $target->per);
        $target->totalPer = $display_per;

        if ($target->endDate >= now()->toDateString()) {
            $target->campain = 'Open';
            $target->campain_color = 'success';
        } else {
            $target->campain = 'Closed';
            $target->campain_color = 'warning';
        }

        if ($target->totalPer >= 100) {
            $target->goal = 'Target Achieved';
            $target->goal_color = 'success';
            $target->ach_status = 'Achieved';
        } elseif ($target->endDate >= now()->toDateString()) {
            $target->goal = 'In Progress';
            $target->goal_color = 'info';
            $target->ach_status = 'In Progress';
        } else {
            $target->goal = 'Not Achieved';
            $target->goal_color = 'danger';
            $target->ach_status = 'Not Achieved';
        }

        return view('balance_target.view', compact('target'));
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $target = balance_targets::find($id);
            $target->update(
                [
                    'customerID' => $request->customerID,
                    'start_value' => $request->start_value,
                    'target_value' => $request->target_value,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'notes' => $request->notes,
                ]
            );

            DB::commit();

            return back()->with('success', 'Balance Target Updated');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $target = balance_targets::find($id);

        $target->delete();
        session()->forget('confirmed_password');

        return back()->with('success', 'Balance Target Deleted');
    }
}
