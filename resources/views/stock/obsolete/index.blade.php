@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$from}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$to}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Reason</span>
                            <select class="form-control" name="reason" aria-label="Username" aria-describedby="basic-addon1">
                                <option value="All">All</option>
                                <option value="Expired" @selected($reason == 'Expired')>Expired</option>
                                <option value="Damaged" @selected($reason == 'Damaged')>Damaged</option>
                                <option value="Lost" @selected($reason == 'Lost')>Lost</option>
                                <option value="Pilfered" @selected($reason == 'Pilfered')>Pilfered</option>
                                <option value="Others" @selected($reason == 'Others')>Others</option>
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
                    <h3>Obsolete Stock</h3>
                    <a href="{{route('obsolete.create')}}" class="btn btn-primary">Create New</a>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Ref #</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Date</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($obsoletes as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->refID }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->warehouse->name }}</td>
                                    <td>{{ date('d M Y', strtotime($item->date)) }}</td>
                                    <td>{{ $item->reason }}</td>
                                    <td>{{ $item->amount }}</td>
                                    <td>{{ $item->notes }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#view_{{$item->id}}">View</button>
                                        <a href="{{ route('obsolete.delete', $item->refID) }}"
                                            class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                                <div id="view_{{$item->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">View Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Product</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->product->name }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Unit</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->unit->unit_name }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Quantity</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->qty }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Loose</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->loose }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Price</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->price }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Amount</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->amount }}</h6>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Warehouse</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->warehouse->name }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Date</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ date('d M Y', strtotime($item->date)) }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Reason</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->reason }}</h6>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <h6>Notes</h6>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6>{{ $item->notes }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                          
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Total</th>
                                <th class="text-end" id="totalAmount">{{number_format($obsoletes->sum('amount'), 2)}}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
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
   
@endsection
