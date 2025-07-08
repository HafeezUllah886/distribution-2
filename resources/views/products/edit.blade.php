@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Edit Product</h3>
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
                    <form action="{{ route('product.update', $product->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" value="{{$product->name}}" required id="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nameurdu">Name (Urdu)</label>
                                        <input type="text" name="nameurdu" value="{{$product->nameurdu}}" id="nameurdu" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="brandID">Brand</label>
                                       <select name="brandID" id="brandID" class="selectize">
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}" @selected($brand->id == $product->brandID)>{{$brand->name}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="catID">Category</label>
                                       <select name="catID" id="catID" class="selectize">
                                        @foreach ($cats as $cat)
                                            <option value="{{$cat->id}}" @selected($cat->id == $product->catID)>{{$cat->name}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="vendorID">Vendor</label>
                                       <select name="vendorID" id="vendorID" class="selectize">
                                        @foreach ($vendors as $vendor)
                                            <option value="{{$vendor->id}}" @selected($vendor->id == $product->vendorID)>{{$vendor->title}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="status">Status</label>
                                       <select name="status" id="status" class="selectize">
                                       <option value="Active" @selected($product->status == "Active")>Active</option>
                                       <option value="In-active" @selected($product->status == "In-active")>In-active</option>
                                       </select>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <h5>Purchase Values</h5>
                                    <hr>
                                    <div class="form-group">
                                        <label for="pprice">Purchase Price</label>
                                        <input type="number" step="any" value="{{$product->pprice}}" name="pprice" required
                                            min="0" id="pprice"
                                            class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="fright">Fright</label>
                                        <input type="number" step="any" name="fright" required value="{{$product->fright}}" min="0" id="fright" class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="claim">Claim</label>
                                        <input type="number" step="any" name="claim" required value="{{$product->claim}}" min="0" id="claim" class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="labor">Labor Charges</label>
                                        <input type="number" step="any" name="labor" required value="{{$product->labor}}" min="0" id="labor" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                                <div class="col-12">
                                                    <div class="card-header d-flex justify-content-between">
                                                        <h5>Units - Pack Sizes</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-10 ">
                                                            <select class="selectize" id="unit">
                                                               @foreach ($units as $unit)
                                                                   <option value="{{$unit->id}}" >{{$unit->name}} - {{$unit->value}}</option>
                                                               @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-2">
                                                            <button class="w-100 btn btn-success" type="button" onclick="addUnit()">+</button>
                                                        </div>
                                                    </div>
                                                    <table  class="w-100 table">
                                                        <thead>
                                                            <th>Unit</th>
                                                            <th class="text-center">Pack Size</th>
                                                            <th></th>
                                                        </thead>
                                                        <tbody id="units">
                                                            @php
                                                                $ser = 0;
                                                            @endphp
                                                            @foreach ($product->units as $unit)
                                                                @php
                                                                    $ser += 1;
                                                                @endphp
                                                                <tr class="p-0" id="row_{{$ser}}">
                                                                <td width="70%" class="p-0"><input type="text" class="form-control form-control-sm" name="unit_names[]" value="{{$unit->unit_name}}"></td>
                                                                <td class="p-0"><input type="number" step="any" class="form-control form-control-sm text-center" name="unit_values[]" value="{{$unit->value}}"></td>
                                                                <td class="p-0"> <span class="btn btn-sm btn-danger" onclick="deleteRow({{$ser}})">X</span></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                        </div>
        
                                       </div>
                                </div>
                                <div class="col-md-6 mt-2">
                                    <h5>Sale Values</h5>
                                    <hr>
                                    <div class="form-group mt-2">
                                        <label for="price">Sale Price</label>
                                        <input type="number" step="any" value="{{$product->price}}" name="price" required
                                             min="0" id="price"
                                            class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="sfright">Fright</label>
                                        <input type="number" step="any" name="sfright" required value="{{$product->sfright}}" min="0" id="sfright" class="form-control">
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="sclaim">Claim</label>
                                        <input type="number" step="any" name="sclaim" required value="{{$product->sclaim}}" min="0" id="sclaim" class="form-control">
                                    </div>
                                   
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mt-2">
                                                <label for="discount">Discount</label>
                                                <input type="number" step="any" name="discount" required
                                                    value="{{$product->discount}}" min="0" 
                                                    id="discount" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mt-2">
                                                <label for="discountp">Discount %</label>
                                                <input type="number" step="any" name="discountp" required
                                                    value="{{$product->discountp}}" min="0"
                                                    id="discountp" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-secondary w-100">Update</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Default Modals -->


@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection

@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
    <script>
        $(".selectize").selectize();

var unitCount = 1;
function addUnit() {

    var unit = $("#unit").find(':selected').val();

    $.ajax({
        url: "{{ url('getUnit/') }}/" + unit,
        method: "GET",
        success: function(response) {
            var html = '<tr class="p-0" id="row_' + response.unit_id + '">';
    html += '<td width="70%" class="p-0"><input type="text" class="form-control form-control-sm" name="unit_names[]" value="'+response.unit_name+'"></td>';
    html += '<td class="p-0"><input type="number" step="any" class="form-control form-control-sm text-center" name="unit_values[]" value="'+response.unit_value+'"></td>';
    html += '<td class="p-0"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' + response.unit_id + ')">X</span></td>';
    html += '</tr>';

    $("#units").append(html);
        }
    });

    
}
        function deleteRow(optionCount) {
            $('#row_' + optionCount).remove();
        }

    </script>
@endsection
