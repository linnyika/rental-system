<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css">
    <style>
        body {
            background: #f4f8fb;
        }

        .admin-shell {
            min-height: 100vh;
        }

        .sidebar {
            background: #1f3648;
            color: #fff;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .78);
            border-radius: .35rem;
            font-weight: 600;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, .12);
            color: #fff;
        }

        .metric-card {
            border: 0;
            box-shadow: 0 .75rem 1.75rem rgba(31, 54, 72, .08);
        }

        .metric-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            font-weight: 700;
        }

        .table-card,
        .panel-card {
            border: 0;
            box-shadow: 0 .75rem 1.75rem rgba(31, 54, 72, .07);
        }

        .status-dot {
            width: .65rem;
            height: .65rem;
            display: inline-block;
            border-radius: 50%;
        }

        .quick-action {
            min-height: 88px;
        }

        @media (min-width: 992px) {
            .sidebar {
                width: 280px;
            }
        }
    </style>
</head>
<body>
<div class="admin-shell d-lg-flex">
    <aside class="sidebar p-3 p-lg-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <div class="text-uppercase small text-white-50 fw-bold">Rental System</div>
                <h1 class="h4 mb-0">Admin</h1>
            </div>
            <span class="badge text-bg-info">Live</span>
        </div>

        <nav class="nav nav-pills flex-lg-column gap-2">
            <a class="nav-link active" href="/admin/dashboard">Dashboard</a>
            <a class="nav-link" href="#">Landlords</a>
            <a class="nav-link" href="#">Properties</a>
            <a class="nav-link" href="#">Tenants</a>
            <a class="nav-link" href="#">Payments</a>
            <a class="nav-link" href="#">Reports</a>
        </nav>

        <div class="mt-4 pt-4 border-top border-light border-opacity-25">
            <div class="small text-white-50 mb-2">Signed in as</div>
            <div class="fw-bold">System Admin</div>
            <button class="btn btn-outline-light btn-sm mt-3 w-100" onclick="logout()">Logout</button>
        </div>
    </aside>

    <main class="flex-grow-1 p-3 p-md-4 p-xl-5">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-uppercase text-primary fw-bold small mb-1">Administration</p>
                <h2 class="display-6 fw-semibold mb-1">Dashboard</h2>
                <p class="text-muted mb-0">Monitor properties, users, rent collection, and service requests.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary">Export</button>
                <button class="btn btn-primary">Add Landlord</button>
            </div>
        </div>

        <div class="row g-3 g-xl-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Properties</p>
                                <h3 class="mb-0">128</h3>
                            </div>
                            <span class="metric-icon bg-primary-subtle text-primary">PR</span>
                        </div>
                        <div class="small text-success mt-3">+12 added this month</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Occupied Units</p>
                                <h3 class="mb-0">86%</h3>
                            </div>
                            <span class="metric-icon bg-success-subtle text-success">OU</span>
                        </div>
                        <div class="small text-muted mt-3">412 of 479 units occupied</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Monthly Rent</p>
                                <h3 class="mb-0">KES 8.4M</h3>
                            </div>
                            <span class="metric-icon bg-info-subtle text-info">RC</span>
                        </div>
                        <div class="small text-success mt-3">91% collection rate</div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card metric-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1">Open Requests</p>
                                <h3 class="mb-0">24</h3>
                            </div>
                            <span class="metric-icon bg-warning-subtle text-warning">MR</span>
                        </div>
                        <div class="small text-danger mt-3">6 overdue follow-ups</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card table-card">
                    <div class="card-header bg-white d-flex align-items-center justify-content-between">
                        <h3 class="h5 mb-0">Recent Landlords</h3>
                        <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Properties</th>
                                <th>Units</th>
                                <th>Status</th>
                                <th class="text-end">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold">Grace Mwangi</div>
                                    <div class="small text-muted">grace@example.com</div>
                                </td>
                                <td>12</td>
                                <td>64</td>
                                <td><span class="badge text-bg-success">Active</span></td>
                                <td class="text-end">KES 0</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold">Daniel Otieno</div>
                                    <div class="small text-muted">daniel@example.com</div>
                                </td>
                                <td>8</td>
                                <td>41</td>
                                <td><span class="badge text-bg-info">Review</span></td>
                                <td class="text-end">KES 42,000</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold">Amina Hassan</div>
                                    <div class="small text-muted">amina@example.com</div>
                                </td>
                                <td>5</td>
                                <td>22</td>
                                <td><span class="badge text-bg-success">Active</span></td>
                                <td class="text-end">KES 0</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="fw-semibold">Peter Kamau</div>
                                    <div class="small text-muted">peter@example.com</div>
                                </td>
                                <td>3</td>
                                <td>18</td>
                                <td><span class="badge text-bg-warning">Pending</span></td>
                                <td class="text-end">KES 15,500</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card panel-card mb-4">
                    <div class="card-header bg-white">
                        <h3 class="h5 mb-0">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-primary w-100 quick-action">Create User</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-primary w-100 quick-action">Add Property</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 quick-action">Review Payments</button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 quick-action">Assign Tasks</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card panel-card">
                    <div class="card-header bg-white">
                        <h3 class="h5 mb-0">System Activity</h3>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex gap-3">
                            <span class="status-dot bg-success mt-2"></span>
                            <div>
                                <div class="fw-semibold">Rent payment confirmed</div>
                                <div class="small text-muted">Tenant account updated 12 minutes ago</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex gap-3">
                            <span class="status-dot bg-info mt-2"></span>
                            <div>
                                <div class="fw-semibold">New property submitted</div>
                                <div class="small text-muted">Awaiting admin review</div>
                            </div>
                        </div>
                        <div class="list-group-item d-flex gap-3">
                            <span class="status-dot bg-warning mt-2"></span>
                            <div>
                                <div class="fw-semibold">Maintenance SLA warning</div>
                                <div class="small text-muted">6 requests need follow-up</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('role');

        window.location.href = '/';
    }
</script>
</body>
</html>
