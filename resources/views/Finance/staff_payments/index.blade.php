@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Staff Payment Receiving</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
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
                            <th>From</th>
                            <th>Received By</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($receivings as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tran->refID }}</td>
                                    <td>{{ $tran->staff->name }}</td>
                                    <td>{{ $tran->user->name }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->date)) }}</td>
                                    <td>{{ $tran->notes }}</td>
                                    <td>{{ number_format($tran->amount) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('staff_payments.show', $tran->id)}}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{route('staff_payments.delete', $tran->refID)}}">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                        Delete
                                                    </a>
                                                </li>
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
    <!-- Default Modals -->

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('staff_payments.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6">
                               @include('layout.payment')
                            </div>
                            <div class="col-6">
                                <div class="form-group mt-2">
                                    <label for="fromID">From (Balance: <span id="accountBalance">0</span>)</label>
                            <select name="fromID" id="fromID" onchange="getBalance()" required class="selectize">
                                <option value=""></option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" required id="date" value="{{ date('Y-m-d') }}"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="notes">Notes</label>
                            <textarea name="notes" required id="notes" cols="30" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
        $(".selectize").selectize();

        function getBalance()
        {
            var id = $("#fromID").find(":selected").val();
            $.ajax({
                url: "{{ url('/userbalance/') }}/" + id,
                method: 'GET',
                success: function(response) {
                    $("#accountBalance").html(response.data);
                    if(response.data > 0)
                    {
                        $("#accountBalance").addClass('text-success');
                        $("#accountBalance").removeClass('text-danger');
                    }
                    else
                    {
                        $("#accountBalance").addClass('text-danger');
                        $("#accountBalance").removeClass('text-success');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>

@endsection
