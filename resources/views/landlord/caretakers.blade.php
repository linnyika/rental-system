@extends('layouts.landlord')

@section('title', 'Caretakers')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Caretakers</h1>
            <p class="text-muted mb-0">Manage caretakers assigned to your properties</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCaretakerModal">
            <i class="fas fa-plus me-2"></i> Assign Caretaker
        </button>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search caretakers...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Properties</option>
                        <option value="1">Riverside Apartments</option>
                        <option value="2">Sunset Villas</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="on_leave">On Leave</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Caretaker</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Assigned Properties</th>
                            <th>Tasks</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $caretakers = [
                                [
                                    'name' => 'James Caretaker',
                                    'email' => 'james@caretaker.com',
                                    'phone' => '+254 712 345 678',
                                    'properties' => 3,
                                    'tasks' => 12,
                                    'status' => 'active',
                                ],
                                [
                                    'name' => 'Mary John',
                                    'email' => 'mary@caretaker.com',
                                    'phone' => '+254 723 456 789',
                                    'properties' => 2,
                                    'tasks' => 8,
                                    'status' => 'active',
                                ],
                                [
                                    'name' => 'Peter Ochieng',
                                    'email' => 'peter@caretaker.com',
                                    'phone' => '+254 734 567 890',
                                    'properties' => 1,
                                    'tasks' => 5,
                                    'status' => 'on_leave',
                                ],
                            ];
                        @endphp

                        @foreach ($caretakers as $caretaker)
                            <tr>
                                <td class="fw-semibold">{{ $caretaker['name'] }}</td>
                                <td>{{ $caretaker['email'] }}</td>
                                <td>{{ $caretaker['phone'] }}</td>
                                <td>{{ $caretaker['properties'] }}</td>
                                <td>{{ $caretaker['tasks'] }}</td>
                                <td>
                                    <span
                                        class="status-indicator {{ $caretaker['status'] === 'on_leave' ? 'inactive' : $caretaker['status'] }}">
                                        <span class="dot"></span>
                                        {{ ucfirst(str_replace('_', ' ', $caretaker['status'])) }}
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
                                        <button class="btn btn-sm btn-outline-danger" title="Remove">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="1" totalItems="3" perPage="10" />
        </div>
    </div>
@endsection
