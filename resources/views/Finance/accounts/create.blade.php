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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title">Account Title</label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mt-2">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="Business">Business</option>
                                        <option value="Customer">Customer</option>
                                        <option value="Vendor">Vendor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 mt-2" id="catBox">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="Cash">Cash</option>
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
        $("#type").on("change",  function (){
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
        });
    </script>
@endsection
