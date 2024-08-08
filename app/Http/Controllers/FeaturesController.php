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
use App\supplier_categories;
use App\Sociallink;
use App\organizations;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeaturesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
        $this->sl = Sociallink::findOrFail(1);
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

        if($user->can('user-features'))
        {
            $features = features::whereIn('user_id',$related_users)->orderBy('id','desc')->get();
            $default_features = default_features::orderBy('id','desc')->get();

            return view('admin.features.index',compact('features','default_features'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function create()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;

        $cats = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();

        if($user->can('create-feature'))
        {
            return view('admin.features.create',compact('cats'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function store(Request $request)
    {
        if($request->comment_box)
        {
            $comment_box = 1;
        }
        else
        {
            $comment_box = 0;
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where("id",$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $category_ids = implode(",",$request->feature_category);
        $feature_update_req = "";
        $supplier_feature_check = 0;

        if($request->heading_id)
        {
            if(!$request->default_feature)
            {
                $feature_update_req = features_updates::where("feature_id",$request->heading_id)->where("default_feature",0)->whereIn("user_id",$related_users)->first();
                $supplier_feature = features::where("id",$request->heading_id)->whereIn("user_id",$related_users)->first();
            }
            else
            {
                $feature_update_req = features_updates::where("feature_id",$request->heading_id)->where("default_feature",1)->whereIn("user_id",$related_users)->first();
                $supplier_feature = features::where("default_feature_id",$request->heading_id)->whereIn("user_id",$related_users)->first();

                // $check_heading = default_features::where("title",$request->title)->where("id","!=",$request->heading_id)->first();

                // if($check_heading)
                // {
                //     Session::flash('unsuccess', 'Feature heading already exists in default features list.');
                //     return redirect()->back();
                // }
                // else
                // {
                //     $feature_update_req = features_updates::where("feature_id",$request->heading_id)->where("default_feature",1)->whereIn("user_id",$related_users)->first();
                //     $supplier_feature = features::where("default_feature_id",$request->heading_id)->whereIn("user_id",$related_users)->first();
                // }
            }
        }
        else
        {
            $check_heading = default_features::where("title",$request->title)->first();

            if($check_heading)
            {
                Session::flash('unsuccess', 'Feature heading already exists in default features list.');
                return redirect()->back();
            }

            $supplier_feature = "";
        }

        if(!$supplier_feature)
        {
            if(!$request->default_feature)
            {
                $supplier_feature_check = 1;
                $default_features_controller = new DefaultFeaturesController();
                $default_feature_id = $default_features_controller->store((object)$request,1);

                $supplier_feature = new features;
                $supplier_feature->default_feature_id = $default_feature_id;
                $supplier_feature->user_id = $user_id;
                $supplier_feature->title = $request->title;
                $supplier_feature->comment_box = $comment_box;
                $supplier_feature->order_no = $request->order_no;
                $supplier_feature->quote_order_no = $request->quote_order_no;
                $supplier_feature->type = $request->feature_type;
                $supplier_feature->is_required = $request->feature_required;
                $supplier_feature->is_unique = $request->feature_unique;
                $supplier_feature->category_ids = $category_ids;
                $supplier_feature->filter = $request->feature_filter;
                $supplier_feature->save();
        
                $supplier_feature_main_id = $supplier_feature->id;
            }
        }
        else
        {
            $supplier_feature->comment_box = $comment_box;
            $supplier_feature->order_no = $request->order_no;
            $supplier_feature->quote_order_no = $request->quote_order_no;
            $supplier_feature->type = $request->feature_type;
            $supplier_feature->is_required = $request->feature_required;
            $supplier_feature->is_unique = $request->feature_unique;
            $supplier_feature->category_ids = $category_ids;
            $supplier_feature->filter = $request->feature_filter;
            $supplier_feature->save();
    
            $supplier_feature_main_id = $supplier_feature->id;
        }

        if($supplier_feature_check)
        {
            $default_features_details = default_features_details::where('feature_id',$default_feature_id)->where('sub_feature',0)->get();

            foreach($default_features_details as $dfd)
            {
                $dfd->feature_id = $supplier_feature_main_id;
                $dfd->default_value_id = $dfd->id;
                $new_feature_value = $dfd->replicate();
                $new_feature_value->setTable('features_details');
                $new_feature_value->save();
    
                $sub_values = default_features_details::where('main_id',$dfd->id)->where('sub_feature',1)->get();
    
                foreach($sub_values as $sv)
                {
                    $sv->feature_id = $supplier_feature_main_id;
                    $sv->default_value_id = $sv->id;
                    $sv->main_id = $new_feature_value->id;
                    $new_feature_sub_value = $sv->replicate();
                    $new_feature_sub_value->setTable('features_details');
                    $new_feature_sub_value->save();
                }
            }
        }
        else
        {
            if(!$feature_update_req)
            {
                $feature_update_req = new features_updates;
                $feature_update_req->user_id = $user_id;
                $feature_update_req->feature_id = $request->heading_id ? $request->heading_id : NULL;
                $feature_update_req->default_feature = $request->default_feature;
                Session::flash('success', 'Feature update request created successfully.');
            }
            else
            {
                Session::flash('success', 'Feature update request updated successfully.');
            }

            $feature_update_req->title = $request->title;
            $feature_update_req->comment_box = $comment_box;
            $feature_update_req->order_no = $request->order_no;
            $feature_update_req->quote_order_no = $request->quote_order_no;
            $feature_update_req->type = $request->feature_type;
            $feature_update_req->is_required = $request->feature_required;
            $feature_update_req->is_unique = $request->feature_unique;
            $feature_update_req->category_ids = $category_ids;
            $feature_update_req->filter = $request->feature_filter;
            $feature_update_req->save();
    
            $feature_main_id = $feature_update_req->id;
    
            if($request->feature_type == 'Select' || $request->feature_type == 'Multiselect' || $request->feature_type == 'Checkbox')
            {
                $features = $request->features;
                $feature_ids = $request->feature_ids;
                $id_array = [];
    
                foreach($features as $x => $key)
                {
                    if($feature_ids[$x])
                    {
                        $feature_check = features_details_updates::where('row_id',$feature_ids[$x])->where("feature_id",$feature_main_id)->first();
                    }
                    else
                    {
                        $feature_check = "";
                    }
                    
                    $f_rows = $request->f_rows;
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
    
                    if($feature_check)
                    {
                        if($key)
                        {
                            $feature_check->row_id = $feature_ids[$x];
                            $feature_check->feature_id = $feature_main_id;
                            $feature_check->title = $key;
                            $feature_check->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                            $feature_check->price_impact = $request->price_impact[$x];
                            $feature_check->impact_type = $request->impact_type[$x];
                            $feature_check->sub_category_ids = $sub_categories;
                            $feature_check->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                            $feature_check->save();
    
                            $main_id = $feature_check->id;
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
                            $details = new features_details_updates;
                            $details->row_id = $feature_ids[$x];
                            $details->feature_id = $feature_main_id;
                            $details->title = $key;
                            $details->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                            $details->price_impact = $request->price_impact[$x];
                            $details->impact_type = $request->impact_type[$x];
                            $details->sub_category_ids = $sub_categories;
                            $details->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                            $details->save();
    
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
    
                            if($request->$sub_id[$s])
                            {
                                $sub_feature_check = features_details_updates::where('row_id',$request->$sub_id[$s])->where("feature_id",$feature_main_id)->first();
                            }
                            else
                            {
                                $sub_feature_check = "";
                            }
    
                            if($sub_feature_check)
                            {
                                if($sub)
                                {
                                    $sub_feature_check->row_id = $request->$sub_id[$s];
                                    $sub_feature_check->title = $sub;
                                    $sub_feature_check->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                    $sub_feature_check->price_impact = $request->$sub_price_impact[$s];
                                    $sub_feature_check->impact_type = $request->$sub_impact_type[$s];
                                    $sub_feature_check->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                    $sub_feature_check->save();
                                }
    
                                $id_array[] = $sub_feature_check->id;
                            }
                            else
                            {
                                if($sub)
                                {
                                    $sub_feature = new features_details_updates;
                                    $sub_feature->row_id = $request->$sub_id[$s];
                                    $sub_feature->feature_id = $feature_main_id;
                                    $sub_feature->main_id = $main_id;
                                    $sub_feature->sub_feature = 1;
                                    $sub_feature->title = $sub;
                                    $sub_feature->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                    $sub_feature->price_impact = $request->$sub_price_impact[$s];
                                    $sub_feature->impact_type = $request->$sub_impact_type[$s];
                                    $sub_feature->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                    $sub_feature->save();
    
                                    $id_array[] = $sub_feature->id;
                                }
                            }
                        }
                    }

                    //----------Supplier Feature Values----------\\

                    if($supplier_feature) //Check if supplier is not adding new default feature to list
                    {
                        $feature_check = features_details::where('default_value_id',$feature_ids[$x])->where("feature_id",$supplier_feature_main_id)->first();
                        $f_rows = $request->f_rows;
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

                        if(!$feature_check && $feature_ids[$x])
                        {
                            if($key)
                            {
                                $ft = default_features_details::where("id",$feature_ids[$x])->pluck("title")->first();

                                $feature_check = new features_details;
                                $feature_check->default_value_id = $feature_ids[$x];
                                $feature_check->feature_id = $supplier_feature_main_id;
                                $feature_check->title = $ft;
                            }
                        }
        
                        if($feature_check)
                        {
                            if($key)
                            {
                                // $feature_check->title = $key;
                                $feature_check->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
                                $feature_check->price_impact = $request->price_impact[$x];
                                $feature_check->impact_type = $request->impact_type[$x];
                                $feature_check->sub_category_ids = $sub_categories;
                                $feature_check->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
                                $feature_check->save();
        
                                $sub_features = 'features'.$f_rows[$x];
                                $sub_features = $request->$sub_features;
            
                                foreach ($sub_features as $s => $sub)
                                {
                                    $sub_values = 'feature_values'.$f_rows[$x];
                                    $sub_price_impact = 'price_impact'.$f_rows[$x];
                                    $sub_impact_type = 'impact_type'.$f_rows[$x];
                                    $sub_id = 'feature_row_ids'.$f_rows[$x];
                                    $sub_factor_values = 'factor_values'.$f_rows[$x];
            
                                    $sub_feature_check = features_details::where('default_value_id',$request->$sub_id[$s])->where("feature_id",$supplier_feature_main_id)->first();
            
                                    if(!$sub_feature_check && $request->$sub_id[$s])
                                    {
                                        if($key)
                                        {
                                            $sft = default_features_details::where("id",$request->$sub_id[$s])->pluck("title")->first();

                                            $sub_feature_check = new features_details;
                                            $sub_feature_check->default_value_id = $request->$sub_id[$s];
                                            $sub_feature_check->feature_id = $supplier_feature_main_id;
                                            $sub_feature_check->main_id = $feature_check->id;
                                            $sub_feature_check->sub_feature = 1;
                                            $sub_feature_check->title = $sft;
                                        }
                                    }

                                    if($sub_feature_check)
                                    {
                                        if($sub)
                                        {
                                            // $sub_feature_check->title = $sub;
                                            $sub_feature_check->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
                                            $sub_feature_check->price_impact = $request->$sub_price_impact[$s];
                                            $sub_feature_check->impact_type = $request->$sub_impact_type[$s];
                                            $sub_feature_check->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
                                            $sub_feature_check->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
    
                features_details_updates::whereNotIn('id',$id_array)->where('feature_id',$feature_main_id)->delete();
            }
    
            $admin_email = $this->sl->admin_email;
    
            \Mail::send(array(), array(), function ($message) use ($admin_email,$user,$request) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Feature Update Request')
                    ->html("Feature update request submitted by supplier: <b>" . $user->company_name . "</b> for feature heading: <b>" . $request->title . "</b><br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        }

        // if(!$request->default_feature && $request->heading_id)
        // {
        //     $feature = features::where('id',$request->heading_id)->first();
        //     Session::flash('success', 'Feature updated successfully.');
        // }
        // else
        // {
        //     $feature = new features;
        //     Session::flash('success', 'New Feature added successfully.');
        // }

        // $feature->user_id = $user_id;
        // $feature->title = $request->title;
        // $feature->comment_box = $comment_box;
        // $feature->order_no = $request->order_no;
        // $feature->quote_order_no = $request->quote_order_no;
        // $feature->type = $request->feature_type;
        // $feature->is_required = $request->feature_required;
        // $feature->is_unique = $request->feature_unique;
        // $feature->category_ids = $category_ids;
        // $feature->filter = $request->feature_filter;
        // $feature->default_feature_id = $request->default_feature ? $request->heading_id : NULL;
        // $feature->save();

        // $feature_main_id = $feature->id;

        // if($request->feature_type == 'Select' || $request->feature_type == 'Multiselect' || $request->feature_type == 'Checkbox')
        // {
        //     $features = $request->features;
        //     $feature_ids = $request->feature_ids;
        //     $id_array = [];

        //     foreach($features as $x => $key)
        //     {
        //         $feature_check = features_details::where('id',$feature_ids[$x])->first();
        //         $f_rows = $request->f_rows;
        //         $sub_categories = 'sub_category_link'.$f_rows[$x];
        //         $sub_category_ids = [];

        //         if($request->$sub_categories)
        //         {
        //             foreach($request->$sub_categories as $y => $sub_cat)
        //             {
        //                 if($sub_cat == 1)
        //                 {
        //                     $sub_category_id = 'sub_category_id'.$f_rows[$x];
        //                     $sub_category_ids[] = $request->$sub_category_id[$y];
        //                 }
        //             }
        //         }

        //         $sub_categories = implode(',', $sub_category_ids);

        //         if(!$sub_categories)
        //         {
        //             $sub_categories = NULL;
        //         }

        //         if($feature_check)
        //         {
        //             if($key)
        //             {
        //                 $feature_check->feature_id = $feature_main_id;
        //                 $feature_check->title = $key;
        //                 $feature_check->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
        //                 $feature_check->price_impact = $request->price_impact[$x];
        //                 $feature_check->impact_type = $request->impact_type[$x];
        //                 $feature_check->sub_category_ids = $sub_categories;
        //                 $feature_check->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
        //                 $feature_check->save();

        //                 $main_id = $feature_check->id;
        //             }
        //             else
        //             {
        //                 $main_id = NULL;
        //             }

        //             $id_array[] = $feature_check->id;
        //         }
        //         else
        //         {
        //             if($key)
        //             {
        //                 $details = new features_details;
        //                 $details->feature_id = $feature_main_id;
        //                 $details->title = $key;
        //                 $details->value = $request->feature_values[$x] ? $request->feature_values[$x] : 0;
        //                 $details->price_impact = $request->price_impact[$x];
        //                 $details->impact_type = $request->impact_type[$x];
        //                 $details->sub_category_ids = $sub_categories;
        //                 $details->factor_value = $request->factor_values[$x] ? $request->factor_values[$x] : 0;
        //                 $details->save();

        //                 $main_id = $details->id;
        //                 $id_array[] = $details->id;
        //             }
        //             else
        //             {
        //                 $main_id = NULL;
        //             }
        //         }

        //         if($main_id)
        //         {
        //             $sub_features = 'features'.$f_rows[$x];
        //             $sub_features = $request->$sub_features;

        //             foreach ($sub_features as $s => $sub)
        //             {
        //                 $sub_values = 'feature_values'.$f_rows[$x];
        //                 $sub_price_impact = 'price_impact'.$f_rows[$x];
        //                 $sub_impact_type = 'impact_type'.$f_rows[$x];
        //                 $sub_id = 'feature_row_ids'.$f_rows[$x];
        //                 $sub_factor_values = 'factor_values'.$f_rows[$x];

        //                 $sub_feature_check = features_details::where('id',$request->$sub_id[$s])->first();

        //                 if($sub_feature_check)
        //                 {
        //                     if($sub)
        //                     {
        //                         $sub_feature_check->title = $sub;
        //                         $sub_feature_check->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
        //                         $sub_feature_check->price_impact = $request->$sub_price_impact[$s];
        //                         $sub_feature_check->impact_type = $request->$sub_impact_type[$s];
        //                         $sub_feature_check->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
        //                         $sub_feature_check->save();
        //                     }

        //                     $id_array[] = $sub_feature_check->id;
        //                 }
        //                 else
        //                 {
        //                     if($sub)
        //                     {
        //                         $sub_feature = new features_details;
        //                         $sub_feature->feature_id = $feature_main_id;
        //                         $sub_feature->main_id = $main_id;
        //                         $sub_feature->sub_feature = 1;
        //                         $sub_feature->title = $sub;
        //                         $sub_feature->value = $request->$sub_values[$s] ? $request->$sub_values[$s] : 0;
        //                         $sub_feature->price_impact = $request->$sub_price_impact[$s];
        //                         $sub_feature->impact_type = $request->$sub_impact_type[$s];
        //                         $sub_feature->factor_value = $request->$sub_factor_values[$s] ? $request->$sub_factor_values[$s] : 0;
        //                         $sub_feature->save();

        //                         $id_array[] = $sub_feature->id;
        //                     }
        //                 }
        //             }
        //         }

        //     }

        //     features_details::whereNotIn('id',$id_array)->where('feature_id',$feature_main_id)->delete();
        // }

        return redirect()->route('admin-feature-index');
    }

    public function edit($id)
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

        if(\Request::route()->getName() == 'admin-feature-edit')
        {
            if($user->can('edit-feature'))
            {
                $feature = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->where('features.id',$id)->whereIn('features.user_id',$related_users)->select("features.*","default_features.title")->first();

                if(!$feature)
                {
                    return redirect()->back();
                }

                $cats = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();
                
                $features_data = default_features_details::where('feature_id',$feature->default_feature_id)->where('sub_feature',0)->get();

                foreach($features_data as $key)
                {
                    $data = features_details::where("feature_id",$id)->where("default_value_id",$key->id)->first();

                    if($data)
                    {
                        $key->value = $data->value;
                        $key->price_impact = $data->price_impact;
                        $key->impact_type = $data->impact_type;
                        $key->sub_category_ids = $data->sub_category_ids;
                        $key->factor_value = $data->factor_value;
                    }
                }

                $sub_features_data = default_features_details::where('feature_id',$feature->default_feature_id)->where('sub_feature',1)->get();
                
                foreach($sub_features_data as $key)
                {
                    $data = features_details::where("feature_id",$id)->where("default_value_id",$key->id)->first();

                    if($data)
                    {
                        $key->value = $data->value;
                        $key->price_impact = $data->price_impact;
                        $key->impact_type = $data->impact_type;
                        $key->sub_category_ids = $data->sub_category_ids;
                        $key->factor_value = $data->factor_value;
                    }
                }

                // $sub_categories = features::leftjoin("categories",\DB::raw("FIND_IN_SET(categories.id,features.category_ids)"),">",\DB::raw("'0'"))->leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->leftjoin('sub_categories','sub_categories.main_id','=','categories.id')->where('supplier_categories.user_id',$user_id)->where('features.id',$feature->id)->where('sub_categories.deleted_at','=',NULL)->select('sub_categories.*','categories.cat_name as title')->get();
                $cat_ids = array_map('intval', explode(',', $feature->category_ids));
                $supplier_categories = array();

                foreach($cat_ids as $key)
                {
                    $find = supplier_categories::where('category_id',$key)->where('organization_id',$organization_id)->first();
                    
                    if($find)
                    {
                        $supplier_categories[] = $key;
                    }
                }

                $sub_categories = sub_categories::whereIn('parent_id',$supplier_categories)->where('deleted_at',NULL)->orderBy('parent_id','desc')->get();

                return view('admin.features.create',compact('feature','cats','features_data','sub_features_data','sub_categories'));
            }
            else
            {
                return redirect()->route('user-login');
            }
        }
        else
        {
            if($user->can('edit-feature'))
            {
                $check = features::where("default_feature_id",$id)->whereIn("user_id",$related_users)->first();

                if($check) //check if supplier is trying to add default feature in his list which is already added
                {
                    $url = route('admin-feature-edit', ['id' => $check->id]);
                    Session::flash('unsuccess', 'This default feature is already added in list. You can edit that <a style="color: #4949d0;" href="'.$url.'">here</a>');
                    return redirect()->back();                    
                }

                $feature = default_features::where('id',$id)->first();

                if(!$feature)
                {
                    return redirect()->back();
                }

                $cats = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();
                $features_data = default_features_details::where('feature_id',$feature->id)->where('sub_feature',0)->get();
                $sub_features_data = default_features_details::where('feature_id',$feature->id)->where('sub_feature',1)->get();
                // $sub_categories = default_features::leftjoin("categories",\DB::raw("FIND_IN_SET(categories.id,default_features.category_ids)"),">",\DB::raw("'0'"))->leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->leftjoin('sub_categories','sub_categories.main_id','=','categories.id')->where('supplier_categories.user_id',$user_id)->where('default_features.id',$feature->id)->where('sub_categories.deleted_at','=',NULL)->select('sub_categories.*','categories.cat_name as title')->get();
                $cat_ids = array_map('intval', explode(',', $feature->category_ids));
                $supplier_categories = array();

                foreach($cat_ids as $key)
                {
                    $find = supplier_categories::where('category_id',$key)->where('organization_id',$organization_id)->first();
                    
                    if($find)
                    {
                        $supplier_categories[] = $key;
                    }
                }

                $sub_categories = sub_categories::whereIn('parent_id',$supplier_categories)->where('deleted_at',NULL)->orderBy('parent_id','desc')->get();

                return view('admin.features.create',compact('feature','cats','features_data','sub_features_data','sub_categories'));
            }
            else
            {
                return redirect()->route('user-login');
            }
        }
    }

    public function destroy($id)
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

        if($user->can('delete-feature'))
        {
            $feature = features::where('id',$id)->whereIn('user_id',$related_users)->first();

            if(!$feature)
            {
                return redirect()->back();
            }

            $feature->delete();

            $feature_ids = product_features::where('heading_id',$id)->pluck('id');
            features_details::where('feature_id',$id)->delete();
            product_features::where('heading_id',$id)->delete();
            model_features::whereIn('product_feature_id',$feature_ids)->delete();
            Session::flash('success', 'Feature deleted successfully.');
            return redirect()->route('admin-feature-index');
        }
        else
        {
            return redirect()->route('user-login');
        }

    }
}
