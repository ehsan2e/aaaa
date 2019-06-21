@if($hasSearchBox ?? true)
    @component('dashboard.components.search-list', [
        'canRunRawQuery' => $searchBoxConfig['canRunRawQuery'] ?? false,
        'id' => $id,
        'placeholder' => $searchBoxConfig['placeholder'] ?? '',
        'queryError' => $searchBoxConfig['queryError'] ?? null,
        'queryParamName' => $searchBoxConfig['queryParamName'] ?? 'q',
    ])
    {!! $searchBar ?? '' !!}
    @slot('extraControls') {{ $extraControls ?? '' }} @endslot
    @endcomponent
@endif
<table class="table table-striped" id="{{ $id . '-list' }}">
    <thead>
    @foreach($columnTitles as $columnTitle)
        <th>{{ $columnTitle }}</th>
    @endforeach
    <tbody>
    @forelse($collection as $item)
        @component($renderer, compact('item'))@endcomponent
    @empty
        <tr>
            <td class="text-center" colspan="{{ count($columnTitles) }}">{!! $slot !!}</td>
        </tr>
    @endforelse
    </tbody>
</table>
{!! $collection->links() !!}