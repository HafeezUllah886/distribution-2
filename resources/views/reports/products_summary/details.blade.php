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
                                        <h3>Products Summary Report</h3>
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
                                        <h5 class="fs-14 mb-0">{{ $branch }}</h5>
                                    </div>
                                   
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
                                                <th scope="col" class="text-start">Vendor</th>
                                                <th scope="col" class="text-end">Unit</th>
                                                <th scope="col" class="text-end">Pack Size</th>
                                                <th scope="col" class="text-end">S-Price</th>
                                                <th scope="col" class="text-end">Sold</th>
                                                <th scope="col" class="text-end">Stock</th>
                                                <th scope="col" class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalSoldQty = 0;
                                                $totalSoldLoose = 0;

                                                $totalProfit = 0;

                                                $totalStockQty = 0;
                                                $totalStockLoose = 0;
                                                $totalAmount = 0;
                                            @endphp
                                        @foreach ($topProductsArray as $key => $product)
                                        @php
                                            $sold = PackInfoWithoutName($product['unitValue'], $product['sold']);
                                            [$soldQty, $soldLoose] = explode(",", $sold);

                                            $totalSoldQty += (int) $soldQty;
                                            $totalSoldLoose += (int) $soldLoose;

                                            $stock = PackInfoWithoutName($product['unitValue'], $product['stock']);
                                            [$stockQty, $stockLoose] = explode(",", $stock);

                                            $totalStockQty += (int) $stockQty;
                                            $totalStockLoose += (int) $stockLoose;

                                            $totalProfit += $product['profit'];
                                            $totalAmount += $product['amount'];
                                        @endphp
                                            <tr>
                                                <td>{{ $key+1}}</td>
                                                <td class="text-start">{{ $product['name']}}</td>
                                                <td class="text-start">{{ $product['vendor']}}</td>
                                                <td class="text-end">{{ $product['unit']}}</td>
                                                <td class="text-end">{{ $product['unitValue']}}</td>
                                                <td class="text-end">{{ number_format($product['price'] * $product['unitValue'],2)}}</td>
                                                <td class="text-end">{{packInfoWithOutName($product['unitValue'], $product['sold'])}} </td>
                                                <td class="text-end">{{packInfoWithOutName($product['unitValue'], $product['stock'])}} </td>
                                                <td class="text-end">{{ number_format($product['amount'],2) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-end">Total</th>
                                                <th class="text-end">{{ number_format($totalSoldQty) }}, {{ number_format($totalSoldLoose) }}</th>
                                                <th class="text-end">{{ number_format($totalStockQty) }}, {{ number_format($totalStockLoose) }}</th>
                                                <th class="text-end">{{ number_format($totalAmount) }}</th>
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



