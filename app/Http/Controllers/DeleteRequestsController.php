<?php

namespace App\Http\Controllers;

use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\delete_requests;
use App\Models\expenses;
use App\Models\method_transactions;
use App\Models\order_delivery;
use App\Models\orders;
use App\Models\sales;
use App\Models\staffPayments;
use App\Models\stock;
use App\Models\transactions;
use App\Models\users_transactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeleteRequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $from = $request->from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $request->to ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $status = $request->status ?? 'pending';

        $delete_req = delete_requests::with('user')
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);

        if ($status != 'all') {
            $delete_req->where('status', $status);
        }

        $delete_req = $delete_req->orderBy('id', 'desc')->get();

        return view('delete_request.index', compact('delete_req', 'from', 'to', 'status'));
    }

    public function approve($id)
    {
        $deleteRequest = delete_requests::find($id);

        if ($deleteRequest->status != 'pending') {
            return redirect()->back()->with('error', 'Request already '.$deleteRequest->status);
        }

        $result = null;
        if ($deleteRequest->model == 'sales') {
            $result = $this->deleteSales($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'expenses') {
            $result = $this->deleteExpense($deleteRequest->refID);
        }

        // Generic approval for other models if any
        $deleteRequest->update(['status' => 'approved']);
        if (isset($result) && $result['status'] == 'error') {
            return to_route('delete_request.index')->with('error', $result['msg']);
        } else {
            return to_route('delete_request.index')->with('success', 'Delete Request Approved');
        }
    }

    public function reject($id)
    {
        $deleteRequest = delete_requests::find($id);

        if ($deleteRequest->status != 'pending') {
            return redirect()->back()->with('error', 'Request already '.$deleteRequest->status);
        }

        $deleteRequest->status = 'rejected';
        $deleteRequest->save();

        return to_route('delete_request.index')->with('success', 'Delete Request Rejected');
    }

    public function destroy($id)
    {
        $deleteRequest = delete_requests::find($id);
        $deleteRequest->delete();

        return to_route('delete_request.index')->with('success', 'Delete Request Record Deleted');
    }

    public function deleteSales($ref)
    {
        try {
            DB::beginTransaction();
            $sale = sales::where('refID', $ref)->first();
            if ($sale) {
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
                    if ($order_status) {
                        $order_status->update(['status' => 'Under Process']);
                    }
                    order_delivery::where('refID', $sale->refID)->delete();
                }
                $sale->delete();
                $this->deleteExpense($ref);
            }
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Sale Deleted',
                'status' => 'success',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');

            return [
                'msg' => $e->getMessage(),
                'status' => 'error',
            ];
        }

    }

    public function deleteExpense($ref)
    {
        try {
            DB::beginTransaction();
            expenses::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            staffPayments::where('refID', $ref)->delete();
            $sale = sales::where('refID', $ref)->first();
            if ($sale) {
                $sale->update(['has_expense' => 0, 'expense_amount' => 0]);
            }
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Expense Deleted',
                'status' => 'success',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            session()->forget('confirmed_password');

            return [
                'msg' => $e->getMessage(),
                'status' => 'error',
            ];
        }
    }
}
