@extends('layout.app')
@section('content')
    <div class="row">
        
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$start}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$end}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Customer</span>
                            <select name="customerID" id="customerID" class="form-control">
                                <option value="">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{$customer->id}}" @selected($customer->id == $customerID)>{{$customer->title}} - {{$customer->area->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Method</span>
                            <select name="method" id="method" class="form-control">
                                <option value="">All</option>
                                <option value="Cash" @selected($method == 'Cash')>Cash</option>
                                <option value="Cheque" @selected($method == 'Cheque')>Cheque</option>
                                <option value="Online" @selected($method == 'Online')>Online</option>
                                <option value="Other" @selected($method == 'Other')>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                       <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Bulk Payments</h3>
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                  
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
                            <th>Invoice IDs</th>
                            <th>Received By</th>
                            <th>Customer</th>
                            <th>Area</th>
                            <th>Order Booker</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Number</th>
                            <th>Bank</th>
                            <th>Cheque Date</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($payments as $key => $payment)
                               
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $payment->invoiceIDs }}</td>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>{{ $payment->customer->title }}</td>
                                    <td>{{ $payment->customer->area->name }}</td>
                                    <td>{{ $payment->orderbooker->name }}</td>
                                    <td>{{ date('d M Y', strtotime($payment->date)) }}</td>
                                    <td>{{ number_format($payment->amount) }}</td>
                                    <td>{{ $payment->method }}</td>
                                    <td>{{ $payment->number }}</td>
                                    <td>{{ $payment->bank }}</td>
                                    <td>{{ date('d M Y', strtotime($payment->cheque_date)) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('bulk_payment.show', $payment->id)}}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{route('bulk_payment.delete', $payment->refID)}}">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
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
    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create New</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('bulk_payment.create') }}" method="get">
                  @csrf
                         <div class="modal-body">
                                <div class="form-group">
                                       <label for="customerID">Customers</label>
                                       <select name="customerID" id="customerID" class="form-control">
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->title}} - {{$customer->area->name}}</option>
                                        @endforeach
                                       </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="orderbookerID">Order Bookers</label>
                                    <select name="orderbookerID" id="orderbookerID" class="form-control">
                                        @foreach ($orderBookers as $orderbooker)
                                            <option value="{{$orderbooker->id}}">{{$orderbooker->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                         </div>
                         <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Create</button>
                         </div>
                  </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
@endsection
