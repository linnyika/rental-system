@extends('admin.layout')

@section('title', 'Tenants')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Entity List</div>
            <h1 class="h3 mb-2">Tenants</h1>
            <p class="text-muted mb-0">All tenants with current unit assignments and balance indicators.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card panel">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Current Unit</th>
                    <th>Outstanding Balance</th>
                    <th>Payment Records</th>
                    <th></th>
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
                        <td class="fw-semibold">{{ $tenant->user?->name ?? 'Unnamed tenant' }}</td>
                        <td>
                            {{ $currentUnit?->property?->name ?? 'Vacant / unassigned' }}
                            @if($currentUnit)
                                <div class="small text-muted">Unit {{ $currentUnit->unit_number }}</div>
                            @endif
                        </td>
                        <td>KES {{ number_format($outstanding) }}</td>
                        <td>{{ number_format($tenant->payments->count()) }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.tenants.show', $tenant) }}">Open report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No tenants found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
