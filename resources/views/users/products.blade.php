@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Order Booker Products - {{$orderbooker->name}}</h3>
                     </div>
                     <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{route('orderbookerproducts.store')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="productID" class="selectize" id="vendor">
                                                        <option value="All">All</option>
                                                        @foreach ($vendors as $ven)
                                                            <option value="{{$ven->id}}" @selected($ven->id == $vendor)>{{$ven->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="productID" class="selectize" id="product">
                                                        @foreach ($products as $product)
                                                            <option value="{{$product->id}}">{{$product->name}}</option>
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
                                            <h4>Assigned Products</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                       <th>#</th>
                                                       <th>Product</th>
                                                       <th>Vendor</th>
                                                       <th>Action</th>
                                                </thead>
                                                <tbody>
                                                       @foreach ($orderbooker_products as $key => $product)
                                                              <tr>
                                                                     <td>{{$key+1}}</td>
                                                                     <td>{{$product->product->name}}</td>
                                                                     <td>{{$product->product->vendor->title}}</td>
                                                                     <td>
                                                                            <a href="{{ route('orderbookerproduct.delete', $product->id) }}" class="btn btn-danger" >Remove</a>
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

    $("#vendor").on('change', function (){
        var vendor = $(this).find(":selected").val();
        var orderbooker = "{{$orderbooker->id}}";

        if(vendor == ''){
           return false;
        }
        
        var url = "{{ route('orderbookerproduct.show', [':orderbooker', ':vendor']) }}"
        .replace(':orderbooker', orderbooker)
        .replace(':vendor', vendor);

        window.open(url, "_self");
    });
</script>
@endsection

