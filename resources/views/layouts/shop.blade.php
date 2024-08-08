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
            line-height: 48px; /* Ensure the line height matches the h1 element */
            margin-top: 0; /* Set margin-top to 0 */
        }

        .category:hover, .subcategory:hover {
            background-color: lightblue;
        }

        .category.selected, .subcategory.selected {
            background-color: #add8e6;
            color: black;
        }

        .container {
            margin-top: 0; /* Remove top margin */
            padding-top: 0; /* Remove padding from the top */
        }

        .flex-container {
            display: flex;
            align-items: flex-end; /* Align items to the bottom */
            margin-top: 5px; /* Adjust margin to create space from the top */
        }

        .categories {
            margin-left: 20px;
            display: flex;
            align-items: center;
            height: 48px; /* Ensure the height matches the h1 element */
        }

        .organization-logo {
            height: 48px; /* Adjust the logo height to match the h1 element */
            margin-right: 20px; /* Add some spacing to the right of the logo */
        }

        .btn.category.m-2 {
            padding-bottom: 0; /* Adjust the bottom padding to be 0 */
        }

        h1 {
            height: 48px; /* Ensure the height is explicitly set */
            display: flex;
            align-items: center;
            margin: 0; /* Remove default margin */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="flex-container">
            <img src="{{ url('assets/images/' . $organization->logo) }}" alt="{{ $organization->shop_name }}" class="organization-logo">
            <div class="categories">
                @yield('categories')
            </div>
        </div>

        @yield('subcategories')
        @yield('brands')

        <div class="row">
            @yield('content')
        </div>
    </div>
</body>
</html>
