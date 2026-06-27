<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\area;
use App\Models\currencymgmt;
use App\Models\discountManagement;
use App\Models\expense_categories;
use App\Models\expenses;
use App\Models\orderbooker_customers;
use App\Models\orderbooker_products;
use App\Models\product_dc;
use App\Models\product_units;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\User;
use App\Models\users_transactions;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $start = $request->start ?? date('Y-m-d');
        $end = $request->end ?? date('Y-m-d');

        $bookerID = $request->orderbookerID ?? null;

        if ($bookerID == null) {
            $sales = sales::with('payments')->whereBetween('date', [$start, $end])->where('branchID', auth()->user()->branchID)->orderby('id', 'desc')->get();
        } else {
            $sales = sales::with('payments')->whereBetween('date', [$start, $end])->where('branchID', auth()->user()->branchID)->where('orderbookerID', $bookerID)->orderby('id', 'desc')->get();
        }

        $warehouses = warehouses::currentBranch()->get();
        $customers = accounts::customer()->currentBranch()->get();

        $orderbookers = User::orderbookers()->currentBranch()->active()->get();
        $supplymen = accounts::supplyMen()->currentBranch()->get();

        $currencies = currencymgmt::all();
        foreach ($currencies as $currency) {
            $currency->qty = getCurrencyBalance($currency->id, auth()->user()->id);
        }
        $categories = expense_categories::currentBranch()->get();

        return view('sales.index', compact('sales', 'start', 'end', 'warehouses', 'customers', 'orderbookers', 'bookerID', 'supplymen', 'currencies', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request)
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $request->orderbookerID)->pluck('productID')->toArray();
        $products = products::whereIn('id', $orderbooker_products)->orderby('name', 'asc')->get();
        $customer = accounts::find($request->customerID);
        foreach ($products as $product) {
            $stock = getStock($product->id);
            $product->stock = $stock;

        }
        $units = units::currentBranch()->get();
        $orderbooker = User::find($request->orderbookerID);
        $warehouse = warehouses::find($request->warehouseID);
        $supplymen = accounts::supplyMen()->currentBranch()->get();
        $date = $request->date ?? date('Y-m-d');

        return view('sales.create', compact('products', 'units', 'customer', 'orderbooker', 'warehouse', 'supplymen', 'date'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($request->isNotFilled('id')) {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            $ref = getRef();
            $sale = sales::create(
                [
                    'customerID' => $request->customerID,
                    'branchID' => Auth()->user()->branchID,
                    'warehouseID' => $request->warehouseID,
                    'orderbookerID' => $request->orderbookerID,
                    'supplymanID' => $request->supplymanID,
                    'orderdate' => $request->orderdate,
                    'date' => $request->date,
                    'bilty' => $request->bilty,
                    'transporter' => $request->transporter,
                    'notes' => $request->notes,
                    'refID' => $ref,
                    'branch_admin_viewed' => false,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $customer = accounts::find($request->customerID)->title;
            foreach ($ids as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc = $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $frieght = $request->fright[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim) + $frieght;
                $amount = $netPrice * $pc;
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                sale_details::create(
                    [
                        'saleID' => $sale->id,
                        'warehouseID' => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
                        'branchID' => Auth()->user()->branchID,
                        'productID' => $id,
                        'price' => $price,
                        'discount' => $discount,
                        'discountp' => $request->discountp[$key],
                        'discountvalue' => $discountvalue,
                        'qty' => $request->qty[$key],
                        'pc' => $pc,
                        'loose' => $request->loose[$key],
                        'netprice' => $netPrice,
                        'amount' => $amount,
                        'price_amount' => $price_amount,
                        'date' => $request->date,
                        'bonus' => $request->bonus[$key],
                        'labor' => $request->labor[$key],
                        'fright' => $request->fright[$key],
                        'claim' => $claim,
                        'unitID' => $unit->id,
                        'refID' => $ref,
                    ]
                );
                createStock($id, 0, $qty, $request->date, "Sold to $customer", $ref, $request->warehouseID);
            }

            $net = round($total, 0);
            $totalLabor = round($totalLabor, 0);

            $sale->update(
                [
                    'net' => $net,
                ]
            );
            if ($totalLabor > 0) {
                createTransaction($request->supplymanID, $request->date, 0, $totalLabor, "Labor Charges of Sale No. $sale->id Customer: $customer Notes: $request->notes", $ref, $request->orderbookerID);
            }

            if ($request->payment == 'Advance') {
                $account_balance = getAccountBalance($request->customerID);
                if ($account_balance >= 0) {

                    createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id Notes: $request->notes", $ref, $request->orderbookerID);
                    DB::commit();

                    return back()->with('success', 'Sale Created But Invoice is not marked as Paid as Customer Balance is not enough');
                } else {
                    $balance = abs($account_balance);
                    $difference = $balance - $net;
                    if ($difference >= 0) {
                        sale_payments::create(
                            [
                                'salesID' => $sale->id,
                                'orderbookerID' => $sale->orderbookerID,
                                'branchID' => auth()->user()->branchID,
                                'customerID' => $sale->customerID,
                                'method' => 'Cash',
                                'number' => 'Advance',
                                'bank' => 'Advance',
                                'cheque_date' => $request->date,
                                'date' => $request->date,
                                'amount' => $net,
                                'notes' => "Advance Payment of Sale No. $sale->id",
                                'userID' => auth()->id(),
                                'refID' => $ref,
                            ]
                        );
                        createTransaction($request->customerID, $request->date, $net, 0, "Sale No. $sale->id Adjusted as Already Paid Notes: $request->notes", $ref, $request->orderbookerID);

                        DB::commit();

                        return back()->with('success', 'Sale Created and marked as paid');
                    } else {
                        $diff = abs($difference);
                        sale_payments::create(
                            [
                                'salesID' => $sale->id,
                                'orderbookerID' => $sale->orderbookerID,
                                'branchID' => auth()->user()->branchID,
                                'customerID' => $sale->customerID,
                                'method' => 'Cash',
                                'number' => 'Advance',
                                'bank' => 'Advance',
                                'cheque_date' => $request->date,
                                'date' => $request->date,
                                'amount' => $diff,
                                'notes' => "Advance Payment of Sale No. $sale->id",
                                'userID' => auth()->id(),
                                'refID' => $ref,
                            ]
                        );
                        createTransaction($request->customerID, $request->date, $diff, 0, "Partial Payment of Sale No. $sale->id as already paid Notes: $request->notes", $ref, $request->orderbookerID);
                        DB::commit();

                        return back()->with('success', "Sale Created and Rs. $diff is marked as paid");
                    }
                }
            } else {
                createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id Notes: $request->notes", $ref, $request->orderbookerID);
            }
            DB::commit();

            return back()->with('success', 'Sale Created');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(sales $sale)
    {
        $balance = spotBalance($sale->customerID, $sale->refID);

        return view('sales.view', compact('sale', 'balance'));
    }

    public function showUrdu($id)
    {
        $sale = sales::findOrFail($id);
        $balance = spotBalance($sale->customerID, $sale->refID);

        return view('sales.urdu', compact('sale', 'balance'));
    }

    public function gatePass($id)
    {
        $sale = sales::find($id);

        return view('sales.gatepass', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sales $sale)
    {
        $orderbooker_products = orderbooker_products::where('orderbookerID', $sale->orderbookerID)->pluck('productID')->toArray();
        $products = products::whereIn('id', $orderbooker_products)->orderby('name', 'asc')->get();
        $customer = accounts::find($sale->customerID);
        foreach ($products as $product) {
            $stock = getStock($product->id);
            $product->stock = $stock;
        }

        foreach ($sale->details as $pro) {
            $pro->stock = round((getStock($pro->productID) + $pro->pc + $pro->bonus + $pro->loose) / $pro->unit->value);
            $pro->stock1 = round((getStock($pro->productID) + $pro->pc + $pro->bonus + $pro->loose));

        }
        $units = units::currentBranch()->get();

        $orderbooker = User::find($sale->orderbookerID);
        $warehouse = warehouses::find($sale->warehouseID);
        $supplymen = accounts::supplyMen()->currentBranch()->get();

        return view('sales.edit', compact('products', 'units', 'customer', 'sale', 'orderbooker', 'warehouse', 'supplymen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $sale = sales::find($id);
            /* foreach($sale->payments as $payment)
            {
                transactions::where('refID', $payment->refID)->delete();
                $payment->delete();
            } */
            foreach ($sale->details as $product) {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }

            transactions::where(['accountID' => $sale->customerID, 'refID' => $sale->refID])->delete();
            transactions::where(['accountID' => $sale->supplymanID, 'refID' => $sale->refID])->delete();
            $ref = $sale->refID;

            if ($request->isNotFilled('id')) {
                throw new Exception('Please Select Atleast One Product');
            }

            $sale->update(
                [
                    'orderbookerID' => $request->orderbookerID,
                    'supplymanID' => $request->supplymanID,
                    'orderdate' => $request->orderdate,
                    'date' => $request->date,
                    'bilty' => $request->bilty,
                    'transporter' => $request->transporter,
                    'notes' => $request->notes,
                ]
            );

            $ids = $request->id;

            $total = 0;
            $totalLabor = 0;
            $customer = accounts::find($request->customerID)->title;
            foreach ($ids as $key => $id) {
                $unit = product_units::find($request->unit[$key]);
                $qty = ($request->qty[$key] * $unit->value) + $request->bonus[$key] + $request->loose[$key];
                $pc = $request->loose[$key] + ($request->qty[$key] * $unit->value);
                $price = $request->price[$key];
                $discount = $request->discount[$key];
                $claim = $request->claim[$key];
                $frieght = $request->fright[$key];
                $discountvalue = $request->price[$key] * $request->discountp[$key] / 100;
                $netPrice = ($price - $discount - $discountvalue - $claim) + $frieght;
                $amount = $netPrice * $pc;
                $price_amount = $price * $pc;
                $total += $amount;
                $totalLabor += $request->labor[$key] * $pc;

                sale_details::create(
                    [
                        'saleID' => $sale->id,
                        'warehouseID' => $request->warehouseID,
                        'orderbookerID' => $request->orderbookerID,
                        'branchID' => Auth()->user()->branchID,
                        'productID' => $id,
                        'price' => $price,
                        'discount' => $discount,
                        'discountp' => $request->discountp[$key],
                        'discountvalue' => $discountvalue,
                        'qty' => $request->qty[$key],
                        'pc' => $pc,
                        'loose' => $request->loose[$key],
                        'netprice' => $netPrice,
                        'amount' => $amount,
                        'price_amount' => $price_amount,
                        'date' => $request->date,
                        'bonus' => $request->bonus[$key],
                        'labor' => $request->labor[$key],
                        'fright' => $request->fright[$key],
                        'claim' => $claim,
                        'unitID' => $unit->id,
                        'refID' => $ref,
                    ]
                );
                createStock($id, 0, $qty, $request->date, "Sold to $customer", $ref, $request->warehouseID);
            }

            $net = round($total, 0);
            $totalLabor = round($totalLabor, 0);

            $sale->update(
                [
                    'net' => $net,
                ]
            );

            createTransaction($request->customerID, $request->date, $net, 0, "Pending Amount of Sale No. $sale->id Notes: $request->notes", $ref, $sale->orderbookerID);

            createTransaction($request->supplymanID, $request->date, 0, $totalLabor, "Labor Charges of Sale No. $sale->id Customer: $customer Notes: $request->notes", $ref, $sale->orderbookerID);

            DB::commit();

            return back()->with('success', 'Sale Updated');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = sales::find($id);
        $checkExpense = expenses::where('refID', $sale->refID)->first();
        if ($checkExpense) {
            return back()->with('error', 'You can not delete this sale because it has an expense.');
        }

        $checkTransaction = users_transactions::where('refID', $sale->refID)->first();
        if ($checkTransaction) {
            return back()->with('error', 'You can not delete this sale because it has a transaction.');
        }

        $checkPayments = sale_payments::where('saleID', $sale->id)->get();
        if ($checkPayments->count() > 0) {
            return back()->with('error', 'You can not delete this sale because it has payments.');
        }

        $customer = accounts::find($sale->customerID)->title;
        $area = $sale->customer->area->name;
        $address = $sale->customer->address;
        $orderbooker = User::find($sale->orderbookerID)->name;
        $supplyman = accounts::find($sale->supplymanID)->title;
        $notes = "Invoice Date: $sale->date | Invoice No.: $id | Invoice Amount: $sale->net | Customer : $customer | Area : $area | Address : $address | Orderbooker : $orderbooker | Supplyman : $supplyman Bilty No. : $sale->bilty | Transporter: $sale->transporter | Sale Notes: $sale->notes";
        $delete = storeDeleteRequest(auth()->user()->id, $sale->branchID, $sale->refID, 'sales', $notes);
        session()->forget('confirmed_password');
        if ($delete == 0) {
            return back()->with('error', 'This record is already requested for deletion.');
        }

        return to_route('sale.index')->with('success', 'Delete Request Sent to Branch Admin.');
    }

    public function getSignleProduct($id, $warehouse, $area, $customer, $date)
    {
        $product = products::with('units')->find($id);
        $stocks = stock::select(DB::raw('SUM(cr) - SUM(db) AS balance'))
            ->where('productID', $product->id)
            ->get();
        $product->stock = getWarehouseProductStock($id, $warehouse);
        $dc = product_dc::where('productID', $product->id)->where('areaID', $area)->first();
        $product->dc = $dc->dc ?? 0;

        $discount = discountManagement::where('customerID', $customer)->where('productID', $id)->active()->currentBranch()->first();
        if ($discount) {
            $status = updateDiscountStatus($discount->id, $date);

            if ($status == 'Active') {
                $product->discount = $discount->discount + $product->discount;
                $product->discountp = $discount->discountp + $product->discountp;
            }
        }
        $sales = sales::where('customerID', $customer)->orderby('id', 'desc')->take('10')->pluck('id')->toArray();

        // Get latest record to preserve expected fields in the view (price, fright, labor, claim, netprice)
        $latest = sale_details::whereIn('saleID', $sales)
            ->where('productID', $id)
            ->orderBy('id', 'desc')
            ->select('price', 'fright', 'labor', 'claim', 'netprice', 'discount', 'discountp')
            ->first();

        $product->last_price = $latest ?? ['price' => 0, 'discount' => 0, 'discountp' => 0, 'fright' => 0, 'labor' => 0, 'claim' => 0, 'netprice' => 0];

        return $product;
    }

    public function getProductByCode($code)
    {
        $product = products::where('code', $code)->first();
        if ($product) {
            return $product->id;
        }

        return 'Not Found';
    }

    public function orderbooker_customers($orderbookerID)
    {
        $customers = orderbooker_customers::where('orderbookerID', $orderbookerID)->get();

        $data = [];

        foreach ($customers as $customer) {
            $data[] = [
                'value' => $customer->customerID,
                'text' => $customer->customer->title.' - '.$customer->customer->area->name,
            ];
        }

        return response()->json($data);
    }

    public function minor_edit(Request $request)
    {

        $sale = sales::find($request->saleID);
        $oldSupplymanID = $sale->supplymanID;
        $supplymanID = $request->supplymanID;
        $bilty = $request->bilty;
        $transporter = $request->transporter;
        $ref = $sale->refID;
        try {

            transactions::where('refID', $ref)->where('accountID', $oldSupplymanID)->update(
                [
                    'accountID' => $supplymanID,
                ]
            );
            $sale->update(
                [
                    'supplymanID' => $supplymanID,
                    'bilty' => $bilty,
                    'transporter' => $transporter,
                ]
            );

            return back()->with('success', 'Sale Updated');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeExpense(Request $request)
    {

        $sale = sales::find($request->saleID);
        try {
            DB::beginTransaction();
            if (! checkMethodExceed($request->method, auth()->user()->id, $request->amount)) {
                throw new \Exception('Method Amount Exceed');
            }
            if (! checkUserAccountExceed(auth()->user()->id, $request->amount)) {
                throw new \Exception('User Account Amount Exceed');
            }
            if ($request->method == 'Cash') {
                if (! checkCurrencyExceed(auth()->user()->id, $request->currencyID, $request->qty)) {
                    throw new \Exception('Currency Qty Exceed');
                }
            }
            $ref = $sale->refID;
            $notes = 'Sale Expense Method '.$request->method.' Invoice Date : '.$sale->date.' Invoice Number : '.$sale->id.' Invoice Amount : '.$sale->amount.' Customer Name : '.$sale->customer->title.' Area : '.$sale->customer->area->name.' Address : '.$sale->customer->address.' Orderbooker : '.$sale->orderbooker->name.' Supplyman : '.$sale->supplyman->title.' Bilty Number : '.$sale->bilty.' Transport : '.$sale->transporter.' Notes : '.$request->notes;
            expenses::create(
                [
                    'userID' => auth()->user()->id,
                    'amount' => $request->amount,
                    'branchID' => auth()->user()->branchID,
                    'categoryID' => $request->category,
                    'date' => $request->date,
                    'method' => $request->method,
                    'number' => $request->number,
                    'bank' => $request->bank,
                    'cheque_date' => $request->cheque_date,
                    'notes' => $notes,
                    'is_for_sale' => 1,
                    'refID' => $ref,
                ]
            );

            $sale->update(
                [
                    'expense_amount' => $request->amount,
                    'has_expense' => 1,
                ]
            );

            createMethodTransaction(auth()->user()->id, $request->method, 0, $request->amount, $request->date, $request->number, $request->bank, $request->cheque_date, $notes, $ref);

            createUserTransaction(auth()->user()->id, $request->date, 0, $request->amount, $notes, $ref);

            if ($request->method == 'Cash') {
                createCurrencyTransaction(auth()->user()->id, $request->currencyID, $request->qty, 'db', $request->date, $notes, $ref);
            }

            if ($request->has('file')) {
                createAttachment($request->file('file'), $ref);
            }

            DB::commit();

            return back()->with('success', 'Expense Saved');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function unviewed(Request $request)
    {

        $bookerID = $request->orderbookerID ?? null;
        $supplymanID = $request->supplymanID ?? null;
        $areaID = $request->areaID ?? null;

        $sales = sales::with('customer', 'customer.area')->where('branch_admin_viewed', false)->where('branchID', auth()->user()->branchID)->orderby('id', 'desc')->get();

        if ($bookerID) {
            $sales = $sales->where('orderbookerID', $bookerID);
        }
        if ($supplymanID) {
            $sales = $sales->where('supplymanID', $supplymanID);
        }
        if ($areaID) {
            $sales = $sales->where('customer.areaID', $areaID);
        }

        $orderbookers = User::orderbookers()->currentBranch()->active()->get();

        $supplymans = accounts::supplyMen()->currentBranch()->get();
        $areas = area::currentBranch()->get();

        return view('sales.unviewed', compact('sales', 'orderbookers', 'bookerID', 'supplymans', 'areas', 'areaID', 'supplymanID'));
    }

    public function addRemark(Request $request)
    {
        $sale = sales::find($request->saleID);
        $sale->update(
            [
                'remarks' => $request->remarks,
            ]
        );

        return back()->with('success', 'Remark Added');
    }

    public function markasviewed(Request $request)
    {
        $sale = sales::find($request->id);
        $sale->update(
            [
                'branch_admin_viewed' => true,
            ]
        );

        return back()->with('success', 'Sale Marked as Viewed');
    }
}
