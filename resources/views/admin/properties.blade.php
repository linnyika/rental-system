@extends('layouts.admin')

@section('title', 'Properties')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Properties</h1>
            <p class="text-muted mb-0">Manage all properties in the system</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPropertyModal">
            <i class="fas fa-plus me-2"></i> Add Property
        </button>
    </div>

    <div class="card panel mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search properties...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="10">10 rows</option>
                        <option value="25">25 rows</option>
                        <option value="50">50 rows</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">Export</button>
                </div>
            </div>
        </div>
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
                ],
                [
                    'name' => 'Sunset Villas',
                    'address' => '456 Sunset Blvd, Mombasa',
                    'units' => 30,
                    'tenants' => 28,
                    'status' => 'active',
                ],
                [
                    'name' => 'Green Valley Estate',
                    'address' => '789 Valley Rd, Kisumu',
                    'units' => 60,
                    'tenants' => 52,
                    'status' => 'active',
                ],
                [
                    'name' => 'Ocean View Tower',
                    'address' => '101 Ocean Dr, Malindi',
                    'units' => 25,
                    'tenants' => 18,
                    'status' => 'maintenance',
                ],
                [
                    'name' => 'Mountain Heights',
                    'address' => '202 Hill Rd, Nakuru',
                    'units' => 40,
                    'tenants' => 35,
                    'status' => 'active',
                ],
                [
                    'name' => 'City Center Plaza',
                    'address' => '303 City Sq, Nairobi',
                    'units' => 55,
                    'tenants' => 42,
                    'status' => 'active',
                ],
            ];
        @endphp

        @foreach ($properties as $property)
            <div class="col-md-6 col-xl-4">
                <x-cards.property-card title="{{ $property['name'] }}" address="{{ $property['address'] }}"
                    units="{{ $property['units'] }}" tenants="{{ $property['tenants'] }}" status="{{ $property['status'] }}"
                    :badge="['text' => 'Premium', 'color' => 'primary']" />
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        <x-tables.pagination currentPage="1" totalPages="3" totalItems="45" perPage="10" />
    </div>

    <!-- Add Property Modal -->
    <x-modals.modal id="addPropertyModal" title="Add New Property" size="lg">
        <form>
            <div class="row">
                <div class="col-md-6">
                    <x-forms.input name="property_name" label="Property Name" required />
                </div>
                <div class="col-md-6">
                    <x-forms.input name="property_type" label="Property Type" placeholder="e.g., Apartment, Villa" />
                </div>
                <div class="col-12">
                    <x-forms.input name="address" label="Address" required />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="city" label="City" required />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="state" label="State/County" required />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="zip_code" label="ZIP Code" />
                </div>
                <div class="col-md-6">
                    <x-forms.input name="total_units" label="Total Units" type="number" required />
                </div>
                <div class="col-md-6">
                    <x-forms.select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive', 'maintenance' => 'Maintenance']" />
                </div>
                <div class="col-12">
                    <x-forms.text-area name="description" label="Description" rows="3" />
                </div>
            </div>
        </form>
    </x-modals.modal>
@endsection
