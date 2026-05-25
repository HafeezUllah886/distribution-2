<?php

namespace App\Http\Controllers;

use App\Models\accountsAdjustment;
use App\Models\bulk_payments;
use App\Models\cheques;
use App\Models\currency_transactions;
use App\Models\customerAdvanceConsumption;
use App\Models\CustomerAdvancePayment;
use App\Models\delete_requests;
use App\Models\User;
use App\Models\employee_ledger;
use App\Models\employee_ledger_adjustment;
use App\Models\expenses;
use App\Models\fixed_assets;
use App\Models\fixed_assets_sales;
use App\Models\generate_salary;
use App\Models\issue_advance;
use App\Models\issue_misc;
use App\Models\issue_salary;
use App\Models\method_transactions;
use App\Models\obsolete_stock;
use App\Models\order_delivery;
use App\Models\orders;
use App\Models\payments;
use App\Models\paymentsReceiving;
use App\Models\purchase;
use App\Models\purchase_order;
use App\Models\purchase_order_delivery;
use App\Models\returns;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\staffAmountAdjustment;
use App\Models\staffPayments;
use App\Models\stock;
use App\Models\stockAdjustment;
use App\Models\StockTransfer;
use App\Models\transactions;
use App\Models\transactions_que;
use App\Models\transfer;
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
        $requested_by = $request->requested_by ?? 'all';
        $model_filter = $request->model_filter ?? 'all';

        $delete_req = delete_requests::with('user')
            ->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ]);

        if ($status != 'all') {
            $delete_req->where('status', $status);
        }

        if ($requested_by != 'all') {
            $delete_req->where('user_id', $requested_by);
        }

        if ($model_filter != 'all') {
            $delete_req->where('model', $model_filter);
        }

        $delete_req = $delete_req->orderBy('id', 'desc')->currentBranch()->get();

        $users = User::where('branchID', auth()->user()->branchID)->orderBy('name')->get();
        $models = delete_requests::currentBranch()->distinct()->pluck('model')->filter()->sort()->values();

        return view('delete_request.index', compact('delete_req', 'from', 'to', 'status', 'requested_by', 'model_filter', 'users', 'models'));
    }

    public function approve(Request $request, $id)
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
        if ($deleteRequest->model == 'purchase') {
            $result = $this->deletePurchase($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'returns') {
            $result = $this->deleteSalesReturns($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'stock_transfer') {
            $result = $this->deleteStockTransfer($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'obsolete_stock') {
            $result = $this->deleteObsoleteStock($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'accounts_adjustment') {
            $result = $this->deleteAccountsAdjustment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'staff_payment') {
            $result = $this->deleteStaffPayment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'payment') {
            $result = $this->deletePayment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'payment_receiving') {
            $result = $this->deletePaymentReceiving($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'transfer') {
            $result = $this->deleteTransfer($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'stock_adjustment') {
            $result = $this->deleteStockAdjustment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'issue_salary') {
            $result = $this->deleteIssueSalary($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'customer_advance') {
            $result = $this->deleteCustomerAdvance($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'fixed_asset') {
            $result = $this->deleteFixedAsset($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'issue_misc') {
            $result = $this->deleteIssueMisc($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'issue_advance') {
            $result = $this->deleteIssueAdvance($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'generate_salary') {
            $result = $this->deleteGenerateSalary($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'employee_ledger_adjustment') {
            $result = $this->deleteEmployeeLedgerAdjustment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'staff_amount_adjustment') {
            $result = $this->deleteStaffAmountAdjustment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'bulk_payment') {
            $result = $this->deleteBulkPayment($deleteRequest->refID);
        }
        if ($deleteRequest->model == 'customer_advance_consumption') {
            $result = $this->deleteCustomerAdvanceConsumption($deleteRequest->refID);
        }

        // Generic approval for other models if any
        $deleteRequest->update(['status' => 'approved']);

        // Send notification to the user who requested the deletion
        $modelName = ucfirst(str_replace('_', ' ', $deleteRequest->model));
        createUserNotification(
            $deleteRequest->user_id,
            'Delete Request Approved',
            "Your delete request for $modelName has been approved by ".auth()->user()->name.' Note: '.$request->notes,
            'success',
            'delete_request',
            $deleteRequest->id,
            'delete_requests'
        );

        if (isset($result) && $result['status'] == 'error') {
            return to_route('delete_request.index')->with('error', $result['msg']);
        } else {
            return to_route('delete_request.index')->with('success', 'Delete Request Approved');
        }
    }

    public function reject(Request $request, $id)
    {
        $deleteRequest = delete_requests::find($id);

        if ($deleteRequest->status != 'pending') {
            return redirect()->back()->with('error', 'Request already '.$deleteRequest->status);
        }

        $deleteRequest->status = 'rejected';
        $deleteRequest->save();

        // Send notification to the user who requested the deletion
        $modelName = ucfirst(str_replace('_', ' ', $deleteRequest->model));
        $notesText = $request->has('notes') ? ' Note: '.$request->notes : '';
        createUserNotification(
            $deleteRequest->user_id,
            'Delete Request Rejected',
            "Your delete request for $modelName has been rejected by ".auth()->user()->name.$notesText,
            'error',
            'delete_request',
            $deleteRequest->id,
            'delete_requests'
        );

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
        $checkExpense = expenses::where('refID', $ref)->first();
        if ($checkExpense) {
            return [
                'msg' => 'You can not delete this sale because it has an expense.',
                'status' => 'error',
            ];
        }

        $checkTransaction = users_transactions::where('refID', $ref)->first();
        if ($checkTransaction) {
            return [
                'msg' => 'You can not delete this sale because it has a transaction.',
                'status' => 'error',
            ];
        }
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

    public function deletePurchase($ref)
    {

        try {
            DB::beginTransaction();
            $purchase = purchase::where('refID', $ref)->first();

            foreach ($purchase->details as $product) {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();
            purchase_order_delivery::where('purchaseID', $purchase->id)->delete();
            $purchase->delete();

            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Purchase Deleted',
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

    public function deleteSalesReturns($ref)
    {
        try {
            DB::beginTransaction();
            $return = returns::where('refID', $ref)->first();

            transactions::where('refID', $return->refID)->delete();

            foreach ($return->details as $product) {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            sale_payments::where('refID', $return->refID)->delete();
            $return->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Sales Return Deleted',
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

    public function deletePurchaseOrder($ref)
    {
        try {
            $order = purchase_order::where('refID', $ref)->first();
            $order->details()->delete();
            $order->delete();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Purchase Order Deleted',
                'status' => 'success',
            ];
        } catch (\Exception $e) {
            session()->forget('confirmed_password');

            return [
                'msg' => $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    public function deleteStockTransfer($ref)
    {
        try {
            DB::beginTransaction();
            $transfer = StockTransfer::where('refID', $ref)->first();
            stock::where('refID', $ref)->delete();
            $transfer->details()->delete();
            $transfer->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Stock Transfer Deleted',
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

    public function deleteObsoleteStock($ref)
    {

        try {
            DB::beginTransaction();
            obsolete_stock::where('refID', $ref)->delete();
            stock::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Obsolete Stock Deleted',
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

    public function deleteAccountsAdjustment($ref)
    {
        try {
            DB::beginTransaction();
            accountsAdjustment::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Accounts Adjustment Deleted',
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

    public function deleteStaffPayment($ref)
    {
        try {
            DB::beginTransaction();
            staffPayments::where('refID', $ref)->delete();
            payments::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            expenses::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            transactions_que::where('trefID', $ref)->update(['status' => 'pending']);
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Staff Payment Deleted',
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

    public function deletePayment($ref)
    {
        try {
            DB::beginTransaction();
            payments::where('refID', $ref)->delete();
            staffPayments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            transactions_que::where('trefID', $ref)->update(['status' => 'pending']);
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Vendor Payment Deleted',
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

    public function deletePaymentReceiving($ref)
    {
        try {
            DB::beginTransaction();
            paymentsReceiving::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Payment Receiving Deleted',
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

    public function deleteTransfer($ref)
    {
        try {
            DB::beginTransaction();
            transfer::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Transfer Deleted',
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

    public function deleteStockAdjustment($ref)
    {
        try {
            DB::beginTransaction();
            stockAdjustment::where('refID', $ref)->delete();
            stock::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Stock Adjustment Deleted',
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

    public function deleteIssueSalary($ref)
    {
        try {
            DB::beginTransaction();
            issue_salary::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Issue Salary Deleted',
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

    public function deleteCustomerAdvance($ref)
    {
        try {
            DB::beginTransaction();
            customerAdvanceConsumption::where('refID', $ref)->delete();
            CustomerAdvancePayment::where('refID', $ref)->delete();
            sale_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Customer Advance Deleted',
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

    public function deleteFixedAsset($ref)
    {
        try {
            DB::beginTransaction();
            $fixed = fixed_assets::where('refID', $ref)->first();
            if ($fixed->status() == 'Sold') {
                $sale = fixed_assets_sales::where('fixedAssetID', $fixed->id)->first();
                users_transactions::where('refID', $sale->refID)->delete();
                currency_transactions::where('refID', $sale->refID)->delete();
                method_transactions::where('refID', $sale->refID)->delete();
                cheques::where('refID', $sale->refID)->delete();
                staffPayments::where('refID', $sale->refID)->delete();
                $sale->delete();
            }
            $fixed->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            staffPayments::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Fixed Asset Deleted',
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

    public function deleteIssueMisc($ref)
    {
        try {
            DB::beginTransaction();
            issue_misc::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Issue Misc Deleted',
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

    public function deleteIssueAdvance($ref)
    {
        try {
            DB::beginTransaction();
            issue_advance::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Issue Advance Deleted',
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

    public function deleteGenerateSalary($ref)
    {
        try {
            DB::beginTransaction();
            $salary = generate_salary::where('refID', $ref)->first();
            if (! $salary) {
                return [
                    'msg' => 'Salary not found',
                    'status' => 'error',
                ];
            }
            employee_ledger::where('refID', $ref)->delete();
            $salary->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Generate Salary Deleted',
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

    public function deleteEmployeeLedgerAdjustment($ref)
    {
        try {
            DB::beginTransaction();
            employee_ledger_adjustment::where('refID', $ref)->delete();
            employee_ledger::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Employee Ledger Adjustment Deleted',
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

    public function deleteStaffAmountAdjustment($ref)
    {
        try {
            DB::beginTransaction();
            staffAmountAdjustment::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Staff Amount Adjustment Deleted',
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

    public function deleteBulkPayment($ref)
    {
        try {
            DB::beginTransaction();
            bulk_payments::where('refID', $ref)->delete();
            sale_payments::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            currency_transactions::where('refID', $ref)->delete();
            users_transactions::where('refID', $ref)->delete();
            method_transactions::where('refID', $ref)->delete();
            transactions_que::where('refID', $ref)->delete();
            cheques::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Bulk Payment Deleted',
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

    public function deleteCustomerAdvanceConsumption($ref)
    {
        try {
            DB::beginTransaction();
            customerAdvanceConsumption::where('refID', $ref)->delete();
            transactions::where('refID', $ref)->delete();
            sale_payments::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');

            return [
                'msg' => 'Customer Advance Consumption Deleted',
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
