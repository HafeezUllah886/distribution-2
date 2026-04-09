<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\branches;
use App\Models\employee;
use App\Models\fixed_assets;
use App\Models\products;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;

class BranchInvestmentReportController extends Controller
{
    public function index(Request $request)
    {

        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }

        
        return view('reports.branch_investment.index', compact('branches'));
    }


    public function data(Request $request)
    {
       $branch = $request->branch;
       $from = $request->from;
       $to = $request->to;

       $lastYearFrom = date('Y-m-d', strtotime($from . ' - 1 year'));
       $lastYearTo = date('Y-m-d', strtotime($to . ' - 1 year'));

       $customers = accounts::customer()->where('branchID', $branch)->get();
     
       foreach($customers as $customer)
       {
           $customer->currentBalance = accountBalanceTillDate($customer->id, $to);
           $customer->lastYearBalance = accountBalanceTillDate($customer->id, $lastYearTo);
          
       }

       $vendors = accounts::vendor()->where('branchID', $branch)->get();
  
       foreach($vendors as $vendor)
       {
           $vendor->currentBalance = accountBalanceTillDate($vendor->id, $to);
           $vendor->lastYearBalance = accountBalanceTillDate($vendor->id, $lastYearTo);
           
       }

       $business = accounts::business()->where('branchID', $branch)->get();
      
       foreach($business as $busines)
       {
           $currentBalance = accountBalanceTillDate($busines->id, $to);
           $lastYearBalance = accountBalanceTillDate($busines->id, $lastYearTo);
           $busines->currentBalance = $currentBalance;
           $busines->lastYearBalance = $lastYearBalance;
       }
      

       $staff = user::where('branchID', $branch)->get();
       foreach($staff as $staf)
       {
           $currentBalance = userBalanceTillDate($staf->id, $to);
           $lastYearBalance = userBalanceTillDate($staf->id, $lastYearTo);
           $staf->currentBalance = $currentBalance;
           $staf->lastYearBalance = $lastYearBalance;
       }

       $personal = accounts::personal()->where('branchID', $branch)->get();
       foreach($personal as $person)
       {
           $currentBalance = accountBalanceTillDate($person->id, $to);
           $lastYearBalance = accountBalanceTillDate($person->id, $lastYearTo);
           $person->currentBalance = $currentBalance;
           $person->lastYearBalance = $lastYearBalance;
       }

       $employees = employee::where('branchID', $branch)->get();
       foreach($employees as $employee)
       {
           $currentBalance = employeeBalanceTillDate($employee->id, $to);
           $lastYearBalance = employeeBalanceTillDate($employee->id, $lastYearTo);
           $employee->currentBalance = $currentBalance;
           $employee->lastYearBalance = $lastYearBalance;
       }

       $products = products::where('branchID', $branch)->get();
       $totalCurrentStockValue = 0;
       $totalLastYearStockValue = 0;
       foreach($products as $product)
       {
           $currentStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $to);
           $lastYearStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $lastYearTo);
           $product->currentStockValue = $currentStockValue;
           $product->lastYearStockValue = $lastYearStockValue;
           $totalCurrentStockValue += $currentStockValue;
           $totalLastYearStockValue += $lastYearStockValue;
       }

       $investors = accounts::investor()->where('branchID', $branch)->get();
      
       foreach($investors as $investor)
       {
           $currentBalance = accountBalanceTillDate($investor->id, $to);
           $lastYearBalance = accountBalanceTillDate($investor->id, $lastYearTo);
           $investor->currentBalance = $currentBalance;
           $investor->lastYearBalance = $lastYearBalance;
       }

       $fixed_assets= fixed_assets::where('branchID', $branch)->get();
       $totalCurrentFixedAssetsValue = 0;
       $totalLastYearFixedAssetsValue = 0;
       foreach($fixed_assets as $fixed_asset)
       {
        if($fixed_asset->date <= $to && ($fixed_asset->whereDoesntHave('sale') || $fixed_asset->sale->date <= $to))
        {
            $totalCurrentFixedAssetsValue += $fixed_asset->amount;
        }
        if($fixed_asset->date <= $lastYearTo && ($fixed_asset->whereDoesntHave('sale') || $fixed_asset->sale->date <= $lastYearTo))
        {
            $totalLastYearFixedAssetsValue += $fixed_asset->amount;
        }
       }

       $branch_name = branches::find($branch)->name;
        return view('reports.branch_investment.details', compact('from', 'to', 'lastYearFrom', 'lastYearTo', 'branch', 'customers', 'branch_name', 'vendors', 'business', 'staff', 'employees', 'products', 'totalCurrentStockValue', 'totalLastYearStockValue', 'personal', 'investors', 'fixed_assets', 'totalCurrentFixedAssetsValue', 'totalLastYearFixedAssetsValue'));
    }
}
