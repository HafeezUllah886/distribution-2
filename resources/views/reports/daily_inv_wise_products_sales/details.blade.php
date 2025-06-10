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
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" style="width: 50px;">Inv #</th>
                                                <th scope="col" class="text-start">Customer</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Orderbooker</th>
                                                <th scope="col">Supplyman</th>
                                                <th scope="col">Net Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($sales as $key => $sale)
                                        <tr class="table-active">
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $sale->id }}</td>
                                            <td class="text-start">{{ $sale->customer->title }}</td>
                                            <td>{{ date('d M Y', strtotime($sale->date)) }}</td>
                                            <td>{{ $sale->orderbooker->name }}</td>
                                            <td>{{ $sale->supplyman->title }}</td>
                                            <td>{{ number_format($sale->net,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="table-active">
                                                            <th scope="col" style="width: 50px;">#</th>
                                                            <th scope="col" class="text-start" style="width: 50px;">Product</th>
                                                            <th scope="col">Unit</th>
                                                            <th scope="col">Pack Size</th>
                                                            <th scope="col">Qty</th>
                                                            <th scope="col">Loose</th>
                                                            <th scope="col">Bonus</th>
                                                            <th scope="col" class="text-end">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($sale->details as $key => $detail)
                                                    <tr>
                                                        <td>{{ $key+1 }}</td>
                                                        <td>{{ $detail->product->name }}</td>
                                                        <td>{{ $detail->unit->unit_name }}</td>
                                                        <td>{{ $detail->unit->value }}</td>
                                                        <td>{{ $detail->qty }}</td>
                                                        <td>{{ $detail->loose }}</td>
                                                        <td>{{ $detail->bonus }}</td>
                                                        <td class="text-end">{{ number_format($detail->amount,2) }}</td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                                <hr>
                                            </td>
                                        </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
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



