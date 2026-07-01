{{-- resources/views/tenant/unit.blade.php --}}
@extends('layouts.tenant')

@section('title', 'My Unit')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Unit</h1>
            <p class="text-muted mb-0">Details of your rental unit</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Unit Information</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Property</div>
                                <div class="fw-semibold">Riverside Apartments</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Unit Number</div>
                                <div class="fw-semibold">A-101</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Unit Type</div>
                                <div class="fw-semibold">2 Bedroom Apartment</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Floor</div>
                                <div class="fw-semibold">1st Floor</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Monthly Rent</div>
                                <div class="fw-semibold">KES 45,000</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Status</div>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Description</div>
                                <div class="mt-1">Spacious 2-bedroom apartment with modern amenities, located in the
                                    prestigious Riverside neighborhood.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card panel mt-4">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Lease Information</div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Lease Start</div>
                                <div class="fw-semibold">January 1, 2024</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Lease End</div>
                                <div class="fw-semibold">December 31, 2024</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Rent Due Date</div>
                                <div class="fw-semibold">1st of each month</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <div class="small text-muted">Security Deposit</div>
                                <div class="fw-semibold">KES 45,000</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Quick Actions</div>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('tenant.payments') }}"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-credit-card me-2 text-primary"></i>Make Payment</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('tenant.maintenance') }}"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-wrench me-2 text-warning"></i>Report Maintenance</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="#"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-file-pdf me-2 text-danger"></i>Download Lease</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a href="#"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-phone me-2 text-success"></i>Contact Landlord</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>

            <div class="card panel mt-4">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Caretaker Info</div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div
                            class="rounded-circle bg-warning mx-auto mb-2 d-flex align-items-center justify-content-center text-white avatar-60">
                            JC
                        </div>
                        <h6>James Caretaker</h6>
                        <p class="text-muted small">+254 712 345 678</p>
                        <button class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-phone me-2"></i> Contact Caretaker
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
