<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\branches;
use App\Models\order_details;
use App\Models\orders;
use App\Models\User;
use Illuminate\Http\Request;

class OrdersReportController extends Controller
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
        
        return view('reports.orders.index', compact('branches', 'areas', 'branch'));
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
        
        return view('reports.orders.filter', compact('customers', 'orderbookers', 'branch'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $areas = accounts::customer()->where('branchID', $request->branch);
        if($request->customer)
        {
            $areas = $areas->whereIn('id', $request->customer);
        }
        $areas = $areas->pluck('areaID')->toArray();

        $areas = area::whereIn('id', $areas)->select('id', 'name')->get();

        foreach($areas as $area)
        {
            $customers = accounts::customer()->where('branchID', $request->branch)->where('areaID', $area->id);
            if($request->customer)
            {
                $customers = $customers->whereIn('id', $request->customer);
            }
            $customers = $customers->get();

            foreach($customers as $customer)
            {
                $orders = order_details::whereBetween('date', [$from, $to])->where('customerID', $customer->id);
                if($request->orderbooker)
                {
                    $orders = $orders->whereIn('orderbookerID', $request->orderbooker);
                }
                $orders = $orders->get();
                $customer->orders = $orders;
            }
            $area->customers = $customers;
        }
           
            $branch = branches::find($request->branch);
            $branch = $branch->name;
        return view('reports.orders.details', compact('from', 'to', 'areas', 'branch'));
    }
}
