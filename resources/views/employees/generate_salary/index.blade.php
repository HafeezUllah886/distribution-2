@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Generate Salaries</h3>
                    <a href="{{route('generate_salary.create')}}" class="btn btn-primary">Generate New</a>
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
                                                       <a class="dropdown-item" href="{{route('generate_salary.delete', $salary->refID)}}">
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
@endsection

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
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

    <script>
       
    </script>
@endsection
