@php
    $rendererData = $rendererData ?? [];
    $layout = $layout?? null;
@endphp
@if($hasSearchBox ?? true)
    @component('dashboard.components.search-list', [
        'canRunRawQuery' => $searchBoxConfig['canRunRawQuery'] ?? false,
        'id' => $id,
        'layout' => $layout,
        'placeholder' => $searchBoxConfig['placeholder'] ?? '',
        'queryError' => $searchBoxConfig['queryError'] ?? null,
        'queryParamName' => $searchBoxConfig['queryParamName'] ?? 'q',
        'sortConfig' => $searchBoxConfig['sortConfig'] ?? ['options' => [], 'orderBy' => -1, 'orderByParam' => 'order_by', 'sortDirection' => \NovaVoip\Interfaces\iPaginationGenerator::SORT_ASC, 'sortDirectionParam' => 'sort_order'],
    ])
        @slot('inlineFilters') {!! $searchBarInlineFilters ?? '' !!} @endslot
        {!! $searchBar ?? '' !!}
        @slot('extraControls') {{ $extraControls ?? '' }} @endslot
        @slot('table')
            @component('dashboard.components.pagination-table', compact('collection', 'columnTitles', 'id', 'rendererData', 'renderer'))
                {!! $slot !!}
            @endcomponent
        @endslot
        @slot('pagination')
            {!! $collection->links() !!}
        @endslot
    @endcomponent
@else
    @component('dashboard.components.pagination-table', compact('collection', 'columnTitles', 'id', 'rendererData', 'renderer'))
        {!! $slot !!}
    @endcomponent
    {!! $collection->links() !!}
@endif