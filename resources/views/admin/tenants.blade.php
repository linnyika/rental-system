@extends('layouts.admin')

@section('title', 'Tenants')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Tenants</h1>
            <p class="text-muted mb-0">Manage all tenants across properties</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTenantModal">
            <i class="fas fa-plus me-2"></i> Add Tenant
        </button>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search tenants...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Properties</option>
                        <option value="1">Riverside Apartments</option>
                        <option value="2">Sunset Villas</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $tenants = [
                                [
                                    'name' => 'John Doe',
                                    'email' => 'john@example.com',
                                    'phone' => '+254 712 345 678',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-101',
                                    'status' => 'active',
                                ],
                                [
                                    'name' => 'Sarah Davis',
                                    'email' => 'sarah@example.com',
                                    'phone' => '+254 723 456 789',
                                    'property' => 'Sunset Villas',
                                    'unit' => 'B-202',
                                    'status' => 'active',
                                ],
                                [
                                    'name' => 'Michael Brown',
                                    'email' => 'michael@example.com',
                                    'phone' => '+254 734 567 890',
                                    'property' => 'Green Valley',
                                    'unit' => 'C-303',
                                    'status' => 'pending',
                                ],
                                [
                                    'name' => 'Emily Wilson',
                                    'email' => 'emily@example.com',
                                    'phone' => '+254 745 678 901',
                                    'property' => 'Ocean View',
                                    'unit' => 'D-404',
                                    'status' => 'active',
                                ],
                                [
                                    'name' => 'David Miller',
                                    'email' => 'david@example.com',
                                    'phone' => '+254 756 789 012',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-205',
                                    'status' => 'inactive',
                                ],
                            ];
                        @endphp

                        @foreach ($tenants as $tenant)
                            <tr>
                                <td class="fw-semibold">{{ $tenant['name'] }}</td>
                                <td>{{ $tenant['email'] }}</td>
                                <td>{{ $tenant['phone'] }}</td>
                                <td>{{ $tenant['property'] }}</td>
                                <td>{{ $tenant['unit'] }}</td>
                                <td>
                                    <span class="status-indicator {{ $tenant['status'] }}">
                                        <span class="dot"></span>
                                        {{ ucfirst($tenant['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="3" totalItems="35" perPage="10" />
        </div>
    </div>
@endsection
