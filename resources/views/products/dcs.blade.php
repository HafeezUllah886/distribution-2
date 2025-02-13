@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Area Wise Product Delivery Charges - {{$product->name}}</h3>
                     </div>
                     <div class="card-body">
                            <form action="{{route('dc.update', $product->id)}}" method="post">
                                   @csrf
                                   @method('PUT')
                           
                            <table class="table">
                                   <thead>
                                          <th>#</th>
                                          <th>Area</th>
                                          <th>Value</th>
                                   </thead>
                                   <tbody>
                                          @foreach ($areas as $key => $area)
                                                 <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$area->name}}</td>
                                                        <td><input type="number" name="value[]" id="value" step="any" value="{{$area->dc}}" class="form-control"></td>
                                                        <input type="hidden" name="areaID[]" value="{{$area->id}}">
                                                 </tr>
                                          @endforeach
                                   </tbody>
                            </table>
                            <button type="submit" class="btn btn-success btn-lg w-100">Save</button>
                     </form>
                     </div>
              </div>
       </div>
</div>

@endsection

