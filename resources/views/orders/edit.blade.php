@extends('layout.popups')
@section('content')
    <script>
        var existingProducts = [];

        @foreach ($order->details as $product)
            @php
                $productID = $product->productID;
            @endphp
            existingProducts.push({{ $productID }});
        @endforeach
    </script>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h3> Edit Order </h3>
                                </div>
                                <div class="col-6 d-flex flex-row-reverse"><a href="{{ route('Branch.orders') }}"
                                        class="btn btn-danger">Close</a></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('Branch.orders.update', $order->id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-10">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-1">
                                <label for="freight_radio">Freight</label>
                                <div class="form-check form-switch form-switch-lg" dir="ltr">
                                    <input type="checkbox" class="form-check-input" onchange="checkCharges()" id="freight_radio" checked="">
                                </div>
                            </div>
                            <div class="col-1">
                                <label for="labor_radio">Labor</label>
                                <div class="form-check form-switch form-switch-lg" dir="ltr">
                                    <input type="checkbox" class="form-check-input" onchange="checkCharges()" id="labor_radio" checked="">
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="10%">Item</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">loose</th>
                                        <th class="text-center">Bonus</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Discount Value</th>
                                        <th class="text-center">Discount %</th>
                                        <th class="text-center">Fright</th>
                                        <th class="text-center">Labor</th>
                                        <th class="text-center">Claim</th>
                                        <th class="text-center">Amount</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($order->details as $product)
                                            @php
                                                $id = $product->productID;
                                                $units = json_decode($product->product->units);
                                            @endphp
                                            <tr id="row_{{ $id }}">
                                                <td class="no-padding">{{ $product->product->name }}</td>
                                                <td class="no-padding"><select name="unit[]"
                                                        class="form-control text-center no-padding"
                                                        onchange="updateChanges({{ $id }})"
                                                        id="unit_{{ $id }}">
                                                        @foreach ($units as $unit)
                                                            <option data-unit="{{ $unit->value }}"
                                                                value="{{ $unit->id }}">{{ $unit->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select></td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="qty[]"
                                                            oninput="updateChanges({{ $id }})" min="0"
                                                            required step="any" value="{{ $product->qty }}"
                                                            class="form-control text-center no-padding"
                                                            id="qty_{{ $id }}">
                                                </td>
                                                <td class="no-padding"><input type="number" name="loose[]"
                                                        oninput="updateChanges({{ $id }})" min="0"
                                                        required step="any" value="{{ $product->loose }}"
                                                        class="form-control text-center no-padding"
                                                        id="loose_{{ $id }}"></td>
                                                <td class="no-padding"><input type="number" name="bonus[]" min="0"
                                                        required oninput="updateChanges({{ $id }})"
                                                        step="any" value="{{ $product->bonus ?? 0 }}"
                                                        class="form-control text-center no-padding"
                                                        id="bonus_{{ $id }}"></td>
                                                <td class="no-padding"><input type="number" name="price[]"
                                                        oninput="updateChanges({ $id })" required step="any"
                                                        value="{{ $product->price }}" min="1"
                                                        class="form-control text-center no-padding"
                                                        id="price_{{ $id }}"></td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="discount[]"
                                                            required step="any" value="{{ $product->discount }}"
                                                            min="0" oninput="updateChanges({{ $id }})"
                                                            class="form-control text-center no-padding"
                                                            id="discount_{{ $id }}"><span
                                                            class="input-group-text no-padding discountText_{{ $id }}"
                                                            id="basic-addon2"></span>
                                                </td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="discountp[]"
                                                            required step="any" value="{{ $product->discountp }}"
                                                            min="0" oninput="updateChanges({{ $id }})"
                                                            class="form-control text-center no-padding"
                                                            id="discountp_{{ $id }}"><span
                                                            class="input-group-text no-padding discountpText_{{ $id }}"
                                                            id="basic-addon2">{{ $product->discountvalue }}</span>
                                                </td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="fright[]"
                                                            required step="any"
                                                            oninput="updateChanges({{ $id }})"
                                                            value="{{ $product->fright }}" min="0"
                                                            class="form-control text-center no-padding"
                                                            id="fright_{{ $id }}"> <span
                                                            class="input-group-text no-padding frightText_{{ $id }}"
                                                            id="basic-addon2"></span></div>
                                                </td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="labor[]"
                                                            required step="any"
                                                            oninput="updateChanges({{ $id }})"
                                                            value="{{ $product->labor }}" min="0"
                                                            class="form-control text-center no-padding"
                                                            id="labor_{{ $id }}"> <span
                                                            class="input-group-text no-padding laborText_{{ $id }}"
                                                            id="basic-addon2"></span></div>
                                                </td>
                                                <td class="no-padding">
                                                    <div class="input-group"><input type="number" name="claim[]"
                                                            required step="any"
                                                            oninput="updateChanges({{ $id }})"
                                                            value="{{ $product->claim }}" min="0"
                                                            class="form-control text-center no-padding"
                                                            id="claim_{{ $id }}"> <span
                                                            class="input-group-text no-padding claimText_{{ $id }}"
                                                            id="basic-addon2"></span></div>
                                                </td>
                                                <td class="no-padding"><input type="number" name="amount[]"
                                                        min="0.1" readonly required step="any" value="1"
                                                        class="form-control text-center no-padding"
                                                        id="amount_{{ $id }}"></td>
                                                <td class="no-padding">
                                                    <span class="btn btn-sm btn-danger"
                                                        onclick="deleteRow({{ $id }})">X</span>
                                                </td>
                                                <input type="hidden" name="id[]" id="id_{{ $id }}" value="{{ $id }}">
                                                <input type="hidden" name="frightValue[]"
                                                    id="frightValue_{{ $id }}">
                                                <input type="hidden" name="laborValue[]"
                                                    id="laborValue_{{ $id }}">
                                                <input type="hidden" name="claimValue[]"
                                                    id="claimValue_{{ $id }}">
                                                <input type="hidden" name="discountValue[]"
                                                    id="discountValue_{{ $id }}">
                                                <input type="hidden" id="stockInput_{{ $id }}"
                                                    value="{{ $product->stock1 }}">
                                                <input type="hidden" name="discountPValue[]"
                                                    id="discountPValue_{{ $id }}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Total</th>
                                            <th class="text-end" id="totalQty">0.00</th>

                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-end" id="totalDiscount">0.00</th>
                                            <th class="text-end" id="totalPDiscount">0.00</th>
                                            <th class="text-end" id="totalFright">0.00</th>
                                            <th class="text-end" id="totalLabor">0.00</th>
                                            <th class="text-end" id="totalClaim">0.00</th>
                                            <th class="text-end" id="totalAmount">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="orderdate">Order Date</label>
                                    <input type="date" name="orderdate" id="orderdate" readonly
                                        value="{{ date('Y-m-d', strtotime($order->date)) }}" class="form-control">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="orderbooker">Order Booker</label>
                                    <input type="text" value="{{ $order->orderbooker->name }}" class="form-control"
                                        readonly>

                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <input type="text" value="{{ $order->customer->title }}" class="form-control"
                                        readonly>

                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5">{{ $order->notes }}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit"
                                    class="btn btn-primary w-100">{{ Auth()->user()->role == 'Branch Admin' ? 'Update & Approve Order' : 'Update Order' }}</button>
                            </div>
                        </div>
                    </form>
                </div>
                @if(auth()->user()->role == 'Branch Admin')
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <h5>Last Recovery Date</h5>
                            <p>{{ date('Y-m-d', strtotime($methodData['date'])) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Last Sale Date</h5>
                            <p>{{ $methodData['last_sale'] ? date('Y-m-d', strtotime($methodData['last_sale'])) : 'N/A' }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Last Sale Amount</h5>
                            <p>{{ number_format($methodData['last_sale_amount'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Last Balance</h5>
                            <p>{{ number_format($methodData['last_balance'], 0) }}</p>
                            <input type="hidden" id="last_balance" value="{{ $methodData['last_balance'] }}">
                        </div>
                        <div class="col-3">
                            <h5>Cash</h5>
                            <p>{{ number_format($methodData['Cash'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Cheque</h5>
                            <p>{{ number_format($methodData['Cheque'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Online</h5>
                            <p>{{ number_format($methodData['Online'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Other</h5>
                            <p>{{ number_format($methodData['Other'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>Total Receivings</h5>
                            <p>{{ number_format($methodData['Cash'] + $methodData['Cheque'] + $methodData['Online'] + $methodData['Other'], 0) }}</p>
                        </div>
                        <div class="col-3">
                            <h5>This Order</h5>
                            <p id="this_order"></p>
                        </div>
                        <div class="col-3">
                            <h5>Net Balance</h5>
                            <p id="net_balance"></p>
                        </div>
                    </div>
                </div>
                @endif
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

            function getSingleProduct(id) {
                $.ajax({
                    url: "{{ url('branchorders/getproduct/') }}/" + id + "/" + {{ $order->customer->areaID }},
                    method: "GET",
                    success: function(product) {
                        let found = $.grep(existingProducts, function(element) {
                            return element === product.id;
                        });
                        if (found.length > 0) {} else {
                            var id = product.id;
                            var units = product.units;
                            var html = '<tr id="row_' + id + '">';
                            html += '<td class="no-padding">' + product.name + '</td>';
                            html +=
                                '<td class="no-padding"><select name="unit[]" class="form-control text-center no-padding" onchange="updateChanges(' +
                                id + ')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                html += '<option data-unit="' + unit.value + '" value="' + unit.id + '">' +
                                    unit.unit_name + '</option>';
                            });
                            html += '</select></td>';
                            html +=
                                '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' +
                                id +
                                ')" min="0" required step="any" value="1" class="form-control text-center no-padding" id="qty_' +
                                id + '"></td>';
                            html +=
                                '<td class="no-padding"><input type="number" name="loose[]" oninput="updateChanges(' +
                                id +
                                ')" min="0" required step="any" value="0" class="form-control text-center no-padding" id="loose_' +
                                id + '"></td>';
                            html +=
                                '<td class="no-padding"><input type="number" name="bonus[]" min="0" required oninput="updateChanges(' +
                                id +
                                ')" step="any" value="0" class="form-control text-center no-padding" id="bonus_' +
                                id + '"></td>';
                            html +=
                                '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' +
                                id + ')" required step="any" value="' + product.price +
                                '" min="1" class="form-control text-center no-padding" id="price_' + id + '"></td>';
                            html +=
                                '<td class="no-padding"><div class="input-group"><input type="number" name="discount[]" readonly required step="any" value="' +
                                product.discount + '" min="0" oninput="updateChanges(' + id +
                                ')" class="form-control text-center no-padding" id="discount_' + id +
                                '"><span class="input-group-text no-padding discountText_' + id +
                                '" id="basic-addon2"></span></td>';
                            html +=
                                '<td class="no-padding"><div class="input-group"><input type="number" name="discountp[]" readonly required step="any" value="' +
                                product.discountp + '" min="0" oninput="updateChanges(' + id +
                                ')" class="form-control text-center no-padding" id="discountp_' + id +
                                '"><span class="input-group-text no-padding discountpText_' + id +
                                '" id="basic-addon2"></span></td>';
                            html +=
                                '<td class="no-padding"><div class="input-group"><input type="number" name="fright[]" readonly required step="any" oninput="updateChanges(' +
                                id + ')" value="' + product.sfright +
                                '" min="0" class="form-control text-center no-padding" id="fright_' + id +
                                '"> <span class="input-group-text no-padding frightText_' + id +
                                '" id="basic-addon2"></span></div></td>';
                            html +=
                                '<td class="no-padding"><div class="input-group"><input type="number" name="labor[]" readonly required step="any" oninput="updateChanges(' +
                                id + ')" value="' + product.dc +
                                '" min="0" class="form-control text-center no-padding" id="labor_' + id +
                                '"> <span class="input-group-text no-padding laborText_' + id +
                                '" id="basic-addon2"></span></div></td>';
                            html +=
                                '<td class="no-padding"><div class="input-group"><input type="number" name="claim[]" readonly required step="any" oninput="updateChanges(' +
                                id + ')" value="' + product.sclaim +
                                '" min="0" class="form-control text-center no-padding" id="claim_' + id +
                                '"> <span class="input-group-text no-padding claimText_' + id +
                                '" id="basic-addon2"></span></div></td>';
                            html +=
                                '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' +
                                id + '"></td>';
                            html +=
                                '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' +
                                id + ')">X</span> </td>';
                            html += '<input type="hidden" name="id[]" id="id_' + id + '" value="' + id + '">';
                            html += '<input type="hidden" name="frightValue[]" id="frightValue_' + id +
                                '" value="0">';
                            html += '<input type="hidden" name="laborValue[]" id="laborValue_' + id +
                                '" value="0">';
                            html += '<input type="hidden" name="claimValue[]" id="claimValue_' + id +
                                '" value="0">';
                            html += '<input type="hidden" name="discountValue[]" id="discountValue_' + id +
                                '" value="0">';
                            html += '<input type="hidden" name="discountPValue[]" id="discountPValue_' + id +
                                '" value="0">';
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
                var bonus = parseFloat($('#bonus_' + id).val());
                var unit = $('#unit_' + id).find(':selected').data('unit');

                var unit_qty = unit * qty;
                var totalQty = unit_qty + loose + bonus;


                var qty = parseFloat($('#qty_' + id).val());
                var loose = parseFloat($('#loose_' + id).val());
                var bonus = parseFloat($('#bonus_' + id).val());
                var unit = $('#unit_' + id).find(':selected').data('unit');

                var price = parseFloat($('#price_' + id).val());
                var discount = parseFloat($('#discount_' + id).val());
                var discountp = parseFloat($('#discountp_' + id).val());
                var fright = parseFloat($('#fright_' + id).val());
                var labor = parseFloat($('#labor_' + id).val());
                var claim = parseFloat($('#claim_' + id).val());

                var discountValue = price * discountp / 100;
                qty = loose + (qty * unit);
                var amount = ((price - discount - discountValue - claim) + fright) * qty;
                $("#amount_" + id).val(amount.toFixed(2));
                $("#frightValue_" + id).val((fright * qty).toFixed(0));
                $("#laborValue_" + id).val((labor * qty).toFixed(0));
                $("#claimValue_" + id).val((claim * qty).toFixed(0));
                $(".frightText_" + id).html(((fright * qty).toFixed(0)));
                $(".laborText_" + id).html((labor * qty).toFixed(0));
                $(".claimText_" + id).html((claim * qty).toFixed(0));
                $(".discountText_" + id).html((discount * qty).toFixed(0));
                $("#discountValue_" + id).val((discount * qty).toFixed(0));
                $(".discountpText_" + id).html((discountValue * qty).toFixed(0));
                $("#discountPValue_" + id).val((discountValue * qty).toFixed(0));
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

                var totalQty = 0;
                $("input[id^='qty_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalQty += parseFloat(inputValue);
                });

                $("#totalQty").html(totalQty.toFixed(2));

                var totalFright = 0;
                $("input[id^='frightValue_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalFright += parseFloat(inputValue);
                });

                $("#totalFright").html(totalFright.toFixed(2));

                var totalLabor = 0;
                $("input[id^='laborValue_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalLabor += parseFloat(inputValue);
                });

                $("#totalLabor").html(totalLabor.toFixed(2));

                var totalClaim = 0;
                $("input[id^='claimValue_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalClaim += parseFloat(inputValue);
                });

                $("#totalClaim").html(totalClaim.toFixed(2));

                var totalDiscount = 0;
                $("input[id^='discountValue_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalDiscount += parseFloat(inputValue);
                });

                $("#totalDiscount").html(totalDiscount.toFixed(2));

                var totalPDiscount = 0;
                $("input[id^='discountPValue_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    totalPDiscount += parseFloat(inputValue);
                });

                $("#totalPDiscount").html(totalPDiscount.toFixed(2));

                var claim = $("#claim").val();
                var net = total - claim;

                $("#net").val(net.toFixed(2));

                var last_balance = $("#last_balance").val();
             
                var net_balance = parseFloat(last_balance) + total;

                $("#this_order").text(total.toFixed(0));

                $("#net_balance").text(parseFloat(net_balance.toFixed(0)));
            }

            function deleteRow(id) {
                existingProducts = $.grep(existingProducts, function(value) {
                    return value !== id;
                });
                $('#row_' + id).remove();
                updateTotal();
            }

             function checkCharges() {
            var freight = $("#freight_radio").is(':checked');
            var labor = $("#labor_radio").is(':checked');
            if (freight) {
                $("input[id^='fright_']").each(function() {
                    var inputId = $(this).attr('id');
                    console.log(inputId);
                    $(this).attr('readonly', false);
                });

            } else {
                $("input[id^='fright_']").each(function() {
                    var inputId = $(this).attr('id');
                    console.log(inputId);
                    $(this).val(0);
                    $(this).attr('readonly', true);
                });
            }

            if (labor) {
                $("input[id^='labor_']").each(function() {
                    var inputId = $(this).attr('id');
                    console.log(inputId);
                    $(this).attr('readonly', false);
                });

            } else {
                $("input[id^='labor_']").each(function() {
                    var inputId = $(this).attr('id');
                    console.log(inputId);
                    $(this).val(0);
                    $(this).attr('readonly', true);
                });
            }
            $("input[id^='id_']").each(function() {
                    var inputId = $(this).attr('id');
                    var inputValue = $(this).val();
                    updateChanges(inputValue);
                });
        }

            @foreach ($order->details as $product)
                updateChanges({{ $product->productID }});
            @endforeach

           
        </script>
    @endsection
