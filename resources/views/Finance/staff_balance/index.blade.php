@extends('layout.app')
@section('content')
    <div class="row">
        <div class="col-12">
            <form>
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">From</span>
                            <input type="date" class="form-control" placeholder="Username" name="start" value="{{$start}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">To</span>
                            <input type="date" class="form-control" placeholder="Username" name="end" value="{{$end}}" aria-label="Username" aria-describedby="basic-addon1">
                        </div>
                    </div>
                    <div class="col-md-2">
                       <input type="submit" value="Filter" class="btn btn-success w-100">
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3>My Balance</h3>
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

                    <table class="table table-bordered" >
                        <thead>
                            <th>Legder</th>
                            <th>Opening Balance</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Balance till {{ $end }}</th> 
                            <th>Current Balance</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <tr>
                                
                                <td class="fw-bold">Main Ledger</td>
                                <td class="fw-bold">{{ number_format($mainAccountOpeningBalance, 2) }}</td>
                                <td class="fw-bold">{{ number_format($mainAccountCredit, 2) }}</td>
                                <td class="fw-bold">{{ number_format($mainAccountDebit, 2) }}</td>
                                <td class="fw-bold">{{ number_format($mainAccountBalanceTillDate, 2) }}</td>
                                <td class="fw-bold">{{ number_format($mainAccountBalance, 2) }}</td>
                                <td><a href="{{ route('otherusers.self_statement', ['id' => auth()->user()->id, 'from' => $start, 'to' => $end]) }}" class="btn btn-primary" target="_blank"> Statement</a></td>
                            </tr>
                            <tr>
                                <td colspan="7" class="fw-bold">Payment Methods Ledgers</td>
                            </tr>
                            @foreach ($methodData as $key => $method)
                                <tr>
                                    <td>{{ $method['name'] }}</td>
                                    <td>{{ number_format($method['opening_balance'], 2) }}</td>
                                    <td>{{ number_format($method['credit'], 2) }}</td>
                                    <td>{{ number_format($method['debit'], 2) }}</td>
                                    <td>{{ number_format($method['balance_till_date'], 2) }}</td>
                                    <td>{{ number_format($method['balance'], 2) }}</td>
                                    <td><a href="{{ route('methodStatement', ['user' => auth()->user()->id, 'method' => $method['name'], 'from' => $start, 'to' => $end]) }}" class="btn btn-primary" target="_blank"> Statement</a></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="7" class="fw-bold">Currency Ledgers</td>
                            </tr>
                            @foreach ($currencies as $key => $currency)
                                <tr>
                                    <td>{{ $currency->title }}</td>
                                    <td>{{ number_format($currency->opening_balance, 2) }}</td>
                                    <td>{{ number_format($currency->credit, 2) }}</td>
                                    <td>{{ number_format($currency->debit, 2) }}</td>
                                    <td>{{ number_format($currency->opening_balance + $currency->credit - $currency->debit, 2) }}</td>
                                    <td>{{ number_format($currency->balance, 2) }}</td>
                                    <td><a href="{{ route('currency.statement', ['id' => $currency->id, 'user' => auth()->user()->id, 'from' => $start, 'to' => $end]) }}" class="btn btn-primary" target="_blank"> Statement</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
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
@endsection
