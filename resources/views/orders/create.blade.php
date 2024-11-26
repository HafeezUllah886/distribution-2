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
                                    <h3> Create Order </h3>
                                </div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()"
                                        class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="post">
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
                            <div class="col-12" id="products_list">

                            </div>
                            <div class="col-12 w-100 text-center mt-2"><h4>Total : <span id="totalAmount">0</span></h4></div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-2">
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
                                <button type="submit" class="btn btn-primary w-100">Create Order</button>
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
                        var price = product.price;
                        var discount = product.discount;
                        var netPrice = price - discount;
                        html = '<div class="card" id="row_' + id + '">';
                            html += '<input type="hidden" name="id[]" value="' + id + '">';
                            html += '<div class="card-body">';
                                html += '<div class="d-flex justify-content-between mb-2">';
                                    html += '<h5 class="fs-15 mb-2 product">'+product.name+'</h5>';
                                    html += '<button class="btn btn-danger btn-sm" onclick="deleteRow('+id+')">-</button>';
                                html += '</div>';
                                html += '<div class="d-flex mb-4 align-items-center">';
                                    html += '<div class="flex-grow-1">';
                                        html += '<input type="hidden" step="any" id="price_' + id + '" value="'+price+'" name="price[]">';
                                        html += '<input type="hidden" step="any" id="amount_' + id + '" value="'+netPrice+'" name="amount[]">';
                                        html += '<h5 class="text-primary fs-18 mb-0"><span>'+ price +'</span></h5>';
                                    html += '</div>';
                                    html += '<div class="flex-grow-1 text-end"><h5 class="text-primary fs-18 mb-0"><span id="amountText_' + id + '">'+netPrice+'</span></h5></div>';
                                html += '</div>';
                                html += '<div class="row">';
                                    html += '<div class="col-6">';
                                        html += '<div>';
                                            html += '<td class="no-padding"><select name="unit[]" class="form-control text-center" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                                                units.forEach(function(unit) {
                                                var isSelected = (unit.id == product.unitID);
                                                    html += '<option data-unit="'+unit.value+'" value="' + unit.id + '" ' + (isSelected ? 'selected' : '') + '>' + unit.name + '</option>';
                                                });
                                            html += '</select></td>';
                                        html += '</div>';
                                    html += '</div>';
                                    html += '<div class="col-6 text-end">';
                                        html += '<div class="input-step flex-shrink-0 w-100">';
                                            html += '<button type="button" onclick="minus('+id+')">–</button>';
                                            html += '<input type="number" id="qty_' + id + '" class="w-100" oninput="updateChanges('+id+')" step="any" name="qty[]" value="1">';
                                            html += '<button type="button" onclick="plus('+id+')">+</button>';
                                        html += '</div>';
                                    html += '</div>';
                                html += '</div>';
                                html += '<div class="row mt-2">';
                                    html += '<div class="col-6">';
                                        html += '<div>';
                                            html += '<td class="no-padding">';
                                                html += '<input type="number" oninput="updateChanges('+id+')" placeholder="Discount" step="any" class="form-control text-center" id="discount_' + id + '" value="'+discount+'" name="discount[]">';
                                            html += '</td>';
                                        html += '</div>';
                                    html += '</div>';
                                    html += '<div class="col-6">';
                                        html += '<div>';
                                            html += '<td class="no-padding">';
                                                html += '<input type="number" placeholder="Bonus" step="any" class="form-control text-center" id="bonus_' + id + '" name="bonus[]">';
                                            html += '</td>';
                                        html += '</div>';
                                    html += '</div>';
                                html += '</div>';
                            html += '</div>';
                        html += '</div>';
                        $("#products_list").prepend(html);
                        updateChanges(id);
                        existingProducts.push(id);
                    }
                }
            });
        }

        function updateChanges(id) {
            var qty = $('#qty_' + id).val();
            var price = $('#price_' + id).val();
            var unit = $('#unit_' + id).find('option:selected');
            unit = unit.data('unit');
            var discount = $('#discount_' + id).val();
            var newQty = qty * unit;

            var amount = (newQty * price) - (newQty * discount);
            $("#amount_" + id).val(amount);
            $("#amountText_" + id).html(amount);

            updateTotal();
        }

        function updateTotal() {

            var totalAmount = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalAmount += parseFloat(inputValue);
            });
            $("#totalAmount").html(totalAmount.toFixed(2));
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_' + id).remove();
            updateTotal();
        }

        function plus(id)
        {
            var qtyInput = $("#qty_"+id);
            var currentValue = parseInt(qtyInput.val());
            currentValue += 1;
            qtyInput.val(currentValue);
            updateChanges(id);
        }

        function minus(id)
        {
            var qtyInput = $("#qty_"+id);
            var currentValue = parseInt(qtyInput.val());
            if(currentValue > 1)
            {
                currentValue -= 1;
            }
            qtyInput.val(currentValue);
            updateChanges(id);
        }

    </script>
@endsection
