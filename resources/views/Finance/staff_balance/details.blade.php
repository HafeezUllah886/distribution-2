@extends('layout.popups')
@section('content')
        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                                <a href="javascript:window.print()" class="btn btn-success ml-4"><i class="ri-printer-line mr-4"></i> Print</a>
                            </div>
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h1>{{ Auth()->user()->branch->name }}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Staff Balance ({{ $staff->name }})</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                     
                        <div class="col-lg-12">
                            <table class="table table-bordered" >
                                <thead>
                                    <th>Legder</th>
                                    <th>Current Balance</th>
                                   
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Main Ledger</td>
                                        <td class="fw-bold">{{ number_format($mainAccountBalance, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="fw-bold">Payment Methods Ledgers</td>
                                    </tr>
                                    @foreach ($methodData as $key => $method)
                                        <tr>
                                            <td>{{ $method['name'] }}</td>
                                            <td>{{ number_format($method['balance'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @if($staff->role != 'Order Booker')
                                    <tr>
                                        <td colspan="7" class="fw-bold">Currency Ledgers</td>
                                    </tr>
                                    @foreach ($currencies as $key => $currency)
                                        <tr>
                                            <td>{{ $currency->title }}</td>
                                            <td>{{ number_format($currency->balance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <!--end card-body-->
                        </div><!--end col-->
                       
                    </div><!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

@endsection



