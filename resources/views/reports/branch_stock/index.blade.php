@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Branch Stock Report</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="branch">Branch</label>
                        <select name="branch" id="branch" class="form-control">
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}">{{$branch->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="warehouse">Stock Value</label>
                        <select name="warehouse" id="value" class="form-control">
                            @if (auth()->user()->role == 'Admin' || auth()->user()->role == 'Branch Admin')
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
@section('page-js')

    <script>

        $("#viewBtn").on("click", function(){
            var branch = $("#branch").find(":selected").val();
            var value = $("#value").find(":selected").val();
            var url = "{{ route('reportBranchStockData', ['branch' => ':branch', 'value' => ':value']) }}"
            .replace(':branch', branch)
            .replace(':value', value);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
