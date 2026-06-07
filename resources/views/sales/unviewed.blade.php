@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">

                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Area</span>
                            <select name="areaID" id="areaID" class="form-control">
                                <option value="">All</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" @selected($area->id == $areaID)>
                                        {{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Supply Man</span>
                            <select name="supplymanID" id="supplymanID" class="form-control">
                                <option value="">All</option>
                                @foreach ($supplymans as $supplyman)
                                    <option value="{{ $supplyman->id }}" @selected($supplyman->id == $supplymanID)>
                                        {{ $supplyman->title }}</option>
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
                    <h3>Un Viewed Sales</h3>

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
                            <th>Date</th>
                            <th>Inv No.</th>
                            <th>Customer</th>
                            <th>Area</th>
                            <th>Address</th>
                            <th>Order Booker</th>
                            <th>Supply Man</th>
                            <th>Notes</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Remark</th>
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
                                    <td>{{ date('d M Y', strtotime($sale->date)) }}</td>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->customer->title }}</td>
                                    <td>{{ $sale->customer->area->name }}</td>
                                    <td>{{ $sale->customer->address }}</td>
                                    <td>{{ $sale->orderbooker->name }}</td>
                                    <td>{{ $sale->supplyman->title }}</td>
                                    <td>{{ $sale->notes }}</td>
                                    <td>{{ number_format($amount) }}</td>
                                    <td>{{ number_format($paid) }}</td>
                                    <td>{{ number_format($due) }}</td>
                                    <td>{{ $sale->remarks }}</td>
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
                                                </li>
                                                @if (auth()->user()->role == 'Branch Admin')
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('sale.markasviewed', $sale->id) }}">
                                                            <i class="ri-check-line align-bottom me-2 text-muted"></i>
                                                            Mark as Received
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" data-bs-toggle="modal"
                                                            data-bs-target="#remarkModal_{{ $sale->id }}">
                                                            <i class="ri-edit-fill align-bottom me-2 text-muted"></i>
                                                            Add Remark
                                                        </a>
                                                    </li>
                                                @endif

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <div id="remarkModal_{{ $sale->id }}" class="modal fade" tabindex="-1"
                                    aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="myModalLabel">Add Remark</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('sale.addRemark') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="saleID" value="{{ $sale->id }}">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="warehouseID">Remark</label>
                                                        <input type="text" name="remarks" value="{{ $sale->remarks }}"
                                                            id="remark" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Add</button>
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
