@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-12">
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
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th rowspan="2" scope="col">S.NO</th>
                                            <th rowspan="2" scope="col">ORDERBOOKER</th>
                                            <th rowspan="2" scope="col">PRODUCT</th>
                                            <th rowspan="2" scope="col">VENDOR</th>
                                            <th rowspan="2" scope="col">UNIT</th>
                                            <th rowspan="2" scope="col">PACK SIZE</th>
                                            <th rowspan="2" scope="col">PRICE</th>
                                            <th colspan="3" scope="col">TARGET</th>
                                            <th colspan="2" scope="col">DURATION</th>
                                            <th rowspan="2" scope="col">ACHIEVEMENT</th>
                                            <th rowspan="2" scope="col">STATUS</th>
                                        </tr>
                                        <tr class="table-active">
                                            <th scope="col">TARGET QTY</th>
                                            <th scope="col">ACHIEVED QTY</th>
                                            <th scope="col">REMAINING QTY</th>
                                            <th scope="col">FROM</th>
                                            <th scope="col">TO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>{{ $target->orderbooker->name }}</td>
                                            <td>{{ $target->product->name }}</td>
                                            <td>{{ $target->product->vendor->title ?? 'N/A' }}</td>
                                            <td>{{ $target->unit->unit_name }}</td>
                                            <td>{{ $target->unit_value }}</td>
                                            <td>{{ number_format($target->product->price * $target->unit_value, 2) }}</td>
                                            <td>{{ number_format($target->pc / $target->unit_value, 2) }}</td>
                                            <td>{{ number_format($target->sold, 2) }}</td>
                                            <td>{{ number_format($target->remaining, 2) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($target->startDate)) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($target->endDate)) }}</td>
                                            <td>
                                                <span class="fw-bold text-{{ $target->achievement_color }}">
                                                    {{ $target->achievement }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-{{ $target->display_status_color }}">
                                                    {{ $target->display_status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
