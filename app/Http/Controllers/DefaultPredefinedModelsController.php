<?php

namespace App\Http\Controllers;

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
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\predefined_models;
use App\predefined_models_details;

class DefaultPredefinedModelsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $models = default_predefined_models::orderBy('id','desc')->get();

        $categories = Category::get();

        return view('admin.default_predefined_models.index',compact('models','categories'));
    }

    public function create()
    {
        $cats = Category::get();

        return view('admin.default_predefined_models.create',compact('cats'));
    }

    public function store(Request $request,$type = 0)
    {
        if($type == 0)
        {
            $check_model = default_predefined_models::where("model",$request->title);

            if($request->heading_id)
            {
                $check_model = $check_model->where("id","!=",$request->heading_id);
            }
    
            $check_model = $check_model->first();
    
            if($check_model)
            {
                Session::flash('unsuccess', 'Model already exists in default models list.');
                return redirect()->back();
            }
        }

        $category_ids = implode(",",$request->model_category);

        if($request->heading_id)
        {
            $model = default_predefined_models::where('id',$request->heading_id)->first();
            Session::flash('success', 'Model updated successfully.');
        }
        else
        {
            $model = new default_predefined_models;
            Session::flash('success', 'New Model added successfully.');
        }

        if($type == 0 || !$request->heading_id)
        {
            $model->model = $request->title;
            $model->category_ids = $category_ids;
            $model->save();
        }

        $default_model_id = $model->id;

        $sizes = $request->sizes;
        $size_ids = $request->size_ids;
        $id_array = [];

        foreach($sizes as $x => $key)
        {
            $size_check = default_predefined_models_details::where('id',$size_ids[$x])->first();

            if($size_check)
            {
                if($key)
                {
                    $size_check->model = $key;

                    if($type == 0)
                    {
                        $size_check->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                        $size_check->measure = $request->size_measure[$x];
                        $size_check->price_impact = $request->price_impact[$x] == 1 ? 1 : 0;
                        $size_check->impact_type = $request->impact_type[$x];
                        $size_check->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                        $size_check->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                        $size_check->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                        $size_check->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                    }
                    
                    $size_check->save();

                    if($type == 0)
                    {
                        $suppliers_models = predefined_models::where("default_model_id",$default_model_id)->get();

                        foreach($suppliers_models as $spm)
                        {
                            $supplier_model_details = predefined_models_details::where("default_model_detail_id",$size_check->id)->where("model_id",$spm->id)->first();

                            if(!$supplier_model_details) //if size was not created for this supplier model than will create it first with default values
                            {
                                $supplier_model_details = new predefined_models_details;
                                $supplier_model_details->default_model_detail_id = $size_check->id;
                                $supplier_model_details->model_id = $spm->id;
                                $supplier_model_details->model = $key;
                                $supplier_model_details->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                                $supplier_model_details->measure = $request->size_measure[$x];
                                $supplier_model_details->price_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                                $supplier_model_details->impact_type = $request->impact_type[$x];
                                $supplier_model_details->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                                $supplier_model_details->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                                $supplier_model_details->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                                $supplier_model_details->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                                $supplier_model_details->save();
                            }
                        }
                    }
                }

                $id_array[] = $size_check->id;
            }
            else
            {
                if($key)
                {
                    $details = new default_predefined_models_details;
                    $details->model_id = $model->id;
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

                    if($type == 0)
                    {
                        $suppliers_models = predefined_models::where("default_model_id",$default_model_id)->get();

                        foreach($suppliers_models as $spm) //create new size with default values for all suppliers models
                        {
                            $supplier_model_details = new predefined_models_details;
                            $supplier_model_details->default_model_detail_id = $details->id;
                            $supplier_model_details->model_id = $spm->id;
                            $supplier_model_details->model = $key;
                            $supplier_model_details->value = $request->size_values[$x] ? $request->size_values[$x] : 0;
                            $supplier_model_details->measure = $request->size_measure[$x];
                            $supplier_model_details->price_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                            $supplier_model_details->impact_type = $request->impact_type[$x];
                            $supplier_model_details->m1_impact = $request->price_impact[$x] == 2 ? 1 : 0;
                            $supplier_model_details->m2_impact = $request->price_impact[$x] == 3 ? 1 : 0;
                            $supplier_model_details->factor = $request->price_impact[$x] == 4 ? 1 : 0;
                            $supplier_model_details->factor_value = $request->size_factor_values[$x] ? $request->size_factor_values[$x] : 0;
                            $supplier_model_details->save();
                        }
                    }

                    $id_array[] = $details->id;
                }
            }

        }

        $default_models_details_delete = default_predefined_models_details::whereNotIn('id',$id_array)->where('model_id',$default_model_id)->get();

        foreach($default_models_details_delete as $delete)
        {
            predefined_models_details::where("default_model_detail_id",$delete->id)->delete();
        }

        default_predefined_models_details::whereNotIn('id',$id_array)->where('model_id',$default_model_id)->delete();

        if($type == 0)
        {
            return redirect()->route('default-models-index');
        }
        else
        {
            return $default_model_id;
        }
    }

    public function edit($id)
    {
        $model = default_predefined_models::where('id',$id)->first();

        if(!$model)
        {
            return redirect()->back();
        }

        $cats = Category::get();
        $models_data = default_predefined_models_details::where('model_id',$model->id)->get();

        return view('admin.default_predefined_models.create',compact('model','models_data','cats'));
    }

    public function destroy($id)
    {
        $check_linking = predefined_models::where("default_model_id",$id)->first();

        if($check_linking)
        {
            Session::flash('unsuccess', 'This model has been linked with supplier(s) models.');
            return redirect()->back();
        }

        $model = default_predefined_models::where('id',$id)->first();

        if(!$model)
        {
            return redirect()->back();
        }

        $model->delete();

        default_predefined_models_details::where('model_id',$id)->delete();

        Session::flash('success', 'Model deleted successfully.');
        return redirect()->route('default-models-index');

    }
}
