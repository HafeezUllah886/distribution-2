@extends('layout.app')
@section('content')
<div class="row">
       <div class="col-12">
              <div class="card">
                     <div class="card-header d-flex justify-content-between">
                            <h3>{{$type}}s</h3>
                            <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create New</button>
                     </div>
                     <div class="card-body">
                            <table class="table">
                                   <thead>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Contact</th>
                                          <th>Balance</th>
                                          <th>Action</th>
                                   </thead>
                                   <tbody>
                                          @foreach ($users as $key => $user)
                                                 <tr>
                                                        <td>{{$key+1}}</td>
                                                        <td>{{$user->name}}</td>
                                                        <td>{{$user->contact}}</td>
                                                        <td>{{getUserAccountBalance($user->id)}}</td>
                                                        <td>
                                                               <div class="dropdown">
                                                                      <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                                          data-bs-toggle="dropdown" aria-expanded="false">
                                                                          <i class="ri-more-fill align-middle"></i>
                                                                      </button>
                                                                      <ul class="dropdown-menu dropdown-menu-end">
                                                                        <li>
                                                                            <button class="dropdown-item" href="javascript:void(0);"
                                                                                onclick="ViewStatment({{ $user->id }})"><i
                                                                                    class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                                                View Statment
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{route('currency.details', $user->id)}}">
                                                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                                                Currency Details
                                                                            </a>
                                                                        </li>
                                                                          <li>
                                                                              <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#edit_{{$user->id}}">
                                                                                  <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                                  Edits
                                                                              </a>
                                                                          </li>

                                                                          <li>
                                                                              <a class="dropdown-item text-{{$user->status == "Blocked" ? "success" : "danger"}}" href="{{route('otherusers.status', [$user->id])}}">
                                                                                  <i class="ri-user-settings-fill align-bottom me-2 text-muted"></i>
                                                                                  {{$user->status == "Blocked" ? "Active" : "Block"}}
                                                                              </a>
                                                                          </li>

                                                                      </ul>
                                                                  </div>
                                                        </td>
                                                 </tr>
                                                 <div id="edit_{{$user->id}}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="myModalLabel">Edit {{$type}}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                                                </div>
                                                                <form action="{{ route('otherusers.update', $user->id) }}" method="Post">
                                                                  @csrf
                                                                  @method("patch")
                                                                         <div class="modal-body">
                                                                             <div class="form-group mt-2">
                                                                                    <label for="contact">Contact</label>
                                                                                    <input type="text" name="contact" value="{{$user->contact}}" required id="contact" class="form-control">
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
                <h5 class="modal-title" id="myModalLabel">Create New {{$type}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form action="{{ route('otherusers.store', [$type]) }}" method="post">
              @csrf
                     <div class="modal-body">
                            <div class="form-group">
                                   <label for="name">Name</label>
                                   <input type="text" name="name" required id="name" class="form-control">
                            </div>
                            <div class="form-group mt-2">
                                <label for="contact">Contact</label>
                                <input type="text" name="contact" required id="contact" class="form-control">
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

<div id="viewStatmentModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">View Account Statment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <form method="get" target="" id="form">
              @csrf
              <input type="hidden" name="accountID" id="accountID">
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

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>

    <script>
        function ViewStatment(account)
        {
            $("#accountID").val(account);
            $("#viewStatmentModal").modal('show');
        }

        $("#viewBtn").on("click", function (){
            var accountID = $("#accountID").val();
            var from = $("#from").val();
            var to = $("#to").val();
            var url = "{{ route('accountStatement', ['id' => ':accountID', 'from' => ':from', 'to' => ':to']) }}"
        .replace(':accountID', accountID)
        .replace(':from', from)
        .replace(':to', to);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection

