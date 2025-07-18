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
                                    <thead>
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
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                            $totalLoose = 0;
                                        @endphp
                                        @php
                                            $rowNumber = 1;
                                        @endphp
                                        @foreach ($vendor_wise as $vendorId => $products)
                                            @php
                                                $vendor = \App\Models\accounts::find($vendorId);
                                                $vendorTotal = 0;
                                                $VendorTotalQty = 0;
                                                $VendorTotalLoose = 0;
                                            @endphp
                                            <tr class="table-active bg-light">
                                                <th colspan="8" class="text-start">{{ $vendor ? $vendor->title : 'Unknown Vendor' }}</th>
                                            </tr>
                                            @foreach ($products as $product)
                                                @php
                                                    $qty = packInfoWithOutName($product->unit_value, $product->total_pc);
                                                    [$Qty, $Loose] = explode(',', $qty);
                                                    $totalQty += (int)$Qty;
                                                    $totalLoose += (int)$Loose;
                                                    $vendorTotal += $product->total_amount;
                                                    $VendorTotalQty += (int)$Qty;
                                                    $VendorTotalLoose += (int)$Loose;
                                                @endphp
                                                <tr>
                                                    <td>{{ $rowNumber++ }}</td>
                                                    <td class="text-start">{{ $product->name }}</td>
                                                    <td class="text-start">{{ $product->brand }}</td>
                                                    <td class="text-start">{{ $product->category }}</td>
                                                    <td class="text-start">{{ $product->unit }}</td>
                                                    <td class="text-end">{{ number_format($product->unit_value) }}</td>
                                                    <td class="text-end">{{ packInfoWithOutName($product->unit_value, $product->total_pc) }}</td>
                                                    <td class="text-end">{{ number_format($product->total_amount) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-active">
                                                <td colspan="6" class="text-end fw-bold">Vendor Total:</td>
                                                <td class="text-end fw-bold">{{ $VendorTotalQty }}, {{ $VendorTotalLoose }}</td>
                                                <td class="text-end fw-bold">{{ number_format($vendorTotal) }}</td>
                                            </tr>
                                        @endforeach
                                       
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active bg-success bg-opacity-25">
                                            <th colspan="6" class="text-end">Grand Total </th>
                                            <th class="text-end">{{$totalQty}}, {{ $totalLoose }}</th>
                                            <th class="text-end">{{ number_format($sale_details->sum('total_amount')) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
