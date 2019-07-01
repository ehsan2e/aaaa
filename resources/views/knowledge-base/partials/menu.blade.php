@php $activeCategory = $activeCategory ?? -1 @endphp
<div class="nav flex-column nav-pills" aria-orientation="vertical">
    <a class="nav-item nav-link{{ $activeCategory == -1 ? ' active' : '' }}" aria-selected="{{ $activeCategory == -1 ? 'yes' : 'no' }}"
       href="{{ route('knowledge-base.index') }}"
       aria-controls="{{ __('All') }}"
    >{{ __('All') }}</a>
    @foreach($postCategories as $postCategory)
        <a class="nav-item nav-link{{ $activeCategory == $postCategory->id ? ' active' : '' }}" aria-selected="{{ $activeCategory == $postCategory->id ? 'yes' : 'no' }}"
           href="{{ $postCategory->permalink }}"
           aria-controls="{{ $postCategory->name }}"
        >{{ $postCategory->name }}</a>
    @endforeach
</div>