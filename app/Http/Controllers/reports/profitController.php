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
        $expense_categories = \App\Models\expense_categories::currentBranch()->get();

        return view('reports.profit.index', compact('branches', 'vendors', 'warehouses', 'towns', 'areas', 'products', 'customers', 'orderbookers', 'expense_categories'));
    }

    public function data(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        $branch = auth()->user()->branchID;
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
                    $purchase_discount = $last_purchase_data->discountvalue + $last_purchase_data->discount;
                    $purchase_freight = $last_purchase_data->fright;
                    $purchase_labor = $last_purchase_data->labor;
                    $purchase_claim = $last_purchase_data->claim;
                    $purchase_net = (($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim)) * $unit;
                } else {
                    $purchase_price = $prod->pprice;
                    $purchase_discount = 0;
                    $purchase_freight = $prod->fright;
                    $purchase_labor = $prod->labor;
                    $purchase_claim = $prod->claim;
                    $purchase_net = (($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim)) * $unit;
                }
            } else {
                $purchase_price = 0;
                $purchase_discount = 0;
                $purchase_discountP = 0;
                $purchase_freight = 0;
                $purchase_labor = 0;
                $purchase_claim = 0;
                foreach ($purchases_data as $pd) {
                    $purchase_price += $pd->price * $pd->pc;
                    $purchase_discount += $pd->discount * $pd->pc;
                    $purchase_discountP += $pd->discountvalue * $pd->pc;
                    $purchase_freight += $pd->fright * $pd->pc;
                    $purchase_labor += $pd->labor * $pd->pc;
                    $purchase_claim += $pd->claim * $pd->pc;
                }
                $total_pc = $purchases_data->sum('pc');
                $purchase_price = $purchase_price / $total_pc;
                $purchase_discount = ($purchase_discount / $total_pc) + ($purchase_discountP / $total_pc);
                $purchase_freight = $purchase_freight / $total_pc;
                $purchase_labor = $purchase_labor / $total_pc;
                $purchase_claim = $purchase_claim / $total_pc;
                $purchase_net = (($purchase_price + $purchase_freight + $purchase_labor) - ($purchase_discount + $purchase_claim)) * $unit;
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
                $sale_price = $prod->price;
                $sale_discount = 0;
                $sale_freight = $prod->sfright;
                $sale_labor = $prod->labor;
                $sale_claim = $prod->sclaim;
                $sale_net = ($sale_price - $sale_freight - $sale_labor - $sale_discount - $sale_claim) * $unit;
                $sold_qty = 0;
            } else {
                $sale_price = 0;
                $sale_discount = 0;
                $sale_discountP = 0;
                $sale_freight = 0;
                $sale_labor = 0;
                $sale_claim = 0;
                foreach ($sales_data_collection as $sd) {
                    $sale_price += $sd->price * $sd->pc;
                    $sale_discount += $sd->discount * $sd->pc;
                    $sale_discountP += $sd->discountvalue * $sd->pc;
                    $sale_freight += $sd->fright * $sd->pc;
                    $sale_labor += $sd->labor * $sd->pc;
                    $sale_claim += $sd->claim * $sd->pc;
                }
                $total_pc = $sales_data_collection->sum('pc');
                $sale_price = $sale_price / $total_pc;
                $sale_discount = ($sale_discount / $total_pc) + ($sale_discountP / $total_pc);
                $sale_freight = $sale_freight / $total_pc;
                $sale_labor = $sale_labor / $total_pc;
                $sale_claim = $sale_claim / $total_pc;
                $sale_net = ($sale_price - $sale_freight - $sale_labor - $sale_discount - $sale_claim) * $unit;
                $sold_qty = $total_pc / $unit;
            }

            $returns_query = returnsDetails::where('productID', $prod->id)
                ->whereHas('return', function ($q) use ($branch, $from, $to, $customer, $orderbooker, $area, $town, $warehouse) {
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
                    if ($warehouse) {
                        $q->whereIn('warehouseID', $warehouse);
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
            $returns_data_collection = $returns_query->get();
            $return_qty = $returns_data_collection->sum('pc') / $unit;
            $net_sale_qty = $sold_qty - $return_qty;

            $ppu = $sale_net - $purchase_net;
            $total_profit = $ppu * $net_sale_qty;

            if ($total_profit != 0 || $sold_qty > 0) {
                $data[] = [
                    'name' => $prod->name,
                    'vendor' => $prod->vendor->title ?? 'Unknown Vendor',
                    'unit' => $unit_name,
                    'pack_size' => $unit,
                    'purchase' => [
                        'price' => $purchase_price * $unit,
                        'discount' => $purchase_discount * $unit,
                        'freight' => $purchase_freight * $unit,
                        'labor' => $purchase_labor * $unit,
                        'claim' => $purchase_claim * $unit,
                        'net' => $purchase_net,
                    ],
                    'sales' => [
                        'price' => $sale_price * $unit,
                        'discount' => $sale_discount * $unit,
                        'freight' => $sale_freight * $unit,
                        'labor' => $sale_labor * $unit,
                        'claim' => $sale_claim * $unit,
                        'net' => $sale_net,
                        'details' => $sales_data_collection,
                    ],
                    'returns' => [
                        'details' => $returns_data_collection,
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

        $salaries_query = \App\Models\generate_salary::with('employee')->where('branchID', auth()->user()->branchID)
            ->whereBetween('date', [$from, $to]);

        $salaries = $salaries_query->sum('salary');
        $salaries_details = $salaries_query->get()->groupBy('employeeID');
        $salaries_data = [];

        foreach ($salaries_details as $empID => $items) {
            $emp_name = $items->first()->employee->name ?? 'Unknown Employee';
            $sum = $items->sum('salary');

            if (! isset($salaries_data[$emp_name])) {
                $salaries_data[$emp_name] = [
                    'sum' => 0,
                    'details' => collect(),
                ];
            }

            $salaries_data[$emp_name]['sum'] += $sum;
            $salaries_data[$emp_name]['details'] = $salaries_data[$emp_name]['details']->merge($items);
        }

        $issue_misc_categories = \App\Models\employees_payment_cats::all()->keyBy('id');
        $issue_misc_query = \App\Models\issue_misc::where('branchID', auth()->user()->branchID)
            ->whereBetween('date', [$from, $to]);
        $issue_miscs = $issue_misc_query->get()->groupBy('catID');
        $issue_misc = $issue_misc_query->sum('amount');
        $issue_misc_data = [];

        foreach ($issue_miscs as $catID => $items) {
            $cat_name = isset($issue_misc_categories[$catID]) ? $issue_misc_categories[$catID]->name : 'Other';
            $sum = $items->sum('amount');

            if (! isset($issue_misc_data[$cat_name])) {
                $issue_misc_data[$cat_name] = [
                    'sum' => 0,
                    'details' => collect(),
                ];
            }

            $issue_misc_data[$cat_name]['sum'] += $sum;
            $issue_misc_data[$cat_name]['details'] = $issue_misc_data[$cat_name]['details']->merge($items);
        }

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

        $vendors_list = $vendor ? accounts::whereIn('id', $vendor)->pluck('title')->toArray() : ['All'];
        $products_list = $product_ids ? products::whereIn('id', $product_ids)->pluck('name')->toArray() : ['All'];
        $warehouses_list = $warehouse ? \App\Models\warehouses::whereIn('id', $warehouse)->pluck('name')->toArray() : ['All'];
        $towns_list = $town ? \App\Models\town::whereIn('id', $town)->pluck('name')->toArray() : ['All'];
        $areas_list = $area ? \App\Models\area::whereIn('id', $area)->pluck('name')->toArray() : ['All'];
        $customers_list = $customer ? accounts::whereIn('id', $customer)->pluck('title')->toArray() : ['All'];
        $orderbookers_list = $orderbooker ? \App\Models\User::whereIn('id', $orderbooker)->pluck('name')->toArray() : ['All'];
        $expense_categories_list = $expense_category_filter ? \App\Models\expense_categories::whereIn('id', $expense_category_filter)->where('branchID', auth()->user()->branchID)->pluck('name')->toArray() : ['All'];

        $filters = [
            'Vendors' => implode(', ', $vendors_list),
            'Products' => implode(', ', $products_list),
            'Warehouses' => implode(', ', $warehouses_list),
            'Towns' => implode(', ', $towns_list),
            'Areas' => implode(', ', $areas_list),
            'Customers' => implode(', ', $customers_list),
            'Orderbookers' => implode(', ', $orderbookers_list),
            'Expense Categories' => implode(', ', $expense_categories_list),
        ];

        return view('reports.profit.details', compact('from', 'to', 'data', 'expenses_data', 'total_expenses', 'salaries', 'salaries_data', 'issue_misc', 'issue_misc_data', 'branch_name', 'filters'));
    }
}
