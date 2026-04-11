@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Branch Investment Report</h3>
                </div>
                <form action="{{ route('reportBranchInvestmentData') }}" method="get">
                    <div class="card-body">
                        <div class="form-group mt-2">
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="date">On Date</label>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ today() }}">
                        </div>

                        <div class="form-group mt-2">
                            <button class="btn btn-success w-100" id="viewBtn">View Repoert</button>
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
@endsection
