<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caretaker Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css">
</head>
<body class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Caretaker Dashboard</h1>
            <p class="text-muted mb-0">Maintenance requests assigned to you.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger" type="submit">Logout</button>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Pending Tasks</div>
                    <div class="display-6">{{ number_format($pendingTasks) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Assigned Requests</div>
                    <div class="display-6">{{ number_format($maintenanceRequests->count()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Maintenance Requests</h2>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end">Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenanceRequests as $request)
                        <tr>
                            <td>{{ $request->tenant?->user?->name ?? 'Unknown tenant' }}</td>
                            <td>{{ $request->unit?->unit_number ?? 'N/A' }}</td>
                            <td>{{ $request->description }}</td>
                            <td><span class="badge text-bg-secondary">{{ str_replace('_', ' ', ucfirst($request->status)) }}</span></td>
                            <td class="text-end">{{ $request->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No assigned maintenance requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
