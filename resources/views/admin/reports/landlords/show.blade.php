@extends('admin.layout')

@section('title', $landlord->user?->name . ' - Landlord Report')

@section('content')
    @php
        $maxTrend = max(1, collect($monthlyRevenueTrends)->max('total'));
    @endphp

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Detailed Entity Report</div>
            <h1 class="h3 mb-2">{{ $landlord->user?->name ?? 'Landlord Report' }}</h1>
            <p class="text-muted mb-0">Profile, properties, units, caretakers, tenants, payments, and occupancy for this landlord.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.landlords.index') }}" class="btn btn-outline-secondary">Back to Landlords</a>
            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-primary">Properties</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Properties</div>
                <div class="fs-3 fw-semibold">{{ number_format($properties->count()) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Units</div>
                <div class="fs-3 fw-semibold">{{ number_format($units->count()) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Active Tenants</div>
                <div class="fs-3 fw-semibold">{{ number_format($tenants->count()) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Occupancy Rate</div>
                <div class="fs-3 fw-semibold">
                    {{ $units->count() ? round(($occupiedUnits->count() / $units->count()) * 100) : 0 }}%
                </div>
            </div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Landlord Profile</div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Name</dt>
                        <dd class="col-sm-7">{{ $landlord->user?->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-5 text-muted">Contact</dt>
                        <dd class="col-sm-7">{{ $landlord->user?->phone ?? 'N/A' }}<br>{{ $landlord->user?->email ?? 'N/A' }}</dd>
                        <dt class="col-sm-5 text-muted">Registration Date</dt>
                        <dd class="col-sm-7">{{ $landlord->created_at?->format('M d, Y H:i') }}</dd>
                        <dt class="col-sm-5 text-muted">Account Status</dt>
                        <dd class="col-sm-7"><span class="badge text-bg-success">Active</span></dd>
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
                            <div class="small text-muted">Total Rent Expected</div>
                            <div class="fs-5 fw-semibold">KES {{ number_format($financials['expected']) }}</div>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light">
                            <div class="small text-muted">Total Rent Collected</div>
                            <div class="fs-5 fw-semibold">KES {{ number_format($financials['collected']) }}</div>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-3 bg-light">
                            <div class="small text-muted">Outstanding Rent</div>
                            <div class="fs-5 fw-semibold">KES {{ number_format($financials['outstanding']) }}</div>
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
                                        <div>{{ $trend['label'] }}</div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ round(($trend['total'] / $maxTrend) * 100) }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end">KES {{ number_format($trend['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No revenue history available.</td></tr>
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
                    <div class="section-title fw-semibold">Properties</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Units</th>
                            <th>Caretaker</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($properties as $property)
                            <tr>
                                <td class="fw-semibold">{{ $property->name }}</td>
                                <td>{{ $property->address ?? 'No address' }}</td>
                                <td>{{ number_format($property->units->count()) }}</td>
                                <td>{{ $property->caretaker?->user?->name ?? 'Unassigned' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No properties found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Units</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Rent</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $unit->property?->name }}</td>
                                <td class="fw-semibold">{{ $unit->unit_number }}</td>
                                <td>KES {{ number_format($unit->rent_amount) }}</td>
                                <td>
                                    <span class="badge {{ $unit->is_occupied ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $unit->is_occupied ? 'Occupied' : 'Vacant' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No units found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Caretakers</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Activity Logs</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($caretakers as $caretaker)
                            <tr>
                                <td class="fw-semibold">{{ $caretaker->user?->name }}</td>
                                <td>{{ $caretaker->user?->phone }}<br><span class="small text-muted">{{ $caretaker->user?->email }}</span></td>
                                <td>{{ number_format($caretaker->activityLogs->count()) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No caretakers assigned.</td></tr>
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
                            <th>Outstanding</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tenants as $tenant)
                            @php
                                $currentOccupancy = $tenant->occupancies->whereNull('end_date')->sortByDesc('start_date')->first();
                                $currentUnit = $currentOccupancy?->unit;
                                $outstanding = $tenant->payments->whereIn('status', ['pending', 'rejected'])->sum('amount');
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $tenant->user?->name }}</td>
                                <td>
                                    {{ $currentUnit?->property?->name ?? 'N/A' }}
                                    @if($currentUnit)
                                        <div class="small text-muted">Unit {{ $currentUnit->unit_number }}</div>
                                    @endif
                                </td>
                                <td>KES {{ number_format($outstanding) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No tenants occupying this landlord's units.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card panel mt-4">
        <div class="card-header bg-white">
            <div class="section-title fw-semibold">Payment History</div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
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
                        <td>{{ $payment->unit?->property?->name }} / {{ $payment->unit?->unit_number }}</td>
                        <td>KES {{ number_format($payment->amount) }}</td>
                        <td><span class="badge {{ $payment->status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }} text-capitalize">{{ $payment->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">No payments recorded.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
