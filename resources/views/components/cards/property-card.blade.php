@props([
    'title' => 'Property',
    'address' => '',
    'units' => 0,
    'tenants' => 0,
    'occupancy' => 0,
    'status' => 'active',
])

<div {{ $attributes->merge(['class' => 'card property-card h-100']) }}>
    <div class="property-image property-image-placeholder">
        <div class="d-flex align-items-center justify-content-center h-100 bg-secondary bg-opacity-10">
            <i class="fas fa-building fa-3x text-secondary icon-opacity-50" aria-hidden="true"></i>
        </div>
        <span class="property-badge bg-{{ $status === 'active' ? 'success' : 'warning' }} text-white">
            {{ ucfirst($status) }}
        </span>
    </div>
    <div class="property-body">
        <h6 class="property-title">{{ $title }}</h6>
        <div class="property-address">
            <i class="fas fa-map-marker-alt me-1 text-muted" aria-hidden="true"></i> {{ $address }}
        </div>
        <div class="property-stats">
            <div class="stat-item">
                <div class="number">{{ $units }}</div>
                <div class="label">Units</div>
            </div>
            <div class="stat-item">
                <div class="number">{{ $tenants }}</div>
                <div class="label">Tenants</div>
            </div>
            <div class="stat-item">
                <div class="number">{{ $occupancy }}%</div>
                <div class="label">Occupancy</div>
            </div>
        </div>
        {{ $slot }}
    </div>
</div>
