@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Products</h3>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
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
                            <th>Code</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th>TP</th>
                            <th>P Price</th>
                            <th>WS Price</th>
                            <th>RT Price</th>
                            <th>Discount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->brand->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>{{ $item->unit->name }}</td>
                                    <td>{{ $item->tp }}</td>
                                    <td>{{ $item->pprice }}</td>
                                    <td>{{ $item->wsprice }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->discount }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info " data-bs-toggle="modal"
                                            data-bs-target="#edit_{{ $item->id }}">Edit</button>
                                    </td>
                                </tr>
                                <div id="edit_{{ $item->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Edit - Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('product.update', $item->id) }}" method="post">
                                                @csrf
                                                @method('patch')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="code">Code</label>
                                                        <input type="text" name="code" required
                                                            value="{{ $item->code }}" id="code"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="name">Name</label>
                                                        <input type="text" name="name" required
                                                            value="{{ $item->name }}" id="name"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="catID">Category</label>
                                                       <select name="catID" id="catID" class="form-control">
                                                        @foreach ($cats as $cat)
                                                            <option value="{{$cat->id}}" @selected($cat->id == $item->catID)>{{$cat->name}}</option>
                                                        @endforeach
                                                       </select>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="unit">Unit</label>
                                                        <select name="unitID" id="unit" class="form-control">
                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->id }}"
                                                                    {{ $unit->id == $item->unitID ? 'selected' : '' }}>
                                                                    {{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="tp">TP</label>
                                                        <input type="number" step="any" name="tp" required
                                                            value="{{ $item->tp }}" min="0" id="tp"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="pprice">Purchase Price</label>
                                                        <input type="number" step="any" name="pprice" required
                                                            value="{{ $item->pprice }}" min="0" id="pprice"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="wsprice">Whole Sale Price</label>
                                                        <input type="number" step="any" name="wsprice" required
                                                            value="{{ $item->wsprice }}" min="0" id="wsprice"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="price">Retail Price</label>
                                                        <input type="number" step="any" name="price" required
                                                            value="{{ $item->price }}" min="0" id="price"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="discount">Discount</label>
                                                        <input type="number" step="any" name="discount" required
                                                            value="{{ $item->discount }}" min="0"
                                                            id="discount" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Close</button>
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

    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('product.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" name="code" required id="code" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" required id="name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="brandID">Brand</label>
                                   <select name="brandID" id="brandID" class="form-control">
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="catID">Category</label>
                                   <select name="catID" id="catID" class="form-control">
                                    @foreach ($cats as $cat)
                                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="unit">Unit</label>
                                    <select name="unitID" id="unit" class="form-control">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="tp">TP</label>
                                    <input type="number" name="tp" required min="0" step="any" id="tp"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="pprice">Purchase Price</label>
                                    <input type="number" step="any" name="pprice" required
                                        value="" min="0" id="pprice"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="wsprice">Whole Sale Price</label>
                                    <input type="number" step="any" name="wsprice" required
                                        value="" min="0" id="wsprice"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="price">Retail Price</label>
                                    <input type="number" step="any" name="price" required
                                        value="" min="0" id="price"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="discount">Discount</label>
                                    <input type="number" step="any" name="discount" required
                                        value="0" min="0"
                                        id="discount" class="form-control">
                                </div>
                            </div>
                            
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
