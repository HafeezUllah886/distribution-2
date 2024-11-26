<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\stock;
use App\Models\stockAdjustment;
use App\Models\units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adjustments = stockAdjustment::orderBy('id', 'desc')->get();
        $products = products::all();
        $units = units::all();

        return view('stock.adjustment.index', compact('adjustments', 'products', 'units'));
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

        try
        {
            DB::beginTransaction();
            $ref = getRef();
            $unit = units::find($request->unitID);
            $qty = $request->qty * $unit->value;
            stockAdjustment::create(
                [
                    'productID' => $request->productID,
                    'unitID'    => $request->unitID,
                    'unitValue' => $unit->value,
                    'date'      => $request->date,
                    'type'      => $request->type,
                    'qty'       => $request->qty,
                    'notes'     => $request->notes,
                    'refID'     => $ref
                ]
            );

            if($request->type == 'Stock-In')
            {
               createStock($request->productID, $qty, 0, $request->date, "Stock-In: $request->notes", $ref);
            }
            else
            {
                createStock($request->productID, 0, $qty, $request->date, "Stock-Out: $request->notes", $ref);
            }

            DB::commit();
            return back()->with('success', "Stock Adjustment Created");
        }
        catch(\Exception $e)
        {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        try
        {
            DB::beginTransaction();
            stockAdjustment::where('refID', $ref)->delete();
            stock::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('stockAdjustments.index')->with('success', "Stock Adjustment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('stockAdjustments.index')->with('error', $e->getMessage());
        }
    }
}
