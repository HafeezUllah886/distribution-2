@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Receive Bulk Payments</h3>
                </div>
                <form action="{{ route('bulk_payment.create') }}" method="get">
                    <div class="card-body">
                   
                        <div class="form-group mt-2">
                            <label for="customer">Customer</label>
                            <select name="customerID" id="customer" class="selectize1">
                                @foreach ($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="orderbooker">Order Booker</label>
                            <select name="orderbookerID" id="orderbooker" class="selectize1">
                                @foreach ($orderBookers as $orderbooker)
                                    <option value="{{$orderbooker->id}}">{{$orderbooker->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-success w-100" id="viewBtn">Proceed</button>
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
        $(".selectize1").selectize();
        </script>
@endsection
