<tr>
    <td>{{ $item->id }}</td>
    <td>{{ $item->code }}</td>
    <td>{{ $item->language }}</td>
    <td>{{ $item->author_name ?? '-' }} @if(isset($item->author_name)) <i class="fa fa-{{ $item->disclose_author ? 'eye text-success' : 'eye-slash text-danger' }}"></i> @endif</td>
    <td>{{ $item->category_name ?? '-' }}</td>
    <td>{{ $item->title }}</td>
    <td><i class="fa fa-{{ $item->active ? 'check text-success' : 'remove text-danger' }}"></i></td>
    <td><a href="{{ $item->permalink }}" target="_blank">{{ $item->permalink }}</a></td>
    <td>
        <a class="btn btn-sm btn-primary"
           href="{{ route('dashboard.admin.cms.post.edit', ['post_category' => $item]) }}">{{ __('Edit') }}</a>
    </td>
</tr>