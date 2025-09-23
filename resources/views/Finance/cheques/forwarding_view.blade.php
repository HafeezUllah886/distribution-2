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
                                        <h1>{{ Auth()->user()->branch->name }}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Cheque Forwarding Receipt</h3>
                                        <p> <span class="text-muted text-uppercase fw-semibold mt-0 m-0 p-0">Receipt Ref # </span><span class="fs-14 m-0 p-0">{{$cheque->forwardedRefID}}</span></p>
                                        <p> <span class="text-muted text-uppercase fw-semibold mt-0 m-0 p-0">Date : </span><span class="fs-14 m-0 p-0">{{date("d M Y" ,strtotime($cheque->forwardedDate))}}</span></p>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                               <table style="width:100%;">
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Forwarded To</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$cheque->forwarded_account->title}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Cheque Amount</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{number_format($cheque->amount,2)}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Amount in Words</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">Rupees {{numberToWords($cheque->amount,2)}} Only</td>
                                </tr> 
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Cheque Number</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$cheque->number}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Bank</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$cheque->bank}}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;" class="p-4 pb-1"><strong>Forwarding Remarks</strong></td>
                                    <td class="border-2 border-top-0 border-start-0 border-end-0 text-center p-4 pb-1">{{$cheque->forwardedNotes}}</td>
                                </tr>
                               
                               </table>

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

