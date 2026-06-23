@extends('admin.layout')

@section('title', $tenant->user?->name . ' - Tenant Report')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Detailed Entity Report</div>
            <h1 class="h3 mb-2">{{ $tenant->user?->name ?? 'Tenant Report' }}</h1>
            <p class="text-muted mb-0">Profile, lease, payments, maintenance history, and outstanding balances.</p>
        </div>
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">Back to Tenants</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Lease Status</div>
                <div class="fs-3 fw-semibold">{{ $currentUnit ? 'Active' : 'Unassigned' }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Current Unit</div>
                <div class="fs-3 fw-semibold">{{ $currentUnit?->unit_number ?? 'N/A' }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Payments</div>
                <div class="fs-3 fw-semibold">{{ number_format($payments->count()) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card metric"><div class="card-body">
                <div class="label">Outstanding</div>
                <div class="fs-3 fw-semibold">KES {{ number_format($outstandingBalance) }}</div>
            </div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Tenant Profile</div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Name</dt>
                        <dd class="col-sm-7">{{ $tenant->user?->name }}</dd>
                        <dt class="col-sm-5 text-muted">Contact</dt>
                        <dd class="col-sm-7">{{ $tenant->user?->phone }}<br>{{ $tenant->user?->email }}</dd>
                        <dt class="col-sm-5 text-muted">Registration Date</dt>
                        <dd class="col-sm-7">{{ $tenant->created_at?->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Lease Information</div>
                </div>
                <div class="card-body">
                    @if($leaseInfo)
                        <div class="row g-3">
                            <div class="col-md-4"><div class="border rounded p-3 bg-light">
                                <div class="small text-muted">Property</div>
                                <div class="fw-semibold">{{ $leaseInfo['property'] }}</div>
                            </div></div>
                            <div class="col-md-4"><div class="border rounded p-3 bg-light">
                                <div class="small text-muted">Unit</div>
                                <div class="fw-semibold">{{ $leaseInfo['unit'] }}</div>
                            </div></div>
                            <div class="col-md-4"><div class="border rounded p-3 bg-light">
                                <div class="small text-muted">Lease Start</div>
                                <div class="fw-semibold">{{ $leaseInfo['started_at']?->format('M d, Y') }}</div>
                            </div></div>
                        </div>
                    @else
                        <div class="text-muted">No open lease found for this tenant.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Payment History</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date?->format('M d, Y') }}</td>
                                <td>KES {{ number_format($payment->amount) }}</td>
                                <td><span class="badge {{ $payment->status === 'verified' ? 'text-bg-success' : 'text-bg-warning' }} text-capitalize">{{ $payment->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No payment records found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Maintenance Request History</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Unit</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($maintenanceRequests as $request)
                            <tr>
                                <td>{{ $request->created_at?->format('M d, Y') }}</td>
                                <td>{{ $request->unit?->property?->name }} / {{ $request->unit?->unit_number }}</td>
                                <td class="text-capitalize">{{ $request->status }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No maintenance requests found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
