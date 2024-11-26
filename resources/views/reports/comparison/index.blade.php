@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Comparison Report</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <select name="customerID" id="customer" class="selectize">
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                            @endforeach
                        </select>
                    </div>
                            <div class="form-group mt-2">
                                <label for="from">Year One</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">From</span>
                                    <input type="date" class="form-control" id="from1" value="{{firstDayOfPreviousYear()}}" aria-label="year_one_from">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control" id="to1" value="{{lastDayOfPreviousYear()}}" aria-label="year_one_to">
                                  </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="from">Year Two</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">From</span>
                                    <input type="date" class="form-control" id="from2" value="{{firstDayOfCurrentYear()}}" aria-label="year_two_from">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control" id="to2" value="{{lastDayOfCurrentYear()}}" aria-label="year_two_to">
                                  </div>
                            </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">View Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
    <style>
        .no-padding {
            padding: 5px 5px !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>

    $(".selectize").selectize();
        $("#viewBtn").on("click", function (){
            var from1 = $("#from1").val();
            var to1 = $("#to1").val();
            var from2 = $("#from2").val();
            var to2 = $("#to2").val();
            var customer = $("#customer").find(":selected").val();
            var url = "{{ route('reportComparisonData', ['from1' => ':from1', 'to1' => ':to1', 'from2' => ':from2', 'to2' => ':to2', 'customer' => ':customer']) }}"
            .replace(':from1', from1)
            .replace(':to1', to1)
            .replace(':from2', from2)
            .replace(':to2', to2)
            .replace(':customer', customer);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
