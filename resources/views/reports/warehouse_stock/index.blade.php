@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Warehouse Stock Report</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="warehouse">Warehouse</label>
                        <select name="warehouse" id="warehouse" class="form-control">
                            @foreach ($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="vendor">Vendors</label>
                        <select name="vendor[]" id="vendor" class="selectize" multiple>
                            @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}">{{$vendor->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="warehouse">Stock Value</label>
                        <select name="warehouse" id="value" class="form-control">
                            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Branch Admin' || auth()->user()->role == 'Accountant')
                                <option value="Purchase Wise">Purchase Wise</option>
                                <option value="Cost Wise">Cost Wise</option>
                            @endif
                            <option value="Sale Wise">Sale Wise</option>
                        </select>
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

        $("#viewBtn").on("click", function(){
            var warehouse = $("#warehouse").find(":selected").val();
            var value = $("#value").find(":selected").val();
            var vendors = $("#vendor").val() || [];
            var vendorsStr = vendors.join(',');
            var url = "{{ route('reportWarehouseStockData', ['warehouse' => ':warehouse', 'value' => ':value', 'vendors' => ':vendors']) }}"
                .replace(':warehouse', warehouse)
                .replace(':value', value)
                .replace(':vendors', vendorsStr);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
