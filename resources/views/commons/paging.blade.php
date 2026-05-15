@php $paging = $paging ?? $links ?? null; @endphp
@if ($paging)
    @include('theme::commons.pagination', ['paging' => $paging])
@endif
