<div class="mb-4">
 
    <div class="d-flex flex-wrap">
        @foreach($categories as $category)
            <a href="{{ route('shop.home', ['shop' => $shop, 'category_id' => $category->id]) }}" class="btn category m-2 {{ request('category_id') == $category->id ? 'selected' : '' }}">
                {{ $category->cat_name }}
            </a>
        @endforeach
    </div>
</div>
