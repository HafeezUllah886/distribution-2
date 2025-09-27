@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start"
                                value="{{ $start }}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end"
                                value="{{ $end }}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Orderbooker</span>
                            <select name="orderbookerID" id="orderbookerID" class="form-control">
                                <option value="">All</option>
                                @foreach ($orderbookers as $booker)
                                    <option value="{{ $booker->id }}" @selected($booker->id == $orderbooker)>{{ $booker->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3> {{$status}} Cheques</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Ref #</th>
                            <th>Cheque From</th>
                            <th>Area</th>
                            <th>Order Booker</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Clearing Date</th>
                            <th>Number</th>
                            <th>Bank</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($cheques as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tran->refID }}</td>
                                    <td>{{ $tran->customer->title }}</td>
                                    <td>{{ $tran->customer->area->name }}</td>
                                    <td>{{ $tran->orderbooker->name }}</td>
                                    <td>{{ number_format($tran->amount) }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->created_at)) }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->cheque_date)) }}</td>
                                    <td>{{ $tran->number }}</td>
                                    <td>{{ $tran->bank }}</td>
                                    <td>{{ $tran->notes }}</td>
                                    <td>{{ $tran->forwarded == 'Yes' ? 'Forwarded' : $tran->status }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($tran->forwarded == 'No' && $tran->status == 'pending')
                                                    <li>
                                                        <a class="dropdown-item" onclick="forwardCheque({{ $tran->id }})"><i
                                                                class="ri-arrow-right-circle-line align-bottom me-2 text-muted"></i>
                                                            Forward
                                                        </a>
                                                    </li>
                                                   
                                                @else
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('cheques.forwardView', ['id' => $tran->id]) }}"><i
                                                                class="ri-eye-2-line align-bottom me-2 text-muted"></i>
                                                            View Forwarding
                                                        </a>
                                                    </li>
                                                    @if ($tran->forwarded == 'Yes')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('viewAttachment', $tran->forwardedRefID) }}"><i
                                                                    class="ri-eye-fill align-bottom me-2"></i>
                                                                View Attachment
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item text-danger"
                                                                href="{{ route('cheques.forwardClear', ['ref' => $tran->forwardedRefID]) }}"><i
                                                                    class="ri-loop-left-line align-bottom me-2 text-danger"></i>
                                                                Reset Forwarding
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                                @if ($tran->forwarded == 'No')
                                                    <li>
                                                        <a class="dropdown-item text-danger"
                                                            href="{{ route('cheques.status', ['id' => $tran->id, 'status' => 'bounced']) }}">
                                                            <i class="ri-close-fill align-bottom me-2 text-danger"></i>
                                                            Mark as Bounced
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="forwardModal" class="modal fade"
        tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Forward Cheque
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form
                    action="{{ route('cheques.forwardCreate') }}"
                    enctype="multipart/form-data" method="get">
                    @csrf
                    <input type="hidden" name="id" id="chequeID">
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="areaID">Area</label>
                            <select name="areaID" id="areaID" class="selectize">
                                <option value=""></option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}">
                                        {{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="Business">Business</option>
                                <option value="Vendor">Vendor</option>
                                <option value="Customer">Customer</option>
                                <option value="Supply Man">Supply Man</option>
                                <option value="Unloader">Unloader</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                            class="btn btn-primary">Continue</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
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
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        /* $(document).ready(function() {
                $('div[id^="forwardModal_"]').on('show.bs.modal', function() {
                    console.log('Modal opened');
                    $(this).find('.selectize').selectize({
                        create: false,
                        sortField: 'text'
                    });
                });
            });
            */
        $(".selectize").selectize();

        function forwardCheque(id) {
            $('#chequeID').val(id);
            $('#forwardModal').modal('show');
        }
    </script>
@endsection
