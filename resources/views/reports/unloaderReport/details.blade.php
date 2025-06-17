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
                                        <h3>Unloader Labour Charges Report</h3>
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
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Unloader</p>
                                        <h5 class="fs-14 mb-0">{{ $unloader->title }}</h5>
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
                                                <th scope="col" class="text-start">Vendor</th>
                                                <th scope="col" class="text-start">Bilty No.</th>
                                                <th scope="col" class="text-start">Transport</th>
                                                <th scope="col" class="text-start">Qty</th>
                                                <th scope="col" class="text-start">Bill Amount</th>
                                                <th scope="col" class="text-start">Labour Charge</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total_qty = 0;
                                                $total_loose = 0;
                                            @endphp
                                        @foreach ($purchases as $key => $purchase)
                                            @php
                                                $total_qty += $purchase->details->sum('qty');
                                                $total_loose += $purchase->details->sum('loose');
                                            @endphp
                                            <tr>
                                                <td class="p-1 m-0">{{ $key+1}}</td>
                                                <td class="text-center p-1 m-0">{{ $purchase->id}}</td>
                                                <td class="text-start p-1 m-0">{{ date('d M Y', strtotime($purchase->recdate))}}</td>
                                                <td class="text-start p-1 m-0">{{ $purchase->vendor->title}}</td>
                                                <td class="text-start p-1 m-0">{{ $purchase->bilty}}</td>
                                                <td class="text-end p-1 m-0">{{ $purchase->transporter}}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($purchase->details->sum('qty'))}}, {{ $purchase->details->sum('loose') }}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($purchase->net,2)}}</td>
                                                <td class="text-end p-1 m-0">{{ number_format($purchase->totalLabor,2)}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="6" class="text-end p-1 m-0">Total</td>
                                            <td class="text-end p-1 m-0">{{ number_format($total_qty)}} , {{ $total_loose }}</td>
                                            <td class="text-end p-1 m-0">{{ number_format($purchases->sum('net'),2)}}</td>
                                            <td class="text-end p-1 m-0">{{ number_format($purchases->sum('totalLabor'),2)}}</td>
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



