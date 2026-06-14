@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                            <a href="javascript:window.print()" class="btn btn-success ml-4"><i
                                    class="ri-printer-line mr-4"></i> Print</a>
                        </div>
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h1>{{ Auth()->user()->branch->name }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Profit / Loss Report</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">From</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($from)) }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">To</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($to)) }}</h5>
                                </div>

                                <!--end col-->
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
                                    {{-- <h5 class="fs-14 mb-0"><span id="total-amount">{{ \Carbon\Carbon::now()->format('h:i A') }}</span></h5> --}}
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </div>
                        <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th scope="col" rowspan="2" class="p-1" style="width: 50px;">#</th>
                                            <th scope="col" rowspan="2" class="p-1">Product</th>
                                            <th scope="col" rowspan="2" class="p-1">Unit</th>
                                            <th scope="col" rowspan="2" class="p-1">Pack Size</th>
                                            <th scope="col" colspan="6" class="p-1">Purchase</th>
                                            <th scope="col" colspan="6" class="p-1">Sales</th>
                                            <th scope="col" rowspan="2" class="text-end p-1">Sold Qty</th>
                                            <th scope="col" rowspan="2" class="text-end p-1">Return Qty</th>
                                            <th scope="col" rowspan="2" class="text-end p-1">Net Sale Qty</th>
                                            <th scope="col" rowspan="2" class="text-end p-1">Profit / Unit</th>
                                            <th scope="col" rowspan="2" class="text-end p-1">Total Profit</th>
                                        </tr>
                                        <tr class="table-active">
                                            <!-- Purchase Sub -->
                                            <th scope="col" class="text-end p-1">Price / Unit</th>
                                            <th scope="col" class="text-end p-1">Discount</th>
                                            <th scope="col" class="text-end p-1">Freight</th>
                                            <th scope="col" class="text-end p-1">Labour</th>
                                            <th scope="col" class="text-end p-1">Claim</th>
                                            <th scope="col" class="text-end p-1">Net Price (Avg)</th>
                                            <!-- Sales Sub -->
                                            <th scope="col" class="text-end p-1">Price / Unit</th>
                                            <th scope="col" class="text-end p-1">Discount</th>
                                            <th scope="col" class="text-end p-1">Freight</th>
                                            <th scope="col" class="text-end p-1">Labour</th>
                                            <th scope="col" class="text-end p-1">Claim</th>
                                            <th scope="col" class="text-end p-1">Net Price (Avg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_profit_sum = 0;
                                            $groupedData = collect($data)->groupBy('vendor');
                                        @endphp
                                        @foreach ($groupedData as $vendor => $items)
                                            <tr>
                                                <td colspan="21" class="text-start fw-bold bg-light fs-5">{{ $vendor }}</td>
                                            </tr>
                                            @foreach ($items as $index => $item)
                                                @php
                                                    $total_profit_sum += $item['total_profit'];
                                                    $uniqueKey = \Illuminate\Support\Str::slug($vendor) . '-' . $index;
                                                @endphp
                                                <tr data-bs-toggle="collapse" data-bs-target="#collapse-{{ $uniqueKey }}"
                                                    style="cursor: pointer;" title="Click to view sales details">
                                                    <td class="p-1">{{ $index + 1 }}</td>
                                                    <td class="text-start p-1">{{ $item['name'] }}</td>
                                                <td class="text-start p-1">{{ $item['unit'] }}</td>
                                                <td class="text-start p-1">{{ $item['pack_size'] }}</td>
                                                <!-- Purchase -->
                                                <td class="text-end p-1">{{ number_format($item['purchase']['price'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($item['purchase']['discount'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($item['purchase']['freight'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($item['purchase']['labor'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($item['purchase']['claim'], 2) }}
                                                </td>
                                                <td class="text-end p-1 fw-bold">
                                                    {{ number_format($item['purchase']['net'], 2) }}</td>
                                                <!-- Sales -->
                                                <td class="text-end p-1">{{ number_format($item['sales']['price'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($item['sales']['discount'], 2) }}
                                                </td>
                                                <td class="text-end p-1">{{ number_format($item['sales']['freight'], 2) }}
                                                </td>
                                                <td class="text-end p-1">{{ number_format($item['sales']['labor'], 2) }}
                                                </td>
                                                <td class="text-end p-1">{{ number_format($item['sales']['claim'], 2) }}
                                                </td>
                                                <td class="text-end fw-bold p-1">
                                                    {{ number_format($item['sales']['net'], 2) }}
                                                </td>

                                                <td class="text-end p-1">{{ number_format($item['sold_qty'], 2) }}</td>
                                                <td class="text-end p-1">{{ number_format($item['return_qty'], 2) }}</td>
                                                <td class="text-end fw-bold p-1">
                                                    {{ number_format($item['net_sale_qty'], 2) }}
                                                </td>
                                                <td class="text-end p-1">{{ number_format($item['profit_per_unit'], 2) }}
                                                </td>
                                                <td class="text-end fw-bold p-1">
                                                    {{ number_format($item['total_profit'], 2) }}
                                                </td>
                                            </tr>
                                            <!-- Accordion content -->
                                            <tr class="collapse p-0" id="collapse-{{ $uniqueKey }}">
                                                <td colspan="21" class="p-0">
                                                    <div class="card card-body m-0 bg-light">
                                                        <h5 class="mb-2">Sales Details for {{ $item['name'] }}</h5>
                                                        @if ($item['sales']['details']->count() > 0)
                                                            <table class="table table-sm table-bordered">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        {{--  <th>Date</th>
                                                                        <th>Customer</th>
                                                                        <th>Order Booker</th> --}}
                                                                        <th>Qty</th>
                                                                        {{--  <th>Price</th>
                                                                        <th>Discount</th>
                                                                        <th>Freight</th>
                                                                        <th>Labour</th>
                                                                        <th>Claim</th> --}}
                                                                        <th>Net Price</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($item['sales']['details'] as $sale_detail)
                                                                        <tr>
                                                                            {{--  <td>{{ date('d M Y', strtotime($sale_detail->date)) }}
                                                                            </td>
                                                                            <td>{{ $sale_detail->sale->customer->title ?? 'N/A' }}
                                                                            </td>
                                                                            <td>{{ $sale_detail->sale->orderbooker->name ?? 'N/A' }}
                                                                            </td> --}}
                                                                            <td>{{ number_format($sale_detail->qty, 2) }}
                                                                            </td>
                                                                            {{--  <td>{{ number_format($sale_detail->price, 2) }}
                                                                            </td>
                                                                            <td>{{ number_format($sale_detail->discountvalue, 2) }}
                                                                            </td>
                                                                            <td>{{ number_format($sale_detail->fright, 2) }}
                                                                            </td>
                                                                            <td>{{ number_format($sale_detail->labor, 2) }}
                                                                            </td>
                                                                            <td>{{ number_format($sale_detail->claim, 2) }}
                                                                            </td> --}}
                                                                            <td>{{ number_format($sale_detail->price + $sale_detail->fright + $sale_detail->labor - ($sale_detail->discountvalue + $sale_detail->discount + $sale_detail->claim), 2) }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @else
                                                            <p class="text-muted">No sales found for this product in the
                                                                selected criteria.</p>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        @endforeach

                                        <!-- Totals -->
                                        <tr class="table-active">
                                            <th colspan="20" class="text-end p-1">Gross Profit</th>
                                            <th class="text-end fs-5 p-1">{{ number_format($total_profit_sum, 2) }}</th>
                                        </tr>

                                        <!-- Expenses Section -->
                                        <tr data-bs-toggle="collapse" data-bs-target="#collapse-expenses"
                                            style="cursor: pointer;" title="Click to view category wise expenses">
                                            <td colspan="20" class="text-end p-1">Expenses</td>
                                            <td class="text-end text-danger p-1">-{{ number_format($total_expenses, 2) }}
                                            </td>
                                        </tr>
                                        <!-- Expense Accordion content -->
                                        <tr class="collapse p-0" id="collapse-expenses">
                                            <td colspan="21" class="p-0">
                                                <div class="card card-body m-0 bg-light">
                                                    <h5 class="mb-2">Category Wise Expenses</h5>
                                                    @if (count($expenses_data) > 0)
                                                        <table class="table table-sm table-bordered text-start mb-0">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>Category Name</th>
                                                                    <th style="width: 20%" class="text-end">Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($expenses_data as $cat_name => $exp_data)
                                                                    <tr>
                                                                        <td class="p-1">{{ $cat_name }}</td>
                                                                        <td class="text-end text-danger p-1">
                                                                            {{ number_format($exp_data['sum'], 2) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <p class="text-muted">No expenses found.</p>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="20" class="text-end p-1">Employees Salaries</td>
                                            <td class="text-end text-danger p-1">-{{ number_format($salaries, 2) }}</td>
                                        </tr>
                                        <tr class="table-active">
                                            <th colspan="20" class="text-end text-danger p-1">Total Deductions</th>
                                            <th class="text-end text-danger p-1">
                                                -{{ number_format($total_expenses + $salaries, 2) }}</th>
                                        </tr>

                                        <!-- Net Profit -->
                                        <tr class="table-success">
                                            <th colspan="20" class="text-end text-success p-1">Net Profit</th>
                                            <th class="text-end text-success p-1">
                                                {{ number_format($total_profit_sum - ($total_expenses + $salaries), 2) }}
                                            </th>
                                        </tr>
                                    </tbody>
                                </table><!--end table-->
                            </div>

                        </div>
                        <!--end card-body-->
                    </div><!--end col-->
                </div><!--end row-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
