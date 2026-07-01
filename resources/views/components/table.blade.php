@props(['columns' => [], 'rows' => [], 'actions' => false, 'searchPlaceholder' => 'Search...'])

<x-tables.table :columns="$columns" :rows="$rows" :actions="$actions" :searchPlaceholder="$searchPlaceholder" {{ $attributes }} />
