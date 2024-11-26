@extends('layout.popups')
@section('content')
<script>
    var existingProducts = [];

    @foreach ($purchase->details as $product)
        @php
            $productID = $product->productID;
        @endphp
        existingProducts.push({{$productID}});
    @endforeach
</script>
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Edit Purchase </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('purchase.update', $purchase->id) }}" method="post">
                        @csrf
                        @method("PUT")
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
                                        <th width="30%">Item</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">P-Price</th>
                                        <th class="text-center">S-Price</th>
                                        <th class="text-center">WS Price</th>
                                        <th class="text-center">RT Price</th>
                                        <th class="text-center">GST 18%</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Bonus</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list">
                                        @foreach ($purchase->details as $product)
                                        @php
                                            $id = $product->product->id;
                                        @endphp
                                        <tr id="row_{{$id}}">
                                            <td class="no-padding">{{$product->product->code . " | " . $product->product->name}}</td>
                                            <td class="no-padding">
                                                <select name="unit[]" class="form-control text-center" onchange="updateChanges({{ $id }})" id="unit_{{ $id }}">
                                                    @foreach ($units as $unit)
                                                        @php
                                                            if($unit->id == $product->unitID)
                                                            {
                                                                $unitValue = $product->unitValue;
                                                            }
                                                        @endphp
                                                        <option data-unit="{{ $unit->value }}" value="{{ $unit->id }}" @selected($unit->id == $product->unitID)>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="no-padding">
                                                    <input type="number" name="qty[]" oninput="updateChanges({{ $id }})" min="0.1"
                                                    required step="any" value="{{$product->qty / $unitValue}}" class="form-control text-center" id="qty_{{ $id }}">
                                            </td>
                                            <td class="no-padding"><input type="number" name="pprice[]" oninput="updateChanges({{$id}})" required step="any" value="{{$product->pprice}}" min="1" class="form-control text-center no-padding" id="pprice_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="price[]" required step="any" value="{{$product->price}}" min="0" class="form-control text-center no-padding" id="price_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="wsprice[]" required step="any" value="{{$product->wsprice}}" min="1" class="form-control text-center no-padding" id="wsprice_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="tp[]" required step="any" value="{{$product->tp}}" min="1" class="form-control text-center no-padding" id="tp_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="gstValue[]" readonly required step="any" value="{{$product->gstValue}}" class="form-control text-center no-padding" id="gstValue_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="{{$product->amount}}" class="form-control text-center no-padding" id="amount_{{$id}}"></td>
                                            <td class="no-padding"><input type="number" name="bonus[]" min="0" required step="any" value="{{$product->bonus}}" oninput="updateChanges({{$id}})" class="form-control text-center no-padding" id="bonus_{{$id}}"></td>
                                            <td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow({{$id}})">X</span> </td>
                                            <input type="hidden" name="id[]" value="{{$id}}">
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-end">Total</th>

                                            <th class="text-end" id="totalGst">0.00</th>
                                            <th class="text-end" id="totalAmount">0.00</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="comp">Purchase Inv No.</label>
                                    <input type="text" name="inv" value="{{$purchase->inv}}" id="inv" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="discount">Discount</label>
                                    <input type="number" name="discount" value="{{$purchase->discount}}" oninput="updateTotal()" id="discount" step="any" value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="fright">Fright (-)</label>
                                    <input type="number" name="fright" id="fright" value="{{$purchase->fright}}" oninput="updateTotal()" min="0" step="any" value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="fright1">Fright (+)</label>
                                    <input type="number" name="fright1" id="fright1" value="{{$purchase->fright1}}" oninput="updateTotal()" min="0" step="any" value="0" class="form-control">
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label for="whTax">WH Tax</label>
                                    <div class="input-group mb-3">
                                        <input type="number" name="whTax" id="whTax" value="{{$purchase->wh}}" oninput="updateTotal()" max="50" min="0" step="any" value="0" aria-describedby="basic-addon2" class="form-control">
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
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" value="{{$purchase->date}}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="vendor">Vendor</label>
                                    <select name="vendorID" id="vendor" class="selectize1">
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" @selected($vendor->id == $purchase->vendorID)>{{ $vendor->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-3 mt-2">
                                <div class="form-group">
                                    <label for="account">Account</label>
                                    <select name="accountID" id="account" class="selectize1">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-3 mt-2">
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
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5">{{$purchase->notes}}</textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Update Purchase</button>
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


        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('purchases/getproduct/') }}/" + id,
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {

                    } else {

                        var id = product.id;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.code + ' | ' + product.name + '</td>';

                        html += '<td class="no-padding"><select name="unit[]" class="form-control text-center no-padding" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                var isSelected = (unit.id == product.unitID);
                                html += '<option data-unit="'+unit.value+'" value="' + unit.id + '" ' + (isSelected ? 'selected' : '') + '>' + unit.name + '</option>';
                            });
                        html += '</select></td>';
                        html += '<td class="no-padding"><input type="number" name="qty[]" oninput="updateChanges(' + id + ')" min="0" required step="any" value="0" class="form-control text-center no-padding" id="qty_' + id + '"></td>';

                        html += '<td class="no-padding"><input type="number" name="pprice[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.pprice+'" min="1" class="form-control text-center no-padding" id="pprice_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="price[]" required step="any" value="'+product.price+'" min="0" class="form-control text-center no-padding" id="price_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="wsprice[]" required step="any" value="'+product.wsprice+'" min="1" class="form-control text-center no-padding" id="wsprice_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="tp[]" required step="any" value="'+product.tp+'" min="1" class="form-control text-center no-padding" id="tp_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="gstValue[]" readonly required step="any" value="0" class="form-control text-center no-padding" id="gstValue_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="bonus[]" min="0" required step="any" value="0" oninput="updateChanges(' + id + ')" class="form-control text-center no-padding" id="bonus_' + id + '"></td>';
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
            var unit = $('#unit_' + id).find('option:selected');
            unit = unit.data('unit');
            var newQty = qty * unit;
            var pprice = parseFloat($('#pprice_' + id).val());
            var tp = parseFloat($('#tp_' + id).val());
            var bonus = parseFloat($('#bonus_' + id).val());

            var gstValue = (tp * 18 / 100) * (newQty + bonus);
            var amount = newQty * pprice;
            $("#amount_"+id).val(amount.toFixed(2));
            $("#gstValue_"+id).val(gstValue.toFixed(2));
            updateTotal();
        }

        updateTotal();
        function updateTotal() {
            var total = 0;
            $("input[id^='amount_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                total += parseFloat(inputValue);
            });

            $("#totalAmount").html(total.toFixed(2));

            var gst = 0;
            $("input[id^='gstValue_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                gst += parseFloat(inputValue);
            });

            $("#totalGst").html(gst.toFixed(2));

            var discount = parseFloat($("#discount").val());
            var fright = parseFloat($("#fright").val());
            var fright1 = parseFloat($("#fright1").val());
            var whTax = parseFloat($("#whTax").val());

            var taxValue = total * whTax / 100;

            $(".whTaxValue").html(taxValue.toFixed(2));

            var net = (total + taxValue + fright1) - (discount + fright);

            $("#net").val(net.toFixed(2));
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
