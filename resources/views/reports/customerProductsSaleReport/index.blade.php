@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Customer Wise Products Sales Report</h3>
                </div>
                <form action="{{ route('reportCustomerProductSalesData') }}" method="get">
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
                        <label for="area">Area</label>
                        <select name="area[]" id="area" class="selectize">
                            <option value='All'>All</option>
                            @foreach ($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="customer">Customer</label>
                        <select name="customer" id="customer" class="selectize">
                            <option value='All'>All</option>
                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">{{$customer->title}}</option>
                            @endforeach
                        </select>
                    </div>
                  
                    <div class="form-group mt-2">
                        <label for="orderbooker">Order Booker</label>
                        <select name="orderbooker[]" id="orderbooker" class="selectize" multiple>
                            @foreach ($orderbookers as $orderbooker)
                                <option value="{{$orderbooker->id}}">{{$orderbooker->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="vendor">Vendor</label>
                        <select name="vendor[]" id="vendor" class="selectize" multiple>
                            @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->title}}</option>
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
        delimiter: ",",
        persist: false,
        
    });

    </script>

<script>
    $(document).ready(function () {
    let customerSelect = $('#customer').selectize();
    let areaSelect = $('#area').selectize();
    let orderbookerSelect = $('#orderbooker').selectize();

    customerSelect[0].selectize.on('change', function(value) {
        if (value) {
            // fetch products
            $.ajax({
                url: '/get-orderbookers-by-customer/' + value,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    let selectize = orderbookerSelect[0].selectize;
                    selectize.clearOptions();
                    selectize.addOption(data);
                    selectize.refreshOptions();
                }
            });
        }
    });
    areaSelect[0].selectize.on('change', function(value) {
        if (value) {
            // fetch products
            $.ajax({
                url: '/get-customers-by-area/' + value,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    let selectize1 = customerSelect[0].selectize;
                    selectize1.clearOptions();
                    selectize1.addOption(data);
                    selectize1.refreshOptions();
                }
            });
        }
    });
});  
</script>
@endsection
