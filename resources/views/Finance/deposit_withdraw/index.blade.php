@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Deposit / Withdraws</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Ref #</th>
                            <th>Account</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($trans as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><a href="{{route('viewAttachment', $tran->refID)}}" target="_black">{{ $tran->refID }} <i class="ri-attachment-2"></i></a> </td>
                                    <td>{{ $tran->account->title }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->date)) }}</td>
                                    <td>{{ $tran->notes }}</td>
                                    <td><span
                                            class="badge {{ $tran->type == 'Withdraw' ? 'bg-warning' : 'bg-info' }}">{{ $tran->type }}</span>
                                    </td>
                                    <td>{{ number_format($tran->amount) }}</td>
                                    <td>
                                        <a href="{{ route('deposit_withdraw.delete', $tran->refID) }}"
                                            class="btn btn-danger">Delete</a>
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
                    <h5 class="modal-title" id="myModalLabel">Create Deposit / Withdraw</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('deposit_withdraw.store') }}" enctype="multipart/form-data" method="post">
                    @csrf

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-6">
                                <div class="form-group mt-2">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="Deposit">Deposit</option>
                                        <option value="Withdraw">Withdraw</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="account">Account</label>
                                    <select name="accountID" id="account" required class="selectize">
                                        <option value=""></option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
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
                                    <textarea name="notes" id="notes" cols="30" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <table class="w-100">
                                    <thead>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($currencies as $currency)
                                            <tr>
                                                <td>{{$currency->title}}</td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" data-value="{{$currency->value}}" id="currency_{{$currency->id}}" oninput="updateTotal()" name="currency[]" value="0">
                                                    <input type="hidden" class="form-control" name="currencyID[]" value="{{$currency->id}}">
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td>Total Amount</td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm" readonly id="total" name="total" value="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Attachement</td>
                                            <td>
                                                <input type="file" class="form-control form-control-sm" name="file">
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

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

        function updateTotal() {
            var total = 0;
            $("input[id^='currency_']").each(function() {
                var inputId = $(this).attr('id');
                var inputVal = $(this).val();
                var inputValue = $(this).data('value');
                var value = inputVal * inputValue;
                total += parseFloat(value);
            });
            $("#total").val(total.toFixed(2));
        }
    </script>
@endsection
