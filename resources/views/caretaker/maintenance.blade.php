@extends('layouts.caretaker')

@section('title', 'Maintenance')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Requests</h1>
            <p class="text-muted mb-0">Manage maintenance requests for your properties</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
            <i class="fas fa-plus me-2"></i> New Request
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-cards.stat-card label="Open" value="5" icon="exclamation-triangle" color="warning" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="In Progress" value="3" icon="spinner" color="info" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Completed" value="12" icon="check-circle" color="success" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Total" value="20" icon="wrench" color="primary" />
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
                        <option value="">All Properties</option>
                        <option value="1">Riverside Apartments</option>
                        <option value="2">Sunset Villas</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
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
                                ],
                                [
                                    'title' => 'Electrical Fault',
                                    'property' => 'Sunset Villas',
                                    'unit' => 'B-202',
                                    'reported_by' => 'Sarah Davis',
                                    'priority' => 'high',
                                    'status' => 'in_progress',
                                ],
                                [
                                    'title' => 'Broken Window',
                                    'property' => 'Green Valley',
                                    'unit' => 'C-303',
                                    'reported_by' => 'Michael Brown',
                                    'priority' => 'medium',
                                    'status' => 'open',
                                ],
                                [
                                    'title' => 'Pest Control',
                                    'property' => 'Ocean View',
                                    'unit' => 'D-404',
                                    'reported_by' => 'Emily Wilson',
                                    'priority' => 'low',
                                    'status' => 'completed',
                                ],
                                [
                                    'title' => 'AC Not Working',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-205',
                                    'reported_by' => 'David Miller',
                                    'priority' => 'high',
                                    'status' => 'in_progress',
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
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Start Work">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="2" totalItems="20" perPage="10" />
        </div>
    </div>
@endsection
