<?php

namespace App\Http\Controllers;

use App\model_features;
use App\product_features;
use App\features;
use App\default_features;
use App\default_features_details;
use App\features_details;
use App\product_models;
use App\User;
use App\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreValidationRequest;
use App\Http\Requests\UpdateValidationRequest;
use Auth;
use App\Generalsetting;
use App\Category;
use App\sub_categories;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefaultFeaturesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $features = default_features::orderBy('id','desc')->get();

        $categories = Category::get();

        return view('admin.default_features.index',compact('features','categories'));
    }

    public function create()
    {
        $cats = Category::get();

        return view('admin.default_features.create',compact('cats'));
    }

    public function store(Request $request,$type = 0)
    {
        if($type == 0)
        {
            $check_fh = default_features::where("title",$request->title);

            if($request->heading_id)
            {
                $check_fh = $check_fh->where("id","!=",$request->heading_id);
            }
    
            $check_fh = $check_fh->first();
    
            if($check_fh)
            {
                Session::flash('unsuccess', 'Feature heading already exists in default features list.');
                return redirect()->back();
            }
        }

        if($request->comment_box)
        {
            $comment_box = 1;
        }
        else
        {
            $comment_box = 0;
        }

        $category_ids = implode(",",$request->feature_category);

        if($request->heading_id)
        {
            $feature = default_features::where('id',$request->heading_id)->first();
            Session::flash('success', 'Feature updated successfully.');
        }
        else
        {
            $feature = new default_features;
            Session::flash('success', 'New Feature added successfully.');
        }

        if($type == 0 || !$request->heading_id)
        {
            $feature->title = $request->title;
            $feature->comment_box = $comment_box;
            $feature->order_no = $request->order_no;
            $feature->quote_order_no = $request->quote_order_no;
            $feature->type = $request->feature_type;
            $feature->is_required = $request->feature_required;
            $feature->is_unique = $request->feature_unique;
            $feature->category_ids = $category_ids;
            $feature->filter = $request->feature_filter;
            $feature->save();
        }

        $feature_main_id = $feature->id;

        if($request->feature_type == 'Select' || $request->feature_type == 'Multiselect' || $request->feature_type == 'Checkbox')
        {
            $features = $request->features;
            $feature_ids = $request->feature_ids;
            $id_array = [];

            foreach($features as $x => $key)
            {
                $feature_check = default_features_details::where('id',$feature_ids[$x])->first();
                $f_rows = $request->f_rows;
                $sub_categories = 'sub_category_link'.$f_rows[$x];
                $sub_category_ids = [];

                if($type == 0)
                {
                    $suppliers_main_features_ids = [];
                }

                foreach($request->$sub_categories as $y => $sub_cat)
                {
                    if($sub_cat == 1)
                    {
                        $sub_category_id = 'sub_category_id'.$f_rows[$x];
                        $sub_category_ids[] = $request->$sub_category_id[$y];
                    }
                }

                $sub_categories = implode(',', $sub_category_ids);

                if(!$sub_categories)
                {
                    $sub_categories = NULL;
                }

                if($feature_check)
                {
                    if($key)
                    {
                        // $feature_check->feature_id = $feature_main_id;
                        $feature_check->title = $key;

                        if($type == 0)
                        {
                            $feature_check->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                            $feature_check->price_impact = $request->price_impact[$x];
                            $feature_check->impact_type = $request->impact_type[$x];
                            $feature_check->sub_category_ids = $sub_categories;
                            $feature_check->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                        }

                        $feature_check->save();
                        $main_id = $feature_check->id;

                        if($type == 0)
                        {
                            $suppliers_features = features::where("default_feature_id",$feature_main_id)->get();

                            foreach($suppliers_features as $spf)
                            {
                                $supplier_feature_details = features_details::where("default_value_id",$feature_check->id)->where("feature_id",$spf->id)->first();

                                if(!$supplier_feature_details) //if feature value was not created for this supplier feature than will create it first with default values
                                {
                                    $supplier_feature_details = new features_details;
                                    $supplier_feature_details->default_value_id = $feature_check->id;
                                    $supplier_feature_details->feature_id = $spf->id;
                                    $supplier_feature_details->title = $key;
                                    $supplier_feature_details->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                                    $supplier_feature_details->price_impact = $request->price_impact[$x];
                                    $supplier_feature_details->impact_type = $request->impact_type[$x];
                                    $supplier_feature_details->sub_category_ids = $sub_categories;
                                    $supplier_feature_details->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                                    $supplier_feature_details->save();
                                }
                                
                                $suppliers_main_features_ids[] = $supplier_feature_details->id;
                            }
                        }
                    }
                    else
                    {
                        $main_id = NULL;
                    }

                    $id_array[] = $feature_check->id;
                }
                else
                {
                    if($key)
                    {
                        $details = new default_features_details;
                        $details->feature_id = $feature_main_id;
                        $details->title = $key;
                        $details->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                        $details->price_impact = $request->price_impact[$x];
                        $details->impact_type = $request->impact_type[$x];
                        $details->sub_category_ids = $sub_categories;
                        $details->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                        $details->save();

                        if($type == 0)
                        {
                            $suppliers_features = features::where("default_feature_id",$feature_main_id)->get();

                            foreach($suppliers_features as $spf) //create new feature value with default values for all suppliers features
                            {
                                $supplier_feature_details = new features_details;
                                $supplier_feature_details->default_value_id = $details->id;
                                $supplier_feature_details->feature_id = $spf->id;
                                $supplier_feature_details->title = $key;
                                $supplier_feature_details->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                                $supplier_feature_details->price_impact = $request->price_impact[$x];
                                $supplier_feature_details->impact_type = $request->impact_type[$x];
                                $supplier_feature_details->sub_category_ids = $sub_categories;
                                $supplier_feature_details->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                                $supplier_feature_details->save();

                                $suppliers_main_features_ids[] = $supplier_feature_details->id;
                            }
                        }

                        $main_id = $details->id;
                        $id_array[] = $details->id;
                    }
                    else
                    {
                        $main_id = NULL;
                    }
                }

                if($main_id)
                {
                    $sub_features = 'features'.$f_rows[$x];
                    $sub_features = $request->$sub_features;

                    foreach ($sub_features as $s => $sub)
                    {
                        $sub_values = 'feature_values'.$f_rows[$x];
                        $sub_price_impact = 'price_impact'.$f_rows[$x];
                        $sub_impact_type = 'impact_type'.$f_rows[$x];
                        $sub_id = 'feature_row_ids'.$f_rows[$x];
                        $sub_factor_values = 'factor_values'.$f_rows[$x];

                        $sub_feature_check = default_features_details::where('id',$request->$sub_id[$s])->first();

                        if($sub_feature_check)
                        {
                            if($sub)
                            {
                                $sub_feature_check->title = $sub;
                                
                                if($type == 0)
                                {
                                    $sub_feature_check->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                    $sub_feature_check->price_impact = $request->$sub_price_impact[$s];
                                    $sub_feature_check->impact_type = $request->$sub_impact_type[$s];
                                    $sub_feature_check->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                }
                                
                                $sub_feature_check->save();
                            }

                            $id_array[] = $sub_feature_check->id;
                        }
                        else
                        {
                            if($sub)
                            {
                                $sub_feature = new default_features_details;
                                $sub_feature->feature_id = $feature_main_id;
                                $sub_feature->main_id = $main_id;
                                $sub_feature->sub_feature = 1;
                                $sub_feature->title = $sub;
                                $sub_feature->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                $sub_feature->price_impact = $request->$sub_price_impact[$s];
                                $sub_feature->impact_type = $request->$sub_impact_type[$s];
                                $sub_feature->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                $sub_feature->save();

                                if($type == 0)
                                {
                                    $suppliers_features = features::where("default_feature_id",$feature_main_id)->get();

                                    foreach($suppliers_features as $z => $spf) //create new sub feature value with default values for all suppliers features values
                                    {
                                        $supplier_sub_feature_details = new features_details;
                                        $supplier_sub_feature_details->default_value_id = $sub_feature->id;
                                        $supplier_sub_feature_details->feature_id = $spf->id;
                                        $supplier_sub_feature_details->main_id = $suppliers_main_features_ids[$z];
                                        $supplier_sub_feature_details->sub_feature = 1;
                                        $supplier_sub_feature_details->title = $sub;
                                        $supplier_sub_feature_details->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                        $supplier_sub_feature_details->price_impact = $request->$sub_price_impact[$s];
                                        $supplier_sub_feature_details->impact_type = $request->$sub_impact_type[$s];
                                        $supplier_sub_feature_details->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                        $supplier_sub_feature_details->save();
                                    }
                                }

                                $id_array[] = $sub_feature->id;
                            }
                        }

                    }

                }

            }

            $default_features_details_delete = default_features_details::whereNotIn('id',$id_array)->where('feature_id',$feature_main_id)->get();

            foreach($default_features_details_delete as $delete)
            {
                features_details::where("default_value_id",$delete->id)->delete();
            }

            default_features_details::whereNotIn('id',$id_array)->where('feature_id',$feature_main_id)->delete();
        }

        if($type == 0)
        {
            return redirect()->route('default-features-index');
        }
        else
        {
            return $feature_main_id;
        }
    }

    public function edit($id)
    {
        $feature = default_features::where('id',$id)->first();

        if(!$feature)
        {
            return redirect()->back();
        }

        $cats = Category::get();
        $features_data = default_features_details::where('feature_id',$feature->id)->where('sub_feature',0)->get();
        $sub_features_data = default_features_details::where('feature_id',$feature->id)->where('sub_feature',1)->get();
        $cat_ids = array_map('intval', explode(',', $feature->category_ids));
        // $sub_categories = default_features::leftjoin("categories",\DB::raw("FIND_IN_SET(categories.id,default_features.category_ids)"),">",\DB::raw("'0'"))->leftjoin('sub_categories','sub_categories.main_id','=','categories.id')->where('default_features.id',$feature->id)->where('sub_categories.deleted_at',NULL)->select('sub_categories.*','categories.cat_name as title')->get();
        $sub_categories = sub_categories::whereIn('parent_id',$cat_ids)->where('deleted_at',NULL)->orderBy('parent_id','asc')->get();

        return view('admin.default_features.create',compact('feature','cats','features_data','sub_features_data','sub_categories'));
    }

    public function destroy($id)
    {
        $check_linking = features::where("default_feature_id",$id)->first();

        if($check_linking)
        {
            Session::flash('unsuccess', 'This feature has been linked with supplier(s) features.');
            return redirect()->back();
        }

        $feature = default_features::where('id',$id)->first();

        if(!$feature)
        {
            return redirect()->back();
        }

        $feature->delete();

        default_features_details::where('feature_id',$id)->delete();
        Session::flash('success', 'Feature deleted successfully.');
        return redirect()->route('default-features-index');

    }
}
