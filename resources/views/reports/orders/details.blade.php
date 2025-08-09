@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                                <a href="javascript:window.print()" class="btn btn-success ml-4"><i class="ri-printer-line mr-4"></i> Print</a>
                            </div>
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h1>{{projectNameHeader()}}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Orders Report</h3>
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
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Areas</p>
                                        <h5 class="fs-14 mb-0">{{ $area1 }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Orderbookers</p>
                                        <h5 class="fs-14 mb-0">{{ $orderbookers }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Customers</p>
                                        <h5 class="fs-14 mb-0">{{ $customers }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                        <h5 class="fs-14 mb-0">{{ $branch }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Status</p>
                                        <h5 class="fs-14 mb-0">{{ $status }}</h5>
                                    </div>
                                    <!--end col-->
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">{{ date("d M Y") }}</span></h5>
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
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0" style="border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000 !important;">
                                                <th scope="col" class="p-1" colspan="8">Ordered</th>
                                                <th scope="col" class="p-1" colspan="4">Delivered</th>
                                                <th scope="col" class="p-1" colspan="3">Pending</th>
                                            </tr>
                                            <tr class="table-active" style="border: 1px solid #000000 !important;">
                                                <th scope="col" class="p-1" style="width: 50px;">Order #</th>
                                                <th scope="col" class="text-start p-1">Product</th>
                                                <th scope="col" class="text-start p-1">Unit</th>
                                                <th scope="col" class="text-start p-1">Pack Size</th>
                                                <th scope="col" class="text-start p-1">Order Date</th>
                                                <th scope="col" class="text-start p-1">Qty</th>
                                                <th scope="col" class="text-start p-1">Loose</th>
                                                <th scope="col" class="p-1">Amount</th>
                                                <th scope="col" class="p-1">Date</th>
                                                <th scope="col" class="text-start p-1">Qty</th>
                                                <th scope="col" class="text-start p-1">Loose</th>
                                                <th scope="col" class="p-1">Amount</th>
                                                <th scope="col" class="text-start p-1">Qty</th>
                                                <th scope="col" class="text-start p-1">Loose</th>
                                                <th scope="col" class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $GrandOrderQty = 0;
                                                $GrandOrderLoose = 0;
                                                $GrandOrderAmount = 0;
                                                $GrandDeliveredQty = 0;
                                                $GrandDeliveredLoose = 0;
                                                $GrandDeliveredAmount = 0;
                                                $GrandPendingQty = 0;
                                                $GrandPendingLoose = 0;
                                                $GrandPendingAmount = 0;
                                            @endphp
                                            @foreach ($areas as $key => $area)
                                            @php
                                                $count = 0;
                                            @endphp
                                            @foreach ($area->customers as $customer)
                                            @php
                                               $count += $customer->orders->count();
                                            @endphp
                                            @endforeach

                                            @if($count > 0)
                                            <tr class="table-active" style="border: 1px solid #000000 !important;">
                                                <th scope="row" class="text-start p-1" style="border: 1px solid #000000 !important;" colspan="15">{{ $area->name}}</th>
                                            </tr>
                                            @php
                                                $areaTotalOrderQty = 0;
                                                $areaTotalOrderLoose = 0;
                                                $areaTotalOrderAmount = 0;
                                                $areaTotalDeliveredQty = 0;
                                                $areaTotalDeliveredLoose = 0;
                                                $areaTotalDeliveredAmount = 0;
                                                $areaTotalPendingQty = 0;
                                                $areaTotalPendingLoose = 0;
                                                $areaTotalPendingAmount = 0;
                                            @endphp
                                                @foreach ($area->customers as $customer)

                                                <tr class="table-active" style="border: 1px solid #000000 !important;">
                                                    <th scope="row" class="text-start p-1" style="border: 1px solid #000000 !important;" colspan="15">{{ $customer->title }}</th>
                                                </tr>
                                                @php
                                                    $totalOrderQty = 0;
                                                    $totalOrderLoose = 0;
                                                    $totalOrderAmount = 0;
                                                    $totalDeliveredQty = 0;
                                                    $totalDeliveredLoose = 0;
                                                    $totalDeliveredAmount = 0;
                                                    $totalPendingQty = 0;
                                                    $totalPendingLoose = 0;
                                                    $totalPendingAmount = 0;
                                                @endphp
                                                    @foreach ($customer->orders as $order)
                                                    @php

                                                    $orderQty = $order->qty;
                                                    $orderLoose = $order->loose;
                                                    $orderAmount = $order->amount;

                                                    $delivered = App\Models\order_delivery::where('orderID', $order->orderID)->where('productID', $order->productID)->get();

                                                    $deliveredQty = $delivered->sum('qty');
                                                    $deliveredLoose = $delivered->sum('loose');
                                                    $deliveredAmount = $order->deliveredAmount();

                                                    $pendingQty = $orderQty - $deliveredQty;
                                                    $pendingLoose = $orderLoose - $deliveredLoose;
                                                    $pendingAmount = $orderAmount - $deliveredAmount;

                                                    $totalOrderQty += $orderQty;
                                                    $totalOrderLoose += $orderLoose;
                                                    $totalOrderAmount += $orderAmount;
                                                    $totalDeliveredQty += $deliveredQty;
                                                    $totalDeliveredLoose += $deliveredLoose;
                                                    $totalDeliveredAmount += $deliveredAmount;
                                                    $totalPendingQty += $pendingQty;
                                                    $totalPendingLoose += $pendingLoose;
                                                    $totalPendingAmount += $pendingAmount;

                                                    $areaTotalOrderQty += $orderQty;
                                                    $areaTotalOrderLoose += $orderLoose;
                                                    $areaTotalOrderAmount += $orderAmount;
                                                    $areaTotalDeliveredQty += $deliveredQty;
                                                    $areaTotalDeliveredLoose += $deliveredLoose;
                                                    $areaTotalDeliveredAmount += $deliveredAmount;
                                                    $areaTotalPendingQty += $pendingQty;
                                                    $areaTotalPendingLoose += $pendingLoose;
                                                    $areaTotalPendingAmount += $pendingAmount;

                                                    $GrandOrderQty += $orderQty;
                                                    $GrandOrderLoose += $orderLoose;
                                                    $GrandOrderAmount += $orderAmount;
                                                    $GrandDeliveredQty += $deliveredQty;
                                                    $GrandDeliveredLoose += $deliveredLoose;
                                                    $GrandDeliveredAmount += $deliveredAmount;
                                                    $GrandPendingQty += $pendingQty;
                                                    $GrandPendingLoose += $pendingLoose;
                                                    $GrandPendingAmount += $pendingAmount;
                                                       
                                                    @endphp
                                                    <tr>
                                                        <td scope="row" class="text-start p-1">{{ $order->orderID }}</td>
                                                        <td class="text-start p-1">{{ $order->product->name }}</td>
                                                        <td class="text-start p-1">{{ $order->unit->unit_name }}</td>
                                                        <td class="text-start p-1">{{ $order->unit->value }}</td>
                                                        <td class="text-start p-1">{{ date('d M Y', strtotime($order->date)) }}</td>
                                                        <td class="text-start p-1">{{ number_format($orderQty) }}</td>
                                                        <td class="text-start p-1">{{ number_format($orderLoose) }}</td>
                                                        <td class="text-start p-1">{{ number_format($orderAmount) }}</td>
                                                        <td class="text-start p-1">{{ $order->lastDelivery() ? date('d M Y', strtotime($order->lastDelivery())) : "-" }}</td>
                                                        <td class="text-start p-1">{{ number_format($deliveredQty) }}</td>
                                                        <td class="text-start p-1">{{ number_format($deliveredLoose) }}</td>
                                                        <td class="text-start p-1">{{ number_format($deliveredAmount) }}</td>
                                                        <td class="text-start p-1">{{ number_format($pendingQty) }}</td>
                                                        <td class="text-start p-1">{{ number_format($pendingLoose) }}</td>
                                                        <td class="text-start p-1">{{ number_format($pendingAmount) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="table-active text-success">
                                                        <th colspan="5" class="text-end p-1">Total of {{ $customer->title }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalOrderQty) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalOrderLoose) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalOrderAmount) }}</th>
                                                        <th class="text-start p-1"></th>
                                                        <th class="text-start p-1">{{ number_format($totalDeliveredQty) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalDeliveredLoose) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalDeliveredAmount) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalPendingQty) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalPendingLoose) }}</th>
                                                        <th class="text-start p-1">{{ number_format($totalPendingAmount) }}</th>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-active text-primary">
                                                    <th colspan="5" class="text-end p-1">Total of {{ $area->name }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalOrderQty) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalOrderLoose) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalOrderAmount) }}</th>
                                                    <th class="text-start p-1"></th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalDeliveredQty) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalDeliveredLoose) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalDeliveredAmount) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalPendingQty) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalPendingLoose) }}</th>
                                                    <th class="text-start p-1">{{ number_format($areaTotalPendingAmount) }}</th>
                                                </tr>
                                                @endif
                                            @endforeach
                                            <tr class="table-active text-danger">
                                                <th colspan="5" class="text-end p-1">Grand Total</th>
                                                <th class="text-start p-1">{{ number_format($GrandOrderQty) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandOrderLoose) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandOrderAmount) }}</th>
                                                <th class="text-start p-1"></th>
                                                <th class="text-start p-1">{{ number_format($GrandDeliveredQty) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandDeliveredLoose) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandDeliveredAmount) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandPendingQty) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandPendingLoose) }}</th>
                                                <th class="text-start p-1">{{ number_format($GrandPendingAmount) }}</th>
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



