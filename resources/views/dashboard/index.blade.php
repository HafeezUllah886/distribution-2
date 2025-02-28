@extends('layout.app')
@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-text">From</div>
            <input type="date" class="form-control" id="from" onchange="filterData()" value="{{ $from }}" placeholder="Username">
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-text">To</div>
            <input type="date" class="form-control" id="to" onchange="filterData()" value="{{ $to }}" placeholder="Username">
        </div>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-text">Branch</div>
            <select name="branch" id="branch" onchange="filterData()" class="form-control">
                <option value="All">All</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $branch->id == $branch1 ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
</div>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <!-- card -->
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 overflow-hidden">
                     <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Sales</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($sales, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
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
                     <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Purchases</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($purchases, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                            <i class="bx bx-shopping-bag text-danger"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Customers Balance</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($customerBalance, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-success-subtle rounded fs-3">
                            <i class="bx bx-user-circle text-success"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Vendors Balance</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($vendorBalance, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Business Balance</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($businessBalance, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                            <i class="bx bx-dollar-circle text-warning"></i>
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
                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Expenses</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-4">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{number_format($expenses, 0)}}</h4>
                        <a class="text-decoration-underline" data-bs-toggle="modal" data-bs-target="#viewStatmentModal">View Details</a>
                    </div>
                    <div class="avatar-sm flex-shrink-0">
                        <span class="avatar-title bg-danger-subtle rounded fs-3">
                            <i class="bx bx-dollar-circle text-danger"></i>
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
@include('layout.view_self_statement')
@endsection

@section('page-css')

@endsection
@section('page-js')
       <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
       <script src="{{asset('assets/js/pages/dashboard-ecommerce.init.js')}}"></script>
       <script>
        function filterData(){
            let branch = $('#branch').val();
            let from = $('#from').val();
            let to = $('#to').val();
            window.location.href = "{{ route('admin.dashboard', ['branch' => ':branch', 'from' => ':from', 'to' => ':to']) }}" 
                .replace(':branch', branch)
                .replace(':from', from)
                .replace(':to', to);
        }
       </script>
@endsection

