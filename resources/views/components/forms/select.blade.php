@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'required' => false])

<div class="form-group">
    @if ($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if ($required)
                <span class="required">*</span>
            @endif
        </label>
    @endif

    <select id="{{ $name }}" name="{{ $name }}" @required($required)
        {{ $attributes->class(['form-select', 'is-invalid' => $errors->has($name)]) }}>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) old($name, $selected) === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
