<?php

namespace App\Http\Controllers;

use App\Brand;
use App\brand_edit_requests;
use App\Model1;
use App\Sociallink;
use App\type_edit_requests;
use App\User;
use App\vats;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreValidationRequest1;
use App\Http\Requests\UpdateValidationRequest;
use Auth;
use App\Generalsetting;
use Mollie\Laravel\Facades\Mollie;
use App\service_types;
use App\sub_services;
use App\handyman_products;
use App\carts;
use App\organizations;
use Illuminate\Database\Eloquent\SoftDeletes;

class BrandController extends Controller
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

        if($user->can('user-brands'))
        {
            $cats = Brand::where(function($query) use($related_users,$organization_id) {
                $query->whereIn('user_id',$related_users)->orWhere(function($query1) use($organization_id) {
                    $query1->whereRaw("find_in_set($organization_id,other_suppliers_organizations)")->where('trademark',0);
                });
            })->orderBy('id','desc')->get();

            return view('admin.brand.index',compact('cats','organization_id'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function otherSuppliersBrands()
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

        if($user->role_id == 4)
        {
            $brands = Brand::whereNotIn('user_id',$related_users)->where('trademark',0)->get();

            return view('user.supplier_brands',compact('brands','organization_id'));
        }
    }

    public function SupplierBrandsStore(Request $request)
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

        $brand_ids = $request->supplier_brands ? $request->supplier_brands : array();
        $brands = Brand::whereNotIn('user_id',$related_users)->get();

        foreach ($brands as $key)
        {
            $other_suppliers_organizations = $key->other_suppliers_organizations ? explode(',',$key->other_suppliers_organizations) : array();

            if(in_array($key->id,$brand_ids))
            {
                if(!in_array($organization_id,$other_suppliers_organizations))
                {
                    $other_suppliers_organizations[] = $organization_id;
                    $other_suppliers_organizations = implode(',',$other_suppliers_organizations);
                    $key->other_suppliers_organizations = $other_suppliers_organizations ? $other_suppliers_organizations : NULL;
                    $key->save();
                }
            }

            if(!$request->supplier_brands || !in_array($key->id,$brand_ids))
            {
                if (($index = array_search($organization_id, $other_suppliers_organizations)) !== false) {

                    unset($other_suppliers_organizations[$index]);
                    $other_suppliers_organizations = implode(',',$other_suppliers_organizations);
                    $key->other_suppliers_organizations = $other_suppliers_organizations ? $other_suppliers_organizations : NULL;
                    $key->save();

                    $brand_edit_request = brand_edit_requests::where('brand_id',$key->id)->where('user_id',$user_id)->first();

                    if($brand_edit_request)
                    {
                        if($brand_edit_request->photo != null){
                            \File::delete(public_path() .'/assets/images/'.$brand_edit_request->photo);
                        }

                        $brand_edit_request->delete();
                    }
                }
            }
        }

        Session::flash('success', 'List updated successfully!');

        return redirect()->back();
    }

    public function create()
    {
        $user = Auth::guard('user')->user();

        if($user->can('brand-create'))
        {
            return view('admin.brand.create');
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function CustomValidations($id,$related_users,$title,$slug)
    {
        if($id)
        {
            $check_name = Brand::where('id','!=',$id)->where('cat_name',$title)->whereIn('user_id',$related_users)->first();

            if($check_name)
            {
                Session::flash('unsuccess', 'Brand name already in use.');
                return redirect()->back()->withInput();
            }

            $check_slug = Brand::where('id','!=',$id)->where('cat_slug',$slug)->whereIn('user_id',$related_users)->first();

            if($check_slug)
            {
                Session::flash('unsuccess', 'Slug already in use.');
                return redirect()->back()->withInput();
            }

            $check_name1 = Brand::where('id','!=',$id)->where('cat_name',$title)->whereNotIn('user_id',$related_users)->first();

            if($check_name1)
            {
                Session::flash('unsuccess', 'Brand name is already taken, If you are allowed to use it send us a message.');
                return redirect()->back()->withInput();
            }

            $check_slug1 = Brand::where('id','!=',$id)->where('cat_slug',$slug)->whereNotIn('user_id',$related_users)->first();

            if($check_slug1)
            {
                Session::flash('unsuccess', 'Slug is already taken, If you are allowed to use it send us a message.');
                return redirect()->back()->withInput();
            }
        }
        else
        {
            $check_name = Brand::where('cat_name',$title)->whereIn('user_id',$related_users)->first();

            if($check_name)
            {
                Session::flash('unsuccess', 'Brand name already in use.');
                return redirect()->back()->withInput();
            }

            $check_slug = Brand::where('cat_slug',$slug)->whereIn('user_id',$related_users)->first();

            if($check_slug)
            {
                Session::flash('unsuccess', 'Slug already in use.');
                return redirect()->back()->withInput();
            }

            $check_name1 = Brand::where('cat_name',$title)->whereNotIn('user_id',$related_users)->first();

            if($check_name1)
            {
                Session::flash('unsuccess', 'Brand name is already taken, If you are allowed to use it send us a message.');
                return redirect()->back()->withInput();
            }

            $check_slug1 = Brand::where('cat_slug',$slug)->whereNotIn('user_id',$related_users)->first();

            if($check_slug1)
            {
                Session::flash('unsuccess', 'Slug is already taken, If you are allowed to use it send us a message.');
                return redirect()->back()->withInput();
            }
        }

        return NULL;
    }

    public function CustomValidationsTypes($id,$related_users,$title,$slug)
    {
        foreach ($title as $a => $key)
        {
            if(isset($id[$a]))
            {
                if($key && $slug[$a])
                {
                    $check_name = Model1::where('id','!=',$id[$a])->where('cat_name',$key)->whereIn('user_id',$related_users)->first();

                    if($check_name)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug = Model1::where('id','!=',$id[$a])->where('cat_slug',$slug[$a])->whereIn('user_id',$related_users)->first();

                    if($check_slug)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_name1 = Model1::where('id','!=',$id[$a])->where('cat_name',$key)->whereNotIn('user_id',$related_users)->first();

                    if($check_name1)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> is already taken, If you are allowed to use it send us a message.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug1 = Model1::where('id','!=',$id[$a])->where('cat_slug',$slug[$a])->whereNotIn('user_id',$related_users)->first();

                    if($check_slug1)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> is already taken, If you are allowed to use it send us a message.');
                        return redirect()->back()->withInput();
                    }
                }
            }
            else
            {
                if($key && $slug[$a])
                {
                    $check_name = Model1::where('cat_name',$key)->whereIn('user_id',$related_users)->first();

                    if($check_name)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug = Model1::where('cat_slug',$slug[$a])->whereIn('user_id',$related_users)->first();

                    if($check_slug)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_name1 = Model1::where('cat_name',$key)->whereNotIn('user_id',$related_users)->first();

                    if($check_name1)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> is already taken, If you are allowed to use it send us a message.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug1 = Model1::where('cat_slug',$slug[$a])->whereNotIn('user_id',$related_users)->first();

                    if($check_slug1)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> is already taken, If you are allowed to use it send us a message.');
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        return NULL;
    }

    public function store(StoreValidationRequest1 $request)
    {
        $user = Auth::guard('user')->user();
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user_id = $user->id;

        $validations = $this->CustomValidations($request->cat_id ? $request->cat_id : NULL,$related_users,$request->cat_name,$request->cat_slug);

        if($validations)
        {
            return $validations;
        }

        // $validations = $this->CustomValidationsTypes($request->cat_id ? $request->type_ids : NULL,$related_users,$request->types,$request->type_slugs);

        // if($validations)
        // {
        //     return $validations;
        // }

        if($request->cat_id)
        {
            $cat = Brand::where('id',$request->cat_id)->first();

            if($cat)
            {
                Session::flash('success', 'Brand edited successfully.');

                $type_ids = array();

                foreach ($request->types as $x => $temp)
                {
                    if($temp && $request->type_slugs[$x])
                    {
                        $type = Model1::where('id',$request->type_ids[$x])->first();

                        if(!$type)
                        {
                            $type = new Model1;
                        }

                        $type->user_id = $user_id;
                        $type->brand_id = $cat->id;
                        $type->cat_name = $temp;
                        $type->cat_slug = $request->type_slugs[$x];
                        $type->description = $request->type_descriptions[$x];
                        $type->save();

                        $type_ids[] = $type->id;
                    }
                }

                $types_delete = Model1::whereNotIn('id',$type_ids)->where('brand_id',$cat->id)->whereIn('user_id',$related_users)->get();

                foreach ($types_delete as $del)
                {
                    if($del->photo != null)
                    {
                        \File::delete(public_path() .'/assets/images/'.$del->photo);
                    }

                    $type_edit_requests = type_edit_requests::where('type_id',$del->id)->get();

                    foreach ($type_edit_requests as $e_del)
                    {
                        if($e_del->photo != null)
                        {
                            \File::delete(public_path() .'/assets/images/'.$e_del->photo);
                        }

                        $e_del->delete();
                    }

                    $del->delete();
                }
            }
            else
            {
                $check = brand_edit_requests::where('brand_id',$request->cat_id)->whereIn('user_id',$related_users)->first();

                if(!$check)
                {
                    $check = new brand_edit_requests;
                    $check->user_id = $user_id;
                    $check->brand_id = $request->cat_id;

                    Session::flash('success', 'Brand edit request has been created successfully.');
                }
                else
                {
                    Session::flash('success', 'Brand edit request has been updated successfully.');
                }

                if($file = $request->file('photo'))
                {
                    $name = time().$file->getClientOriginalName();
                    $file->move('assets/images',$name);
                    if($check->photo != null)
                    {
                        \File::delete(public_path() .'/assets/images/'.$check->photo);
                    }
                    $check->photo = $name;
                }

                $check->cat_name = $request->cat_name;
                $check->cat_slug = $request->cat_slug;
                $check->description = $request->description;
                $check->save();

                foreach ($request->types as $t => $key)
                {
                    $type_id = $request->type_ids[$t] ? $request->type_ids[$t] : 0;

                    if($key && $request->type_slugs[$t])
                    {
                        $check_type = type_edit_requests::where('brand_id',$request->cat_id)->whereIn('user_id',$related_users)->skip($t)->first();

                        if($request->removed_rows[$t])
                        {
                            if($check_type)
                            {
                                $check_type->delete_row = 1;
                                $check_type->save();
                            }
                        }
                        else
                        {
                            if(!$check_type)
                            {
                                $check_type = new type_edit_requests;
                                $check_type->user_id = $user_id;
                                $check_type->brand_id = $request->cat_id;
                            }

                            $check_type->type_id = $type_id;
                            $check_type->cat_name = $key;
                            $check_type->cat_slug = $request->type_slugs[$t];
                            $check_type->description = $request->type_descriptions[$t];
                            $check_type->delete_row = 0;
                            $check_type->save();
                        }
                    }
                }

                $admin_email = $this->sl->admin_email;
                $supplier_company = $user->company_name;
                $brand = Brand::where('id',$request->cat_id)->pluck('cat_name')->first();

                \Mail::send(array(), array(), function ($message) use ($admin_email,$supplier_company,$brand) {
                    $message->to($admin_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                        ->subject('Brand Edit Request')
                        ->html('Dear Nordin Adoui, A new brand edit request has been submitted by <b>'.$supplier_company.'</b> for brand: <b>'.$brand.'</b>.', 'text/html');
                });

                return redirect()->route('admin-brand-index');
            }
        }
        else
        {
            $cat = new Brand;
            Session::flash('success', 'New Brand added successfully.');
        }

        $input = $request->all();
        $input['user_id'] = $user_id;

        if($file = $request->file('photo'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images',$name);
            if($cat->photo != null)
            {
                \File::delete(public_path() .'/assets/images/'.$cat->photo);
            }
            $input['photo'] = $name;
        }

        $cat->fill($input)->save();

        if(!$request->cat_id)
        {
            foreach ($request->types as $s => $key)
            {
                if($key && $request->type_slugs[$s])
                {
                    $type = new Model1;
                    $type->user_id = $user_id;
                    $type->brand_id = $cat->id;
                    $type->cat_name = $key;
                    $type->cat_slug = $request->type_slugs[$s];
                    $type->description = $request->type_descriptions[$s];
                    $type->save();
                }
            }
        }

        return redirect()->route('admin-brand-index');
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

        if($user->can('brand-edit'))
        {
            $cats = Brand::where('id','=',$id)->first();

            if(!$cats)
            {
                return redirect()->back();
            }

            $brand_edit_request = brand_edit_requests::where('brand_id',$cats->id)->whereIn('user_id',$related_users)->first();
            $type_edit_requests = type_edit_requests::where('brand_id',$cats->id)->whereIn('user_id',$related_users)->where('delete_row',0)->get();
            $types = Model1::where('brand_id',$cats->id)->get();

            return view('admin.brand.create',compact('cats','organization_id','types','brand_edit_request','type_edit_requests'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function update(UpdateValidationRequest $request, $id)
    {
        $vat = vats::where('id',$request->vat)->first();

        if(!$request->main_service)
        {
            $i =0;

            foreach ($request->sub_service as $key) {

                if($request->s_id[$i] != 0)
                {
                    $update = sub_services::where('id',$request->s_id[$i])->update(['cat_id'=>$key]);
                }
                else
                {
                    $sub_services = new sub_services;
                    $sub_services->cat_id = $key;
                    $sub_services->sub_id = $id;
                    $sub_services->save();

                }

                $i++;
            }
        }

        if($request->variable_questions)
        {
            $request['variable_questions'] = 1;
        }
        else
        {
            $request['variable_questions'] = 0;
        }

        $cat = Category::findOrFail($id);

        $input = $request->all();

        $input['vat_id'] = $vat->id;
        $input['vat_percentage'] = $vat->vat_percentage;
        $input['vat_rule'] = $vat->rule;
        $input['vat_code'] = $vat->code;

        if ($file = $request->file('photo'))
        {
            $name = time().$file->getClientOriginalName();
            $file->move('assets/images',$name);
            if($cat->photo != null)
            {
                \File::delete(public_path() .'/assets/images/'.$cat->photo);
            }
            $input['photo'] = $name;
        }

        $cat->update($input);
        Session::flash('success', 'Service updated successfully.');
        return redirect()->route('admin-cat-index');
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

        if($user->can('brand-delete'))
        {
            $cat = Brand::where('id',$id)->whereIn('user_id',$related_users)->first();

            if(!$cat)
            {
                $other_supplier_brand = Brand::where('id',$id)->first();

                if(!$other_supplier_brand)
                {
                    return redirect()->back();
                }

                $suppliers_organizations = explode(',',$other_supplier_brand->other_suppliers_organizations);

                if (($index = array_search($organization_id, $suppliers_organizations)) !== false) {

                    unset($suppliers_organizations[$index]);
                    $other_suppliers_organizations = implode(',',$suppliers_organizations);
                    $other_supplier_brand->other_suppliers_organizations = $other_suppliers_organizations ? $other_suppliers_organizations : NULL;
                    $other_supplier_brand->save();

                    $brand_edit_request = brand_edit_requests::where('brand_id',$other_supplier_brand->id)->whereIn('user_id',$related_users)->first();

                    if($brand_edit_request)
                    {
                        if($brand_edit_request->photo != null){
                            \File::delete(public_path() .'/assets/images/'.$brand_edit_request->photo);
                        }

                        $brand_edit_request->delete();
                    }

                    $type_edit_request = type_edit_requests::where('brand_id',$other_supplier_brand->id)->whereIn('user_id',$related_users)->first();

                    if($type_edit_request)
                    {
                        if($type_edit_request->photo != null){
                            \File::delete(public_path() .'/assets/images/'.$type_edit_request->photo);
                        }

                        $type_edit_request->delete();
                    }
                }
                else
                {
                    return redirect()->back();
                }
            }
            else
            {
                if($cat->other_suppliers_organizations)
                {
                    $cat->user_id = 0;
                    $cat->trademark = 0;
                    $cat->save();
                }
                else
                {
                    if($cat->photo != null){
                        \File::delete(public_path() .'/assets/images/'.$cat->photo);
                    }

                    $cat->delete();
                    Model1::where('brand_id',$id)->delete();
                    brand_edit_requests::where('brand_id',$id)->delete();
                    type_edit_requests::where('brand_id',$id)->delete();
                }
            }

            Session::flash('success', 'Brand deleted successfully.');
            return redirect()->route('admin-brand-index');
        }
        else
        {
            return redirect()->route('user-login');
        }
    }
}
