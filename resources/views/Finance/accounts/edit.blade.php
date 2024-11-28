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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title">Account Title</label>
                                    <input type="text" name="title" value="{{$account->title}}" id="title"
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
                                    <label for="contact">Contact #</label>
                                    <input type="text" name="contact" id="contact" value="{{ $account->contact }}"
                                        class="form-control">
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
