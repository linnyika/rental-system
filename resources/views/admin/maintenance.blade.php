@extends('layouts.admin')

@section('title', 'Maintenance')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Requests</h1>
            <p class="text-muted mb-0">Manage all maintenance requests across properties</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="fas fa-file-export me-2"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
                <i class="fas fa-plus me-2"></i> New Request
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Open</div>
                            <div class="h4 mb-0">12</div>
                        </div>
                        <div class="icon bg-warning-subtle text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">In Progress</div>
                            <div class="h4 mb-0">8</div>
                        </div>
                        <div class="icon bg-info-subtle text-info">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Resolved</div>
                            <div class="h4 mb-0">34</div>
                        </div>
                        <div class="icon bg-success-subtle text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Total</div>
                            <div class="h4 mb-0">54</div>
                        </div>
                        <div class="icon bg-primary-subtle text-primary">
                            <i class="fas fa-wrench"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search requests...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Reported By</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $requests = [
                                [
                                    'title' => 'Plumbing Leak',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-101',
                                    'reported_by' => 'John Doe',
                                    'priority' => 'high',
                                    'status' => 'open',
                                    'date' => '2024-01-15',
                                ],
                                [
                                    'title' => 'Electrical Fault',
                                    'property' => 'Sunset Villas',
                                    'unit' => 'B-202',
                                    'reported_by' => 'Sarah Davis',
                                    'priority' => 'high',
                                    'status' => 'in_progress',
                                    'date' => '2024-01-14',
                                ],
                                [
                                    'title' => 'Broken Window',
                                    'property' => 'Green Valley',
                                    'unit' => 'C-303',
                                    'reported_by' => 'Michael Brown',
                                    'priority' => 'medium',
                                    'status' => 'open',
                                    'date' => '2024-01-12',
                                ],
                                [
                                    'title' => 'Pest Control',
                                    'property' => 'Ocean View',
                                    'unit' => 'D-404',
                                    'reported_by' => 'Emily Wilson',
                                    'priority' => 'low',
                                    'status' => 'resolved',
                                    'date' => '2024-01-10',
                                ],
                                [
                                    'title' => 'AC Not Working',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-205',
                                    'reported_by' => 'David Miller',
                                    'priority' => 'high',
                                    'status' => 'in_progress',
                                    'date' => '2024-01-08',
                                ],
                            ];
                        @endphp

                        @foreach ($requests as $request)
                            <tr>
                                <td class="fw-semibold">{{ $request['title'] }}</td>
                                <td>{{ $request['property'] }}</td>
                                <td>{{ $request['unit'] }}</td>
                                <td>{{ $request['reported_by'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $request['priority'] === 'high' ? 'danger' : ($request['priority'] === 'medium' ? 'warning' : 'success') }}">
                                        {{ ucfirst($request['priority']) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $request['status'] === 'open' ? 'warning' : ($request['status'] === 'in_progress' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request['status'])) }}
                                    </span>
                                </td>
                                <td>{{ $request['date'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Assign">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="3" totalItems="54" perPage="10" />
        </div>
    </div>
@endsection
