@extends('layouts.landlord')

@section('title', 'Properties')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Properties</h1>
            <p class="text-muted mb-0">Manage your property portfolio</p>
        </div>
        <a class="btn btn-primary" href="{{ route('landlord.properties.create') }}">
            <i class="fas fa-plus me-2"></i> Add Property
        </a>
    </div>

    <div class="row g-4">
        @php
            $properties = [
                [
                    'name' => 'Riverside Apartments',
                    'address' => '123 River Rd, Nairobi',
                    'units' => 45,
                    'tenants' => 38,
                    'status' => 'active',
                    'occupancy' => 84,
                ],
                [
                    'name' => 'Sunset Villas',
                    'address' => '456 Sunset Blvd, Mombasa',
                    'units' => 30,
                    'tenants' => 28,
                    'status' => 'active',
                    'occupancy' => 93,
                ],
                [
                    'name' => 'Green Valley Estate',
                    'address' => '789 Valley Rd, Kisumu',
                    'units' => 60,
                    'tenants' => 52,
                    'status' => 'active',
                    'occupancy' => 87,
                ],
                [
                    'name' => 'Ocean View Tower',
                    'address' => '101 Ocean Dr, Malindi',
                    'units' => 25,
                    'tenants' => 18,
                    'status' => 'maintenance',
                    'occupancy' => 72,
                ],
                [
                    'name' => 'Mountain Heights',
                    'address' => '202 Hill Rd, Nakuru',
                    'units' => 40,
                    'tenants' => 35,
                    'status' => 'active',
                    'occupancy' => 88,
                ],
                [
                    'name' => 'City Center Plaza',
                    'address' => '303 City Sq, Nairobi',
                    'units' => 55,
                    'tenants' => 42,
                    'status' => 'active',
                    'occupancy' => 76,
                ],
            ];
        @endphp

        @foreach ($properties as $property)
            <div class="col-md-6 col-xl-4">
                <div class="card property-card h-100">
                    <div class="property-image property-image-placeholder">
                        <div class="d-flex align-items-center justify-content-center h-100 bg-secondary bg-opacity-10">
                            <i class="fas fa-building fa-3x text-secondary icon-opacity-50"></i>
                        </div>
                        <span
                            class="property-badge bg-{{ $property['status'] === 'active' ? 'success' : 'warning' }} text-white">
                            {{ ucfirst($property['status']) }}
                        </span>
                    </div>
                    <div class="property-body">
                        <h6 class="property-title">{{ $property['name'] }}</h6>
                        <div class="property-address">
                            <i class="fas fa-map-marker-alt me-1 text-muted"></i> {{ $property['address'] }}
                        </div>
                        <div class="property-stats">
                            <div class="stat-item">
                                <div class="number">{{ $property['units'] }}</div>
                                <div class="label">Units</div>
                            </div>
                            <div class="stat-item">
                                <div class="number">{{ $property['tenants'] }}</div>
                                <div class="label">Tenants</div>
                            </div>
                            <div class="stat-item">
                                <div class="number">{{ $property['occupancy'] }}%</div>
                                <div class="label">Occupancy</div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary flex-grow-1">View Details</button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
