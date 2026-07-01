@extends('layouts.app')

@section('content')
    <div class="admin-shell d-flex min-vh-100">
        <!-- Sidebar -->
        <aside class="sidebar d-none d-lg-flex flex-column flex-shrink-0 p-3 p-xl-4"
            style="width: 280px; background: linear-gradient(180deg, #13293d 0%, #1f3b57 100%); color: #fff; min-height: 100vh; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom border-light border-opacity-10">
                <div class="brand-chip"
                    style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                    RS
                </div>
                <div>
                    <div class="small text-white-50 text-uppercase fw-semibold">Rental System</div>
                    <div class="h6 mb-0 fw-bold">Admin Oversight</div>
                </div>
            </div>

            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.properties*') ? 'active' : '' }}"
                    href="{{ route('admin.properties') }}">
                    <i class="fas fa-building me-2"></i> Properties
                </a>
                <a class="nav-link {{ request()->routeIs('admin.landlords*') ? 'active' : '' }}"
                    href="{{ route('admin.landlords') }}">
                    <i class="fas fa-user-tie me-2"></i> Landlords
                </a>
                <a class="nav-link {{ request()->routeIs('admin.tenants*') ? 'active' : '' }}"
                    href="{{ route('admin.tenants') }}">
                    <i class="fas fa-users me-2"></i> Tenants
                </a>
                <a class="nav-link {{ request()->routeIs('admin.caretakers*') ? 'active' : '' }}"
                    href="{{ route('admin.caretakers') }}">
                    <i class="fas fa-tools me-2"></i> Caretakers
                </a>
                <a class="nav-link {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"
                    href="{{ route('admin.payments') }}">
                    <i class="fas fa-credit-card me-2"></i> Payments
                </a>
                <a class="nav-link {{ request()->routeIs('admin.maintenance*') ? 'active' : '' }}"
                    href="{{ route('admin.maintenance') }}">
                    <i class="fas fa-wrench me-2"></i> Maintenance
                </a>
                <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}"
                    href="{{ route('admin.reports') }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                    href="{{ route('admin.users') }}">
                    <i class="fas fa-user-cog me-2"></i> Users
                </a>
                <a class="nav-link {{ request()->routeIs('admin.oversight*') ? 'active' : '' }}"
                    href="{{ route('admin.oversight.index') }}">
                    <i class="fas fa-eye me-2"></i> Oversight
                </a>
                <a class="nav-link {{ request()->routeIs('admin.units*') ? 'active' : '' }}"
                    href="{{ route('admin.units') }}">
                    <i class="fas fa-door-open me-2"></i> Units
                </a>
            </nav>

            <div class="mt-auto pt-3 border-top border-light border-opacity-10">
                <div class="small text-white-50 mb-1">Signed in as</div>
                <div class="fw-semibold">Admin User</div>
                <div class="small text-white-50">admin@system.com</div>
                <a href="#" class="btn btn-outline-light btn-sm w-100 mt-2">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow-1 p-3 p-md-4 p-xl-5" style="background: #f5f7fb; min-height: 100vh;">
            <!-- Mobile Top Bar -->
            <div class="d-flex d-lg-none justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="fw-bold">Rental System</div>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
                    </ul>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <!-- Mobile Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas"
        style="width: 280px; background: linear-gradient(180deg, #13293d 0%, #1f3b57 100%); color: #fff;">
        <div class="offcanvas-header border-bottom border-light border-opacity-10">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-chip"
                    style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;">
                    RS
                </div>
                <div>
                    <div class="small text-white-50 text-uppercase fw-semibold">Rental System</div>
                    <div class="h6 mb-0 fw-bold">Admin Oversight</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link text-white" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                <a class="nav-link text-white" href="{{ route('admin.properties') }}">
                    <i class="fas fa-building me-2"></i> Properties
                </a>
                <a class="nav-link text-white" href="{{ route('admin.landlords') }}">
                    <i class="fas fa-user-tie me-2"></i> Landlords
                </a>
                <a class="nav-link text-white" href="{{ route('admin.tenants') }}">
                    <i class="fas fa-users me-2"></i> Tenants
                </a>
                <a class="nav-link text-white" href="{{ route('admin.caretakers') }}">
                    <i class="fas fa-tools me-2"></i> Caretakers
                </a>
                <a class="nav-link text-white" href="{{ route('admin.payments') }}">
                    <i class="fas fa-credit-card me-2"></i> Payments
                </a>
                <a class="nav-link text-white" href="{{ route('admin.maintenance') }}">
                    <i class="fas fa-wrench me-2"></i> Maintenance
                </a>
                <a class="nav-link text-white" href="{{ route('admin.reports') }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
                <a class="nav-link text-white" href="{{ route('admin.users') }}">
                    <i class="fas fa-user-cog me-2"></i> Users
                </a>
                <a class="nav-link text-white" href="{{ route('admin.oversight.index') }}">
                    <i class="fas fa-eye me-2"></i> Oversight
                </a>
                <a class="nav-link {{ request()->routeIs('admin.properties*') ? 'active' : '' }}"
                    href="{{ route('admin.properties') }}">
                    <i class="fas fa-building me-2"></i> Properties
                </a>
                <a class="nav-link {{ request()->routeIs('admin.units*') ? 'active' : '' }}"
                    href="{{ route('admin.units') }}">
                    <i class="fas fa-door-open me-2"></i> Units
                </a>
            </nav>
        </div>
    </div>
@endsection
