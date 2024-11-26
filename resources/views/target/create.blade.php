@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Create Target </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('targets.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value="0"></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="20%">Product</th>
                                        <th class="text-center">Qty</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                  
                                </table>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="start">Start</label>
                                    <input type="date" name="startDate" required id="start" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="end">End</label>
                                    <input type="date" name="endDate" required id="end" value="{{ date('Y-m-d') }}"
                                    class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customerID" id="customer" class="selectize1">
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Target</button>
                            </div>
                </div>
            </form>
            </div>

        </div>
        <!--end card-->
    </div>
    <!--end col-->
    </div>
    <!--end row-->
@endsection

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
    <style>
        .no-padding {
            padding: 5px 5px !important;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(".selectize1").selectize();
        $(".selectize").selectize({
            onChange: function(value) {
                if (!value.length) return;
                if (value != 0) {
                    getSingleProduct(value);
                    this.clear();
                    this.focus();
                }

            },
        });
        var units = @json($units);
        var existingProducts = [];

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('sales/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {

                    } else {
                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding" width="70%">' + product.name + '</td>';
                        html += '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' + id +')" min="0.1" required step="any" value="1" class="form-control text-center" id="qty_' + id + '"></td>';
                        html += '<td class="no-padding"><select name="unit[]" class="form-control text-center" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                var isSelected = (unit.id == product.unitID);
                                html += '<option data-unit="'+unit.value+'" value="' + unit.id + '" ' + (isSelected ? 'selected' : '') + '>' + unit.name + '</option>';
                            });
                        html += '</select></td>';
                        html += '<td> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" id="stock_'+id+'" value="' + product.stock + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                      
                        existingProducts.push(id);
                    }
                }
            });
        }
    </script>
@endsection
