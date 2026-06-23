@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between align-items-xl-center mb-4">
        <div>
            <div class="text-uppercase fw-semibold text-primary small mb-1">System Overview</div>
            <h1 class="h3 mb-2">Admin Dashboard</h1>
            <p class="text-muted mb-0">Platform-wide statistics and reporting entry points for the full rental ecosystem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="{{ route('admin.landlords.index') }}">Landlords</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.tenants.index') }}">Tenants</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.caretakers.index') }}">Caretakers</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.properties.index') }}">Properties</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Users</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_users']) }}</div>
                        </div>
                        <span class="icon">U</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Landlords</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_landlords']) }}</div>
                        </div>
                        <span class="icon">L</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Tenants</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_tenants']) }}</div>
                        </div>
                        <span class="icon">T</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Caretakers</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_caretakers']) }}</div>
                        </div>
                        <span class="icon">C</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Properties</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_properties']) }}</div>
                        </div>
                        <span class="icon">P</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Units</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_units']) }}</div>
                        </div>
                        <span class="icon">U</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Occupied Units</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_occupied_units']) }}</div>
                        </div>
                        <span class="icon bg-success-subtle text-success">O</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Vacant Units</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_vacant_units']) }}</div>
                        </div>
                        <span class="icon bg-warning-subtle text-warning">V</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Active Leases</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_active_leases']) }}</div>
                        </div>
                        <span class="icon bg-info-subtle text-info">A</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Rent Collected</div>
                            <div class="fs-3 fw-semibold">KES {{ number_format($stats['total_rent_collected']) }}</div>
                        </div>
                        <span class="icon bg-primary-subtle text-primary">R</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Pending Payments</div>
                            <div class="fs-3 fw-semibold">{{ number_format($stats['total_pending_payments']) }}</div>
                        </div>
                        <span class="icon bg-danger-subtle text-danger">P</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Recent Registrations</div>
                        <div class="small text-muted">Latest user accounts across all roles.</div>
                    </div>
                    <span class="badge text-bg-light stat-badge">{{ $recentRegistrations->count() }} shown</span>
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
                                <td class="fw-semibold">
                                    {{ $user->name }}
                                    @if($user->role === 'landlord' && $user->landlord)
                                        <div class="small"><a href="{{ route('admin.landlords.show', $user->landlord) }}">View report</a></div>
                                    @elseif($user->role === 'tenant' && $user->tenant)
                                        <div class="small"><a href="{{ route('admin.tenants.show', $user->tenant) }}">View report</a></div>
                                    @elseif($user->role === 'caretaker' && $user->caretaker)
                                        <div class="small"><a href="{{ route('admin.caretakers.show', $user->caretaker) }}">View report</a></div>
                                    @endif
                                </td>
                                <td class="text-capitalize">{{ $user->role }}</td>
                                <td>
                                    <div>{{ $user->email ?? 'No email' }}</div>
                                    <div class="small text-muted">{{ $user->phone ?? 'No phone' }}</div>
                                </td>
                                <td>{{ $user->created_at?->format('M d, Y H:i') }}</td>
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
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.landlords.index') }}">
                        <span>Landlords</span><span class="badge text-bg-light">{{ number_format($stats['total_landlords']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.tenants.index') }}">
                        <span>Tenants</span><span class="badge text-bg-light">{{ number_format($stats['total_tenants']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.caretakers.index') }}">
                        <span>Caretakers</span><span class="badge text-bg-light">{{ number_format($stats['total_caretakers']) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="{{ route('admin.properties.index') }}">
                        <span>Properties</span><span class="badge text-bg-light">{{ number_format($stats['total_properties']) }}</span>
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
                                        {{ $payment->tenant?->user?->name ?? 'Unknown tenant' }}
                                        @if($payment->unit)
                                            - {{ $payment->unit->property?->name }} / {{ $payment->unit->unit_number }}
                                        @endif
                                    </div>
                                </div>
                                <span class="badge {{ $payment->status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }} align-self-start text-capitalize">{{ $payment->status }}</span>
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
