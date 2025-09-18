@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Pay Bills from Advance Payments</h3>
                </div>
                <form action="{{ route('customer_advance.getBills') }}" method="get">
                <div class="card-body">
                    <input type="hidden" name="customerID" value="{{ $customer->id }}">
                    <input type="hidden" name="advanceID" value="{{ $advance->id }}">
                            <div class="form-group mt-2">
                                <label for="customer">Customer</label>
                               <input type="text" name="customer" class="form-control" readonly value="{{ $customer->title }}" id="customer">
                            </div>
                       
                            <div class="form-group mt-2">
                                <label for="orderbooker">Orderbooker</label>
                                <select name="orderbooker" id="orderbooker" class="form-control">
                                    @foreach ($orderbookers as $orderbooker)
                                        <option value="{{$orderbooker->orderbookerID}}">{{$orderbooker->orderbooker->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                  
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" type="submit" id="viewBtn">Get Bills</button>
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
