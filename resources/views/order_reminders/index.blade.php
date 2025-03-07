@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$from}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$to}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Status</span>
                            <select class="form-control" name="status" aria-label="Username" aria-describedby="basic-addon1">
                                <option value="All">All</option>
                                <option value="Pending" @selected($status == 'Pending')>Pending</option>
                                <option value="Completed" @selected($status == 'Completed')>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                       <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>Order Reminders</h3>
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
                            <th>Order Booker</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Loose</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($reminders as $key => $reminder)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $reminder->orderbooker }}</td>
                                    <td>{{ $reminder->customer }}</td>
                                    <td>{{ $reminder->product }}</td>
                                    <td>{{ $reminder->unit }}</td>
                                    <td>{{ $reminder->qty }}</td>
                                    <td>{{ $reminder->loose }}</td>
                                    <td>{{ date('d M Y', strtotime($reminder->date)) }}</td>
                                    <td>{{ $reminder->status }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($reminder->status != "Completed")
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reminder.update', ['id' => $reminder->id, 'status' => 'Completed']) }}"><i
                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Mark As Complete
                                                    </a>
                                                </li>
                                                @else
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reminder.update', ['id' => $reminder->id, 'status' => 'Pending']) }}"><i
                                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Mark As Pending
                                                    </a>
                                                </li>
                                                @endif
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
@endsection

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/datatable.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('assets/libs/datatable/responsive.bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/libs/datatable/buttons.dataTables.min.css') }}">
@endsection
@section('page-js')
    <script src="{{ asset('assets/libs/datatable/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.print.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/vfs_fonts.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/pdfmake.min.js')}}"></script>
    <script src="{{ asset('assets/libs/datatable/jszip.min.js')}}"></script>

    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script>
        function finalizeOrder(order)
        {
            $("#orderID").val(order);
            $("#finalizeOrderModal").modal('show');
        }

        $("#finalizeBtn").on("click", function (){      
            var orderID = $("#orderID").val();
            var warehouseID = $("#warehouseID").find(':selected').val();
            var url = "{{ route('Branch.orders.finalize', ['id' => ':orderID', 'warehouseID' => ':warehouseID']) }}"
        .replace(':orderID', orderID)
        .replace(':warehouseID', warehouseID);
            window.open(url, "_blank", "width=1000,height=800");
        });
    </script>
@endsection