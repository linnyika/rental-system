@extends('admin.layout')

@section('title', 'Landlords')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Entity List</div>
            <h1 class="h3 mb-2">Landlords</h1>
            <p class="text-muted mb-0">Alphabetical list of all landlords with properties, units, and contact information.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card panel">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Properties</th>
                    <th>Units</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($landlords as $landlord)
                    @php
                        $propertyNames = $landlord->properties->pluck('name')->implode(', ');
                        $unitCount = $landlord->properties->sum(fn ($property) => $property->units->count());
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $landlord->user?->name ?? 'Unnamed landlord' }}</td>
                        <td>
                            <div>{{ $landlord->user?->phone ?? 'No phone' }}</div>
                            <div class="small text-muted">{{ $landlord->user?->email ?? 'No email' }}</div>
                        </td>
                        <td>
                            <div>{{ $landlord->properties->count() }} property(ies)</div>
                            <div class="small text-muted">{{ $propertyNames ?: 'No properties' }}</div>
                        </td>
                        <td>{{ number_format($unitCount) }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.landlords.show', $landlord) }}">Open report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No landlords found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
