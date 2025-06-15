<?php


namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\orderbooker_customers;
use App\Models\sales;
use Illuminate\Http\Request;
use App\Models\User;

class invoicePaymentsReportController extends Controller
{
    public function index()
    {
        $orderbookers = User::orderbookers()->currentBranch()->get();
        return view('reports.invoice_payments.index', compact('orderbookers'));
    }


    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $orderbooker = $request->orderbooker;

        $customers = orderbooker_customers::where('orderbookerID', $orderbooker)->pluck('customerID')->toArray();
        $customers = accounts::whereIn('id', $customers)->get();

        foreach ($customers as $customer) {
            $sales = sales::with('payments')->where('customerID', $customer->id)->where('orderbookerID', $orderbooker)->whereBetween('date', [$from, $to])->get();

            $customer->sales = $sales;
        }

        $orderbooker = User::find($orderbooker);
        return view('reports.invoice_payments.details', compact('customers', 'from', 'to', 'orderbooker'));
    }
}
