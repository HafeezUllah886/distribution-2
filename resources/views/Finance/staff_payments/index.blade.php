@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" name="start" value="{{ $start }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" name="end" value="{{ $end }}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <select name="from" id="from" class="form-control">
                                <option @selected($from == 'All') value="All">All</option>
                                @foreach ($users as $user)
                                    <option @selected($from == $user->id) value="{{ $user->id }}">{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Method</span>
                            <select name="method" id="method" class="form-control">
                                <option @selected($method == 'All') value="All">All</option>
                                <option @selected($method == 'Cash') value="Cash">Cash</option>
                                <option @selected($method == 'Online') value="Online">Online</option>
                                <option @selected($method == 'Cheque') value="Cheque">Cheque</option>
                                <option @selected($method == 'Other') value="Other">Other</option>
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
                            <th>Method</th>
                            <th>Number</th>
                            <th>Bank</th>
                            <th>Cheque Date</th>
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
                                    <td>{{ $tran->method }}</td>
                                    <td>{{ $tran->number }}</td>
                                    <td>{{ $tran->bank }}</td>
                                    <td>{{ $tran->remarks }}</td>
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
                                                    <button class="dropdown-item"
                                                        onclick="newWindow('{{ route('staff_payments.show', $tran->id) }}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="{{ route('staff_payments.delete', $tran->refID) }}">
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('staff_payments.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-4">
                                @include('layout.payment')
                                <div class="form-group mt-2 check_customer d-none">
                                    <label for="customerID">Check From</label>
                                    <select name="customerID" id="customerID" required class="selectize">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-2 check_customer d-none">
                                    <label for="orderbookerID">Order Booker</label>
                                    <select name="orderbookerID" id="orderbookerID" required class="selectize">
                                        @foreach ($orderbookers as $orderbooker)
                                            <option value="{{ $orderbooker->id }}">{{ $orderbooker->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group mt-2">
                                    <label for="fromID">From (Balance: <span id="accountBalance">0</span>) <span
                                            class="text-primary" id="viewBalance">View Balance</span></label>
                                    <select name="fromID" id="fromID" onchange="getBalance()" required
                                        class="selectize">
                                        <option value=""></option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}
                                                ({{ $user->role }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" required id="date"
                                        value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                                <div class="form-group mt-2">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" required id="notes" cols="30" class="form-control" rows="2"></textarea>
                                </div>
                                 <div class="row">
                                     <div class="col-12">
                                        <div class="form-group mt-2">
                                            <label for="category">Expense Category</label>
                                            <select name="category" id="category" onchange="addExpenses(this.value)" class="selectize">
                                                <option value=""></option>
                                                @foreach ($expense_categories as $expense_category)
                                                    <option value="{{ $expense_category->id }}" data-title="{{ $expense_category->name }}"> {{ $expense_category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1 g-0" id="expense_inputs">
                                    
                                </div>
                                <div class="row">
                                    <div class="col-4 text-end" >Total</div>
                                    <div class="col-2" id="expense_total">0</div>
                                    <div class="col-2">Grand Total</div>
                                    <div class="col-2" id="grand_total">0</div>
                                </div>
                               
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

        function getBalance() {
            var id = $("#fromID").find(":selected").val();
            $.ajax({
                url: "{{ url('/userbalance/') }}/" + id,
                method: 'GET',
                success: function(response) {
                    $("#accountBalance").html(response.data);
                    if (response.data > 0) {
                        $("#accountBalance").addClass('text-success');
                        $("#accountBalance").removeClass('text-danger');
                    } else {
                        $("#accountBalance").addClass('text-danger');
                        $("#accountBalance").removeClass('text-success');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        $("#viewBalance").click(function() {
            var user = $("#fromID").find(":selected").val();
            var url = "{{ url('/staff_balance/') }}/" + user;
            window.open(url, "_blank", "width=600,height=800");
        });

        function addExpenses(id) {
    var name = $("#category").find(":selected").text();
    var expenseId = id.toString();
    
    // Check if this category is already added
    var existingInput = $(`input[name='expense_id[]'][value='${expenseId}']`);
    if (existingInput.length > 0) {
        alert('This category has already been added!');
        return;
    }

    var html = `<div class='row mb-1 g-1 expense-row' data-expense-id='${expenseId}'>
        <div class='col-4 d-flex align-items-center'>
            <span>${name}</span>
        </div>
        <div class='col-2 g-1'>
            <input type='number' required name='expense_amount[]' oninput="calculateTotal()" placeholder="Amount" class='form-control form-control-sm no-padding text-center'>
            <input type='hidden' name='expense_id[]' value='${expenseId}'>
        </div>
        <div class='col-5 g-1'>
            <input type='text' required name='expense_note[]' placeholder="Notes" class='form-control form-control-sm no-padding text-center'>
        </div>
        <div class='col-1 g-1 d-flex align-items-center'>
            <button type='button' class='btn btn-sm btn-danger remove-expense'>&times;</button>
        </div>
    </div>`;
    
    $("#expense_inputs").append(html);
    calculateTotal();
}

// Add click handler for remove buttons
$(document).on('click', '.remove-expense', function() {
    $(this).closest('.expense-row').remove();
    calculateTotal();
});


function calculateTotal() {
    var total = 0;
    $('input[name="expense_amount[]"]').each(function() {
        var amount = parseFloat($(this).val()) || 0;
        total += amount;
    });
    $('#expense_total').text(total.toFixed(2));
    var amount = $('#amount').val();
    var g_total = parseInt(total) + parseInt(amount);
    $('#grand_total').text(g_total.toFixed(2));

}
    </script>
@endsection
