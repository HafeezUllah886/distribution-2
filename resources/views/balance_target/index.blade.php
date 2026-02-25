@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">From</span>
                            <input type="date" class="form-control" name="start" value="{{ $start }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">To</span>
                            <input type="date" class="form-control" name="end" value="{{ $end }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Orderbooker</span>
                            <select name="orderbookerID" class="form-control selectize">
                                <option value="">All</option>
                                @foreach ($orderbookers as $orderbooker)
                                    <option value="{{ $orderbooker->id }}" @selected($orderbooker->id == request('orderbookerID'))>
                                        {{ $orderbooker->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Customer</span>
                            <select name="customerID" class="form-control selectize">
                                <option value="">All</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected($customer->id == request('customerID'))>
                                        {{ $customer->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Achievement</span>
                            <select name="achievement" class="form-control">
                                <option value="">All</option>
                                <option value="Achieved" @selected(request('achievement') == 'Achieved')>Achieved</option>
                                <option value="In Progress" @selected(request('achievement') == 'In Progress')>In Progress</option>
                                <option value="Not Achieved" @selected(request('achievement') == 'Not Achieved')>Not Achieved</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Status</span>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="Open" @selected(request('status') == 'Open')>Open</option>
                                <option value="Closed" @selected(request('status') == 'Closed')>Closed</option>
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
                    <h3>Balance Targets</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Order Booker</th>
                            <th>Customer</th>
                            <th>Start Balance</th>
                            <th>Target Balance</th>
                            <th>Current Balance</th>
                            <th>Achievement</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($targets as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->branch->name }}</td>
                                    <td>{{ $item->orderbooker->name }}</td>
                                    <td>{{ $item->customer->title }}</td>
                                    <td>{{ number_format($item->start_value, 2) }}</td>
                                    <td>{{ number_format($item->target_value, 2) }}</td>
                                    <td>{{ number_format($item->current_balance, 2) }}</td>
                                    <td>{{ number_format($item->totalPer, 2) }}%</td>
                                    <td>{{ date('d M Y', strtotime($item->startDate)) }}
                                        <br>{{ date('d M Y', strtotime($item->endDate)) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->campain_color }}">{{ $item->campain }}</span>
                                        <br>
                                        <span class="badge bg-{{ $item->goal_color }}">{{ $item->goal }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item"
                                                        onclick="newWindow('{{ route('balance_targets.show', $item->id) }}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#edit{{ $item->id }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Edit
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('balance_targets.destroy', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i
                                                                class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <div id="edit{{ $item->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Edit Balance Target</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('balance_targets.update', $item->id) }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="orderbookerID">Order Booker</label>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $item->orderbooker->name }}" readonly>
                                                                <input type="hidden" name="orderbookerID"
                                                                    value="{{ $item->orderbookerID }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="customerID">Customer</label>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $item->customer->title }}" readonly>
                                                                <input type="hidden" name="customerID"
                                                                    value="{{ $item->customerID }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="start_value">Start Balance</label>
                                                                <input type="number" step="0.01" name="start_value"
                                                                    id="start_value" class="form-control"
                                                                    value="{{ $item->start_value }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="target_value">Target Balance</label>
                                                                <input type="number" step="0.01" name="target_value"
                                                                    id="target_value" class="form-control"
                                                                    value="{{ $item->target_value }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="startDate">Start Date</label>
                                                                <input type="date" name="startDate" id="startDate"
                                                                    class="form-control" value="{{ $item->startDate }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="endDate">End Date</label>
                                                                <input type="date" name="endDate" id="endDate"
                                                                    class="form-control" value="{{ $item->endDate }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group mt-2">
                                                                <label for="notes">Notes</label>
                                                                <textarea name="notes" class="form-control">{{ $item->notes }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">Close</button>
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
    <div id="new" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Balance Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('balance_targets.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="orderbookerID">Order Booker</label>
                                    <select name="orderbookerID" id="orderbookerID" onchange="getCustomers(this.value)"
                                        class="selectize">
                                        <option value="">Select Order Booker</option>
                                        @foreach ($orderbookers as $orderbooker)
                                            <option value="{{ $orderbooker->id }}">{{ $orderbooker->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="customerID">Customer</label>
                                    <select name="customerID" id="customerID" onchange="getCustomerBalance()"
                                        class="customerID">
                                        <option value="">Select Customer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="start_value">Closing Balance</label>
                                    <input type="number" step="0.01" readonly name="start_value" id="start_value"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="target_value">Target Balance</label>
                                    <input type="number" step="0.01" name="target_value" id="target_value"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="startDate">Start Date</label>
                                    <input type="date" name="startDate" id="startDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="endDate">End Date</label>
                                    <input type="date" name="endDate" id="endDate" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-2">
                                    <label for="notes">Notes</label>
                                    <textarea name="notes" class="form-control"></textarea>
                                </div>
                            </div>
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
    <script>
        var orderbookerSelect = $("#orderbookerID").selectize()[0].selectize;
        var customerSelect = $("#customerID").selectize()[0].selectize;

        function getCustomers(orderbookerID) {
            $.ajax({
                url: "{{ route('getOrderbookerCustomers', ':orderbookerID') }}".replace(':orderbookerID',
                    orderbookerID),
                type: "GET",
                success: function(response) {
                    customerSelect.clear();
                    customerSelect.clearOptions();

                    response.forEach(function(item) {
                        customerSelect.addOption({
                            value: item.value,
                            text: item.text
                        });
                    });

                    customerSelect.refreshOptions(false);
                }
            });
        }

        function getCustomerBalance() {
            console.log(orderbookerSelect.getValue());
            console.log(customerSelect.getValue());
            var orderbookerID = orderbookerSelect.getValue();
            var customerID = customerSelect.getValue();
            $.ajax({
                url: "{{ url('/customerbalance/:id/:orderbookerID') }}".replace(':id',
                        customerID)
                    .replace(':orderbookerID', orderbookerID),
                type: "GET",
                success: function(response) {
                    $('#start_value').val(response.data);
                    console.log(response);
                }
            });
        }
    </script>
@endsection
