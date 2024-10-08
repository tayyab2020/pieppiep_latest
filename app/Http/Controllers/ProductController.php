<?php

namespace App\Http\Controllers;

use App\Brand;
use App\color;
use App\colors;
use App\color_images;
use App\estimated_prices;
use App\Exports\ProductsExport;
use App\features;
use App\Imports\ProductsImport;
use App\Model1;
use App\model_features;
use App\curtain_variables;
use App\predefined_models_details;
use App\supplier_categories;
use App\price_tables;
use App\product;
use App\product_features;
use App\product_ladderbands;
use App\product_models;
use App\Products;
use App\retailer_labor_costs;
use App\retailer_margins;
use App\retailers_requests;
use App\User;
use App\vats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Category;
use App\sub_categories;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreValidationRequest3;
use App\Http\Requests\UpdateValidationRequest;
use Auth;
use App\Generalsetting;
use Mollie\Laravel\Facades\Mollie;
use App\service_types;
use App\sub_services;
use App\handyman_products;
use App\carts;
use App\predefined_models;
use App\default_features_details;
use App\features_details;
use App\organizations;
use App\user_organizations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Excel;
use Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user', ['except' => ['getSubCategoriesByCategory','featuresData','pricesTables','store','productsModelsByBrands']]);
    }

    public function SelectProductCategory()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;

        $is_floor = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where(function($query) {
            $query->where('categories.cat_name','LIKE', '%Floors%')->orWhere('categories.cat_name','LIKE', '%Vloeren%');
        })->select('categories.id')->first();

        $is_blind = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where(function($query) {
            $query->where('categories.cat_name','LIKE', '%Blinds%')->orWhere('categories.cat_name','LIKE', '%Binnen zonwering%');
        })->select('categories.id')->first();

        $type = 3;
        return view('user.select_type', compact('type','is_floor','is_blind'));
    }

    public function getSizesByModel(Request $request)
    {
        $sizes = predefined_models_details::leftjoin("default_predefined_models_details","default_predefined_models_details.id","=","predefined_models_details.default_model_detail_id")
        ->where('predefined_models_details.model_id','=',$request->id)->select("predefined_models_details.*","default_predefined_models_details.model")->get();

        return $sizes;
    }

    public function getSubCategoriesByCategory(Request $request)
    {
        if($request->type == 'single')
        {
            $sub_categories = sub_categories::where('parent_id','=',$request->id)->get();
        }
        else
        {
            if($request->id)
            {
                $ids_array = explode(',', $request->id);
            }
            else
            {
                $ids_array = [];
            }

            $sub_categories = sub_categories::whereIn('parent_id',$ids_array)->with('main_category')->get();
        }

        return $sub_categories;
    }

    public function productsModelsByBrands(Request $request)
    {
        $models = Model1::where('brand_id','=',$request->id)->get();

        return $models;
    }

    public function index()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('user-products'))
        {
            if($user->role_id == 4)
            {
                $cats = Products::leftjoin('categories as t1','t1.id','=','products.category_id')->leftjoin('categories as t2','t2.id','=','products.sub_category_id')->leftjoin('brands','brands.id','=','products.brand_id')->leftjoin('models','models.id','=','products.model_id')->where('products.organization_id',$organization_id)->orderBy('products.id','desc')->select('products.*','t1.cat_name as category','t2.cat_name as sub_category','brands.cat_name as brand','models.cat_name as model')->get();
                
                $categories = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();

                $brands = Brand::where(function($query) use($related_users,$organization_id) {
                    $query->whereIn('user_id',$related_users)->orWhere(function($query1) use($organization_id) {
                        $query1->whereRaw("find_in_set($organization_id,other_suppliers_organizations)")->where('trademark',0);
                    });
                })->orderBy('id','desc')->get();

                return view('admin.product.index',compact('cats','categories','brands'));
            }
            else
            {
                return redirect()->route('suppliers');
            }
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function ProductsSupplier($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $supplier_organization_id = $id;
        // $supplier_organization = organizations::findOrFail($supplier_organization_id);
        // $supplier_related_users = $supplier_organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('user-products'))
        {
            $cats = Products::leftJoin('retailer_margins', function($join) use($related_users){
                $join->on('products.id', '=', 'retailer_margins.product_id')
                    ->whereIn('retailer_margins.retailer_id',$related_users);
            })->leftJoin('retailer_labor_costs', function($join) use($related_users){
                $join->on('products.id', '=', 'retailer_labor_costs.product_id')
                    ->whereIn('retailer_labor_costs.retailer_id',$related_users);
            })
                ->leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                ->leftjoin('retailers_requests','retailers_requests.supplier_organization','=','organizations.id')
                ->leftjoin('categories','categories.id','=','products.sub_category_id')
                ->leftjoin('brands','brands.id','=','products.brand_id')
                ->leftjoin('models','models.id','=','products.model_id')
                ->where('products.organization_id',$supplier_organization_id)
                ->where('retailers_requests.retailer_organization',$organization_id)
                ->where('retailers_requests.status',1)
                ->where('retailers_requests.active',1)
                ->orderBy('products.id','desc')
                ->select('products.*','retailer_labor_costs.labor','retailer_margins.margin as retailer_margin','organizations.company_name','categories.cat_name as category','brands.cat_name as brand','models.cat_name as model')->get();

            return view('admin.product.index',compact('cats'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function storeRetailerMargins(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }
        
        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $products = $request->product_ids;

        foreach($products as $i => $key)
        {
            $check = retailer_margins::where('product_id',$key)->whereIn('retailer_id',$related_users);

            if($check->first())
            {
                $check->update(['margin' => $request->margin[$i] ? $request->margin[$i] : 100]);
            }
            else
            {
                if(is_numeric($request->margin[$i]))
                {
                    $post = new retailer_margins;
                    $post->product_id = $key;
                    $post->retailer_id = $user_id;
                    $post->margin = $request->margin[$i] ? $request->margin[$i] : 100;
                    $post->save();
                }
            }

            $check1 = retailer_labor_costs::where('product_id',$key)->whereIn('retailer_id',$related_users);

            if($check1->first())
            {
                $check1->update(['labor' => $request->labor[$i] ? $request->labor[$i] : 0]);
            }
            else
            {
                if(is_numeric($request->labor[$i]))
                {
                    $post = new retailer_labor_costs;
                    $post->product_id = $key;
                    $post->retailer_id = $user_id;
                    $post->labor = $request->labor[$i] ? $request->labor[$i] : 0;
                    $post->save();
                }
            }
        }

        Session::flash('success', 'Task completed successfully.');
        return redirect()->route('admin-product-index');
    }

    public function resetSupplierMargins()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        retailer_margins::whereIn('retailer_id',$related_users)->delete();

        Session::flash('success', 'Task completed successfully.');
        return redirect()->route('admin-product-index');
    }

    public function create(Request $request,$admin = 0)
    {
        if(!$admin)
        {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            // $main_id = $user->main_id;
    
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }
        }
        else
        {
            $user_id = $request->supplier;
        }

        $organization_id = user_organizations::where("user_id",$user_id)->pluck("organization_id")->first();
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if(((isset($user) && $user->can('product-create')) || $admin) && $request->cat)
        {
            $categories = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where('categories.cat_name',$request->cat)->select('categories.*')->get();
            
            $brands = Brand::where(function($query) use($related_users,$organization_id) {
                $query->whereIn('user_id',$related_users)->orWhere(function($query1) use($organization_id) {
                    $query1->whereRaw("find_in_set($organization_id,other_suppliers_organizations)")->where('trademark',0);
                });
            })->orderBy('id','desc')->get();

            if(!count($categories))
            {
                Session::flash('unsuccess', __('text.Sorry, this category is not available in your selected categories list.'));
                return redirect()->back();
            }

            $category_id = $categories[0]->id;
            $predefined_models = predefined_models::whereRaw("find_in_set('$category_id',category_ids)")->whereIn('user_id',$related_users)->get();
            $tables = price_tables::where('connected',1)->whereIn('user_id',$related_users)->get();
            $features_headings = features::whereIn('user_id',$related_users)->get();

            if($request->cat == 'Blinds' || $request->cat == 'Binnen zonwering')
            {
                return view('admin.product.create',compact('user_id','admin','categories','brands','tables','features_headings','predefined_models'));
            }
            else
            {
                return view('admin.product.create_for_floors',compact('user_id','admin','categories','brands','tables','features_headings','predefined_models'));
            }

        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function pricesTables(Request $request)
    {
        $tables = price_tables::where('id',$request->id)->get();

        return $tables;
    }

    public function import()
    {
        $user = Auth::guard('user')->user();

        if($user->can('product-import'))
        {
            return view('admin.product.import');
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function PostImport(Request $request)
    {
        ini_set('memory_limit', '-1');
        $extension = strtolower($request->excel_file->getClientOriginalExtension());

        if(!in_array($extension, ['xls', 'xlsx']))
        {
            return redirect()->back()->withErrors("File should be of format xlsx or xls")->withInput();
        }

        $import = new ProductsImport;
        Excel::import($import,request()->file('excel_file'));


//        if(count($import->data) > 0)
//        {
//            $product = Products::where('excel',1)->whereNotIn('id', $import->data)->get();
//
//            foreach ($product as $key)
//            {
//                if($key->photo != null){
//                    \File::delete(public_path() .'/assets/images/'.$key->photo);
//                }
////                handyman_products::where('product_id',$key->id)->delete();
//                $key->delete();
//            }
//        }

        Session::flash('success', 'Task completed successfully.');
        return redirect()->route('admin-product-index');
    }

    public function PostExport(Request $request)
    {
        $user = Auth::guard('user')->user();

        if($user->can('product-export'))
        {
            ini_set('memory_limit', '-1');
            return Excel::download(new ProductsExport(),'products.xlsx');
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function copy($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('product-copy'))
        {
            $product = Products::where('id','=',$id)->where('organization_id',$organization_id)->first();

            if(!$product)
            {
                return redirect()->back();
            }

            $product_title = $product->title;
            $count = 1;

            while (Products::where('title',$product_title)->exists()) {

                $temp = substr($product_title, 0, strrpos($product_title, ' copy'));

                if(!$temp)
                {
                    $temp = $product_title;
                }

                $product_title = "{$temp} copy " . $count++;
            }

            $product->title = $product_title;

            $product_slug = $product->slug;
            $count = 1;

            while (Products::where('slug',$product_slug)->exists()) {

                $temp = substr($product_slug, 0, strrpos($product_slug, '-copy'));

                if(!$temp)
                {
                    $temp = $product_slug;
                }

                $product_slug = "{$temp}-copy-" . $count++;
            }

            $product->slug = $product_slug;
            $product->photo = NULL;

            $newPost = $product->replicate();
            $newPost->save();
            $product_id = $newPost->id;

            $colors_data = colors::where('product_id','=',$id)->get();

            foreach ($colors_data as $color)
            {
                $color->product_id = $product_id;
                $newPost = $color->replicate();
                $newPost->save();
            }

            $features_data = product_features::where('product_id','=',$id)->get();
            $fe_array = array();
            $ma_array = array();

            foreach ($features_data as $s => $feature)
            {
                if($feature->main_id)
                {
                    $org = product_features::where('id',$feature->main_id)->first();
                    $check = product_features::where('product_id',$product_id)->where('heading_id',$org->heading_id)->where('main_id',NULL)->where('sub_feature',$org->sub_feature)->where('title',$org->title)->where('value',$org->value)->where('max_size',$org->max_size)->where('price_impact',$org->price_impact)->where('impact_type',$org->impact_type)->where('variable',$org->variable)->first();
                    $feature->main_id = $check->id;
                }
                else
                {
                    $ma_array[] = $feature->id;
                }

                $feature->product_id = $product_id;
                $newPost = $feature->replicate();
                $newPost->save();

                if(!$feature->main_id)
                {
                    $fe_array[] = $newPost->id;
                }

            }

            $ma_array = array_unique($ma_array);
            $fe_array = array_unique($fe_array);

            $models_data = product_models::where('product_id','=',$id)->get();
            $mod_array = array();
            $newmod_array = array();

            foreach ($models_data as $model)
            {
                $mod_array[] = $model->id;
                $model->product_id = $product_id;
                $newPost = $model->replicate();
                $newPost->save();
                $newmod_array[] = $newPost->id;
            }

            $mod_array = array_unique($mod_array);
            $newmod_array = array_unique($newmod_array);

            $model_features_data = model_features::whereIn('model_id',$mod_array)->get();

            foreach ($model_features_data as $model_feature)
            {
                $index = array_search($model_feature->product_feature_id, $ma_array);
                $index1 = array_search($model_feature->model_id, $mod_array);
                $model_feature->model_id = $newmod_array[$index1];
                $model_feature->product_feature_id = $fe_array[$index];
                $newPost = $model_feature->replicate();
                $newPost->save();
            }

            $labor_data = retailer_labor_costs::where('product_id','=',$id)->get();

            foreach ($labor_data as $labor)
            {
                $labor->product_id = $product_id;
                $newPost = $labor->replicate();
                $newPost->save();
            }

            $margin_data = retailer_margins::where('product_id','=',$id)->get();

            foreach ($margin_data as $margin)
            {
                $margin->product_id = $product_id;
                $newPost = $margin->replicate();
                $newPost->save();
            }

            $ladderband_data = product_ladderbands::where('product_id','=',$id)->get();

            foreach ($ladderband_data as $ladderband)
            {
                $ladderband->product_id = $product_id;
                $newPost = $ladderband->replicate();
                $newPost->save();
            }

            $estimated_prices_data = estimated_prices::where('product_id','=',$id)->get();

            foreach ($estimated_prices_data as $estimated_price)
            {
                $estimated_price->product_id = $product_id;
                $newPost = $estimated_price->replicate();
                $newPost->save();
            }

            Session::flash('success', 'Product copied successfully!');
            return redirect()->back();
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    // Compress image
    public function compressImage($source, $destination) {

        $info = getimagesize($source);

        if (isset($info['mime']) && $info['mime'] == 'image/jpeg')
        {
            $source = imagecreatefromjpeg($source);
        }

        $image = Image::make($source);

        // Compress the image
        $image->save($destination, 20);

        $info = filesize($destination);
        $fileSizeMB = $info / (1024 * 1024);
        
        if($fileSizeMB > 2)
        {
            $image->resize(1920, 1080, function($constraint){
                $constraint->aspectRatio();
            })->save($destination);
        }

        return;

    }

    public function store(StoreValidationRequest3 $request,$admin = 0)
    {
        ini_set('max_execution_time', 180);
        $input = $request->all();
        $tmpFilePath = 'assets/colorImages/';

        // $prices = preg_replace("/,([\s])+/",",",$request->estimated_price);
        $colors = $request->colors;
        $features = $request->feature_headings ? $request->feature_headings : [];
        $models = $request->models;
        $sub_products = $request->sub_codes;
        $feature_row = array();
        $feature_id = array();

        // if($prices)
        // {
        //     $pricesArray = explode(',', $prices);
        // }
        // else
        // {
        //     $pricesArray = [];
        // }

        if($request->form_type == 1)
        {
            $input['ladderband'] = 0;
            $input['ladderband_value'] = 0;
        }
        else
        {
            if($input['ladderband'])
            {
                if(!$input['ladderband_value'])
                {
                    $input['ladderband_value'] = 0;
                }
            }
        }

        $input['margin'] = is_numeric($input['margin']) ? $input['margin'] : NULL;

        if($request->cat_id)
        {
            if($request->removed1)
            {
                $removed1 = explode(',', $request->removed1);
            }
            else
            {
                $removed1 = [];
            }

            if($request->removed)
            {
                $removed = explode(',', $request->removed);
            }
            else
            {
                $removed = [];
            }

            if($request->removed_ladderband)
            {
                $removed_ladderband = explode(',', $request->removed_ladderband);
            }
            else
            {
                $removed_ladderband = [];
            }

            if($request->removed_colors)
            {
                $removed_colors = explode(',', $request->removed_colors);
            }
            else
            {
                $removed_colors = [];
            }

            product_features::whereIn('id',$removed)->delete();
            product_ladderbands::whereIn('id',$removed_ladderband)->delete();
            colors::whereIn('id',$removed_colors)->delete();

            if($request->form_type == 1)
            {
                $color_images = color_images::whereIn('color_id',$removed_colors)->get();

                foreach($color_images as $key)
                {
                    \File::delete(public_path($tmpFilePath.$key->image));
                }

                color_images::whereIn('color_id',$removed_colors)->delete();
            }
            
            product_models::whereIn('id',$removed1)->delete();
            model_features::whereIn('model_id',$removed1)->delete();
            $model_ids = product_models::where('product_id',$request->cat_id)->pluck('id');
            model_features::whereIn('model_id',$model_ids)->whereIn('product_feature_id',$removed)->delete();
            curtain_variables::whereIn('model_id',$model_ids)->delete();

            $cat = Products::where('id',$request->cat_id)->first();

            if($file = $request->file('photo'))
            {
                \File::delete(public_path() .'/assets/images/'.$cat->photo);
                $name = time().$file->getClientOriginalName();
                $file->move('assets/images',$name);
                $input['photo'] = $name;
            }

            $cat->fill($input)->save();

            $fea = product_features::where('product_id',$request->cat_id)->where('sub_feature',0)->get();

            if(count($fea) == 0)
            {
                foreach ($features as $f => $key)
                {
                    if($key != NULL && $request->features[$f] != NULL)
                    {
                        $fea = new product_features;
                        $fea->feature_value_id = $request->features[$f];
                        $fea->value = $request->feature_values[$f] ? $request->feature_values[$f] : 0;
                        $fea->product_id = $request->cat_id;
                        $fea->heading_id = $key;
                        $fea->max_size = NULL; /*$request->max_size[$f] ? str_replace(",",".",$request->max_size[$f]) : NULL;*/
                        $fea->price_impact = ($request->price_impact[$f] == 0 || $request->price_impact[$f] == 1) ? $request->price_impact[$f] : 0;
                        $fea->impact_type = $request->impact_type[$f];
                        $fea->variable = $request->price_impact[$f] == 2 ? 1 : 0;
                        $fea->m2_impact = $request->price_impact[$f] == 3 ? 1 : 0;
                        $fea->factor = $request->price_impact[$f] == 4 ? 1 : 0;
                        $fea->factor_value = $request->form_type == 1 ? NULL : ($request->factor_values[$f] ? $request->factor_values[$f] : 0);
                        $fea->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                        $fea->save();

                        $s_titles = 'features'.$request->f_rows[$f];
                        $sub_features = $request->$s_titles;

                        if($sub_features)
                        {
                            foreach($sub_features as $s => $sub)
                            {
                                $s_value = 'feature_values'.$request->f_rows[$f];
                                $s_price_impact = 'price_impact'.$request->f_rows[$f];
                                $s_impact_type = 'impact_type'.$request->f_rows[$f];
                                $fc_value = 'factor_values'.$request->f_rows[$f];

                                if($sub != NULL)
                                {
                                    $sub_feature = new product_features;
                                    $sub_feature->product_id = $request->cat_id;
                                    $sub_feature->heading_id = $key;
                                    $sub_feature->main_id = $fea->id;
                                    $sub_feature->sub_feature = 1;
                                    $sub_feature->feature_value_id = $sub;
                                    $sub_feature->value = $request->$s_value[$s] ? $request->$s_value[$s] : 0;
                                    $sub_feature->price_impact = ($request->$s_price_impact[$s] == 0 || $request->$s_price_impact[$s] == 1) ? $request->$s_price_impact[$s] : 0;
                                    $sub_feature->impact_type = $request->$s_impact_type[$s];
                                    $sub_feature->variable = $request->$s_price_impact[$s] == 2 ? 1 : 0;
                                    $sub_feature->m2_impact = $request->$s_price_impact[$s] == 3 ? 1 : 0;
                                    $sub_feature->factor = $request->$s_price_impact[$s] == 4 ? 1 : 0;
                                    $sub_feature->factor_value = $request->form_type == 1 ? NULL : ($request->$fc_value[$s] ? $request->$fc_value[$s] : 0);
                                    $sub_feature->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                    $sub_feature->save();
                                }
                            }
                        }

                        $feature_row[] = $request->f_rows[$f];
                        $feature_id[] = $fea->id;
                    }
                }
            }
            else
            {
                if(count($features) > 0)
                {
                    foreach ($features as $f => $key)
                    {
                        $fea_check = product_features::where('product_id',$request->cat_id)->where('sub_feature',0)->skip($f)->first();

                        if($fea_check)
                        {
                            if($key != NULL && $request->features[$f] != NULL)
                            {
                                $fea_check->feature_value_id = $request->features[$f];
                                $fea_check->heading_id = $key;
                                $fea_check->value = $request->feature_values[$f] ? $request->feature_values[$f] : 0;
                                $fea_check->max_size = NULL; /*$request->max_size[$f] ? str_replace(",",".",$request->max_size[$f]) : NULL;*/
                                $fea_check->price_impact = ($request->price_impact[$f] == 0 || $request->price_impact[$f] == 1) ? $request->price_impact[$f] : 0;
                                $fea_check->impact_type = $request->impact_type[$f];
                                $fea_check->variable = $request->price_impact[$f] == 2 ? 1 : 0;
                                $fea_check->m2_impact = $request->price_impact[$f] == 3 ? 1 : 0;
                                $fea_check->factor = $request->price_impact[$f] == 4 ? 1 : 0;
                                $fea_check->factor_value = $request->form_type == 1 ? NULL : ($request->factor_values[$f] ? $request->factor_values[$f] : 0);
                                $fea_check->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                $fea_check->save();

                                $s_titles = 'features'.$request->f_rows[$f];
                                $sub_features = $request->$s_titles;

                                if($sub_features)
                                {
                                    foreach($sub_features as $s => $sub)
                                    {
                                        $sub_fea_check = product_features::where('main_id',$fea_check->id)->skip($s)->first();

                                        $s_value = 'feature_values'.$request->f_rows[$f];
                                        $s_price_impact = 'price_impact'.$request->f_rows[$f];
                                        $s_impact_type = 'impact_type'.$request->f_rows[$f];
                                        $fc_value = 'factor_values'.$request->f_rows[$f];

                                        if($sub_fea_check)
                                        {
                                            if($sub != NULL)
                                            {
                                                $sub_fea_check->heading_id = $key;
                                                $sub_fea_check->main_id = $fea_check->id;
                                                $sub_fea_check->sub_feature = 1;
                                                $sub_fea_check->feature_value_id = $sub;
                                                $sub_fea_check->value = $request->$s_value[$s] ? $request->$s_value[$s] : 0;
                                                $sub_fea_check->price_impact = ($request->$s_price_impact[$s] == 0 || $request->$s_price_impact[$s] == 1) ? $request->$s_price_impact[$s] : 0;
                                                $sub_fea_check->impact_type = $request->$s_impact_type[$s];
                                                $sub_fea_check->variable = $request->$s_price_impact[$s] == 2 ? 1 : 0;
                                                $sub_fea_check->m2_impact = $request->$s_price_impact[$s] == 3 ? 1 : 0;
                                                $sub_fea_check->factor = $request->$s_price_impact[$s] == 4 ? 1 : 0;
                                                $sub_fea_check->factor_value = $request->form_type == 1 ? NULL : ($request->$fc_value[$s] ? $request->$fc_value[$s] : 0);
                                                $sub_fea_check->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                                $sub_fea_check->save();
                                            }
                                        }
                                        else
                                        {
                                            if($sub != NULL)
                                            {
                                                $sub_feature = new product_features;
                                                $sub_feature->product_id = $request->cat_id;
                                                $sub_feature->heading_id = $key;
                                                $sub_feature->main_id = $fea_check->id;
                                                $sub_feature->sub_feature = 1;
                                                $sub_feature->feature_value_id = $sub;
                                                $sub_feature->value = $request->$s_value[$s] ? $request->$s_value[$s] : 0;
                                                $sub_feature->price_impact = ($request->$s_price_impact[$s] == 0 || $request->$s_price_impact[$s] == 1) ? $request->$s_price_impact[$s] : 0;
                                                $sub_feature->impact_type = $request->$s_impact_type[$s];
                                                $sub_feature->variable = $request->$s_price_impact[$s] == 2 ? 1 : 0;
                                                $sub_feature->m2_impact = $request->$s_price_impact[$s] == 3 ? 1 : 0;
                                                $sub_feature->factor = $request->$s_price_impact[$s] == 4 ? 1 : 0;
                                                $sub_feature->factor_value = $request->form_type == 1 ? NULL : ($request->$fc_value[$s] ? $request->$fc_value[$s] : 0);
                                                $sub_feature->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                                $sub_feature->save();
                                            }
                                        }
                                    }
                                }

                                $feature_row[] = $request->f_rows[$f];
                                $feature_id[] = $fea_check->id;
                            }
                        }
                        else
                        {
                            if($key != NULL && $request->features[$f] != NULL)
                            {
                                $fea = new product_features;
                                $fea->product_id = $request->cat_id;
                                $fea->feature_value_id = $request->features[$f];
                                $fea->heading_id = $key;
                                $fea->value = $request->feature_values[$f] ? $request->feature_values[$f] : 0;
                                $fea->max_size = NULL; /*$request->max_size[$f] ? str_replace(",",".",$request->max_size[$f]) : NULL;*/
                                $fea->price_impact = ($request->price_impact[$f] == 0 || $request->price_impact[$f] == 1) ? $request->price_impact[$f] : 0;
                                $fea->impact_type = $request->impact_type[$f];
                                $fea->variable = $request->price_impact[$f] == 2 ? 1 : 0;
                                $fea->m2_impact = $request->price_impact[$f] == 3 ? 1 : 0;
                                $fea->factor = $request->price_impact[$f] == 4 ? 1 : 0;
                                $fea->factor_value = $request->form_type == 1 ? NULL : ($request->factor_values[$f] ? $request->factor_values[$f] : 0);
                                $fea->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                $fea->save();

                                $s_titles = 'features'.$request->f_rows[$f];
                                $sub_features = $request->$s_titles;

                                if($sub_features)
                                {
                                    foreach($sub_features as $s => $sub)
                                    {
                                        $s_value = 'feature_values'.$request->f_rows[$f];
                                        $s_price_impact = 'price_impact'.$request->f_rows[$f];
                                        $s_impact_type = 'impact_type'.$request->f_rows[$f];
                                        $fc_value = 'factor_values'.$request->f_rows[$f];

                                        if($sub != NULL)
                                        {
                                            $sub_feature = new product_features;
                                            $sub_feature->product_id = $request->cat_id;
                                            $sub_feature->heading_id = $key;
                                            $sub_feature->main_id = $fea->id;
                                            $sub_feature->sub_feature = 1;
                                            $sub_feature->feature_value_id = $sub;
                                            $sub_feature->value = $request->$s_value[$s] ? $request->$s_value[$s] : 0;
                                            $sub_feature->price_impact = ($request->$s_price_impact[$s] == 0 || $request->$s_price_impact[$s] == 1) ? $request->$s_price_impact[$s] : 0;
                                            $sub_feature->impact_type = $request->$s_impact_type[$s];
                                            $sub_feature->variable = $request->$s_price_impact[$s] == 2 ? 1 : 0;
                                            $sub_feature->m2_impact = $request->$s_price_impact[$s] == 3 ? 1 : 0;
                                            $sub_feature->factor = $request->$s_price_impact[$s] == 4 ? 1 : 0;
                                            $sub_feature->factor_value = $request->form_type == 1 ? NULL : ($request->$fc_value[$s] ? $request->$fc_value[$s] : 0);
                                            $sub_feature->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                            $sub_feature->save();
                                        }
                                    }
                                }

                                $feature_row[] = $request->f_rows[$f];
                                $feature_id[] = $fea->id;
                            }
                        }
                    }
                }
                else
                {
                    $features_ids = product_features::where('product_id',$request->cat_id)->pluck('id');
                    product_features::where('product_id',$request->cat_id)->delete();
                    $model_ids = product_models::where('product_id',$request->cat_id)->pluck('id');
                    model_features::whereIn('model_id',$model_ids)->whereIn('product_feature_id',$features_ids)->delete();
                }
            }

            foreach ($models as $m => $temp)
            {
                $model_check = product_models::where('product_id',$request->cat_id)->skip($m)->first();

                if($model_check)
                {
                    if(($request->form_type == 1 && $temp != NULL) || ($temp != NULL && $request->model_values[$m] != NULL))
                    {
                        $model_check->model = $temp;
                        $model_check->max_size = is_numeric($request->model_max_size[$m]) || $request->model_max_size[$m] ? str_replace(",", ".", $request->model_max_size[$m]) : NULL;
                        $model_check->max_width = is_numeric($request->model_max_width[$m]) || $request->model_max_width[$m] ? str_replace(",", ".", $request->model_max_width[$m]) : NULL;
                        $model_check->max_height = is_numeric($request->model_max_height[$m]) || $request->model_max_height[$m] ? str_replace(",", ".", $request->model_max_height[$m]) : NULL;
                        
                        if($request->form_type == 1)
                        {
                            $model_check->value = 0;
                            $model_check->factor_value = NULL;
                            $model_check->price_impact = 0;
                            $model_check->impact_type = 0;
                            $model_check->factor = 0;
                            $model_check->m2_impact = 0;
                            $model_check->m1_impact = 0;
                            $model_check->measure = $request->model_measure[$m];
                            $model_check->estimated_price_per_box = str_replace(',', '.',$request->estimated_price_per_box[$m]);
                            $model_check->estimated_price_quantity = str_replace(',', '.',$request->estimated_price_quantity[$m]);
                            $model_check->estimated_price = str_replace(',', '.',$request->estimated_price[$m]);
                            $model_check->combination = $request->model_combination[$m];
                        }
                        else
                        {
                            $model_check->value = str_replace(",", ".", $request->model_values[$m]);
                            $model_check->factor_value = $request->model_factor_values[$m] ? str_replace(",", ".", $request->model_factor_values[$m]) : NULL;
                            $model_check->price_impact = ($request->model_price_impact[$m] == 0 || $request->model_price_impact[$m] == 1) ? $request->model_price_impact[$m] : 0;
                            $model_check->impact_type = $request->model_impact_type[$m];
                            $model_check->factor = $request->model_price_impact[$m] == 4 ? 1 : 0;
                            $model_check->m2_impact = $request->model_price_impact[$m] == 3 ? 1 : 0;
                            $model_check->m1_impact = $request->model_price_impact[$m] == 2 ? 1 : 0;
                            $model_check->factor_max_width = $request->model_factor_max_width[$m] ? str_replace(",", ".", $request->model_factor_max_width[$m]) : NULL;
                            $model_check->curtain_type = $request->curtain_type[$m];
                        }
                        
                        $model_check->size = $request->size_ids[$m] ? 1 : 0;
                        $model_check->size_id = $request->size_ids[$m];
                        $model_check->childsafe = $request->childsafe[$m];
                        $model_check->save();
                    }

                    if($request->form_type == 2)
                    {
                        $curtain_row = $request->row_curtain_id[$m];
                        $curtain_variable_options = "curtain_variable_options".$curtain_row;
                        $curtain_variable_descriptions = "curtain_variable_descriptions".$curtain_row;
                        $curtain_variable_values = "curtain_variable_values".$curtain_row;

                        $curtain_variables = $request->$curtain_variable_options;

                        if($curtain_variables)
                        {
                            foreach($curtain_variables as $cv => $curt)
                            {
                                if($request->$curtain_variable_descriptions[$cv] || $request->$curtain_variable_values[$cv])
                                {
                                    $curtain_variable = new curtain_variables;
                                    $curtain_variable->model_id = $model_check->id;
                                    $curtain_variable->enabled = $curt;
                                    $curtain_variable->description = $request->$curtain_variable_descriptions[$cv];
                                    $curtain_variable->value = $request->$curtain_variable_values[$cv] ? str_replace(",", ".", $request->$curtain_variable_values[$cv]) : 0;
                                    $curtain_variable->save();
                                }
                            }
                        }
                    }

                    foreach ($feature_row as $a => $abc)
                    {
                        $model_features_check = model_features::where('model_id',$model_check->id)->skip($a)->first();
                        $selected_feature = 'selected_model_feature' . $abc;
                        $link = $request->$selected_feature[$m];

                        if($model_features_check)
                        {
                            $model_features_check->model_id = $model_check->id;
                            $model_features_check->product_feature_id = $feature_id[$a];
                            $model_features_check->linked = $link;
                            $model_features_check->save();
                        }
                        else
                        {
                            $model_feature = new model_features;
                            $model_feature->model_id = $model_check->id;
                            $model_feature->product_feature_id = $feature_id[$a];
                            $model_feature->linked = $link;
                            $model_feature->save();
                        }
                    }
                }
                else
                {
                    if(($request->form_type == 1 && $temp != NULL) || ($temp != NULL && $request->model_values[$m] != NULL)) {

                        $model = new product_models;
                        $model->product_id = $request->cat_id;
                        $model->model = $temp;
                        $model->max_size = is_numeric($request->model_max_size[$m]) || $request->model_max_size[$m] ? str_replace(",", ".", $request->model_max_size[$m]) : NULL;
                        $model->max_width = is_numeric($request->model_max_width[$m]) || $request->model_max_width[$m] ? str_replace(",", ".", $request->model_max_width[$m]) : NULL;
                        $model->max_height = is_numeric($request->model_max_height[$m]) || $request->model_max_height[$m] ? str_replace(",", ".", $request->model_max_height[$m]) : NULL;
                        
                        if($request->form_type == 1)
                        {
                            $model->value = 0;
                            $model->factor_value = NULL;
                            $model->price_impact = 0;
                            $model->impact_type = 0;
                            $model->factor = 0;
                            $model->m2_impact = 0;
                            $model->m1_impact = 0;
                            $model->measure = $request->model_measure[$m];
                            $model->estimated_price_per_box = str_replace(',', '.',$request->estimated_price_per_box[$m]);
                            $model->estimated_price_quantity = str_replace(',', '.',$request->estimated_price_quantity[$m]);
                            $model->estimated_price = str_replace(',', '.',$request->estimated_price[$m]);
                            $model->combination = $request->model_combination[$m];
                        }
                        else
                        {
                            $model->value = str_replace(",", ".", $request->model_values[$m]);
                            $model->factor_value = $request->model_factor_values[$m] ? str_replace(",", ".", $request->model_factor_values[$m]) : NULL;
                            $model->price_impact = ($request->model_price_impact[$m] == 0 || $request->model_price_impact[$m] == 1) ? $request->model_price_impact[$m] : 0;
                            $model->impact_type = $request->model_impact_type[$m];
                            $model->factor = $request->model_price_impact[$m] == 4 ? 1 : 0;
                            $model->m2_impact = $request->model_price_impact[$m] == 3 ? 1 : 0;
                            $model->m1_impact = $request->model_price_impact[$m] == 2 ? 1 : 0;
                            $model->factor_max_width = $request->model_factor_max_width[$m] ? str_replace(",", ".", $request->model_factor_max_width[$m]) : NULL;
                            $model->curtain_type = $request->curtain_type[$m];
                        }
                        
                        $model->size = $request->size_ids[$m] ? 1 : 0;
                        $model->size_id = $request->size_ids[$m];
                        $model->childsafe = $request->childsafe[$m];
                        $model->save();

                        if($request->form_type == 2)
                        {
                            $curtain_row = $request->row_curtain_id[$m];
                            $curtain_variable_options = "curtain_variable_options".$curtain_row;
                            $curtain_variable_descriptions = "curtain_variable_descriptions".$curtain_row;
                            $curtain_variable_values = "curtain_variable_values".$curtain_row;

                            $curtain_variables = $request->$curtain_variable_options;

                            if($curtain_variables)
                            {
                                foreach($curtain_variables as $cv => $curt)
                                {
                                    if($request->$curtain_variable_descriptions[$cv] || $request->$curtain_variable_values[$cv])
                                    {
                                        $curtain_variable = new curtain_variables;
                                        $curtain_variable->model_id = $model->id;
                                        $curtain_variable->enabled = $curt;
                                        $curtain_variable->description = $request->$curtain_variable_descriptions[$cv];
                                        $curtain_variable->value = $request->$curtain_variable_values[$cv] ? str_replace(",", ".", $request->$curtain_variable_values[$cv]) : 0;
                                        $curtain_variable->save();
                                    }
                                }
                            }
                        }

                        foreach ($feature_row as $a => $abc)
                        {
                            $selected_feature = 'selected_model_feature' . $abc;
                            $link = $request->$selected_feature[$m];

                            $model_feature = new model_features;
                            $model_feature->model_id = $model->id;
                            $model_feature->product_feature_id = $feature_id[$a];
                            $model_feature->linked = $link;
                            $model_feature->save();
                        }
                    }
                }
            }

            if($request->form_type == 2)
            {
                $sub_pro = product_ladderbands::where('product_id',$request->cat_id)->get();

                if(count($sub_pro) == 0)
                {
                    foreach ($sub_products as $s => $key)
                    {
                    
                        if($key != NULL && $request->sub_product_titles[$s] != NULL)
                        {
                            $sub_pro = new product_ladderbands;
                            $sub_pro->title = $request->sub_product_titles[$s];
                            $sub_pro->product_id = $request->cat_id;
                            $sub_pro->code = $key;
                            $sub_pro->size1_value = $request->size1_value[$s];
                            $sub_pro->size2_value = $request->size2_value[$s];
                            $sub_pro->save();
                        }
                    }
                }
                else
                {
                    if(count($sub_products) > 0)
                    {
                        foreach ($sub_products as $s => $key)
                        {
                            $sub_check = product_ladderbands::where('product_id',$request->cat_id)->skip($s)->first();

                            if($sub_check)
                            {
                                if($key != NULL && $request->sub_product_titles[$s] != NULL)
                                {
                                    $sub_check->title = $request->sub_product_titles[$s];
                                    $sub_check->code = $key;
                                    $sub_check->size1_value = $request->size1_value[$s];
                                    $sub_check->size2_value = $request->size2_value[$s];
                                    $sub_check->save();
                                }
                            }
                            else
                            {
                                if($key != NULL && $request->sub_product_titles[$s] != NULL)
                                {
                                    $sub_pro = new product_ladderbands;
                                    $sub_pro->title = $request->sub_product_titles[$s];
                                    $sub_pro->product_id = $request->cat_id;
                                    $sub_pro->code = $key;
                                    $sub_pro->size1_value = $request->size1_value[$s];
                                    $sub_pro->size2_value = $request->size2_value[$s];
                                    $sub_pro->save();
                                }
                            }
                        }
                    }
                    else
                    {
                        product_ladderbands::where('product_id',$request->cat_id)->delete();
                    }
                }

                $col = colors::where('product_id',$request->cat_id)->get();

                if(count($col) == 0)
                {
                    foreach ($colors as $c => $key)
                    {
                        if($key != NULL && $request->color_codes[$c] != NULL)
                        {
                            $col = new colors;
                            $col->title = $key;
                            $col->color_code = $request->color_codes[$c];
                            $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                            $col->product_id = $request->cat_id;
                            $col->table_id = $request->price_tables[$c] ? $request->price_tables[$c] : NULL;
                            $col->save();
                        }
                    }
                }
                else
                {
                    if(count($colors) > 0)
                    {
                        foreach ($colors as $c => $key)
                        {
                            $col_check = colors::where('product_id',$request->cat_id)->skip($c)->first();

                            if($col_check)
                            {
                                if($key != NULL && $request->color_codes[$c] != NULL)
                                {
                                    $col_check->title = $key;
                                    $col_check->color_code = $request->color_codes[$c];
                                    $col_check->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                                    $col_check->table_id = $request->price_tables[$c] ? $request->price_tables[$c] : NULL;
                                    $col_check->save();
                                }
                            }
                            else
                            {
                                if($key != NULL && $request->color_codes[$c] != NULL)
                                {
                                    $col = new colors;
                                    $col->title = $key;
                                    $col->color_code = $request->color_codes[$c];
                                    $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                                    $col->product_id = $request->cat_id;
                                    $col->table_id = $request->price_tables[$c] ? $request->price_tables[$c] : NULL;
                                    $col->save();
                                }
                            }
                        }
                    }
                    else
                    {
                        colors::where('product_id',$request->cat_id)->delete();
                    }
                }
            }
            else
            {
                $col = colors::where('product_id',$request->cat_id)->get();

                if(count($col) == 0)
                {
                    foreach ($colors as $c => $key)
                    {
                        if($key != NULL && $request->color_codes[$c] != NULL)
                        {
                            $col = new colors;
                            $col->title = $key;
                            $col->color_code = $request->color_codes[$c];
                            $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                            $col->product_id = $request->cat_id;
                            $col->table_id = NULL;
                            $col->save();

                            $color_images = 'color_images'.$request->color_row[$c];

                            if($file = $request->file($color_images))
                            {
                                foreach($file as $temp)
                                {
                                    $name = time().'-'.$col->id.'-'.$temp->getClientOriginalName();
                                    $compressedImagePath = $tmpFilePath.$name;
                                    $this->compressImage($temp,$compressedImagePath);

                                    $color_image = new color_images;
                                    $color_image->product_id = $request->cat_id;
                                    $color_image->color_id = $col->id;
                                    $color_image->image = $name;
                                    $color_image->save();
                                }                                
                            }
                        }
                    }
                }
                else
                {
                    if(count($colors) > 0)
                    {
                        foreach ($colors as $c => $key)
                        {
                            $col_check = colors::where('product_id',$request->cat_id)->skip($c)->first();

                            if($col_check)
                            {
                                if($key != NULL && $request->color_codes[$c] != NULL)
                                {
                                    $col_check->title = $key;
                                    $col_check->color_code = $request->color_codes[$c];
                                    $col_check->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                                    $col_check->table_id = NULL;
                                    $col_check->save();

                                    $color_images = 'color_images'.$request->color_row[$c];

                                    if($file = $request->file($color_images))
                                    {
                                        $c_images = color_images::where('color_id',$col_check->id)->get();

                                        foreach($c_images as $c_i1)
                                        {
                                            \File::delete(public_path($tmpFilePath.$c_i1->image));
                                        }
                                        
                                        color_images::where('color_id',$col_check->id)->delete();
                                        
                                        foreach($file as $temp)
                                        {
                                            $name = time().'-'.$col_check->id.'-'.$temp->getClientOriginalName();
                                            $compressedImagePath = $tmpFilePath.$name;
                                            $this->compressImage($temp,$compressedImagePath);

                                            $color_image = new color_images;
                                            $color_image->product_id = $request->cat_id;
                                            $color_image->color_id = $col_check->id;
                                            $color_image->image = $name;
                                            $color_image->save();
                                        }
                                    }
                                }
                            }
                            else
                            {
                                if($key != NULL && $request->color_codes[$c] != NULL)
                                {
                                    $col = new colors;
                                    $col->title = $key;
                                    $col->color_code = $request->color_codes[$c];
                                    $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                                    $col->product_id = $request->cat_id;
                                    $col->table_id = NULL;
                                    $col->save();

                                    $color_images = 'color_images'.$request->color_row[$c];

                                    if($file = $request->file($color_images))
                                    {                                        
                                        foreach($file as $temp)
                                        {
                                            $name = time().'-'.$col->id.'-'.$temp->getClientOriginalName();
                                            $compressedImagePath = $tmpFilePath.$name;
                                            $this->compressImage($temp,$compressedImagePath);

                                            $color_image = new color_images;
                                            $color_image->product_id = $request->cat_id;
                                            $color_image->color_id = $col->id;
                                            $color_image->image = $name;
                                            $color_image->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        colors::where('product_id',$request->cat_id)->delete();

                        $c_images = color_images::where('product_id',$request->cat_id)->get();
                                        
                        foreach($c_images as $c_i1)
                        {
                            \File::delete(public_path($tmpFilePath.$c_i1->image));
                        }

                        color_images::where('product_id',$request->cat_id)->delete();
                    }
                }

                // $est = estimated_prices::where('product_id',$request->cat_id)->get();

                // if(count($est) == 0)
                // {
                //     foreach ($pricesArray as $price)
                //     {
                //         $est = new estimated_prices;
                //         $est->product_id = $request->cat_id;
                //         $est->price = $price;
                //         $est->save();
                //     }
                // }
                // else
                // {
                //     if(count($pricesArray) > 0)
                //     {
                //         foreach ($pricesArray as $x => $price)
                //         {
                //             $est_check = estimated_prices::where('product_id',$request->cat_id)->skip($x)->first();

                //             if($est_check)
                //             {
                //                 $est_check->price = $pricesArray[$x];
                //                 $est_check->save();
                //             }
                //             else
                //             {
                //                 $temp = new estimated_prices;
                //                 $temp->product_id = $request->cat_id;
                //                 $temp->price = $pricesArray[$x];
                //                 $temp->save();
                //             }
                //         }
                //     }
                //     else
                //     {
                //         estimated_prices::where('product_id',$request->cat_id)->delete();
                //     }
                // }
            }

            Session::flash('success', __('text.Product edited successfully.'));

            if($admin)
            {
                return redirect()->route('all-product-edit',$request->cat_id);
            }
            else
            {
                return redirect()->route('admin-product-edit',$request->cat_id);
            }
        }
        else
        {
            if($admin)
            {
                $user_id = $request->supplier_id;
                $user = User::where("id",$user_id)->first();
            }
            else
            {
                $user = Auth::guard('user')->user();
                $user_id = $user->id;
                // $main_id = $user->main_id;
    
                // if($main_id)
                // {
                //     $user_id = $main_id;
                // }
            }

            $organization_id = $user->organization->id;

            $input['user_id'] = $user_id;
            $input['organization_id'] = $organization_id;

            $cat = new Products();

            if($file = $request->file('photo'))
            {
                \File::delete(public_path() .'/assets/images/'.$cat->photo);
                $name = time().$file->getClientOriginalName();
                $file->move('assets/images',$name);
                $input['photo'] = $name;
            }

            $cat->fill($input)->save();

            foreach ($features as $f => $key)
            {
                if($key != NULL && $request->features[$f] != NULL)
                {
                    $feature = new product_features;
                    $feature->product_id = $cat->id;
                    $feature->feature_value_id = $request->features[$f];
                    $feature->heading_id = $key;
                    $feature->value = $request->feature_values[$f] ? $request->feature_values[$f] : 0;
                    $feature->max_size = NULL; /*$request->max_size[$f] ? str_replace(",",".",$request->max_size[$f]) : NULL;*/
                    $feature->price_impact = ($request->price_impact[$f] == 0 || $request->price_impact[$f] == 1) ? $request->price_impact[$f] : 0;
                    $feature->impact_type = $request->impact_type[$f];
                    $feature->variable = $request->price_impact[$f] == 2 ? 1 : 0;
                    $feature->m2_impact = $request->price_impact[$f] == 3 ? 1 : 0;
                    $feature->factor = $request->price_impact[$f] == 4 ? 1 : 0;
                    $feature->factor_value = $request->form_type == 1 ? NULL : ($request->factor_values[$f] ? $request->factor_values[$f] : 0);
                    $feature->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                    $feature->save();

                    $s_titles = 'features'.$request->f_rows[$f];
                    $sub_features = $request->$s_titles;

                    if($sub_features)
                    {
                        foreach($sub_features as $s => $sub)
                        {
                            $s_value = 'feature_values'.$request->f_rows[$f];
                            $s_price_impact = 'price_impact'.$request->f_rows[$f];
                            $s_impact_type = 'impact_type'.$request->f_rows[$f];
                            $fc_value = 'factor_values'.$request->f_rows[$f];

                            if($sub != NULL)
                            {
                                $sub_feature = new product_features;
                                $sub_feature->product_id = $cat->id;
                                $sub_feature->heading_id = $key;
                                $sub_feature->main_id = $feature->id;
                                $sub_feature->sub_feature = 1;
                                $sub_feature->feature_value_id = $sub;
                                $sub_feature->value = $request->$s_value[$s] ? $request->$s_value[$s] : 0;
                                $sub_feature->price_impact = ($request->$s_price_impact[$s] == 0 || $request->$s_price_impact[$s] == 1) ? $request->$s_price_impact[$s] : 0;
                                $sub_feature->impact_type = $request->$s_impact_type[$s];
                                $sub_feature->variable = $request->$s_price_impact[$s] == 2 ? 1 : 0;
                                $sub_feature->m2_impact = $request->$s_price_impact[$s] == 3 ? 1 : 0;
                                $sub_feature->factor = $request->$s_price_impact[$s] == 4 ? 1 : 0;
                                $sub_feature->factor_value = $request->form_type == 1 ? NULL : ($request->$fc_value[$s] ? $request->$fc_value[$s] : 0);
                                $sub_feature->status = $request->form_type == 1 ? $request->features_status[$f] : 1;
                                $sub_feature->save();
                            }
                        }
                    }

                    $feature_row[] = $request->f_rows[$f];
                    $feature_id[] = $feature->id;
                }
            }

            foreach ($models as $m => $temp)
            {
                $model_value = isset($request->model_values[$m]) ? ($request->model_values[$m] ? str_replace(",", ".", $request->model_values[$m]) : 0) : 0;
                $model_factor_value = isset($request->model_factor_values[$m]) ? ($request->model_factor_values[$m] ? str_replace(",", ".", $request->model_factor_values[$m]) : NULL) : NULL;

                if($temp != NULL) {

                    $model = new product_models;
                    $model->product_id = $cat->id;
                    $model->model = $temp;
                    $model->value = $model_value;
                    $model->factor_value = $model_factor_value;
                    $model->max_size = is_numeric($request->model_max_size[$m]) || $request->model_max_size[$m] ? str_replace(",", ".", $request->model_max_size[$m]) : NULL;
                    $model->max_width = is_numeric($request->model_max_width[$m]) || $request->model_max_width[$m] ? str_replace(",", ".", $request->model_max_width[$m]) : NULL;
                    $model->max_height = is_numeric($request->model_max_height[$m]) || $request->model_max_height[$m] ? str_replace(",", ".", $request->model_max_height[$m]) : NULL;
                    
                    if($request->form_type == 1)
                    {
                        $model->price_impact = 0;
                        $model->impact_type = 0;
                        $model->factor = 0;
                        $model->m2_impact = 0;
                        $model->m1_impact = 0;
                        $model->measure = $request->model_measure[$m];
                        $model->estimated_price_per_box = str_replace(',', '.',$request->estimated_price_per_box[$m]);
                        $model->estimated_price_quantity = str_replace(',', '.',$request->estimated_price_quantity[$m]);
                        $model->estimated_price = str_replace(',', '.',$request->estimated_price[$m]);
                        $model->combination = $request->model_combination[$m];
                    }
                    else
                    {
                        $model->price_impact = $request->model_price_impact[$m];
                        $model->impact_type = $request->model_impact_type[$m];
                        $model->factor = $request->model_price_impact[$m] == 4 ? 1 : 0;
                        $model->m2_impact = $request->model_price_impact[$m] == 3 ? 1 : 0;
                        $model->m1_impact = $request->model_price_impact[$m] == 2 ? 1 : 0;
                        $model->factor_max_width = $request->model_factor_max_width[$m] ? str_replace(",", ".", $request->model_factor_max_width[$m]) : NULL;
                        $model->curtain_type = $request->curtain_type[$m];
                    }

                    $model->size = $request->size_ids[$m] ? 1 : 0;
                    $model->size_id = $request->size_ids[$m];
                    $model->childsafe = $request->childsafe[$m];
                    $model->save();

                    if($request->form_type == 2)
                    {
                        $curtain_row = $request->row_curtain_id[$m];
                        $curtain_variable_options = "curtain_variable_options".$curtain_row;
                        $curtain_variable_descriptions = "curtain_variable_descriptions".$curtain_row;
                        $curtain_variable_values = "curtain_variable_values".$curtain_row;

                        $curtain_variables = $request->$curtain_variable_options;

                        if($curtain_variables)
                        {
                            foreach($curtain_variables as $cv => $curt)
                            {
                                if($request->$curtain_variable_descriptions[$cv] || $request->$curtain_variable_values[$cv])
                                {
                                    $curtain_variable = new curtain_variables;
                                    $curtain_variable->model_id = $model->id;
                                    $curtain_variable->enabled = $curt;
                                    $curtain_variable->description = $request->$curtain_variable_descriptions[$cv];
                                    $curtain_variable->value = $request->$curtain_variable_values[$cv] ? str_replace(",", ".", $request->$curtain_variable_values[$cv]) : 0;
                                    $curtain_variable->save();
                                }
                            }
                        }
                    }

                    foreach ($feature_row as $a => $abc)
                    {
                        $selected_feature = 'selected_model_feature' . $abc;
                        $link = $request->$selected_feature[$m];

                        $model_feature = new model_features;
                        $model_feature->model_id = $model->id;
                        $model_feature->product_feature_id = $feature_id[$a];
                        $model_feature->linked = $link;
                        $model_feature->save();
                    }
                }
            }

            if($request->form_type == 2)
            {
                foreach ($sub_products as $s => $key)
                {
                    if($key != NULL && $request->sub_product_titles[$s] != NULL)
                    {
                        $sub_pro = new product_ladderbands;
                        $sub_pro->title = $request->sub_product_titles[$s];
                        $sub_pro->product_id = $cat->id;
                        $sub_pro->code = $key;
                        $sub_pro->size1_value = $request->size1_value[$s];
                        $sub_pro->size2_value = $request->size2_value[$s];
                        $sub_pro->save();
                    }
                }

                foreach ($colors as $c => $key)
                {
                    if($key != NULL && $request->color_codes[$c] != NULL)
                    {
                        $col = new colors;
                        $col->title = $key;
                        $col->color_code = $request->color_codes[$c];
                        $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                        $col->product_id = $cat->id;
                        $col->table_id = $request->price_tables[$c] ? $request->price_tables[$c] : NULL;
                        $col->save();
                    }
                }

                // foreach ($pricesArray as $x => $price)
                // {
                //     $est = new estimated_prices;
                //     $est->product_id = $cat->id;
                //     $est->price = $price;
                //     $est->save();
                // }
            }
            else
            {
                foreach ($colors as $c => $key)
                {
                    if($key != NULL && $request->color_codes[$c] != NULL)
                    {
                        $col = new colors;
                        $col->title = $key;
                        $col->color_code = $request->color_codes[$c];
                        $col->max_height = $request->color_max_height[$c] ? str_replace(",",".",$request->color_max_height[$c]) : NULL;
                        $col->product_id = $cat->id;
                        $col->table_id = NULL;
                        $col->save();

                        $color_images = 'color_images'.$request->color_row[$c];

                        if($file = $request->file($color_images))
                        {                                        
                            foreach($file as $temp)
                            {
                                $name = time().'-'.$col->id.'-'.$temp->getClientOriginalName();
                                $compressedImagePath = $tmpFilePath.$name;
                                $this->compressImage($temp,$compressedImagePath);

                                $color_image = new color_images;
                                $color_image->product_id = $cat->id;
                                $color_image->color_id = $col->id;
                                $color_image->image = $name;
                                $color_image->save();
                            }
                        }
                    }
                }
            }                

            Session::flash('success', __('text.New Product added successfully.'));

            if($admin)
            {
                return redirect()->route('all-products');
            }
            else
            {
                return redirect()->route('admin-product-index');
            }

            // $check = Products::leftjoin('categories','categories.id','=','products.category_id')->leftjoin('brands','brands.id','=','products.brand_id')->where('products.user_id',$user_id)->where('products.title',$request->title)->where('categories.id',$request->category_id)->where('brands.id',$request->brand_id)->select('products.*')->first();
            
            // if(!$check)
            // {
                
            // }
            // else
            // {
            //     $cat = Category::where("id",$request->category_id)->pluck("cat_name")->first();

            //     if($admin)
            //     {
            //         $route = route('all-product-edit',$check->id);
            //         Session::flash('unsuccess', 'Product already exists with same title, model number, category and brand. You can edit that product <a style="color: #b44b33;font-weight: bold;" href="'.$route.'">here</a>');
            //         return redirect()->route('all-products');
            //     }
            //     else
            //     {
            //         $route = route('admin-product-edit',$check->id);
            //         Session::flash('unsuccess', 'Product already exists with same title, model number, category and brand. You can edit that product <a style="color: #b44b33;font-weight: bold;" href="'.$route.'">here</a>');
            //         return redirect()->route('admin-product-create', ['cat' => $cat]);
            //     }
            // }
        }
    }

    public function edit($id,$admin = 0)
    {
        if($admin == 1)
        {
            $user_id = Products::where("id",$id)->pluck("user_id")->first();
        }
        else
        {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            // $main_id = $user->main_id;
    
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }
        }

        $organization_id = user_organizations::where("user_id",$user_id)->pluck("organization_id")->first();
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if((isset($user) && $user->can('product-edit')) || $admin == 1)
        {
            $cats = Products::where('id','=',$id)->first();

            if(!$cats)
            {
                return redirect()->back();
            }

            $colors_data = colors::leftjoin('price_tables','price_tables.id','=','colors.table_id')->where('colors.product_id','=',$id)->select('colors.id','colors.title as color','colors.color_code','colors.table_id','colors.max_height','price_tables.title as table')->with('images')->get();
            $features_data = product_features::leftjoin("features_details","features_details.id","=","product_features.feature_value_id")->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where('product_features.product_id',$id)->where('product_features.sub_feature',0)->select("product_features.*","features_details.feature_id","default_features_details.title")->get();
            $sub_features_data = product_features::leftjoin("features_details","features_details.id","=","product_features.feature_value_id")->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where('product_features.product_id',$id)->where('product_features.sub_feature',1)->select("product_features.*","features_details.feature_id","default_features_details.title")->get();
            
            $feature_values = [];
            $sub_feature_values = [];

            foreach($features_data as $x => $fd)
            {
                $feature_values[$x] = features_details::leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where("features_details.feature_id",$fd->feature_id)->where("features_details.sub_feature",0)->where(function($query) use($fd,$cats) {
                    $query->where('features_details.id',$fd->feature_value_id)->orWhereRaw("find_in_set('$cats->sub_category_id',features_details.sub_category_ids)");
                })->select("features_details.*","default_features_details.title")->get();

                $sub_feature_values[$x] = features_details::leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where("features_details.main_id",$fd->feature_value_id)->select("features_details.*","default_features_details.title")->get();
            }

            $ladderband_data = product_ladderbands::where('product_id',$id)->get();
            $categories = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where('categories.id',$cats->category_id)->select('categories.*')->get();
            $category_id = $categories[0]->id;
            $predefined_models = predefined_models::whereRaw("find_in_set('$category_id',category_ids)")->whereIn('user_id',$related_users)->get();
            $sub_categories = sub_categories::where('parent_id',$cats->category_id)->get();

            $brands = Brand::where(function($query) use($related_users,$organization_id) {
                $query->whereIn('user_id',$related_users)->orWhere(function($query1) use($organization_id) {
                    $query1->whereRaw("find_in_set($organization_id,other_suppliers_organizations)")->where('trademark',0);
                });
            })->orderBy('id','desc')->get();

            $types = Model1::where('brand_id',$cats->brand_id)->get();
            $tables = price_tables::where('connected',1)->whereIn('user_id',$related_users)->get();
            $features_headings = features::whereIn('user_id',$related_users)->get();
            $models = product_models::leftjoin("predefined_models_details","predefined_models_details.id","=","product_models.size_id")
            ->leftjoin("default_predefined_models_details","default_predefined_models_details.id","=","predefined_models_details.default_model_detail_id")
            ->with(['features' => function($query)
            {
                $query->leftjoin('product_features','product_features.id','=','model_features.product_feature_id')
                    ->leftjoin('features_details','features_details.id','=','product_features.feature_value_id')    
                    ->leftjoin('default_features_details','default_features_details.id','=','features_details.default_value_id')
                    ->leftjoin('features','features.id','=','product_features.heading_id')
                    ->leftjoin('default_features','default_features.id','=','features.default_feature_id')
                    ->select('model_features.*','default_features.title as heading','default_features_details.title as feature_title');

            }])->select("product_models.*","default_predefined_models_details.model as size_title")->where('product_id',$id)->get();

            if($categories[0]->cat_name == 'Blinds' || $categories[0]->cat_name == 'Binnen zonwering')
            {
                return view('admin.product.create',compact('feature_values','sub_feature_values','user_id','admin','ladderband_data','cats','categories','sub_categories','brands','models','tables','colors_data','features_data','sub_features_data','features_headings','predefined_models'));
            }
            else
            {
                return view('admin.product.create_for_floors',compact('feature_values','sub_feature_values','user_id','admin','types','ladderband_data','cats','categories','sub_categories','brands','models','tables','colors_data','features_data','sub_features_data','features_headings','predefined_models'));
            }
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function destroy($id,$admin = 0)
    {
        $tmpFilePath = 'assets/colorImages/';
        
        if($admin == 1)
        {
            $user_id = Products::where("id",$id)->pluck("user_id")->first();
        }
        else
        {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            // $main_id = $user->main_id;
    
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }
        }

        $organization_id = user_organizations::where("user_id",$user_id)->pluck("organization_id")->first();
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if((isset($user) && $user->can('product-delete')) || $admin)
        {
            $cat = Products::where('id',$id)->where('organization_id',$organization_id)->first();

            if(!$cat)
            {
                return redirect()->back();
            }

            product_features::where('product_id',$id)->delete();
            product_ladderbands::where('product_id',$id)->delete();
            colors::where('product_id',$id)->delete();

            $c_images = color_images::where('product_id',$id)->get();
                                        
            foreach($c_images as $c_i1)
            {
                \File::delete(public_path($tmpFilePath.$c_i1->image));
            }

            color_images::where('product_id',$id)->delete();
            estimated_prices::where('product_id',$id)->delete();
            $model_ids = product_models::where('product_id',$id)->pluck('id');
            product_models::where('product_id',$id)->delete();
            model_features::whereIn('model_id',$model_ids)->delete();
            curtain_variables::whereIn('model_id',$model_ids)->delete();
            retailer_labor_costs::where('product_id',$id)->delete();
            retailer_margins::where('product_id',$id)->delete();

            if($cat->photo == null){
                $cat->delete();
                Session::flash('success', 'Product deleted successfully.');

                if($admin)
                {
                    return redirect()->route('all-products');
                }
                else
                {
                    return redirect()->route('admin-product-index');
                }
            }

            \File::delete(public_path() .'/assets/images/'.$cat->photo);
            $cat->delete();
            Session::flash('success', 'Product deleted successfully.');
            
            if($admin)
            {
                return redirect()->route('all-products');
            }
            else
            {
                return redirect()->route('admin-product-index');
            }
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function featuresData(Request $request)
    {
        $id = $request->id;
        $sub_id = $request->sub_id;

        if(!$request->user_id)
        {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            // $main_id = $user->main_id;
    
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }
        }
        else
        {
            $user_id = $request->user_id;
        }

        $organization_id = user_organizations::where("user_id",$user_id)->pluck("organization_id")->first();
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($request->value_id)
        {
            // $features = features_details::leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->leftjoin("features","features.id","=","features_details.feature_id")->where("features.user_id",$user_id)->where('default_features_details.main_id',$request->value_id)->select("features_details.*","default_features_details.id","default_features_details.title")->get();
            $features = features_details::leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")->where('features_details.main_id',$request->value_id)->select("features_details.*","default_features_details.title")->get();
        }
        else if($request->heading_id)
        {
            if($sub_id)
            {
                $features = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->whereHas('feature_details', function ($query) use ($sub_id) {
                    $query->whereRaw("find_in_set('$sub_id', features_details.sub_category_ids)");
                }, '>', 0)->with(['feature_details' => function($query) use($sub_id)
                {
                    $query->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")
                    ->whereRaw("find_in_set('$sub_id',features_details.sub_category_ids)")
                    ->select("features_details.*","default_features_details.title");
                }])->with('sub_features')->where('features.id',$request->heading_id)->whereIn('features.user_id',$related_users)->select("features.*","default_features.title")->get();
            }
            else
            {
                $features = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->with(['feature_details' => function($query)
                {
                    $query->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")
                    ->select("features_details.*","default_features_details.title");
                }])->with('sub_features')->whereRaw("find_in_set('$id',features.category_ids)")->where('features.id',$request->heading_id)->whereIn('features.user_id',$related_users)->select("features.*","default_features.title")->get();
            }
        }
        else
        {
            if($sub_id)
            {
                $features = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->whereHas('feature_details', function ($query) use ($sub_id) {
                    $query->whereRaw("find_in_set('$sub_id', features_details.sub_category_ids)");
                }, '>', 0)->with(['feature_details' => function($query) use($sub_id)
                {
                    $query->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")
                    ->whereRaw("find_in_set('$sub_id',features_details.sub_category_ids)")
                    ->select("features_details.*","default_features_details.title");
                }])->with('sub_features')->whereIn('features.user_id',$related_users)->select("features.*","default_features.title")->get();
            }
            else
            {
                $features = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->with(['feature_details' => function($query)
                {
                    $query->leftjoin("default_features_details","default_features_details.id","=","features_details.default_value_id")
                    ->select("features_details.*","default_features_details.title");
                }])->with('sub_features')->whereRaw("find_in_set('$id',features.category_ids)")->whereIn('features.user_id',$related_users)->select("features.*","default_features.title")->get();
            }
        }

        return $features;
    }
}
