@extends('admin.layout')

@section('title', 'Properties')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Entity List</div>
            <h1 class="h3 mb-2">Properties</h1>
            <p class="text-muted mb-0">Property oversight with unit counts, occupancy, and revenue indicators.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card panel">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Landlord</th>
                    <th>Caretaker</th>
                    <th>Units</th>
                    <th>Occupancy</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($properties as $property)
                    @php
                        $units = $property->units;
                        $occupiedUnits = $units->where('is_occupied', true)->count();
                        $occupancyRate = $units->count() ? round(($occupiedUnits / $units->count()) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $property->name }}</td>
                        <td>{{ $property->landlord?->user?->name ?? 'Unknown landlord' }}</td>
                        <td>{{ $property->caretaker?->user?->name ?? 'Unassigned' }}</td>
                        <td>{{ number_format($units->count()) }}</td>
                        <td>{{ $occupancyRate }}%</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.properties.show', $property) }}">Open report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No properties found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
