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
                                        <img src="{{asset('assets/images/logo.png')}}" style="width:250px;">
                                        <div class="mt-sm-5 mt-4">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h6 class="text-muted text-uppercase fw-semibold">Industrial Area, Sirki Road, Quetta</h6>
                                                    <p class="text-muted mb-1" id="address-details">NTN: 2645388-6</p>
                                                    <p class="text-muted mb-0" id="zip-code"><span>0331-8358638 | </span> jaffarqta92@gmail.com</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Customer</p>
                                                    <h5 class="fs-14 mb-0"> <span class="text-muted">M/S :</span> {{$target->customer->title}}</h5>
                                                    <h5 class="fs-14 mb-0"> <span class="text-muted">CNIC :</span> {{$target->customer->cnic ?? "NA"}} | <span class="text-muted">Contact :</span> {{$target->customer->contact ?? "NA"}}</h5>
                                                    <h5 class="fs-14 mb-0"> <span class="text-muted">NTN #</span> {{$target->customer->ntn ?? "NA"}} | <span class="text-muted">STRN #</span> {{$target->customer->strn ?? "NA"}}</h5>
                                                    <h5 class="fs-14 mb-0"> <span class="text-muted">Address :</span> {{$target->customer->address ?? "NA"}}</h5>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Target Report</h3>
                                        <p> <span class="text-muted text-uppercase fw-semibold mt-0 m-0 p-0">Target # </span><span class="fs-14 m-0 p-0">{{$target->id}}</span></p>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
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
                                                <th scope="col" class="text-end">Target Qty</th>
                                                <th scope="col" class="text-end">Achieved Qty</th>
                                                <th scope="col" class="text-end">Percent</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                           @foreach ($target->details as $key => $product)
                                               <tr class="border-1 border-dark">
                                                <td class="m-1 p-1 border-1 border-dark">{{$key+1}}</td>
                                                <td class="text-start m-1 p-1 border-1 border-dark">{{$product->product->name}}</td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">{{number_format($product->qty)}} </td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">{{number_format($product->sold)}}</td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">{{$product->per}} %</td>
                                               </tr>
                                           @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($target->details->sum('qty'))}}</th>
                                                <th class="text-end">{{number_format($target->details->sum('sold'))}}</th>
                                                <th class="text-end">{{number_format($target->totalPer)}}%</th>
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

