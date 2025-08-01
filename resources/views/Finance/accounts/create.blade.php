@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Create Account</h3>
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
                    <form action="{{ route('account.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title">Account Title</label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title_urdu">Account Title (Urdu)</label>
                                    <input type="text" name="title_urdu" id="title_urdu" value="{{ old('title_urdu') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-2">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" onchange="checkType()">
                                        <option value="Customer">Customer</option>
                                        <option value="Vendor">Vendor</option>
                                        @if(Auth::user()->role == "Admin" || Auth::user()->role == "Accountant" || Auth::user()->role == "Branch Admin")
                                        <option value="Business">Business</option>
                                        <option value="Supply Man">Supply Man</option>
                                        <option value="Unloader">Unloader</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-2">
                                    <label for="branck">Branch</label>
                                    <select name="branch" id="branch" class="form-control">
                                        @foreach ($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2" id="catBox">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="Bank">Bank</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer" >
                                <div class="form-group">
                                    <label for="cnic">Customer Category</label>
                                    <select name="c_type" id="c_type" class="form-control">
                                        <option value="Distributor">Distributor</option>
                                        <option value="Retailer">Retailer</option>
                                        <option value="Wholeseller">Wholeseller</option>
                                        <option value="Super Mart">Super Mart</option>
                                        <option value="Sub Dealer">Sub Dealer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="area">Area</label>
                                    <select name="area" id="area" class="selectize">
                                        <option value=""></option>
                                        @foreach ($areas as $area)
                                            <option value="{{$area->id}}">{{$area->town->name}} - {{$area->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer vendor business">
                                <div class="form-group ">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="address_urdu">Address (Urdu)</label>
                                    <input type="text" name="address_urdu" id="address_urdu" value="{{ old('address_urdu') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="limit">Credit Limit</label>
                                    <input type="number" min="0" name="limit" id="limit" value="{{ old('limit') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6 mt-2" >
                                <div class="form-group">
                                    <label for="contact">Contact #</label>
                                    <input type="text" name="contact" id="contact" value="{{ old('contact') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-6 mt-2" >
                                <div class="form-group">
                                    <label for="email">Email #</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="form-control">
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
        $(".customer").hide();
        $(".selectize").selectize();
       
    function checkType(){
            var type = $("#type").find(":selected").val();
            if(type === "Business")
            {
                $("#catBox").show();
            }
            else
            {
                $("#catBox").hide();
            }
            if(type === "Customer")
            {
                $(".customer").show();
            }
            else
            {
                $(".customer").hide();
            }
            if(type === "Vendor")
            {
                $(".vendor").show();
            }
            if(type === "Business")
            {
                $(".business").show();
            }
           /*  else
            {
                $(".vendor").hide();
                if(type === "Customer")
                {
                    $(".customer").show();
                }
            } */
        }
        checkType();
    </script>
@endsection
