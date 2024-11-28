@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Currencies - {{$account->title}}</h3>
                     </div>
                     <div class="card-body">
                            <table class="table">
                                   <thead>
                                          <th>#</th>
                                          <th>Currency</th>
                                          <th>Balance</th>
                                        {{--   <th>Action</th> --}}
                                   </thead>
                                   <tbody>
                                          @foreach ($currencies as $key => $currency)
                                                 <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$currency->title}}</td>
                                                        <td>{{getCurrencyBalance($currency->id, $account->id)}}</td>

                                                        {{-- <td>
                                                               <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#edit_{{$unit->id}}">Edit</button>
                                                        </td> --}}
                                                 </tr>
                                                    </div><!-- /.modal -->
                                          @endforeach
                                   </tbody>
                            </table>
                     </div>
              </div>
       </div>
</div>
<!-- Default Modals -->

@endsection

