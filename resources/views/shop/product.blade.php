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
                                        <option value="{{ $model->id }}" data-measure="{{ strtolower($model->measure) }}" data-price="{{ $model->estimated_price }}" data-quantity="{{ $model->estimated_price_quantity }}" data-max-width="{{ $model->max_size }}" data-subcategory="{{ strtolower($subcategory) }}">{{ $model->model }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group quantity-group">
                                <label for="quantity">Enter Quantity</label>
                                <input type="number" name="quantity[]" class="form-control quantity-input" required>
                            </div>

                            <div class="form-group width-group d-none">
                                <label for="width">Enter Width (cm)</label>
                                <input type="number" name="width[]" class="form-control width-input">
                            </div>

                            <div class="form-group height-group d-none">
                                <label for="height">Enter Height (cm)</label>
                                <input type="number" name="height[]" class="form-control height-input">
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
                newRow.find('.width-group, .height-group').addClass('d-none');
                newRow.find('.quantity-group').removeClass('d-none');
                newRow.appendTo('#calculationRows');
            });

            // Remove a row for product selection
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.calculation-row').remove();
                calculateTotalPrice();
            });

            // Calculate the total price automatically
            $('#priceCalculator').on('input', '.quantity-input, .width-input, .height-input, .model-select', function() {
                calculateTotalPrice();
            });

            // Toggle input fields based on measure type and subcategory
            $('#priceCalculator').on('change', '.model-select', function() {
                var measure = $(this).find('option:selected').data('measure');
                var subcategory = $(this).find('option:selected').data('subcategory');
                var row = $(this).closest('.calculation-row');

                console.log('Selected Measure:', measure);
                console.log('Selected Subcategory:', subcategory);

                if ((subcategory === 'tapijt' || subcategory === 'vinyl') && measure === 'm1') {
                    row.find('.width-group, .height-group').removeClass('d-none');
                    row.find('.quantity-group').addClass('d-none');
                } else {
                    row.find('.width-group, .height-group').addClass('d-none');
                    row.find('.quantity-group').removeClass('d-none');
                }
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
                    var width = parseFloat($(this).find('.width-input').val());
                    var height = parseFloat($(this).find('.height-input').val());
                    var estimatedPriceQuantity = parseFloat(model.data('quantity'));
                    var maxSize = parseFloat(model.data('max-width'));

                    console.log('Measure:', measure, 'Price:', price, 'Quantity:', quantity, 'Width:', width, 'Height:', height, 'Estimated Price Quantity:', estimatedPriceQuantity, 'Max Size:', maxSize);

                    if (measure === 'm2' && estimatedPriceQuantity > 0) {
                        var boxesNeeded = Math.ceil(quantity / estimatedPriceQuantity);
                        totalPrice += boxesNeeded * price;
                    } else if (measure === 'm1') {
                        var rollsNeeded = Math.ceil(width / maxSize);
                        totalPrice += rollsNeeded * (height / 100) * price;
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
