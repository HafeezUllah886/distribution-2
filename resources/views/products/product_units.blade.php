@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Product Units - {{ $product->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('product_units.store') }}"  method="post">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="row mt-2 mb-2">
                            <div class="col-10">
                                <div class="form-group ">
                                    <select name="unit" id="unit" class="form-control">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-primary w-100">Add</button>
                            </div>
                        </div>
                    </form>

                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Unit</th>
                            <th>Value</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($product_units as $key => $product_unit)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $product_unit->unit_name }}</td>
                                    <td>{{ $product_unit->value }}</td>
                                    <td>
                                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#edit_{{ $product_unit->id }}">Edit</a>
                                    </td>
                                </tr>
                                <div id="edit_{{ $product_unit->id }}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Edit Product Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('product_units.update', $product_unit->id) }}" enctype="multipart/form-data" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mt-2">
                            <label for="unit_name">Name</label>
                            <input type="text" name="unit_name" required id="unit_name" value="{{ $product_unit->unit_name }}" class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="value">Value</label>
                            <input type="text" name="value" required id="value" value="{{ $product_unit->value }}" class="form-control">
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
        $(".selectize").selectize();

       
    </script>
@endsection
