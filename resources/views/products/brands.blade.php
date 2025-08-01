@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Product Brands</h3>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                     </div>
                     <div class="card-body">
                            <table class="table">
                                   <thead>
                                          <th>#</th>
                                          <th>Brand</th>
                                          <th>Action</th>
                                   </thead>
                                   <tbody>
                                          @foreach ($brands as $key => $brand)
                                                 <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$brand->name}}</td>
                                                        <td>
                                                               <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#edit_{{$brand->id}}">Edit</button>
                                                        </td>
                                                 </tr>
                                                 <div id="edit_{{$brand->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="myModalLabel">Edit Brand</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                                </div>
                                                                <form action="{{ route('brands.update', $brand->id) }}" method="Post">
                                                                  @csrf
                                                                  @method("patch")
                                                                         <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label for="name">Name</label>
                                                                                    <input type="text" name="name" required value="{{$brand->name}}" id="name" class="form-control">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="branchID">Branch</label>
                                                                                    <select name="branchID" id="branchID" class="form-control">
                                                                                        @foreach ($branches as $branch)
                                                                                            <option value="{{$branch->id}}" @if ($brand->branchID == $branch->id) selected @endif>{{$branch->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                         </div>
                                                                         <div class="modal-footer">
                                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                                <button type="submit" class="btn btn-primary">Save</button>
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
                <h5 class="modal-title" id="myModalLabel">Create New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form action="{{ route('brands.store') }}" method="post">
              @csrf
                     <div class="modal-body">
                        <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" required id="name" class="form-control">
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                     </div>
              </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

