@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                   
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Vendor</span>
                            <select name="vendorID" id="vendorID" class="form-control">
                                <option value="">All</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{$vendor->id}}" @selected($vendor->id == $vendorID)>{{$vendor->title}}</option>
                                @endforeach
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
                    <h3>Products Stock </h3>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Product</th>
                            <th>Stock</th>
                            @if(auth()->user()->role != 'Operator')
                            <th>Stock Value (P)</th>
                            @endif
                            <th>Stock Value (S)</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($products as $key => $product)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{packInfo($product->units[0]->value, $product->units[0]->unit_name , getStock($product->id))}} </td>
                                    @if(auth()->user()->role != 'Operator')
                                    <td>{{number_format(productStockValue($product->id))}} </td>
                                    @endif
                                    <td>{{number_format(productStockValueS($product->id))}} </td>
                                    <td>
                                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#new_{{$product->id}}">
                                                Details
                                        </button>
                                    </td>
                                </tr>
                                <div id="new_{{$product->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">View Stock Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                            </div>
                                            <form method="get" action="{{route('product_stock.show', $product->id)}}" id="form">
                                              @csrf
                                              <input type="hidden" name="productID" value="{{$product->id}}" id="productID">
                                                     <div class="modal-body">
                                                       <div class="form-group">
                                                        <label for="">Select Dates</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text" id="inputGroup-sizing-default">From</span>
                                                            <input type="date" id="from" name="from" value="{{ firstDayOfMonth() }}" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                            <span class="input-group-text" id="inputGroup-sizing-default">To</span>
                                                            <input type="date" id="to" name="to" value="{{ lastDayOfMonth() }}" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                                                        </div>
                                                       </div>
                                                       <div class="form-group mt-2">
                                                        <label for="unit">Unit</label>
                                                        <select name="unit" class="form-control" id="unit">
                                                            @foreach ($product->units as $unit)
                                                                <option value="{{$unit->id}}">{{$unit->unit_name}}</option>
                                                            @endforeach
                                                        </select>
                                                       </div>
                                                       <div class="form-group mt-2">
                                                        <label for="warehouse">Warehouse</label>
                                                        <select name="warehouse" class="form-control" id="warehouse">
                                                            <option value="all">All</option>
                                                            @foreach ($warehouses as $warehouse)
                                                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                                            @endforeach
                                                        </select>
                                                       </div>
                                                     </div>
                                                     <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" id="viewBtn" class="btn btn-primary">View</button>
                                                     </div>
                                              </form>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
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


@endsection
