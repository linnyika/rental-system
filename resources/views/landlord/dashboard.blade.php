@extends('layouts.landlord')

@section('title', 'Landlord Dashboard')

@section('content')
    @php
        $stats = [
            'properties' => 8,
            'units' => 156,
            'tenants' => 142,
            'monthly_rent' => 2850000,
            'occupancy_rate' => 91,
            'pending_payments' => 12,
            'maintenance_requests' => 5,
        ];

        $recentPayments = [
            [
                'tenant' => 'John Doe',
                'property' => 'Riverside Apartments',
                'unit' => 'A-101',
                'amount' => 45000,
                'date' => '2024-01-15',
                'status' => 'paid',
            ],
            [
                'tenant' => 'Sarah Davis',
                'property' => 'Sunset Villas',
                'unit' => 'B-202',
                'amount' => 32000,
                'date' => '2024-01-14',
                'status' => 'pending',
            ],
            [
                'tenant' => 'Michael Brown',
                'property' => 'Green Valley',
                'unit' => 'C-303',
                'amount' => 28000,
                'date' => '2024-01-12',
                'status' => 'overdue',
            ],
        ];

        $maintenanceRequests = [
            [
                'title' => 'Plumbing Leak',
                'property' => 'Riverside Apartments',
                'unit' => 'A-101',
                'priority' => 'high',
                'status' => 'open',
            ],
            [
                'title' => 'Electrical Fault',
                'property' => 'Sunset Villas',
                'unit' => 'B-202',
                'priority' => 'high',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Broken Window',
                'property' => 'Green Valley',
                'unit' => 'C-303',
                'priority' => 'medium',
                'status' => 'open',
            ],
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <div class="text-uppercase fw-semibold text-primary small mb-1">Welcome back, John</div>
            <h1 class="h3 mb-2">Landlord Dashboard</h1>
            <p class="text-muted mb-0">Overview of your property portfolio and finances.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('landlord.properties') }}">
                <i class="fas fa-plus me-2"></i> Add Property
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Properties" value="{{ $stats['properties'] }}" icon="building" color="primary" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Total Units" value="{{ $stats['units'] }}" icon="door-open" color="info" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Active Tenants" value="{{ $stats['tenants'] }}" icon="users" color="success" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Occupancy Rate" value="{{ $stats['occupancy_rate'] }}%" icon="home"
                color="warning" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Monthly Rent" value="KES {{ number_format($stats['monthly_rent']) }}"
                icon="money-bill-wave" color="success" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Pending Payments" value="{{ $stats['pending_payments'] }}" icon="clock"
                color="danger" />
        </div>
        <div class="col-6 col-md-3">
            <x-cards.stat-card label="Maintenance Requests" value="{{ $stats['maintenance_requests'] }}" icon="wrench"
                color="warning" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Recent Payments</div>
                        <div class="small text-muted">Latest rent payments from tenants</div>
                    </div>
                    <a href="{{ route('landlord.payments') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant</th>
                                <th>Property</th>
                                <th>Unit</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPayments as $payment)
                                <tr>
                                    <td class="fw-semibold">{{ $payment['tenant'] }}</td>
                                    <td>{{ $payment['property'] }}</td>
                                    <td>{{ $payment['unit'] }}</td>
                                    <td class="fw-semibold">KES {{ number_format($payment['amount']) }}</td>
                                    <td>{{ $payment['date'] }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $payment['status'] === 'paid' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card panel mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Maintenance Overview</div>
                        <div class="small text-muted">Active maintenance requests</div>
                    </div>
                    <a href="{{ route('landlord.maintenance') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @foreach ($maintenanceRequests as $request)
                        <x-cards.maintenance-card title="{{ $request['title'] }}" property="{{ $request['property'] }}"
                            unit="{{ $request['unit'] }}" priority="{{ $request['priority'] }}"
                            status="{{ $request['status'] }}" />
                    @endforeach
                </div>
            </div>

            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Quick Actions</div>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('landlord.properties') }}">
                        <span><i class="fas fa-building me-2 text-primary"></i>Manage Properties</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('landlord.tenants') }}">
                        <span><i class="fas fa-users me-2 text-success"></i>Manage Tenants</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('landlord.payments') }}">
                        <span><i class="fas fa-credit-card me-2 text-warning"></i>View Payments</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('landlord.reports') }}">
                        <span><i class="fas fa-chart-line me-2 text-info"></i>Generate Reports</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
