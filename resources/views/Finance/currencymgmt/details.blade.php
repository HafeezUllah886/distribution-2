@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Currencies - {{$user->name}}</h3>
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
                                                        <td>{{getCurrencyBalance($currency->id, $user->id)}}</td>

                                                        <td>
                                                               <button class="btn btn-info" href="javascript:void(0);"
                                                               onclick="ViewStatment({{ $currency->id }})">
                                                               View Statment
                                                           </button>
                                                        </td> 
                                                 </tr>
                                                   
                                          @endforeach
                                   </tbody>
                            </table>
                     </div>
              </div>
       </div>
</div>
<!-- Default Modals -->

<div id="viewStatmentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
       <div class="modal-dialog">
           <div class="modal-content">
               <div class="modal-header">
                   <h5 class="modal-title" id="myModalLabel">View Currency Statment</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
               </div>
               <form method="get" target="" id="form">
                 @csrf
                 <input type="hidden" name="currencyID" id="currencyID">
                 
                        <div class="modal-body">
                          <div class="form-group">
                           <label for="">Select Dates</label>
                           <div class="input-group">
                               <span class="input-group-text" id="inputGroup-sizing-default">From</span>
                               <input type="date" id="from" name="from" value="{{ firstDayOfMonth() }}" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                               <span class="input-group-text" id="inputGroup-sizing-default">To</span>
                               <input type="date" id="to" name="to" value="{{ lastDayOfMonth() }}" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                           </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                               <button type="button" id="viewBtn" class="btn btn-primary">View</button>
                        </div>
                 </form>
           </div><!-- /.modal-content -->
       </div><!-- /.modal-dialog -->
   </div><!-- /.modal -->

@endsection

@section('page-js')

<script>
       function ViewStatment(account)
       {
           $("#currencyID").val(account);
           $("#viewStatmentModal").modal('show');
       }

       $("#viewBtn").on("click", function (){
           var currencyID = $("#currencyID").val();
           var userID = "{{ $user->id }}";
           var from = $("#from").val();
           var to = $("#to").val();
           var url = "{{ route('currency.statement', ['id' => ':currencyID', 'user' => ':userID', 'from' => ':from', 'to' => ':to']) }}"
       .replace(':currencyID', currencyID)
       .replace(':userID', userID)
       .replace(':from', from)
       .replace(':to', to);
           window.open(url, "_blank", "width=1000,height=800");
       });
   </script>
       
@endsection

