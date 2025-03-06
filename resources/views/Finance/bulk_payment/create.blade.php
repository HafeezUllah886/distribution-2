@extends('layout.popups')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('sale_payment.store') }}" enctype="multipart/form-data" id="paymentForm" onsubmit="validateForm(event)" method="post">
                <div class="card-header row">
                    <div class="col-6"><h3> Create Invoice Payments </h3></div>
                    <div class="col-6 d-flex flex-row-reverse"><a href="{{route('dashboard')}}" class="btn btn-danger">Close</a></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                                @csrf
                                <div class="modal-body">
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
                                                        <input type="number" class="form-control form-control-sm" data-value="{{$currency->value}}" id="currency_{{$currency->id}}" oninput="updateTotal()" name="qty[]" value="0">
                                                        <input type="hidden" class="form-control" name="currencyID[]" value="{{$currency->id}}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td>Total Amount</td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" readonly id="total" name="amount" value="0">
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
                                    <div class="form-group mt-2">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                            id="date" class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="notes">Notes</label>
                                        <textarea name="notes" class="form-control" id="notes" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                        </div>
                        <div class="col-md-6">

                            <table class="table">
                                <thead>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Amount</th>
                                </thead>
                                <tbody>
                                    @foreach ($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>{{ date('d M Y', strtotime($invoice->date)) }}</td>
                                            <td>{{ number_format($invoice->net, 2) }}</td>
                                            <td>{{ number_format($invoice->paid(), 2) }}</td>
                                            <td>{{ number_format($invoice->due(), 2) }}</td>
                                            <td><input type="number" id="amount_{{ $invoice->id }}" name="amount[]" oninput="updateNet()" class="form-control form-control-sm" value="0" min="0" max="{{ $invoice->due() }}"></td>
                                            <input type="hidden" name="invoiceID[]" value="{{ $invoice->id }}">
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end">Net Amount</td>
                                        <td><input type="number" id="netAmount" value="0" class="form-control form-control-sm"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <button type="submit" class="btn btn-primary w-100">Save</button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>


    </div>
    </div>
    <!-- Default Modals -->
@endsection

@section('page-js')
   
    <script>

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

        function updateNet() {
            var amount = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputVal = $(this).val();
                amount += parseFloat(inputVal);
            });
            $("#netAmount").val(amount.toFixed(2));
        }

        function validateForm(event) {
            event.preventDefault(); // Prevent the default form submission

            // Get the values of total and netAmount
            var total = parseFloat(document.getElementById('total').value);
            var netAmount = parseFloat(document.getElementById('netAmount').value);

            // Check if total equals netAmount
            if (total === netAmount) {
                // Proceed with the form submission
                document.getElementById('paymentForm').submit();
            } else {
                // Display an alert
                alert('Total does not match Net Amount. Please check your values.');
            }
        }
    </script>
@endsection
