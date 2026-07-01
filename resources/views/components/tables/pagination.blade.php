@props([
    'currentPage' => null,
    'totalPages' => null,
    'totalItems' => null,
    'perPage' => null,
    'total' => null,
])

@php
    $current = (int) ($currentPage ?? 1);
    $pages =
        (int) ($totalPages ??
            max(1, (int) ceil(((int) ($total ?? ($totalItems ?? 0))) / max(1, (int) ($perPage ?? 10)))));
@endphp

@if ($pages > 1)
    <nav class="pagination-container" aria-label="Table pagination">
        <ul class="pagination pagination-sm justify-content-end mb-0">
            <li class="page-item {{ $current <= 1 ? 'disabled' : '' }}">
                <span class="page-link" aria-hidden="true">&laquo;</span>
            </li>
            @for ($i = 1; $i <= $pages; $i++)
                <li class="page-item {{ $i === $current ? 'active' : '' }}"
                    aria-current="{{ $i === $current ? 'page' : 'false' }}">
                    <span class="page-link">{{ $i }}</span>
                </li>
            @endfor
            <li class="page-item {{ $current >= $pages ? 'disabled' : '' }}">
                <span class="page-link" aria-hidden="true">&raquo;</span>
            </li>
        </ul>
    </nav>
@endif
