<table class="table table-striped" id="{{ $id . '-list' }}">
    <thead>
    @foreach($columnTitles as $columnTitle)
        <th>{{ $columnTitle }}</th>
    @endforeach
    <tbody>
    @forelse($collection as $item)
        @component($renderer, compact('item') + $rendererData)@endcomponent
    @empty
        <tr>
            <td class="text-center" colspan="{{ count($columnTitles) }}">{!! $slot !!}</td>
        </tr>
    @endforelse
    </tbody>
</table>