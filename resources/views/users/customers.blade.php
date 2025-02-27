@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Assigned Customers - {{$orderbooker->name}}</h3>
                     </div>
                     <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{route('orderbookercustomers.store')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-group">
                                                    <select name="customerID" class="selectize" id="townID">
                                                        @foreach ($customers as $customer)
                                                            <option value="{{$customer->id}}">{{$customer->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="orderbookerID" value="{{$orderbooker->id}}">
                                            <div class="col-2">
                                                <button class="btn btn-success w-100">Assign</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Assigned Customers</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                       <th>#</th>
                                                       <th>Customer</th>
                                                       <th>Action</th>
                                                </thead>
                                                <tbody>
                                                       @foreach ($orderbooker_customers as $key => $customer)
                                                              <tr>
                                                                     <td>{{$key+1}}</td>
                                                                     <td>{{$customer->customer->title}}</td>
                                                                     <td>
                                                                            <a href="{{ route('orderbookercustomers.destroy', $customer->id) }}" class="btn btn-danger" >Remove</a>
                                                                     </td>
                                                              </tr>
                                                       @endforeach
                                                </tbody>
                                         </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                     </div>
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
    $(".selectize").selectize();
</script>
@endsection

