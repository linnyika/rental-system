<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css">
</head>
<body class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Tenant Dashboard</h1>
            <p class="text-muted mb-0">Your current unit, payments, and maintenance requests.</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger" type="submit">Logout</button>
        </form>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="h5">Current Unit</h2>
            @if ($currentUnit)
                <div class="fw-semibold">Unit {{ $currentUnit->unit_number }}</div>
                <div class="text-muted">
                    {{ $currentUnit->property?->name }} -
                    KES {{ number_format($currentUnit->rent_amount) }}
                </div>
            @else
                <div class="text-muted">You are not currently assigned to a unit.</div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Payment History</h2>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($payments as $payment)
                        <div class="list-group-item d-flex justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold">KES {{ number_format($payment->amount) }}</div>
                                <div class="small text-muted">{{ ucfirst($payment->method) }} - {{ $payment->payment_date }}</div>
                            </div>
                            <span class="badge text-bg-secondary align-self-start">{{ ucfirst($payment->status) }}</span>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No payments recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h2 class="h5 mb-0">Maintenance Requests</h2>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($maintenanceRequests as $request)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between gap-3">
                                <div class="fw-semibold">{{ $request->description }}</div>
                                <span class="badge text-bg-secondary align-self-start">{{ str_replace('_', ' ', ucfirst($request->status)) }}</span>
                            </div>
                            <div class="small text-muted">{{ $request->created_at?->format('M d, Y') }}</div>
                        </div>
                    @empty
                        <div class="list-group-item text-muted">No maintenance requests recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
