@props([
    'title' => 'Maintenance Request',
    'property' => '',
    'status' => 'pending',
    'priority' => 'medium',
])

<div {{ $attributes->merge(['class' => 'card panel h-100']) }}>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0">{{ $title }}</h6>
            <span
                class="badge text-bg-{{ $status === 'completed' ? 'success' : ($status === 'in_progress' ? 'info' : 'warning') }}">
                {{ str_replace('_', ' ', ucfirst($status)) }}
            </span>
        </div>
        <p class="small text-muted mb-2">{{ $property }}</p>
        <span
            class="badge text-bg-{{ $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'secondary') }}">
            {{ ucfirst($priority) }} Priority
        </span>
        {{ $slot }}
    </div>
</div>
