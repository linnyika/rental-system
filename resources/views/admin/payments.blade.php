@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payments</h1>
            <p class="text-muted mb-0">Monitor all payment transactions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary">
                <i class="fas fa-download me-2"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fas fa-plus me-2"></i> Record Payment
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Total Collected</div>
                            <div class="h4 mb-0">KES 3,428,500</div>
                        </div>
                        <div class="icon bg-success-subtle text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Pending</div>
                            <div class="h4 mb-0">KES 456,000</div>
                        </div>
                        <div class="icon bg-warning-subtle text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Overdue</div>
                            <div class="h4 mb-0">KES 123,500</div>
                        </div>
                        <div class="icon bg-danger-subtle text-danger">
                            <i class="fas fa-exclamation-circle"></i>
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
                        <input type="text" class="form-control" placeholder="Search payments...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="verified">Verified</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
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
                            <th>Tenant</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Amount</th>
                            <th>Date</th>
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
                                    'date' => '2024-01-15',
                                    'status' => 'verified',
                                ],
                                [
                                    'tenant' => 'Sarah Davis',
                                    'property' => 'Sunset Villas',
                                    'unit' => 'B-202',
                                    'amount' => 32000,
                                    'date' => '2024-01-14',
                                    'status' => 'pending',
                                ],
                                [
                                    'tenant' => 'Michael Brown',
                                    'property' => 'Green Valley',
                                    'unit' => 'C-303',
                                    'amount' => 28000,
                                    'date' => '2024-01-12',
                                    'status' => 'overdue',
                                ],
                                [
                                    'tenant' => 'Emily Wilson',
                                    'property' => 'Ocean View',
                                    'unit' => 'D-404',
                                    'amount' => 55000,
                                    'date' => '2024-01-10',
                                    'status' => 'verified',
                                ],
                                [
                                    'tenant' => 'David Miller',
                                    'property' => 'Riverside Apartments',
                                    'unit' => 'A-205',
                                    'amount' => 38000,
                                    'date' => '2024-01-08',
                                    'status' => 'verified',
                                ],
                            ];
                        @endphp

                        @foreach ($payments as $payment)
                            <tr>
                                <td class="fw-semibold">{{ $payment['tenant'] }}</td>
                                <td>{{ $payment['property'] }}</td>
                                <td>{{ $payment['unit'] }}</td>
                                <td class="fw-semibold">KES {{ number_format($payment['amount']) }}</td>
                                <td>{{ $payment['date'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $payment['status'] === 'verified' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment['status']) }}
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
                                        <button class="btn btn-sm btn-outline-success" title="Verify">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-tables.pagination currentPage="1" totalPages="3" totalItems="42" perPage="10" />
        </div>
    </div>
@endsection
