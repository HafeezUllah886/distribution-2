<?php

namespace App\Http\Controllers;

use App\Models\product_units;
use App\Models\products;
use App\Models\StockTransfer;
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
        $from = $request->start ?? date('Y-m-d');
        $to = $request->end ?? date('Y-m-d');
        $stockTransfers = StockTransfer::with('details')->where('branchID', auth()->user()->branchID)->whereBetween('date', [$from, $to])->get();
        $warehouses = warehouses::currentBranch()->get();

        return view('stock.transfer.index', compact('stockTransfers', 'warehouses', 'from', 'to'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->fromWarehouse == $request->toWarehouse) {
            return redirect()->back()->with('error', 'From and To Warehouse cannot be the same');
        }
        $warehouseFrom = warehouses::find($request->fromWarehouse);
        $warehouseTo = warehouses::find($request->toWarehouse);
        $products = products::currentBranch()->get();
        foreach ($products as $product) {
            $product->stock = getWarehouseProductStock($product->id, $warehouseFrom->id);
        }

        return view('stock.transfer.create', compact('products', 'warehouseFrom', 'warehouseTo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
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

            foreach ($ids as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $pc = $request->loose[$key] + ($request->qty[$key] * $unit->value);

                StockTransferDetails::create(
                    [
                        'stockTransferID' => $stockTransfer->id,
                        'productID' => $id,
                        'branchID' => Auth()->user()->branchID,
                        'qty' => $request->qty[$key],
                        'loose' => $request->loose[$key],
                        'pc' => $pc,
                        'unitID' => $unit->id,
                        'refID' => $ref,
                    ]
                );
                $fromWarehouse = warehouses::find($request->fromWarehouse);
                $toWarehouse = warehouses::find($request->toWarehouse);
                createStock($id, 0, $pc, now(), "Transfered to $toWarehouse->name:  $request->notes", $ref, $fromWarehouse->id);
                createStock($id, $pc, 0, now(), "Transfered from $fromWarehouse->name:  $request->notes", $ref, $toWarehouse->id);
            }
            DB::commit();

            return redirect()->route('stockTransfers.index')->with('success', 'Stock Transfer Created Successfully');
        } catch (\Exception $e) {
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
        $transfer = StockTransfer::where('refID', $ref)->first();
        $warehouseFrom = warehouses::find($transfer->from);
        $warehouseTo = warehouses::find($transfer->to);
        $notes = "Stock Transfer Date: $transfer->date | From: $warehouseFrom->name | To: $warehouseTo->name | Notes: $transfer->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $transfer->branchID, $transfer->refID, 'stock_transfer', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('stockTransfers.index')->with('success', 'Stock Transfer Delete Request Sent to Branch Admin');
    }
}
