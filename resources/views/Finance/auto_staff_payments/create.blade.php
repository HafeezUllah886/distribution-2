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
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Customer</th>
                                <th>Order Booker</th>
                                <th>Number</th>
                                <th>Bank</th>
                                <th>Cheque Date</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td><input type="checkbox" checked name="transactions[]" value="{{ $transaction->id }}"></td>
                                    <td>{{ $transaction->customer->title }}</td>
                                    <td>{{ $transaction->orderbooker->name }}</td>
                                    <td>{{ $transaction->number }}</td>
                                    <td>{{ $transaction->bank }}</td>
                                    <td>{{ date('d-m-Y', strtotime($transaction->cheque_date)) }}</td>
                                    <td>{{ $transaction->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="card-body">
                       <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="account">Forward To</label>
                                <select name="account" id="account" class="selectize">
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account->id}}">{{$account->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea name="notes" id="notes" class="form-control"></textarea>
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
    $(".selectize").selectize({
        
    });

    </script>
@endsection
