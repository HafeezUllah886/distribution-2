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
                                        
                                        </thead>
                                        @php
                                            $grandTotalInv = 0;
                                            $grandTotalPaid = 0;
                                            $grandTotalDue = 0;
                                            $grandTotalAmount = 0;

                                            $totalCash = 0;
                                            $totalOnline = 0;
                                            $totalCheque = 0;
                                            $totalOthers = 0;

                                            $totalPaidInv = 0;
                                            $totalDueInv = 0;
                                            $totalPartialyPaidInv = 0;

                                            $totalPaidAmount = 0;
                                            $totalDueAmount = 0;
                                            $totalPartialyPaidAmount = 0;

                                        @endphp
                                        @foreach ($customers as $key => $customer)
                                                @php
                                                $totalPaid = 0;
                                                $totalDue = 0;
                                            @endphp
                                            @foreach ($customer->sales as $key => $sale)
                                                @php
                                                    $totalPaid += $sale->paid();
                                                    $totalDue += $sale->due();

                                                    if($sale->paid() == $sale->net)
                                                    {
                                                        $totalPaidInv += 1;
                                                        $totalPaidAmount += $sale->net;

                                                    }
                                                    if($sale->paid() != 0 && $sale->paid() < $sale->net)
                                                    {
                                                        $totalPartialyPaidInv += 1;
                                                        $totalPartialyPaidAmount += $sale->paid();
                                                    }
                                                    if($sale->paid() == 0)
                                                    {
                                                        $totalDueInv += 1;
                                                        $totalDueAmount += $sale->due();
                                                    }
                                                   
                                                    
                                                @endphp
                                            @endforeach
                                            @php
                                                $grandTotalInv += $customer->sales->count();
                                                $grandTotalPaid += $totalPaid;
                                                $grandTotalDue += $totalDue;
                                                $grandTotalAmount += $customer->sales->sum('net');
                                            @endphp
                                            @if($totalPaid > 0 || $totalDue > 0)
                                        <thead>
                                            <tr class="table-active bg-success bg-opacity-50">
                                                <th scope="col" colspan="7" class="text-start">{{ $customer->title }} - {{ $customer->area->name }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                            @foreach ($customer->sales as $key => $sale)
                                            <thead>
                                                <tr class="table-active">
                                                    <th scope="col" style="width: 50px;" >#</th>
                                                    <th scope="col">Inv #</th>
                                                    <th scope="col">Inv Date</th>
                                                    <th scope="col">Inv Age</th>
                                                    <th scope="col">Inv Amount</th>
                                                    <th scope="col">Payments</th>
                                                    <th scope="col">Balance</th>
                                                </tr>
                                            </thead>
                                            
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $sale->id }}</td>
                                                <td>{{ date('d M Y', strtotime($sale->date)) }}</td>
                                                <td>{{ $sale->age() }}</td>
                                                <td>{{ $sale->net }}</td>
                                                <td>
                                                   <table class="table table-bordered mb-0">
                                                    <thead>
                                                        <tr class="table-active">
                                                            <td class="p-1">Date</td>
                                                            <td class="p-1">Amount</td>
                                                            <td class="p-1 text-start">Method</td>
                                                            <td class="p-1 text-start">User</td>
                                                            <td class="p-1 text-start">Notes</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($sale->payments as $payment)
                                                        @php
                                                            if($payment->method == 'Cash'){
                                                                $totalCash += $payment->amount;
                                                            }elseif($payment->method == 'Online'){
                                                                $totalOnline += $payment->amount;
                                                            }elseif($payment->method == 'Cheque'){
                                                                $totalCheque += $payment->amount;
                                                            }else{
                                                                $totalOthers += $payment->amount;
                                                            }
                                                        @endphp
                                                            <tr>
                                                                <td class="p-1">{{ date('d M Y', strtotime($payment->date)) }}</td>
                                                                <td class="p-1">{{ number_format($payment->amount) }}</td>
                                                                <td class="p-1 text-start">{{ $payment->method }}</td>
                                                                <td class="p-1 text-start">{{ $payment->user->name }}</td>
                                                                <td class="p-1 text-start">{{ $payment->notes }}</td>
                                                            </tr>
                                                        @endforeach
                                                       
                                                    </tbody>
                                                       </table>
                                                </td>
                                                <td>{{ number_format($sale->due()) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-active ">
                                                <th class="text-start">Total</th>
                                                <th>{{ $customer->sales->count() }}</th>
                                                <th></th>
                                                <th></th>
                                                <th>{{ number_format($customer->sales->sum('net')) }}</th>
                                                <th>{{ number_format($totalPaid) }}</th>
                                                <th>{{ number_format($totalDue) }}</th>
                                            </tr>
                                        </tbody>
                                        @endif
        
                                        @endforeach
                                        <tfoot>
                                            <tr class="table-active bg-info bg-opacity-25">
                                                <th class="text-start">G-Total:</th>
                                                <th>{{ $grandTotalInv }}</th>
                                                <th></th>
                                                <th></th>
                                                <th>{{ number_format($grandTotalAmount) }}</th>
                                                <th>{{ number_format($grandTotalPaid) }}</th>
                                                <th>{{ number_format($grandTotalDue) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table><!--end table-->
                                </div>
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12 p-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Collection</th>
                                        <th class="text-center">Cash</th>
                                        <th class="text-center">Online</th>
                                        <th class="text-center">Cheque</th>
                                        <th class="text-center">Others</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">Amount</td>
                                        <td class="text-center">{{ number_format($totalCash) }}</td>
                                        <td class="text-center">{{ number_format($totalOnline) }}</td>
                                        <td class="text-center">{{ number_format($totalCheque) }}</td>
                                        <td class="text-center">{{ number_format($totalOthers) }}</td>
                                        <td class="text-center">{{ number_format($totalCash + $totalOnline + $totalCheque + $totalOthers) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12 p-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Total Inv</th>
                                        <th class="text-center">Total Paid</th>
                                        <th class="text-center">Total Partialy Paid</th>
                                        <th class="text-center">Total Due</th>
                                        <th class="text-center">Total Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ $grandTotalInv }}</td>
                                        <td class="text-center">{{ $totalPaidInv }}</td>
                                        <td class="text-center">{{ $totalPartialyPaidInv }}</td>
                                        <td class="text-center">{{ $totalDueInv }}</td>
                                        <td class="text-center">{{ $grandTotalInv }}</td>

                                    </tr>
                                    <tr>
                                        <td class="text-center">{{ number_format($grandTotalAmount) }}</td>
                                        <td class="text-center">{{ number_format($totalPaidAmount) }}</td>
                                        <td class="text-center">{{ number_format($totalPartialyPaidAmount) }}</td>
                                        <td class="text-center">{{ number_format($totalDueAmount) }}</td>
                                        <td class="text-center">{{ number_format($grandTotalDue) }}</td>
                                        
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



