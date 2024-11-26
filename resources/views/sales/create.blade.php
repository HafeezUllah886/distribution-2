@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Create Sale </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">

                        <div class="row">
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value="0"></option>
                                        @foreach ($products as $product)
                                            @if ($product->stock > 0)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <form method="get" id="code_form">
                                        <label for="code">Code (Shortcut key : F2)</label>
                                        <input type="number" class="form-control" id="code">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('sale.store') }}" method="post">
                            <div class="row">
                                @csrf
                                <div class="col-12">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <th width="20%">Product</th>
                                            <th class="text-center">Qty</th>
                                            <th width="10%" class="text-center">Unit</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Disc</th>
                                            <th class="text-center">Tax Inc</th>
                                            <th class="text-center">RP</th>
                                            <th class="text-center">GST%</th>
                                            <th class="text-center">GST</th>
                                            <th class="text-center">Bonus</th>
                                            <th></th>
                                        </thead>
                                        <tbody id="products_list"></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="" class="text-end">Total</th>
                                                <th class="text-end" id="totalQty">0.00</th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-end" id="totalDiscount">0.00</th>
                                                <th class="text-end" id="totalTI">0.00</th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-end" id="totalGST">0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="orderbooker">Order Booker</label>
                                        <select name="orderbookerID" id="orderbooker" class="selectize1">
                                            @foreach ($orderbookers as $booker)
                                                <option value="{{ $booker->id }}">{{ $booker->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="discount">Discount</label>
                                        <input type="number" name="discount1" oninput="updateTotal()" id="discount" step="any" value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="fright">Fright(-)</label>
                                        <input type="number" name="fright" id="fright" oninput="updateTotal()" min="0" step="any" value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="fright1">Fright(+)</label>
                                        <input type="number" name="fright1" id="fright1" oninput="updateTotal()" min="0" step="any" value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="whTax">WH Tax</label>
                                        <div class="input-group mb-3">
                                            <input type="number" name="whTax" id="whTax" oninput="updateTotal()" max="50" min="0" step="any" value="0" aria-describedby="basic-addon2" class="form-control">
                                            <span class="input-group-text whTaxValue" id="basic-addon2">0</span>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="net">Net Amount</label>
                                        <input type="number" name="net" id="net" step="any" readonly value="0" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <select name="customerID" id="customer" class="selectize1">
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="account">Account</label>
                                        <select name="accountID" id="account" class="selectize1">
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="status">Payment Status</label>
                                        <select name="status" id="status" class="selectize1">
                                            <option value="paid">Paid</option>
                                            <option value="pending">Pending</option>
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
                                    <button type="submit" class="btn btn-primary w-100">Create Sale</button>
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
                        html += '<td class="no-padding">' + product.name + '</td>';
                        html += '<td class="no-padding"><div class="input-group">  <span class="input-group-text" id="stockValue_'+id+'">'+product.stock +'</span><input type="number" max="'+product.stock+'" name="qty[]" oninput="updateChanges(' + id +')" min="0" required step="any" value="1" class="form-control text-center" id="qty_' + id + '"></div></td>';
                        html += '<td class="no-padding"><select name="unit[]" class="form-control text-center" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                var isSelected = (unit.id == product.unitID);
                                html += '<option data-unit="'+unit.value+'" value="' + unit.id + '" ' + (isSelected ? 'selected' : '') + '>' + unit.name + '</option>';
                            });
                        html += '</select></td>';
                        html += '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.price+'" min="0" class="form-control text-center" id="price_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="discount[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.discount+'" min="0" class="form-control text-center" id="discount_' + id + '"><input type="hidden" step="any" value="'+product.discount+'" id="packdiscount_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="ti[]" required step="any" value="0.00" min="0" class="form-control text-center" id="ti_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="tp[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.tp+'" min="0" class="form-control text-center" id="tp_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="gst[]" oninput="updateChanges(' + id + ')" required step="any" value="18" min="0" class="form-control text-center" id="gst_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="gstValue[]" required step="any" value="0.00" min="0" class="form-control text-center" id="gstValue_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="bonus[]" min="0" required step="any" value="0" oninput="updateChanges(' + id + ')" class="form-control text-center no-padding" id="bonus_' + id + '"></td>';
                        html += '<td> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" id="stock_'+id+'" value="' + product.stock + '">';
                        html += '</tr>';
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
            var stock = $('#stock_' + id).val();
            var discount = $('#discount_' + id).val();
            var bonus = parseFloat($('#bonus_' + id).val());
            var newQty = qty * unit;

            var ti = (newQty * price) - (newQty * discount);
            $("#ti_"+id).val(ti);

            $("#packdiscount_"+id).val(newQty * discount);
            var tp = $("#tp_"+id).val();
            var gst = $("#gst_"+id).val();

            var gstValue = (tp * gst / 100) * (newQty + bonus);

            $("#gstValue_"+id).val(gstValue.toFixed(2));

            var newPrice = price - discount;

            $("#stockValue_"+id).html(stock / unit);
            $("#qty_"+id).attr("max", stock / unit);

            updateTotal();
        }

        function updateTotal() {


            var totalDiscount = 0;
            $("input[id^='packdiscount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalDiscount += parseFloat(inputValue);
            });
            $("#totalDiscount").html(totalDiscount.toFixed(2));

            var totalTI = 0;
            $("input[id^='ti_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalTI += parseFloat(inputValue);
            });
            $("#totalTI").html(totalTI.toFixed(2));

            var totalGST = 0;
            $("input[id^='gstValue_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalGST += parseFloat(inputValue);
            });
            $("#totalGST").html(totalGST.toFixed(2));

            var totalQty = 0;
            $("input[id^='qty_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalQty += parseFloat(inputValue);
            });

            $("#totalQty").html(totalQty.toFixed(2));

            var discount = parseFloat($("#discount").val());
            var fright = parseFloat($("#fright").val());
            var fright1 = parseFloat($("#fright1").val());
            var whTax = parseFloat($("#whTax").val());

            var taxValue = totalTI * whTax / 100;

            $(".whTaxValue").html(taxValue.toFixed(2));

            var net = (totalTI + taxValue + fright1) - (discount + fright);

            $("#net").val(net.toFixed(2));
        }

        function deleteRow(id) {
            existingProducts = $.grep(existingProducts, function(value) {
                return value !== id;
            });
            $('#row_'+id).remove();
            updateTotal();
        }

        $("#code_form").on("submit", function(e)
    {
        e.preventDefault();
        var code = $("#code").val();
        $("#code").val('');
        $.ajax({
                url: "{{ url('product/searchByCode/') }}/" + code,
                method: "GET",
                success: function(response) {
                    if(response == "Not Found")
                    {
                        Toastify({
                        text: "Product Not Found",
                        className: "info",
                        close: true,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "linear-gradient(to right, #FF5733, #E70000)",
                        }
                        }).showToast();
                    }
                    else
                    {
                        getSingleProduct(response);
                    }
                }
            }
        );

    });
    $(".selectize1").selectize();

    </script>
@endsection
