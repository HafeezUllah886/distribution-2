@extends('layout.app')
@section('content')
    <div class="row d-flex justify-content-center">
        <div class="col-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Create Auto Staff Payments</h3>
                </div>
                <form action="{{ route('auto_staff_payments.create') }}" method="get">
                <div class="card-body">
                    
                            <div class="form-group mt-2">
                                <label for="staff">Staff</label>
                                <select name="staff" id="staff" class="selectize">
                                    @foreach ($staff as $staff)
                                        <option value="{{$staff->id}}">{{$staff->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                       
                            <div class="form-group mt-2">
                                <label for="method">Method</label>
                                <select name="method" id="method" class="form-control">
                                    <option value="Online">Online</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group mt-2">
                                <label for="forward">Forward To </label>
                                <select name="forward" id="forward" class="form-control">
                                    <option value="Vendor">Vendor</option>
                                    <option value="Business">Business</option>
                                </select>
                            </div>
                       
                    </div>
                  
                    <div class="form-group mt-2">
                        <button class="btn btn-success w-100" type="submit" id="viewBtn">Create</button>
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
        
    });

    </script>
@endsection
