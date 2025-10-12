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
                        <label for="filter">Filter</label>
                        <select name="filter" id="filter" class="form-control">
                            <option value="All">All</option>
                            <option value="Department">Department Wise</option>
                            <option value="Designation">Designation Wise</option>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="value">Value</label>
                        <select name="value" id="value1" class="selectize">
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

        var departments = @json($departments);
        var designations = @json($designations);
        $("#filter").on("change", function (){
           var filter = $("#filter").find(':selected').val();
           if(filter == "Department")
           {
            console.log(departments);
            $("#value1").selectize({
                options: departments,
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                create: false,
            });
           }
           else if(filter == "Designation")
           {
            console.log(designations);
            $("#value1").selectize({
                options: designations,
                valueField: 'id',
                labelField: 'name',
                searchField: 'name',
                maxItems: 1,
                create: false,
            });
           }
        });
        $("#viewBtn").on("click", function (){
            var filter = $("#filter").find(':selected').val();
            var value = $("#value1").find(':selected').val();
            console.log(value);
            var url = "{{ route('reportEmpBalanceSheetData', ['filter' => ':filter', 'value' => ':value']) }}"
        .replace(':filter', filter)
        .replace(':value', value);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection
