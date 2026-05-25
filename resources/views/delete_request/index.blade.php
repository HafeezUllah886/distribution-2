@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row g-1">

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" name="from" class="form-control" value="{{ $from }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" name="to" class="form-control" value="{{ $to }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Status</span>
                            <select name="status" class="form-control" id="">
                                <option value="all">All</option>
                                <option value="pending" @selected($status == 'pending')>Pending</option>
                                <option value="approved" @selected($status == 'approved')>Approved</option>
                                <option value="rejected" @selected($status == 'rejected')>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Requested By</span>
                            <select name="requested_by" class="form-control">
                                <option value="all">All</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected($requested_by == $user->id)>{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Model</span>
                            <select name="model_filter" class="form-control">
                                <option value="all">All</option>
                                @foreach ($models as $mdl)
                                    <option value="{{ $mdl }}" @selected($model_filter == $mdl)>
                                        {{ ucwords(str_replace('_', ' ', $mdl)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Delete Requests</h3>

                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Requested by</th>
                            <th>Date</th>
                            <th>Ref #</th>
                            <th>Model</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($delete_req as $key => $req)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $req->user->name }}</td>
                                    <td>{{ $req->created_at->format('d M Y') }}</td>
                                    <td>{{ $req->refID }}</td>
                                    <td>{{ $req->model }}</td>
                                    <td>{{ $req->notes }}</td>
                                    <td>
                                        @if ($req->status == 'pending')
                                            <span class="badge bg-warning">{{ $req->status }}</span>
                                        @elseif($req->status == 'approved')
                                            <span class="badge bg-success">{{ $req->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $req->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($req->status == 'pending')
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#approveModal{{ $req->id }}">
                                                            <i class="ri-check-line align-bottom me-2 text-muted"></i>
                                                            Approve
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#rejectModal{{ $req->id }}">
                                                            <i class="ri-close-line align-bottom me-2 text-muted"></i>
                                                            Reject
                                                        </a>
                                                    </li>
                                                @endif

                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="{{ route('delete_request.delete', $req->id) }}"
                                                        onclick="return confirm('Are you sure you want to delete this request record?')">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                        Delete
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
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
    @foreach ($delete_req as $req)
        @if ($req->status == 'pending')
            <!-- Approve Modal -->
            <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1"
                aria-labelledby="approveModalLabel{{ $req->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="approveModalLabel{{ $req->id }}">Approve Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('delete_request.approve', $req->id) }}" method="GET">
                            <div class="modal-body pb-0 text-start">
                                <div class="mb-3">
                                    <label for="approvalNotes{{ $req->id }}" class="form-label">Approval Notes</label>
                                    <textarea class="form-control" name="notes" id="approvalNotes{{ $req->id }}" rows="3" required
                                        placeholder="Enter approval notes..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Approve Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1"
                aria-labelledby="rejectModalLabel{{ $req->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel{{ $req->id }}">Reject Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('delete_request.reject', $req->id) }}" method="GET">
                            <div class="modal-body pb-0 text-start">
                                <div class="mb-3">
                                    <label for="rejectionNotes{{ $req->id }}" class="form-label">Rejection Notes</label>
                                    <textarea class="form-control" name="notes" id="rejectionNotes{{ $req->id }}" rows="3" required
                                        placeholder="Enter reason for rejection..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Reject Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach


@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/selectize/selectize.min.css') }}">
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

    <script src="{{ asset('assets/libs/selectize/selectize.min.js') }}"></script>
@endsection
