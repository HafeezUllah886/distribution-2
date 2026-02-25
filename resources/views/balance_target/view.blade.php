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
                                    <h3>Balance Target</h3>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th>S.NO</th>
                                            <th>ORDERBOOKER</th>
                                            <th>CUSTOMER</th>
                                            <th>START BALANCE</th>
                                            <th>TARGET BALANCE</th>
                                            <th>CLOSING BALANCE</th>
                                            <th>ACHIEVEMENT</th>
                                            <th>START DATE</th>
                                            <th>END DATE</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>{{ $target->orderbooker->name }}</td>
                                            <td>{{ $target->customer->title }}</td>
                                            <td>{{ number_format($target->start_value, 2) }}</td>
                                            <td>{{ number_format($target->target_value, 2) }}</td>
                                            <td>{{ number_format($target->current_balance, 2) }}</td>
                                            <td>{{ number_format($target->totalPer, 2) }}%</td>
                                            <td>{{ date('d-M-Y', strtotime($target->startDate)) }}</td>
                                            <td>{{ date('d-M-Y', strtotime($target->endDate)) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $target->campain_color }}">{{ $item->campain ?? $target->campain }}</span>
                                                <br>
                                                <span
                                                    class="badge bg-{{ $target->goal_color }}">{{ $item->goal ?? $target->goal }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if ($target->notes)
                                <div class="mt-4">
                                    <h5>Notes:</h5>
                                    <p>{{ $target->notes }}</p>
                                </div>
                            @endif
                        </div>
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
