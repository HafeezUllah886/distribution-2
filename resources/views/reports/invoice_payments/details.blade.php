@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-9">
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
                                    <h1>{{ projectNameHeader() }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Invoice Payments Report</h3>
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
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Orderbooker</p>
                                    <h5 class="fs-14 mb-0">{{ $orderbooker }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Customer</p>
                                    <h5 class="fs-14 mb-0">{{ $customer }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Type</p>
                                    <h5 class="fs-14 mb-0">{{ $type }}</h5>
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
                    <div class="col-lg-12 pb-0 mb-0">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-nowrap align-middle mb-0">
                                    @php
                                        $totalAmount = 0;
                                        $totalPaid = 0;
                                        $totalDue = 0;
                                        $totalInv = 0;

                                        $totalCash = 0;
                                        $totalOnline = 0;
                                        $totalCheque = 0;
                                        $totalOthers = 0;


                                        $totalPartialyPaid = 0;
                                        $totalPartialyPaidNum = 0;
                                        $totalPaidFullNum = 0;
                                        $totalPaidFull = 0;

                                        $totalPartialyDue = 0;
                                        $totalPartialyDueNum = 0;

                                        $totalUntouchedNum = 0;
                                        $totalUntouched = 0;
                                    @endphp

                                    @foreach ($data as $key => $area)
                                    @php
                                        $countArea = 0;
                                        foreach ($area['customers'] as $customer) {
                                            $countArea += $customer->sales->count();
                                        }
                                    @endphp
                                    @if ($countArea > 0)
                                        <tr class="table-active text-success">
                                            <th class="text-start p-1" colspan="7">{{$key}}</th>
                                        </tr>
                                        @php
                                            $areaTotalAmount = 0;
                                            $areaTotalPaid = 0;
                                            $areaTotalDue = 0;
                                            $areaTotalInv = 0;
                                        @endphp
                                        @foreach ($area['customers'] as $customer)
                                        @php
                                            $countCustomer = $customer->sales->count();
                                        @endphp
                                        @if ($countCustomer > 0)
                                            <tr class="table-active text-primary ">
                                                <th class="text-start p-1" colspan="7">{{$customer->title}}</th>
                                            </tr>
                                            <tr>
                                                <th class="p-1">#</th>
                                                <th class="p-1">Inv No</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Age</th>
                                                <th class="p-1">Amount</th>
                                                <th class="p-1">Payment</th>
                                                <th class="p-1">Balance</th>
                                            </tr>
                                            @php
                                                $customerTotalAmount = 0;
                                                $customerTotalPaid = 0;
                                                $customerTotalDue = 0;
                                                $customerTotalInv = 0;
                                            @endphp
                                            @foreach ($customer->sales as $sale)
                                            @php
                                                $customerTotalAmount += $sale->net;
                                                $customerTotalPaid += $sale->paid();
                                                $customerTotalDue += $sale->due();
                                                $customerTotalInv += 1;

                                                $areaTotalAmount += $sale->net;
                                                $areaTotalPaid += $sale->paid();
                                                $areaTotalDue += $sale->due();
                                                $areaTotalInv += 1;

                                                $totalAmount += $sale->net;
                                                $totalPaid += $sale->paid();
                                                $totalDue += $sale->due();
                                                $totalInv += 1;

                                                if($sale->paid() > 0 && $sale->due() > 0)
                                                {
                                                    $totalPartialyPaidNum += 1;
                                                    $totalPartialyPaid += $sale->paid();
                                                    $totalPartialyDueNum += 1;
                                                    $totalPartialyDue += $sale->due();
                                                }

                                                if($sale->due() == 0)
                                                {
                                                    $totalPaidFull += $sale->paid();
                                                    $totalPaidFullNum += 1;
                                                }

                                                if( $sale->paid() == 0 )
                                                {
                                                    $totalUntouched += $sale->due();
                                                    $totalUntouchedNum += 1;
                                                }

                                            @endphp
                                                <tr>
                                                    <td class="p-1">{{$loop->iteration}}</td>
                                                    <td class="p-1">{{$sale->id}}</td>
                                                    <td class="p-1">{{date('d M Y', strtotime($sale->date))}}</td>
                                                    <td class="p-1">{{$sale->age()}}</td>
                                                    <td class="p-1">{{number_format($sale->net)}}</td>
                                                    <td class="p-1">
                                                        <table class="table">
                                                            <tr >
                                                                <th class="p-1">Date</th>
                                                                <th class="p-1">Amount</th>
                                                                <th class="p-1">Method</th>
                                                                <th class="p-1">User</th>
                                                                <th class="p-1">Notes</th>
                                                            </tr>
                                                            @foreach ($sale->payments as $payment)
                                                            @php
                                                                if($payment->method == "Cash")
                                                                {
                                                                    $totalCash += $payment->amount;
                                                                }
                                                                if($payment->method == "Online")
                                                                {
                                                                    $totalOnline += $payment->amount;
                                                                }
                                                                if($payment->method == "Cheque")
                                                                {
                                                                    $totalCheque += $payment->amount;
                                                                }
                                                                if($payment->method == "Other")
                                                                {
                                                                    $totalOthers += $payment->amount;
                                                                }
                                                            @endphp
                                                                <tr>
                                                                    <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                                    <td class="p-1">{{number_format($payment->amount)}}</td>
                                                                    <td class="p-1">{{$payment->method}}</td>
                                                                    <td class="p-1">{{$payment->user->name}}</td>
                                                                    <td class="p-1">{{$payment->notes}}</td>
                                                                </tr>
                                                            @endforeach
                                                            <tr class="table-active">
                                                                <th class="p-1">Total</th>
                                                                <th class="p-1">{{number_format($sale->paid())}}</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td class="p-1">{{number_format($sale->due())}}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-active text-primary">
                                                <th class="p-1 text-end" colspan="4">Total of {{$customer->title}} - {{$customerTotalInv}} Inv(s)</th>
                                                <th class="p-1">{{number_format($customerTotalAmount)}}</th>
                                                <th class="p-1">{{number_format($customerTotalPaid)}}</th>
                                                <th class="p-1">{{number_format($customerTotalDue)}}</th>
                                            </tr>
                                        @endif
                                        @endforeach
                                        <tr class="table-active text-success">
                                            <th class="p-1 text-end" colspan="4">Total of {{$key}} - {{$areaTotalInv}} Inv(s)</th>
                                            <th class="p-1">{{number_format($areaTotalAmount)}}</th>
                                            <th class="p-1">{{number_format($areaTotalPaid)}}</th>
                                            <th class="p-1">{{number_format($areaTotalDue)}}</th>
                                        </tr>
                                    @endif
                                 
                                    @endforeach
                                        <tr class="table-active text-danger">
                                            <th class="p-1 text-end" colspan="4">Grand Total - {{$totalInv}} Inv(s)</th>
                                            <th class="p-1">{{number_format($totalAmount)}}</th>
                                            <th class="p-1">{{number_format($totalPaid)}}</th>
                                            <th class="p-1">{{number_format($totalDue)}}</th>
                                        </tr>
                                   
                                </tbody>
                              
                                </table><!--end table-->
                            </div>
                        </div>
                        <!--end card-body-->
                    </div><!--end col-->
                    <div class="col-lg-12 p-4 pt-0 pb-0 mb-0 mt-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center p-1">Collection</th>
                                    <th class="text-center p-1">Cash</th>
                                    <th class="text-center p-1">Online</th>
                                    <th class="text-center p-1">Cheque</th>
                                    <th class="text-center p-1">Others</th>
                                    <th class="text-center p-1">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center p-1">Amount</td>
                                    <td class="text-center p-1">{{ number_format($totalCash) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalOnline) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalCheque) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalOthers) }}</td>
                                    <td class="text-center p-1">
                                        {{ number_format($totalCash + $totalOnline + $totalCheque + $totalOthers) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div> 
                    
                    <div class="col-lg-12 p-4 pt-0 mt-0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center p-1">Total Inv</th>
                                    <th class="text-center p-1">Fully Paid</th>
                                    <th class="text-center p-1">Partially Paid</th>
                                    <th class="text-center p-1">Partially Due</th>
                                    <th class="text-center p-1">Untouched</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center p-1">{{ $totalInv }}</td>
                                    <td class="text-center p-1">{{ $totalPaidFullNum }}</td>
                                    <td class="text-center p-1">{{ $totalPartialyPaidNum }}</td>
                                    <td class="text-center p-1">{{ $totalPartialyDueNum }}</td>
                                    <td class="text-center p-1">{{ $totalUntouchedNum }}</td>

                                </tr>
                                <tr>
                                    <td class="text-center p-1">{{ number_format($totalAmount) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalPaidFull) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalPartialyPaid) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalPartialyDue) }}</td>
                                    <td class="text-center p-1">{{ number_format($totalUntouched) }}</td>
                                   

                                </tr>
                            </tbody>
                        </table>
                    </div> 
                </div><!--end row-->
            </div>
            <!--end card-->
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
