<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\organizations;
use App\product_models;
use App\Products;
use App\sub_categories;
use Illuminate\Support\Facades\Validator;

class ShopController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request, $shop)
    {
        Log::info('Entering index method.', ['subdomain' => $shop]);

        try {
            // Find the organization by the subdomain
            $organization = organizations::where('subdomain', $shop)->firstOrFail();
            Log::info('Organization found.', ['organization_id' => $organization->id]);

            // Fetch categories and brands
            $categories = Category::all();
            $subcategories = collect([]);
            $brands = collect([]);

            // Initialize product query
            $productsQuery = Products::whereHas('retailersRequests', function ($query) use ($organization) {
                $query->where('retailer_organization', $organization->id)
                      ->where('active', 1)
                      ->where('status', 1);
            });

            if ($request->has('category_id')) {
                $categoryId = $request->input('category_id');
                $subcategories = sub_categories::where('parent_id', $categoryId)->get();
                $productsQuery->where('category_id', $categoryId);
                $brands = Brand::whereHas('products', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })->get();
            }

            if ($request->has('subcategory_id')) {
                $subcategoryId = $request->input('subcategory_id');
                $productsQuery->where('sub_category_id', $subcategoryId);
                $brands = Brand::whereHas('products', function ($query) use ($subcategoryId) {
                    $query->where('sub_category_id', $subcategoryId);
                })->get();
            }

            if ($request->has('brand_id')) {
                $productsQuery->where('brand_id', $request->input('brand_id'));
            }

            $products = $productsQuery->with(['models', 'colors.images'])->get();
            Log::info('Products retrieved.', ['products' => $products->pluck('id')]);

            // Pass the organization data, categories, subcategories, brands, and products to the view
            return view('shop.index', compact('organization', 'products', 'categories', 'subcategories', 'brands', 'shop'));
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error finding organization.', ['subdomain' => $shop, 'error' => $e->getMessage()]);

            // Use Laravel's default error handling
            abort(500, 'Internal Server Error');
        }
    }

    // for your information, it deals here with each product category separtely. For floors it works already fine. Now the blinds.
    public function showProduct($shop, $id)
{
    Log::info('Entering showProduct method.', ['product_id' => $id]);

    try {
        // Retrieve the organization based on subdomain
        $organization = organizations::where('subdomain', $shop)->firstOrFail();
        Log::info('Organization found.', ['organization_id' => $organization->id]);

        // Retrieve the product with the base relationships
        $productQuery = Products::with([
            'models',
            'colors.images',
            'colors.priceTable.prices',
            'category',
            'subCategory',
            'features.options',
            'features.heading'
            
        ]);

        // Load the product data
        $product = $productQuery->findOrFail($id);
        Log::info('Product found.', ['product_id' => $product->id]);

        // Normalize category name to avoid case sensitivity issues
        $normalizedCategoryName = strtolower($product->category->cat_name);

        // Conditionally load linked features for "Binnen zonwering" category
        if ($normalizedCategoryName === 'binnen zonwering') {
            $product->load(['models.linkedFeatures.heading']);
            Log::info('Linked features loaded for "Binnen zonwering".', ['product_id' => $product->id]);
        }

        // Fetch categories, subcategories, and brands related to the product's category
        $categories = Category::all();
        $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
        $brands = Brand::whereHas('products', function ($query) use ($product) {
            $query->where('category_id', $product->category_id);
        })->get();

        // Determine subcategory and measure type
        $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
        $measure = strtolower(optional($product->models->first())->measure ?? '');

        Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
        Log::info('Fetched Measure:', ['measure' => $measure]);

        // Prepare data to pass to the view
        $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');

        // Select appropriate view based on product attributes
        if ($measure === 'm1') {
            Log::info('Loading view: shop.product_m1', $data);
            return view('shop.product_m1', $data);
        } elseif ($measure === 'm2') {
            Log::info('Loading view: shop.product_m2', $data);
            return view('shop.product_m2', $data);
        } elseif ($subcategory === 'vloerkleden' && in_array($measure, ['per piece', 'custom sized'])) {
            Log::info('Loading view: shop.vloerkleden', $data);
            return view('shop.vloerkleden', $data);
        } elseif ($normalizedCategoryName === 'binnen zonwering') {
            Log::info('Loading view: shop.product_binnen_zonwering', $data);
            return view('shop.product_binnen_zonwering', $data);
        } else {
            Log::info('Loading view: shop.product_default', $data);
            return view('shop.product_default', $data);
        }
    } catch (\Exception $e) {
        // Log the exception details for debugging
        Log::error('Error finding product.', [
            'product_id' => $id,
            'shop' => $shop,
            'organization' => $organization ?? null,
            'product' => $product ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return a 500 error response
        abort(500, 'Internal Server Error');
    }
}


    /*
    
    public function showProduct($shop, $id)
{
    Log::info('Entering showProduct method.', ['product_id' => $id]);

    try {
        // Retrieve the organization based on subdomain
        $organization = organizations::where('subdomain', $shop)->firstOrFail();
        Log::info('Organization found.', ['organization_id' => $organization->id]);

        // Retrieve the product with the base relationships
        $productQuery = Products::with([
            'models',
            'colors.images',
            'colors.priceTable.prices',
            'category',
            'subCategory',
            'features.options',
            'features.heading'
        ]);

        // Load the product data
        $product = $productQuery->findOrFail($id);
        Log::info('Product found.', ['product_id' => $product->id]);

        // Conditionally load linked features for "Binnen zonwering" category
        if ($product->category->cat_name === 'Binnen zonwering') {
            $product->load(['models.linkedFeatures.heading']);
        }

        // Fetch categories, subcategories, and brands related to the product's category
        $categories = Category::all();
        $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
        $brands = Brand::whereHas('products', function ($query) use ($product) {
            $query->where('category_id', $product->category_id);
        })->get();

        // Determine subcategory and measure type
        $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
        $measure = strtolower(optional($product->models->first())->measure ?? '');

        Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
        Log::info('Fetched Measure:', ['measure' => $measure]);

        // Prepare data to pass to the view
        $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');

        // Select appropriate view based on product attributes
        if ($measure === 'm1') {
            Log::info('Loading view: shop.product_m1', $data);
            return view('shop.product_m1', $data);
        } elseif ($measure === 'm2') {
            Log::info('Loading view: shop.product_m2', $data);
            return view('shop.product_m2', $data);
        } elseif ($subcategory === 'vloerkleden' && in_array($measure, ['per piece', 'custom sized'])) {
            Log::info('Loading view: shop.vloerkleden', $data);
            return view('shop.vloerkleden', $data);
        } elseif ($product->category->cat_name === 'Binnen zonwering') {
            Log::info('Loading view: shop.product_binnen_zonwering', $data);
            return view('shop.product_binnen_zonwering', $data);
        } else {
            Log::info('Loading view: shop.product_default', $data);
            return view('shop.product_default', $data);
        }
    } catch (\Exception $e) {
        // Log the exception details for debugging
        Log::error('Error finding product.', [
            'product_id' => $id,
            'shop' => $shop,
            'organization' => $organization ?? null,
            'product' => $product ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return a 500 error response
        abort(500, 'Internal Server Error');
    }
}

*/

/*

    
    public function showProduct($shop, $id)
    {
        Log::info('Entering showProduct method.', ['product_id' => $id]);
    
        try {
            // Retrieve the organization
            $organization = organizations::where('subdomain', $shop)->firstOrFail();
            Log::info('Organization found.', ['organization_id' => $organization->id]);
    
            // Retrieve the product along with its related models, colors, category, brand, subcategory, and features
            $product = Products::with([
                'models',
                'colors.images',
                'colors.priceTable.prices',
                'category',
                'subCategory',
                'modelfeatures',
                'features.options', // Include options for features
                'features.heading', // Include headings for features
                'models.linkedFeatures.heading', // Load the features and their headings
            ])->findOrFail($id);
            Log::info('Product found.', ['product_id' => $product->id]);

            // Fetch categories, subcategories, and brands
            $categories = Category::all();
            $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
            $brands = Brand::whereHas('products', function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })->get();
    
            // Get the subcategory and measure
            $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
            $measure = strtolower(optional($product->models->first())->measure ?? '');
    
            Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
            Log::info('Fetched Measure:', ['measure' => $measure]);



            $models = $product->models;
            $features = $product->features ?? collect(); // Ensure $features is a collection
    
            // Pass the product data, subcategory, measure, categories, subcategories, brands, and shop to the appropriate view
            $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');
    
            if ($measure === 'm1') {
                Log::info('Loading view: shop.product_m1', $data);
                return view('shop.product_m1', $data);
            } elseif ($measure === 'm2') {
                Log::info('Loading view: shop.product_m2', $data);
                return view('shop.product_m2', $data);
            } elseif ($subcategory === 'vloerkleden' && ($measure === 'per piece' || $measure === 'custom sized')) {
                Log::info('Loading view: shop.vloerkleden', $data);
                return view('shop.vloerkleden', $data);
            } elseif ($product->category->cat_name === 'Binnen zonwering') {
                Log::info('Loading view: shop.product_binnen_zonwering', $data);
                return view('shop.product_binnen_zonwering', $data);
            } else {
                Log::info('Loading view: shop.product_default', $data);
                return view('shop.product_default', $data);
            }
        } catch (\Exception $e) {
            // Log the exception with more details
            Log::error('Error finding product.', [
                'product_id' => $id,
                'shop' => $shop,
                'organization' => $organization ?? null,
                'product' => $product ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            // Use Laravel's default error handling
            abort(500, 'Internal Server Error');
        }
    }
    
*/

    /*
    public function showProduct($shop, $id)
    {
        Log::info('Entering showProduct method.', ['product_id' => $id]);
    
        try {
            // Retrieve the organization
            $organization = organizations::where('subdomain', $shop)->firstOrFail();
            Log::info('Organization found.', ['organization_id' => $organization->id]);
    
            // Retrieve the product along with its related models, colors, category, brand, subcategory, and features
            $product = Products::with([
                'models',
                'colors.images',
                'colors.priceTable.prices',
                'category',
                'subCategory',
                'features.options', // Include options for features
                'features.heading' // Include headings for features
            ])->findOrFail($id);
            Log::info('Product found.', ['product_id' => $product->id]);
    
            // Fetch categories, subcategories, and brands
            $categories = Category::all();
            $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
            $brands = Brand::whereHas('products', function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })->get();
    
            // Get the subcategory and measure
            $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
            $measure = strtolower(optional($product->models->first())->measure ?? '');
    
            Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
            Log::info('Fetched Measure:', ['measure' => $measure]);
    
            // Pass the product data, subcategory, measure, categories, subcategories, brands, and shop to the appropriate view
            $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');
    
            if ($measure === 'm1') {
                Log::info('Loading view: shop.product_m1', $data);
                return view('shop.product_m1', $data);
            } elseif ($measure === 'm2') {
                Log::info('Loading view: shop.product_m2', $data);
                return view('shop.product_m2', $data);
            } elseif ($subcategory === 'vloerkleden' && ($measure === 'per piece' || $measure === 'custom sized')) {
                Log::info('Loading view: shop.vloerkleden', $data);
                return view('shop.vloerkleden', $data);
            } elseif ($product->category->cat_name === 'Binnen zonwering') {
                Log::info('Loading view: shop.product_binnen_zonwering', $data);
                return view('shop.product_binnen_zonwering', $data);
            } else {
                Log::info('Loading view: shop.product_default', $data);
                return view('shop.product_default', $data);
            }
        } catch (\Exception $e) {
            // Log the exception with more details
            Log::error('Error finding product.', [
                'product_id' => $id,
                'shop' => $shop,
                'organization' => $organization ?? null,
                'product' => $product ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            // Use Laravel's default error handling
            abort(500, 'Internal Server Error');
        }
    }
*/
    
/*
    public function showProduct($shop, $id)
{
    Log::info('Entering showProduct method.', ['product_id' => $id]);

    try {
        // Retrieve the organization
        $organization = organizations::where('subdomain', $shop)->firstOrFail();
        Log::info('Organization found.', ['organization_id' => $organization->id]);

        // Retrieve the product along with its related models, colors, category, brand, and subcategory
        $product = Products::with([
            'models',
            'colors.images',
            'colors.priceTable.prices',
            'category',
            'subCategory',
            'features'
        ])->findOrFail($id);
        Log::info('Product found.', ['product_id' => $product->id]);

        // Fetch categories, subcategories, and brands
        $categories = Category::all();
        $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
        $brands = Brand::whereHas('products', function ($query) use ($product) {
            $query->where('category_id', $product->category_id);
        })->get();

        // Get the subcategory and measure
        $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
        $measure = strtolower(optional($product->models->first())->measure ?? '');

        Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
        Log::info('Fetched Measure:', ['measure' => $measure]);

        // Pass the product data, subcategory, measure, categories, subcategories, brands, and shop to the appropriate view
        $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');

        if ($measure === 'm1') {
            Log::info('Loading view: shop.product_m1', $data);
            return view('shop.product_m1', $data);
        } elseif ($measure === 'm2') {
            Log::info('Loading view: shop.product_m2', $data);
            return view('shop.product_m2', $data);
        } elseif ($subcategory === 'vloerkleden' && ($measure === 'per piece' || $measure === 'custom sized')) {
            Log::info('Loading view: shop.vloerkleden', $data);
            return view('shop.vloerkleden', $data);
        } elseif ($product->category->cat_name === 'Binnen zonwering') {
            Log::info('Loading view: shop.product_binnen_zonwering', $data);
            return view('shop.product_binnen_zonwering', $data);
        } else {
            Log::info('Loading view: shop.product_default', $data);
            return view('shop.product_default', $data);
        }
    } catch (\Exception $e) {
        // Log the exception with more details
        Log::error('Error finding product.', [
            'product_id' => $id,
            'shop' => $shop,
            'organization' => $organization ?? null,
            'product' => $product ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Use Laravel's default error handling
        abort(500, 'Internal Server Error');
    }
}


    */

/*
    public function showProduct($shop, $id)
    {
        Log::info('Entering showProduct method.', ['product_id' => $id]);
    
        try {
            // Retrieve the organization
            $organization = organizations::where('subdomain', $shop)->firstOrFail();
            Log::info('Organization found.', ['organization_id' => $organization->id]);
    
            // Retrieve the product along with its related models, colors, category, brand, and subcategory
            $product = Products::with(['models', 'colors.images', 'category', 'subCategory'])->findOrFail($id);
            Log::info('Product found.', ['product_id' => $product->id]);
    
            // Fetch categories, subcategories, and brands
            $categories = Category::all();
            $subcategories = sub_categories::where('parent_id', $product->category_id)->get();
            $brands = Brand::whereHas('products', function ($query) use ($product) {
                $query->where('category_id', $product->category_id);
            })->get();
    
            // Get the subcategory and measure
            $subcategory = strtolower(optional($product->subCategory)->cat_name ?? '');
            $measure = strtolower(optional($product->models->first())->measure ?? '');
    
            Log::info('Fetched Subcategory:', ['subcategory' => $subcategory]);
            Log::info('Fetched Measure:', ['measure' => $measure]);
    
            // Pass the product data, subcategory, measure, categories, subcategories, brands, and shop to the appropriate view
            $data = compact('product', 'subcategory', 'measure', 'categories', 'subcategories', 'brands', 'shop', 'organization');
            
            if ($measure === 'm1') {
                return view('shop.product_m1', $data);
            } elseif ($measure === 'm2') {
                return view('shop.product_m2', $data);
            } elseif ($subcategory === 'vloerkleden' && ($measure === 'per piece' || $measure === 'custom sized')) {
                return view('shop.vloerkleden', $data);
            } else {
                return view('shop.product_default', $data);
            }
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error finding product.', ['product_id' => $id, 'error' => $e->getMessage()]);
    
            // Use Laravel's default error handling
            abort(500, 'Internal Server Error');
        }
    }

    */
    public function create()
    {
        Log::info('Entering create method.');
    
        // Ensure the user is authenticated using the 'user' guard
        $user = Auth::guard('user')->user();
        if (!$user) {
            Log::warning('User not authenticated.');
            return redirect()->route('user-login')->with('error', 'You need to be logged in to create a shop.');
        }
    
        $user_id = $user->id;
        Log::info('Authenticated user found.', ['user_id' => $user->id]);
    
        // Retrieve the organization for the authenticated user
        if (!$user->organization) {
            Log::error('User does not belong to any organization.', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }
    
        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');
    
        Log::info('Organization retrieved.', ['organization_id' => $organization->id]);
    
        // Example of a custom permission check
        if (!$this->userHasPermission($user, 'show-shop')) {
            Log::warning('User does not have the required permission.', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }
    
        return view('user.create_shop', compact('organization'));
    }

    public function store(Request $request)
    {
        Log::info('Entering store method.');

        // Ensure the user is authenticated using the 'user' guard
        $user = Auth::guard('user')->user();
        if (!$user) {
            Log::warning('User not authenticated.');
            return redirect()->route('user-login')->with('error', 'You need to be logged in to create a shop.');
        }

        $user_id = $user->id;
        Log::info('Authenticated user found.', ['user_id' => $user->id]);

        // Retrieve the organization for the authenticated user
        if (!$user->organization) {
            Log::error('User does not belong to any organization.', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        Log::info('Organization retrieved.', ['organization_id' => $organization->id]);

        // Example of a custom permission check
        if (!$this->userHasPermission($user, 'show-shop')) {
            Log::warning('User does not have the required permission.', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'shop_name' => 'required|string|max:255',
            'subdomain' => 'nullable|string|max:255|unique:organizations,subdomain',
            'shop_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed.', ['errors' => $validator->errors()]);
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        try {
            // Update the organization with the new shop details
            $organization->shop_name = $request->shop_name;
            $organization->subdomain = $request->subdomain;
            $organization->shop_description = $request->shop_description;
            $organization->save();

            Log::info('Shop registered successfully.', ['organization_id' => $organization->id]);

            return redirect()->route('shop.create')->with('success', 'Shop registered successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update organization.', ['organization_id' => $organization->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to register shop. Please try again.');
        }
    }

    public function show($id)
    {
        Log::info('Entering show method.');

        // Ensure the user is authenticated using the 'user' guard
        $user = Auth::guard('user')->user();
        if (!$user) {
            Log::warning('User not authenticated.');
            return redirect()->route('user-login')->with('error', 'You need to be logged in to view the shop.');
        }

        $user_id = $user->id;
        Log::info('Authenticated user found.', ['user_id' => $user->id]);

        // Retrieve the organization by ID
        $organization = organizations::findOrFail($id);

        Log::info('Organization retrieved.', ['organization_id' => $organization->id]);

        // Example of a custom permission check
        if (!$this->userHasPermission($user, 'show-shop')) {
            Log::warning('User does not have the required permission.', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }

        // Pass the organization data to the view
        return view('shop.show', compact('organization'));
    }

    private function userHasPermission($user, $permission)
    {
        // Check if the user has role_id 2
        if ($user->role_id != 2) {
            return false;
        }

        // Implement your own logic to check if the user has the specific permission
        $permissions = [
            2 => ['show-shop'], // Assuming role_id 2 has 'show-shop' permission
            // Add other role_id to permissions mappings as needed
        ];

        return in_array($permission, $permissions[$user->role_id] ?? []);
    }
}
