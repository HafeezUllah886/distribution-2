@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Orders Report</h3>
                </div>
                <form action="{{route('reportOrdersFilter')}}" method="get">
                <div class="card-body">
                    <div class="form-group mt-2">
                        <label for="branch">Branch</label>
                        <select name="branch" id="branch" onchange="reloadPage()" class="form-control">
                            @foreach ($branches as $branche)
                                <option value="{{$branche->id}}" @selected($branche->id == $branch)>{{$branche->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="area">Area</label>
                        <select name="area[]" id="area" class="selectize" multiple>
                            @foreach ($areas as $area)
                                <option value="{{$area->id}}">{{$area->name}}</option>
                            @endforeach
                        </select>
                    </div>
                   
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" id="viewBtn">Filter</button>
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
        placeholder: 'Select Option...'
    });

    function reloadPage() {
     var branch = document.getElementById('branch').value;
     window.location.href = '/reports/sales?branch=' + branch;
    }        
    </script>
@endsection
