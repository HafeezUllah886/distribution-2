@extends('layout.app')
@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Orders</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"> {{number_format(totalOrders())}} </h4>
                        <a href="{{route('Branch.orders', ['status' => 'All'])}}" class="text-decoration-underline">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-info"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Pending Orders</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"> {{number_format(pendingOrders())}} </h4>
                        <a href="{{route('Branch.orders', ['status' => 'Pending'])}}" class="text-decoration-underline">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-warning"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Approved Orders</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"> {{number_format(approvedOrders())}} </h4>
                        <a href="{{route('Branch.orders', ['status' => 'Approved'])}}" class="text-decoration-underline">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-success"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Completed Orders</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4"> {{number_format(completedOrders())}} </h4>
                        <a href="{{route('Branch.orders', ['status' => 'Finalized'])}}" class="text-decoration-underline">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-success"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                     <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Sales (This Month)</p>
                    </div>
                    <div class="flex-shrink-0">
                            @if (calculatePercentageDifference(salesPreviousMonth(),salesThisMonth()) > 0)
                            <h5 class="text-success fs-14 mb-0">
                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> {{(calculatePercentageDifference(salesPreviousMonth(),salesThisMonth()))}}
                            </h5>
                            @else
                            <h5 class="text-danger fs-14 mb-0">
                            <i class="ri-arrow-right-down-line fs-13 align-middle"></i>  {{(calculatePercentageDifference(salesPreviousMonth(),salesThisMonth()))}}
                            </h5>
                            @endif
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format(salesThisMonth())}}</h4>
                        <a href="{{route('sale.index')}}" class="text-decoration-underline">View sales</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-info-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-info"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Customers</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{CustomersCount()}}</h4>
                        <a href="#" class="text-decoration-underline">See details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-user-circle text-warning"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> My Balance</p>
                    </div>
                    {{-- <div class="flex-shrink-0">
                        <h5 class="text-muted fs-14 mb-0">
                            +0.00 %
                        </h5>
                    </div> --}}
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format(getUserAccountBalance(auth()->id()))}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="bx bx-wallet text-primary"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> My Currency</p>
                    </div>
                    {{-- <div class="flex-shrink-0">
                        <h5 class="text-muted fs-14 mb-0">
                            +0.00 %
                        </h5>
                    </div> --}}
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format(myCurrency(auth()->id()))}}</h4>
                        <a class="text-decoration-underline" href="{{route('currency.details', auth()->id())}}">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                            <i class="bx bx-wallet text-primary"></i>
                        </span>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>
@endsection
@section('page-css')

@endsection
@section('page-js')
       <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
       <script src="{{asset('assets/js/pages/dashboard-ecommerce.init.js')}}"></script>
       <script>


        function updateCustomerImpressionChart(ordersData, earningsData, refundsData, months) {
            var t = getChartColorsArray("customer_impression_charts");
            if (t) {
                var e = {
                    series: [
                        { name: "Sales", type: "area", data: ordersData },   // Updated Orders data
                        { name: "Profit", type: "bar", data: earningsData }, // Updated Earnings data
                        { name: "Expense", type: "line", data: refundsData } // Updated Refunds data
                    ],
                    chart: { height: 370, type: "line", toolbar: { show: !1 } },
                    stroke: { curve: "straight", dashArray: [0, 0, 8], width: [2, 0, 2.2] },
                    fill: { opacity: [0.1, 0.9, 1] },
                    markers: { size: [0, 0, 0], strokeWidth: 2, hover: { size: 4 } },
                    xaxis: { categories: months, axisTicks: { show: !1 }, axisBorder: { show: !1 } },
                    grid: { show: !0, xaxis: { lines: { show: !0 } }, yaxis: { lines: { show: !1 } }, padding: { top: 0, right: -2, bottom: 15, left: 10 } },
                    legend: { show: !0, horizontalAlign: "center", offsetX: 0, offsetY: -5, markers: { width: 9, height: 9, radius: 6 }, itemMargin: { horizontal: 10, vertical: 0 } },
                    plotOptions: { bar: { columnWidth: "30%", barHeight: "70%" } },
                    colors: t,
                    tooltip: {
                        shared: !0,
                        y: [
                            {
                                formatter: function (e) {
                                    return void 0 !== e ? e.toFixed(0) : e;
                                },
                            },
                            {
                                formatter: function (e) {
                                    return void 0 !== e ? e.toFixed(2) : e;
                                },
                            },
                            {
                                formatter: function (e) {
                                    return void 0 !== e ? e.toFixed(0) : e;
                                },
                            },
                        ],
                    },
                };
                if (customerImpressionChart) {
                    customerImpressionChart.destroy();
                }
                customerImpressionChart = new ApexCharts(document.querySelector("#customer_impression_charts"), e);
                customerImpressionChart.render();
            }
        }

        var sales = @json($sales);
        var months = @json($monthNames);
        var expenses = @json($expenses);
        var profits = @json($profits);
        updateCustomerImpressionChart(
            sales,
            profits,
            expenses,
            months
        )


       </script>
@endsection

