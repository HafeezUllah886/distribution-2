@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                            <a href="javascript:window.print()" class="btn btn-success ml-4"><i
                                    class="ri-printer-line mr-4"></i> Print</a>
                        </div>
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h1>{{ Auth()->user()->branch->name }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Sales Target</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                        <!--end card-header-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Order Booker</p>
                                    <h5 class="fs-14 mb-0">{{ $target->orderbooker->name }}</h5>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Dates</p>
                                    <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">From </small><span
                                            id="invoice-date">{{ date('d M Y', strtotime($target->startDate)) }}</span>
                                    </h5>
                                    <h5 class="fs-14 mb-0"><small class="text-muted" id="invoice-time">To
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</small><span
                                            id="invoice-date">{{ date('d M Y', strtotime($target->endDate)) }}</span>
                                    </h5>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Status</p>
                                    <span class="badge bg-{{ $target->campain_color }}">{{ $target->campain }}</span>
                                    <br>
                                    <span class="badge bg-{{ $target->goal_color }}">{{ $target->goal }}</span>
                                    <h5 class="fs-14 mb-0">
                                    </h5>
                                </div>
                                <!--end col-->
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
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
                                            <th scope="col" class="text-start">Product</th>
                                            <th scope="col" class="text-start">Unit</th>
                                            <th scope="col" class="text-end">Target Qty</th>
                                            <th scope="col" class="text-end">Achieved Qty</th>
                                            <th scope="col" class="text-end">Percent</th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-list">
                                        @foreach ($target->details as $key => $detail)
                                            <tr class="border-1 border-dark">
                                                <td class="m-1 p-1 border-1 border-dark">{{ $key + 1 }}</td>

                                                <td class="text-start m-1 p-1 border-1 border-dark">
                                                    {{ $detail->product->name }}
                                                </td>
                                                <td class="text-start m-1 p-1 border-1 border-dark">
                                                    {{ $detail->unit->name }}
                                                </td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">

                                                    {{ number_format($detail->targetQty, 2) }} </td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">
                                                    {{ number_format($detail->sold, 2) }}</td>
                                                <td class="text-end m-1 p-1 border-1 border-dark">
                                                    {{ number_format($detail->per, 2) }} %
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end">
                                                {{ number_format($target->details->sum('targetQty'), 2) }}
                                            </th>
                                            <th class="text-end">{{ number_format($target->details->sum('sold'), 2) }}
                                            </th>
                                            <th class="text-end">{{ number_format($target->totalPer, 2) }}%</th>
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
            font-family: 'Noto Nastaliq Urdu';
            font-size: 12px;
        }
    </style>
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
@endsection
