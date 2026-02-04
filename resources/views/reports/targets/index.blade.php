@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Targets Report</h3>
                </div>
                <form action="{{ route('reportTargetsData') }}" method="get">
                    <div class="card-body">
                        <div class="form-group mt-2">
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach ($branches as $branche)
                                    <option value="{{ $branche->id }}" @selected($branche->id == $branch)>{{ $branche->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="from">From</label>
                                    <input type="date" name="from" id="from" class="form-control"
                                        value="{{ firstDayOfMonth() }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="to">To</label>
                                    <input type="date" name="to" id="to" class="form-control"
                                        value="{{ now()->toDateString() }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label for="orderbooker">Orderbooker</label>
                            <select name="orderbooker[]" id="orderbooker" class="selectize" multiple>
                                @foreach ($orderbookers as $orderbooker)
                                    <option value="{{ $orderbooker->id }}">{{ $orderbooker->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="status1">Status</label>
                            <select name="status" id="status1" class="form-control">
                                <option value="">All</option>
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                                <option value="Achieved">Achieved</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Not Achieved">Not Achieved</option>
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
    </script>
@endsection
