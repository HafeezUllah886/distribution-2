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
                                        <h3>Purchase Vouchar</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12 ">

                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Invoice</p>
                                        <h5 class="fs-14 mb-0"><span class="text-muted">ID: </span>{{$purchase->id}}</h5>
                                        <h5 class="fs-14 mb-0"><span class="text-muted">Inv #: </span>{{$purchase->inv}}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Dates</p>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">Order: </span>{{date("d M Y" ,strtotime($purchase->orderdate))}}</h5>
                                        <h5 class="fs-14 mb-0"> <span class="text-muted">Receiving: </span>{{date("d M Y" ,strtotime($purchase->recdate))}}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Vendor</p>
                                        <h5 class="fs-14 mb-0">{{$purchase->vendor->title}}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Transport</p>
                                        <h5 class="fs-14 mb-0"><span id="text-muted">Bilty #</span>{{$purchase->bilty}}</h5>
                                        <h5 class="fs-14 mb-0"><span id="text-muted">Transporter: </span>{{$purchase->transporter}}</h5>
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
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                                <thead>
                                                    <tr class="table-active">
                                                        <th scope="col" style="width: 50px;">#</th>
                                                        <th scope="col" class="text-start">Product</th>
                                                        <th scope="col" class="text-start">Unit</th>
                                                        <th scope="col" class="text-end">Qty</th>
                                                        <th scope="col" class="text-end">Bonus</th>
                                                        <th scope="col" class="text-end">Price</th>
                                                        <th scope="col" class="text-end">Dis-Val</th>
                                                        <th scope="col" class="text-end">Dis-Per</th>
                                                        <th scope="col" class="text-end">Claim</th>
                                                        <th scope="col" class="text-end">Net Price</th>
                                                        <th scope="col" class="text-end">Fright</th>
                                                        <th scope="col" class="text-end">Labor</th>
                                                        <th scope="col" class="text-end">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="products-list">
                                                    @php
                                                        $totalQty = 0;
                                                        $totalDiscount = 0;
                                                        $totalDiscountValue = 0;
                                                        $totalFright = 0;
                                                        $totalClaim = 0;
                                                        $totalLabor = 0;
                                                    @endphp
                                                   @foreach ($purchase->details as $key => $product)
                                                   @php
                                                   $qty = $product->qty;
                                                   $discount = $product->discount * $qty;
                                                   $discountvalue = $product->discountvalue * $qty;
                                                   $claim = $product->claim * $qty;
                                                   $fright = $product->fright * $qty;
                                                   $labor = $product->labor * $qty;
                                                   $totalQty += $qty;
                                                   $totalDiscount += $discount;
                                                   $totalDiscountValue += $discountvalue;
                                                   $totalClaim += $claim;
                                                   $totalFright += $fright;
                                                   $totalLabor += $labor;
                                                    @endphp
                                                       <tr>
                                                        <td class="p-1 m-1">{{$key+1}}</td>
                                                        <td class="text-start p-1 m-1">{{$product->product->name}}</td>
                                                        <td class="text-start m-1 p-1">{{$product->unit->name}}</td>
                                                        <td class="text-end m-1 p-1">{{number_format($product->qty)}}</td>
                                                        <td class="text-end m-1 p-1">{{number_format($product->bonus)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->price,2)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->discount)}} | {{number_format($discount)}}</td>
                                                        <td class="text-end p-1 m-1">{{$product->discountp}}% | {{number_format($product->discountvalue)}} | {{number_format($discountvalue)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->claim)}} | {{number_format($claim)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->netprice,2)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->fright)}} | {{number_format($fright)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->labor)}} | {{number_format($labor)}}</td>
                                                        <td class="text-end p-1 m-1">{{number_format($product->amount,2)}}</td>
                                                       </tr>
                                                   @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="3" class="text-end">Total</th>
                                                        <th class="text-end">{{number_format($totalQty)}}</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th class="text-end">{{number_format($totalDiscount)}}</th>
                                                        <th class="text-end">{{number_format($totalDiscountValue)}}</th>
                                                        <th class="text-end">{{number_format($totalClaim)}}</th>
                                                        <th></th>
                                                        <th class="text-end">{{number_format($totalFright)}}</th>
                                                        <th class="text-end">{{number_format($totalLabor)}}</th>
                                                        <th class="text-end">{{number_format($purchase->details->sum('amount'), 2)}}</th>
                                                    </tr>
                                                </tfoot>
                                            </table><!--end table-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p><strong>Notes: </strong>{{$purchase->notes}}</p>
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

