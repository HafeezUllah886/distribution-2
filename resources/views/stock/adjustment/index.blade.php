@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Stock Adjustments</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Ref #</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($adjustments as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->refID }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ date('d M Y', strtotime($item->date)) }}</td>
                                    <td>
                                        <span class="badge {{ $item->type == 'Stock-In' ? 'bg-info' : 'bg-warning' }}">{{ $item->type }}</span>
                                    </td>
                                    <td>{{ number_format($item->qty) }}</td>
                                    <td>{{ $item->unit->name }}</td>
                                    <td>{{ $item->notes }}</td>
                                    <td>
                                        <a href="{{ route('stockAdjustment.delete', $item->refID) }}"
                                            class="btn btn-danger">Delete</a>
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

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Stock Adjustment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('stockAdjustments.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="product">Product</label>
                            <select name="productID" id="product" required class="selectize">
                                <option value=""></option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="unit">Unit</label>
                            <select name="unitID" id="unit" class="form-control">
                                @foreach ($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="qty">Qty</label>
                            <input type="number" name="qty" required id="qty"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="Stock-In">Stock-In</option>
                                <option value="Stock-Out">Stock-Out</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" required id="date" value="{{ date('Y-m-d') }}"
                                class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" cols="30" class="form-control" rows="5"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
        $(".selectize").selectize();
    </script>
@endsection
