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
                                        <h3>Daily Load Sheet</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Order Booker</p>
                                        <h5 class="fs-14 mb-0">{{ $salesData['orderbooker'] }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($salesData['date'])) }}</h5>
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
                                                <th scope="col" class="text-start">Product</th>
                                                <th scope="col" class="text-end">Quantity</th>
                                                <th scope="col" class="text-end">CTN / Boxes</th>
                                              {{--   <th scope="col" class="text-end">Amount</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody >
                                            @php
                                            $total_qty = 0;
                                            $total_ctn = 0;
                                            @endphp
                                            @foreach ($salesData['sale_details'] as $key => $productDetails)
                                            @php
                                                $total_qty += $productDetails['total_qty'];
                                                $total_ctn += $productDetails['total_qty'] / $productDetails['pack_size'];
                                            @endphp
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td class="text-start">{{ $productDetails['name'] }}</td>
                                                    <td class="text-end">{{ $productDetails['total_qty'] }}</td>
                                                    <td class="text-end">{{ number_format($productDetails['total_qty'] / $productDetails['pack_size'], 2)}} {{$productDetails['pack_size'] > 1 ? "Box" : "Nos"}}</td>
                                                    {{-- <td class="text-end">{{ number_format($productDetails['total_amount'], 2) }}</td>  </tr> --}}
                                                @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($total_qty, 2)}}</th>
                                                <th class="text-end">{{number_format($total_ctn, 2)}}</th>
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



