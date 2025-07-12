@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Edit Employee</h3>
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
                    <form action="{{ route('employees.update', $employee->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" required id="name" class="form-control" value="{{ $employee->name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fname">Father Name</label>
                                        <input type="text" name="fname" required id="fname" class="form-control" value="{{ $employee->fname }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="designation">Designation</label>
                                        <input type="text" name="designation" required id="designation" class="form-control" value="{{ $employee->designation }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="department">Department</label>
                                        <input type="text" name="department" required id="department" class="form-control" value="{{ $employee->department }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="contact">Contact</label>
                                       <input type="text" name="contact" id="contact" class="form-control" value="{{ $employee->contact }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="address">Address</label>
                                       <input type="text" name="address" id="address" class="form-control" value="{{ $employee->address }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="doe">Date of Enrollment</label>
                                       <input type="date" name="doe" id="doe" required class="form-control" value="{{ $employee->doe }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="salary">Salary</label>
                                       <input type="number" step="any" name="salary" required id="salary" class="form-control" value="{{ $employee->salary }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="limit">Credit Limit</label>
                                       <input type="number" step="any" name="limit" required id="limit" class="form-control" value="{{ $employee->limit }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="status">Status</label>
                                       <select name="status" id="status1" class="form-control">
                                       <option value="Active" {{ $employee->status == 'Active' ? 'selected' : '' }}>Active</option>
                                       <option value="In-active" {{ $employee->status == 'In-active' ? 'selected' : '' }}>In-active</option>
                                       </select>
                                    </div>
                                </div>
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-secondary w-100">Update</button>
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

@endsection
