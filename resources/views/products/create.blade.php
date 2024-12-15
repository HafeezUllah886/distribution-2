@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Create Product</h3>
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
                    <form action="{{ route('product.store') }}" method="post">
                        @csrf
                        <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" required id="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nameurdu">Name (Urdu)</label>
                                        <input type="text" name="nameurdu" id="nameurdu" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="brandID">Brand</label>
                                       <select name="brandID" id="brandID" class="selectize">
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->id}}">{{$brand->name}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="catID">Category</label>
                                       <select name="catID" id="catID" class="selectize">
                                        @foreach ($cats as $cat)
                                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="vendorID">Vendor</label>
                                       <select name="vendorID" id="vendorID" class="selectize">
                                        @foreach ($vendors as $vendor)
                                            <option value="{{$vendor->id}}">{{$vendor->title}}</option>
                                        @endforeach
                                       </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="pprice">Purchase Price</label>
                                        <input type="number" step="any" name="pprice" required
                                            value="" min="0" id="pprice"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="price">Sale Price</label>
                                        <input type="number" step="any" name="price" required
                                            value="" min="0" id="price"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="discount">Discount</label>
                                        <input type="number" step="any" name="discount" required
                                            value="0" min="0"
                                            id="discount" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="fright">Fright</label>
                                        <input type="number" step="any" name="fright" required value="0" min="0" id="fright" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="labor">Labor Charges</label>
                                        <input type="number" step="any" name="labor" required value="0" min="0" id="labor" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="claim">Claim</label>
                                        <input type="number" step="any" name="claim" required value="0" min="0" id="claim" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mt-2">
                                        <label for="status">Status</label>
                                       <select name="status" id="status" class="selectize">
                                       <option value="Active">Active</option>
                                       <option value="In-active">In-active</option>
                                       </select>
                                    </div>
                                </div>
                               <div class="col-12">
                                <div class="row">
                                        <div class="col-6">
                                            <div class="card-header d-flex justify-content-between">
                                                <h5>Units - Pack Sizes</h5>
                                            </div>
                                            <div class="row">
                                                <div class="col-10 ">
                                                    <select class="selectize" id="unit">
                                                       @foreach ($units as $unit)
                                                           <option value="{{$unit->id}}" data-name="{{$unit->name}}" data-value="{{$unit->value}}">{{$unit->name}} - {{$unit->value}}</option>
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

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                               </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-secondary w-100">Create</button>
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
            var selectizeInstance = $("#unit")[0].selectize; // Access the Selectize instance
            var unit_id = selectizeInstance.getValue(); // Get the selected value (from the 'value' attribute)
            var unit_name = selectizeInstance.options[unit_id]?.name; // Access the data-name equivalent
            var unit_value = selectizeInstance.options[unit_id]?.value; // Access the data-value equivalent
            unitCount += 1;

            var html = '<tr class="p-0" id="row_' + unitCount + '">';
            html += '<td width="70%" class="p-0"><input type="text" class="form-control form-control-sm" name="unit_names[]" value="'+unit_name+'"></td>';
            html += '<td class="p-0"><input type="number" step="any" class="form-control form-control-sm text-center" name="unit_values[]" value="'+unit_value+'"></td>';
            html += '<td class="p-0"> <span class="btn btn-sm btn-danger" onclick="deleteRow(' + unitCount + ')">X</span></td>';
            html += '</tr>';

            $("#units").append(html);
        }
        function deleteRow(optionCount) {
            $('#row_' + optionCount).remove();
        }

    </script>
@endsection
