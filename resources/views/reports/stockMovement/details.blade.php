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
                                           
                                        @foreach ($products as $key => $product)
                                        @if ($product->opening_stock > 0 || $product->stock_in > 0 || $product->stock_out > 0 || $product->closing_stock > 0)
                                            <tr>
                                                <td class="p-1 m-0">{{ $key+1}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->name}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->units[0]->unit_name}}</td>
                                                <td class="text-start p-1 m-0">{{ $product->units[0]->value}}</td>
                                                <td class="text-end p-1 m-0">{{ packInfoWithOutName($product->units[0]->value, $product->opening_stock) }}</td>

                                                <td class="text-end p-1 m-0 text-success">{{ packInfoWithOutName($product->units[0]->value, $product->purchased) }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ packInfoWithOutName($product->units[0]->value, $product->returned) }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ packInfoWithOutName($product->units[0]->value, $product->stock_adjustment_in) }}</td>
                                                <td class="text-end p-1 m-0 text-success">{{ packInfoWithOutName($product->units[0]->value, $product->total_stock_in) }}</td>

                                                <td class="text-end p-1 m-0 text-danger">{{ packInfoWithOutName($product->units[0]->value, $product->sales) }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ packInfoWithOutName($product->units[0]->value, $product->obsolete) }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ packInfoWithOutName($product->units[0]->value, $product->stock_adjustment_out) }}</td>
                                                <td class="text-end p-1 m-0 text-danger">{{ packInfoWithOutName($product->units[0]->value, $product->total_stock_out) }}</td>

                                                <td class="text-end p-1 m-0">{{ packInfoWithOutName($product->units[0]->value, $product->closing_stock) }}</td>
                                                <td class="text-end p-1 m-0">{{ packInfoWithOutName($product->units[0]->value, $product->current_stock) }}</td>
                                            </tr>
                                        @endif
                                        @endforeach
                                        
                                    </tbody>
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



