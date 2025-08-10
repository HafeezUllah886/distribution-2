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
                                        <h1>{{ Auth()->user()->branch->name }}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Daily Invoice Wise Products Sales Report</h3>
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
                                        <h5 class="fs-14 mb-0">{{ $branch->name }}</h5>
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
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <td scope="col" class="p-1 m-0" style="width: 50px;">#</td>
                                                <td scope="col" class="p-1 m-0" style="width: 50px;">Inv #</td>
                                                <td scope="col" class="text-start p-1 m-0">Customer</td>
                                                <td scope="col" class="p-1 m-0">Date</td>
                                                <td scope="col" class="p-1 m-0">Orderbooker</td>
                                                <td scope="col" class="p-1 m-0">Supplyman</td>
                                                <td scope="col" class="p-1 m-0">Net Amount</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalQty = 0;
                                                $totalLoose = 0;
                                                $totalBonus = 0;
                                                $totalAmount = 0;
                                            @endphp
                                        @foreach ($sales as $key => $sale)
                                        @php
                                            $totalQty += $sale->details->sum('qty');
                                            $totalLoose += $sale->details->sum('loose');
                                            $totalBonus += $sale->details->sum('bonus');
                                            $totalAmount += $sale->details->sum('amount');
                                        @endphp
                                        <tr class="table-active text-success">
                                            <th class="p-1 m-0">{{ $key+1 }}</th>
                                            <th class="p-1 m-0">{{ $sale->id }}</th>
                                            <th class="text-start p-1 m-0">{{ $sale->customer->title }}</th>
                                            <th class="p-1 m-0">{{ date('d M Y', strtotime($sale->date)) }}</th>
                                            <th class="p-1 m-0">{{ $sale->orderbooker->name }}</th>
                                            <th class="p-1 m-0">{{ $sale->supplyman->title }}</th>
                                            <th class="p-1 m-0">{{ number_format($sale->net,2)}}</th>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <table class="table table-bordered table-nowrap align-middle mb-0">
                                                    <thead>
                                                        <tr class="table-active">
                                                            <td scope="col" class="p-1 m-0" style="width: 50px;">#</td>
                                                            <td scope="col" class="text-start p-1 m-0" style="width: 50px;">Product</td>
                                                            <td scope="col" class="p-1 m-0">Unit</td>
                                                            <td scope="col" class="p-1 m-0">Pack Size</td>
                                                            <td scope="col" class="p-1 m-0">Qty</td>
                                                            <td scope="col" class="p-1 m-0">Loose</td>
                                                            <td scope="col" class="p-1 m-0">Bonus</td>
                                                            <td scope="col" class="text-end p-1 m-0">Amount</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($sale->details as $key => $detail)
                                                    <tr>
                                                        <td class="p-1 m-0">{{ $key+1 }}</td>
                                                        <td class="p-1 m-0">{{ $detail->product->name }}</td>
                                                        <td class="p-1 m-0">{{ $detail->unit->unit_name }}</td>
                                                        <td class="p-1 m-0">{{ $detail->unit->value }}</td>
                                                        <td class="p-1 m-0">{{ $detail->qty }}</td>
                                                        <td class="p-1 m-0">{{ $detail->loose }}</td>
                                                        <td class="p-1 m-0">{{ $detail->bonus }}</td>
                                                        <td class="text-end p-1 m-0">{{ number_format($detail->amount,2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                   
                                                        <tr class="table-active text-success">
                                                            <th colspan="4" class="text-end">Total</th>
                                                            <th class="text-end">{{ number_format($sale->details->sum('qty')) }}</th>
                                                            <th class="text-end">{{ number_format($sale->details->sum('loose')) }}</th>
                                                            <th class="text-end">{{ number_format($sale->details->sum('bonus')) }}</th>
                                                            <th class="text-end">{{ number_format($sale->details->sum('amount'),2) }}</th>
                                                        </tr>
                                                    </tbody>
                                                   
                                                </table>
                                                <hr>
                                            </td>
                                        </tr>
                                        @endforeach
                                       
                                        <tr class="table-active text-success">
                                            <th colspan="7" class="text-start">Grand Total:    QTY: {{ number_format($totalQty) }} ----- LOOSE: {{ number_format($totalLoose) }} ----- BONUS: {{ number_format($totalBonus) }} ----- AMOUNT: {{ number_format($totalAmount,2) }}</th>
                                          
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



