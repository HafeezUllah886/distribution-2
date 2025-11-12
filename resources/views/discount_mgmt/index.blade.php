@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$from}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$to}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                  
                    <div class="col-md-2">
                       <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Discount Management</h3>
                    <div>
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                    </div>
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
                            <th>Customer</th>
                            <th>Area</th>
                            <th>Product</th>
                            <th>Discount</th>
                            <th>Discount %</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($discounts as $key => $discount)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $discount->customer->title }}</td>
                                    <td>{{ $discount->customer->area->name }}</td>
                                    <td>{{ $discount->product->name }}</td>
                                    <td>{{ $discount->discount }}</td>
                                    <td>{{ $discount->discountp }}</td>
                                    <td>{{ date('d M Y', strtotime($discount->start_date)) }}</td>
                                    <td>{{ date('d M Y', strtotime($discount->end_date)) }}</td>
                                    <td>{{ $discount->status }}</td>
                                    <td>

                                        <a href="{{ route('discount.delete', $discount->id) }}"
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('discount.store') }}" enctype="multipart/form-data" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mt-2">
                                    <label for="area">Area</label>
                                    <select name="area" required id="area" onchange="getCustomers(this.value)" class="form-control">
                                        <option value="">Select Area</option>
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="customer">Customer</label>
                                    <select name="customer" required id="customer" class="selectize">
                                        <option value="">Select Customer</option>
                                    </select>
                                </div>
                                <div class="form-group mt-2">
                                    <label for="product">Product</label>
                                    <select name="product" required id="product" class="selectize">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mt-2">
                                            <label for="flat_discount">Flat Discount</label>
                                            <input type="number" name="flat_discount" value="0" step="any" required id="flat_discount" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mt-2">
                                            <label for="percentage_discount">Percentage Discount</label>
                                            <div class="input-group">
                                                <input type="number" name="percentage_discount" value="0" step="any" required id="percentage_discount" class="form-control">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mt-2">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required id="start_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mt-2">
                                            <label for="end_date">End Date</label>
                                            <input type="date" name="end_date" value="{{ date('Y-m-d') }}" required id="end_date" class="form-control">
                                        </div>
                                    </div>
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

        $('#customer').selectize();
        $('#product').selectize();

        function getCustomers(area)
        {
            var customerSelectize = $('#customer')[0].selectize; // Access the selectize instance
            if (area) {
        // Make an AJAX call to fetch sectors for selected town
        $.ajax({
            url: '/customer_by_area/' + area,
            type: 'GET',
            success: function (data) {
                
                // Clear previous options 
                customerSelectize.clearOptions();

                // Add new options
                customerSelectize.addOption(data); // data should be an array of {value: '', text: ''}
                customerSelectize.refreshOptions(false);
            }
        });
    }
    else
    {
        customerSelectize.clearOptions();
    }
}


   </script>
   
@endsection
