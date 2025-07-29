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
                                    <h3>Branch Wise Stock Report</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                    <h5 class="fs-14 mb-0">{{ $branch }}</h5>
                                </div>
                                <!--end col-->
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
                                    {{-- <h5 class="fs-14 mb-0"><span id="total-amount">{{ \Carbon\Carbon::now()->format('h:i A') }}</span></h5> --}}
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Stock Value</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ $value }}</span></h5>
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
                                            <th scope="col" class="text-start">Product</th>
                                            <th scope="col" class="text-start">Unit</th>
                                            <th scope="col" class="text-start">Pack Size</th>
                                            <th scope="col" class="text-start">Unit Value</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col">Stock Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                            $totalAmount = 0;
                                        @endphp
                                        @foreach ($vendors as $key => $vendor)
                                            @php
                                                $amount = $vendor->vendor_products->sum('stock_value');

                                                $totalAmount += $amount;
                                                $tQty = 0;
                                            @endphp
                                            @foreach ($vendor->vendor_products as $key => $product)
                                                @php
                                                    $qty = $product->stock / $product->units[0]->value;
                                                    $totalQty += $qty;
                                                    $tQty += $qty;
                                                @endphp
                                            @endforeach
                                           {{--  @if ($tQty > 0) --}}
                                                @endphp
                                                <tr>
                                                    <td colspan="7">
                                                        <h4 class="fs-14 mb-0 text-start">{{ $vendor->title }}</h4>
                                                    </td>
                                                </tr>

                                                @foreach ($vendor->vendor_products as $key => $product)
                                                    @php
                                                        if ($product->stock > 0) {
                                                            $unit_value =
                                                                $product->stock_value /
                                                                ($product->stock / $product->units[0]->value);
                                                        } else {
                                                            $unit_value = 0;
                                                        }
                                                        $qty = $product->stock / $product->units[0]->value;
                                                    @endphp
                                                   {{--  @if ($qty > 0) --}}
                                                        <tr>
                                                            <td class="p-1 m-0">{{ $key + 1 }}</td>
                                                            <td class="text-start p-1 m-0">{{ $product->name }}</td>
                                                            <td class="text-start p-1 m-0">
                                                                {{ $product->units[0]->unit_name }}</td>
                                                            <td class="text-start p-1 m-0">{{ $product->units[0]->value }}
                                                            </td>
                                                            <td class="text-start p-1 m-0">
                                                                {{ number_format($unit_value, 2) }}</td>
                                                            <td class="text-end p-1 m-0">{{ number_format($qty, 2) }}</td>
                                                            <td class="text-end p-1 m-0">
                                                                {{ number_format($product->stock_value, 2) }}</td>
                                                        </tr>
                                                   {{--  @endif --}}
                                                @endforeach
                                                <tr>
                                                    <td colspan="5" class="text-end p-1 m-0">Total</td>
                                                    <td class="text-end p-1 m-0">{{ number_format($tQty, 2) }}</td>
                                                    <td class="text-end p-1 m-0">{{ number_format($amount, 2) }}</td>
                                                </tr>
                                            {{-- @endif --}}
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end p-1 m-0">Grand Total</th>
                                            <th class="text-end p-1 m-0">{{ number_format($totalQty, 2) }}</th>
                                            <th class="text-end p-1 m-0">{{ number_format($totalAmount, 2) }}</th>
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
