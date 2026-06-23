@extends('admin.layout')

@section('title', $caretaker->user?->name . ' - Caretaker Report')

@section('content')
    @php
        $managedTenants = $tenants->count();
    @endphp

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Detailed Entity Report</div>
            <h1 class="h3 mb-2">{{ $caretaker->user?->name ?? 'Caretaker Report' }}</h1>
            <p class="text-muted mb-0">Assigned properties, units, managed tenants, activity logs, and task history.</p>
        </div>
        <a href="{{ route('admin.caretakers.index') }}" class="btn btn-outline-secondary">Back to Caretakers</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Properties</div>
            <div class="fs-3 fw-semibold">{{ number_format($properties->count()) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Units</div>
            <div class="fs-3 fw-semibold">{{ number_format($units->count()) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Managed Tenants</div>
            <div class="fs-3 fw-semibold">{{ number_format($managedTenants) }}</div>
        </div></div></div>
        <div class="col-md-3"><div class="card metric"><div class="card-body">
            <div class="label">Activity Logs</div>
            <div class="fs-3 fw-semibold">{{ number_format($activityLogs->count()) }}</div>
        </div></div></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Caretaker Profile</div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Name</dt>
                        <dd class="col-sm-7">{{ $caretaker->user?->name }}</dd>
                        <dt class="col-sm-5 text-muted">Contact</dt>
                        <dd class="col-sm-7">{{ $caretaker->user?->phone }}<br>{{ $caretaker->user?->email }}</dd>
                        <dt class="col-sm-5 text-muted">Landlord</dt>
                        <dd class="col-sm-7">{{ $caretaker->landlord?->user?->name ?? 'Unassigned' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Assigned Properties</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Units</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($properties as $property)
                            <tr>
                                <td class="fw-semibold">{{ $property->name }}</td>
                                <td>{{ $property->address ?? 'No address' }}</td>
                                <td>{{ number_format($property->units->count()) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No properties assigned.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Managed Tenants</div>
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
                                <td>{{ $currentUnit?->property?->name ?? 'N/A' }} / {{ $currentUnit?->unit_number ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">No tenants managed by this caretaker.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card panel h-100">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Activity Logs</div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activityLogs as $log)
                            <tr>
                                <td>{{ $log->activity_date?->format('M d, Y') }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">No activity logs available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card panel">
        <div class="card-header bg-white">
            <div class="section-title fw-semibold">Task History</div>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light">
                <tr>
                    <th>Request</th>
                    <th>Tenant</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->request?->description ?? 'Maintenance task' }}</td>
                        <td>{{ $task->request?->tenant?->user?->name ?? 'N/A' }}</td>
                        <td>{{ $task->request?->unit?->property?->name }} / {{ $task->request?->unit?->unit_number }}</td>
                        <td class="text-capitalize">{{ $task->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No tasks found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
