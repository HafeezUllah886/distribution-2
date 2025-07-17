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
                                        <h3>Sales Report</h3>
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
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Areas</p>
                                        <h5 class="fs-14 mb-0">{{ $area }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Orderbookers</p>
                                        <h5 class="fs-14 mb-0">{{ $orderbookers }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Customers</p>
                                        <h5 class="fs-14 mb-0">{{ $customers }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                        <h5 class="fs-14 mb-0">{{ $branch }}</h5>
                                    </div>
                                    <!--end col-->
                                    <!--end col-->
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
                                                <th scope="col" class="text-start">Branch</th>
                                                <th scope="col" class="text-start">Customer Name</th>
                                                <th scope="col" class="text-start">Order Booker</th>
                                                <th scope="col" class="text-start">Supplyman</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Qty</th>
                                                <th scope="col">Disc</th>
                                                <th scope="col">Fright</th>
                                                <th scope="col">Labor</th>
                                                <th scope="col">Claim</th>
                                                <th scope="col">Amount</th>
                                                <th scope="col">Paid</th>
                                                <th scope="col">Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalAmount = 0;
                                               $totalPaid = 0;
                                                $totalDue = 0;

                                                $netQty = 0;
                                                $netLoose = 0;
                                                $netFright = 0;
                                                $netClaim = 0;
                                                $netLabor = 0;
                                                $netDiscount = 0;
                                            @endphp
                                        @foreach ($sales as $key => $item)
                                            @php
                                                $amount = $item->net;
                                                $paid = $item->paid();
                                                $due = $item->due();

                                                $totalAmount += $amount;
                                               $totalPaid += $paid;
                                                $totalDue += $due;
                                                $totalFright = 0;
                                                $totalClaim = 0;
                                                $totalLabor = 0;
                                                $netQty += $item->details->sum('qty');
                                                $netLoose += $item->details->sum('loose');
                                                $netDiscount += $item->details->sum('discount') + $item->details->sum('discountValue');
                                                
                                                @endphp
                                                 @foreach ($item->details as $key => $product)
                                                 @php
                                                 $qty = $product->pc;
                                                 $fright = $product->fright * $qty;
                                                 $claim = $product->claim * $qty;
                                                 $labor = $product->labor * $qty;
                                                 $totalFright += $fright;

                                                 $totalClaim += $claim;
                                                 $totalLabor += $labor;
                                                 
                                                 
                                                
                                                 @endphp
                                             @endforeach
                                             @php
                                                 $netFright += $totalFright;
                                                 $netClaim += $totalClaim;
                                                 $netLabor += $totalLabor;
                                             @endphp
                                            <tr>
                                                <td>{{ $item->id}}</td>
                                                <td class="text-start">{{ $item->branch->name}}</td>
                                                <td class="text-start">{{ $item->customer->title }}</td>
                                                <td class="text-start">{{ $item->orderbooker->name }}</td>
                                                <td class="text-start">{{ $item->supplyman->title }}</td>
                                                <td>{{ date("d M Y", strtotime($item->date))}}</td>
                                                <td class="text-end">{{ number_format($item->details->sum('qty'), 0) }}, {{ $item->details->sum('loose') }}</td>
                                                <td class="text-end">{{ number_format($item->details->sum('discount') + $item->details->sum('discountValue'), 0) }}</td>
                                                <td class="text-end">{{ number_format($totalFright, 0) }}</td>
                                                <td class="text-end">{{ number_format($totalLabor, 0) }}</td>
                                                <td class="text-end">{{ number_format($totalClaim, 0) }}</td>
                                                <td class="text-end">{{ number_format($item->net, 0) }}</td>
                                               <td class="text-end">{{ number_format($paid, 0) }}</td>
                                                <td class="text-end">{{ number_format($due, 0) }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                                <th colspan="6" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($netQty)}}, {{ number_format($netLoose) }}</th>
                                                <th class="text-end">{{number_format($netDiscount, 0)}}</th>
                                                <th class="text-end">{{number_format($netFright, 0)}}</th>
                                                <th class="text-end">{{number_format($netLabor, 0)}}</th>
                                                <th class="text-end">{{number_format($netClaim, 0)}}</th>
                                               
                                                <th class="text-end">{{number_format($sales->sum('net'), 0)}}</th>
                                               <th class="text-end">{{number_format($totalPaid, 0)}}</th>
                                                <th class="text-end">{{number_format($totalDue, 0)}}</th>
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



