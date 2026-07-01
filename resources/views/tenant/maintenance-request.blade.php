{{-- resources/views/tenant/maintenance.blade.php --}}
@extends('layouts.tenant')

@section('title', 'Maintenance Request')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Requests</h1>
            <p class="text-muted mb-0">Submit and track maintenance requests</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newRequestModal">
            <i class="fas fa-plus me-2"></i> New Request
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label text-uppercase small fw-semibold text-muted">Open</div>
                            <div class="fs-3 fw-semibold">2</div>
                        </div>
                        <div class="icon bg-warning-subtle text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label text-uppercase small fw-semibold text-muted">In Progress</div>
                            <div class="fs-3 fw-semibold">1</div>
                        </div>
                        <div class="icon bg-info-subtle text-info">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label text-uppercase small fw-semibold text-muted">Completed</div>
                            <div class="fs-3 fw-semibold">8</div>
                        </div>
                        <div class="icon bg-success-subtle text-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card metric">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="label text-uppercase small fw-semibold text-muted">Total</div>
                            <div class="fs-3 fw-semibold">11</div>
                        </div>
                        <div class="icon bg-primary-subtle text-primary">
                            <i class="fas fa-wrench"></i>
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
                        <input type="text" class="form-control" placeholder="Search requests...">
                    </div>
                </div>
                <div class="filter-group">
                    <select class="form-select">
                        <option value="">All Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select class="form-select">
                        <option value="">All Priority</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Request</th>
                            <th>Unit</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([['title' => 'Plumbing Issue', 'unit' => 'A-101', 'priority' => 'high', 'status' => 'in_progress', 'date' => '2024-01-10'], ['title' => 'AC Not Cooling', 'unit' => 'A-101', 'priority' => 'medium', 'status' => 'open', 'date' => '2024-01-12'], ['title' => 'Broken Window Latch', 'unit' => 'A-101', 'priority' => 'low', 'status' => 'open', 'date' => '2024-01-13'], ['title' => 'Pest Control', 'unit' => 'A-101', 'priority' => 'medium', 'status' => 'completed', 'date' => '2023-12-15'], ['title' => 'Electrical Issue', 'unit' => 'A-101', 'priority' => 'high', 'status' => 'completed', 'date' => '2023-12-20']] as $request)
                            <tr>
                                <td class="fw-semibold">{{ $request['title'] }}</td>
                                <td>{{ $request['unit'] }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $request['priority'] === 'high' ? 'danger' : ($request['priority'] === 'medium' ? 'warning' : 'success') }}">
                                        {{ ucfirst($request['priority']) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $request['status'] === 'open' ? 'warning' : ($request['status'] === 'in_progress' ? 'info' : 'success') }}">
                                        {{ ucfirst(str_replace('_', ' ', $request['status'])) }}
                                    </span>
                                </td>
                                <td>{{ $request['date'] }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <button class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($request['status'] !== 'completed')
                                            <button class="btn btn-sm btn-outline-danger" title="Cancel">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    Showing 1 to 5 of 11 entries
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

    {{-- New Maintenance Request Modal --}}
    <div class="modal fade" id="newRequestModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Maintenance Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Request Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter request title">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-select">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="4" placeholder="Describe the issue in detail..."></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">Attach Photos</label>
                                    <input type="file" class="form-control" multiple accept="image/*">
                                    <div class="form-text">Upload photos of the issue (optional)</div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </div>
    </div>
@endsection
