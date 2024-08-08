<?php

namespace App\Http\Controllers;

use App\model_features;
use App\product_features;
use App\features;
use App\features_details;
use App\default_features;
use App\default_features_details;
use App\features_updates;
use App\features_details_updates;
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
use App\organizations;

class FeaturesUpdateRequestsController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $route = \Route::currentRouteName();

        if($route == "admin-features-update-requests")
        {
            $features = features_updates::leftJoin('user_organizations', 'user_organizations.user_id', '=', 'features_updates.user_id')
            ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
            ->orderBy('features_updates.id','desc')->select("features_updates.*","organizations.company_name")->get();
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

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $features = features_updates::whereIn("user_id",$related_users)->orderBy('id','desc')->get();
        }

        $categories = Category::get();

        return view('admin.default_features.index',compact('features','categories'));
    }

    public function edit($id)
    {
        $route = \Route::currentRouteName();

        if($route == "admin-feature-update-request")
        {
            $feature = features_updates::where('id',$id)->first();
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

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $feature = features_updates::where('id',$id)->whereIn("user_id",$related_users)->first();
        }

        if(!$feature)
        {
            return redirect()->back();
        }

        $cats = Category::get();
        $features_data = features_details_updates::where('feature_id',$feature->id)->where('sub_feature',0)->get();
        $sub_features_data = features_details_updates::where('feature_id',$feature->id)->where('sub_feature',1)->get();
        $cat_ids = array_map('intval', explode(',', $feature->category_ids));
        // $sub_categories = default_features::leftjoin("categories",\DB::raw("FIND_IN_SET(categories.id,default_features.category_ids)"),">",\DB::raw("'0'"))->leftjoin('sub_categories','sub_categories.main_id','=','categories.id')->where('default_features.id',$feature->id)->where('sub_categories.deleted_at',NULL)->select('sub_categories.*','categories.cat_name as title')->get();
        $sub_categories = sub_categories::whereIn('parent_id',$cat_ids)->where('deleted_at',NULL)->orderBy('parent_id','asc')->get();

        return view('admin.default_features.create',compact('feature','cats','features_data','sub_features_data','sub_categories'));
    }

    public function create_update_feature($request)
    {
        $user_id = $request->user_id;
        
        if($request->comment_box)
        {
            $comment_box = 1;
        }
        else
        {
            $comment_box = 0;
        }

        $category_ids = implode(",",$request->feature_category);
        $feature_update_req = "";

        if($request->feature_id)
        {
            $feature = features::where('id',$request->feature_id)->first();
            Session::flash('success', 'Feature updated successfully.');
        }
        else
        {
            $feature = new features;
            Session::flash('success', 'New Feature added successfully.');
        }

        $feature->user_id = $user_id;
        $feature->title = $request->title;
        $feature->comment_box = $comment_box;
        $feature->order_no = $request->order_no;
        $feature->quote_order_no = $request->quote_order_no;
        $feature->type = $request->feature_type;
        $feature->is_required = $request->feature_required;
        $feature->is_unique = $request->feature_unique;
        $feature->category_ids = $category_ids;
        $feature->filter = $request->feature_filter;
        $feature->default_feature_id = $request->default_feature_id;
        $feature->save();

        $feature_main_id = $feature->id;

        if($request->feature_type == 'Select' || $request->feature_type == 'Multiselect' || $request->feature_type == 'Checkbox')
        {
            $id_array = [];
            $f_rows = $request->f_rows;
            $default_features_details = default_features_details::where('feature_id',$request->default_feature_id)->where('sub_feature',0)->get();

            foreach($default_features_details as $x => $dfd)
            {
                $sub_categories = 'sub_category_link'.$f_rows[$x];
                $sub_category_ids = [];

                if($request->$sub_categories)
                {
                    foreach($request->$sub_categories as $y => $sub_cat)
                    {
                        if($sub_cat == 1)
                        {
                            $sub_category_id = 'sub_category_id'.$f_rows[$x];
                            $sub_category_ids[] = $request->$sub_category_id[$y];
                        }
                    }
                }

                $sub_categories = implode(',', $sub_category_ids);

                if(!$sub_categories)
                {
                    $sub_categories = NULL;
                }

                $feature_check = features_details::where('default_value_id',$dfd->id)->where("feature_id",$feature_main_id)->first();

                if(!$feature_check)
                {
                    $dfd->feature_id = $feature_main_id;
                    $dfd->default_value_id = $dfd->id;
                    $dfd->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                    $dfd->price_impact = $request->price_impact[$x];
                    $dfd->impact_type = $request->impact_type[$x];
                    $dfd->sub_category_ids = $sub_categories;
                    $dfd->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                    $feature_check = $dfd->replicate();
                    $feature_check->setTable('features_details');
                    $feature_check->save();
                }
                else
                {
                    $feature_check->title = $dfd->title;
                    $feature_check->save();
                }
    
                $id_array[] = $feature_check->id;
                $sub_features = default_features_details::where('main_id',$dfd->id)->where('sub_feature',1)->get();
    
                foreach($sub_features as $s => $sub)
                {
                    $sub_values = 'feature_values'.$f_rows[$x];
                    $sub_price_impact = 'price_impact'.$f_rows[$x];
                    $sub_impact_type = 'impact_type'.$f_rows[$x];
                    $sub_factor_values = 'factor_values'.$f_rows[$x];

                    $sub_feature_check = features_details::where('default_value_id',$sub->id)->where("feature_id",$feature_main_id)->first();

                    if(!$sub_feature_check)
                    {
                        $sub->feature_id = $feature_main_id;
                        $sub->main_id = $feature_check->id;
                        $sub->default_value_id = $sub->id;
                        $sub->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                        $sub->price_impact = $request->$sub_price_impact[$s];
                        $sub->impact_type = $request->$sub_impact_type[$s];
                        $sub->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                        $sub_feature_check = $sub->replicate();
                        $sub_feature_check->setTable('features_details');
                        $sub_feature_check->save();
                    }
                    else
                    {
                        $sub_feature_check->title = $sub->title;
                        $sub_feature_check->save();
                    }

                    $id_array[] = $sub_feature_check->id;
                }
            }

            features_details::whereNotIn('id',$id_array)->where('feature_id',$feature_main_id)->delete();
        }

        return;
    }

    public function post(Request $request)
    {
        $route = \Route::currentRouteName();
        $inputData = $request->all();

        if($route == "admin-feature-update-request-post")
        {
            $update_id = $request->heading_id;
            $update_req = features_updates::where("id",$update_id)->first();

            if($update_req->feature_id)
            {
                if(!$update_req->default_feature)
                {
                    $update_req->default_feature_id = features::where("id",$update_req->feature_id)->pluck("default_feature_id")->first();
                }
                else
                {
                    $update_req->default_feature_id = $update_req->feature_id;
                }

                $exists = default_features::where("id",$update_req->default_feature_id)->first();    
            }
            else
            {
                $exists = default_features::where("title",$request->title)->first();    
            }

            if(!$exists)
            {
                $inputData['heading_id'] = "";
            }
            else
            {
                $inputData['heading_id'] = $exists->id;
            }

            $request->replace($inputData);

            $default_features_controller = new DefaultFeaturesController();
            $default_feature_id = $default_features_controller->store((object)$request,1);

            $update_req->feature_id = features::where("default_feature_id",$default_feature_id)->where("user_id",$update_req->user_id)->pluck("id")->first();

            $inputData['feature_id'] = $update_req->feature_id;
            $inputData['user_id'] = $update_req->user_id;
            $inputData['default_feature_id'] = $default_feature_id;
            // Update the request with the modified input data
            $request->replace($inputData);
            $this->create_update_feature($request);

            features_updates::where("id",$update_id)->delete();
            features_details_updates::where("feature_id",$update_id)->delete();

            return redirect()->route("admin-features-update-requests");
        }
    }

    public function destroy($id)
    {
        $route = \Route::currentRouteName();
        
        if($route == "admin-feature-update-request-delete")
        {
            $feature = features_updates::where('id',$id)->first();
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

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $feature = features_updates::where('id',$id)->whereIn("user_id",$related_users)->first();
        }

        if(!$feature)
        {
            return redirect()->back();
        }

        $feature->delete();

        features_details_updates::where('feature_id',$id)->delete();
        Session::flash('success', 'Feature update request deleted successfully.');
        
        return redirect()->back();
    }
}
