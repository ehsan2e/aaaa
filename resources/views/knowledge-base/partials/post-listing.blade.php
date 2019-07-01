@forelse($posts as $post)
    <div class="card">
        <div class="card-body">
            <img src="{{ url($post->picture ?? 'images/default-post-image.png') }}" width="100%">
            <h2><a href="{{ $post->permalink }}">{{ $post->title }}</a></h2>
            {!! $post->excerpt !!}
            <div><a href="{{ $post->permalink }}">{{ __('read more ...') }}</a></div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body">
            {{ __('No post available') }}
        </div>
    </div>
@endforelse
{!! $posts->links() !!}