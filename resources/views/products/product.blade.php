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
                            <th>ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>P Price</th>
                            <th>S Price</th>
                            <th>Discount</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->brand->name }}</td>
                                    <td>{{ $item->category->name }}</td>
                                    <td>{{ $item->vendor->title }}</td>
                                    <td>{{ number_format($item->pprice,0) }}</td>
                                    <td>{{ number_format($item->price,0) }}</td>
                                    <td>{{ $item->discount }}</td>
                                    <td>
                                        <div class="dropdown">
                                               <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                   data-bs-toggle="dropdown" aria-expanded="false">
                                                   <i class="ri-more-fill align-middle"></i>
                                               </button>
                                               <ul class="dropdown-menu dropdown-menu-end">
                                                   <li>
                                                       <a class="dropdown-item" href="{{route('product.edit', $item->id)}}">
                                                           <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                           Edit
                                                       </a>
                                                   </li>
                                                   <li>
                                                       <a class="dropdown-item" href="{{route('product_units.show', $item->id)}}">
                                                           <i class="ri-list-check align-bottom me-2 text-muted"></i>
                                                           Units
                                                       </a>
                                                   </li>
                                                   <li>
                                                       <a class="dropdown-item" href="{{route('dc.show', $item->id)}}">
                                                           <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                           Delivery Charges
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
        function refresh()
        {
            var category = $("#category").find(":selected").val();
            var brand = $("#brand").find(":selected").val();
            var url = "{{ url('products/index/')}}/"+category+'/'+brand;
            window.open(url, "_self");
        }
    </script>
@endsection
