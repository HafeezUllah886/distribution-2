@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Profit / Lost Report</h3>
                </div>
                <form action="{{ route('reportProfitData') }}" method="get">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="from">From</label>
                                <input type="date" name="from" id="from" value="{{firstDayOfMonth()}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="to">To</label>
                                <input type="date" name="to" id="to" value="{{lastDayOfMonth()}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="branch">Branch</label>
                                <select name="branch" id="branch" class="form-control">
                                    @if(auth()->user()->role == "Admin")
                                    <option value="All">All</option>
                                    @endif
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="vendor">Vendors</label>
                                <select name="vendor[]" id="vendor" class="selectize" multiple>
                                    @foreach ($vendors as $vendor)
                                        <option value="{{$vendor->id}}">{{$vendor->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="product">Products</label>
                                <select name="product[]" id="product" class="selectize" multiple>
                                    @foreach ($products as $product)
                                        <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="warehouse">Warehouses</label>
                                <select name="warehouse[]" id="warehouse" class="selectize" multiple>
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="town">Towns</label>
                                <select name="town[]" id="town" class="selectize" multiple>
                                    @foreach ($towns as $town)
                                        <option value="{{$town->id}}">{{$town->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="area">Areas</label>
                                <select name="area[]" id="area" class="selectize" multiple>
                                    @foreach ($areas as $area)
                                        <option value="{{$area->id}}">{{$area->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="customer">Customers</label>
                                <select name="customer[]" id="customer" class="selectize" multiple>
                                    @foreach ($customers as $customer)
                                        <option value="{{$customer->id}}">{{$customer->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mt-2">
                                <label for="orderbooker">Order Bookers</label>
                                <select name="orderbooker[]" id="orderbooker" class="selectize" multiple>
                                    @foreach ($orderbookers as $ob)
                                        <option value="{{$ob->id}}">{{$ob->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <label for="expense_category">Expense Categories</label>
                                <select name="expense_category[]" id="expense_category" class="selectize" multiple>
                                    @foreach ($expense_categories as $ec)
                                        <option value="{{$ec->id}}">{{$ec->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mt-4">
                                <button class="btn btn-success w-100" type="submit" id="viewBtn">View Report</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection
@section('page-js')

<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
<script>
    $(".selectize").selectize({
        plugins: ['remove_button'],
        maxItems: null,
        create: false,
        placeholder: 'Select items...'
    });

    </script>
@endsection
