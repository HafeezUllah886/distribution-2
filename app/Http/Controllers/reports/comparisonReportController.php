<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\products;
use App\Models\transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;

class comparisonReportController extends Controller
{
    public function index()
    {
        $customers = accounts::customer()->get();
        return view('reports.comparison.index', compact('customers'));
    }

    public function data($from1, $to1, $from2, $to2, $customer)
    {
        $products = products::orderBy('catID')->get();
        
        foreach($products as $product)
        {
            $year1Sold = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.salesID')
            ->where('sales.customerID', $customer)  // Filter by customer ID
            ->where('sale_details.productID', $product->id)  // Filter by product ID
            ->whereBetween('sale_details.date', [$from1, $to1])  // Filter by date range
            ->sum('sale_details.qty');

            $year2Sold = DB::table('sales')
            ->join('sale_details', 'sales.id', '=', 'sale_details.salesID')
            ->where('sales.customerID', $customer)  // Filter by customer ID
            ->where('sale_details.productID', $product->id)  // Filter by product ID
            ->whereBetween('sale_details.date', [$from2, $to2])  // Filter by date range
            ->sum('sale_details.qty');

            $product->sold1 = $year1Sold;
            $product->sold2 = $year2Sold;

            $growth = calculateGrowthPercentage($year1Sold, $year2Sold);

            $product->growth = $growth;

        }
        $customer = accounts::find($customer);
        return view('reports.comparison.details', compact('from1', 'to1', 'from2', 'to2','customer','products'));
    }
}
