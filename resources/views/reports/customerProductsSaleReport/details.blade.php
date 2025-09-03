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
                                    <h3>Customer Wise Products Sales Report</h3>
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
                                {{-- <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Customer(s)</p>
                                    <h5 class="fs-14 mb-0">{{ is_array($customer_titles) ? implode(',', $customer_titles) : $customer_titles }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Area(s)</p>
                                    <h5 class="fs-14 mb-0">{{ is_array($area) ? implode(',', $area) : $area }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Orderbooker(s)</p>
                                    <h5 class="fs-14 mb-0">{{ is_array($orderbooker_titles) ? implode(',', $orderbooker_titles) : $orderbooker_titles }}</h5>
                                </div> --}}
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
                                    
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                            $totalAmount = 0;
                                            $customer_total_qty = 0;
                                            $customer_total_amount = 0;
                                        @endphp
                                        @php
                                            $rowNumber = 1;
                                        @endphp
                                        @foreach ($customers as $customer)
                                        
                                        @if($customer->sales->count() > 0)
                                        <tr class="table-active">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col" class="text-start">Product</th>
                                            <th scope="col" class="text-start">Brand</th>
                                            <th scope="col" class="text-start">Category</th>
                                            <th scope="col" class="text-start">Unit</th>
                                            <th scope="col" class="text-end">Pack Size</th>
                                            <th scope="col" class="text-end">Sold Qty</th>
                                            <th scope="col" class="text-end">Total Amount</th>
                                        </tr>
                                        <tr class="table-active bg-light">
                                            <th colspan="8" class="text-start text-success">{{ $customer->title }}</th>
                                        </tr>
                                       
                                        
                                        @foreach($customer->sales as $vendor => $products)
                                            <tr class="table-active bg-light">
                                                <th colspan="8" class="text-start text-warning">{{ $vendor }}</th>
                                            </tr>
                                            @php
                                            $vendor_total_qty = 0;
                                            $vendor_total_amount = 0;
                                        @endphp
                                            @foreach($products as $product)
                                            @php
                                                $vendor_total_qty += $product->total_qty;
                                                $vendor_total_amount += $product->total_amount;
                                                $customer_total_qty += $product->total_qty;
                                                $customer_total_amount += $product->total_amount;
                                                $totalQty += $product->total_qty;
                                                $totalAmount += $product->total_amount;
                                            @endphp
                                                <tr>
                                                    <td>{{ $rowNumber++ }}</td>
                                                    <td class="text-start">{{ $product->name }}</td>
                                                    <td class="text-start">{{ $product->brand }}</td>
                                                    <td class="text-start">{{ $product->category }}</td>
                                                    <td class="text-start">{{ $product->unit }}</td>
                                                    <td class="text-end">{{ $product->unit_value }}</td>
                                                    <td class="text-end">{{ $product->total_qty }}</td>
                                                    <td class="text-end">{{ number_format($product->total_amount) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-active text-warning">
                                                <th colspan="6" class="text-end">Total of {{ $vendor }}</th>
                                                <th class="text-end">{{ number_format($vendor_total_qty) }}</th>
                                                <th class="text-end">{{ number_format($vendor_total_amount) }}</th>
                                            </tr>
                                        @endforeach
                                        <tr class="table-active text-success">
                                            <th colspan="6" class="text-end">Total of {{ $customer->title }}</th>
                                            <th class="text-end">{{ number_format($customer_total_qty) }}</th>
                                            <th class="text-end">{{ number_format($customer_total_amount) }}</th>
                                        </tr>

                                        @endif
                                    @endforeach
                                    <tr class="table-active text-danger">
                                        <th colspan="6" class="text-end">Grand Total</th>
                                        <th class="text-end">{{ number_format($totalQty) }}</th>
                                        <th class="text-end">{{ number_format($totalAmount) }}</th>
                                    </tr>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
