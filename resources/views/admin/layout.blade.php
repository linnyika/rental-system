<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css">
    <style>
        body {
            background: #f5f7fb;
            color: #17202a;
        }

        .admin-shell {
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(180deg, #13293d 0%, #1f3b57 100%);
            color: #fff;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .82);
            border-radius: .5rem;
            font-weight: 600;
            padding: .7rem .9rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .12);
        }

        .brand-chip {
            width: 44px;
            height: 44px;
            border-radius: .75rem;
            background: rgba(255, 255, 255, .12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-chip img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .panel {
            border: 0;
            box-shadow: 0 .75rem 1.5rem rgba(31, 54, 72, .08);
        }

        .metric {
            min-height: 126px;
            border: 0;
            box-shadow: 0 .75rem 1.5rem rgba(31, 54, 72, .08);
        }

        .metric .icon {
            width: 44px;
            height: 44px;
            border-radius: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: #e9f2ff;
            color: #1b4f91;
        }

        .metric .label {
            color: #6b7280;
            font-size: .88rem;
        }

        .section-title {
            letter-spacing: 0;
        }

        .stat-badge {
            font-size: .78rem;
            letter-spacing: 0;
        }

        .table > :not(caption) > * > * {
            padding: .9rem .95rem;
        }

        @media (min-width: 992px) {
            .sidebar {
                width: 290px;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="admin-shell d-lg-flex">
    <aside class="sidebar p-3 p-lg-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="brand-chip">
                @if(!empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="System Logo">
                @else
                    <span class="fw-bold">RS</span>
                @endif
            </div>
            <div>
                <div class="small text-white-50 text-uppercase fw-semibold">Rental System</div>
                <div class="h5 mb-0">Admin Oversight</div>
            </div>
        </div>

        <nav class="nav nav-pills flex-column gap-2">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="nav-link {{ request()->routeIs('admin.landlords.*') ? 'active' : '' }}" href="{{ route('admin.landlords.index') }}">Landlords</a>
            <a class="nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">Tenants</a>
            <a class="nav-link {{ request()->routeIs('admin.caretakers.*') ? 'active' : '' }}" href="{{ route('admin.caretakers.index') }}">Caretakers</a>
            <a class="nav-link {{ request()->routeIs('admin.properties.*') ? 'active' : '' }}" href="{{ route('admin.properties.index') }}">Properties</a>
        </nav>

        <div class="mt-4 pt-4 border-top border-light border-opacity-25">
            <div class="small text-white-50 mb-1">Signed in as</div>
            <div class="fw-semibold">{{ auth()->user()->name }}</div>
            <div class="small text-white-50">{{ auth()->user()->email }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="btn btn-outline-light btn-sm w-100" type="submit">Logout</button>
            </form>
        </div>
    </aside>

    <main class="flex-grow-1 p-3 p-md-4 p-xl-5">
        @yield('content')
    </main>
</div>
</body>
</html>
