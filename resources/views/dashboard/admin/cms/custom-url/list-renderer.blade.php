<tr>
    <td>{{ $item->id }}</td>
    <td><a href="{{ $item->url }}" target="_blank">{{ $item->path }}</a></td>
    <td>
        @if(isset($item->redirect_url))
            <a href="{{ $item->target_url }}" target="_blank">{{ $item->redirect_url }}</a>
        @else
            -
        @endif
    </td>
    <td>{{ $item->redirect_status ?? '-' }}</td>
    <td>
        @if($item->handler)
            <p>{{ $item->handler_type }}</p>
            @if($item->parameters)
                <ul>
                    @foreach(is_array($item->parameters) ? $item->parameters : []  as $key => $value)
                        <li><b>{{ $key }}:</b> {{$value}}</li>
                    @endforeach
                </ul>
            @endif
        @else
            -
        @endif
    </td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.cms.custom-url.edit', ['custom_url' => $item]) }}">{{ __('Edit') }}</a>
        <a class="btn btn-sm btn-danger"
           href="Javascript:removeItem('{{ route('dashboard.admin.cms.custom-url.destroy', ['custom_url' => $item]) }}', '{{ __('Are you sure that you want to remove :url', ['url' => $item->url]) }}')"
        >{{ __('Delete') }}</a>
    </td>
</tr>