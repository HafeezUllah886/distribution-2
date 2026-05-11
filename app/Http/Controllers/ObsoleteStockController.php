<?php

namespace App\Http\Controllers;

use App\Models\obsolete_stock;
use App\Models\product_units;
use App\Models\products;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObsoleteStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $from = $request->start ?? date('Y-m-d');
        $to = $request->end ?? date('Y-m-d');
        $reason = $request->reason ?? 'All';
        $obsoletes = obsolete_stock::currentBranch()->whereBetween('date', [$from, $to])->orderBy('id', 'desc');
        if ($reason != 'All') {
            $obsoletes->where('reason', $reason);
        }
        $obsoletes = $obsoletes->get();

        return view('stock.obsolete.index', compact('obsoletes', 'from', 'to', 'reason'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::currentBranch()->get();
        $warehouses = warehouses::currentBranch()->get();

        return view('stock.obsolete.create', compact('products', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($request->isNotFilled('id')) {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            foreach ($request->id as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $amount = $request->price[$key] * $pc;
                $ref = getRef();

                obsolete_stock::create(
                    [
                        'productID' => $id,
                        'branchID' => Auth()->user()->branchID,
                        'warehouseID' => $request->warehouseID,
                        'unitID' => $request->unit[$key],
                        'unitValue' => $unit->value,
                        'pc' => $pc,
                        'qty' => $request->qty[$key],
                        'loose' => $request->loose[$key],
                        'reason' => $request->reason[$key],
                        'date' => $request->date,
                        'notes' => $request->notes[$key],
                        'refID' => $ref,
                        'amount' => $amount,
                        'price' => $request->price[$key],
                    ]
                );

                $reason = $request->reason[$key];
                $notes = $request->notes[$key];
                createStock($id, 0, $pc, $request->date, "Obsolete | $reason | $notes", $ref, $request->warehouseID);
            }

            DB::commit();

            return back()->with('success', 'Obsolete Stock Created');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, obsolete_stock $obsolete_stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        $obsolete = obsolete_stock::where('refID', $ref)->first();
        $warehouse = warehouses::find($obsolete->warehouseID);
        $notes = "Obsolete Stock Date: $obsolete->date | Warehouse: $warehouse->name | Reason: $obsolete->reason | Notes: $obsolete->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $obsolete->branchID, $obsolete->refID, 'obsolete_stock', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('obsolete.index')->with('success', 'Obsolete Stock Delete Request Sent to Branch Admin');

    }
}
