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
                            <span class="input-group-text">Product</span>
                            <select name="productID" class="form-control selectize">
                                <option value="">All</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" @selected($product->id == request('productID'))>
                                        {{ $product->name }} ({{ $product->vendor->title }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Vendor</span>
                            <select name="vendorID" class="form-control selectize">
                                <option value="">All</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" @selected($vendor->id == request('vendorID'))>
                                        {{ $vendor->title }}</option>
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
                    <div class="col-md-2">
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
                    <h3>Targets</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new">Create
                        New</button>
                </div>
                <div class="card-body">
                    <table class="table" id="buttons-datatables">
                        <thead>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Order Booker</th>
                            <th>Product</th>
                            <th>Vendor</th>
                            <th>Unit</th>
                            <th>Target</th>
                            <th>Achieved</th>
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
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->vendor->title }}</td>
                                    <td>{{ $item->unit->unit_name }} - {{ $item->unit_value }}</td>
                                    <td>{{ $item->pc / $item->unit_value }}</td>
                                    <td>{{ $item->sold }} - {{ number_format($item->totalPer, 2) }}%</td>
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
                                                        onclick="newWindow('{{ route('targets.show', $item->id) }}')"
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
                                                    <a class="dropdown-item text-danger"
                                                        href="{{ route('target.delete', $item->id) }}">
                                                        <i class="ri-delete-bin-2-fill align-bottom me-2 text-danger"></i>
                                                        Delete
                                                    </a>
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
                                                <h5 class="modal-title" id="myModalLabel">Edit Target</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"> </button>
                                            </div>
                                            <form action="{{ route('targets.update', $item->id) }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="orderbookerID">Order Booker</label>
                                                                <input type="text" name="orderbookerID"
                                                                    class="form-control"
                                                                    value="{{ $item->orderbooker->name }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="productID">Product</label>
                                                                <input type="text" name="productID"
                                                                    class="form-control"
                                                                    value="{{ $item->product->name }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="unitID">Unit</label>
                                                                <input type="text" name="unitID" class="form-control"
                                                                    value="{{ $item->unit->unit_name }}" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-2">
                                                                <label for="target">Target Qty</label>
                                                                <input type="number" name="target" id="target"
                                                                    class="form-control"
                                                                    value="{{ $item->pc / $item->unit->value }}">
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
                    <h5 class="modal-title" id="myModalLabel">Create Target</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('targets.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="orderbookerID">Order Booker</label>
                                    <select name="orderbookerID" id="orderbookerID" onchange="getProducts(this.value)"
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
                                    <label for="productID">Product</label>
                                    <select name="productID" id="productID" onchange="getUnits(this.value)"
                                        class="productID">
                                        <option value="">Select Product</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="unitID">Unit</label>
                                    <select name="unitID" id="unitID" class="unitID selectize">
                                        <option value="">Select Unit</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-2">
                                    <label for="target">Target Qty</label>
                                    <input type="number" name="target" id="target" class="form-control">
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
        var productSelect = $("#productID").selectize()[0].selectize;
        var unitSelect = $(".unitID").selectize()[0].selectize;

        function getProducts(orderbookerID) {
            $.ajax({
                url: "{{ route('getOrderbookerProducts', ':orderbookerID') }}".replace(':orderbookerID',
                    orderbookerID),
                type: "GET",
                success: function(response) {
                    productSelect.clear();
                    productSelect.clearOptions();

                    response.forEach(function(item) {
                        productSelect.addOption({
                            value: item.value,
                            text: item.text + ' (' + item.vendor + ')'
                        });
                    });

                    productSelect.refreshOptions(false);
                }
            });
        }

        function getUnits(productID) {
            $.ajax({
                url: "{{ route('getUnits', ':productID') }}".replace(':productID',
                    productID),
                type: "GET",
                success: function(response) {
                    unitSelect.clear();
                    unitSelect.clearOptions();
                    console.log(response);

                    response.forEach(function(item) {
                        unitSelect.addOption({
                            value: item.value,
                            text: item.text + " - " + item.unit_value
                        });
                    });

                    unitSelect.refreshOptions(false);
                }
            });
        }
    </script>
@endsection
