<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .category, .subcategory {
            background-color: lightgrey;
            color: black;
        }

        .category:hover, .subcategory:hover {
            background-color: lightblue;
        }

        .category.selected, .subcategory.selected {
            background-color: #add8e6;
            color: black;
        }

        .card-img-top, .img-fluid {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .color-thumbnails {
            display: flex;
            justify-content: start;
            margin-top: 10px;
        }

        .color-thumbnail {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            margin-right: 5px;
            cursor: pointer;
            border: 1px solid #ddd;
        }

        .plus-icon {
            width: 24px;
            height: 24px;
            background: url('path_to_plus_icon_image') no-repeat center center;
            background-size: contain;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome to {{ $shop }}'s Shop</h1>
        <p>This is a simple test view to verify the subdomain configuration.</p>
        <p>Organization: {{ $organization->shop_name }}</p>

        <!-- Categories at the top -->
        <div class="mb-4">
            <h2>Categories</h2>
            <div class="d-flex flex-wrap">
                @foreach($categories as $category)
                    <a href="{{ route('shop.home', ['shop' => $shop, 'category_id' => $category->id]) }}" class="btn category m-2 {{ request('category_id') == $category->id ? 'selected' : '' }}">
                        {{ $category->cat_name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Subcategories if a category is selected -->
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

        <div class="row">
            <!-- Brands on the left side -->
            <div class="col-md-3">
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
            </div>

            <!-- Products in the main section -->
            <div class="col-md-9">
                <h2>Products</h2>
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-md-4">
                            <div class="card mb-4">
                                @if(isset($product->colors[0]) && isset($product->colors[0]->images[0]))
                                    <img src="{{ asset('/assets/colorImages/' . $product->colors[0]->images[0]->image) }}" class="img-fluid mb-3 main-product-image" alt="{{ $product->title }}">
                                @else
                                    <img src="{{ asset('/assets/colorImages/default.jpg') }}" class="card-img-top main-product-image" alt="{{ $product->title }}">
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $product->title }}</h5>
                                    <select class="form-control mb-2">
                                        @foreach($product->models as $model)
                                            <option value="{{ $model->id }}">{{ $model->model }}: € {{ number_format($model->measure == 'm2' ? $model->estimated_price : $model->estimated_price_per_box, 2, ',', '.') }} per {{ $model->measure }}</option>
                                        @endforeach
                                    </select>
                                    @if(count($product->colors) > 1)
                                        <div class="color-thumbnails">
                                            @foreach($product->colors as $index => $color)
                                                @if($index < 6 && isset($color->images[0]))
                                                    <img src="{{ asset('/assets/colorImages/' . $color->images[0]->image) }}" class="color-thumbnail" data-image="{{ asset('/assets/colorImages/' . $color->images[0]->image) }}" alt="{{ $color->title }}">
                                                @endif
                                            @endforeach
                                            @if(count($product->colors) > 6)
                                                <a href="{{ url('shopproduct/'.$product->id) }}" class="plus-icon" title="View more colors"></a>
                                            @endif
                                        </div>
                                    @endif
                                    <a href="{{ url('shopproduct/'.$product->id) }}" class="btn btn-primary mt-2">View Product</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.color-thumbnail').forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const mainImage = this.closest('.card').querySelector('.main-product-image');
                    mainImage.src = this.getAttribute('data-image');
                });
            });
        });
    </script>
</body>
</html>
