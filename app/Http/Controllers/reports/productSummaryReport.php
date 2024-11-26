<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\products;
use App\Models\sale_details;
use Illuminate\Http\Request;

class productSummaryReport extends Controller
{
    public function index()
    {
        $topProducts = products::withSum('saleDetails', 'qty')->withSum('saleDetails', 'ti')
        ->orderByDesc('sale_details_sum_qty')
        ->get();

        $topProductsArray = [];

        foreach($topProducts as $product)
        {
            $stock = getStock($product->id);
            $price = avgSalePrice('all', 'all', $product->id);
            $pprice = avgPurchasePrice('all', 'all', $product->id);
            
            $ppu = $price - $pprice;
            $profit = $ppu * $product->sale_details_sum_qty;
            $stockValue = $pprice * $stock;

            $topProductsArray[] = ['name' => $product->name, 'unit' => $product->unit->name, 'unitValue' => $product->unit->value, 'price' => $price, 'pprice' => $pprice, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue, 'amount' => $product->sale_details_sum_ti, 'sold' => $product->sale_details_sum_qty];
        }

        return view('reports.productSummary.details', compact('topProductsArray'));
    }

    
}
