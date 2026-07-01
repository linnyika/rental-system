@extends('layouts.admin')

@section('title', 'Oversight')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Oversight Dashboard</h1>
            <p class="text-muted mb-0">Complete system oversight and monitoring</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- System Health -->
        <div class="col-md-6">
            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">System Health</div>
                    <div class="small text-muted">Current system status and metrics</div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Database</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Storage</span>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress progress-w-150 progress-h-8">
                                    <div class="progress-bar bg-info w-75"></div>
                                </div>
                                <span class="small">65%</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Server Status</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Response Time</span>
                            <span class="badge bg-success">42ms</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Users</span>
                            <span class="fw-semibold">1,247</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-md-6">
            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Recent Activity</div>
                    <div class="small text-muted">Latest system events</div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex gap-3">
                            <div class="icon bg-success-subtle text-success rounded-circle p-2 icon-circle-36">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">New user registered</div>
                                <div class="small text-muted">John Doe joined as tenant</div>
                                <div class="small text-muted">2 hours ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex gap-3">
                            <div class="icon bg-warning-subtle text-warning rounded-circle p-2 icon-circle-36">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Payment received</div>
                                <div class="small text-muted">KES 45,000 from John Doe</div>
                                <div class="small text-muted">5 hours ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex gap-3">
                            <div class="icon bg-danger-subtle text-danger rounded-circle p-2 icon-circle-36">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Maintenance request</div>
                                <div class="small text-muted">Plumbing leak reported</div>
                                <div class="small text-muted">8 hours ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex gap-3">
                            <div class="icon bg-info-subtle text-info rounded-circle p-2 icon-circle-36">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">New property added</div>
                                <div class="small text-muted">Riverside Apartments</div>
                                <div class="small text-muted">1 day ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
