<?php

namespace App\Http\Controllers;

use App\predefined_models;
use App\predefined_models_details;
use App\default_predefined_models;
use App\default_predefined_models_details;
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
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\organizations;
use App\predefined_models_updates;
use App\predefined_models_details_updates;
use App\Sociallink;

class PredefinedModelsController extends Controller
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

        if($user->can('user-models'))
        {
            $models = predefined_models::whereIn('user_id',$related_users)->orderBy('id','desc')->get();
            $default_models = default_predefined_models::orderBy('id','desc')->get();

            return view('admin.predefined_models.index',compact('models','default_models'));
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

        if($user->can('model-create'))
        {
            return view('admin.predefined_models.create',compact('cats'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function store(Request $request)
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

        $category_ids = implode(",",$request->model_category);
        $model_update_req = "";
        $supplier_model_check = 0;

        if($request->heading_id)
        {
            if(!$request->default_model)
            {
                $model_update_req = predefined_models_updates::where("model_id",$request->heading_id)->where("default_model",0)->whereIn("user_id",$related_users)->first();
                $supplier_model = predefined_models::where("id",$request->heading_id)->whereIn("user_id",$related_users)->first();
            }
            else
            {
                $model_update_req = predefined_models_updates::where("model_id",$request->heading_id)->where("default_model",1)->whereIn("user_id",$related_users)->first();
                $supplier_model = predefined_models::where("default_model_id",$request->heading_id)->whereIn("user_id",$related_users)->first();
            }
        }
        else
        {
            $check_model = default_predefined_models::where("title",$request->title)->first();

            if($check_model)
            {
                Session::flash('unsuccess', 'Model already exists in default models list.');
                return redirect()->back();
            }

            $supplier_model = "";
        }

        if(!$supplier_model)
        {
            if(!$request->default_model)
            {
                $supplier_model_check = 1;
                $default_models_controller = new DefaultPredefinedModelsController();
                $default_model_id = $default_models_controller->store((object)$request,1);

                $supplier_model = new predefined_models;
                $supplier_model->default_model_id = $default_model_id;
                $supplier_model->user_id = $user_id;
                $supplier_model->model = $request->title;
                $supplier_model->category_ids = $category_ids;
                $supplier_model->save();
        
                $supplier_model_id = $supplier_model->id;
            }
        }
        else
        {
            $supplier_model->category_ids = $category_ids;
            $supplier_model->save();
    
            $supplier_model_id = $supplier_model->id;
        }

        if($supplier_model_check)
        {
            $default_models_details = default_predefined_models_details::where('model_id',$default_model_id)->get();

            foreach($default_models_details as $dmd)
            {
                $dmd->model_id = $supplier_model_id;
                $dmd->default_model_detail_id = $dmd->id;
                $new_model_detail = $dmd->replicate();
                $new_model_detail->setTable('predefined_models_details');
                $new_model_detail->save();
            }
        }
        else
        {
            if(!$model_update_req)
            {
                $model_update_req = new predefined_models_updates;
                $model_update_req->user_id = $user_id;
                $model_update_req->model_id = $request->heading_id ? $request->heading_id : NULL;
                $model_update_req->default_model = $request->default_model;
                Session::flash('success', 'Model update request created successfully.');
            }
            else
            {
                Session::flash('success', 'Model update request updated successfully.');
            }

            $model_update_req->model = $request->title;
            $model_update_req->category_ids = $category_ids;
            $model_update_req->save();
    
            $model_id = $model_update_req->id;
    
            $sizes = $request->sizes;
            $size_ids = $request->size_ids;
            $id_array = [];

            foreach($sizes as $x => $key)
            {
                if($size_ids[$x])
                {
                    $size_check = predefined_models_details_updates::where('row_id',$size_ids[$x])->where("model_id",$model_id)->first();
                }
                else
                {
                    $size_check = "";
                }

                if($size_check)
                {
                    if($key)
                    {
                        $size_check->row_id = $size_ids[$x];
                        $size_check->model_id = $model_id;
                        $size_check->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                        $size_check->measure = $request->size_measure[$x];
                        $size_check->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                        $size_check->impact_type = $request->impact_type[$x];
                        $size_check->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                        $size_check->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                        $size_check->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                        $size_check->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                        $size_check->save();
                    }

                    $id_array[] = $size_check->id;
                }
                else
                {
                    if($key)
                    {
                        $details = new predefined_models_details_updates;
                        $details->row_id = $size_ids[$x];
                        $details->model_id = $model_id;
                        $details->model = $key;
                        $details->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                        $details->measure = $request->size_measure[$x];
                        $details->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                        $details->impact_type = $request->impact_type[$x];
                        $details->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                        $details->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                        $details->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                        $details->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                        $details->save();

                        $id_array[] = $details->id;
                    }
                }

                //----------Supplier Model Details----------\\

                if($supplier_model) //Check if supplier is not adding new default model to list
                {
                    $model_detail_check = predefined_models_details::where('default_model_detail_id',$size_ids[$x])->where("model_id",$supplier_model_id)->first();

                    if(!$model_detail_check && $size_ids[$x])
                    {
                        if($key)
                        {
                            $dmt = default_predefined_models_details::where("id",$size_ids[$x])->pluck("model")->first();

                            $model_detail_check = new predefined_models_details;
                            $model_detail_check->default_model_detail_id = $size_ids[$x];
                            $model_detail_check->model_id = $supplier_model_id;
                            $model_detail_check->model = $dmt;
                        }
                    }
    
                    if($model_detail_check)
                    {
                        if($key)
                        {
                            $model_detail_check->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                            $model_detail_check->measure = $request->size_measure[$x];
                            $model_detail_check->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                            $model_detail_check->impact_type = $request->impact_type[$x];
                            $model_detail_check->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                            $model_detail_check->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                            $model_detail_check->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                            $model_detail_check->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                            $model_detail_check->save();
                        }
                    }
                }
            }

            predefined_models_details_updates::whereNotIn('id',$id_array)->where('model_id',$model_id)->delete();
    
            $admin_email = $this->sl->admin_email;
    
            \Mail::send(array(), array(), function ($message) use ($admin_email,$user,$request) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Model Update Request')
                    ->html("Model update request submitted by supplier: <b>" . $user->company_name . "</b> for model: <b>" . $request->model . "</b><br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        }

        return redirect()->route('predefined-model-index');
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

        if(\Request::route()->getName() == 'predefined-model-edit')
        {
            if($user->can('model-edit'))
            {
                $model = predefined_models::leftjoin("default_predefined_models","default_predefined_models.id","=","predefined_models.default_model_id")->where('predefined_models.id',$id)->whereIn('predefined_models.user_id',$related_users)->select("predefined_models.*","default_predefined_models.model")->first();

                if(!$model)
                {
                    return redirect()->back();
                }

                $cats = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();

                $models_data = default_predefined_models_details::where('model_id',$model->default_model_id)->get();

                foreach($models_data as $key)
                {
                    $data = predefined_models_details::where("model_id",$id)->where("default_model_detail_id",$key->id)->first();

                    if($data)
                    {
                        $key->value = $data->value;
                        $key->measure = $data->measure;
                        $key->price_impact = $data->price_impact;
                        $key->impact_type = $data->impact_type;
                        $key->m1_impact = $data->m1_impact;
                        $key->m2_impact = $data->m2_impact;
                        $key->factor = $data->factor;
                        $key->factor_value = $data->factor_value;
                    }
                }

                return view('admin.predefined_models.create',compact('model','cats','models_data'));
            }
            else
            {
                return redirect()->route('user-login');
            }
        }
        else
        {
            if($user->can('model-edit'))
            {
                $check = predefined_models::where("default_model_id",$id)->whereIn("user_id",$related_users)->first();

                if($check) //check if supplier is trying to add default model in his list which is already added
                {
                    $url = route('predefined-model-edit', ['id' => $check->id]);
                    Session::flash('unsuccess', 'This default model is already added in list. You can edit that <a style="color: #4949d0;" href="'.$url.'">here</a>');
                    return redirect()->back();                    
                }

                $model = default_predefined_models::where('id',$id)->first();

                if(!$model)
                {
                    return redirect()->back();
                }

                $cats = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->select('categories.*')->get();
                $models_data = default_predefined_models_details::where('model_id',$model->id)->get();

                return view('admin.predefined_models.create',compact('model','cats','models_data'));
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

        if($user->can('model-delete'))
        {
            $model = predefined_models::where('id',$id)->whereIn('user_id',$related_users)->first();

            if(!$model)
            {
                return redirect()->back();
            }

            $detail_ids = predefined_models_details::where("model_id",$id)->pluck('id');

            $model->delete();
            $model_ids = product_models::whereIn('size_id',$detail_ids)->pluck('id');
            predefined_models_details::where('model_id',$id)->delete();
            product_models::whereIn('id',$model_ids)->delete();
            model_features::whereIn('model_id',$model_ids)->delete();

            Session::flash('success', 'Model deleted successfully.');
            return redirect()->route('predefined-model-index');
        }
        else
        {
            return redirect()->route('user-login');
        }

    }
}
