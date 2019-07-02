@if(count($item['children'] ?? []) === 0)
    @if($level === 0)
        <li class="nav-item ">
            <a class="nav-link @inactivepath($item['identifier'], 'active')" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
        </li>
    @else
        <a class="dropdown-item @inactivepath($item['identifier'], 'active')" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
    @endif
@else
    <li class="nav-item dropdown @inactivepath($item['identifier'], 'active')">
        <a id="{{ $item['identifier'] }}Dropdown" class="nav-link dropdown-toggle" href="{{ $item['href'] }}"
           role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
            {{ $item['label'] }} <span class="caret"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="$item['identifier'] }}Dropdown">
            @foreach($item['children'] as $child)
                @component('dashboard.partials.menu-item', ['item' => $child, 'level' => $level + 1])@endcomponent
            @endforeach
        </div>
    </li>
@endif