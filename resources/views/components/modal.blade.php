@props(['id', 'title' => 'Modal', 'size' => 'md'])

<x-modals.modal :id="$id" :title="$title" :size="$size" {{ $attributes }}>
    {{ $slot }}
</x-modals.modal>
