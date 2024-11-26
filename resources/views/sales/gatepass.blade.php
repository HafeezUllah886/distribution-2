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
                                        <div class="card-header border-bottom-dashed p-4">
                                            @include('layout.header')
                                        </div>
                                        <div class="mt-sm-5 mt-4">
                                            <div class="row">
                                                <div class="col-4"></div>
                                                <div class="col-4 text-center"><h2>GATE PASS</h2></div>
                                            </div>

                                                <table style="width:100%;">
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>ID</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$sale->id}}</td>
                                                        <td class="p-4 pb-1"><strong>Date</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{date('d M Y', strtotime($sale->date))}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Name of Factory</strong></td>
                                                        <td colspan="3" class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">JAFFAR & BROTHERS</td>
                                                    </tr>
                                                    <tr>
                                                        @php
                                                        $totalQty = 0;
                                                    @endphp
                                                   @foreach ($sale->details as $key => $product)
                                                   @php
                                                       $totalQty += ($product->qty + $product->bonus) / $product->unitValue;
                                                   @endphp
                                                   @endforeach
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Quantity of Goods</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{number_format($totalQty)}}</td>
                                                        <td class="p-4 pb-1"><strong>C/S</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1"></td>

                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Name of Consignee</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$sale->customer->title}}</td>
                                                        <td  class="p-4 pb-1"><strong>Address</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$sale->customer->address}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Mode of Transport</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1"></td>
                                                        <td  class="p-4 pb-1"><strong>Time of Removal</strong></td>
                                                        <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$sale->customer->address}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Remarks (if any)</strong></td>
                                                        <td colspan="3" class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20%;" class="p-4 pb-1"><strong>Transporter</strong></td>
                                                        <td colspan="3" class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1"></td>
                                                    </tr>
                                                   </table>


                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Product</th>
                                                <th scope="col" class="text-start">Unit</th>
                                                <th scope="col" class="text-end">CTN</th>
                                                <th scope="col" class="text-end">Items Qty</th>

                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                            @php
                                                $totalQty = 0;
                                            @endphp
                                           @foreach ($sale->details as $key => $product)
                                           @php
                                               $totalQty += ($product->qty + $product->bonus) / $product->unitValue;
                                           @endphp
                                               <tr class="border-1 border-dark">
                                                <td class="m-1 p-1 border-1 border-dark">{{$key+1}}</td>
                                                <td class="text-start m-1 p-1 border-1 border-dark">{{$product->product->name}}</td>
                                                <td class="text-start m-1 p-1 border-1 border-dark">{{$product->unit->name}}</td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">{{number_format($product->qty / $product->unitValue)}}</td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">{{number_format($product->qty + $product->bonus)}}</td>
                                               </tr>
                                           @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-1 border-dark">
                                                <th colspan="3" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($totalQty)}}</th>
                                                <th class="text-end">{{number_format($sale->details->sum('qty') + $sale->details->sum('bonus'))}}</th>
                                            </tr>
                                        </tfoot>
                                    </table><!--end table-->
                                </div>
                            </div>
                            <table>
                                <tr>
                                    <td class="p-4 pb-1"><strong>Authorized Sign: ____________________</strong></td>

                                </tr>
                               </table>
                            </table>
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

@section('page-css')
<link href='https://fonts.googleapis.com/css?family=Noto Nastaliq Urdu' rel='stylesheet'>
<style>
    .urdu {
        font-family: 'Noto Nastaliq Urdu';font-size: 12px;
    }
    </style>
@endsection


