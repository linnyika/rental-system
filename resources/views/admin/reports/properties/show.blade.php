@extends('admin.layout')

@section('title', $property->name . ' - Property Report')

@section('content')
    @php
        $maxTrend = max(1, collect($monthlyRevenueTrends)->max('total'));
    @endphp

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Detailed Entity Report</div>
            <h1 class="h3 mb-2">{{ $property->name }}</h1>
            <p class="text-muted mb-0">Property details, units, occupancy statistics, tenants, and revenue reports.</p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">Back to Properties</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Units</div>
            <div class="fs-3 fw-semibold">{{ number_format($units->count()) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Occupied</div>
            <div class="fs-3 fw-semibold">{{ number_format($occupiedUnits->count()) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Vacant</div>
            <div class="fs-3 fw-semibold">{{ number_format($vacantUnits->count()) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Occupancy</div>
            <div class="fs-3 fw-semibold">{{ $units->count() ? round(($occupiedUnits->count() / $units->count()) * 100) : 0 }}%</div>
        </div></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Property Details</div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Name</dt>
                        <dd class="col-sm-7">{{ $property->name }}</dd>
                        <dt class="col-sm-5 text-muted">Address</dt>
                        <dd class="col-sm-7">{{ $property->address ?? 'No address' }}</dd>
                        <dt class="col-sm-5 text-muted">Landlord</dt>
                        <dd class="col-sm-7">{{ $property->landlord?->user?->name ?? 'Unknown' }}</dd>
                        <dt class="col-sm-5 text-muted">Caretaker</dt>
                        <dd class="col-sm-7">{{ $property->caretaker?->user?->name ?? 'Unassigned' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Financial Reports</div>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><div class="border rounded p-3 bg-light">
                            <div class="small text-muted">Expected</div>
                            <div class="fw-semibold">KES {{ number_format($financials['expected']) }}</div>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light">
                            <div class="small text-muted">Collected</div>
                            <div class="fw-semibold">KES {{ number_format($financials['collected']) }}</div>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light">
                            <div class="small text-muted">Outstanding</div>
                            <div class="fw-semibold">KES {{ number_format($financials['outstanding']) }}</div>
                        </div></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($monthlyRevenueTrends as $trend)
                                <tr>
                                    <td>
                                        {{ $trend['label'] }}
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ round(($trend['total'] / $maxTrend) * 100) }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">KES {{ number_format($trend['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No revenue records found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Units</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Unit</th>
                            <th>Rent</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td class="fw-semibold">{{ $unit->unit_number }}</td>
                                <td>KES {{ number_format($unit->rent_amount) }}</td>
                                <td><span class="badge {{ $unit->is_occupied ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $unit->is_occupied ? 'Occupied' : 'Vacant' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No units found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Tenants</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Current Unit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tenants as $tenant)
                            @php
                                $currentOccupancy = $tenant->occupancies->whereNull('end_date')->sortByDesc('start_date')->first();
                                $currentUnit = $currentOccupancy?->unit;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $tenant->user?->name }}</td>
                                <td>{{ $currentUnit?->unit_number ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">No tenants assigned to this property.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card panel">
        <div class="card-header bg-white">
            <div class="section-title fw-semibold">Payment History</div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Tenant</th>
                    <th>Unit</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td>{{ $payment->tenant?->user?->name }}</td>
                        <td>{{ $payment->unit?->unit_number }}</td>
                        <td>KES {{ number_format($payment->amount) }}</td>
                        <td><span class="badge {{ $payment->status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }} text-capitalize">{{ $payment->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No payments recorded for this property.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
