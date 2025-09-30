<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{route('dashboard')}}" class="logo logo-dark">
            <span class="logo-sm">
                <h3 class="text-white">{{projectNameShort()}}</h3>
            </span>
            <span class="logo-lg">
                <h3 class="text-white mt-3">{{projectNameHeader()}}</h3>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-sm">
                <h3 class="text-white">{{projectNameShort()}}</h3>
            </span>
            <span class="logo-lg">
                <h3 class="text-white mt-3">{{projectNameHeader()}}</h3>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div class="dropdown sidebar-user m-1 rounded">
        <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <span class="d-flex align-items-center gap-2">
                <img class="rounded header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}"
                    alt="Header Avatar">
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">{{ auth()->user()->name }}</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i
                            class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span
                            class="align-middle">Online</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <!-- item-->
            <h6 class="dropdown-header">Welcome {{ auth()->user()->name }}!</h6>
            <a class="dropdown-item" href="{{ route('profile') }}"><i
                    class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                    class="align-middle">Profile</span></a>
            <a class="dropdown-item" href="auth-logout-basic.html"><i
                    class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle"
                    data-key="t-logout">Logout</span></a>
        </div>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
                    </a>
                </li> <!-- end Dashboard Menu -->
               <li class="nav-item">
                    <a class="nav-link menu-link" href="#sales" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-shopping-cart-fill"></i><span data-key="t-apps">Sale</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sales">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('sale.index', ['start' => firstDayOfMonth(), 'end' => now()->toDateString()]) }}" class="nav-link" data-key="t-chat"> Sales History</a>
                            </li>
                        </ul>
                    </div>
                </li> 
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAccounts" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAccounts">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Accounts</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAccounts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('account.create') }}" class="nav-link"
                                    data-key="t-basic-elements">Create Account</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Business') }}" class="nav-link"
                                    data-key="t-form-select">
                                    Business Accounts </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Customer') }}" class="nav-link"
                                    data-key="t-checkboxs-radios">Customer Accounts</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Vendor') }}" class="nav-link"
                                    data-key="t-pickers">
                                    Vendor Accounts </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Supply Man') }}" class="nav-link"
                                    data-key="t-pickers">
                                    Supply Man Accounts </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Unloader') }}" class="nav-link"
                                    data-key="t-pickers">
                                    Unloader Accounts </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('accountsList', 'Freight') }}" class="nav-link"
                                    data-key="t-pickers">
                                    Freight Accounts </a>
                            </li>
                        </ul>
                    </div>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMakePayments" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarMakePayments">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Make Payments</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMakePayments">
                        <ul class="nav nav-sm flex-column">
                           <li class="nav-item">
                                <a href="{{ route('payments.index') }}" class="nav-link"
                                    data-key="t-input-masks">Payments</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('expenses.index') }}" class="nav-link" data-key="t-range-slider">
                                    Expenses</a>
                            </li>
                           
                        </ul>
                    </div>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarReceivePayments" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarReceivePayments">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Receive Payments</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarReceivePayments">
                        <ul class="nav nav-sm flex-column">
                           
                           
                           <li class="nav-item">
                                <a href="{{ route('payments_receiving.index') }}" class="nav-link"
                                    data-key="t-input-masks">Receive Payments</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('staff_payments.index') }}" class="nav-link"
                                    data-key="t-input-masks">Staff Payments</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('auto_staff_payments') }}" class="nav-link"
                                    data-key="t-input-masks">Auto Staff Payments</a>
                            </li> 
                           <li class="nav-item">
                                <a href="{{ route('bulk_payment.index') }}" class="nav-link"
                                    data-key="t-input-masks">Bulk Payments</a>
                            </li> 
                        </ul>
                    </div>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMiscellaneous" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarMiscellaneous">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Miscellaneous</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMiscellaneous">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('accounts_adjustments.index') }}" class="nav-link"
                                    data-key="t-input-masks">Accounts Adjustment</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('staff_amounts_adjustments.index') }}" class="nav-link"
                                    data-key="t-input-masks">Staff Amount Adjustment</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('transfers.index') }}" class="nav-link"
                                    data-key="t-advanced">Transfer</a>
                            </li>
                        </ul>
                    </div>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarEmployees" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarEmployees">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Employees</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarEmployees">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('employees.index') }}" class="nav-link"
                                    data-key="t-input-masks">List</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('generate_salary.index') }}" class="nav-link"
                                    data-key="t-input-masks">Generate Salary</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('issue_salary.index') }}" class="nav-link"
                                    data-key="t-input-masks">Issue Salary</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('issue_advance.index') }}" class="nav-link"
                                    data-key="t-input-masks">Issue Advance</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('issue_misc.index') }}" class="nav-link"
                                    data-key="t-input-masks">Issue Miscellaneous</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('employee_adjustments.index') }}" class="nav-link"
                                    data-key="t-input-masks">Ledger Adjustments</a>
                            </li> 
                        </ul>
                    </div>
                </li>  

                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('my_balance') }}">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-my_balance">My Balance</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#cheques" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarApps">
                        <i class="ri-shopping-cart-fill"></i><span data-key="t-apps">Cheques</span>
                    </a>
                    <div class="collapse menu-dropdown" id="cheques">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('cheques.index', ['status' => 'All']) }}" class="nav-link" data-key="t-chat"> All</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('cheques.index', ['status' => 'pending']) }}" class="nav-link" data-key="t-chat"> Pending</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('cheques.index', ['status' => 'forwarded']) }}" class="nav-link" data-key="t-chat"> Forwarded</a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="{{ route('cheques.index', ['status' => 'bounced']) }}" class="nav-link" data-key="t-chat"> Bounced</a>
                            </li>
                        </ul>
                    </div>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link" href="{{ route('customer_advances.index') }}">
                        <i class="ri-cash-line"></i> <span data-key="t-customer_advances">Customer Advances</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarOtherUsers" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarOtherUsers">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Other Users</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarOtherUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('otherusers.index', ['Order Booker']) }}" class="nav-link"
                                    data-key="t-form-select">
                                   Order Bookers </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('otherusers.index', ['Operator']) }}" class="nav-link"
                                    data-key="t-form-select">
                                   Operators </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('otherusers.index', ['Branch Admin']) }}" class="nav-link"
                                    data-key="t-form-select">
                                   Branch Admins </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarReports">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">Reports</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarReports">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('reportProductSummary') }}" class="nav-link"
                                    data-key="t-basic-elements">Products Summary</a>
                            </li> 
                            <li class="nav-item">
                                <a href="{{ route('reportSales') }}" class="nav-link"
                                    data-key="t-basic-elements">Sales Report</a>
                            </li>
                           <li class="nav-item">
                                <a href="{{ route('reportPurchases') }}" class="nav-link"
                                    data-key="t-basic-elements">Purchases Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportBalanceSheet') }}" class="nav-link"
                                    data-key="t-basic-elements">Balance Sheet</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportWarehouseStock') }}" class="nav-link"
                                    data-key="t-basic-elements">Warehouse Stock Report</a>
                            </li>

                           <li class="nav-item">
                                <a href="{{ route('reportBranchStock') }}" class="nav-link"
                                    data-key="t-basic-elements">Branch Stock Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportTopCustomers') }}" class="nav-link"
                                    data-key="t-basic-elements">Top Customers Report</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportTopSellingProducts') }}" class="nav-link"
                                    data-key="t-basic-elements">Top Selling Products</a>
                            </li>
                           <li class="nav-item">
                                <a href="{{ route('reportTopOrderbookers') }}" class="nav-link"
                                    data-key="t-basic-elements">Top Orderbookers</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportDailyVendorWiseProductsSales') }}" class="nav-link"
                                    data-key="t-basic-elements">Daily Vendor Wise Products Sales</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportDailyInvWiseProductsSales') }}" class="nav-link"
                                    data-key="t-basic-elements">Daily Invoice Wise Products Sales</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportCustomerProductSales') }}" class="nav-link"
                                    data-key="t-basic-elements">Customer Product Sales</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportInvoicePayments') }}" class="nav-link"
                                    data-key="t-basic-elements">Invoices Payments</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportStockMovement') }}" class="nav-link"
                                    data-key="t-basic-elements">Stock Movement</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportSupplymanReport') }}" class="nav-link"
                                    data-key="t-basic-elements">Supplyman Labour Charges</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportUnloaderReport') }}" class="nav-link"
                                    data-key="t-basic-elements">Unloader Labour Charges</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('reportOrderbookerWiseCustomerBalance') }}" class="nav-link"
                                    data-key="t-basic-elements">Orderbooker Wise Customer Balance</a>
                            </li>
                           <li class="nav-item">
                                <a href="{{ route('reportExpense') }}" class="nav-link"
                                    data-key="t-basic-elements">Expense Report</a>
                            </li>
                        </ul>
                    </div>
                </li> 
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
