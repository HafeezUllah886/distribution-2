@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                                <a href="javascript:window.print()" class="btn btn-primary ml-4"><i class="ri-printer-line mr-4"></i> Print</a>
                            </div>
                            <div class="card-header border-bottom-dashed p-4">
                               @include('layout.header')
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12 ">
                            <div class="w-100 text-center"><h2>PURCHASE ORDER</h2></div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-1">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Order #</p>
                                        <h5 class="fs-14 mb-0">{{$order->id}}</h5>
                                    </div>
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Vendor</p>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted"></span> {{$order->vendor->title}}</h5>

                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Vehicle</p>
                                        <h5 class="fs-14 mb-0"><span>{{$order->vehicle}}</h5>
                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Driver Contact</p>
                                        <h5 class="fs-14 mb-0"><span>{{$order->driver_contact}}</h5>
                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Transporter</p>
                                        <h5 class="fs-14 mb-0"><span>{{$order->transporter}}</h5>
                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Bilty</p>
                                        <h5 class="fs-14 mb-0"><span>{{$order->bilty}}</h5>
                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0">{{date("d M Y" ,strtotime($order->date))}}</h5>
                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Status</p>
                                        <h5 class="fs-14 mb-0"><span>{{$order->status}}</h5>
                                    </div>
                                    
                                    <!--end col-->
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
                                                <th scope="col" class="text-start">Unit</th>
                                                <th scope="col" class="text-end">Qty</th>
                                                <th scope="col" class="text-end">Loose</th>
                                                <th scope="col" class="text-end">Bonus</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                            @php
                                                $totalQty = 0;
                                                $totalLoose = 0;
                                                $totalBonus = 0;
                                            @endphp
                                           @foreach ($order->details as $key => $product)
                                           @php
                                           $qty = $product->pc;
                                          
                                           $totalQty += $product->qty;
                                           $totalLoose += $product->loose;
                                           $totalBonus += $product->bonus;
                                            @endphp
                                               <tr>
                                                <td class="p-1 m-1">{{$key+1}}</td>
                                                <td class="text-start p-1 m-1">{{$product->product->name}} | {{$product->product->nameurdu}}</td>
                                                <td class="text-start m-1 p-1">{{$product->unit->unit_name}}</td>
                                                <td class="text-end m-1 p-1">{{number_format($product->qty)}}</td>
                                                <td class="text-end m-1 p-1">{{number_format($product->loose)}}</td>
                                                <td class="text-end m-1 p-1">{{number_format($product->bonus)}}</td>
                                               
                                               </tr>
                                           @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($totalQty)}}</th>
                                                <th class="text-end">{{number_format($totalLoose)}}</th>
                                                <th class="text-end">{{number_format($totalBonus)}}</th>
                                               
                                            </tr> 
                                        </tfoot>
                                    </table><!--end table-->
                                </div>
                                <div class="w-100 text-center"><h2>RECEIVING STATUS</h2></div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table">
                                            <thead>
                                                <th class="m-1 p-1">#</th>
                                                <th class="m-1 p-1">Product</th>
                                                <th class="m-1 p-1">Total Order Qty</th>
                                                <th class="m-1 p-1">Received Qty</th>
                                                <th class="m-1 p-1">Remainaing Qty</th>
                                            </thead>
                                            <tbody>
                                               
                                                @foreach ($order->details as $key => $product)
                                                   
                                                    <tr>
                                                        <td class="m-1 p-1">{{$key+1}}</td>
                                                        <td class="m-1 p-1">{{$product->product->name}}</td>
                                                        <td class="m-1 p-1">{{ packInfo($product->unit->value, $product->unit->unit_name, $product->pc) }}</td>
                                                        <td class="m-1 p-1">{{ packInfo($product->unit->value, $product->unit->unit_name, $product->delivered()) }}</td>
                                                        <td class="m-1 p-1">{{ packInfo($product->unit->value, $product->unit->unit_name, $product->remaining()) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    @php
                                                        $totalQty = $order->details->sum('qty');
                                                        $totalReceivedQty = $order->delivered_items->sum('qty');
                                                        $totalRemainingQty = $totalQty - $totalReceivedQty;

                                                    @endphp
                                                    <td colspan="2" class="text-end">Total</td>
                                                    <td class="">{{ $order->totalQty() }}</td>
                                                    <td class="">{{ $order->totalReceivedQty() }}</td>
                                                    <td class="">{{ $order->totalPendingQty() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                @if ($order->notes != "")
                                <p><strong>Notes: </strong>{{$order->notes}}</p>
                                @endif
                               {{-- <p class="text-center urdu"><strong>نوٹ: مال آپ کے آرڈر کے مطابق بھیجا جا رہا ہے۔ مال ایکسپائر یا خراب ہونے کی صورت میں واپس نہیں لیا جائے گا۔ دکاندار سیلزمین کے ساتھ کسی قسم کے ذاتی لین دین کا ذمہ دار خود ہوگا۔</strong></p>
 --}}
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

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
<link href='https://fonts.googleapis.com/css?family=Noto Nastaliq Urdu' rel='stylesheet'>
<style>
    .urdu {
        font-family: 'Noto Nastaliq Urdu';font-size: 12px;
    }
    </style>
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js')}}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
@endsection

