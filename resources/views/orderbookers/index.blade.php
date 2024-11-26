@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Order Bookers
                            </h3>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                     </div>
                     <div class="card-body">
                            <table class="table">
                                   <thead>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Email</th>
                                          <th>Contact</th>
                                          <th>Action</th>
                                   </thead>
                                   <tbody>
                                          @foreach ($orderbookers as $key => $booker)
                                                 <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$booker->name}}</td>
                                                        <td>{{$booker->email}}</td>
                                                        <td>{{$booker->contact}}</td>
                                                        <td>
                                                               <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#edit_{{$booker->id}}">Edit</button>
                                                        </td>
                                                 </tr>
                                                 <div id="edit_{{$booker->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="myModalLabel">Edit Order Booker</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                                </div>
                                                                <form action="{{ route('orderbooker.update', $booker->id) }}" method="Post">
                                                                  @csrf
                                                                  @method("patch")
                                                                         <div class="modal-body">
                                                                             <div class="form-group">
                                                                                    <label for="name">Name</label>
                                                                                    <input type="text" name="name" value="{{$booker->name}}" required id="name" class="form-control">
                                                                             </div>
                                                                             <div class="form-group mt-2">
                                                                                    <label for="email">Email</label>
                                                                                    <input type="email" name="email" value="{{$booker->email}}" required id="email" class="form-control">
                                                                             </div>
                                                                             <div class="form-group mt-2">
                                                                                    <label for="contact">Contact</label>
                                                                                    <input type="text" name="contact" value="{{$booker->contact}}" id="contact" class="form-control">
                                                                             </div>
                                                                             <div class="form-group mt-2">
                                                                                    <label for="password">Password</label>
                                                                                    <input type="password" name="password" id="password" autocomplete="false" class="form-control">
                                                                             </div>
                                                                         </div>
                                                                         <div class="modal-footer">
                                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Update</button>
                                                                         </div>
                                                                  </form>
                                                            </div><!-- /.modal-content -->
                                                        </div><!-- /.modal-dialog -->
                                                    </div><!-- /.modal -->
                                          @endforeach
                                   </tbody>
                            </table>
                     </div>
              </div>
       </div>
</div>
<!-- Default Modals -->

<div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Create New Order Booker</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form action="{{ route('orderbooker.store') }}" method="post">
              @csrf
                     <div class="modal-body">
                            <div class="form-group">
                                   <label for="name">Name</label>
                                   <input type="text" name="name" required id="name" class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                   <label for="email">Email</label>
                                   <input type="email" name="email" required id="email" class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                   <label for="contact">Contact</label>
                                   <input type="text" name="contact" id="contact" class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                   <label for="password">Password</label>
                                   <input type="password" name="password" required id="password" class="form-control">
                            </div>
                     </div>
                     <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                     </div>
              </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

