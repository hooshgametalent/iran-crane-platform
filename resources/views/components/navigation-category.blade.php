<span>
    <a href="{{ route('catalog.category', $category) }}">{{ $category->localized('title') }}</a>
    @if($category->children->isNotEmpty())
        <span class="submenu">
            @foreach($category->children as $child)
                @include('components.navigation-category', ['category' => $child])
            @endforeach
        </span>
    @endif
</span>
