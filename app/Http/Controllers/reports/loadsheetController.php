<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\sales;
use Illuminate\Http\Request;
use App\Models\User;

class loadsheetController extends Controller
{
    public function index()
    {
        $orderbookers = User::all();

        return view('reports.loadsheet.index', compact('orderbookers'));
    }

    public function data($id, $date)
    {
        $sales = sales::whereDate('date', $date)
        ->where('orderbookerID', $id)
        ->with('details') // Eager load sale details
        ->get();

        if ($sales->isEmpty()) {
            return back()->with('error', "No Data Found");
        }

        $salesData = [
            'sale_info' => $sales->map->toArray(), // Sale details for all sales
            'sale_details' => [], // Initialize as an empty array
            'total_sale_amount' => 0,
        ];

        $allProducts = []; // Store product details with accumulated quantities and amounts

        foreach ($sales as $sale) {
            $productSales = $sale->details->groupBy('productID');

            foreach ($productSales as $productID => $saleDetails) {
                $totalQty = $saleDetails->sum('qty');
                $totalAmount = $saleDetails->sum('ti');

                // Check if product exists in $allProducts
                if (!isset($allProducts[$productID])) {
                    $product = $saleDetails->first()->product; // Access product details
                    $allProducts[$productID] = [
                    'productID' => $productID,
                    'name' => $product->name,
                    'total_qty' => 0,
                    'total_amount' => 0,
                    'pack_size' => $product->unit->value
                    ];
                }

                $allProducts[$productID]['total_qty'] += $totalQty;
                $allProducts[$productID]['total_amount'] += $totalAmount;
            }

            $salesData['total_sale_amount'] += $sale->details->sum('ti');
            $salesData['orderbooker'] = $sale->orderbooker->name;
            $salesData['date'] = $sale->date;
        }

        $salesData['sale_details'] = array_values($allProducts); // Convert to a plain array
        return view('reports.loadsheet.details', compact('salesData'));
    }

}
