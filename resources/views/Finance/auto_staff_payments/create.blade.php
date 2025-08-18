@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Auto Staff Payments for {{ $staff->name }} Method : {{$method}}</h3>
                </div>
                <form action="{{ route('auto_staff_payments.store') }}" method="post">
                    @csrf
                    <input type="hidden" id="staff" name="staff" value="{{ $staff->id }}">
                    <input type="hidden" id="method" name="method" value="{{ $method }}">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Customer</th>
                                <th>Area</th>
                                <th>Order Booker</th>
                                <th>Date</th>
                                <th>Bank</th>
                                <th>Cheque / Slip No.</th>
                                <th>Cheque Date</th>
                                <th>Amount</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td><input type="checkbox" checked name="transactions[]" value="{{ $transaction->id }}"></td>
                                    <td>{{ $transaction->customer->title }}</td>
                                    <td>{{ $transaction->customer->area->name }}</td>
                                    <td>{{ $transaction->orderbooker->name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($transaction->date)) }}</td>
                                    <td>{{ $transaction->bank }}</td>
                                    <td>{{ $transaction->number }}</td>
                                    <td>{{ date('d-m-Y', strtotime($transaction->cheque_date)) }}</td>
                                    <td>{{ $transaction->amount }}</td>
                                    <td>{{ $transaction->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="card-body @if($method == 'Cheque') d-none @endif">
                       <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="forward">Forward To Filter </label>
                                <select name="forward" id="forward" @readonly($method == 'Cheque') onchange="refresh_page()" class="form-control">
                                    <option value="Vendor" @selected($forward == 'Vendor')>Vendor</option>
                                    <option value="Business" @selected($forward == 'Business')>Business</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="account">Forward To</label>
                                <select name="account" id="account" @readonly($method == 'Cheque') class="selectize">
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account->id}}">{{$account->title}} - {{$account->address}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                       </div>
                    </div>
                  
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" type="submit" id="viewBtn">Create</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')

<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
<script>
    $(".selectize").selectize();

    function refresh_page() {
        var forward = $("#forward").find("option:selected").val();
        var method = $("#method").val();
        var staff = $("#staff").val();
        
        window.location.href = "{{ url('/auto_staff_payments/create') }}?staff="+staff+"&method="+method+"&forward="+forward;
    }

  
    </script>
@endsection
