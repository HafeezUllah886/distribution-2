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
                        <label for="warehouse">Stock Value</label>
                        <select name="warehouse" id="value" class="form-control">
                            <option value="Purchase Wise">Purchase Wise</option>
                            <option value="Sale Wise">Sale Wise</option>
                            <option value="Cost Wise">Cost Wise</option>
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
@section('page-js')

    <script>

        $("#viewBtn").on("click", function(){
            var warehouse = $("#warehouse").find(":selected").val();
            var value = $("#value").find(":selected").val();
            var url = "{{ route('reportWarehouseStockData', ['warehouse' => ':warehouse', 'value' => ':value']) }}"
            .replace(':warehouse', warehouse)
            .replace(':value', value);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
