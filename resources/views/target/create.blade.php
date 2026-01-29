@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h3> Create Target </h3>
                                </div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()"
                                        class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('targets.store') }}" method="post">
                        @csrf
                        <div class="row">

                            <div class="col-12">
                                <div class="form-group" id="product_div">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="col-12 mt-3">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="40%">Product</th>
                                        <th class="text-center">Qty</th>
                                        <th width="15%" class="text-center">Unit</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="targets_list"></tbody>

                                </table>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="start">Start Date</label>
                                    <input type="date" name="startDate" required id="start"
                                        value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="end">End Date</label>
                                    <input type="date" name="endDate" required id="end" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="orderbooker">Order Booker</label>
                                    <input type="text" name="orderbooker" id="orderbooker" class="form-control"
                                        value="{{ $orderbooker->name }}" readonly>
                                    <input type="hidden" name="orderbookerID" id="orderbooker" class="form-control"
                                        value="{{ $orderbooker->id }}">
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

        var existingProducts = [];

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('getSignleProduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {} else {

                        var id = product.id;
                        var units = product.units;
                        var html = '<tr id="row_' + id + '">';
                        html +=
                            '<td class="no-padding">' + product.name +
                            '</td>';
                        html +=
                            '<td class="no-padding"><input type="number" name="qty[]" min="0" required step="any" value="0" class="form-control text-center no-padding" id="qty_' +
                            id + '"></td>';
                        html +=
                            '<td class="no-padding"><select name="unit[]" class="form-control text-center no-padding" id="unit_' +
                            id + '">';
                        units.forEach(function(unit) {
                            html += '<option data-unit="' + unit.value + '" value="' + unit.id + '">' +
                                unit.unit_name + '</option>';
                        });
                        html += '</select></td>';
                        html +=
                            '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' +
                            id + ')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" id="id_' + id + '" value="' + id + '">';
                        html += '</tr>';
                        $("#targets_list").prepend(html);
                        existingProducts.push(id);
                    }
                }
            });
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
        }
    </script>
@endsection
