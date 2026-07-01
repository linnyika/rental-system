@props([
    'submitLabel' => 'Save',
    'cancelLabel' => 'Cancel',
    'cancelUrl' => null,
])

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        {{ $submitLabel }}
    </button>

    @if ($cancelUrl)
        <a href="{{ $cancelUrl }}" class="btn btn-outline-secondary">
            {{ $cancelLabel }}
        </a>
    @endif
</div>
