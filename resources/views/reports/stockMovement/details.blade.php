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
                                        <h3>Stock Movement Report</h3>
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
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">To (Closing Date)</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($to)) }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                        <h5 class="fs-14 mb-0">{{ $branch->name }}</h5>
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
                                                <th scope="col" rowspan="2" style="width: 50px;">#</th>
                                                <th scope="col" rowspan="2" class="text-start">Product</th>
                                                <th scope="col" rowspan="2" class="text-start">Unit</th>
                                                <th scope="col" rowspan="2" class="text-start">Pack <br> Size</th>
                                                <th scope="col" rowspan="2">Opening</th>
                                                <th scope="col" class="bg-success" colspan="4">Stock - In</th>
                                                <th scope="col" class="bg-danger" colspan="4">Stock - Out</th>
                                                <th scope="col" rowspan="2">Closing</th>
                                                <th scope="col" rowspan="2">Current</th>
                                            </tr>
                                            <tr class="table-active">
                                                <th scope="col" class="bg-success">Purchase</th>
                                                <th scope="col" class="bg-success">Sale Ret</th>
                                                <th scope="col" class="bg-success">Adj.</th>
                                                <th scope="col" class="bg-success">Total In</th>
                                                <th scope="col" class="bg-danger">Sales</th>
                                                <th scope="col" class="bg-danger">Obsolete</th>
                                                <th scope="col" class="bg-danger">Adj.</th>
                                                <th scope="col" class="bg-danger">Total Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php
                                                $totalOpeningQty = 0;
                                                $totalOpeningLoose = 0;

                                                $totalPurchaseQty = 0;
                                                $totalPurchaseLoose = 0;

                                                $totalSaleRetQty = 0;
                                                $totalSaleRetLoose = 0;

                                                $totalInAdjQty = 0;
                                                $totalInAdjLoose = 0;

                                                $totalInQty = 0;
                                                $totalInLoose = 0;

                                                $totalSaleQty = 0;
                                                $totalSaleLoose = 0;

                                                $totalObsoleteQty = 0;
                                                $totalObsoleteLoose = 0;

                                                $totalOutAdjQty = 0;
                                                $totalOutAdjLoose = 0;

                                                $totalOutQty = 0;
                                                $totalOutLoose = 0;

                                                $totalClosingQty = 0;
                                                $totalClosingLoose = 0;

                                                $totalCurrentQty = 0;
                                                $totalCurrentLoose = 0;
                                            @endphp
                                           
                                        @foreach ($products as $key => $product)
                                        @if ($product->opening_stock > 0 || $product->stock_in > 0 || $product->stock_out > 0 || $product->closing_stock > 0)

                                        @php

                                            $opening = packInfoWithOutName($product->units[0]->value, $product->opening_stock);
                                            $purchase = packInfoWithOutName($product->units[0]->value, $product->purchased);
                                            $saleRet = packInfoWithOutName($product->units[0]->value, $product->returned);
                                            $inAdj = packInfoWithOutName($product->units[0]->value, $product->stock_adjustment_in);
                                            $in = packInfoWithOutName($product->units[0]->value, $product->total_stock_in);
                                            $sale = packInfoWithOutName($product->units[0]->value, $product->sales);
                                            $obsolete = packInfoWithOutName($product->units[0]->value, $product->obsolete);
                                            $outAdj = packInfoWithOutName($product->units[0]->value, $product->stock_adjustment_out);
                                            $out = packInfoWithOutName($product->units[0]->value, $product->total_stock_out);
                                            $closing = packInfoWithOutName($product->units[0]->value, $product->closing_stock);
                                            $current = packInfoWithOutName($product->units[0]->value, $product->current_stock);


                                            // Handle opening
                                            [$opeingQty, $opeingLoose] = explode(",", $opening);
                                            $totalOpeningQty += (int) $opeingQty;
                                            $totalOpeningLoose += (int) $opeingLoose;

                                            // Handle purchase
                                            [$purchaseQty, $purchaseLoose] = explode(",", $purchase);
                                            $totalPurchaseQty += (int) $purchaseQty;
                                            $totalPurchaseLoose += (int) $purchaseLoose;

                                            // Handle sale return
                                            [$saleRetQty, $saleRetLoose] = explode(",", $saleRet);
                                            $totalSaleRetQty += (int) $saleRetQty;
                                            $totalSaleRetLoose += (int) $saleRetLoose;

                                            // Handle in adjustment
                                            [$inAdjQty, $inAdjLoose] = explode(",", $inAdj);
                                            $totalInAdjQty += (int) $inAdjQty;
                                            $totalInAdjLoose += (int) $inAdjLoose;

                                            // Handle in
                                            [$inQty, $inLoose] = explode(",", $in);
                                            $totalInQty += (int) $inQty;
                                            $totalInLoose += (int) $inLoose;

                                            // Handle sale
                                            [$saleQty, $saleLoose] = explode(",", $sale);
                                            $totalSaleQty += (int) $saleQty;
                                            $totalSaleLoose += (int) $saleLoose;
                                            

                                            // Handle obsolete
                                            [$obsoleteQty, $obsoleteLoose] = explode(",", $obsolete);
                                            $totalObsoleteQty += (int) $obsoleteQty;
                                            $totalObsoleteLoose += (int) $obsoleteLoose;

                                            // Handle out adjustment
                                            [$outAdjQty, $outAdjLoose] = explode(",", $outAdj);
                                            $totalOutAdjQty += (int) $outAdjQty;
                                            $totalOutAdjLoose += (int) $outAdjLoose;

                                            // Handle out
                                            [$outQty, $outLoose] = explode(",", $out);
                                            $totalOutQty += (int) $outQty;
                                            $totalOutLoose += (int) $outLoose;

                                            // Handle closing
                                            [$closingQty, $closingLoose] = explode(",", $closing);
                                            $totalClosingQty += (int) $closingQty;
                                            $totalClosingLoose += (int) $closingLoose;

                                            // Handle current
                                            [$currentQty, $currentLoose] = explode(",", $current);
                                            $totalCurrentQty += (int) $currentQty;
                                            $totalCurrentLoose += (int) $currentLoose;

                                        @endphp
                                            <tr>
                                                <td class="p-1 m-0">{{ $key+1}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->name}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->units[0]->unit_name}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->units[0]->value}}</td>
                                                <td class="text-end p-1 m-0">{{ $opening }}</td>

                                                <td class="text-end p-1 m-0 text-success">{{ $purchase }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ $saleRet }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ $inAdj }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ $in }}</td>

                                                <td class="text-end p-1 m-0 text-danger">{{ $sale }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ $obsolete }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ $outAdj }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ $out }}</td>

                                                <td class="text-end p-1 m-0">{{ $closing }}</td>
                                                <td class="text-end p-1 m-0">{{ $current }}</td>
                                            </tr>
                                        @endif
                                        @endforeach
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active bg-success bg-opacity-25">
                                            <th colspan="4" class="text-end p-1 m-0">Total</th>
                                            <th class="text-end p-1 m-0">{{ $totalOpeningQty }}, {{ $totalOpeningLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalPurchaseQty }}, {{ $totalPurchaseLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalSaleRetQty }}, {{ $totalSaleRetLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalInAdjQty }}, {{ $totalInAdjLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalInQty }}, {{ $totalInLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalSaleQty }}, {{ $totalSaleLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalObsoleteQty }}, {{ $totalObsoleteLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalOutAdjQty }}, {{ $totalOutAdjLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalOutQty }}, {{ $totalOutLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalClosingQty }}, {{ $totalClosingLoose }}</th>
                                            <th class="text-end p-1 m-0">{{ $totalCurrentQty }}, {{ $totalCurrentLoose }}</th>
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



