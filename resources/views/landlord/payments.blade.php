@extends('layouts.landlord')

@section('title', 'Payments')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payments</h1>
            <p class="text-muted mb-0">Track all rent payments</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="fas fa-plus me-2"></i> Record Payment
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <x-cards.stat-card label="Total Collected" value="KES 2,850,000" icon="check-circle" color="success" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="This Month" value="KES 285,000" icon="calendar" color="info" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Pending" value="KES 123,500" icon="clock" color="warning" />
        </div>
        <div class="col-md-3">
            <x-cards.stat-card label="Overdue" value="KES 45,000" icon="exclamation-circle" color="danger" />
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <div class="table-toolbar">
                <div class="search-box">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search payments...">
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
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $payments = [
                                [
                                    'tenant' => 'John Doe',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-101',
                                    'amount' => 45000,
                                    'due' => '2024-01-01',
                                    'status' => 'paid',
                                ],
                                [
                                    'tenant' => 'Sarah Davis',
                                    'property' => 'Sunset Villas',
                                    'unit' => 'B-202',
                                    'amount' => 32000,
                                    'due' => '2024-01-01',
                                    'status' => 'pending',
                                ],
                                [
                                    'tenant' => 'Michael Brown',
                                    'property' => 'Green Valley',
                                    'unit' => 'C-303',
                                    'amount' => 28000,
                                    'due' => '2024-01-01',
                                    'status' => 'overdue',
                                ],
                                [
                                    'tenant' => 'Emily Wilson',
                                    'property' => 'Ocean View',
                                    'unit' => 'D-404',
                                    'amount' => 55000,
                                    'due' => '2024-01-01',
                                    'status' => 'paid',
                                ],
                                [
                                    'tenant' => 'David Miller',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-205',
                                    'amount' => 38000,
                                    'due' => '2024-01-01',
                                    'status' => 'paid',
                                ],
                            ];
                        @endphp

                        @foreach ($payments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment['tenant'] }}</td>
                                <td>{{ $payment['property'] }}</td>
                                <td>{{ $payment['unit'] }}</td>
                                <td class="fw-semibold">KES {{ number_format($payment['amount']) }}</td>
                                <td>{{ $payment['due'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $payment['status'] === 'paid' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="Send Reminder">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="2" totalItems="18" perPage="10" />
        </div>
    </div>
@endsection
