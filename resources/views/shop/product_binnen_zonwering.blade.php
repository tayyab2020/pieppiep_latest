@extends('layouts.shop')

@section('categories')
    @include('partials.categories', ['categories' => $categories])
@endsection

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .model-select-container, .feature-select-container {
            display: flex;
            align-items: center;
        }
        .model-select, .feature-select {
            width: 45%;
        }
        .model-price, .feature-price {
            width: 45%;
            margin-left: 10%;
        }
        .quantity-group {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .quantity-input {
            width: 35%;
        }
        .remove-row {
            width: 25%;
            margin-left: 5%;
            margin-right: 5%;
        }
        .add-row {
            width: 30%;
            margin-right: 0%;
        }
        .remove-row img, .add-row img {
            width: 100%;
        }
        .dimensions-group {
            display: flex;
            gap: 10px;
        }
        .width-group, .height-group {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <!-- Left side: Product image and thumbnails -->
            <div class="col-md-6">
                @if(isset($product->colors[0]) && isset($product->colors[0]->images[0]))
                    <img id="mainImage" src="{{ asset('/assets/colorImages/' . $product->colors[0]->images[0]->image) }}" class="img-fluid mb-3" alt="{{ $product->title }}">
                @else
                    <img id="mainImage" src="{{ asset('/assets/colorImages/default.jpg') }}" class="img-fluid mb-3" alt="{{ $product->title }}">
                @endif
                
                <div class="row">
                    @foreach($product->colors as $color)
                        @if($color->images->isNotEmpty())
                            <div class="col-3 text-center">
                                <img src="{{ asset('/assets/colorImages/' . $color->images->first()->image) }}" class="img-thumbnail color-thumbnail" data-image="{{ asset('/assets/colorImages/' . $color->images->first()->image) }}" data-color-id="{{ $color->id }}" alt="{{ $color->title }}">
                                <div>{{ $color->title }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Product specifications -->
                <h3 class="mt-4">Specifications</h3>
                <ul>
                    <li>Model Number: {{ $product->model_number }}</li>
                    <li>Category: {{ $product->category->cat_name }}</li>
                    <li>Brand: {{ $product->brand->cat_name }}</li>
                    <li>Subcategory: {{ $subcategory }}</li>
                </ul>
            </div>
            
            <!-- Right side: Product details and calculator -->
            <div class="col-md-6">
                <h1>{{ $product->title }}</h1>
                <p>{{ $product->description }}</p>
                
                <form id="priceCalculator">
                    @csrf
                    <div id="calculationRows">
                        <div class="calculation-row">
                            <div class="dimensions-group">
                                <div class="form-group width-group">
                                    <label for="width">Enter Width (cm)</label>
                                    <input type="number" name="width[]" class="form-control width-input" placeholder="100 cm">
                                </div>

                                <div class="form-group height-group">
                                    <label for="height">Enter Height (cm)</label>
                                    <input type="number" name="height[]" class="form-control height-input" placeholder="100 cm">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="color">Select Color</label>
                                <select name="color[]" class="form-control color-select">
                                    @foreach($product->colors as $color)
                                        <option value="{{ $color->id }}" data-image="{{ asset('public/assets/colorImages/' . ($color->images->first() ? $color->images->first()->image : 'default.jpg')) }}" data-max-height="{{ $color->max_height }}" data-table-id="{{ $color->table_id }}">{{ $color->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group model-select-container">
                                <select name="model[]" class="form-control model-select">
                                    @foreach($product->models as $model)
                                        <option value="{{ $model->id }}" data-price="{{ $model->estimated_price }}" data-quantity="{{ $model->estimated_price_quantity }}" data-price-impact="{{ $model->price_impact }}" data-value="{{ $model->value }}">{{ $model->model }}</option>
                                    @endforeach
                                </select>
                                <span class="model-price">€ 0,00</span>
                            </div>

                            <div id="featureRows">
                                @if($product->features && $product->features->isNotEmpty())
                                    @foreach($product->features->groupBy('heading_id') as $headingId => $features)
                                        @if($features->isNotEmpty())
                                       
                                            <div class="form-group feature-select-container">
                                                <label for="feature_heading_{{ $headingId }}">{{ $features->first()->heading->title }}</label>
                                                <select name="features[{{ $headingId }}]" id="feature_heading_{{ $headingId }}" class="form-control feature-select">
                                                    <option value="" class="feature-placeholder">Select {{ $features->first()->heading->title }}</option>
                                                    @foreach($features as $feature)
                                                        <option value="{{ $feature->id }}" data-price-impact="{{ $feature->price_impact }}" data-value="{{ $feature->value }}">{{ $feature->title }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="feature-price">€ 0,00</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <p>No features available for this product.</p>
                                @endif
                            </div>

                            <div class="form-group quantity-heading">
                                <label for="quantity">Quantity</label>
                                <div class="form-group quantity-group">
                                    <input type="number" name="quantity[]" class="form-control quantity-input" min="1" value="1">
                                    <button type="button" class="btn btn-danger remove-row">Remove Row</button>
                                    <button type="button" id="addRow" class="btn btn-secondary">Add Another Row</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <h2 class="mt-4">Total Price: <span id="totalPrice">€ 0,00</span></h2>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Function to round price according to rules
            function roundPrice(price) {
                var roundedPrice = Math.floor(price);
                if (price - roundedPrice >= 0.50) {
                    roundedPrice += 1;
                }
                return roundedPrice;
            }

            // Function to update the main image
            function updateMainImage(image) {
                $('#mainImage').attr('src', image);
            }

            // Update the main image when a color is selected from the dropdown
            $('.color-select').on('change', function() {
                var image = $(this).find(':selected').data('image');
                updateMainImage(image);
                calculateTotalPrice(); // Recalculate the price based on the new color selection
            });

            // Update the main image when a thumbnail is clicked
            $('.color-thumbnail').on('click', function() {
                var image = $(this).data('image');
                var colorId = $(this).data('color-id');
                updateMainImage(image);
                $('.color-select').val(colorId).trigger('change'); // Trigger change event to recalculate price
            });

            // Add another row for product selection
            $('#addRow').click(function() {
                var newRow = $('.calculation-row:first').clone();
                newRow.find('input').val('');
                newRow.appendTo('#calculationRows');
                newRow.find('.model-select').trigger('change');
            });

            // Remove a row for product selection
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.calculation-row').remove();
                calculateTotalPrice();
            });

            // Update model price display and filter features based on selected model
            $('#priceCalculator').on('change', '.model-select', function() {
                updateModelPrice($(this));
                filterFeaturesBasedOnModel($(this).val());
                updateFeaturePrices($(this).closest('.calculation-row'));
                calculateTotalPrice();
            });

            // Calculate the total price automatically
            $('#priceCalculator').on('input', '.quantity-input, .width-input, .height-input, .feature-select', function() {
                updateFeaturePrices($(this).closest('.calculation-row'));
                calculateTotalPrice();
            });

            // Function to update the model price display
            function updateModelPrice(element) {
                var price = parseFloat(element.find('option:selected').data('price')) || 0;
                var value = parseFloat(element.find('option:selected').data('value')) || 0;
                var row = element.closest('.calculation-row');

                var modelPrice = (element.find('option:selected').data('price-impact') === 1) ? value : price;

                // Apply rounding
                modelPrice = roundPrice(modelPrice);

                row.find('.model-price').text('€ ' + modelPrice + ',00');
            }

            // Function to update feature prices
            function updateFeaturePrices(row) {
                row.find('.feature-select-container').each(function() {
                    var featureSelect = $(this).find('.feature-select option:selected');
                    var featurePriceImpact = parseFloat(featureSelect.data('price-impact')) || 0;
                    var featureValue = parseFloat(featureSelect.data('value')) || 0;

                    // Determine if the feature has a price impact
                    var finalFeaturePrice = (featurePriceImpact === 1) ? featureValue : 0;

                    // Apply rounding
                    finalFeaturePrice = roundPrice(finalFeaturePrice);

                    // Display the final feature price
                    $(this).find('.feature-price').text('€ ' + finalFeaturePrice + ',00');
                });
            }

            // Function to filter features based on selected model
            function filterFeaturesBasedOnModel(modelId) {
                var model = @json($product->models).find(m => m.id == modelId);

                if (model && model.linked_features) {
                console.log("linked_features");
                    console.log(model.linked_features);
                    var modelFeatures = model.linked_features.map(f => ({
                        id: f.id,
                        title: f.title,
                        value: f.value,
                        priceImpact: f.price_impact,
                        linked: f.pivot ? f.pivot.linked : 0
                    }));

                    console.log('Selected Model:', model);
                    console.log('Model Features:', modelFeatures);

                    $('.feature-select-container').each(function() {
                        $(this).show();
                        var featureSelect = $(this).find('.feature-select');
                        var featureOptions = featureSelect.find('option');

                        var flag = 0;

                        featureOptions.each(function() {
                            var option = $(this);
                            var featureId = option.val();
                            var featureData = modelFeatures.find(f => f.id == featureId);

                            if (featureData && featureData.linked == 1) {
                                flag = 1;
                                option.show();
                                option.text(featureData.title + (featureData.value ? ` (Meerprijs: €${featureData.value})` : ''));
                                if (featureData.priceImpact) {
                                    option.data('price-impact', featureData.priceImpact);
                                }
                            } else {
                                option.hide();
                            }
                        });

                        featureSelect.val("");

                        if (!flag) {
                            $(this).hide();
                        }
                    });
                } else {
                    console.warn('No linked features found for this model.');
                }
            }

            // Initial calculation
            calculateTotalPrice();

            function calculateTotalPrice() {
                var totalPrice = 0;

                $('.calculation-row').each(function() {
                    var model = $(this).find('.model-select option:selected');
                    var price = parseFloat(model.data('price')) || 0;
                    var quantity = parseInt($(this).find('.quantity-input').val()) || 0;
                    var width = parseFloat($(this).find('.width-input').val()) || 0;
                    var height = parseFloat($(this).find('.height-input').val()) || 0;

                    console.log('Model:', model.text());
                    console.log('Price:', price.toFixed(2));
                    console.log('Quantity:', quantity);
                    console.log('Width:', width.toFixed(2));
                    console.log('Height:', height.toFixed(2));

                    if (width > 0 && height > 0) {
                        var tableId = $(this).find('.color-select option:selected').data('table-id');
                        var basePrice = findBasePrice(tableId, width, height);
                        console.log('Base Price:', basePrice.toFixed(2));
                        totalPrice += basePrice * quantity;
                    } else if (quantity > 0) {
                        totalPrice += quantity * price;
                    }

                    if (model.data('price-impact') === 1) {
                        totalPrice += parseFloat(model.data('value')) || 0;
                    }
                });

                // Log selected features and their price impacts
                $('.feature-select').each(function() {
                    var feature = $(this).find('option:selected');
                    var featurePriceImpact = parseFloat(feature.data('price-impact')) || 0;
                    var featureValue = parseFloat(feature.data('value')) || 0;
                    console.log('Feature:', feature.text(), 'Price Impact:', featurePriceImpact.toFixed(2), 'Value:', featureValue.toFixed(2));
                    if (featurePriceImpact === 1) {
                        totalPrice += featureValue;
                    }
                });

                // Apply rounding to total price
                totalPrice = roundPrice(totalPrice);

                $('#totalPrice').text('€ ' + totalPrice + ',00');
            }

            function findBasePrice(tableId, width, height) {
                var basePrice = 0;
                var maxBasePrice = 0;

                @foreach($product->colors as $color)
                    if ({{ $color->table_id }} == tableId) {
                        @foreach($color->priceTable->prices as $price)
                            if (width <= {{ $price->x_axis }} && height <= {{ $price->y_axis }}) {
                                basePrice = {{ $price->value }};
                                return basePrice;
                            } else if (width > {{ $price->x_axis }} && height > {{ $price->y_axis }}) {
                                maxBasePrice = {{ $price->value }};
                            }
                        @endforeach
                    }
                @endforeach

                return maxBasePrice > 0 ? maxBasePrice : basePrice;
            }
        });
    </script>
@endsection
