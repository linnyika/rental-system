@props(['label' => null, 'name' => null])

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    @if ($label && $name)
        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    {{ $slot }}
</div>
