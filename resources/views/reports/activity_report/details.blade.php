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
                                        <h3>Activity Report</h3>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                        <h5 class="fs-14 mb-0">{{ $branch }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">From Date</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($from)) }}</h5>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">To Date</p>
                                        <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($to)) }}</h5>
                                    </div>
                                    <!--end col-->
                                    <!--end col-->
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
                                <div class="card-head">
                                    <h3>Payments</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalPayments = 0;
                                          @endphp
                                          @foreach ($payment_accounts as $key => $payment_account)
                                           @if ($payment_account->payments->count() > 0)
                                           @php
                                           $totalPayments += $payment_account->payments->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="9" class="p-1 text-start">{{$payment_account->title}}</th>
                                            </tr>
                                        @foreach ($payment_account->payments as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="8" class="text-end p-1">Total of {{$payment_account->title}}</th>
                                            <th class="text-end p-1">{{number_format($payment_account->payments->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="8" class="text-end p-1">Total Payments</th>
                                            <th class="text-end p-1">{{number_format($totalPayments, 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Receivings</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalReceivings = 0;
                                          @endphp
                                          @foreach ($payment_accounts as $key => $payment_account)
                                           @if ($payment_account->receivings->count() > 0)
                                           @php
                                           $totalReceivings += $payment_account->receivings->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="9" class="p-1 text-start">{{$payment_account->title}}</th>
                                            </tr>
                                        @foreach ($payment_account->receivings as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="8" class="text-end p-1">Total of {{$payment_account->title}}</th>
                                            <th class="text-end p-1">{{number_format($payment_account->receivings->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="8" class="text-end p-1">Total Receivings</th>
                                            <th class="text-end p-1">{{number_format($totalReceivings, 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Staff Payments</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalStaffPayments = 0;
                                          @endphp
                                          @foreach ($staffs as $key => $staff)
                                           @if ($staff->payments->count() > 0)
                                           @php
                                           $totalStaffPayments += $staff->payments->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="9" class="p-1 text-start">{{$staff->name}}</th>
                                            </tr>
                                        @foreach ($staff->payments as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="8" class="text-end p-1">Total of {{$staff->name}}</th>
                                            <th class="text-end p-1">{{number_format($staff->payments->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="8" class="text-end p-1">Total Staff Payments</th>
                                            <th class="text-end p-1">{{number_format($totalStaffPayments, 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Sale Payments</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Inv#</th>
                                               {{--  <th class="p-1">Order Booker</th> --}}
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalSalePayments = 0;
                                          @endphp
                                          @foreach ($customers as $key => $customer)
                                           @if ($customer->salePayments->count() > 0)
                                           @php
                                           $totalSalePayments += $customer->salePayments->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="10" class="p-1 text-start">{{$customer->title}}</th>
                                            </tr>
                                        @foreach ($customer->salePayments as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>    
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->salesID}}</td>
                                                {{-- <td class="p-1">{{$payment->orderbooker->name}}</td> --}}
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="9" class="text-end p-1">Total of {{$customer->title}}</th>
                                            <th class="text-end p-1">{{number_format($customer->salePayments->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="9" class="text-end p-1">Total Sale Payments</th>
                                            <th class="text-end p-1">{{number_format($totalSalePayments, 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Expenses</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalExpenses = 0;
                                          @endphp
                                          @foreach ($expense_categories as $key => $expense_category)
                                           @if ($expense_category->trans->count() > 0)
                                           @php
                                           $totalExpenses += $expense_category->trans->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="9" class="p-1 text-start">{{$expense_category->name}}</th>
                                            </tr>
                                        @foreach ($expense_category->trans as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>    
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="8" class="text-end p-1">Total of {{$expense_category->name}}</th>
                                            <th class="text-end p-1">{{number_format($expense_category->trans->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="8" class="text-end p-1">Total Expenses</th>
                                            <th class="text-end p-1">{{number_format($totalExpenses, 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Employee Salaries</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1 text-start">Employee</th>
                                                <th class="p-1">Month</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @foreach ($salaries as $key => $salary)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$salary->refID}}</td>    
                                                <td class="p-1 text-start">{{$salary->employee->name}}</td>    
                                                <td class="p-1">{{$salary->month}}</td>    
                                                <td class="p-1">{{date('d M Y', strtotime($salary->date))}}</td>
                                                <td class="p-1">{{$salary->method}}</td>
                                                <td class="p-1">{{$salary->number}}</td>
                                                <td class="p-1">{{$salary->bank}}</td>
                                                <td class="p-1">{{$salary->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$salary->notes}}</td>
                                                <td class="text-end p-1">{{$salary->salary}}</td>
                                            </tr>
                                          @endforeach
                                          <tr>
                                            <th colspan="10" class="text-end p-1">Total Salaries</th>
                                            <th class="text-end p-1">{{number_format($salaries->sum('salary'), 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Employee Advances</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1 text-start">Employee</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @foreach ($advances as $key => $advance)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$advance->refID}}</td>    
                                                <td class="p-1 text-start">{{$advance->employee->name}}</td>    
                                                <td class="p-1">{{date('d M Y', strtotime($advance->date))}}</td>
                                                <td class="p-1">{{$advance->method}}</td>
                                                <td class="p-1">{{$advance->number}}</td>
                                                <td class="p-1">{{$advance->bank}}</td>
                                                <td class="p-1">{{$advance->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$advance->notes}}</td>
                                                <td class="text-end p-1">{{$advance->advance}}</td>
                                            </tr>
                                          @endforeach
                                          <tr>
                                            <th colspan="9" class="text-end p-1">Total Advances</th>
                                            <th class="text-end p-1">{{number_format($advances->sum('advance'), 2)}}</th>
                                        </tr>
                                        </tbody>
                                    </table><!--end table-->
                                </div>

                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 pt-2">
                                <div class="card-head">
                                    <h3>Employee Misc Payments</h3>
                                </div>
                                <div>
                                    <table class="table table-bordered text-center table-nowrap align-middle mb-0 w-100" style="width: 100% !important;border: 1px solid #000000;">
                                        <thead>
                                            <tr class="table-active" style="border: 1px solid #000000;">
                                                <th class="p-1">#</th>
                                                <th class="p-1">Ref #</th>
                                                <th class="p-1 text-start">Employee</th>
                                                <th class="p-1">Date</th>
                                                <th class="p-1">Method</th>
                                                <th class="p-1">Number</th>
                                                <th class="p-1">Bank</th>
                                                <th class="p-1">Cheque Date</th>
                                                <th class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">Notes</th>
                                                <th class="p-1">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                          @php
                                          $totalEmpMisc = 0;
                                          @endphp
                                          @foreach ($emp_payment_cats as $key => $emp_payment_cat)
                                           @if ($emp_payment_cat->trans->count() > 0)
                                           @php
                                           $totalEmpMisc += $emp_payment_cat->trans->sum('amount');
                                           @endphp
                                           <tr>
                                                <th colspan="10" class="p-1 text-start">{{$emp_payment_cat->name}}</th>
                                            </tr>
                                        @foreach ($emp_payment_cat->trans as $payment)
                                            <tr>
                                                <td class="p-1">{{$loop->iteration}}</td>
                                                <td class="p-1">{{$payment->refID}}</td>    
                                                <td class="p-1 text-start">{{$payment->employee->name}}</td>    
                                                <td class="p-1">{{date('d M Y', strtotime($payment->date))}}</td>
                                                <td class="p-1">{{$payment->method}}</td>
                                                <td class="p-1">{{$payment->number}}</td>
                                                <td class="p-1">{{$payment->bank}}</td>
                                                <td class="p-1">{{$payment->cheque_date}}</td>
                                                <td class="text-start p-1" style="max-width: 100px; overflow-wrap: break-word; white-space: normal;">{{$payment->notes}}</td>
                                                <td class="text-end p-1">{{$payment->amount}}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="9" class="text-end p-1">Total of {{$emp_payment_cat->name}}</th>
                                            <th class="text-end p-1">{{number_format($emp_payment_cat->trans->sum('amount'), 2)}}</th>
                                        </tr>
                                               
                                           @endif
                                          @endforeach
                                          <tr>
                                            <th colspan="9" class="text-end p-1">Total Employee Misc Payments</th>
                                            <th class="text-end p-1">{{number_format($totalEmpMisc, 2)}}</th>
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



