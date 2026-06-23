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
                                            <th scope="col" rowspan="2" class="p-1 align-middle" style="width: 50px;">
                                                #</th>
                                            <th scope="col" rowspan="2" class="p-1 align-middle">Product</th>
                                            <th scope="col" rowspan="2" class="p-1 align-middle">Unit</th>
                                            <th scope="col" rowspan="2" class="p-1 align-middle">Pack Size</th>
                                            <th scope="col" colspan="6" class="p-1 align-middle">Purchase</th>
                                            <th scope="col" colspan="6" class="p-1 align-middle">Sales</th>
                                            <th scope="col" rowspan="2" class="text-end p-1 align-middle">Sold Qty
                                            </th>
                                            <th scope="col" rowspan="2" class="text-end p-1 align-middle">Return Qty
                                            </th>
                                            <th scope="col" rowspan="2" class="text-end p-1 align-middle">Net Sale Qty
                                            </th>
                                            <th scope="col" rowspan="2" class="text-end p-1 align-middle">Profit /
                                                Unit</th>
                                            <th scope="col" rowspan="2" class="text-end p-1 align-middle">Total Profit
                                            </th>
                                        </tr>
                                        <tr class="table-active">
                                            <!-- Purchase Sub -->
                                            <th scope="col" class="text-end p-1 align-middle">Price / Pack</th>
                                            <th scope="col" class="text-end p-1 align-middle">Discount</th>
                                            <th scope="col" class="text-end p-1 align-middle">Freight</th>
                                            <th scope="col" class="text-end p-1 align-middle">Labour</th>
                                            <th scope="col" class="text-end p-1 align-middle">Claim</th>
                                            <th scope="col" class="text-end p-1 align-middle">Net Price (Avg)</th>
                                            <!-- Sales Sub -->
                                            <th scope="col" class="text-end p-1 align-middle">Price / Pack</th>
                                            <th scope="col" class="text-end p-1 align-middle">Discount</th>
                                            <th scope="col" class="text-end p-1 align-middle">Freight</th>
                                            <th scope="col" class="text-end p-1 align-middle">Labour</th>
                                            <th scope="col" class="text-end p-1 align-middle">Claim</th>
                                            <th scope="col" class="text-end p-1 align-middle">Net Price (Avg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total_profit_sum = 0;
                                            $groupedData = collect($data)->groupBy('vendor');
                                            $grand_totals = [
                                                'pur_price' => 0,
                                                'pur_discount' => 0,
                                                'pur_freight' => 0,
                                                'pur_labor' => 0,
                                                'pur_claim' => 0,
                                                'pur_net' => 0,
                                                'sal_price' => 0,
                                                'sal_discount' => 0,
                                                'sal_freight' => 0,
                                                'sal_labor' => 0,
                                                'sal_claim' => 0,
                                                'sal_net' => 0,
                                                'sold_qty' => 0,
                                                'return_qty' => 0,
                                                'net_sale_qty' => 0,
                                                'profit_per_unit' => 0,
                                                'total_profit' => 0,
                                            ];
                                        @endphp
                                        @foreach ($groupedData as $vendor => $items)
                                            @php
                                                $vendor_totals = [
                                                    'pur_price' => 0,
                                                    'pur_discount' => 0,
                                                    'pur_freight' => 0,
                                                    'pur_labor' => 0,
                                                    'pur_claim' => 0,
                                                    'pur_net' => 0,
                                                    'sal_price' => 0,
                                                    'sal_discount' => 0,
                                                    'sal_freight' => 0,
                                                    'sal_labor' => 0,
                                                    'sal_claim' => 0,
                                                    'sal_net' => 0,
                                                    'sold_qty' => 0,
                                                    'return_qty' => 0,
                                                    'net_sale_qty' => 0,
                                                    'profit_per_unit' => 0,
                                                    'total_profit' => 0,
                                                ];
                                            @endphp
                                            <tr>
                                                <td colspan="21" class="text-start fw-bold bg-light fs-5">
                                                    {{ $vendor }}</td>
                                            </tr>
                                            @foreach ($items as $index => $item)
                                                @php
                                                    $total_profit_sum += $item['total_profit'];
                                                    $uniqueKey = \Illuminate\Support\Str::slug($vendor) . '-' . $index;

                                                    $ps = $item['pack_size'] ?? 1;
                                                    $pur_price = $item['purchase']['price'];
                                                    $pur_discount = $item['purchase']['discount'];
                                                    $pur_freight = $item['purchase']['freight'];
                                                    $pur_labor = $item['purchase']['labor'];
                                                    $pur_claim = $item['purchase']['claim'];
                                                    $pur_net = $item['purchase']['net'];

                                                    $sal_price = $item['sales']['price'];
                                                    $sal_discount = $item['sales']['discount'];
                                                    $sal_freight = $item['sales']['freight'];
                                                    $sal_labor = $item['sales']['labor'];
                                                    $sal_claim = $item['sales']['claim'];
                                                    $sal_net = $item['sales']['net'];

                                                    $vendor_totals['pur_price'] += $pur_price;
                                                    $vendor_totals['pur_discount'] += $pur_discount;
                                                    $vendor_totals['pur_freight'] += $pur_freight;
                                                    $vendor_totals['pur_labor'] += $pur_labor;
                                                    $vendor_totals['pur_claim'] += $pur_claim;
                                                    $vendor_totals['pur_net'] += $pur_net;

                                                    $vendor_totals['sal_price'] += $sal_price;
                                                    $vendor_totals['sal_discount'] += $sal_discount;
                                                    $vendor_totals['sal_freight'] += $sal_freight;
                                                    $vendor_totals['sal_labor'] += $sal_labor;
                                                    $vendor_totals['sal_claim'] += $sal_claim;
                                                    $vendor_totals['sal_net'] += $sal_net;

                                                    $vendor_totals['sold_qty'] += $item['sold_qty'];
                                                    $vendor_totals['return_qty'] += $item['return_qty'];
                                                    $vendor_totals['net_sale_qty'] += $item['net_sale_qty'];
                                                    $vendor_totals['profit_per_unit'] += $item['profit_per_unit'];
                                                    $vendor_totals['total_profit'] += $item['total_profit'];

                                                    $grand_totals['pur_price'] += $pur_price;
                                                    $grand_totals['pur_discount'] += $pur_discount;
                                                    $grand_totals['pur_freight'] += $pur_freight;
                                                    $grand_totals['pur_labor'] += $pur_labor;
                                                    $grand_totals['pur_claim'] += $pur_claim;
                                                    $grand_totals['pur_net'] += $pur_net;

                                                    $grand_totals['sal_price'] += $sal_price;
                                                    $grand_totals['sal_discount'] += $sal_discount;
                                                    $grand_totals['sal_freight'] += $sal_freight;
                                                    $grand_totals['sal_labor'] += $sal_labor;
                                                    $grand_totals['sal_claim'] += $sal_claim;
                                                    $grand_totals['sal_net'] += $sal_net;

                                                    $grand_totals['sold_qty'] += $item['sold_qty'];
                                                    $grand_totals['return_qty'] += $item['return_qty'];
                                                    $grand_totals['net_sale_qty'] += $item['net_sale_qty'];
                                                    $grand_totals['profit_per_unit'] += $item['profit_per_unit'];
                                                    $grand_totals['total_profit'] += $item['total_profit'];
                                                @endphp
                                                <tr data-bs-toggle="collapse" data-bs-target="#collapse-{{ $uniqueKey }}"
                                                    style="cursor: pointer;" title="Click to view sales details">
                                                    <td class="p-1">{{ $index + 1 }}</td>
                                                    <td class="text-start p-1">{{ $item['name'] }}</td>
                                                    <td class="text-start p-1">{{ $item['unit'] }}</td>
                                                    <td class="text-start p-1">{{ $item['pack_size'] }}</td>
                                                    <!-- Purchase -->
                                                    <td class="text-end p-1">
                                                        {{ number_format($pur_price, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($pur_discount, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($pur_freight, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($pur_labor, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($pur_claim, 2) }}
                                                    </td>
                                                    <td class="text-end p-1 fw-bold">
                                                        {{ number_format($pur_net, 2) }}</td>
                                                    <!-- Sales -->
                                                    <td class="text-end p-1">
                                                        {{ number_format($sal_price, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($sal_discount, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($sal_freight, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($sal_labor, 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($sal_claim, 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold p-1">
                                                        {{ number_format($sal_net, 2) }}
                                                    </td>

                                                    <td class="text-end p-1">{{ number_format($item['sold_qty'], 2) }}
                                                    </td>
                                                    <td class="text-end p-1">{{ number_format($item['return_qty'], 2) }}
                                                    </td>
                                                    <td class="text-end fw-bold p-1">
                                                        {{ number_format($item['net_sale_qty'], 2) }}
                                                    </td>
                                                    <td class="text-end p-1">
                                                        {{ number_format($item['profit_per_unit'], 2) }}
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

                                                                            <th>Qty</th>
                                                                            <th>Price</th>
                                                                            <th>Discount</th>
                                                                            <th>Freight</th>
                                                                            <th>Labour</th>
                                                                            <th>Claim</th>
                                                                            <th>Net Price</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $total_qty = 0;
                                                                            $grouped_details = [];
                                                                            foreach (
                                                                                $item['sales']['details']
                                                                                as $detail
                                                                            ) {
                                                                                $key =
                                                                                    $detail->price .
                                                                                    '_' .
                                                                                    $detail->discountvalue .
                                                                                    '_' .
                                                                                    $detail->discount .
                                                                                    '_' .
                                                                                    $detail->fright .
                                                                                    '_' .
                                                                                    $detail->labor .
                                                                                    '_' .
                                                                                    $detail->claim;
                                                                                if (!isset($grouped_details[$key])) {
                                                                                    $grouped_details[
                                                                                        $key
                                                                                    ] = clone $detail;
                                                                                    $grouped_details[$key]->pc = 0;
                                                                                }
                                                                                $grouped_details[$key]->pc +=
                                                                                    $detail->pc;
                                                                            }
                                                                        @endphp

                                                                        @foreach ($grouped_details as $sale_detail)
                                                                            @php
                                                                                $qty = $sale_detail->pc / $ps;
                                                                                $total_qty += $qty;
                                                                                $discount =
                                                                                    ($sale_detail->discountvalue +
                                                                                        $sale_detail->discount) *
                                                                                    $ps;
                                                                                $price = $sale_detail->price * $ps;
                                                                                $fright = $sale_detail->fright * $ps;
                                                                                $labor = $sale_detail->labor * $ps;
                                                                                $claim = $sale_detail->claim * $ps;
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ number_format($qty, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($price, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($discount, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($fright, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($labor, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($claim, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($price - $discount + $fright + $labor - $claim, 2) }}
                                                                                </td>

                                                                            </tr>
                                                                        @endforeach
                                                                        <tr>
                                                                            <th>{{ number_format($total_qty, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['sales']['details']->avg('price') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format(($item['sales']['details']->avg('discountvalue') + $item['sales']['details']->avg('discount')) * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['sales']['details']->avg('fright') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['sales']['details']->avg('labor') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['sales']['details']->avg('claim') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format(($item['sales']['details']->avg('price') + $item['sales']['details']->avg('fright') + $item['sales']['details']->avg('labor') - ($item['sales']['details']->avg('discountvalue') + $item['sales']['details']->avg('discount') + $item['sales']['details']->avg('claim'))) * $ps, 2) }}
                                                                            </th>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                <p class="text-muted">No sales found for this product in
                                                                    the
                                                                    selected criteria.</p>
                                                            @endif

                                                            <hr>
                                                            <h5 class="mb-2 mt-4">Sale Returns Details for {{ $item['name'] }}</h5>
                                                            @if ($item['returns']['details']->count() > 0)
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-dark">
                                                                        <tr>

                                                                            <th>Qty</th>
                                                                            <th>Price</th>
                                                                            <th>Discount</th>
                                                                            <th>Freight</th>
                                                                            <th>Labour</th>
                                                                            <th>Claim</th>
                                                                            <th>Net Price</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $total_ret_qty = 0;
                                                                            $grouped_ret_details = [];
                                                                            foreach (
                                                                                $item['returns']['details']
                                                                                as $detail
                                                                            ) {
                                                                                $key =
                                                                                    $detail->price .
                                                                                    '_' .
                                                                                    $detail->discountvalue .
                                                                                    '_' .
                                                                                    $detail->discount .
                                                                                    '_' .
                                                                                    $detail->fright .
                                                                                    '_' .
                                                                                    $detail->labor .
                                                                                    '_' .
                                                                                    $detail->claim;
                                                                                if (!isset($grouped_ret_details[$key])) {
                                                                                    $grouped_ret_details[
                                                                                        $key
                                                                                    ] = clone $detail;
                                                                                    $grouped_ret_details[$key]->pc = 0;
                                                                                }
                                                                                $grouped_ret_details[$key]->pc +=
                                                                                    $detail->pc;
                                                                            }
                                                                        @endphp

                                                                        @foreach ($grouped_ret_details as $ret_detail)
                                                                            @php
                                                                                $qty = $ret_detail->pc / $ps;
                                                                                $total_ret_qty += $qty;
                                                                                $discount =
                                                                                    ($ret_detail->discountvalue +
                                                                                        $ret_detail->discount) *
                                                                                    $ps;
                                                                                $price = $ret_detail->price * $ps;
                                                                                $fright = $ret_detail->fright * $ps;
                                                                                $labor = $ret_detail->labor * $ps;
                                                                                $claim = $ret_detail->claim * $ps;
                                                                            @endphp
                                                                            <tr>
                                                                                <td>{{ number_format($qty, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($price, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($discount, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($fright, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($labor, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($claim, 2) }}
                                                                                </td>
                                                                                <td>{{ number_format($price - $discount + $fright + $labor - $claim, 2) }}
                                                                                </td>

                                                                            </tr>
                                                                        @endforeach
                                                                        <tr>
                                                                            <th>{{ number_format($total_ret_qty, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['returns']['details']->avg('price') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format(($item['returns']['details']->avg('discountvalue') + $item['returns']['details']->avg('discount')) * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['returns']['details']->avg('fright') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['returns']['details']->avg('labor') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format($item['returns']['details']->avg('claim') * $ps, 2) }}
                                                                            </th>
                                                                            <th>{{ number_format(($item['returns']['details']->avg('price') + $item['returns']['details']->avg('fright') + $item['returns']['details']->avg('labor') - ($item['returns']['details']->avg('discountvalue') + $item['returns']['details']->avg('discount') + $item['returns']['details']->avg('claim'))) * $ps, 2) }}
                                                                            </th>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                <p class="text-muted">No sale returns found for this product in
                                                                    the
                                                                    selected criteria.</p>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <!-- Vendor Totals -->
                                            <tr class="table-info fw-bold">
                                                <td colspan="4" class="text-end p-1">Vendor Total:</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['pur_price'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['pur_discount'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['pur_freight'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['pur_labor'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['pur_claim'], 2) }}</td>
                                                <td class="text-end p-1">{{ number_format($vendor_totals['pur_net'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sal_price'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sal_discount'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sal_freight'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sal_labor'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sal_claim'], 2) }}</td>
                                                <td class="text-end p-1">{{ number_format($vendor_totals['sal_net'], 2) }}
                                                </td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['sold_qty'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['return_qty'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['net_sale_qty'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['profit_per_unit'], 2) }}</td>
                                                <td class="text-end p-1">
                                                    {{ number_format($vendor_totals['total_profit'], 2) }}</td>
                                            </tr>
                                        @endforeach

                                        <!-- Grand Totals -->
                                        <tr class="table-warning fw-bold">
                                            <td colspan="4" class="text-end p-1">Grand Total:</td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_price'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_discount'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_freight'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_labor'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_claim'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['pur_net'], 2) }}</td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_price'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_discount'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_freight'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_labor'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_claim'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sal_net'], 2) }}</td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['sold_qty'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['return_qty'], 2) }}
                                            </td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['net_sale_qty'], 2) }}
                                            </td>
                                            <td class="text-end p-1">
                                                {{ number_format($grand_totals['profit_per_unit'], 2) }}</td>
                                            <td class="text-end p-1">{{ number_format($grand_totals['total_profit'], 2) }}
                                            </td>
                                        </tr>

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
