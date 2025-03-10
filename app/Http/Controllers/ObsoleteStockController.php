<?php

namespace App\Http\Controllers;

use App\Models\obsolete_stock;
use App\Http\Controllers\Controller;
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
    public function index()
    {
        $obsoletes = obsolete_stock::currentBranch()->orderBy('id', 'desc')->get();
        return view('stock.obsolete.index', compact('obsoletes'));
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
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            } 
            DB::beginTransaction();
            foreach($request->id as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $amount = $request->price[$key] * $pc;
                $ref = getRef();

                obsolete_stock::create(
                    [
                        'productID'       => $id,
                        'branchID'        => Auth()->user()->branchID,
                        'warehouseID'     => $request->warehouseID,
                        'unitID'          => $request->unit[$key],
                        'unitValue'       => $unit->value,
                        'pc'              => $pc,
                        'qty'             => $request->qty[$key],
                        'loose'           => $request->loose[$key],
                        'reason'          => $request->reason[$key],
                        'date'            => $request->date,
                        'notes'           => $request->notes[$key],
                        'refID'           => $ref,
                        'amount'          => $amount,
                        'price'           => $request->price[$key],
                    ]
                );

                $reason = $request->reason[$key];
                $notes = $request->notes[$key];
                createStock($id, 0, $pc, $request->date, "Obsolete | $reason | $notes", $ref, $request->warehouseID);
            }       

            DB::commit();
            return back()->with('success', "Obsolete Stock Created");
        }
        catch(\Exception $e)
        {
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
    public function destroy(obsolete_stock $obsolete_stock)
    {
        //
    }
}
