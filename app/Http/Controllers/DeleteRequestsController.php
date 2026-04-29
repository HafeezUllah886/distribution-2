<?php

namespace App\Http\Controllers;

use App\Models\delete_requests;
use App\Models\order_delivery;
use App\Models\orders;
use App\Models\sales;
use App\Models\stock;
use App\Models\transactions;
use Illuminate\Support\Facades\DB;

class DeleteRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function approve($id)
    {

        $deleteRequest = delete_requests::find($id);
        if ($deleteRequest->model == 'Sales') {
            try {
                DB::beginTransaction();
                $sale = sales::where('refID', $deleteRequest->refID)->first();
                foreach ($sale->payments as $payment) {
                    transactions::where('refID', $payment->refID)->delete();
                    $payment->delete();
                }
                foreach ($sale->details as $product) {
                    stock::where('refID', $product->refID)->delete();
                    $product->delete();
                }
                transactions::where('refID', $sale->refID)->delete();

                $order = order_delivery::where('refID', $sale->refID)->first();
                if ($order) {
                    $order_id = $order->orderID;

                    $order_status = orders::find($order_id);
                    $order_status->update(
                        [
                            'status' => 'Under Process',
                        ]
                    );

                    order_delivery::where('refID', $sale->refID)->delete();
                }
                $sale->delete();

                DB::commit();
                session()->forget('confirmed_password');

                return to_route('sale.index')->with('success', 'Sale Deleted');
            } catch (\Exception $e) {
                DB::rollBack();
                session()->forget('confirmed_password');

                return to_route('sale.index')->with('error', $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Delete Request Approved');
    }

    public function reject($id)
    {

        $deleteRequest = delete_requests::find($id);
        $deleteRequest->status = 'rejected';
        $deleteRequest->save();

        return redirect()->back()->with('success', 'Delete Request Rejected');
    }

    //
}
