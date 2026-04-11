@extends('layout.popups')
@section('content')
    <style>
        /* Accordion heading rows */
        tr.accordion-heading {
            cursor: pointer;
            user-select: none;
        }

        tr.accordion-heading:hover {
            filter: brightness(0.93);
        }

        tr.accordion-heading .acc-toggle-icon {
            float: right;
            margin-right: 4px;
            transition: transform 0.2s ease;
            font-style: normal;
            font-size: 12px;
        }

        tr.accordion-heading.collapsed .acc-toggle-icon {
            transform: rotate(-90deg);
        }

        tr.accordion-detail {
            transition: opacity 0.15s ease;
        }

        /* Print: always show all rows */
        @media print {
            tr.accordion-detail {
                display: table-row !important;
            }

            .acc-toggle-icon {
                display: none !important;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                            <a href="javascript:window.print()" class="btn btn-success ml-4"><i
                                    class="ri-printer-line mr-4"></i> Print</a>
                        </div>
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h1>{{ Auth()->user()->branch->name }}</h1>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <h3>Branch Investment Report</h3>
                                </div>
                            </div>
                        </div>
                        <!--end card-header-->
                    </div><!--end col-->
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">On Date</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($date)) }}</h5>
                                </div>

                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                    <h5 class="fs-14 mb-0">{{ $branch_name }}</h5>
                                </div>
                                <!--end col-->
                                <!--end col-->
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0"><span id="total-amount">{{ date('d M Y') }}</span></h5>
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
                                <table class="table table-bordered text-center table-nowrap align-middle mb-0">

                                    <thead>
                                        <tr class="table-active p-1 fw-bold">
                                            <th scope="col" class="p-1 fw-bold" style="width: 50px;">#</th>
                                            <th scope="col" class="p-1 text-start fw-bold">Particulars</th>
                                            <th scope="col" class="p-1 text-end fw-bold">Values</th>
                                            <th scope="col" class="p-1 text-end fw-bold">Last Year</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-active p-1">
                                            <td colspan="4" class="p-1 text-start fw-bold fs-16">General Investment</td>
                                        </tr>

                                        {{-- ── 1. Customers Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-customers">
                                            <td class="p-1">1</td>
                                            <td class="p-1 fw-bold text-start">
                                                Customers Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($customers->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($customers->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($customers as $customer)
                                            <tr class="p-1 accordion-detail acc-customers">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $customer->title }} | {{ $customer->area->name }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($customer->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($customer->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 2. Vendors Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-vendors">
                                            <td class="p-1">2</td>
                                            <td class="p-1 fw-bold text-start">
                                                Vendors Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($vendors->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($vendors->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($vendors as $vendor)
                                            <tr class="p-1 accordion-detail acc-vendors">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $vendor->title }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($vendor->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($vendor->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 3. Business Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-business">
                                            <td class="p-1">3</td>
                                            <td class="p-1 fw-bold text-start">
                                                Business Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($business->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($business->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($business as $busines)
                                            <tr class="p-1 accordion-detail acc-business">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $busines->title }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($busines->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($busines->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 4. Staff Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-staff">
                                            <td class="p-1">4</td>
                                            <td class="p-1 fw-bold text-start">
                                                Staff Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($staff->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($staff->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($staff as $staf)
                                            <tr class="p-1 accordion-detail acc-staff">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $staf->name }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($staf->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($staf->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 5. Personal Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-personal">
                                            <td class="p-1">5</td>
                                            <td class="p-1 fw-bold text-start">
                                                Personal Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($personal->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($personal->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($personal as $person)
                                            <tr class="p-1 accordion-detail acc-personal">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $person->title }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($person->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($person->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 6. Employees Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-employees">
                                            <td class="p-1">6</td>
                                            <td class="p-1 fw-bold text-start">
                                                Employees Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($employees->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($employees->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($employees as $employee)
                                            <tr class="p-1 accordion-detail acc-employees">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $employee->name }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($employee->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">
                                                    {{ number_format($employee->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 7. Floor Stock Value (no children) ── --}}
                                        <tr class="table-active p-1">
                                            <td class="p-1">7</td>
                                            <td class="p-1 fw-bold text-start">Floor Stock Value (On Cost)</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalCurrentStockValue, 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalLastYearStockValue, 2) }}</td>
                                        </tr>

                                        <tr class="table-active p-1">
                                            <td colspan="4" class="p-1 fw-bold text-start fs-16">Invested Amount &
                                                Fixed
                                                Assets
                                            </td>
                                        </tr>

                                        {{-- ── 8. Investors Balance ── --}}
                                        <tr class="table-active p-1 accordion-heading" data-target="acc-investors">
                                            <td class="p-1">8</td>
                                            <td class="p-1 fw-bold text-start">
                                                Investors Balance
                                                <i class="acc-toggle-icon">▼</i>
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($investors->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($investors->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        @foreach ($investors as $investor)
                                            <tr class="p-1 accordion-detail acc-investors">
                                                <td class="p-1"></td>
                                                <td class="p-1 text-start">
                                                    {{ $investor->title }}
                                                </td>
                                                <td class="p-1 text-end">{{ number_format($investor->currentBalance, 2) }}
                                                </td>
                                                <td class="p-1 text-end">
                                                    {{ number_format($investor->lastYearBalance, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach

                                        {{-- ── 9. Fixed Assets (no children) ── --}}
                                        <tr class="table-active p-1">
                                            <td class="p-1">9</td>
                                            <td class="p-1 fw-bold text-start">Fixed Assets</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalCurrentFixedAssetsValue, 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalLastYearFixedAssetsValue, 2) }}</td>
                                        </tr>

                                    </tbody>
                                    <tfoot>
                                        @php
                                            $totalGeneralCurrent =
                                                $customers->sum('currentBalance') +
                                                $business->sum('currentBalance') +
                                                $personal->sum('currentBalance') +
                                                $employees->sum('currentBalance') +
                                                $staff->sum('currentBalance') +
                                                $totalCurrentStockValue +
                                                $vendors->sum('currentBalance');

                                            $totalGeneralLastYear =
                                                $customers->sum('lastYearBalance') +
                                                $business->sum('lastYearBalance') +
                                                $personal->sum('lastYearBalance') +
                                                $employees->sum('lastYearBalance') +
                                                $staff->sum('lastYearBalance') +
                                                $totalLastYearStockValue +
                                                $vendors->sum('lastYearBalance');

                                            $totalCurrentInvestment =
                                                $investors->sum('currentBalance') + $totalCurrentFixedAssetsValue;
                                            $totalLastYearInvestment =
                                                $investors->sum('lastYearBalance') + $totalLastYearFixedAssetsValue;
                                        @endphp

                                        <tr class="table-active p-1">
                                            <td class="p-1 fw-bold text-end" colspan="2">Total Investment and Fixed
                                                Assets
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalGeneralCurrent + $totalCurrentFixedAssetsValue, 2) }}
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalGeneralLastYear + $totalLastYearFixedAssetsValue, 2) }}
                                            </td>
                                        </tr>
                                        <tr class="table-active p-1">
                                            <td class="p-1 fw-bold text-end" colspan="2">Total Fixed Investment &
                                                Assets
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($investors->sum('currentBalance'), 2) }}</td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($investors->sum('lastYearBalance'), 2) }}</td>
                                        </tr>
                                        <tr class="table-active p-1">
                                            <td class="p-1 fw-bold text-end" colspan="2">Result / Outcome of Investment
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalGeneralCurrent + $totalCurrentFixedAssetsValue - $investors->sum('currentBalance'), 2) }}
                                            </td>
                                            <td class="p-1 fw-bold text-end">
                                                {{ number_format($totalGeneralLastYear + $totalLastYearFixedAssetsValue - $investors->sum('lastYearBalance'), 2) }}
                                            </td>
                                        </tr>


                                    </tfoot>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('tr.accordion-heading').forEach(function(heading) {
                heading.addEventListener('click', function() {
                    var target = this.dataset.target;
                    var details = document.querySelectorAll('tr.' + target);
                    var isCollapsed = this.classList.contains('collapsed');

                    if (isCollapsed) {
                        // Expand
                        details.forEach(function(row) {
                            row.style.display = '';
                        });
                        heading.classList.remove('collapsed');
                    } else {
                        // Collapse
                        details.forEach(function(row) {
                            row.style.display = 'none';
                        });
                        heading.classList.add('collapsed');
                    }
                });

                // Collapse all sections by default on page load
                heading.classList.add('collapsed');
                document.querySelectorAll('tr.' + heading.dataset.target).forEach(function(row) {
                    row.style.display = 'none';
                });
            });
        });
    </script>
@endsection
