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
                                        <h3>Supplyman Labour Charges Report</h3>
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
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Supplyman</p>
                                        <h5 class="fs-14 mb-0">{{ $supplyman->title }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">{{ date("d M Y") }}</span></h5>
                                    </div>
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
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Inv No.</th>
                                                <th scope="col" class="text-start">Date</th>
                                                <th scope="col" class="text-start">Customer</th>
                                                <th scope="col" class="text-start">Orderbooker</th>
                                                <th scope="col" class="text-start">Bilty No.</th>
                                                <th scope="col" class="text-start">Transport</th>
                                                <th scope="col" class="text-start">Qty</th>
                                                <th scope="col" class="text-start">Bill Amount</th>
                                                <th scope="col" class="text-start">Labour Charge</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_labour = 0;
                                                $total_qty = 0;
                                                $total_loose = 0;
                                            @endphp
                                           
                                        @foreach ($sales as $key => $sale)
                                        @php
                                            $labor = 0;
                                        @endphp
                                        @foreach ($sale->details as $detail)
                                            @php
                                                $labor += $detail->labor * $detail->pc;
                                            @endphp
                                        @endforeach
                                        @php
                                            $total_labour += $labor;
                                            $total_qty += $sale->details->sum('qty');
                                            $total_loose += $sale->details->sum('loose');
                                        @endphp
                                            <tr>
                                                <td class="p-1 m-0">{{ $key+1}}</td>
                                                <td class="text-center p-1 m-0">{{ $sale->id}}</td>
                                                <td class="text-start p-1 m-0">{{ date('d M Y', strtotime($sale->date))}}</td>
                                                <td class="text-start p-1 m-0">{{ $sale->customer->title}}</td>
                                                <td class="text-start p-1 m-0">{{ $sale->orderbooker->name}}</td>
                                                <td class="text-end p-1 m-0">{{ $sale->bilty}}</td>
                                                <td class="text-end p-1 m-0">{{ $sale->transporter}}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($sale->details->sum('qty'))}}, {{ $sale->details->sum('loose') }}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($sale->net,2)}}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($labor,2)}}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active bg-success bg-opacity-25">
                                            <th colspan="7" class="text-end p-1 m-0">Total</th>
                                            <th class="text-end p-1 m-0">{{ number_format($total_qty)}} , {{ $total_loose }}</th>
                                            <th class="text-end p-1 m-0">{{ number_format($sales->sum('net'),2)}}</th>
                                            <th class="text-end p-1 m-0">{{ number_format($total_labour,2)}}</th>
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



