@props([
    'columns' => [],
    'rows' => [],
    'actions' => false,
    'searchPlaceholder' => 'Search...',
])

<div class="table-container">
    <div class="table-toolbar">
        <div class="search-box">
            <input type="text" class="form-control" placeholder="{{ $searchPlaceholder }}"
                aria-label="{{ $searchPlaceholder }}">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ is_array($column) ? $column['label'] ?? '' : $column }}</th>
                    @endforeach
                    @if ($actions)
                        <th class="text-end">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach ($columns as $column)
                            @php
                                $key = is_array($column) ? $column['key'] ?? null : $column;
                            @endphp
                            <td>{{ $key ? $row[$key] ?? '' : '' }}</td>
                        @endforeach
                        @if ($actions)
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" type="button" aria-label="View row">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + ($actions ? 1 : 0) }}" class="text-center text-muted py-4">No
                            records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
