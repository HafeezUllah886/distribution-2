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
                                    <h1>{{ Auth()->user()->branch->name }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Daily Vendor Wise Products Sales Report</h3>
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
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
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
                                            <th scope="col">Product</th>
                                            <th scope="col" class="text-start">Unit</th>
                                            <th scope="col" class="text-end">Pack Size</th>
                                            <th scope="col" class="text-end">Sold Qty</th>
                                            <th scope="col" class="text-end">Loose</th>
                                            <th scope="col" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                            $totalLoose = 0;
                                            $totalAmount = 0;
                                        @endphp

                                        @foreach ($vendors as $key => $vendor)
                                            @php
                                                $ser = 1;
                                                $check_total = $vendor->products->sum('product_amount');
                                            @endphp
                                            @if ($check_total > 0)
                                                <tr>
                                                    <td colspan="7" class="text-start">{{ $vendor->title }}</td>
                                                </tr>

                                                @foreach ($vendor->products as $product)
                                                    @if ($product->product_amount > 0)
                                                        <tr>
                                                            <td>{{ $ser++ }}</td>
                                                            <td class="text-start">{{ $product->name }}</td>
                                                            <td class="text-start">{{ $product->product_unit }}</td>
                                                            <td class="text-end">
                                                                {{ number_format($product->product_unit_value) }}</td>
                                                            <td class="text-end">
                                                                {{ number_format($product->product_qty) }}</td>
                                                            <td class="text-end">
                                                                {{ number_format($product->product_loose) }}</td>
                                                            <td class="text-end">
                                                                {{ number_format($product->product_amount) }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                <tr class="table-active">
                                                    <td colspan="4" class="text-end">Total</td>
                                                    <td class="text-end">
                                                        {{ number_format($vendor->products->sum('product_qty')) }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($vendor->products->sum('product_loose')) }}</td>
                                                    <td class="text-end">
                                                        {{ number_format($vendor->products->sum('product_amount')) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $totalQty += $vendor->products->sum('product_qty');
                                                    $totalLoose += $vendor->products->sum('product_loose');
                                                    $totalAmount += $vendor->products->sum('product_amount');
                                                @endphp
                                                 @endif
                                            @endforeach
                                       
                                        <tr class="table-active bg-success bg-opacity-25">
                                            <th colspan="4" class="text-end">Grand Total </th>
                                            <th class="text-end">{{ number_format($totalQty) }}</th>
                                            <th class="text-end">{{ number_format($totalLoose) }}</th>
                                            <th class="text-end">{{ number_format($totalAmount) }}</th>
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
