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
                                        @foreach ($customers as $key => $customer)
                                                @php
                                                $totalPaid = 0;
                                                $totalDue = 0;
                                            @endphp
                                            @foreach ($customer->sales as $key => $sale)
                                                @php
                                                    $totalPaid += $sale->paid();
                                                    $totalDue += $sale->due();
                                                @endphp
                                            @endforeach
                                            @if($totalPaid > 0 || $totalDue > 0)
                                        <thead>
                                            <tr class="table-active bg-success bg-opacity-50">
                                                <th scope="col" colspan="7" class="text-start">{{ $customer->title }} - {{ $customer->area->name }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                            @foreach ($customer->sales as $key => $sale)
                                            
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
                                                            <td class="p-1 text-start">Notes</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($sale->payments as $payment)
                                                            <tr>
                                                                <td class="p-1">{{ date('d M Y', strtotime($payment->date)) }}</td>
                                                                <td class="p-1">{{ number_format($payment->amount) }}</td>
                                                                <td class="p-1 text-start">{{ $payment->method }}</td>
                                                                <td class="p-1 text-start">{{ $payment->notes }}</td>
                                                            </tr>
                                                        @endforeach
                                                        <tr class="table-active">
                                                            <td class="text-end p-1">Total:</td>
                                                            <td class="p-1">{{ number_format($sale->paid()) }}</td>
                                                            <td colspan="2"></td>
                                                        </tr>
                                                    </tbody>
                                                       </table>
                                                </td>
                                                <td>{{ number_format($sale->due()) }}</td>
                                            </tr>
                                            @endforeach
                                            <tr class="table-active">
                                                <td colspan="7" class="text-start">Total:  Inv({{ $customer->sales->count() }}) ------ Amount ({{ $customer->sales->sum('net') }}) ------ Paid({{ $totalPaid }}) ------ Due({{ $totalDue }})</td>
                                            </tr>
                                        </tbody>
                                        @endif
        
                                        @endforeach
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



