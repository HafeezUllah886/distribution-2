@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Create Obsolete Stock </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><a href="{{route('obsolete.index')}}" class="btn btn-danger">Close</a></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('obsolete.store') }}" method="post">
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
                                        <th width="20%">Item</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">loose</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Reason</th>
                                        <th class="text-center">Notes</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-end">Total</th>
                                            <th class="text-end" id="totalAmount">0.00</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="warehouseID">Warehouse</label>
                                    <select name="warehouseID" id="warehouseID" class="form-control">
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Stock Adjustment</button>
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

        var warehouses = @json($warehouses);
        var existingProducts = [];

        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('getSignleProduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {
                    } else {
                        var id = product.id;
                        var units = product.units;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.name + '</td>';
                        html += '<td class="no-padding"><select name="unit[]" class="form-control text-center no-padding" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                html += '<option data-unit="'+unit.value+'" value="' + unit.id + '">' + unit.unit_name + '</option>';
                            });
                        html += '</select></td>';
                        html += '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' + id + ')" min="0" required step="any" value="1" class="form-control text-center no-padding" id="qty_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="loose[]" oninput="updateChanges(' + id + ')" min="0" required step="any" value="0" class="form-control text-center no-padding" id="loose_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.pprice+'" min="1" class="form-control text-center no-padding" id="price_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' + id + '"></td>';
                        html += '<td class="no-padding"><select name="reason[]" class="form-control text-center no-padding" id="reason_' + id + '">';
                            html += '<option value="Expired">Expired</option>';
                            html += '<option value="Damaged">Damaged</option>';
                            html += '<option value="Lost">Lost</option>';
                            html += '<option value="Pilfered">Pilfered</option>';
                            html += '<option value="Others">Others</option>';
                        html += '</select></td>';
                        html += '<td class="no-padding"><input type="text" name="notes[]" class="form-control no-padding" id="notes_' + id + '"></td>';
                        html += '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '</tr>';
                        $("#products_list").prepend(html);
                        existingProducts.push(id);
                        updateChanges(id);
                    }
                }
            });
        }
        function updateChanges(id) {
            var qty = parseFloat($('#qty_' + id).val());
            var loose = parseFloat($('#loose_' + id).val());
            var price = parseFloat($('#price_' + id).val());
            var unit = $('#unit_' + id).find(':selected').data('unit');
            var unit_qty = qty * unit;
            var totalQty = unit_qty + loose;
            var amount = (price) * totalQty;
            $("#amount_"+id).val(amount.toFixed(2));
            updateTotal();
        }

        function updateTotal() {
            var total = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                total += parseFloat(inputValue);
            });

            $("#totalAmount").html(total.toFixed(2));
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_'+id).remove();
            updateTotal();
        }


    </script>
@endsection
