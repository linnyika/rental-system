@extends('layouts.caretaker')

@section('title', 'Tasks')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Tasks</h1>
            <p class="text-muted mb-0">Manage your assigned tasks</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-cards.stat-card label="All Tasks" value="12" icon="tasks" color="primary" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Pending" value="8" icon="clock" color="warning" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="In Progress" value="3" icon="spinner" color="info" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Completed" value="1" icon="check-circle" color="success" />
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search tasks...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Priority</option>
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
                            <th>Task</th>
                            <th>Property</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $tasks = [
                                [
                                    'title' => 'Plumbing Inspection',
                                    'property' => 'Riverside Apartments',
                                    'priority' => 'high',
                                    'status' => 'pending',
                                    'due' => '2024-01-20',
                                ],
                                [
                                    'title' => 'Security Audit',
                                    'property' => 'Sunset Villas',
                                    'priority' => 'medium',
                                    'status' => 'in_progress',
                                    'due' => '2024-01-18',
                                ],
                                [
                                    'title' => 'Garden Maintenance',
                                    'property' => 'Green Valley',
                                    'priority' => 'low',
                                    'status' => 'pending',
                                    'due' => '2024-01-25',
                                ],
                                [
                                    'title' => 'Fire Safety Check',
                                    'property' => 'Riverside Apartments',
                                    'priority' => 'high',
                                    'status' => 'completed',
                                    'due' => '2024-01-15',
                                ],
                                [
                                    'title' => 'Paint Touch-up',
                                    'property' => 'Ocean View',
                                    'priority' => 'medium',
                                    'status' => 'in_progress',
                                    'due' => '2024-01-22',
                                ],
                            ];
                        @endphp

                        @foreach ($tasks as $task)
                            <tr>
                                <td class="fw-semibold">{{ $task['title'] }}</td>
                                <td>{{ $task['property'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'success') }}">
                                        {{ ucfirst($task['priority']) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $task['status'] === 'pending' ? 'warning' : ($task['status'] === 'in_progress' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $task['status'])) }}
                                    </span>
                                </td>
                                <td>{{ $task['due'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Update Status">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Report Issue">
                                            <i class="fas fa-flag"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="2" totalItems="12" perPage="10" />
        </div>
    </div>
@endsection
