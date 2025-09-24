@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
          
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Consumptions</h3>
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
                            <th>Ref #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Order Booker</th>
                            <th>Invoice</th>
                            <th>Inv Date</th>
                            <th>Inv Amount</th>
                            <th>Consumed Amount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($custonmer_advance as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tran->refID }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->date)) }}</td>
                                    <td>{{ $tran->customer->title }}</td>
                                    <td>{{ $tran->orderbooker->name }}</td>
                                    <td>{{ $tran->invoice->id }}</td>
                                    <td>{{date('d M Y', strtotime($tran->invoice->date)) }}</td>
                                    <td>{{ number_format($tran->invoice->net) }}</td>
                                    <td>{{ number_format($tran->amount) }}</td>
                                  
                                    <td>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{ route('customer_advance.delete_consumption', ['ref' => $tran->refID]) }}">
                                                        <i class="ri-close-fill align-bottom me-2 text-danger"></i>
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
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>

    <script>
        /* $(document).ready(function() {
            
            $('div[id^="forwardModal_"]').on('show.bs.modal', function() {
 
                console.log('Modal opened');
                $(this).find('.selectize').selectize({
                    create: false,
                    sortField: 'text'
                });
            });
        });
        */



    </script>
    
@endsection
