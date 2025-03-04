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
                            <div class="row">
                                <div class="col-4"></div>
                                <div class="col-4 text-center"><h2>RETURNED INVOICE</h2></div>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-1">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Return #</p>
                                        <h5 class="fs-14 mb-0">{{$return->id}}</h5>
                                    </div>
                                    <div class="col-4">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Customer</p>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">M/S :</span> {{$return->customer->title}}</h5>
                                        @if ($return->customerID != 2)
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">Area :</span> {{$return->customer->area->name ?? "NA"}} | <span class="text-muted">Contact :</span> {{$return->customer->contact ?? "NA"}}</h5>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">Type :</span> {{$return->customer->c_type}}</h5>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">Address :</span> {{$return->customer->address ?? "NA"}}</h5>
                                        @endif

                                    </div>
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Order Booker</p>
                                        <h5 class="fs-14 mb-0">{{$return->orderbooker->name}}</h5>
                                    </div>
                                   
                                    <div class="col-2">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0">{{date("d M Y" ,strtotime($return->date))}}</h5>
                                    </div>
                                   
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
                                                <th scope="col" class="text-end">Price</th>
                                                <th scope="col" class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                            @php
                                                $totalQty = 0;
                                                $totalLoose = 0;
                                            @endphp
                                           @foreach ($return->details as $key => $product)
                                           @php
                                           $qty = $product->pc;
                                           $totalQty += $product->qty;
                                           $totalLoose += $product->loose;
                                           $netAmount = $return->details->sum('amount');
                                            @endphp
                                               <tr>
                                                <td class="p-1 m-1">{{$key+1}}</td>
                                                <td class="text-start p-1 m-1">{{$product->product->name}} | {{$product->product->nameurdu}}</td>
                                                <td class="text-start m-1 p-1">{{$product->unit->unit_name}}</td>
                                                <td class="text-end m-1 p-1">{{number_format($product->qty)}}</td>
                                                <td class="text-end m-1 p-1">{{number_format($product->loose)}}</td>
                                                <td class="text-end p-1 m-1">{{number_format($product->price,2)}}</td>
                                                <td class="text-end p-1 m-1">{{number_format($product->amount,2)}}</td>
                                               </tr>
                                           @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-end p-1" colspan="6">Total Amount</th>
                                                <th class="text-end p-1">{{number_format($netAmount,2 )}}</th>
                                            </tr>
                                        </tfoot>
                                    </table><!--end table-->
                                </div>
                            </div>
                            <div class="card-footer">
                                @if ($return->notes != "")
                                <p><strong>Notes: </strong>{{$return->notes}}</p>
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

