{{-- resources/views/caretaker/dashboard.blade.php --}}
@extends('caretaker.layout')

@section('title', 'Caretaker Dashboard')

@section('content')
    <div class="d-flex flex-column flex-xl-row gap-3 justify-content-between align-items-xl-center mb-4">
        <div>
            <div class="text-uppercase fw-semibold text-primary small mb-1">Caretaker Overview</div>
            <h1 class="h3 mb-2">Maintenance & Task Management</h1>
            <p class="text-muted mb-0">Complete overview of all maintenance tasks and requests assigned to you.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary" href="{{ route('caretaker.maintenance.index') }}">
                <i class="bi bi-list-task"></i> All Tasks
            </a>
            <a class="btn btn-outline-primary" href="{{ route('caretaker.maintenance.pending') }}">
                <i class="bi bi-clock"></i> Pending
            </a>
            <a class="btn btn-outline-primary" href="{{ route('caretaker.maintenance.in-progress') }}">
                <i class="bi bi-arrow-repeat"></i> In Progress
            </a>
            <a class="btn btn-outline-primary" href="{{ route('caretaker.maintenance.completed') }}">
                <i class="bi bi-check2-all"></i> Completed
            </a>
            <a class="btn btn-outline-primary" href="{{ route('caretaker.maintenance.report') }}">
                <i class="bi bi-bar-chart"></i> Report
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-danger" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Total Assigned</div>
                            <div class="fs-3 fw-semibold">{{ number_format($totalAssigned) }}</div>
                        </div>
                        <span class="icon bg-primary-subtle text-primary">
                            <i class="bi bi-clipboard-check"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Pending Tasks</div>
                            <div class="fs-3 fw-semibold">{{ number_format($pendingTasks) }}</div>
                        </div>
                        <span class="icon bg-warning-subtle text-warning">
                            <i class="bi bi-clock-history"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">In Progress</div>
                            <div class="fs-3 fw-semibold">{{ number_format($inProgress) }}</div>
                        </div>
                        <span class="icon bg-info-subtle text-info">
                            <i class="bi bi-arrow-repeat"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label">Completed</div>
                            <div class="fs-3 fw-semibold">{{ number_format($completed) }}</div>
                        </div>
                        <span class="icon bg-success-subtle text-success">
                            <i class="bi bi-check2-circle"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($pendingRequests > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>{{ number_format($pendingRequests) }} pending maintenance request(s)</strong>
                awaiting task assignment. Contact the landlord to assign these requests.
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card panel">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-title fw-semibold">Recent Maintenance Tasks</div>
                        <div class="small text-muted">Latest tasks assigned to you with their current status.</div>
                    </div>
                    <span class="badge text-bg-light stat-badge">{{ $tasks->count() }} active</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Task Details</th>
                                <th>Tenant</th>
                                <th>Unit / Property</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tasks as $task)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ Str::limit($task->request?->description ?? 'N/A', 40) }}</div>
                                        <div class="small text-muted">
                                            @if ($task->request?->is_major)
                                                <span class="badge text-bg-danger me-1">Major</span>
                                            @endif
                                            <i class="bi bi-calendar3"></i> {{ $task->created_at?->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $task->request?->tenant?->user?->name ?? 'Unknown' }}</div>
                                        <div class="small text-muted">{{ $task->request?->tenant?->user?->email ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div><strong>Unit {{ $task->request?->unit?->unit_number ?? 'N/A' }}</strong></div>
                                        <div class="small text-muted">{{ $task->request?->unit?->property?->name ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'assigned' => 'text-bg-secondary',
                                                'in_progress' => 'text-bg-info',
                                                'done' => 'text-bg-success',
                                                'cancelled' => 'text-bg-danger',
                                            ];
                                            $statusLabels = [
                                                'assigned' => 'Pending',
                                                'in_progress' => 'In Progress',
                                                'done' => 'Completed',
                                                'cancelled' => 'Cancelled',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$task->status] ?? 'text-bg-secondary' }}">
                                            {{ $statusLabels[$task->status] ?? ucfirst($task->status) }}
                                        </span>
                                        @if ($task->tenant_confirmed)
                                            <span class="badge text-bg-success mt-1 d-block">✓ Confirmed</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            @if ($task->status === 'assigned')
                                                <form method="POST"
                                                    action="{{ route('caretaker.maintenance.start', $task->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-primary" type="submit"
                                                        title="Start Task">
                                                        <i class="bi bi-play-fill"></i> Start
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($task->status === 'in_progress')
                                                <form method="POST"
                                                    action="{{ route('caretaker.maintenance.complete', $task->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" type="submit"
                                                        title="Complete Task">
                                                        <i class="bi bi-check2"></i> Complete
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($task->status === 'done' && !$task->tenant_confirmed)
                                                <form method="POST"
                                                    action="{{ route('caretaker.maintenance.confirm', $task->id) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-info" type="submit"
                                                        title="Confirm by Tenant">
                                                        <i class="bi bi-check2-all"></i> Confirm
                                                    </button>
                                                </form>
                                            @endif
                                            <a class="btn btn-sm btn-outline-secondary"
                                                href="{{ route('caretaker.maintenance.show', $task->id) }}">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <div class="py-3">
                                            <i class="bi bi-check-circle display-6 d-block mb-2 text-success"></i>
                                            <div class="mb-2">No maintenance tasks assigned to you.</div>
                                            <div class="small">You're all caught up!</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card panel mb-4">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Quick Actions</div>
                    <div class="small text-muted">Common tasks and shortcuts.</div>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('caretaker.maintenance.pending') }}">
                        <span><i class="bi bi-clock me-2"></i>View Pending Tasks</span>
                        <span class="badge text-bg-warning">{{ number_format($pendingTasks) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('caretaker.maintenance.in-progress') }}">
                        <span><i class="bi bi-arrow-repeat me-2"></i>In Progress</span>
                        <span class="badge text-bg-info">{{ number_format($inProgress) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('caretaker.maintenance.completed') }}">
                        <span><i class="bi bi-check2-all me-2"></i>Completed Tasks</span>
                        <span class="badge text-bg-success">{{ number_format($completed) }}</span>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="{{ route('caretaker.maintenance.report') }}">
                        <span><i class="bi bi-bar-chart me-2"></i>Generate Report</span>
                        <span class="badge text-bg-primary">New</span>
                    </a>
                    @if ($pendingRequests > 0)
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-warning"
                            href="#">
                            <span><i class="bi bi-exclamation-triangle me-2"></i>Unassigned Requests</span>
                            <span class="badge text-bg-warning">{{ number_format($pendingRequests) }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Task Summary</div>
                    <div class="small text-muted">Overview of your workload distribution.</div>
                </div>
                <div class="card-body">
                    @php
                        $pendingWidth = $totalAssigned > 0 ? (int) round(($pendingTasks / $totalAssigned) * 100) : 0;
                        $inProgressWidth = $totalAssigned > 0 ? (int) round(($inProgress / $totalAssigned) * 100) : 0;
                        $completedWidth = $totalAssigned > 0 ? (int) round(($completed / $totalAssigned) * 100) : 0;

                        $progressClass = static function (int $percent): string {
                            if ($percent >= 100) {
                                return 'w-100';
                            }
                            if ($percent >= 75) {
                                return 'w-75';
                            }
                            if ($percent >= 50) {
                                return 'w-50';
                            }
                            if ($percent >= 25) {
                                return 'w-25';
                            }

                            return 'w-0';
                        };
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-clock me-1"></i>Pending</span>
                            <span class="fw-semibold">{{ number_format($pendingTasks) }}</span>
                        </div>
                        <div class="progress progress-h-8">
                            <div class="progress-bar bg-warning {{ $progressClass($pendingWidth) }}" role="progressbar"
                                aria-valuenow="{{ $pendingWidth }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-arrow-repeat me-1"></i>In Progress</span>
                            <span class="fw-semibold">{{ number_format($inProgress) }}</span>
                        </div>
                        <div class="progress progress-h-8">
                            <div class="progress-bar bg-info {{ $progressClass($inProgressWidth) }}" role="progressbar"
                                aria-valuenow="{{ $inProgressWidth }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-check2-circle me-1"></i>Completed</span>
                            <span class="fw-semibold">{{ number_format($completed) }}</span>
                        </div>
                        <div class="progress progress-h-8">
                            <div class="progress-bar bg-success {{ $progressClass($completedWidth) }}" role="progressbar"
                                aria-valuenow="{{ $completedWidth }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-clipboard-check me-1"></i>Total Tasks</span>
                        <span class="fw-semibold">{{ number_format($totalAssigned) }}</span>
                    </div>
                    @if ($pendingRequests > 0)
                        <div class="d-flex justify-content-between mt-2 text-warning">
                            <span class="text-muted"><i class="bi bi-exclamation-triangle me-1"></i>Unassigned</span>
                            <span class="fw-semibold">{{ number_format($pendingRequests) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
