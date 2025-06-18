<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Http\Controllers\Controller;
use App\Models\product_units;
use App\Models\products;
use App\Models\stock;
use App\Models\StockTransferDetails;
use App\Models\warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->start ?? firstDayOfMonth();
        $to = $request->end ?? lastDayOfMonth();
        $stockTransfers = StockTransfer::with('details')->where('branchID', auth()->user()->branchID)->whereBetween('date', [$from, $to])->get();
        $warehouses = warehouses::currentBranch()->get();
       
        return view('stock.transfer.index', compact('stockTransfers', 'warehouses', 'from', 'to'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if($request->fromWarehouse == $request->toWarehouse){
            return redirect()->back()->with('error', 'From and To Warehouse cannot be the same');
        }
            $warehouseFrom = warehouses::find($request->fromWarehouse);
            $warehouseTo = warehouses::find($request->toWarehouse);
            $products = products::currentBranch()->get();
            foreach($products as $product){
               $product->stock = getWarehouseProductStock($product->id, $warehouseFrom->id);
            }

        return view('stock.transfer.create', compact('products', 'warehouseFrom', 'warehouseTo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            DB::beginTransaction();
            $ref = getRef();
            $stockTransfer = StockTransfer::create([
                'branchID' => auth()->user()->branchID,
                'from' => $request->fromWarehouse,
                'to' => $request->toWarehouse,
                'date' => now(),
                'notes' => $request->notes,
                'refID' => $ref,
                'createdBy' => auth()->user()->id,
            ]);

            $ids = $request->id;

            foreach($ids as $key => $id)
            {
                $unit = product_units::find($request->unit[$key]);
                $pc =   $request->loose[$key] + ($request->qty[$key] * $unit->value);

                StockTransferDetails::create(
                    [
                        'stockTransferID' => $stockTransfer->id,
                        'productID'     => $id,
                        'branchID'        => Auth()->user()->branchID,
                        'qty'           => $request->qty[$key],
                        'loose'         => $request->loose[$key],
                        'pc'            => $pc,
                        'unitID'        => $unit->id,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, 0, $pc, now(), "Transfer:  $request->notes", $ref, $request->fromWarehouse);
                createStock($id, $pc, 0, now(), "Transfer:  $request->notes", $ref, $request->toWarehouse);
            }
            DB::commit();
            return redirect()->route('stockTransfers.index')->with('success', 'Stock Transfer Created Successfully');
        }catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stockTransfer = StockTransfer::with('details')->find($id);
        return view('stock.transfer.details', compact('stockTransfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockTransfer $stockTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockTransfer $stockTransfer)
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
            $transfer = StockTransfer::where('refID', $ref)->first();
            stock::where('refID', $ref)->delete();
            $transfer->details()->delete();
            $transfer->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('stockTransfers.index')->with('success', "Stock Transfer Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('stockTransfers.index')->with('error', $e->getMessage());
        }
    }
}
