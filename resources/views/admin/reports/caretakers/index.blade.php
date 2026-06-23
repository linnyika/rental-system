@extends('admin.layout')

@section('title', 'Caretakers')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="text-uppercase text-primary small fw-semibold mb-1">Entity List</div>
            <h1 class="h3 mb-2">Caretakers</h1>
            <p class="text-muted mb-0">Caretaker oversight with assigned properties, units, and managed tenants.</p>
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
                    <th>Properties</th>
                    <th>Units</th>
                    <th>Managed Tenants</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($caretakers as $caretaker)
                    @php
                        $managedTenants = $caretaker->properties
                            ->flatMap(fn ($property) => $property->units)
                            ->flatMap(fn ($unit) => $unit->occupancies->whereNull('end_date'))
                            ->pluck('tenant_id')
                            ->unique()
                            ->count();
                        $unitCount = $caretaker->properties->sum(fn ($property) => $property->units->count());
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $caretaker->user?->name }}</td>
                        <td>{{ $caretaker->landlord?->user?->name ?? 'Unassigned' }}</td>
                        <td>{{ number_format($caretaker->properties->count()) }}</td>
                        <td>{{ number_format($unitCount) }}</td>
                        <td>{{ number_format($managedTenants) }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('admin.caretakers.show', $caretaker) }}">Open report</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No caretakers found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
