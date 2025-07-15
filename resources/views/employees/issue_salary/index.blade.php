@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Month</span>
                            <input type="month" name="month" class="form-control" id="month" value="{{ $month }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Designation</span>
                            <select class="form-control" name="designation" aria-label="Username" aria-describedby="basic-addon1">
                                <option value="All">All</option>
                                @foreach ($designations as $designation)
                                    <option value="{{ $designation }}" @selected($designation == $desig)>{{ $designation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Department</span>
                            <select class="form-control" name="department" aria-label="Username" aria-describedby="basic-addon1">
                                <option value="All">All</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department }}" @selected($department == $dept)>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                       <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Issue Salaries</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issue_salary">
                        Issue Salary
                    </button>
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
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Ref</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Address</th>
                            <th>Salary</th>
                            <th>Date</th>
                            <th>Salary Month</th>
                          
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($salaries as $key => $salary)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $salary->refID }}</td>
                                    <td>{{ $salary->employee->name }}</td>
                                    <td>{{ $salary->employee->designation }}</td>
                                    <td>{{ $salary->employee->department }}</td>
                                    <td>{{ $salary->employee->address }}</td>
                                    <td>{{ number_format($salary->salary) }}</td>
                                    <td>{{ date('d-m-Y', strtotime($salary->date)) }}</td>
                                    <td>{{ date('M Y', strtotime($salary->month)) }}</td>
                                    <td>
                                        <div class="dropdown">
                                               <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                   data-bs-toggle="dropdown" aria-expanded="false">
                                                   <i class="ri-more-fill align-middle"></i>
                                               </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                   <li>
                                                       <a class="dropdown-item" href="{{route('issue_salary.show', $salary->id)}}">
                                                           <i class="ri-eye-fill align-bottom me-2 text-primary"></i>
                                                           View
                                                       </a>
                                                   </li>
                                                   <li>
                                                       <a class="dropdown-item" href="{{route('issue_salary.delete', $salary->refID)}}">
                                                           <i class="ri-delete-bin-fill align-bottom me-2 text-danger"></i>
                                                           Delete
                                                       </a>
                                                   </li>
                                               </ul> 
                                           </div>
                                 </td>
                                
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Default Modals -->

    <div class="modal fade" id="issue_salary" tabindex="-1" aria-labelledby="issue_salaryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="issue_salaryLabel">Issue Salary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('issue_salary.create')}}" method="get">
                    @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee">Employee</label>
                        <select name="employee" id="employee" class="selectize">
                            <option value="">Select Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="month">Month</label>
                        <input type="month" name="month" id="month" class="form-control">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Continue</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js')}}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>

    <script>
        $(".selectize").selectize();

       
    </script>
@endsection
