<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\employee;
use App\Models\fixed_assets;
use App\Models\products;
use App\Models\User;
use Illuminate\Http\Request;

class BranchInvestmentReportController extends Controller
{
    public function index(Request $request)
    {

        if (auth()->user()->role == 'Admin') {
            $branches = branches::all();
        } else {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }

        return view('reports.branch_investment.index', compact('branches'));
    }

    public function data(Request $request)
    {
        $branch = $request->branch;
        $date = $request->current_date;
        $lastYearDate = $request->last_date;

        $customers = accounts::customer()->where('branchID', $branch)->get();

        foreach ($customers as $customer) {
            $customer->currentBalance = accountBalanceTillDate($customer->id, $date);
            $customer->lastYearBalance = accountBalanceTillDate($customer->id, $lastYearDate);

        }

        $vendors = accounts::vendor()->where('branchID', $branch)->get();

        foreach ($vendors as $vendor) {
            $vendor->currentBalance = accountBalanceTillDate($vendor->id, $date);
            $vendor->lastYearBalance = accountBalanceTillDate($vendor->id, $lastYearDate);

        }

        $business = accounts::business()->where('branchID', $branch)->get();

        foreach ($business as $busines) {
            $currentBalance = accountBalanceTillDate($busines->id, $date);
            $lastYearBalance = accountBalanceTillDate($busines->id, $lastYearDate);
            $busines->currentBalance = $currentBalance;
            $busines->lastYearBalance = $lastYearBalance;
        }

        $unloaders = accounts::unloader()->where('branchID', $branch)->get();

        foreach ($unloaders as $unloader) {
            $currentBalance = accountBalanceTillDate($unloader->id, $date);
            $lastYearBalance = accountBalanceTillDate($unloader->id, $lastYearDate);
            $unloader->currentBalance = $currentBalance;
            $unloader->lastYearBalance = $lastYearBalance;
        }
        $supplymans = accounts::supplyMen()->where('branchID', $branch)->get();

        foreach ($supplymans as $supplyman) {
            $currentBalance = accountBalanceTillDate($supplyman->id, $date);
            $lastYearBalance = accountBalanceTillDate($supplyman->id, $lastYearDate);
            $supplyman->currentBalance = $currentBalance;
            $supplyman->lastYearBalance = $lastYearBalance;
        }
        $freights = accounts::freight()->where('branchID', $branch)->get();

        foreach ($freights as $freight) {
            $currentBalance = accountBalanceTillDate($freight->id, $date);
            $lastYearBalance = accountBalanceTillDate($freight->id, $lastYearDate);
            $freight->currentBalance = $currentBalance;
            $freight->lastYearBalance = $lastYearBalance;
        }

        $staff = user::where('branchID', $branch)->get();
        foreach ($staff as $staf) {
            $currentBalance = userBalanceTillDate($staf->id, $date);
            $lastYearBalance = userBalanceTillDate($staf->id, $lastYearDate);
            $staf->currentBalance = $currentBalance;
            $staf->lastYearBalance = $lastYearBalance;
        }

        $personal = accounts::personal()->where('branchID', $branch)->get();
        foreach ($personal as $person) {
            $currentBalance = accountBalanceTillDate($person->id, $date);
            $lastYearBalance = accountBalanceTillDate($person->id, $lastYearDate);
            $person->currentBalance = $currentBalance;
            $person->lastYearBalance = $lastYearBalance;
        }

        $employees = employee::where('branchID', $branch)->get();
        foreach ($employees as $employee) {
            $currentBalance = employeeBalanceTillDate($employee->id, $date);
            $lastYearBalance = employeeBalanceTillDate($employee->id, $lastYearDate);
            $employee->currentBalance = $currentBalance;
            $employee->lastYearBalance = $lastYearBalance;
        }

        $products = products::where('branchID', $branch)->get();
        $totalCurrentStockValue = 0;
        $totalLastYearStockValue = 0;
        foreach ($products as $product) {
            $currentStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $date);
            $lastYearStockValue = branch_product_stock_value_cost_wise_till_date($product->id, $branch, $lastYearDate);
            $product->currentStockValue = $currentStockValue;
            $product->lastYearStockValue = $lastYearStockValue;

        }

        $investors = accounts::investor()->where('branchID', $branch)->get();

        foreach ($investors as $investor) {
            $currentBalance = accountBalanceTillDate($investor->id, $date);
            $lastYearBalance = accountBalanceTillDate($investor->id, $lastYearDate);
            $investor->currentBalance = abs($currentBalance);
            $investor->lastYearBalance = abs($lastYearBalance);
        }

        $totalInvestmentCurrent = $investors->sum('currentBalance');
        $totalInvestmentLastYear = $investors->sum('lastYearBalance');

        foreach ($investors as $investor) {
            $investor->currentPercentage = $totalInvestmentCurrent > 0 ? ($investor->currentBalance / $totalInvestmentCurrent) * 100 : 0;
            $investor->lastYearPercentage = $totalInvestmentLastYear > 0 ? ($investor->lastYearBalance / $totalInvestmentLastYear) * 100 : 0;
        }
        $fixed_assets = fixed_assets::with('sale')->where('branchID', $branch)->get();
        $totalCurrentFixedAssetsValue = 0;
        $totalLastYearFixedAssetsValue = 0;
        foreach ($fixed_assets as $fixed_asset) {
            $fixed_asset->currentBalance = 0;
            $fixed_asset->lastYearBalance = 0;
            // Include if: acquired on/before the date AND (never sold OR sold after the date)
            if ($fixed_asset->date <= $date && (is_null($fixed_asset->sale) || $fixed_asset->sale->date > $date)) {
                $fixed_asset->currentBalance = $fixed_asset->amount;
                $totalCurrentFixedAssetsValue += $fixed_asset->amount;
            }
            if ($fixed_asset->date <= $lastYearDate && (is_null($fixed_asset->sale) || $fixed_asset->sale->date > $lastYearDate)) {
                $fixed_asset->lastYearBalance = $fixed_asset->amount;
                $totalLastYearFixedAssetsValue += $fixed_asset->amount;
            }
        }

        $branch_name = branches::find($branch)->name;

        return view('reports.branch_investment.details', compact('date', 'lastYearDate', 'branch', 'customers', 'branch_name', 'vendors', 'business', 'unloaders', 'supplymans', 'freights', 'staff', 'employees', 'products', 'totalCurrentStockValue', 'totalLastYearStockValue', 'personal', 'investors', 'fixed_assets', 'totalCurrentFixedAssetsValue', 'totalLastYearFixedAssetsValue'));
    }
}
