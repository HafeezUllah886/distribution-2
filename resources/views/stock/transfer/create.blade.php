@extends('layout.popups')
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-12">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6"><h3> Create Stock Transfer </h3></div>
                                <div class="col-6 d-flex flex-row-reverse"><a href="{{route('stockTransfers.index')}}" class="btn btn-danger">Close</a></div>
                            </div>
                        </div>
                    </div>
                </div><!--end row-->
                <div class="card-body">
                    <form action="{{ route('stockTransfers.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="product">Product</label>
                                    <select name="product" class="selectize" id="product">
                                        <option value="0"></option>
                                        @foreach ($products as $product)
                                            @if($product->stock > 0)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <th width="10%">Item</th>
                                        <th width="10%" class="text-center">Unit</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">loose</th>
                                        <th></th>
                                    </thead>
                                    <tbody id="products_list"></tbody>
                                </table>
                            </div>
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="">From Warehouse</label>
                                    <input type="text" value="{{$warehouseFrom->name}}" class="form-control" readonly>
                                    <input type="hidden" name="fromWarehouse" value="{{$warehouseFrom->id}}">
                                </div>
                            </div>
                            <div class="col-4 mt-2">
                                <div class="form-group">
                                    <label for="">To Warehouse</label>
                                    <input type="text" value="{{$warehouseTo->name}}" class="form-control" readonly>
                                    <input type="hidden" name="toWarehouse" value="{{$warehouseTo->id}}">
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

        var existingProducts = [];
        function getSingleProduct(id) {
            $.ajax({
                url: "{{ url('getproduct/') }}/" + id + "/" + {{$warehouseFrom->id}},
                method: "GET",
                success: function(product) {
                    let found = $.grep(existingProducts, function(element) {
                        return element === product.id;
                    });
                    if (found.length > 0) {
                    } else {
                        if(product.stock == 0)
                        {
                            alert("Stock is not available");
                            return;
                        }
                        var id = product.id;
                        var units = product.units;
                        var html = '<tr id="row_' + id + '">';
                        html += '<td class="no-padding">' + product.name + '</td>';
                        html += '<td class="no-padding"><select name="unit[]" class="form-control text-center no-padding" onchange="updateChanges(' + id +')" id="unit_' + id + '">';
                            units.forEach(function(unit) {
                                html += '<option data-unit="'+unit.value+'" value="' + unit.id + '">' + unit.unit_name + '</option>';
                            });
                        html += '</select></td>';
                        html += '<td class="no-padding"><div class="input-group"><span class="input-group-text no-padding stock_'+id+'" id="basic-addon2">'+product.stock+'</span><input type="number" name="qty[]" oninput="updateChanges(' + id + ')" max="'+product.stock+'" min="0" required step="any" value="1" class="form-control text-center no-padding" id="qty_' + id + '"> </div></td>';
                        html += '<td class="no-padding"><input type="number" name="loose[]" oninput="updateChanges(' + id + ')" min="0" required step="any" value="0" class="form-control text-center no-padding" id="loose_' + id + '"></td>';
                        html += '<td class="no-padding"> <span class="btn btn-sm btn-danger" onclick="deleteRow('+id+')">X</span> </td>';
                        html += '<input type="hidden" name="id[]" value="' + id + '">';
                        html += '<input type="hidden" id="stockInput_'+id+'" value="'+product.stock+'">';
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
            var unit = $('#unit_' + id).find(':selected').data('unit');
            var stock = parseFloat($('#stockInput_' + id).val());

            var unit_qty = unit * qty;
            var stock_place = stock / unit;
            var totalQty = unit_qty + loose;

            $("#qty_"+id).attr("max", stock_place);
            $(".stock_"+id).html(stock_place.toFixed(0));

            if(totalQty > stock)
            {
                $('#qty_' + id).val(0);
                $('#loose_' + id).val(0);
                alert("Qty Exceeted then availble Stock");
            }


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
