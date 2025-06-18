@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>View Invoice Payments Report</h3>
                </div>
                <form action="{{ route('reportInvoicePaymentsData') }}" method="get">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="from">From</label>
                                <input type="date" name="from" id="from" value="{{firstDayOfMonth()}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="to">To</label>
                                <input type="date" name="to" id="to" value="{{lastDayOfMonth()}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="orderbooker">Orderbooker</label>
                                <select name="orderbooker" id="orderbooker" class="form-control">
                                    <option value="All">All</option>
                                    @foreach ($orderbookers as $orderbooker)
                                        <option value="{{$orderbooker->id}}">{{$orderbooker->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="customer">Customers</label>
                                <select name="customer" id="customer" class="form-control">
                                    <option value="All">All</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{$customer->id}}">{{$customer->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="area">Area (Blank = All)</label>
                                <select name="area[]" id="area" class="selectize" multiple>
                                    @foreach ($areas as $area)
                                        <option value="{{$area->id}}">{{$area->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mt-2">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="All">All</option>
                                    <option value="Due">Only Due</option>
                                    <option value="Paid">Only Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                  
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" type="submit" id="viewBtn">View Report</button>
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
        placeholder: 'Select orderbooker...'
    });

    </script>
@endsection
