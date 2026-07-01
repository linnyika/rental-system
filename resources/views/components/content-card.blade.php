@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'card panel']) }}>
    @if ($title || $subtitle)
        <div class="card-header bg-white">
            @if ($title)
                <div class="section-title fw-semibold">{{ $title }}</div>
            @endif
            @if ($subtitle)
                <div class="small text-muted">{{ $subtitle }}</div>
            @endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
