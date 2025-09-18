@extends('layout.popups')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('bulk_payment.store') }}" enctype="multipart/form-data" id="paymentForm" onsubmit="validateForm(event)" method="post">
                <div class="card-header row">
                    <div class="col-6"><h3> Use Advance Payment </h3></div>
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
                                   <input type="hidden" name="orderbookerID" value="{{$advance->orderbookerID}}">
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
                                            <td><input type="number" id="amount_{{ $invoice->id }}" name="invamount[]" oninput="updateNet()" step="any" class="form-control form-control-sm" value="0" min="0" max="{{ $invoice->due() }}"></td>
                                            <input type="hidden" name="invoiceID[]" value="{{ $invoice->id }}">
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end">Net Amount</td>
                                        <td><input type="number" id="netAmount" name="netAmount" readonly value="0" class="form-control form-control-sm"></td>
                                        <input type="hidden" name="customerID" value="{{ $_GET['customerID'] }}">
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

        function updateNet() {
            var amount = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputVal = $(this).val();
                amount += parseFloat(inputVal);
            });
            $("#netAmount").val(amount.toFixed(2));
        }

        // Update selector to match the actual ID pattern 'currency_'
        $(document).on('input', "input[id^='currency_']", function() {
            divideAmount();
        });
        $("#amount").on("input", function() {
            divideAmount();
        });
        function divideAmount() {
            var amount = parseFloat($("#amount").val());
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputVal = $(this).val();
                var inpputMax = $(this).attr('max');
                if(amount > inpputMax) {
                    $(this).val(parseFloat(inpputMax).toFixed(2));
                    amount -= inpputMax;
                }
                else
                {
                    $(this).val(parseFloat(amount).toFixed(2));
                    amount = 0;
                }
            });
            updateNet();
          
        }
    </script>
@endsection
