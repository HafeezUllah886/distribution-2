@extends('layout.popups')
@section('content')
    <style>
        .acc-btn {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .acc-btn .acc-num {
            width: 40px;
            flex-shrink: 0;
            text-align: center;
        }

        .acc-btn .acc-name {
            flex-grow: 1;
            text-align: left;
        }

        .acc-btn .acc-val {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
        }

        .acc-btn .acc-ly {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e8f0fe;
            color: #000;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-body {
            padding: 0;
        }

        /* Column headers row */
        .acc-col-header {
            display: flex;
            width: 100%;
            padding: 4px 16px;
            background: #f3f3f3;
            font-weight: 700;
            font-size: 0.8rem;
            border-bottom: 1px solid #dee2e6;
        }

        .acc-col-header .h-num {
            width: 40px;
            flex-shrink: 0;
            text-align: center;
        }

        .acc-col-header .h-name {
            flex-grow: 1;
        }

        .acc-col-header .h-val {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
        }

        .acc-col-header .h-ly {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
        }

        /* Section label rows */
        .section-label {
            background: #f8f9fa;
            padding: 6px 16px;
            font-weight: 700;
            font-size: 1rem;
            border-top: 2px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
        }

        /* Static (non-accordion) rows */
        .static-row {
            display: flex;
            width: 100%;
            align-items: center;
            padding: 6px 16px;
            border-bottom: 1px solid #dee2e6;
            background: #f8f9fa;
        }

        .static-row .s-num {
            width: 40px;
            flex-shrink: 0;
            text-align: center;
            font-weight: 600;
        }

        .static-row .s-name {
            flex-grow: 1;
            font-weight: 600;
        }

        .static-row .s-val {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
            font-weight: 600;
        }

        .static-row .s-ly {
            width: 130px;
            flex-shrink: 0;
            text-align: right;
            font-weight: 600;
        }

        /* Detail table inside accordion */
        .detail-table {
            width: 100%;
            margin: 0;
        }

        .detail-table td {
            padding: 4px 8px;
            font-size: 0.825rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-table td:first-child {
            width: 40px;
            text-align: center;
        }

        .detail-table td:nth-child(2) {}

        .detail-table td:nth-child(3),
        .detail-table td:nth-child(4) {
            width: 130px;
            text-align: right;
        }

        /* Totals table */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .totals-table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-weight: 700;
        }

        .totals-table td:last-child,
        .totals-table td:nth-last-child(2) {
            text-align: right;
            width: 130px;
        }

        /* ── PRINT ─────────────────────────────────────────────── */
        @media print {

            /* Hide the toggle chevron — print what is currently visible */
            .accordion-button::after {
                display: none !important;
            }

            .accordion-button {
                padding: 4px 8px !important;
            }

            .accordion-item {
                border: none !important;
            }
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-xxl-9">
            <div class="card" id="demo">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end d-print-none p-2 mt-4">
                            <a href="javascript:window.print()" class="btn btn-success ml-4">
                                <i class="ri-printer-line mr-4"></i> Print
                            </a>
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
                    </div><!--end col-->

                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Last Year Date</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($lastYearDate)) }}</h5>
                                </div>
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Current Year Date</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y', strtotime($date)) }}</h5>
                                </div>
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Branch</p>
                                    <h5 class="fs-14 mb-0">{{ $branch_name }}</h5>
                                </div>
                                <div class="col-3">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold">Printed On</p>
                                    <h5 class="fs-14 mb-0">{{ date('d M Y') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div><!--end col-->

                    <div class="col-lg-12">
                        <div class="card-body p-4">

                            {{-- Column header bar --}}
                            <div class="acc-col-header">
                                <div class="h-num">#</div>
                                <div class="h-name">Particulars</div>
                                <div class="h-val">Values</div>
                                <div class="h-ly">Last Year</div>
                            </div>

                            {{-- ══ Section: General Investment ══ --}}
                            <div class="section-label">General Investment</div>

                            <div class="accordion accordion-flush" id="investmentAccordion">

                                {{-- ── 1. Customers Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hCustomers">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#accCustomers" aria-expanded="false"
                                            aria-controls="accCustomers">
                                            <span class="acc-btn">
                                                <span class="acc-num">1</span>
                                                <span class="acc-name">Customers Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($customers->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($customers->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accCustomers" class="accordion-collapse collapse" aria-labelledby="hCustomers">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($customers as $customer)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $customer->title }} | {{ $customer->area->name }}</td>
                                                        <td>{{ number_format($customer->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($customer->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── 2. Vendors Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hVendors">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#accVendors" aria-expanded="false" aria-controls="accVendors">
                                            <span class="acc-btn">
                                                <span class="acc-num">2</span>
                                                <span class="acc-name">Vendors Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($vendors->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($vendors->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accVendors" class="accordion-collapse collapse" aria-labelledby="hVendors">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($vendors as $vendor)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $vendor->title }}</td>
                                                        <td>{{ number_format($vendor->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($vendor->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── 3. Business Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hBusiness">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#accBusiness" aria-expanded="false" aria-controls="accBusiness">
                                            <span class="acc-btn">
                                                <span class="acc-num">3</span>
                                                <span class="acc-name">Business Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($business->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($business->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accBusiness" class="accordion-collapse collapse" aria-labelledby="hBusiness">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($business as $busines)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $busines->title }}</td>
                                                        <td>{{ number_format($busines->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($busines->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── 4. Staff Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hStaff">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accStaff" aria-expanded="false"
                                            aria-controls="accStaff">
                                            <span class="acc-btn">
                                                <span class="acc-num">4</span>
                                                <span class="acc-name">Staff Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($staff->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($staff->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accStaff" class="accordion-collapse collapse" aria-labelledby="hStaff">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($staff as $staf)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $staf->name }}</td>
                                                        <td>{{ number_format($staf->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($staf->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── 5. Personal Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hPersonal">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accPersonal" aria-expanded="false"
                                            aria-controls="accPersonal">
                                            <span class="acc-btn">
                                                <span class="acc-num">5</span>
                                                <span class="acc-name">Personal Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($personal->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($personal->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accPersonal" class="accordion-collapse collapse"
                                        aria-labelledby="hPersonal">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($personal as $person)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $person->title }}</td>
                                                        <td>{{ number_format($person->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($person->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── 6. Employees Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hEmployees">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accEmployees"
                                            aria-expanded="false" aria-controls="accEmployees">
                                            <span class="acc-btn">
                                                <span class="acc-num">6</span>
                                                <span class="acc-name">Employees Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($employees->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($employees->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accEmployees" class="accordion-collapse collapse"
                                        aria-labelledby="hEmployees">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($employees as $employee)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $employee->name }}</td>
                                                        <td>{{ number_format($employee->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($employee->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                {{-- ── 7. Floor Stock Value ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hFloorStock">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accFloorStock"
                                            aria-expanded="false" aria-controls="accFloorStock">
                                            <span class="acc-btn">
                                                <span class="acc-num">7</span>
                                                <span class="acc-name">Floor Stock Value (On Cost)</span>
                                                <span
                                                    class="acc-val">{{ number_format($products->sum('currentStockValue'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($products->sum('lastYearStockValue'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accFloorStock" class="accordion-collapse collapse"
                                        aria-labelledby="hFloorStock">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($products as $product)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>{{ number_format($product->currentStockValue, 2) }}</td>
                                                        <td>{{ number_format($product->lastYearStockValue, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- end accordion --}}


                            {{-- ══ Section: Invested Amount & Fixed Assets ══ --}}
                            <div class="section-label">Invested Amount &amp; Fixed Assets</div>

                            <div class="accordion accordion-flush" id="investmentAccordion2">

                                {{-- ── 8. Investors Balance ── --}}
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hInvestors">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accInvestors"
                                            aria-expanded="false" aria-controls="accInvestors">
                                            <span class="acc-btn">
                                                <span class="acc-num">8</span>
                                                <span class="acc-name">Fixed Assets</span>
                                                <span
                                                    class="acc-val">{{ number_format($fixed_assets->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($fixed_assets->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accInvestors" class="accordion-collapse collapse"
                                        aria-labelledby="hInvestors">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($fixed_assets as $fixed_asset)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $fixed_asset->item_description }}</td>
                                                        <td>{{ number_format($fixed_asset->currentBalance, 2) }}</td>
                                                        <td>{{ number_format($fixed_asset->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="hInvestors">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#accInvestors"
                                            aria-expanded="false" aria-controls="accInvestors">
                                            <span class="acc-btn">
                                                <span class="acc-num">9</span>
                                                <span class="acc-name">Investors Balance</span>
                                                <span
                                                    class="acc-val">{{ number_format($investors->sum('currentBalance'), 2) }}</span>
                                                <span
                                                    class="acc-ly">{{ number_format($investors->sum('lastYearBalance'), 2) }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="accInvestors" class="accordion-collapse collapse"
                                        aria-labelledby="hInvestors">
                                        <div class="accordion-body">
                                            <table class="detail-table">
                                                @foreach ($investors as $investor)
                                                    <tr>
                                                        <td></td>
                                                        <td>{{ $investor->title }}</td>
                                                        <td>({{ number_format($investor->currentPercentage, 2) }}%)
                                                            {{ number_format($investor->currentBalance, 2) }}</td>
                                                        <td>({{ number_format($investor->lastYearPercentage, 2) }}%)
                                                            {{ number_format($investor->lastYearBalance, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>{{-- end accordion2 --}}



                            {{-- ══ Totals ══ --}}
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
                            @endphp

                            <table class="totals-table mt-3">
                                <tr class="table-active">
                                    <td colspan="2" class="text-end">Total Investment and Fixed Assets</td>
                                    <td>{{ number_format($totalGeneralCurrent + $totalCurrentFixedAssetsValue, 2) }}</td>
                                    <td>{{ number_format($totalGeneralLastYear + $totalLastYearFixedAssetsValue, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="2" class="text-end">Total Fixed Investment &amp; Assets</td>
                                    <td>{{ number_format($investors->sum('currentBalance'), 2) }}</td>
                                    <td>{{ number_format($investors->sum('lastYearBalance'), 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="2" class="text-end">Result / Outcome of Investment</td>
                                    <td>{{ number_format($totalGeneralCurrent + $totalCurrentFixedAssetsValue - $investors->sum('currentBalance'), 2) }}
                                    </td>
                                    <td>{{ number_format($totalGeneralLastYear + $totalLastYearFixedAssetsValue - $investors->sum('lastYearBalance'), 2) }}
                                    </td>
                                </tr>
                            </table>

                        </div><!--end card-body-->
                    </div><!--end col-->
                </div><!--end row-->
            </div><!--end card-->
        </div><!--end col-->
    </div><!--end row-->
@endsection
