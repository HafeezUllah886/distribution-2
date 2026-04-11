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
       $date = $request->date;

       $lastYearDate = date('Y-m-d', strtotime($date . ' - 1 year'));

       $customers = accounts::customer()->where('branchID', $branch)->get();
     
       foreach($customers as $customer)
       {
           $customer->currentBalance = accountBalanceTillDate($customer->id, $date);
           $customer->lastYearBalance = accountBalanceTillDate($customer->id, $lastYearDate);
          
       }

       $vendors = accounts::vendor()->where('branchID', $branch)->get();
  
       foreach($vendors as $vendor)
       {
           $vendor->currentBalance = accountBalanceTillDate($vendor->id, $date);
           $vendor->lastYearBalance = accountBalanceTillDate($vendor->id, $lastYearDate);
           
       }

       $business = accounts::business()->where('branchID', $branch)->get();
      
       foreach($business as $busines)
       {
           $currentBalance = accountBalanceTillDate($busines->id, $date);
           $lastYearBalance = accountBalanceTillDate($busines->id, $lastYearDate);
           $busines->currentBalance = $currentBalance;
           $busines->lastYearBalance = $lastYearBalance;
       }
      

       $staff = user::where('branchID', $branch)->get();
       foreach($staff as $staf)
       {
           $currentBalance = userBalanceTillDate($staf->id, $date);
           $lastYearBalance = userBalanceTillDate($staf->id, $lastYearDate);
           $staf->currentBalance = $currentBalance;
           $staf->lastYearBalance = $lastYearBalance;
       }

       $personal = accounts::personal()->where('branchID', $branch)->get();
       foreach($personal as $person)
       {
           $currentBalance = accountBalanceTillDate($person->id, $date);
           $lastYearBalance = accountBalanceTillDate($person->id, $lastYearDate);
           $person->currentBalance = $currentBalance;
           $person->lastYearBalance = $lastYearBalance;
       }

       $employees = employee::where('branchID', $branch)->get();
       foreach($employees as $employee)
       {
           $currentBalance = employeeBalanceTillDate($employee->id, $date);
           $lastYearBalance = employeeBalanceTillDate($employee->id, $lastYearDate);
           $employee->currentBalance = $currentBalance;
           $employee->lastYearBalance = $lastYearBalance;
       }

       $products = products::where('branchID', $branch)->get();
       $totalCurrentStockValue = 0;
       $totalLastYearStockValue = 0;
       foreach($products as $product)
       {
           $currentStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $date);
           $lastYearStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $lastYearDate);
           $product->currentStockValue = $currentStockValue;
           $product->lastYearStockValue = $lastYearStockValue;
           $totalCurrentStockValue += $currentStockValue;
           $totalLastYearStockValue += $lastYearStockValue;
       }

       $investors = accounts::investor()->where('branchID', $branch)->get();
      
       foreach($investors as $investor)
       {
           $currentBalance = accountBalanceTillDate($investor->id, $date);
           $lastYearBalance = accountBalanceTillDate($investor->id, $lastYearDate);
           $investor->currentBalance = $currentBalance;
           $investor->lastYearBalance = $lastYearBalance;
       }

       $fixed_assets= fixed_assets::where('branchID', $branch)->get();
       $totalCurrentFixedAssetsValue = 0;
       $totalLastYearFixedAssetsValue = 0;
       foreach($fixed_assets as $fixed_asset)
       {
        if($fixed_asset->date <= $date && ($fixed_asset->whereDoesntHave('sale') || $fixed_asset->sale->date <= $date))
        {
            $totalCurrentFixedAssetsValue += $fixed_asset->amount;
        }
        if($fixed_asset->date <= $lastYearDate && ($fixed_asset->whereDoesntHave('sale') || $fixed_asset->sale->date <= $lastYearDate))
        {
            $totalLastYearFixedAssetsValue += $fixed_asset->amount;
        }
       }

       $branch_name = branches::find($branch)->name;
        return view('reports.branch_investment.details', compact('date', 'lastYearDate', 'branch', 'customers', 'branch_name', 'vendors', 'business', 'staff', 'employees', 'products', 'totalCurrentStockValue', 'totalLastYearStockValue', 'personal', 'investors', 'fixed_assets', 'totalCurrentFixedAssetsValue', 'totalLastYearFixedAssetsValue'));
    }
}
