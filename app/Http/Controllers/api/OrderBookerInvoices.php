<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\sales;
use Illuminate\Http\Request;

class OrderBookerInvoices extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->start_date ?? firstDayOfMonth();
        $end_date = $request->end_date ?? lastDayOfMonth();

        $invoices = sales::with('details')->without('customer', 'supplyman', 'orderbooker', 'branch')->where('orderbookerID', $request->user()->id)->whereBetween('date', [$start_date, $end_date])->get();

        $data = [];
        foreach($invoices as $invoice)
        {
            $data[] = [
                'salesID' => $invoice->id,
                'customer' => $invoice->customer->title,
                'area' => $invoice->customer->area->name
                'supply_man' => $invoice->supplyman->title,
                'orderID' => $invoice->orderID,
                'invoice_date' => $invoice->date,
                'order_date' => $invoice->orderdate,
                'bilty_no' => $invoice->bilty,
                'transporter' => $invoice->transporter,
                'notes' => $invoice->notes,
                'bill_amount' => $invoice->net,
                'payments' => $invoice->payments()->select('method', 'number', 'bank', 'cheque_date', 'amount', 'date', 'notes')->get(),
                'products' => $invoice->details()->with(['product', 'unit'])->get()->map(function($detail) {
                    return [
                        'product_name' => $detail->product->name ?? null,
                        'unit_name' => $detail->unit->unit_name ?? null,
                        'pack_size' => $detail->unit->value ?? null,
                        'pack_qty' => $detail->qty,
                        'loose' => $detail->loose,
                        'bonus' => $detail->bonus,
                        'price' => $detail->price,
                        'discount' => $detail->discount,
                        'discount_percentage' => $detail->discountp,
                        'discount_percentage_value' => $detail->discountvalue,
                        'fright' => $detail->fright,
                        'labor' => $detail->labor,
                        'claim' => $detail->claim,
                        'net_price' => $detail->netprice,
                        'amount' => $detail->amount
                    ];
                })
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Invoices retrieved successfully',
                'data' => [
                    'invoices' => $data,
                    'start_date' => $start_date,
                    'end_date' => $end_date,    
                ],
            ]
        ], 200);
    }
}
