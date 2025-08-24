@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$start}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$end}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Orderbooker</span>
                            <select name="orderbookerID" id="orderbookerID" class="form-control">
                                <option value="">All</option>
                                @foreach ($orderbookers as $booker)
                                    <option value="{{$booker->id}}" @selected($booker->id == $orderbooker)>{{$booker->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Status</span>
                            <select name="status" id="status1" class="form-control">
                                <option value="">All</option>
                                <option value="cleared" @selected($status == 'cleared')>Cleared</option>
                                <option value="bounced" @selected($status == 'bounced')>Bounced</option>
                                <option value="pending" @selected($status == 'pending')>Pending</option>

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
                    <h3>Cheques</h3>
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
                                    <td>{{ $tran->status }} @if($tran->forwardedTo != null)<br> {{ $tran->forwarded->title }} @endif</td>
                                    <td>
                                        @if($tran->status == 'pending')
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#forwardModal_{{ $tran->id }}"><i class="ri-check-fill align-bottom me-2 text-muted"></i>
                                                        Forwarded To
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('cheques.status', ['id' => $tran->id, 'status' => 'cleared']) }}"><i class="ri-check-fill align-bottom me-2 text-muted"></i>
                                                        Mark as Cleared
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{ route('cheques.status', ['id' => $tran->id, 'status' => 'bounced']) }}">
                                                        <i class="ri-close-fill align-bottom me-2 text-danger"></i>
                                                        Mark as Bounced
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @if($tran->status == 'pending')
                                <div class="modal fade" id="forwardModal_{{ $tran->id }}" tabindex="-1" aria-labelledby="forwardModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="forwardModalLabel">Forward Cheque</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form id="forwardForm" method="get">
                                            <div class="modal-body">
                                            
                                                    <div class="mb-3">
                                                        <label class="form-label">Forward To</label>
                                                        <select name="account" id="account" class="selectize">
                                                            @foreach ($accounts as $account)
                                                                <option value="{{$account->id}}">{{$account->title}} - {{$account->type}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="id" id="cheque_id" value="{{ $tran->id }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Forward</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
          
        </div>
    </div>
    <!-- Default Modals -->

   
@endsection
@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
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


        $(document).ready(function() {
            $(".selectize").selectize();
            $("#forwardForm").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ route('cheques.forward') }}";
               
                var selectize = $("#account").find(":selected").val();
                var id = $("#cheque_id").val();
                console.log(selectize);
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        id: id,
                        account: selectize
                    },
                    success: function(response) {
                        if(response.success) {
                            form.trigger("reset");
                            $("#forwardModal").modal("hide");
                          alert(response.message);
                          location.reload();
                        } else {
                          alert(response.message);
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });
        });

    </script>
    
@endsection
