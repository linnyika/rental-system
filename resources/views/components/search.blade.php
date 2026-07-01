@props(['placeholder' => 'Search...', 'name' => 'q', 'value' => null])

<div {{ $attributes->merge(['class' => 'search-box']) }}>
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-search" aria-hidden="true"></i></span>
        <input type="search" class="form-control" name="{{ $name }}" value="{{ $value }}"
            placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}">
    </div>
</div>
