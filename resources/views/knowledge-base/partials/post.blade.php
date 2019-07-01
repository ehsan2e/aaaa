<div class="card">
    <div class="card-body">
        <img src="{{ url($post->picture ?? 'images/default-post-image.png') }}" width="100%">
        <h1>{{ $post->title }}</h1>
        {!! $post->content !!}
    </div>
</div>