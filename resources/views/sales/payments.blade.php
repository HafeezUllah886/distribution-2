@extends('layout.popups')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header row">
                    <div class="col-6"><h3> Sale Payments </h3></div>
                    <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
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
                            <form action="{{ route('sale_payment.store') }}" enctype="multipart/form-data" method="post">
                                @csrf
                                <input type="hidden" name="salesID" value="{{ $sale->id }}">
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
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary w-100">Save</button>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-6">

                            <table class="table">
                                <thead>
                                    <th>Date</th>
                                    <th>Received By</th>
                                    <th>Notes</th>
                                    <th class="text-end">Amount</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($sale->payments as $payment)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($payment->date)) }}</td>
                                            <td>{{ $payment->user->name }}</td>
                                            <td>{{ $payment->notes }}</td>
                                            <td class="text-end">{{ number_format($payment->amount) }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('salePayment.show', $payment->id) }}"
                                                    class="btn btn-info btn-sm">Print</a>
                                                    @if ($payment->userID == auth()->id())
                                                    <a href="{{ route('salePayment.delete', [$sale->id, $payment->refID]) }}"
                                                        class="btn btn-danger btn-sm">X</a>
                                                    @endif
                                               

                                                </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Bill</th>
                                        <th class="text-end">{{ number_format($sale->net) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Received</th>
                                        <th class="text-end">{{ number_format($sale->payments->sum('amount')) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total Balance</th>
                                        <th class="text-end">{{ number_format($sale->net - $sale->payments->sum('amount')) }}</th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>
    <!-- Default Modals -->
@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')
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
