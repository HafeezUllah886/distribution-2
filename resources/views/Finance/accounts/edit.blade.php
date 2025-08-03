@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Edit Account</h3>
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
                    <form action="{{ route('account.update', $account->id) }}" method="post">
                        @csrf
                        @method('put')
                        <input type="hidden" name="accountID" value="{{$account->id}}">
                        <input type="hidden" name="type" value="{{$account->type}}">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title">Account Title</label>
                                    <input type="text" name="title" value="{{$account->title}}" id="title"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="title">Account Title (Urdu)</label>
                                    <input type="text" name="title_urdu" value="{{$account->title_urdu}}" id="title_urdu"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="col-12 mt-2 {{$account->type != "Business" ? "d-none" : ""}}"  id="catBox">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>

                            @if ($account->type == 'Customer')
                            <div class="col-6 mt-2 customer" >
                                <div class="form-group">
                                    <label for="cnic">Customer Category</label>
                                    <select name="c_type" id="c_type" class="form-control">
                                        <option value="Distributor" @selected($account->c_type == "Distributor")>Distributor</option>
                                        <option value="Retailer" @selected($account->c_type == "Retailer")>Retailer</option>
                                        <option value="Wholeseller" @selected($account->c_type == "Wholeseller")>Wholeseller</option>
                                        <option value="Super Mart" @selected($account->c_type == "Super Mart")>Super Mart</option>
                                        <option value="Sub Dealer" @selected($account->c_type == "Sub Dealer")>Sub Dealer</option>
                                        <option value="Other" @selected($account->c_type == "Other")>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer" >
                                <div class="form-group">
                                    <label for="cnic">Area</label>
                                    <select name="area" id="area" class="form-control">
                                        @foreach ($areas as $area)
                                            <option value="{{ $area->id }}" @selected($account->areaID == $area->id)>{{ $area->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer" >
                                <div class="form-group">
                                    <label for="cnic">Customer Category</label>
                                    <select name="c_type" id="c_type" class="form-control">
                                        <option value="Distributor" @selected($account->c_type == "Distributor")>Distributor</option>
                                        <option value="Retailer" @selected($account->c_type == "Retailer")>Retailer</option>
                                        <option value="Wholeseller" @selected($account->c_type == "Wholeseller")>Wholeseller</option>
                                        <option value="Super Mart" @selected($account->c_type == "Super Mart")>Super Mart</option>
                                        <option value="Sub Dealer" @selected($account->c_type == "Sub Dealer")>Sub Dealer</option>
                                        <option value="Other" @selected($account->c_type == "Other")>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer" >
                                <div class="form-group">
                                    <label for="contact">Contact #</label>
                                    <input type="text" name="contact" id="contact" value="{{ $account->contact }}"
                                        class="form-control">
                                </div>
                            </div>
                            @endif
                            @if ($account->type == 'Customer' || $account->type == 'Vendor' || $account->type == 'Business')
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" id="address" value="{{ $account->address }}" class="form-control">
                                </div>
                            </div>
                            @endif
                            @if ($account->type == 'Customer')
                           
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="address_urdu">Address (urdu)</label>
                                    <input type="text" name="address_urdu" id="address_urdu" value="{{ $account->address_urdu }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6 mt-2 customer">
                                <div class="form-group ">
                                    <label for="limit">Credit Limit</label>
                                    <input type="number" min="0" name="limit" value="{{$account->credit_limit}}" id="limit" class="form-control">
                                </div>
                            </div>
                            @endif

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
