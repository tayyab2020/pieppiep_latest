@if($subcategories->isNotEmpty())
    <div class="mb-4">
        <h2>Subcategories</h2>
        <div class="d-flex flex-wrap">
            @foreach($subcategories as $subcategory)
                <a href="{{ route('shop.home', ['shop' => $shop, 'category_id' => request('category_id'), 'subcategory_id' => $subcategory->id]) }}" class="btn subcategory m-2 {{ request('subcategory_id') == $subcategory->id ? 'selected' : '' }}">
                    {{ $subcategory->cat_name }}
                </a>
            @endforeach
        </div>
    </div>
@endif
