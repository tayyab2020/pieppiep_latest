<?php

namespace App\Http\Controllers;

use App\product_models;
use App\predefined_models;
use App\predefined_models_details;
use App\default_predefined_models;
use App\default_predefined_models_details;
use App\predefined_models_updates;
use App\predefined_models_details_updates;
use App\User;
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

class ModelsUpdateRequestsController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $route = \Route::currentRouteName();

        if($route == "admin-models-update-requests")
        {
            $models = predefined_models_updates::leftJoin('user_organizations', 'user_organizations.user_id', '=', 'predefined_models_updates.user_id')
            ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
            ->orderBy('predefined_models_updates.id','desc')->select("predefined_models_updates.*","organizations.company_name")->get();
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

            $models = predefined_models_updates::whereIn("user_id",$related_users)->orderBy('id','desc')->get();
        }

        $categories = Category::get();

        return view('admin.default_predefined_models.index',compact('models','categories'));
    }

    public function edit($id)
    {
        $route = \Route::currentRouteName();

        if($route == "admin-model-update-request")
        {
            $model = predefined_models_updates::where('id',$id)->first();
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

            $model = predefined_models_updates::where('id',$id)->whereIn("user_id",$related_users)->first();
        }

        if(!$model)
        {
            return redirect()->back();
        }

        $cats = Category::get();
        $models_data = predefined_models_details_updates::where('model_id',$model->id)->get();

        return view('admin.default_predefined_models.create',compact('model','cats','models_data'));
    }

    public function create_update_model($request)
    {
        $user_id = $request->user_id;

        $category_ids = implode(",",$request->model_category);
        $model_update_req = "";

        if($request->model_id)
        {
            $model = predefined_models::where('id',$request->model_id)->first();
            Session::flash('success', 'Model updated successfully.');
        }
        else
        {
            $model = new predefined_models;
            Session::flash('success', 'New Model added successfully.');
        }

        $model->user_id = $user_id;
        $model->model = $request->title;
        $model->category_ids = $category_ids;
        $model->default_model_id = $request->default_model_id;
        $model->save();

        $model_id = $model->id;

        $id_array = [];
        $default_models_details = default_predefined_models_details::where('model_id',$request->default_model_id)->get();

        foreach($default_models_details as $x => $dmd)
        {
            $model_check = predefined_models_details::where('default_model_detail_id',$dmd->id)->where("model_id",$model_id)->first();

            if(!$model_check)
            {
                $dmd->model_id = $model_id;
                $dmd->default_model_detail_id = $dmd->id;
                $dmd->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                $dmd->measure = $request->size_measure[$x];
                $dmd->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                $dmd->impact_type = $request->impact_type[$x];
                $dmd->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                $dmd->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                $dmd->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                $dmd->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                $model_check = $dmd->replicate();
                $model_check->setTable('predefined_models_details');
                $model_check->save();
            }
            else
            {
                $model_check->model = $dmd->model;
                $model_check->save();
            }

            $id_array[] = $model_check->id;
        }

        $other_suppliers_models = predefined_models::where('id','!=',$request->model_id)->where("default_model_id",$request->default_model_id)->get();

        foreach($other_suppliers_models as $spm) //if new size is created than create new size with default values for all suppliers models
        {
            foreach($default_models_details as $x => $dmd)
            {
                $details_check = predefined_models_details::where('default_model_detail_id',$dmd->id)->where("model_id",$spm->id)->first();

                if(!$details_check)
                {
                    $dmd->model_id = $spm->id;
                    $dmd->default_model_detail_id = $dmd->id;
                    $dmd->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                    $dmd->measure = $request->size_measure[$x];
                    $dmd->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                    $dmd->impact_type = $request->impact_type[$x];
                    $dmd->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                    $dmd->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                    $dmd->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                    $dmd->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                    $details_check = $dmd->replicate();
                    $details_check->setTable('predefined_models_details');
                    $details_check->save();
                }
            }
        }

        predefined_models_details::whereNotIn('id',$id_array)->where('model_id',$model_id)->delete();

        return;
    }

    public function post(Request $request)
    {
        $route = \Route::currentRouteName();
        $inputData = $request->all();

        if($route == "admin-model-update-request-post")
        {
            $update_id = $request->heading_id;
            $update_req = predefined_models_updates::where("id",$update_id)->first();

            if($update_req->model_id)
            {
                if(!$update_req->default_model)
                {
                    $update_req->default_model_id = predefined_models::where("id",$update_req->model_id)->pluck("default_model_id")->first();
                }
                else
                {
                    $update_req->default_model_id = $update_req->model_id;
                }

                $exists = default_predefined_models::where("id",$update_req->default_model_id)->first();    
            }
            else
            {
                $exists = default_predefined_models::where("model",$request->title)->first();    
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

            $default_models_controller = new DefaultPredefinedModelsController();
            $default_model_id = $default_models_controller->store((object)$request,1);

            $update_req->model_id = predefined_models::where("default_model_id",$default_model_id)->where("user_id",$update_req->user_id)->pluck("id")->first();

            $inputData['model_id'] = $update_req->model_id;
            $inputData['user_id'] = $update_req->user_id;
            $inputData['default_model_id'] = $default_model_id;
            // Update the request with the modified input data
            $request->replace($inputData);
            $this->create_update_model($request);

            predefined_models_updates::where("id",$update_id)->delete();
            predefined_models_details_updates::where("model_id",$update_id)->delete();

            return redirect()->route("admin-models-update-requests");
        }
    }

    public function destroy($id)
    {
        $route = \Route::currentRouteName();
        
        if($route == "admin-model-update-request-delete")
        {
            $model = predefined_models_updates::where('id',$id)->first();
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

            $model = predefined_models_updates::where('id',$id)->whereIn("user_id",$related_users)->first();
        }

        if(!$model)
        {
            return redirect()->back();
        }

        $model->delete();

        predefined_models_details_updates::where('model_id',$id)->delete();
        Session::flash('success', 'Model update request deleted successfully.');
        
        return redirect()->back();
    }
}
