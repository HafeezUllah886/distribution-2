@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Products</h3>
                    <a href="{{route('product.create')}}" class="btn btn-primary">Create New</a>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="category">Categroy</label>
                                <select id="category"onchange="refresh()" class="form-control">
                                    <option value="all">All</option>
                                    @foreach ($cats as $cat)
                                        <option value="{{$cat->id}}" @selected($cat->id == $s_cat)>{{$cat->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <select id="brand"onchange="refresh()" class="form-control">
                                    <option value="all">All</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}" @selected($brand->id == $s_brand)>{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
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
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>P Price</th>
                            <th>S Price</th>
                            <th>Discount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->brand->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>{{ $item->pprice }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->discount }}</td>
                                    <td>
                                        <a href="{{route('product.edit', $item->id)}}" class="btn btn-info">Edit</a>
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
        function refresh()
        {
            var category = $("#category").find(":selected").val();
            var brand = $("#brand").find(":selected").val();
            var url = "{{ url('products/index/')}}/"+category+'/'+brand;
            window.open(url, "_self");
        }
    </script>
@endsection
