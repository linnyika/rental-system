@props(['label' => '', 'value' => null, 'number' => null, 'icon' => 'chart-bar', 'color' => 'primary'])

<x-cards.stat-card :label="$label" :value="$value" :number="$number" :icon="$icon" :color="$color"
    {{ $attributes }} />
