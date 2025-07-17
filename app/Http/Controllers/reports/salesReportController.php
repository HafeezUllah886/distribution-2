<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\branches;
use App\Models\sales;
use App\Models\User;
use Illuminate\Http\Request;

class salesReportController extends Controller
{
    public function index(Request $request)
    {

        $branch = $request->branch ?? auth()->user()->branchID;

        if(auth()->user()->role == "Admin")
        {
            $branches = branches::all();
        }
        else
        {
            $branches = branches::where('id', auth()->user()->branchID)->get();
        }

        $areas = area::where('branchID', $branch)->get();
        
        return view('reports.sales.index', compact('branches', 'areas', 'branch'));
    }

    public function filter(Request $request)
    {
       $customers = accounts::customer()->where('branchID', $request->branch);
       if($request->area)
       {
           $customers = $customers->where('areaID', $request->area);
       }
       $customers = $customers->get();
       $orderbookers = User::orderbookers()->where('branchID', $request->branch)->get();

       $branch = $request->branch;

       $area = "";
            if($request->area)
            {
                $area = implode(',', $request->area);
            }
            else
            {
                $area = "All";
            }
     
        return view('reports.sales.filter', compact('customers', 'orderbookers', 'branch', 'area'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;


        $customers = accounts::customer()->where('branchID', $request->branch);
        if($request->customer)
        {
            $customers = $customers->whereIn('id', $request->customer);
        }
        $customers = $customers->pluck('id')->toArray();

       
            $sales = sales::with('customer', 'orderbooker', 'supplyman')->whereBetween('date', [$from, $to])->whereIn('customerID', $customers);
            if($request->orderbooker)
            {
                $sales = $sales->whereIn('orderbookerID', $request->orderbooker);
            }
            $sales = $sales->get();
            $branch = branches::find($request->branch);
            $branch = $branch->name;
            
            $area = "";
            if($request->area == "All")
            {
                $area = "All";
            }
            else
            {
                $areas = explode(',', $request->area);
                $area = area::whereIn('id', $areas)->pluck('name')->toArray();
                $area = implode(',', $area);
            }

            $orderbookers = "";
            if($request->orderbooker)
            {
                $orderbookers = User::whereIn('id', $request->orderbooker)->pluck('name')->toArray();
                $orderbookers = implode(',', $orderbookers);
            }
            else
            {
                $orderbookers = "All";
            }

            $customers = "";
            if($request->customer)
            {
                $customers = accounts::customer()->where('branchID', $request->branch)->whereIn('id', $request->customer)->pluck('title')->toArray();
                $customers = implode(',', $customers);
            }
            else
            {
                $customers = "All";
            }
           

           
        return view('reports.sales.details', compact('from', 'to', 'sales', 'branch', 'area', 'orderbookers', 'customers'));
    }
}
