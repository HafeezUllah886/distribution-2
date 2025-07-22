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
           $customers = $customers->whereIn('areaID', $request->area);
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
        
        return view('reports.orders.filter', compact('customers', 'orderbookers', 'branch', 'area'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;


        $areas = accounts::customer()->where('branchID', $request->branch);
        if($request->area != "All")
        {
            $area = explode(',', $request->area);
            $areas = $areas->whereIn('areaID', $area);
        }
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

                $orders = orders::whereBetween('date', [$from, $to])->where('customerID', $customer->id);
                if($request->orderbooker)
                {
                    $orders = $orders->whereIn('orderbookerID', $request->orderbooker);
                }
                $orders = $orders->withSum('details', 'pc')->withSum('delivered_items', 'pc')->get();

                $completedOrderIDs = $orders->map(function($order){
                    return $order->details_sum_pc == $order->delivered_items_sum_pc ? $order->id : null;
                })->toArray();

                $pendingOrderIDs = $orders->map(function($order){
                    return  $order->delivered_items_sum_pc < $order->details_sum_pc ? $order->id : null;
                })->toArray();

                $allOrderIDs = $orders->pluck('id')->toArray();

                $customer->orders = null;

                if($request->status == "All")
                {
                    $orderDetails = order_details::whereIn('orderID', $allOrderIDs);
                }
                else if($request->status == "Completed")
                {
                    $orderDetails = order_details::whereIn('orderID', $completedOrderIDs);
                }
                else if($request->status == "Pending")
                {
                    $orderDetails = order_details::whereIn('orderID', $pendingOrderIDs);
                }

                    if($request->orderbooker)
                    {
                        $orderDetails = $orderDetails->whereIn('orderbookerID', $request->orderbooker);
                    }
                    $orderDetails = $orderDetails->get();
    
                    $customer->orders = $orderDetails;
            }
            $area->customers = $customers;
        }
           
            $branch = branches::find($request->branch);
            $branch = $branch->name;

            $area1 = "";
            if($request->area == "All")
            {
                $area1 = "All";
            }
            else
            {
                $areas1 = explode(',', $request->area);
                $area1 = area::whereIn('id', $areas1)->pluck('name')->toArray();
                $area1 = implode(',', $area1);
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

            $status = $request->status;
           
        return view('reports.orders.details', compact('from', 'to', 'areas', 'branch', 'area1', 'orderbookers', 'customers', 'status'));
    }
}
