@extends('layouts.landlord')

@section('title', 'Reports')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reports</h1>
            <p class="text-muted mb-0">Generate financial and property reports</p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-file-pdf me-2"></i> Generate Report
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card panel h-100">
                <div class="card-body text-center">
                    <div class="icon bg-primary-subtle text-primary mx-auto mb-3 icon-square-60">
                        <i class="fas fa-building"></i>
                    </div>
                    <h5>Property Report</h5>
                    <p class="text-muted small">Detailed property performance and occupancy</p>
                    <button class="btn btn-outline-primary btn-sm">Generate</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card panel h-100">
                <div class="card-body text-center">
                    <div class="icon bg-success-subtle text-success mx-auto mb-3 icon-square-60">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h5>Financial Report</h5>
                    <p class="text-muted small">Revenue, expenses, and profit summaries</p>
                    <button class="btn btn-outline-primary btn-sm">Generate</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card panel h-100">
                <div class="card-body text-center">
                    <div class="icon bg-warning-subtle text-warning mx-auto mb-3 icon-square-60">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5>Tenant Report</h5>
                    <p class="text-muted small">Tenant demographics and lease summaries</p>
                    <button class="btn btn-outline-primary btn-sm">Generate</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card panel h-100">
                <div class="card-body text-center">
                    <div class="icon bg-danger-subtle text-danger mx-auto mb-3 icon-square-60">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h5>Maintenance Report</h5>
                    <p class="text-muted small">Maintenance cost and resolution metrics</p>
                    <button class="btn btn-outline-primary btn-sm">Generate</button>
                </div>
            </div>
        </div>
    </div>
@endsection
