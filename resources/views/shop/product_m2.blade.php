<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
                            <div class="col-3">
                                <img src="{{ asset('/assets/colorImages/' . $color->images->first()->image) }}" class="img-thumbnail color-thumbnail" data-image="{{ asset('/assets/colorImages/' . $color->images->first()->image) }}" alt="{{ $color->title }}">
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
                    <li>Measure: {{ $measure }}</li>
                    <!-- Add other specifications here -->
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
                            <div class="form-group">
                                <label for="color">Select Color</label>
                                <select name="color[]" class="form-control color-select">
                                    @foreach($product->colors as $color)
                                        <option value="{{ $color->id }}" data-image="{{ asset('public/assets/colorImages/' . ($color->images->first() ? $color->images->first()->image : "default.jpg")) }}">{{ $color->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="model">Select Model</label>
                                <select name="model[]" class="form-control model-select">
                                    @foreach($product->models as $model)
                                        <option value="{{ $model->id }}" data-measure="{{ strtolower($model->measure) }}" data-price="{{ $model->estimated_price }}" data-quantity="{{ $model->estimated_price_quantity }}">{{ $model->model }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Enter Quantity (m2)</label>
                                <input type="number" name="quantity[]" class="form-control quantity-input" required>
                            </div>

                            <button type="button" class="btn btn-danger remove-row">Remove Row</button>
                        </div>
                    </div>

                    <button type="button" id="addRow" class="btn btn-secondary">Add Another Row</button>
                </form>

                <h2 class="mt-4">Total Price: <span id="totalPrice">€0,00</span></h2>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Update the main image when a color is selected
            $('.color-thumbnail, .color-select').on('click change', function() {
                var image = $(this).data('image') || $(this).find(':selected').data('image');
                $('#mainImage').attr('src', image);
            });

            // Add another row for product selection
            $('#addRow').click(function() {
                var newRow = $('.calculation-row:first').clone();
                newRow.find('input').val('');
                newRow.appendTo('#calculationRows');
            });

            // Remove a row for product selection
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.calculation-row').remove();
                calculateTotalPrice();
            });

            // Calculate the total price automatically
            $('#priceCalculator').on('input', '.quantity-input, .model-select', function() {
                calculateTotalPrice();
            });

            // Initial calculation
            calculateTotalPrice();

            function calculateTotalPrice() {
                var totalPrice = 0;

                $('.calculation-row').each(function() {
                    var model = $(this).find('.model-select option:selected');
                    var measure = model.data('measure');
                    var price = parseFloat(model.data('price'));
                    var quantity = parseFloat($(this).find('.quantity-input').val());

                    if (measure === 'm2') {
                        var estimatedPriceQuantity = parseFloat(model.data('quantity'));
                        var boxesNeeded = Math.ceil(quantity / estimatedPriceQuantity);
                        totalPrice += boxesNeeded * price;
                    } else {
                        totalPrice += quantity * price;
                    }
                });

                $('#totalPrice').text('€' + totalPrice.toFixed(2).replace('.', ','));
            }
        });
    </script>
</body>
</html>
