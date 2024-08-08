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
        .model-select-container {
            display: flex;
            align-items: center;
        }
        .model-select {
            width: 45%;
        }
        .model-price {
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

                            <div class="form-group model-select-container">
                                <select name="model[]" class="form-control model-select">
                                    @foreach($product->models as $model)
                                        <option value="{{ $model->id }}" data-measure="{{ strtolower($model->measure) }}" data-price="{{ $model->estimated_price }}" data-quantity="{{ $model->estimated_price_quantity }}">{{ $model->model }}</option>
                                    @endforeach
                                </select>
                                <span id="modelPrice" class="model-price"></span>
                            </div>


                            <div class="form-group width-group" style="display: none;">
                                <label for="width">Enter Width (cm)</label>
                                <input type="number" name="width[]" class="form-control width-input" placeholder="100 cm">
                            </div>

                            <div class="form-group height-group" style="display: none;">
                                <label for="height">Enter Height (cm)</label>
                                <input type="number" name="height[]" class="form-control height-input" placeholder="100 cm">
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


                </form>

                <h2 class="mt-4">Total Price: <span id="totalPrice">€ 0,00</span></h2>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Function to update the main image
            function updateMainImage(image) {
                $('#mainImage').attr('src', image);
            }

  
            // Update the main image when a color is selected from the dropdown
            $('.color-select').on('change', function() {
                var image = $(this).find(':selected').data('image');
                updateMainImage(image);
            });


                  // Update the main image when a color is selected from the dropdown
                  $('.color-select').on('change', function() {
                var image = $(this).find(':selected').data('image');
                updateMainImage(image);
            });



            // Update the main image and select field when a thumbnail is clicked
            $('.color-thumbnail').on('click', function() {
                var image = $(this).data('image');
                var colorId = $(this).data('color-id');
                updateMainImage(image);
                $('.color-select').val(colorId);
            });

            // Add another row for product selection
            $('#addRow').click(function() {
                var newRow = $('.calculation-row:first').clone();
                newRow.find('input').val('');
                newRow.find('.width-group, .height-group').hide();
                newRow.appendTo('#calculationRows');
                newRow.find('.model-select').trigger('change');
            });

            // Remove a row for product selection
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.calculation-row').remove();
                calculateTotalPrice();
            });

            // Show/hide fields based on selected measure
            $('#priceCalculator').on('change', '.model-select', function() {
                updateModelPrice($(this));
                calculateTotalPrice();
            });

            // Trigger change event on page load to show price of default selected model
            $('.model-select').trigger('change');

            // Calculate the total price automatically
            $('#priceCalculator').on('input', '.quantity-input, .width-input, .height-input', function() {
                calculateTotalPrice();
            });

            // Function to update the model price display
            function updateModelPrice(element) {
                var measure = element.find('option:selected').data('measure');
                var price = parseFloat(element.find('option:selected').data('price')).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                var row = element.closest('.calculation-row');
                
                if (measure === 'per piece') {
                    row.find('.quantity-group').show();
                    row.find('.width-group, .height-group').hide();
                    row.find('.model-price').text('€ ' + price + ' per piece');
                } else if (measure === 'custom sized') {
                    row.find('.width-group, .height-group').show();
                    row.find('.model-price').text('€ ' + price + ' per m²');
                } else {
                    row.find('.width-group, .height-group').hide();
                    row.find('.model-price').text('');
                }
            }

            // Initial calculation
            calculateTotalPrice();

            function calculateTotalPrice() {
                var totalPrice = 0;

                $('.calculation-row').each(function() {
                    var model = $(this).find('.model-select option:selected');
                    var measure = model.data('measure');
                    var price = parseFloat(model.data('price'));
                    var quantity = parseInt($(this).find('.quantity-input').val());
                    var width = parseFloat($(this).find('.width-input').val());
                    var height = parseFloat($(this).find('.height-input').val());

                    if (measure === 'per piece' && !isNaN(quantity)) {
                        totalPrice += quantity * price;
                    } else if (measure === 'custom sized' && !isNaN(width) && !isNaN(height)) {
                        totalPrice += (width / 100) * (height / 100) * price * quantity;
                    }
                });

                $('#totalPrice').text('€' + totalPrice.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        });
    </script>
@endsection
