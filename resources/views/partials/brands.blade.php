<h3>Filter by Brand</h3>
<ul class="list-group">
    @foreach($brands as $brand)
        <li class="list-group-item">
            <a href="{{ route('shop.home', ['shop' => $shop, 'category_id' => request('category_id'), 'subcategory_id' => request('subcategory_id'), 'brand_id' => $brand->id]) }}">
                {{ $brand->cat_name }}
            </a>
        </li>
    @endforeach
</ul>
