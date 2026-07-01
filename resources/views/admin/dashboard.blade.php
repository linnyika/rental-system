@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $stats = [
            'total_users' => 1247,
            'total_landlords' => 89,
            'total_tenants' => 856,
            'total_caretakers' => 47,
            'total_properties' => 234,
            'total_units' => 1023,
            'total_occupied_units' => 847,
            'total_vacant_units' => 176,
            'total_active_leases' => 811,
            'total_rent_collected' => 3428500,
            'total_pending_payments' => 28,
        ];

        $recentRegistrations = [
            (object) [
                'name' => 'John Doe',
                'role' => 'tenant',
                'email' => 'john@example.com',
                'phone' => '+254 712 345 678',
                'created_at' => now()->subHours(2),
            ],
            (object) [
                'name' => 'Jane Smith',
                'role' => 'landlord',
                'email' => 'jane@example.com',
                'phone' => '+254 723 456 789',
                'created_at' => now()->subHours(5),
            ],
            (object) [
                'name' => 'Bob Johnson',
                'role' => 'caretaker',
                'email' => 'bob@example.com',
                'phone' => '+254 734 567 890',
                'created_at' => now()->subHours(8),
            ],
            (object) [
                'name' => 'Alice Williams',
                'role' => 'tenant',
                'email' => 'alice@example.com',
                'phone' => '+254 745 678 901',
                'created_at' => now()->subHours(12),
            ],
            (object) [
                'name' => 'Charlie Brown',
                'role' => 'landlord',
                'email' => 'charlie@example.com',
                'phone' => '+254 756 789 012',
                'created_at' => now()->subHours(18),
            ],
        ];

        $recentPayments = [
            (object) [
                'amount' => 45000,
                'tenant' => (object) ['name' => 'John Doe'],
                'unit' => (object) [
                    'property' => (object) ['name' => 'Riverside Apartments'],
                    'unit_number' => 'A-101',
                ],
                'status' => 'verified',
            ],
            (object) [
                'amount' => 32000,
                'tenant' => (object) ['name' => 'Alice Williams'],
                'unit' => (object) ['property' => (object) ['name' => 'Sunset Villas'], 'unit_number' => 'B-202'],
                'status' => 'pending',
            ],
            (object) [
                'amount' => 28000,
                'tenant' => (object) ['name' => 'David Wilson'],
                'unit' => (object) ['property' => (object) ['name' => 'Green Valley'], 'unit_number' => 'C-303'],
                'status' => 'verified',
            ],
            (object) [
                'amount' => 55000,
                'tenant' => (object) ['name' => 'Sarah Davis'],
                'unit' => (object) ['property' => (object) ['name' => 'Ocean View'], 'unit_number' => 'D-404'],
                'status' => 'verified',
            ],
        ];
    @endphp

    <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between align-items-xl-center mb-4">
        <div>
            <div class="text-uppercase fw-semibold text-primary small mb-1">System Overview</div>
            <h1 class="h3 mb-2">Admin Dashboard</h1>
            <p class="text-muted mb-0">Platform-wide statistics and reporting entry points for the full rental ecosystem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.landlords') }}">Landlords</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.tenants') }}">Tenants</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.caretakers') }}">Caretakers</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.properties') }}">Properties</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Users" value="{{ number_format($stats['total_users']) }}" icon="users"
                color="primary" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Landlords" value="{{ number_format($stats['total_landlords']) }}"
                icon="user-tie" color="info" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Tenants" value="{{ number_format($stats['total_tenants']) }}" icon="user"
                color="success" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Caretakers" value="{{ number_format($stats['total_caretakers']) }}"
                icon="tools" color="warning" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Properties" value="{{ number_format($stats['total_properties']) }}"
                icon="building" color="primary" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Total Units" value="{{ number_format($stats['total_units']) }}" icon="door-open"
                color="info" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Occupied Units" value="{{ number_format($stats['total_occupied_units']) }}"
                icon="home" color="success" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Vacant Units" value="{{ number_format($stats['total_vacant_units']) }}"
                icon="home" color="warning" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Active Leases" value="{{ number_format($stats['total_active_leases']) }}"
                icon="file-signature" color="info" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Rent Collected" value="KES {{ number_format($stats['total_rent_collected']) }}"
                icon="money-bill-wave" color="success" />
        </div>
        <div class="col-6 col-lg-3">
            <x-cards.stat-card label="Pending Payments" value="{{ number_format($stats['total_pending_payments']) }}"
                icon="clock" color="danger" />
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Recent Registrations</div>
                        <div class="small text-muted">Latest user accounts across all roles.</div>
                    </div>
                    <span class="badge text-bg-light stat-badge">{{ count($recentRegistrations) }} shown</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $user)
                                <tr>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $user->role === 'landlord' ? 'info' : ($user->role === 'tenant' ? 'success' : 'warning') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $user->email ?? 'No email' }}</div>
                                        <div class="small text-muted">{{ $user->phone ?? 'No phone' }}</div>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No registrations yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card panel mb-4">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Oversight Areas</div>
                    <div class="small text-muted">Drill into each entity list and report.</div>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('admin.landlords') }}">
                        <span><i class="fas fa-user-tie me-2 text-info"></i>Landlords</span>
                        <span class="badge text-bg-light">{{ number_format($stats['total_landlords']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('admin.tenants') }}">
                        <span><i class="fas fa-user me-2 text-success"></i>Tenants</span>
                        <span class="badge text-bg-light">{{ number_format($stats['total_tenants']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('admin.caretakers') }}">
                        <span><i class="fas fa-tools me-2 text-warning"></i>Caretakers</span>
                        <span class="badge text-bg-light">{{ number_format($stats['total_caretakers']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('admin.properties') }}">
                        <span><i class="fas fa-building me-2 text-primary"></i>Properties</span>
                        <span class="badge text-bg-light">{{ number_format($stats['total_properties']) }}</span>
                    </a>
                </div>
            </div>

            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Recent Rent Activity</div>
                    <div class="small text-muted">Latest payment records in the system.</div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recentPayments as $payment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">KES {{ number_format($payment->amount) }}</div>
                                    <div class="small text-muted">
                                        {{ $payment->tenant->name ?? 'Unknown tenant' }}
                                        @if ($payment->unit)
                                            - {{ $payment->unit->property->name ?? '' }} /
                                            {{ $payment->unit->unit_number ?? '' }}
                                        @endif
                                    </div>
                                </div>
                                <span
                                    class="badge {{ $payment->status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }} align-self-start text-capitalize">{{ $payment->status }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No payment records yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
