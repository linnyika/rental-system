@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">User Management</h1>
            <p class="text-muted mb-0">Manage all system users and permissions</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-2"></i> Add User
        </button>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search users...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="landlord">Landlord</option>
                        <option value="tenant">Tenant</option>
                        <option value="caretaker">Caretaker</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $users = [
                                [
                                    'name' => 'Admin User',
                                    'email' => 'admin@system.com',
                                    'role' => 'admin',
                                    'status' => 'active',
                                    'last_login' => '2024-01-15 14:30',
                                    'joined' => 'Jan 1, 2023',
                                ],
                                [
                                    'name' => 'John Landlord',
                                    'email' => 'john@landlord.com',
                                    'role' => 'landlord',
                                    'status' => 'active',
                                    'last_login' => '2024-01-14 10:15',
                                    'joined' => 'Jan 15, 2024',
                                ],
                                [
                                    'name' => 'Jane Tenant',
                                    'email' => 'jane@tenant.com',
                                    'role' => 'tenant',
                                    'status' => 'active',
                                    'last_login' => '2024-01-13 16:45',
                                    'joined' => 'Feb 20, 2024',
                                ],
                                [
                                    'name' => 'James Caretaker',
                                    'email' => 'james@caretaker.com',
                                    'role' => 'caretaker',
                                    'status' => 'active',
                                    'last_login' => '2024-01-12 09:00',
                                    'joined' => 'Mar 10, 2024',
                                ],
                                [
                                    'name' => 'Bob Johnson',
                                    'email' => 'bob@landlord.com',
                                    'role' => 'landlord',
                                    'status' => 'inactive',
                                    'last_login' => '2023-12-20 11:30',
                                    'joined' => 'Jan 5, 2024',
                                ],
                            ];
                        @endphp

                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            class="rounded-circle bg-{{ $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'landlord' ? 'primary' : ($user['role'] === 'tenant' ? 'success' : 'warning')) }} text-white d-flex align-items-center justify-content-center avatar-32">
                                            {{ substr($user['name'], 0, 2) }}
                                        </div>
                                        <span class="fw-semibold">{{ $user['name'] }}</span>
                                    </div>
                                </td>
                                <td>{{ $user['email'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'landlord' ? 'primary' : ($user['role'] === 'tenant' ? 'success' : 'warning')) }}">
                                        {{ ucfirst($user['role']) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="status-indicator {{ $user['status'] === 'suspended' ? 'inactive' : $user['status'] }}">
                                        <span class="dot"></span>
                                        {{ ucfirst($user['status']) }}
                                    </span>
                                </td>
                                <td>{{ $user['last_login'] }}</td>
                                <td>{{ $user['joined'] }}</td>
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

            <x-tables.pagination currentPage="1" totalPages="4" totalItems="48" perPage="10" />
        </div>
    </div>
@endsection
