<?php

namespace App\Http\Controllers;

use App\Models\order_reminder;
use App\Http\Controllers\Controller;
use App\Models\order_details;
use Illuminate\Http\Request;

class OrderReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function storeReminder($id)
    {
        $product = order_details::findOrFail($id);

        $orderID = $product->orderID;
        $product_name = $product->product->name;
        $customer = $product->order->customer->title;
        $orderbooker = $product->order->orderbooker->name;

        $check = order_reminder::where(['orderID'=> $orderID, 'product' => $product_name])->first();

        if($check){
            return response()->json(['msg' => 'exists']);
        }
        else{
            
        $reminder = new order_reminder();
        $reminder->branchID = auth()->user()->branchID;
        $reminder->orderID = $orderID;
        $reminder->customer = $customer;
        $reminder->orderbooker = $orderbooker;
        $reminder->product = $product_name;
        $reminder->unit = $product->unit->unit_name;
        $reminder->qty = $product->qty;
        $reminder->loose = $product->loose;
        $reminder->date = date('Y-m-d');
        $reminder->status = 'Pending';
        $reminder->save();

        return response()->json(['msg' => 'created']);
        }
    }

    public function index(Request $request)
    {
        $from = $request->from ?? firstDayOfMonth();
        $to = $request->to ?? now()->toDateString();
        $status = $request->status ?? 'All';

      /*   $reminders = order_reminder::where('branchID', auth()->user()->branchID)->whereBetween('date', [$from, $to])->get(); */

       if($status == 'All'){
            $reminders = order_reminder::where('branchID', auth()->user()->branchID)->whereBetween('date', [$from, $to])->orderBy('status', 'desc')->get();
        }else{
            $reminders = order_reminder::where('branchID', auth()->user()->branchID)->where('status', $status)->whereBetween('date', [$from, $to])->orderBy('status', 'desc')->get();
        }
        return view('order_reminders.index', compact('reminders', 'from', 'to', 'status'));
    }

    public function update(Request $request)
    {
        $reminder = order_reminder::findOrFail($request->id);
        $reminder->status = $request->status;
        $reminder->save();
        return back()->with('success', 'Reminder updated successfully');
    }
}
