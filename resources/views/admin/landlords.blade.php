@extends('layouts.admin')

@section('title', 'Landlords')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Landlords</h1>
            <p class="text-muted mb-0">Manage all landlords and their properties</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandlordModal">
            <i class="fas fa-plus me-2"></i> Add Landlord
        </button>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search landlords...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
                            <th>Properties</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $landlords = [
                                [
                                    'name' => 'John Landlord',
                                    'email' => 'john@landlord.com',
                                    'phone' => '+254 712 345 678',
                                    'properties' => 5,
                                    'status' => 'active',
                                    'joined' => 'Jan 15, 2024',
                                ],
                                [
                                    'name' => 'Jane Smith',
                                    'email' => 'jane@landlord.com',
                                    'phone' => '+254 723 456 789',
                                    'properties' => 3,
                                    'status' => 'active',
                                    'joined' => 'Feb 20, 2024',
                                ],
                                [
                                    'name' => 'Bob Johnson',
                                    'email' => 'bob@landlord.com',
                                    'phone' => '+254 734 567 890',
                                    'properties' => 2,
                                    'status' => 'inactive',
                                    'joined' => 'Mar 10, 2024',
                                ],
                                [
                                    'name' => 'Alice Williams',
                                    'email' => 'alice@landlord.com',
                                    'phone' => '+254 745 678 901',
                                    'properties' => 7,
                                    'status' => 'active',
                                    'joined' => 'Jan 5, 2024',
                                ],
                            ];
                        @endphp

                        @foreach ($landlords as $landlord)
                            <tr>
                                <td class="fw-semibold">{{ $landlord['name'] }}</td>
                                <td>{{ $landlord['email'] }}</td>
                                <td>{{ $landlord['phone'] }}</td>
                                <td>{{ $landlord['properties'] }}</td>
                                <td>
                                    <span class="status-indicator {{ $landlord['status'] }}">
                                        <span class="dot"></span>
                                        {{ ucfirst($landlord['status']) }}
                                    </span>
                                </td>
                                <td>{{ $landlord['joined'] }}</td>
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

            <x-tables.pagination currentPage="1" totalPages="2" totalItems="24" perPage="10" />
        </div>
    </div>
@endsection
