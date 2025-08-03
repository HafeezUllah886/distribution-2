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
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="area" class="selectize" id="area">
                                                        <option value="All">All</option>
                                                        @foreach ($areas as $ar)
                                                            <option value="{{$ar->id}}" @selected($ar->id == $area)>{{$ar->name}} | {{$ar->town->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="customerID" class="selectize" id="customer">
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
                                                       <th>Town</th>
                                                       <th>Area</th>
                                                       <th>Action</th>
                                                </thead>
                                                <tbody>
                                                       @foreach ($orderbooker_customers as $key => $customer)
                                                              <tr>
                                                                     <td>{{$key+1}}</td>
                                                                     <td>{{$customer->customer->title}}</td>
                                                                     <td>{{$customer->customer->area->town->name}}</td>
                                                                     <td>{{$customer->customer->area->name}}</td>
                                                                     <td>
                                                                            <a href="{{ route('orderbookercustomer.delete', $customer->id) }}" class="btn btn-danger" >Remove</a>
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

    $("#area").on('change', function (){
        var area = $(this).find(":selected").val();
        var orderbooker = "{{$orderbooker->id}}";

        if(area == ''){
           return false;
        }
        
        var url = "{{ route('orderbookercustomer.show', [':orderbooker', ':area']) }}"
        .replace(':orderbooker', orderbooker)
        .replace(':area', area);

        window.open(url, "_self");
    });
</script>
@endsection

