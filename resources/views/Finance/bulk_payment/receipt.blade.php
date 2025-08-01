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
                                        <div class="mt-sm-5 mt-4">
                                          
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Bulk Payment Receipt</h3>
                                        <p> <span class="text-muted text-uppercase fw-semibold mt-0 m-0 p-0">Receipt Ref # </span><span class="fs-14 m-0 p-0">{{$payment->refID}}</span></p>
                                        <p> <span class="text-muted text-uppercase fw-semibold mt-0 m-0 p-0">Date : </span><span class="fs-14 m-0 p-0">{{date("d M Y" ,strtotime($payment->date))}}</span></p>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-9">
                            <div class="card-body p-4">
                               <table style="width:100%;">
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Received with thanks from</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->customer->title}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Receiving Amount</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{number_format($payment->amount,2)}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Amount in Words</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">Rupees {{numberToWords($payment->amount,2)}} Only</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Received by</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->user->name}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Method</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->method}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Number</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->number}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Bank</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->bank}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Remarks</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->remarks}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Invoices</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$payment->invoiceIDs}}</td>
                                </tr>
                               </table>

                               <table style="width:100%;">
                                <tr>
                                    <td style="width80%;" class="p-4 pb-1 text-end" colspan="3"><strong>Previous Balance: </strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{number_format(spotBalanceBefore($payment->customerID, $payment->refID),2)}}</td>
                                </tr>
                                <tr>
                                    <td class="p-4 pb-1"><strong>Deposited By: ____________________</strong></td>
                                    <td class="p-4 pb-1"><strong>Received By: ____________________</strong></td>
                                    <td class="p-4 pb-1 text-end"><strong>Current Balance: </strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{number_format(spotBalance($payment->customerID, $payment->refID),2)}}</td>
                                </tr>
                               </table>


                            </div>
                            <div class="card-footer">

                                <p><strong>Notes: </strong> {{$payment->notes}}</p>


                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-3">
                            <div class="card-body p-4">
                                @if ($payment->method == 'Cash')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Currency</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currencies as $currency)
                                                @if ($currency->qty > 0)
                                                    <tr>
                                                        <td>{{ $currency->title }}</td>
                                                        <td>{{ number_format($currency->qty, 0) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

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

