@props([
    'label' => '',
    'value' => null,
    'number' => null,
    'icon' => 'chart-bar',
    'color' => 'primary',
    'iconClass' => null,
])

@php
    $displayValue = $value ?? ($number ?? '0');
    $resolvedColor = $iconClass ?? $color;
@endphp

<div {{ $attributes->merge(['class' => 'card metric']) }} role="group" aria-label="{{ $label }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <div class="label">{{ $label }}</div>
                <div class="fs-3 fw-semibold">{{ $displayValue }}</div>
            </div>
            <span class="icon bg-{{ $resolvedColor }}-subtle text-{{ $resolvedColor }}" aria-hidden="true">
                <i class="fas fa-{{ $icon }}"></i>
            </span>
        </div>
    </div>
</div>
