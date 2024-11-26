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
                                        <h3>Products Summary Report</h3>
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
                                                <th scope="col" class="text-start">Packing</th>
                                                <th scope="col" class="text-end">Avg Purchase Rate</th>
                                                <th scope="col" class="text-end">Avg Sale Price</th>
                                                <th scope="col" class="text-end">Sold Qty</th>
                                                <th scope="col" class="text-end">Sale Amount</th>
                                                <th scope="col" class="text-end">Profit</th>
                                                <th scope="col" class="text-end">Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                           
                                        @foreach ($topProductsArray as $key => $item)
                                        
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td class="text-start">{{ $item['name'] }}</td>
                                                <td class="text-start">{{ $item['unit'] }}</td>
                                                <td class="text-end">{{ number_format($item['pprice'] * $item['unitValue'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['price'] * $item['unitValue'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['sold'] / $item['unitValue'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['amount'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['profit'],2) }}</td>
                                                <td class="text-end">{{ number_format($item['stock'] / $item['unitValue'],2) }}</td>
                                            </tr>
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



