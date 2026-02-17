<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\orderbooker_products;
use App\Models\product_units;
use App\Models\products;
use App\Models\targets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $start = $request->start ?? firstDayOfCurrentYear();
        $end = $request->end ?? lastDayOfCurrentYear();

        $query = targets::currentBranch()
            ->with(['orderbooker', 'product.vendor', 'branch', 'unit'])
            ->whereBetween('endDate', [$start, $end]);

        if ($request->filled('orderbookerID')) {
            $query->where('orderbookerID', $request->orderbookerID);
        }

        if ($request->filled('productID')) {
            $query->where('productID', $request->productID);
        }

        if ($request->filled('vendorID')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('vendorID', $request->vendorID);
            });
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
            $saleQuery = DB::table('sale_details')
                ->where('orderbookerID', $target->orderbookerID)
                ->where('productID', $target->productID)
                ->whereBetween('date', [$target->startDate, $target->endDate]);

            $qtySold = $saleQuery->sum('pc');
            $target->sold = $qtySold / $target->unit_value;
            $targetQty = $target->pc / $target->unit_value;

            // Cap the pieces for percentage calculation status, but use $target->pc (total pieces) for comparison
            $qtySoldForPer = $qtySold > $target->pc ? $target->pc : $qtySold;

            $target->per = $target->pc > 0 ? ($qtySoldForPer / $target->pc * 100) : 0;
            $target->totalPer = $target->per;
            $target->actual_per = $target->pc > 0 ? ($qtySold / $target->pc * 100) : 0;

            if ($target->endDate >= now()->toDateString()) {
                $target->campain = 'Open';
                $target->campain_color = 'success';
            } else {
                $target->campain = 'Closed';
                $target->campain_color = 'warning';
            }

            if ($target->per >= 100) {
                $target->goal = 'Target Achieved';
                $target->goal_color = 'success';
                $target->ach_status = 'Achieved';
            } elseif ($target->endDate >= now()->toDateString() && $target->per < 100) {
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
        $products = products::currentBranch()->active()->with('vendor')->get();
        $vendors = accounts::vendor()->currentBranch()->active()->get();

        return view('target.index', compact('targets', 'start', 'end', 'orderbookers', 'products', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $request->orderbookerID)->pluck('productID')->toArray();
        $products = products::whereIn('id', $orderbooker_products)->orderby('name', 'asc')->get();
        $orderbooker = User::find($request->orderbookerID);

        return view('target.create', compact('products', 'orderbooker'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $unit = product_units::find($request->unitID);
            $target = targets::create(
                [
                    'branchID' => auth()->user()->branchID,
                    'orderbookerID' => $request->orderbookerID,
                    'productID' => $request->productID,
                    'pc' => $request->target * $unit->value,
                    'unitID' => $request->unitID,
                    'unit_value' => $unit->value,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'notes' => $request->notes,
                ]
            );

            DB::commit();

            return back()->with('success', 'Target Saved');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $target = targets::with('orderbooker', 'product.vendor', 'unit')->find($id);

        $query = DB::table('sale_details')
            ->where('orderbookerID', $target->orderbookerID)
            ->where('productID', $target->productID)
            ->whereBetween('date', [$target->startDate, $target->endDate]);

        $qtySold = $query->sum('pc');
        $target->sold = $qtySold / $target->unit_value;
        $targetQty = $target->pc / $target->unit_value;
        $target->remaining = $targetQty - $target->sold;

        $qtySoldForPer = $qtySold > $target->pc ? $target->pc : $qtySold;
        $target->per = $target->pc > 0 ? ($qtySoldForPer / $target->pc * 100) : 0;
        $target->totalPer = $target->per;

        if ($target->endDate >= now()->toDateString()) {
            $target->campain = 'Open';
            $target->campain_color = 'success';
            $target->display_status = 'ACTIVE';
            $target->display_status_color = 'success';
        } else {
            $target->campain = 'Closed';
            $target->campain_color = 'warning';
            $target->display_status = 'CLOSED';
            $target->display_status_color = 'danger';
        }

        if ($target->per >= 100) {
            $target->goal = 'Target Achieved';
            $target->goal_color = 'success';
            $target->achievement = 'ACHIEVED';
            $target->achievement_color = 'success';
        } else {
            $target->goal = 'Not Achieved';
            $target->goal_color = 'danger';
            $target->achievement = 'PENDING';
            $target->achievement_color = 'danger';
        }

        return view('target.view', compact('target'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(targets $targets)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $target = targets::find($id);
            $target->update(
                [
                    'pc' => $request->target * $target->unit_value,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'notes' => $request->notes,
                ]
            );

            DB::commit();

            return back()->with('success', 'Target Updated');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $target = targets::find($id);

        $target->delete();
        session()->forget('confirmed_password');

        return to_route('targets.index')->with('success', 'Target Deletes');
    }
}
