@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Balance Sheet Report</h3>
                </div>
                <div class="card-body">
                    
                    <div class="form-group mt-2">
                        <label for="type">Account Type</label>
                        <select name="type" id="type" class="form-control">
                            <option value="Business">Business</option>
                            <option value="Vendor">Vendor</option>
                            <option value="Customer">Customer</option>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="areaID">Area</label>
                        <select name="areaID" id="areaID" class="form-control">
                           <option value="All">Select Area</option>
                            @foreach ($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="orderbookerID">Order Booker</label>
                        <select name="orderbookerID" id="orderbookerID" class="form-control">
                           <option value="All">Select Order Booker</option>
                            @foreach ($orderbookers as $orderbooker)
                                <option value="{{$orderbooker->id}}">{{$orderbooker->name}}</option>
                            @endforeach
                        </select>
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
                        <label for="from">From</label>
                        <input type="date" name="from" id="from" value="{{firstDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <label for="to">To</label>
                                <input type="date" name="to" id="to" value="{{lastDayOfMonth()}}" class="form-control">
                    </div>
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">View Report</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('page-js')

    <script>
        $("#viewBtn").on("click", function (){
            var from = $("#from").val();
            var to = $("#to").val();
            var type = $("#type").find(':selected').val();
            var branch = $("#branch").find(':selected').val();
            var area = $("#areaID").find(':selected').val();
            var orderbooker = $("#orderbookerID").find(':selected').val();
            var url = "{{ route('reportBalanceSheetData', ['from' => ':from', 'to' => ':to', 'type' => ':type', 'branch' => ':branch', 'area' => ':area', 'orderbooker' => ':orderbooker']) }}"
        .replace(':from', from)
        .replace(':to', to)
        .replace(':type', type)
        .replace(':branch', branch)
        .replace(':area', area)
        .replace(':orderbooker', orderbooker);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
