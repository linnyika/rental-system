@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reports</h1>
            <p class="text-muted mb-0">Generate and view system reports</p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-file-pdf me-2"></i> Generate Report
        </button>
    </div>

    <div class="row g-4">
        <!-- Report Types -->
        <div class="col-md-6 col-xl-3">
            <div class="card panel h-100">
                <div class="card-body text-center">
                    <div class="icon bg-primary-subtle text-primary mx-auto mb-3 icon-square-60">
                        <i class="fas fa-building"></i>
                    </div>
                    <h5>Property Report</h5>
                    <p class="text-muted small">Detailed property statistics and occupancy rates</p>
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
                    <p class="text-muted small">Revenue, payments, and financial summaries</p>
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
                    <p class="text-muted small">Tenant demographics and lease information</p>
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
                    <p class="text-muted small">Maintenance requests and resolution times</p>
                    <button class="btn btn-outline-primary btn-sm">Generate</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card panel mt-4">
        <div class="card-header bg-white">
            <div class="section-title fw-semibold">Recent Reports</div>
            <div class="small text-muted">Previously generated reports</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Report Name</th>
                        <th>Type</th>
                        <th>Date Generated</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $reports = [
                            [
                                'name' => 'January Financial Summary',
                                'type' => 'Financial',
                                'date' => '2024-01-31',
                                'status' => 'completed',
                            ],
                            [
                                'name' => 'Q4 Property Occupancy',
                                'type' => 'Property',
                                'date' => '2024-01-15',
                                'status' => 'completed',
                            ],
                            [
                                'name' => 'December Maintenance Report',
                                'type' => 'Maintenance',
                                'date' => '2024-01-10',
                                'status' => 'completed',
                            ],
                            [
                                'name' => 'Annual Tenant Report 2023',
                                'type' => 'Tenant',
                                'date' => '2024-01-01',
                                'status' => 'completed',
                            ],
                            [
                                'name' => 'Yearly Financial Overview',
                                'type' => 'Financial',
                                'date' => '2023-12-31',
                                'status' => 'processing',
                            ],
                        ];
                    @endphp

                    @foreach ($reports as $report)
                        <tr>
                            <td class="fw-semibold">{{ $report['name'] }}</td>
                            <td><span class="badge bg-secondary">{{ $report['type'] }}</span></td>
                            <td>{{ $report['date'] }}</td>
                            <td>
                                <span class="badge bg-{{ $report['status'] === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($report['status']) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
