@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-11">
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
                                    <h3>Targets Report</h3>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <h5 class="fs-14 mb-0">{{ $branchName }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Status</p>
                                    <h5 class="fs-14 mb-0">{{ $statusName }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Orderbookers</p>
                                    <h5 class="fs-14 mb-0">{{ $orderbookerNames }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span>{{ date('d M Y') }}</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-active">
                                            <th rowspan="2" style="width: 50px;">S.NO</th>
                                            <th rowspan="2" class="text-start">ORDERBOOKER</th>
                                            <th rowspan="2" class="text-start">PRODUCT</th>
                                            <th rowspan="2" class="text-start">VENDOR</th>
                                            <th rowspan="2">UNIT</th>
                                            <th rowspan="2">PACK SIZE</th>
                                            <th rowspan="2">PRICE</th>
                                            <th colspan="3">TARGET</th>
                                            <th colspan="2">DURATION</th>
                                            <th rowspan="2">ACHIEVEMENT</th>
                                            <th rowspan="2">STATUS</th>
                                        </tr>
                                        <tr class="table-active">
                                            <th>TARGET QTY</th>
                                            <th>ACHIEVED QTY</th>
                                            <th>REMAINING QTY</th>
                                            <th>FROM</th>
                                            <th>TO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($targets as $key => $item)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="text-start">{{ $item->orderbooker->name }}</td>
                                                <td class="text-start">{{ $item->product->name }}</td>
                                                <td class="text-start">{{ $item->product->vendor->title }}</td>
                                                <td>{{ $item->unit->unit_name }}</td>
                                                <td>{{ number_format($item->pack_size, 0) }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ number_format($item->target_qty, 2) }}</td>
                                                <td>{{ number_format($item->achieved_qty, 2) }}</td>
                                                <td>{{ number_format($item->remaining_qty, 2) }}</td>
                                                <td>{{ date('d/m/Y', strtotime($item->startDate)) }}</td>
                                                <td>{{ date('d/m/Y', strtotime($item->endDate)) }}</td>
                                                <td>
                                                    <span
                                                        class="fw-bold text-{{ $item->ach_color }}">{{ $item->ach_label }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="fw-bold text-{{ $item->campain_status == 'ACTIVE' ? 'success' : 'danger' }}">{{ $item->campain_status }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
