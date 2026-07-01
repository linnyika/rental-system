@extends('layouts.landlord')

@section('title', 'Caretaker Logs')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Caretaker Activity Logs</h1>
            <p class="text-muted mb-0">Monitor caretaker activities across your properties</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter me-2"></i> Filter
            </button>
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search logs...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Caretakers</option>
                        <option value="1">James Caretaker</option>
                        <option value="2">Mary John</option>
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
                            <th>Date & Time</th>
                            <th>Caretaker</th>
                            <th>Activity</th>
                            <th>Property</th>
                            <th>Details</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $logs = [
                                [
                                    'datetime' => '2024-01-15 10:30',
                                    'caretaker' => 'James Caretaker',
                                    'activity' => 'Maintenance',
                                    'property' => 'Riverside Apartments',
                                    'details' => 'Fixed plumbing leak in A-101',
                                    'status' => 'completed',
                                ],
                                [
                                    'datetime' => '2024-01-15 09:15',
                                    'caretaker' => 'Mary John',
                                    'activity' => 'Inspection',
                                    'property' => 'Sunset Villas',
                                    'details' => 'Routine property inspection',
                                    'status' => 'completed',
                                ],
                                [
                                    'datetime' => '2024-01-14 16:45',
                                    'caretaker' => 'James Caretaker',
                                    'activity' => 'Repair',
                                    'property' => 'Green Valley',
                                    'details' => 'Replaced broken window in C-303',
                                    'status' => 'completed',
                                ],
                                [
                                    'datetime' => '2024-01-14 14:30',
                                    'caretaker' => 'Peter Ochieng',
                                    'activity' => 'Maintenance',
                                    'property' => 'Ocean View',
                                    'details' => 'AC repair in D-404',
                                    'status' => 'in_progress',
                                ],
                                [
                                    'datetime' => '2024-01-14 11:00',
                                    'caretaker' => 'Mary John',
                                    'activity' => 'Payment Collection',
                                    'property' => 'Sunset Villas',
                                    'details' => 'Collected rent from B-202',
                                    'status' => 'completed',
                                ],
                            ];
                        @endphp

                        @foreach ($logs as $log)
                            <tr>
                                <td class="fw-semibold">{{ $log['datetime'] }}</td>
                                <td>{{ $log['caretaker'] }}</td>
                                <td>{{ $log['activity'] }}</td>
                                <td>{{ $log['property'] }}</td>
                                <td>{{ $log['details'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $log['status'] === 'completed' ? 'success' : 'info' }}">
                                        {{ ucfirst(str_replace('_', ' ', $log['status'])) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="4" totalItems="56" perPage="10" />
        </div>
    </div>
@endsection
