<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\expenses;
use App\Models\purchase;
use App\Models\sales;
use Illuminate\Http\Request;

class adminDashboardController extends Controller
{
    public function index($branch = null, $from = null, $to = null)
    {
        $branch1 = $branch ? $branch : 'All';
        $from = $from ? $from : firstDayOfMonth();
        $to = $to ? $to : lastDayOfMonth();

        $branches = branches::all();

        if($branch1 == 'All')
        {
            $sales = sales::whereBetween('date', [$from, $to])->sum('net');
            $purchases = purchase::whereBetween('orderdate', [$from, $to])->sum('net');
            $customers = accounts::customer()->get();
            $vendors = accounts::vendor()->get();
            $business = accounts::business()->get();
            $expenses = expenses::whereBetween('date', [$from, $to])->sum('amount');
        }
        else
        {
            $sales = sales::whereBetween('date', [$from, $to])->where('branchID', $branch1)->sum('net');
            $purchases = purchase::whereBetween('orderdate', [$from, $to])->where('branchID', $branch1)->sum('net');
            $customers = accounts::customer()->where('branchID', $branch1)->get();
            $vendors = accounts::vendor()->where('branchID', $branch1)->get();
            $business = accounts::business()->where('branchID', $branch1)->get();
            $expenses = expenses::whereBetween('date', [$from, $to])->where('branchID', $branch1)->sum('amount');
        }

        $customerBalance = 0;
        foreach($customers as $customer)
        {
            $customerBalance += accountTillDateBalance($customer->id, $to);
        }

        $vendorBalance = 0;
        foreach($vendors as $vendor)
        {
            $vendorBalance += accountTillDateBalance($vendor->id, $to);
        }

        $businessBalance = 0;
        foreach($business as $bus)
        {
            $businessBalance += accountTillDateBalance($bus->id, $to);
        }

        return view('dashboard.index', compact(
            'branch1', 
            'from', 
            'to', 
            'branches', 
            'sales', 
            'purchases', 
            'customerBalance', 
            'vendorBalance', 
            'businessBalance', 
            'expenses'
        ));
    }
}
