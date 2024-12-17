@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Create Purchase </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><button onclick="window.close()" class="btn btn-danger">Close</button></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('purchase.store') }}" method="post">
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
                                    <tbody id="products_list"></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-end">Total</th>
                                            <th class="text-end" id="totalQty">0.00</th>
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
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="comp">Purchase Inv No.</label>
                                    <input type="text" name="inv" id="inv" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="comp">Bilty No.</label>
                                    <input type="text" name="bilty" id="bilty" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="comp">Transporter</label>
                                    <input type="text" name="transporter" id="transporter" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="orderdate">Order Date</label>
                                    <input type="date" name="orderdate" id="orderdate" value="{{ date('Y-m-d') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="date">Receiving Date</label>
                                    <input type="date" name="recdate" id="date" value="{{ date('Y-m-d') }}" class="form-control">
                                    <input type="hidden" name="vendorID" value="{{ $vendor }}">
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
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary w-100">Create Purchase</button>
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
                url: "{{ url('purchases/getproduct/') }}/" + id,
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
                        html += '<td class="no-padding"><input type="number" name="bonus[]" min="0" required step="any" value="0" class="form-control text-center no-padding" id="bonus_' + id + '"></td>';
                        html += '<td class="no-padding"><input type="number" name="price[]" oninput="updateChanges(' + id + ')" required step="any" value="'+product.pprice+'" min="1" class="form-control text-center no-padding" id="price_' + id + '"></td>';
                        html += '<td class="no-padding"><div class="input-group"><input type="number" name="discount[]" required step="any" value="0" min="0" oninput="updateChanges(' + id + ')" class="form-control text-center no-padding" id="discount_' + id + '"><span class="input-group-text no-padding discountText_'+id+'" id="basic-addon2"></span></td>';
                        html += '<td class="no-padding"><div class="input-group"><input type="number" name="discountp[]" required step="any" value="0" min="0" oninput="updateChanges(' + id + ')" class="form-control text-center no-padding" id="discountp_' + id + '"><span class="input-group-text no-padding discountpText_'+id+'" id="basic-addon2"></span></td>';
                        html += '<td class="no-padding"><div class="input-group"><input type="number" name="fright[]" required step="any" oninput="updateChanges(' + id + ')" value="'+product.fright+'" min="0" class="form-control text-center no-padding" id="fright_' + id + '"> <span class="input-group-text no-padding frightText_'+id+'" id="basic-addon2"></span></div></td>';
                        html += '<td class="no-padding"><div class="input-group"><input type="number" name="labor[]" required step="any" oninput="updateChanges(' + id + ')" value="'+product.labor+'" min="0" class="form-control text-center no-padding" id="labor_' + id + '"> <span class="input-group-text no-padding laborText_'+id+'" id="basic-addon2"></span></div></td>';
                        html += '<td class="no-padding"><div class="input-group"><input type="number" name="claim[]" required step="any" oninput="updateChanges(' + id + ')" value="'+product.claim+'" min="0" class="form-control text-center no-padding" id="claim_' + id + '"> <span class="input-group-text no-padding claimText_'+id+'" id="basic-addon2"></span></div></td>';
                        html += '<td class="no-padding"><input type="number" name="amount[]" min="0.1" readonly required step="any" value="1" class="form-control text-center no-padding" id="amount_' + id + '"></td>';
                        html += '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" name="frightValue[]" id="frightValue_'+id+'" value="0">';
                        html += '<input type="hidden" name="laborValue[]" id="laborValue_'+id+'" value="0">';
                        html += '<input type="hidden" name="claimValue[]" id="claimValue_'+id+'" value="0">';
                        html += '<input type="hidden" name="discountValue[]" id="discountValue_'+id+'" value="0">';
                        html += '<input type="hidden" name="discountPValue[]" id="discountPValue_'+id+'" value="0">';
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
            var bonus = parseFloat($('#bonus_' + id).val());
            var price = parseFloat($('#price_' + id).val());
            var discount = parseFloat($('#discount_' + id).val());
            var discountp = parseFloat($('#discountp_' + id).val());
            var fright = parseFloat($('#fright_' + id).val());
            var labor = parseFloat($('#labor_' + id).val());
            var claim = parseFloat($('#claim_' + id).val());
            var discountValue = price * discountp / 100;
            var amount = (price - discount - discountValue - claim) * qty;
            $("#amount_"+id).val(amount.toFixed(2));
            $("#frightValue_"+id).val(fright * qty);
            $("#laborValue_"+id).val(labor * qty);
            $("#claimValue_"+id).val(claim * qty);
            $(".frightText_"+id).html(fright * qty);
            $(".laborText_"+id).html(labor * qty);
            $(".claimText_"+id).html(claim * qty);
            $(".discountText_"+id).html(discount * qty);
            $("#discountValue_"+id).val(discount * qty);
            $(".discountpText_"+id).html(discountValue * qty);
            $("#discountPValue_"+id).val(discountValue * qty);
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

            var totalQty = 0;
            $("input[id^='qty_']").each(function() {
                var inputId = $(this).attr('id');
                var inputValue = $(this).val();
                totalQty += parseFloat(inputValue);
            });

            $("#totalQty").html(totalQty.toFixed(2));

            var claim = $("#claim").val();
            var net = total - claim;

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
