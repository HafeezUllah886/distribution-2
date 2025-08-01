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
                                        <h3>Profit / Loss Report</h3>
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
                                                <th scope="col">Product</th>
                                                <th scope="col">Unit</th>
                                                <th scope="col">Pack</th>
                                                <th scope="col" class="text-end">Avg Purchase Rate</th>
                                                <th scope="col" class="text-end">Avg Sale Price</th>
                                                <th scope="col" class="text-end">Sold Qty</th>
                                                <th scope="col" class="text-end">Profit / Unit</th>
                                                <th scope="col" class="text-end">Profit</th>
                                                <th scope="col" class="text-end">Stock</th>
                                                <th scope="col" class="text-end">Stock Value</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            @php
                                                $total = 0;
                                                $totalPR = 0;
                                                $totalSP = 0;
                                                $totalS = 0;
                                                $totalStockQty = 0;
                                                $totalStockLoose = 0;
                                                $totalValue = 0;
                                                $totalPPU = 0;
                                            @endphp
                                        @foreach ($data as $key => $item)
                                        @if ($item['profit'] != 0)
                                            
                                        
                                        @php
                                            $total += $item['profit'];
                                            $totalPR += $item['purchaseRate'];
                                            $totalSP += $item['saleRate'];
                                            $totalS += $item['sold'];
                                            

                                            $totalValue += $item['stockValue'];
                                            $totalPPU += $item['ppu'];

                                            $stock = packInfoWithOutName($item['unit_value'], $item['stock']);
                                            [$stockQty, $stockLoose] = explode(',', $stock);
                                            $totalStockQty += (int) $stockQty;
                                            $totalStockLoose += (int) $stockLoose;
                                        @endphp
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td class="text-start">{{ $item['name'] }}</td>
                                                <td class="text-start">{{ $item['unit'] }}</td>
                                                <td class="text-start">{{ $item['unit_value'] }}</td>
                                                <td class="text-end">{{ number_format($item['purchaseRate'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['saleRate'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['sold'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['ppu'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['profit'],2) }}</td>
                                                <td class="text-end">{{ packInfoWithOutName($item['unit_value'], $item['stock']) }}</td>
                                                <td class="text-end">{{ number_format($item['stockValue'],2) }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end">Total</th>
                                                <th class="text-end">{{number_format($totalPR, 2)}}</th>
                                                <th class="text-end">{{number_format($totalSP, 2)}}</th>
                                                <th class="text-end">{{number_format($totalS, 2)}}</th>
                                                <th class="text-end">{{number_format($totalPPU, 2)}}</th>
                                                <th class="text-end">{{number_format($total, 2)}}</th>
                                                <th class="text-end">{{$totalStockQty}} , {{$totalStockLoose}}</th>
                                                <th class="text-end">{{number_format($totalValue, 2)}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="8" class="text-end">Expense</th>
                                                <th class="text-end">{{number_format($expenses, 2)}}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="8" class="text-end">Net Profit</th>
                                                <th class="text-end">{{number_format($total - $expenses, 2)}}</th>
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



