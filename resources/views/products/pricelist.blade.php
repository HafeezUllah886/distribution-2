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
                                        <h3>Price List</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
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
                                                <th scope="col" class="text-start">Product Name</th>
                                                <th scope="col" class="text-start">Packing</th>
                                                <th scope="col" class="text-start">Size</th>
                                                <th scope="col" class="text-start">Price Pack</th>
                                                <th scope="col" class="text-start">Price Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                        @foreach ($categories as $key => $cat)
                                            <tr>
                                               <td colspan="9" class="text-uppercase border-1 border-dark no-padding"><strong>{{$cat->name}}</strong></td>
                                            </tr>
                                            @foreach ($cat->products as $product)
                                            <tr>
                                                <td class="text-start border-1 border-dark no-padding">{{ $product->name}}</td>
                                                <td class="text-start border-1 border-dark no-padding">{{ $product->unit->name }}</td>
                                                <td class="text-start border-1 border-dark no-padding">{{ $product->unit->value }}</td>
                                                <td class="text-start border-1 border-dark no-padding">{{ number_format($product->price * $product->unit->value) }}</td>
                                                <td class="text-start border-1 border-dark no-padding">{{ number_format($product->price) }}</td>
                                            </tr>
                                            @endforeach
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
@section('page-css')
<style>
    .no-padding {
        padding: 5px 5px !important;
    }
</style>

@endsection



