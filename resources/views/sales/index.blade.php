@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start"
                                value="{{ $start }}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end"
                                value="{{ $end }}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Orderbooker</span>
                            <select name="orderbookerID" id="orderbookerID" class="form-control">
                                <option value="">All</option>
                                @foreach ($orderbookers as $orderbooker)
                                    <option value="{{ $orderbooker->id }}" @selected($orderbooker->id == $bookerID)>
                                        {{ $orderbooker->name }}</option>
                                @endforeach
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
                    <h3>Sales</h3>
                    @if (auth()->user()->role == 'Operator')
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#new">Create
                            New</button>
                    @endif
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
                            <th>Inv No.</th>
                            <th>Customer</th>
                            <th>Area</th>
                            <th>Order Booker</th>
                            <th>Date</th>
                            <th>Notes</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @foreach ($sales as $key => $sale)
                                @php
                                    $amount = $sale->net;
                                    $paid = $sale->payments->sum('amount');
                                    $due = $amount - $paid;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->customer->title }}</td>
                                    <td>{{ $sale->customer->area->name }}</td>
                                    <td>{{ $sale->orderbooker->name }}</td>
                                    <td>{{ date('d M Y', strtotime($sale->date)) }}</td>
                                    <td>{{ $sale->notes }}</td>
                                    <td>{{ number_format($amount) }}</td>
                                    <td>{{ number_format($paid) }}</td>
                                    <td>{{ number_format($due) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item"
                                                        onclick="newWindow('{{ route('sale.show', $sale->id) }}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View
                                                    </button>
                                                    <button class="dropdown-item"
                                                        onclick="newWindow('{{ route('sale.showUrdu', $sale->id) }}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        View (URDU)
                                                    </button>
                                                </li>
                                                {{--  <li>
                                                    <button class="dropdown-item" onclick="newWindow('{{route('sale.gatePass', $sale->id)}}')"
                                                        onclick=""><i
                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                        Gate Pass
                                                    </button>
                                                </li> --}}
                                                @if (auth()->user()->role == 'Operator' && $sale->edit)
                                                    <li>
                                                        <a class="dropdown-item"
                                                            onclick="newWindow('{{ route('sale.edit', $sale->id) }}')">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#minorEditModal_{{ $sale->id }}">
                                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                        Minor Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        onclick="newWindow('{{ route('salePayment.index', $sale->id) }}')">
                                                        <i
                                                            class="ri-money-dollar-circle-fill align-bottom me-2 text-muted"></i>
                                                        Payments
                                                    </a>
                                                </li>
                                                @if (auth()->user()->role == 'Operator')
                                                    <li>
                                                        <a class="dropdown-item text-danger"
                                                            href="{{ route('sale.delete', $sale->id) }}">
                                                            <i
                                                                class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                            Delete
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <div id="minorEditModal_{{ $sale->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Minor Edit</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('sale.minor_edit') }}" method="get">
                                                <input type="hidden" name="saleID" value="{{ $sale->id }}">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="warehouseID">Bilty</label>
                                                        <input type="text" name="bilty" value="{{ $sale->bilty }}"
                                                            id="bilty" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="warehouseID">Transporter</label>
                                                        <input type="text" name="transporter"
                                                            value="{{ $sale->transporter }}" id="transporter"
                                                            class="form-control">
                                                    </div>
                                                    <div class="form-group mt-2">
                                                        <label for="supplyMan">Supply Man</label>
                                                        <select name="supplymanID" id="supplyMan" class="form-control">
                                                            <option value="">Select Supply Man</option>
                                                            @foreach ($supplymen as $supplyman)
                                                                <option value="{{ $supplyman->id }}"
                                                                    @selected($supplyman->id == $sale->supplymanID)>{{ $supplyman->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('sale.create') }}" method="get">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="warehouseID">Warehouses</label>
                            <select name="warehouseID" id="warehouseID" class="form-control">
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="orderbookerID">Order Booker</label>
                            <select name="orderbookerID" id="orderbookerID" onchange="getCustomers(this.value)"
                                class="form-control">
                                <option value="">Select Order Booker</option>
                                @foreach ($orderbookers as $orderbooker)
                                    <option value="{{ $orderbooker->id }}">{{ $orderbooker->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="customerID">Customer</label>
                            <select name="customerID" id="customerID" class="selectize">
                                <option value="">Select Customer</option>
                            </select>
                        </div>
                        <div class="form-group mt-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}"
                                class="form-control">
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
        $('#customerID').selectize();

        function getCustomers(orderbookerID) {
            var customerSelectize = $('#customerID')[0].selectize; // Access the selectize instance
            if (orderbookerID) {
                // Make an AJAX call to fetch sectors for selected town
                $.ajax({
                    url: '/orderbooker/getcustomers/' + orderbookerID,
                    type: 'GET',
                    success: function(data) {

                        // Clear previous options 
                        customerSelectize.clearOptions();

                        // Add new options
                        customerSelectize.addOption(data); // data should be an array of {value: '', text: ''}
                        customerSelectize.refreshOptions(false);
                    }
                });
            } else {
                customerSelectize.clearOptions();
            }
        }
    </script>
@endsection
