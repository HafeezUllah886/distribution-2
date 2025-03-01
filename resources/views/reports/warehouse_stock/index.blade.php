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
                            @if(auth()->user()->role == "Admin")
                            <option value="All">All</option>
                            @endif
                            @foreach ($warehouses as $warehouse)
                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                            @endforeach
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
            var url = "{{ route('reportWarehouseStockData', ['warehouse' => ':warehouse']) }}"
            .replace(':warehouse', warehouse);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
