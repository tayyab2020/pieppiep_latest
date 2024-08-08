<table>
    <thead>
    <tr>
        <th>Article ID</th>
        <th>Image Path</th>
        <th>Title</th>
        <th>Slug</th>
        <th>Category</th>
        <th>Sub Category</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Model Value</th>
        <th>Max Size</th>
        <th>Max Width</th>
        <th>Max Height</th>
        <th>Price per box</th>
        <th>Price Quantity</th>
        <th>Estimated Price</th>
        <th>Type</th>
        <th>Supplier</th>
        <th>Color</th>
        <th>Features</th>
        <th>Margin</th>
        <th>Size</th>
        <th>Additional Info</th>
        <th>Floor type</th>
        <th>Floor type 2</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)

        <?php
            $features = [];
        
            foreach($product->features as $key)
            {
                $features[] = $key->title;
            }
        ?>

        <tr>
            <td>{{ $product->article_code }}</td>
            <td>{{ $product->color_image ? $gs->site."assets/colorImages/".$product->color_image : "" }}</td>
            <td>{{ $product->title }}</td>
            <td>{{ $product->slug }}</td>
            <td>{{ $product->cat_name }}</td>
            <td>{{ $product->sub_category }}
            <td>{{ $product->brand_name }}</td>
            <td>{{ $product->model }}</td>
            <td>{{ $product->model_value }}</td>
            <td>{{ $product->max_size }}</td>
            <td>{{ $product->max_width }}</td>
            <td>{{ $product->max_height }}</td>
            <td>{{ $product->estimated_price_per_box }}</td>
            <td>{{ $product->estimated_price_quantity }}</td>
            <td>{{ $product->estimated_price }}</td>
            <td>{{ $product->model_name }}</td>
            <td>{{ $product->company_name }}</td>
            <td>{{ $product->color }}</td>
            <td>{{ implode(",",$features) }}</td>
            <td>{{ $product->margin }}</td>
            <td>{{ $product->size }}</td>
            <td>{{ $product->additional_info }}</td>
            <td>{{ $product->floor_type }}</td>
            <td>{{ $product->floor_type2 }}</td>
            <td>{{ $product->description }}</td>
        </tr>
    @endforeach
    </tbody>
</table>