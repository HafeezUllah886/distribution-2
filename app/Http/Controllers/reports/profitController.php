<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\accounts;
use App\Models\branches;
use App\Models\expenses;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\returnsDetails;
use App\Models\sale_details;
use Illuminate\Http\Request;

class profitController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'Admin') {
            $branches = branches::all();
            $warehouses = \App\Models\warehouses::all();
            $towns = \App\Models\town::all();
            $areas = \App\Models\area::all();
            $products = \App\Models\products::all();
        } else {
            $branches = branches::where('id', auth()->user()->branchID)->get();
            $warehouses = \App\Models\warehouses::where('branchID', auth()->user()->branchID)->get();
            $towns = \App\Models\town::where('branchID', auth()->user()->branchID)->get();
            $areas = \App\Models\area::where('branchID', auth()->user()->branchID)->get();
            $products = \App\Models\products::all();
        }
        $vendors = accounts::vendor()->currentBranch()->get();
        $customers = accounts::customer()->currentBranch()->get();
        $orderbookers = \App\Models\User::orderbookers()->currentBranch()->get();
        $expense_categories = \App\Models\expense_categories::all();

        return view('reports.profit.index', compact('branches', 'vendors', 'warehouses', 'towns', 'areas', 'products', 'customers', 'orderbookers', 'expense_categories'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = $request->branch;
        $vendor = $request->vendor;
        $product_ids = $request->product;
        $warehouse = $request->warehouse;
        $town = $request->town;
        $area = $request->area;
        $customer = $request->customer;
        $orderbooker = $request->orderbooker;

        $products = products::query();
        if ($vendor) {
            $products->whereIn('vendorID', $vendor);
        }
        if ($product_ids) {
            $products->whereIn('id', $product_ids);
        }
        $products = $products->get();
        $data = [];

        foreach ($products as $prod) {
            $unit = $prod->units->first() ? $prod->units->first()->value : 1;
            $unit_name = $prod->units->first() ? $prod->units->first()->unit_name : 'N/A';

            // --- PURCHASES LOGIC ---
            $purchases = purchase_details::where('productID', $prod->id)
                ->whereHas('purchase', function ($q) use ($branch, $from, $to) {
                    if ($branch != 'All') {
                        $q->where('branchID', $branch);
                    }
                    $q->whereBetween('date', [$from, $to]);
                });
            if ($warehouse) {
                $purchases->whereIn('warehouseID', $warehouse);
            }
            $purchases_data = $purchases->get();

            if ($purchases_data->isEmpty()) {
                $last_purchase = purchase_details::where('productID', $prod->id)
                    ->whereHas('purchase', function ($q) use ($branch) {
                        if ($branch != 'All') {
                            $q->where('branchID', $branch);
                        }
                    });
                if ($warehouse) {
                    $last_purchase->whereIn('warehouseID', $warehouse);
                }
                $last_purchase_data = $last_purchase->latest('date')->first();

                if ($last_purchase_data) {
                    $purchase_price = $last_purchase_data->price;
                    $purchase_discount = $last_purchase_data->discountvalue;
                    $purchase_freight = $last_purchase_data->fright;
                    $purchase_labor = $last_purchase_data->labor;
                    $purchase_claim = $last_purchase_data->claim;
                    $purchase_net = $last_purchase_data->netprice;
                } else {
                    $purchase_price = $prod->pprice;
                    $purchase_discount = $prod->discount;
                    $purchase_freight = $prod->fright;
                    $purchase_labor = $prod->labor;
                    $purchase_claim = $prod->claim;
                    $purchase_net = ($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim);
                }
            } else {
                $purchase_price = $purchases_data->avg('price');
                $purchase_discount = $purchases_data->avg('discountvalue');
                $purchase_freight = $purchases_data->avg('fright');
                $purchase_labor = $purchases_data->avg('labor');
                $purchase_claim = $purchases_data->avg('claim');
                $purchase_net = $purchases_data->avg('netprice');
            }

            // --- SALES LOGIC ---
            $sales_query = sale_details::where('productID', $prod->id)
                ->whereHas('sale', function ($q) use ($branch, $from, $to, $customer, $orderbooker, $area, $town) {
                    if ($branch != 'All') {
                        $q->where('branchID', $branch);
                    }
                    $q->whereBetween('date', [$from, $to]);
                    if ($customer) {
                        $q->whereIn('customerID', $customer);
                    }
                    if ($orderbooker) {
                        $q->whereIn('orderbookerID', $orderbooker);
                    }
                    if ($area || $town) {
                        $q->whereHas('customer', function ($q2) use ($area, $town) {
                            if ($area) {
                                $q2->whereIn('areaID', $area);
                            }
                            if ($town) {
                                $q2->whereHas('area', function ($q3) use ($town) {
                                    $q3->whereIn('townID', $town);
                                });
                            }
                        });
                    }
                });
            if ($warehouse) {
                $sales_query->whereIn('warehouseID', $warehouse);
            }
            $sales_data_collection = $sales_query->get();

            if ($sales_data_collection->isEmpty()) {
                $sale_price = 0;
                $sale_discount = 0;
                $sale_freight = 0;
                $sale_labor = 0;
                $sale_claim = 0;
                $sale_net = 0;
                $sold_qty = 0;
            } else {
                $sale_price = $sales_data_collection->avg('price');
                $sale_discount = $sales_data_collection->avg('discountvalue');
                $sale_freight = $sales_data_collection->avg('fright');
                $sale_labor = $sales_data_collection->avg('labor');
                $sale_claim = $sales_data_collection->avg('claim');
                $sale_net = $sales_data_collection->avg('netprice');
                $sold_qty = $sales_data_collection->sum('qty');
            }

            $returns_query = returnsDetails::where('productID', $prod->id)
                ->whereHas('return', function ($q) use ($branch, $from, $to, $customer, $orderbooker, $area, $town) {
                    if ($branch != 'All') {
                        $q->where('branchID', $branch);
                    }
                    $q->whereBetween('date', [$from, $to]);
                    if ($customer) {
                        $q->whereIn('customerID', $customer);
                    }
                    if ($orderbooker) {
                        $q->whereIn('orderbookerID', $orderbooker);
                    }
                    if ($area || $town) {
                        $q->whereHas('customer', function ($q2) use ($area, $town) {
                            if ($area) {
                                $q2->whereIn('areaID', $area);
                            }
                            if ($town) {
                                $q2->whereHas('area', function ($q3) use ($town) {
                                    $q3->whereIn('townID', $town);
                                });
                            }
                        });
                    }
                });
            $return_qty = $returns_query->sum('qty');
            $net_sale_qty = $sold_qty - $return_qty;

            $ppu = $sale_net - $purchase_net;
            $total_profit = $ppu * $net_sale_qty;

            if ($total_profit != 0 || $sold_qty > 0) {
                $data[] = [
                    'name' => $prod->name,
                    'unit' => $unit_name,
                    'pack_size' => $unit,
                    'purchase' => [
                        'price' => $purchase_price,
                        'discount' => $purchase_discount,
                        'freight' => $purchase_freight,
                        'labor' => $purchase_labor,
                        'claim' => $purchase_claim,
                        'net' => $purchase_net,
                    ],
                    'sales' => [
                        'price' => $sale_price,
                        'discount' => $sale_discount,
                        'freight' => $sale_freight,
                        'labor' => $sale_labor,
                        'claim' => $sale_claim,
                        'net' => $sale_net,
                        'details' => $sales_data_collection,
                    ],
                    'sold_qty' => $sold_qty,
                    'return_qty' => $return_qty,
                    'net_sale_qty' => $net_sale_qty,
                    'profit_per_unit' => $ppu,
                    'total_profit' => $total_profit,
                ];
            }
        }

        $expense_category_filter = $request->expense_category; // array of IDs

        $expenses_query = expenses::whereBetween('date', [$from, $to])
            ->where('branchID', auth()->user()->branchID);

        if ($expense_category_filter) {
            $expenses_query->whereIn('categoryID', $expense_category_filter);
        }

        $expenses = $expenses_query->get()->groupBy('categoryID');

        $salaries = \App\Models\generate_salary::where('branchID', auth()->user()->branchID)
            ->whereBetween('month', [$from, $to])
            ->sum('salary');

        $branch_name = branches::find(auth()->user()->branchID)->name;

        $expense_categories = \App\Models\expense_categories::all()->keyBy('id');
        $expenses_data = [];
        $total_expenses = 0;

        foreach ($expenses as $catID => $items) {
            $cat_name = isset($expense_categories[$catID]) ? $expense_categories[$catID]->name : 'Other';
            $sum = $items->sum('amount');

            if (! isset($expenses_data[$cat_name])) {
                $expenses_data[$cat_name] = [
                    'sum' => 0,
                    'details' => collect(),
                ];
            }

            $expenses_data[$cat_name]['sum'] += $sum;
            $expenses_data[$cat_name]['details'] = $expenses_data[$cat_name]['details']->merge($items);

            $total_expenses += $sum;
        }

        return view('reports.profit.details', compact('from', 'to', 'data', 'expenses_data', 'total_expenses', 'salaries', 'branch_name'));
    }
}
