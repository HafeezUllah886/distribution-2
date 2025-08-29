<?php

namespace App\Http\Controllers;

use App\Models\product_units;
use App\Models\products;
use App\Models\stock;
use App\Models\stockAdjustment;
use App\Models\units;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->start ?? date('Y-m-d');
        $to = $request->end ?? date('Y-m-d');
        $adjustments = stockAdjustment::currentBranch()->whereBetween('date', [$from, $to])->orderBy('id', 'desc')->get();
        return view('stock.adjustment.index', compact('adjustments', 'from', 'to'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::currentBranch()->get();
        $units = units::all();
        $warehouses = warehouses::currentBranch()->get();
        return view('stock.adjustment.create', compact('products', 'units', 'warehouses'));
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
                $ref = getRef();

                stockAdjustment::create(
                    [
                        'productID'       => $id,
                        'branchID'        => Auth()->user()->branchID,
                        'warehouseID'     => $request->warehouseID,
                        'unitID'          => $request->unit[$key],
                        'unitValue'       => $unit->value,
                        'pc'              => $pc,
                        'qty'             => $request->qty[$key],
                        'loose'           => $request->loose[$key],
                        'type'            => $request->type,
                        'date'            => $request->date,
                        'notes'           => $request->notes[$key],
                        'refID'           => $ref,
                    ]
                );

                $notes = $request->notes[$key];

                if($request->type == 'Stock-In')
                {
                    createStock($id, $pc, 0, $request->date, "Stock Adj - In | $notes", $ref, $request->warehouseID);
                }
                else
                {
                    createStock($id, 0, $pc, $request->date, "Stock Adj - Out | $notes", $ref, $request->warehouseID);
                }
            }       

            DB::commit();
            return back()->with('success', "Stock Adjustment Created");
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
