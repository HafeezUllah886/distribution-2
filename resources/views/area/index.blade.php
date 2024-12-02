@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>Towns & Areas</h3>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create Town</button>
                     </div>
                     <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="{{route('areas.store')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <select name="townID" class="selectize" id="townID">
                                                        @foreach ($towns as $town)
                                                            <option value="{{$town->id}}">{{$town->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="form-group">
                                                    <input type="text" name="name" placeholder="Area Name" required id="name" class="form-control">
                                             </div>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-success w-100">Create Area</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Towns</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                       <th>#</th>
                                                       <th>Name</th>
                                                       <th>Action</th>
                                                </thead>
                                                <tbody>
                                                       @foreach ($towns as $key => $town)
                                                              <tr>
                                                                     <td>{{$key+1}}</td>
                                                                     <td>{{$town->name}}</td>
                                                                     <td>
                                                                            <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#edit_{{$town->id}}">Edit</button>
                                                                     </td>
                                                              </tr>
                                                              <div id="edit_{{$town->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                                     <div class="modal-dialog">
                                                                         <div class="modal-content">
                                                                             <div class="modal-header">
                                                                                 <h5 class="modal-title" id="myModalLabel">Edit Town</h5>
                                                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                                             </div>
                                                                             <form action="{{ route('towns.update', $town->id) }}" method="Post">
                                                                               @csrf
                                                                               @method("patch")
                                                                                      <div class="modal-body">
                                                                                             <div class="form-group">
                                                                                                    <label for="name">Name</label>
                                                                                                    <input type="text" name="name" required value="{{$town->name}}" id="name" class="form-control">
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
                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Areas</h4>
                                        </div>
                                        <div class="card-body">
                                            <table class="table">
                                                <thead>
                                                       <th>#</th>
                                                       <th>Town</th>
                                                       <th>Area</th>
                                                       <th>Action</th>
                                                </thead>
                                                <tbody>
                                                       @foreach ($areas as $key => $area)
                                                              <tr>
                                                                     <td>{{$key+1}}</td>
                                                                     <td>{{$area->town->name}}</td>
                                                                     <td>{{$area->name}}</td>
                                                                     <td>
                                                                            <button type="button" class="btn btn-info " data-bs-toggle="modal" data-bs-target="#edit1_{{$area->id}}">Edit</button>
                                                                     </td>
                                                              </tr>
                                                              <div id="edit1_{{$area->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                                     <div class="modal-dialog">
                                                                         <div class="modal-content">
                                                                             <div class="modal-header">
                                                                                 <h5 class="modal-title" id="myModalLabel">Edit Area</h5>
                                                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                                             </div>
                                                                             <form action="{{ route('areas.update', $area->id) }}" method="Post">
                                                                               @csrf
                                                                               @method("patch")
                                                                                      <div class="modal-body">
                                                                                             <div class="form-group">
                                                                                                    <label for="townID">Town</label>
                                                                                                    <select name="townID" class="selectize" id="townID">
                                                                                                        @foreach ($towns as $town)
                                                                                                            <option value="{{$town->id}}" @selected($town->id == $area->townID)>{{$town->name}}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                </div>
                                                                                             <div class="form-group">
                                                                                                    <label for="name">Name</label>
                                                                                                    <input type="text" name="name" required value="{{$area->name}}" id="name" class="form-control">
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
                     </div>
              </div>
       </div>
</div>
<!-- Default Modals -->

<div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Create New Town</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form action="{{ route('towns.store') }}" method="post">
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

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
@endsection


@section('page-js')
<script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
<script>
    $(".selectize").selectize();
</script>
@endsection

