<?php


namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\area;
use App\Models\orderbooker_customers;
use App\Models\sales;
use Illuminate\Http\Request;
use App\Models\User;

class invoicePaymentsReportController extends Controller
{
    public function index()
    {
        $orderbookers = User::orderbookers()->currentBranch()->active()->get();
        $customers = accounts::customer()->currentBranch()->get();
        $areas = area::currentBranch()->get();
        return view('reports.invoice_payments.index', compact('orderbookers', 'customers', 'areas'));
    }


    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $orderbooker = $request->orderbooker;
        $customer = $request->customer;
        $area = $request->area ?? 'All';
        $type = $request->type;

        if ($customer != 'All') {
            $customers = accounts::where('id', $customer)->get();
        } else {
            if($area != 'All') {
                $customers = accounts::customer()->whereIn('areaID', $area)->get();
            } else {
                $customers = accounts::customer()->currentBranch()->orderBy('areaID', 'asc')->get();
            }
        }

        $areas = $customers->pluck('areaID')->toArray();


        foreach ($customers as $customer1) {

            $sales = sales::with('payments')->where('customerID', $customer1->id)->whereBetween('date', [$from, $to]);
            if($orderbooker != "All")
            {
                $sales->where('orderbookerID', $orderbooker);
            }
            if($type != "All")
            {
                if($type == "Paid")
                {
                    $sales->paidStatus();
                }
                if($type == "Due")
                {
                    $sales->dueStatus();
                }
            }
            $customer1->sales = $sales->get();
        }

        $data = [];

        foreach ($areas as $area) {
            $areaName = area::find($area)->name;
            $data[$areaName]= 
            [
                'customers' => $customers->where('areaID', $area)->values(),
            ];
        }

        if($orderbooker != "All")
        {
            $orderbooker = User::find($orderbooker)->name;
        }
        if($customer != "All")
        {
            $customer = accounts::find($customer)->title;
        }

        return view('reports.invoice_payments.details', compact('data', 'from', 'to', 'orderbooker', 'customer', 'area', 'type'));
    }
}
