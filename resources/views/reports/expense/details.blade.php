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
                                        <h1>{{projectNameHeader()}}</h1>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        <h3>Expense Report</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">From</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($from)) }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">To</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($to)) }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Category</p>
                                        <h5 class="fs-14 mb-0">{{ $category }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">{{ date("d M Y") }}</span></h5>
                                        {{-- <h5 class="fs-14 mb-0"><span id="total-amount">{{ \Carbon\Carbon::now()->format('h:i A') }}</span></h5> --}}
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Date</th>
                                                <th scope="col" class="text-start">Method</th>
                                                <th scope="col" class="text-start">Bank</th>
                                                <th scope="col" class="text-start">Number</th>
                                                <th scope="col" class="text-start">Cheque Date</th>
                                                <th scope="col" class="text-start">Notes</th>
                                                <th scope="col" class="text-start">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                         @php
                                             $totalAmount = 0;
                                         @endphp
                                         @foreach($data as $categoryName => $categoryExpenses)
                                             <tr class="table-active">
                                                 <td colspan="8" class="text-start">
                                                     <h5 class="mb-0">{{ $categoryName }}</h5>
                                                 </td>
                                             </tr>
                                             @foreach($categoryExpenses as $key => $expense)
                                                 <tr>
                                                     <td>{{ $key + 1 }}</td>
                                                     <td class="text-start">{{ date('d M Y', strtotime($expense['date'])) }}</td>
                                                     <td class="text-start">{{ $expense['method'] }}</td>
                                                     <td class="text-start">{{ $expense['bank'] }}</td>
                                                     <td class="text-start">{{ $expense['number'] }}</td>
                                                     <td class="text-start">{{ $expense['cheque_date'] ? date('d M Y', strtotime($expense['cheque_date'])) : '' }}</td>
                                                     <td class="text-start">{{ $expense['notes'] }}</td>
                                                     <td class="text-start">{{ number_format($expense['amount'], 2) }}</td>
                                                 </tr>
                                             @endforeach
                                             <tr class="table-active">
                                                 <td colspan="7" class="text-end">Total for {{ $categoryName }}:</td>
                                                 <td class="text-start">{{ number_format(array_sum(array_column($categoryExpenses, 'amount')), 2) }}</td>
                                             </tr>
                                         @endforeach
                                         <tr class="table-active">
                                             <td colspan="7" class="text-end">Grand Total:</td>
                                             <td class="text-start">{{ number_format(array_sum(array_column($expenses->toArray(), 'amount')), 2) }}</td>
                                         </tr>
                                     </tbody>
                                        
                                    </table><!--end table-->
                                </div>

                            </div>
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



