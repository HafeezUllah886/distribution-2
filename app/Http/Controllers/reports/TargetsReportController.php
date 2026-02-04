<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\branches;
use App\Models\targets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetsReportController extends Controller
{
    public function index(Request $request)
    {
        $branch = $request->branch ?? auth()->user()->branchID;

        if (auth()->user()->role == 'Admin') {
            $branches = branches::all();
        } else {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }

        $orderbookers = User::orderbookers()->where('branchID', $branch)->active()->get();

        return view('reports.targets.index', compact('branches', 'orderbookers', 'branch'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branchID = $request->branch;

        $query = targets::whereBetween('endDate', [$from, $to])
            ->where('branchID', $branchID)
            ->with(['orderbooker', 'product.vendor', 'branch', 'unit']);

        if ($request->filled('orderbooker')) {
            $query->whereIn('orderbookerID', $request->orderbooker);
        }

        $targets = $query->get();

        foreach ($targets as $target) {
            $saleQuery = DB::table('sale_details')
                ->where('orderbookerID', $target->orderbookerID)
                ->where('productID', $target->productID)
                ->whereBetween('date', [$target->startDate, $target->endDate]);

            $qtySold = $saleQuery->sum('pc');

            // Core calculations
            $target->pack_size = $target->unit_value;
            $target->unit_price = $target->product->price * $target->unit_value;

            $target->target_qty = $target->pc / $target->unit_value;
            $target->achieved_qty = $qtySold / $target->unit_value;
            $target->remaining_qty = $target->target_qty - $target->achieved_qty;
            if ($target->remaining_qty < 0) {
                $target->remaining_qty = 0;
            }

            $qtySoldForPer = $qtySold > $target->pc ? $target->pc : $qtySold;

            $target->per = $target->pc > 0 ? ($qtySoldForPer / $target->pc * 100) : 0;
            $target->totalPer = $target->per;

            // UI Labels based on image
            if ($target->endDate >= now()->toDateString()) {
                $target->campain_status = 'ACTIVE';
            } else {
                $target->campain_status = 'EXPIRED';
            }

            if ($target->per >= 100) {
                $target->ach_label = 'ACHIEVED';
                $target->ach_color = 'success';
            } else {
                $target->ach_label = 'PENDING';
                $target->ach_color = 'warning';
            }
        }

        if ($request->filled('status')) {
            $targets = $targets->filter(function ($target) use ($request) {
                if (in_array($request->status, ['Open', 'Closed'])) {
                    $val = $request->status == 'Open' ? 'ACTIVE' : 'EXPIRED';

                    return $target->campain_status == $val;
                }
                $val = $request->status == 'Achieved' ? 'ACHIEVED' : 'PENDING';

                return $target->ach_label == $val;
            });
        }

        $branchName = branches::find($branchID)->name;

        $orderbookerNames = 'All';
        if ($request->filled('orderbooker')) {
            $orderbookerNames = User::whereIn('id', $request->orderbooker)->pluck('name')->implode(', ');
        }

        $statusName = $request->status ?? 'All';

        return view('reports.targets.details', compact('from', 'to', 'targets', 'branchName', 'orderbookerNames', 'statusName'));
    }
}
