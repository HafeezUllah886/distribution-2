@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Orderbooker Wise Customer Balance Report</h3>
                </div>
                <form action="{{route('reportOrderbookerWiseCustomerBalanceData')}}" method="get">
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="customer">Customer</label>
                        <select name="customer" id="customer" class="selectize">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="orderbooker">Order Booker</label>
                        <select name="orderbooker" id="orderbooker" class="selectize">
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">View Report</button>
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
       $(document).ready(function () {
        let customerSelect = $('#customer').selectize();
        let orderbookerSelect = $('#orderbooker').selectize();

        customerSelect[0].selectize.on('change', function(value) {
            if (value) {
                // fetch products
                $.ajax({
                    url: '/get-orderbookers-by-customer/' + value,
                    type: 'GET',
                    success: function (data) {
                        let selectize = orderbookerSelect[0].selectize;
                        selectize.clearOptions();
                        selectize.addOption(data);
                        selectize.refreshOptions();
                    }
                });
            }
        });
    });  
    </script>
@endsection
