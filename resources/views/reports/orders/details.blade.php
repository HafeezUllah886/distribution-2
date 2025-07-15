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
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                        <h5 class="fs-14 mb-0">{{ $branch }}</h5>
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
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Branch</th>
                                                <th scope="col" class="text-start">Customer Name</th>
                                                <th scope="col" class="text-start">Order Booker</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Disc</th>
                                                <th scope="col">Fright</th>
                                                <th scope="col">Labor</th>
                                                <th scope="col">Claim</th>
                                                <th scope="col">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $GrandtotalQty = 0;
                                                $GrandtotalLoose = 0;
                                                $GrandtotalDisc = 0;
                                                $GrandtotalFright = 0;
                                                $GrandtotalLabor = 0;
                                                $GrandtotalClaim = 0;
                                                $GrandtotalAmount = 0;
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
                                            <tr class="table-active">
                                                <th scope="row" class="text-start" colspan="11">{{ $area->name}}</th>
                                            </tr>
                                            @php
                                                $areaTotalQty = 0;
                                                $areaTotalLoose = 0;
                                                $areaTotalDisc = 0;
                                                $areaTotalFright = 0;
                                                $areaTotalLabor = 0;
                                                $areaTotalClaim = 0;
                                                $areaTotalAmount = 0;
                                            @endphp
                                                @foreach ($area->customers as $customer)

                                                <tr class="table-active">
                                                    <th scope="row" class="text-start" colspan="11">{{ $customer->title }}</th>
                                                </tr>
                                                @php
                                                    $totalQty = 0;
                                                    $totalLoose = 0;
                                                    $totalDisc = 0;
                                                    $totalFright = 0;
                                                    $totalLabor = 0;
                                                    $totalClaim = 0;
                                                    $totalAmount = 0;
                                                @endphp
                                                    @foreach ($customer->orders as $order)
                                                    @php
                                                        $totalQty += $order->details->sum('qty');
                                                        $totalLoose += $order->details->sum('loose');
                                                        $totalDisc += $order->details->sum('discount') + $order->details->sum('discountvalue');
                                                        $totalFright += $order->details->sum('fright');
                                                        $totalLabor += $order->details->sum('labor');
                                                        $totalClaim += $order->details->sum('claim');
                                                        $totalAmount += $order->details->sum('amount');

                                                        $areaTotalQty += $order->details->sum('qty');
                                                        $areaTotalLoose += $order->details->sum('loose');
                                                        $areaTotalDisc += $order->details->sum('discount') + $order->details->sum('discountvalue');
                                                        $areaTotalFright += $order->details->sum('fright');
                                                        $areaTotalLabor += $order->details->sum('labor');
                                                        $areaTotalClaim += $order->details->sum('claim');
                                                        $areaTotalAmount += $order->details->sum('amount');

                                                        $GrandtotalQty += $order->details->sum('qty');
                                                        $GrandtotalLoose += $order->details->sum('loose');
                                                        $GrandtotalDisc += $order->details->sum('discount') + $order->details->sum('discountvalue');
                                                        $GrandtotalFright += $order->details->sum('fright');
                                                        $GrandtotalLabor += $order->details->sum('labor');
                                                        $GrandtotalClaim += $order->details->sum('claim');
                                                        $GrandtotalAmount += $order->details->sum('amount');
                                                    @endphp
                                                    <tr>
                                                        <td scope="row" class="text-start ">{{ $loop->iteration }}</td>
                                                        <td class="text-start">{{ $order->branch->name }}</td>
                                                        <td class="text-start">{{ $order->customer->title }}</td>
                                                        <td class="text-start">{{ $order->orderbooker->name }}</td>
                                                        <td class="text-start">{{ date('d M Y', strtotime($order->date)) }}</td>
                                                        <td class="text-start">{{ $order->details->sum('qty') }}, {{$order->details->sum('loose')}}</td>
                                                        <td class="text-start">{{ $order->details->sum('discount') + $order->details->sum('discountvalue') }}</td>
                                                        <td class="text-start">{{ $order->details->sum('fright') }}</td>
                                                        <td class="text-start">{{ $order->details->sum('labor') }}</td>
                                                        <td class="text-start">{{ $order->details->sum('claim') }}</td>
                                                        <td class="text-start">{{ $order->details->sum('amount') }}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr class="table-active text-success">
                                                        <th colspan="5" class="text-end">Total of {{ $customer->title }}</th>
                                                        <th class="text-start">{{ $totalQty }}, {{$totalLoose}}</th>
                                                        <th class="text-start">{{ $totalDisc }}</th>
                                                        <th class="text-start">{{ $totalFright }}</th>
                                                        <th class="text-start">{{ $totalLabor }}</th>
                                                        <th class="text-start">{{ $totalClaim }}</th>
                                                        <th class="text-start">{{ $totalAmount }}</th>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-active text-primary">
                                                    <td colspan="5" class="text-end">Total of {{ $area->name }}</td>
                                                    <th class="text-start">{{ $areaTotalQty }}, {{$areaTotalLoose}}</th>
                                                    <th class="text-start">{{ $areaTotalDisc }}</th>
                                                    <th class="text-start">{{ $areaTotalFright }}</th>
                                                    <th class="text-start">{{ $areaTotalLabor }}</th>
                                                    <th class="text-start">{{ $areaTotalClaim }}</th>
                                                    <th class="text-start">{{ $areaTotalAmount }}</th>
                                                </tr>
                                                @endif
                                            @endforeach
                                           
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-active text-danger">
                                                <th colspan="5" class="text-end">Grand Total</th>
                                                <th class="text-start">{{ $GrandtotalQty }}, {{$GrandtotalLoose}}</th>
                                                <th class="text-start">{{ $GrandtotalDisc }}</th>
                                                <th class="text-start">{{ $GrandtotalFright }}</th>
                                                <th class="text-start">{{ $GrandtotalLabor }}</th>
                                                <th class="text-start">{{ $GrandtotalClaim }}</th>
                                                <th class="text-start">{{ $GrandtotalAmount }}</th>
                                            </tr>
                                        </tfoot>
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



