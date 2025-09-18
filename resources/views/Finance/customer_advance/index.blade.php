@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="from" value="{{$from}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="to" value="{{$to}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Orderbooker</span>
                            <select name="orderbookerID" id="orderbookerID" class="form-control">
                                <option value="">All</option>
                                @foreach ($orderbookers as $booker)
                                    <option value="{{$booker->id}}" @selected($booker->id == $orderbooker)>{{$booker->name}}</option>
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
                    <h3>Customer Advance Payments</h3>
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
                            <th>Area</th>
                            <th>Order Booker</th>
                            <th>Notes</th>
                            <th>Amount</th>
                            <th>Consumed</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($advances as $key => $tran)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tran->refID }}</td>
                                    <td>{{ date('d M Y', strtotime($tran->date)) }}</td>
                                    <td>{{ $tran->customer->title }}</td>
                                    <td>{{ $tran->customer->area->name }}</td>
                                    <td>{{ $tran->orderbooker->name }}</td>
                                    <td>{{ $tran->notes }}</td>
                                    <td>{{ number_format($tran->amount) }}</td>
                                    <td>{{ number_format($tran->consumed) }}</td>
                                    <td>{{ number_format($tran->balance) }}</td>
                                    <td>
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">

                                                <li>
                                                    <a class="dropdown-item" href="{{ route('customer_advance.pay', ['id' => $tran->id]) }}"><i class="ri-check-fill align-bottom me-2 text-muted"></i>
                                                        Pay Bills
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{ route('customer_advance.delete', ['id' => $tran->id]) }}">
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


        $(document).ready(function() {
            $(".selectize").selectize();
            $("#forwardForm").submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = "{{ route('cheques.forward') }}";
               
                var selectize = $("#account").find(":selected").val();
                var id = $("#cheque_id").val();
                console.log(selectize);
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        id: id,
                        account: selectize
                    },
                    success: function(response) {
                        if(response.success) {
                            form.trigger("reset");
                            $("#forwardModal").modal("hide");
                          alert(response.message);
                          location.reload();
                        } else {
                          alert(response.message);
                        }
                    },
                    error: function(response) {
                        alert(response.responseJSON.message);
                    }
                });
            });
        });

    </script>
    
@endsection
