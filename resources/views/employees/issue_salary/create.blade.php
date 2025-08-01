@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Issue Salary</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('issue_salary.store') }}" enctype="multipart/form-data" method="post">
                        @csrf

                        <div class="row">
                           <div class="col-6">
                            @include('layout.payment')
                           </div>
                           <div class="col-6">
                            <div class="form-group">
                                <label for="employee">Employee</label>
                                <input type="text" name="employee" id="employee" value="{{ $employee->name }}" readonly class="form-control">
                                <input type="hidden" name="employeeID" value="{{ $employee->id }}" >
                            </div>
                            <div class="col-12 mt-2">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="balance">Balance</label>
                                            <input type="number" name="balance" id="balance" value="{{ $balance }}" readonly class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="salary">Monthly Salary</label>
                                            <input type="number" name="salary" id="salary" value="{{ $employee->salary }}" readonly class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <label for="month">Salary Month</label>
                                <input type="month" name="month" value="{{ $month }}" readonly class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                <label for="date">Date</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                <label for="notes">Notes</label>
                                <textarea name="notes" cols="30" rows="5" class="form-control"></textarea>
                            </div>
                           
                           </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 mt-4">Issue Salary</button>
                            </div>
                        </div>
                       
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Default Modals -->


@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');

        selectAll.addEventListener('change', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        });
    });
</script>

@endsection
