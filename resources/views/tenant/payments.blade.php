{{-- resources/views/tenant/payments.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Payments')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payments</h1>
            <p class="text-muted mb-0">Manage your rent payments</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
            <i class="fas fa-credit-card me-2"></i> Make Payment
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Total Paid</div>
                            <div class="h4 mb-0">KES 180,000</div>
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
                            <div class="small text-muted">Next Payment</div>
                            <div class="h4 mb-0">KES 45,000</div>
                        </div>
                        <div class="icon bg-info-subtle text-info">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                    <div class="small text-muted mt-2">Due: February 1, 2024</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card panel">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">Payment Status</div>
                            <div class="h4 mb-0">Up to date</div>
                        </div>
                        <div class="icon bg-success-subtle text-success">
                            <i class="fas fa-check"></i>
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
                        <option value="">All Years</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-end">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([['date' => '2024-01-01', 'description' => 'January Rent', 'amount' => 45000, 'status' => 'paid'], ['date' => '2023-12-01', 'description' => 'December Rent', 'amount' => 45000, 'status' => 'paid'], ['date' => '2023-11-01', 'description' => 'November Rent', 'amount' => 45000, 'status' => 'paid'], ['date' => '2023-10-01', 'description' => 'October Rent', 'amount' => 45000, 'status' => 'paid'], ['date' => '2023-09-01', 'description' => 'September Rent', 'amount' => 45000, 'status' => 'paid']] as $payment)
                            <tr>
                                <td>{{ $payment['date'] }}</td>
                                <td>{{ $payment['description'] }}</td>
                                <td class="fw-semibold">KES {{ number_format($payment['amount']) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment['status'] === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment['status']) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Showing 1 to 5 of 12 entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><span class="page-link">‹</span></li>
                        <li class="page-item active"><span class="page-link">1</span></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">›</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    {{-- Make Payment Modal --}}
    <div class="modal fade" id="makePaymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Make Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" value="45000">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select">
                                        <option value="">Select payment method...</option>
                                        <option value="mpesa">M-Pesa</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="card">Credit/Debit Card</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Reference</label>
                                    <input type="text" class="form-control"
                                        placeholder="Payment reference (optional)">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck">
                                    <label class="form-check-label" for="termsCheck">
                                        I confirm that the payment details are correct
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Process Payment</button>
                </div>
            </div>
        </div>
    </div>
@endsection
