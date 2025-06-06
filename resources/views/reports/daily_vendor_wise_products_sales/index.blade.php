@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Vendor Wise Sales Report</h3>
                </div>
                <form action="{{ route('reportDailyVendorWiseProductsSalesData') }}" method="get">
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="from">From</label>
                        <input type="date" name="from" id="from" value="{{firstDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="to">To</label>
                                <input type="date" name="to" id="to" value="{{lastDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="branch">Branch</label>
                        <select name="branch" id="branch" class="form-control">
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" type="submit" id="viewBtn">View Report</button>
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
        plugins: ['remove_button'],
        maxItems: null,
        create: false,
        placeholder: 'Select vendors...'
    });

    </script>
@endsection
