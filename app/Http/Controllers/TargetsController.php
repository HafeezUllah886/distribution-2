<?php

namespace App\Http\Controllers;

use App\Models\orderbooker_products;
use App\Models\product_units;
use App\Models\products;
use App\Models\targetDetails;
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
        $targets = targets::currentBranch()->whereBetween('endDate', [$start, $end])->orderBy('endDate', 'desc')->get();
        foreach ($targets as $target) {
            $totalTarget = 0;
            $totalSold = 0;

            foreach ($target->details as $detail) {
                $query = DB::table('sale_details')
                    ->where('orderbookerID', $target->orderbookerID)
                    ->where('productID', $detail->productID)
                    ->whereBetween('date', [$target->startDate, $target->endDate]);

                $qtySold = $query->sum('pc');
                $detail->sold = $qtySold / $detail->unit_value;
                $targetQty = $detail->pc / $detail->unit_value;

                if ($qtySold > $targetQty) {
                    $qtySold = $targetQty;
                }
                $detail->per = $targetQty > 0 ? ($qtySold / $targetQty * 100) : 0;

                $totalTarget += $targetQty;
                $totalSold += $qtySold;
            }
            $totalPer = $totalTarget > 0 ? ($totalSold / $totalTarget * 100) : 0;
            $target->totalPer = $totalPer;

            if ($target->endDate >= now()->toDateString()) {

                $target->campain = 'Open';
                $target->campain_color = 'success';
            } else {
                $target->campain = 'Closed';
                $target->campain_color = 'warning';
            }

            if ($totalPer >= 100) {
                $target->goal = 'Target Achieved';
                $target->goal_color = 'success';
            } elseif ($target->endDate >= now()->toDateString() && $totalPer < 100) {
                $target->goal = 'In Progress';
                $target->goal_color = 'info';
            } else {
                $target->goal = 'Not Achieved';
                $target->goal_color = 'danger';
            }
        }

        $orderbookers = User::orderbookers()->currentBranch()->active()->get();

        return view('target.index', compact('targets', 'start', 'end', 'orderbookers'));
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
            $target = targets::create(
                [
                    'branchID' => auth()->user()->branchID,
                    'orderbookerID' => $request->orderbookerID,
                    'startDate' => $request->startDate,
                    'endDate' => $request->endDate,
                    'notes' => $request->notes,
                ]
            );

            $ids = $request->id;

            foreach ($ids as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                targetDetails::create(
                    [
                        'targetID' => $target->id,
                        'productID' => $id,
                        'pc' => $request->qty[$key],
                        'unitID' => $unit->id,
                        'unit_value' => $unit->value,
                    ]
                );
            }
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
    public function show(targets $target)
    {
        $totalTarget = 0;
        $totalSold = 0;

        foreach ($target->details as $detail) {
            $query = DB::table('sale_details')
                ->where('orderbookerID', $target->orderbookerID)
                ->whereBetween('date', [$target->startDate, $target->endDate]);

            if ($detail->type == 'Product') {
                $query->where('productID', $detail->productID);
            } else {
                $query->join('products', 'sale_details.productID', '=', 'products.id')
                    ->where('products.catID', $detail->categoryID);
            }

            $qtySold = $query->sum('sale_details.qty');

            $targetQty = $detail->qty;

            if ($qtySold > $targetQty) {
                $qtySold = $targetQty;
            }
            $detail->sold = $qtySold;
            $detail->per = $targetQty > 0 ? ($qtySold / $targetQty * 100) : 0;

            $totalTarget += $targetQty;
            $totalSold += $qtySold;
        }
        $totalPer = $totalTarget > 0 ? ($totalSold / $totalTarget * 100) : 0;
        $target->totalPer = $totalPer;

        if ($target->endDate >= now()->toDateString()) {

            $target->campain = 'Open';
            $target->campain_color = 'success';
        } else {
            $target->campain = 'Closed';
            $target->campain_color = 'warning';
        }

        if ($totalPer >= 100) {
            $target->goal = 'Target Achieved';
            $target->goal_color = 'success';
        } elseif ($target->endDate >= now()->toDateString() && $totalPer < 100) {
            $target->goal = 'In Progress';
            $target->goal_color = 'info';
        } else {
            $target->goal = 'Not Achieved';
            $target->goal_color = 'danger';
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
    public function update(Request $request, targets $targets)
    {
        //
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
