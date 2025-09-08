@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Generate Salary</h3>
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
                    <form action="{{ route('generate_salary.store') }}" method="post">
                        @csrf
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox"  checked id="select-all"></th>
                                    <th>Employee Name</th>
                                    <th>Designation</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td><input type="checkbox" name="employees[]" checked class="form-check-input employee-checkbox" value="{{ $employee->id }}"></td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->designation }}</td>
                                        <td><input type="number" class="form-control" name="salary[{{$employee->id}}]" required min="0" value="{{ $employee->salary }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="month">Month</label>
                                    <input type="month" name="month" required id="month" class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" required id="date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary w-100 mt-4">Generate</button>
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
