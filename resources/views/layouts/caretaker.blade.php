@extends('layouts.app')

@section('content')
    <div class="caretaker-shell d-flex min-vh-100">
        <aside class="sidebar d-none d-lg-flex flex-column flex-shrink-0 p-3 p-xl-4"
            style="width: 280px; background: #fff; color: #17202a; min-height: 100vh; border-right: 1px solid #e9ecef; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                <div class="brand-chip"
                    style="width: 44px; height: 44px; border-radius: 12px; background: #1a7a5a; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px;">
                    RS
                </div>
                <div>
                    <div class="small text-muted text-uppercase fw-semibold">Rental System</div>
                    <div class="h6 mb-0 fw-bold">Caretaker Portal</div>
                </div>
            </div>

            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link {{ request()->routeIs('caretaker.dashboard') ? 'active' : '' }}"
                    href="{{ route('caretaker.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.properties*') ? 'active' : '' }}"
                    href="{{ route('caretaker.properties') }}">
                    <i class="fas fa-building me-2"></i> Properties
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.tasks*') ? 'active' : '' }}"
                    href="{{ route('caretaker.tasks') }}">
                    <i class="fas fa-tasks me-2"></i> Tasks
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.maintenance*') ? 'active' : '' }}"
                    href="{{ route('caretaker.maintenance') }}">
                    <i class="fas fa-wrench me-2"></i> Maintenance
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.payments*') ? 'active' : '' }}"
                    href="{{ route('caretaker.payments') }}">
                    <i class="fas fa-credit-card me-2"></i> Payments
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.activity*') ? 'active' : '' }}"
                    href="{{ route('caretaker.activity') }}">
                    <i class="fas fa-clipboard-list me-2"></i> Activity Logs
                </a>
                <a class="nav-link {{ request()->routeIs('caretaker.reports*') ? 'active' : '' }}"
                    href="{{ route('caretaker.reports') }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
            </nav>

            <div class="mt-auto pt-3 border-top">
                <div class="small text-muted mb-1">Signed in as</div>
                <div class="fw-semibold">James Caretaker</div>
                <div class="small text-muted">james@caretaker.com</div>
                <div class="mt-2">
                    <span class="badge bg-info">On Duty</span>
                </div>
                <a href="#" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </aside>

        <main class="flex-grow-1 p-3 p-md-4 p-xl-5" style="background: #f8f9fa; min-height: 100vh;">
            <div class="d-flex d-lg-none justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarOffcanvas">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="fw-bold">Caretaker Portal</div>
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

    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" style="width: 280px; background: #fff;">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="brand-chip"
                    style="width: 44px; height: 44px; border-radius: 12px; background: #1a7a5a; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px;">
                    RS
                </div>
                <div>
                    <div class="small text-muted text-uppercase fw-semibold">Rental System</div>
                    <div class="h6 mb-0 fw-bold">Caretaker Portal</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="nav nav-pills flex-column gap-1">
                <a class="nav-link" href="{{ route('caretaker.dashboard') }}">
                    <i class="fas fa-th-large me-2"></i> Dashboard
                </a>
                <a class="nav-link" href="{{ route('caretaker.properties') }}">
                    <i class="fas fa-building me-2"></i> Properties
                </a>
                <a class="nav-link" href="{{ route('caretaker.tasks') }}">
                    <i class="fas fa-tasks me-2"></i> Tasks
                </a>
                <a class="nav-link" href="{{ route('caretaker.maintenance') }}">
                    <i class="fas fa-wrench me-2"></i> Maintenance
                </a>
                <a class="nav-link" href="{{ route('caretaker.payments') }}">
                    <i class="fas fa-credit-card me-2"></i> Payments
                </a>
                <a class="nav-link" href="{{ route('caretaker.activity') }}">
                    <i class="fas fa-clipboard-list me-2"></i> Activity Logs
                </a>
                <a class="nav-link" href="{{ route('caretaker.reports') }}">
                    <i class="fas fa-chart-line me-2"></i> Reports
                </a>
            </nav>
        </div>
    </div>
@endsection
