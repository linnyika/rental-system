{{-- resources/views/tenant/dashboard.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Tenant Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <div class="text-uppercase fw-semibold text-primary small mb-1">Welcome home, {{ Auth::user()->name ?? 'Sarah' }}
            </div>
            <h1 class="h3 mb-2">Tenant Dashboard</h1>
            <p class="text-muted mb-0">Overview of your unit and rental activity.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card panel h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">My Unit</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Property</span>
                            <span class="fw-semibold">Riverside Apartments</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Unit Number</span>
                            <span class="fw-semibold">A-101</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Type</span>
                            <span class="fw-semibold">2 Bedroom Apartment</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Monthly Rent</span>
                            <span class="fw-semibold">KES 45,000</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card panel h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Payment Summary</h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Paid</span>
                            <span class="fw-semibold">KES 180,000</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Next Due Date</span>
                            <span class="fw-semibold">2024-02-01</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Payment Status</span>
                            <span class="badge bg-success">Up to date</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('tenant.payments') }}" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-credit-card me-2"></i> Make Payment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Recent Payments</div>
                        <div class="small text-muted">Your payment history</div>
                    </div>
                    <a href="{{ route('tenant.payments') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([['amount' => 45000, 'date' => '2024-01-01', 'status' => 'paid'], ['amount' => 45000, 'date' => '2023-12-01', 'status' => 'paid'], ['amount' => 45000, 'date' => '2023-11-01', 'status' => 'paid'], ['amount' => 45000, 'date' => '2023-10-01', 'status' => 'paid']] as $payment)
                                <tr>
                                    <td>{{ $payment['date'] }}</td>
                                    <td class="fw-semibold">KES {{ number_format($payment['amount']) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment['status'] === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payment['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Maintenance Requests</div>
                        <div class="small text-muted">Your recent requests</div>
                    </div>
                    <a href="{{ route('tenant.maintenance') }}" class="btn btn-sm btn-outline-primary">New Request</a>
                </div>
                <div class="card-body">
                    @foreach ([['title' => 'Plumbing Issue', 'date' => '2024-01-10', 'status' => 'in_progress'], ['title' => 'AC Maintenance', 'date' => '2023-12-15', 'status' => 'completed']] as $request)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $request['title'] }}</div>
                                <div class="small text-muted">{{ $request['date'] }}</div>
                            </div>
                            <span class="badge bg-{{ $request['status'] === 'completed' ? 'success' : 'info' }}">
                                {{ ucfirst(str_replace('_', ' ', $request['status'])) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
