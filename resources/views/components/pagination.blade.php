@props(['currentPage' => 1, 'totalPages' => 1, 'totalItems' => 0, 'perPage' => 10, 'total' => null])

<x-tables.pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$totalItems" :perPage="$perPage" :total="$total"
    {{ $attributes }} />
