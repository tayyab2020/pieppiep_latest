<?php

namespace App\Http\Controllers;

use App\Brand;
use App\brand_edit_requests;
use App\Http\Requests\StoreValidationRequest7;
use App\Model1;
use App\type_edit_requests;
use App\vats;
use Illuminate\Http\Request;
use App\Category;
use App\sub_categories;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreValidationRequest;
use App\Http\Requests\UpdateValidationRequest;
use Auth;
use App\Generalsetting;
use Mollie\Laravel\Facades\Mollie;
use App\service_types;
use App\sub_services;
use App\handyman_products;
use App\carts;
use App\features;
use App\supplier_categories;
use App\User;
use App\organizations;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyBrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $brands = Brand::orderBy('brands.id','desc')->with('brand_edit_requests')->paginate(10);

        return view('admin.brand.my_brands_index',compact('brands'));
    }

    public function create()
    {
        $suppliers = User::where('role_id',4)->pluck('id')->toArray();

        $organizations = organizations::whereHas('users', function ($query) use ($suppliers) {
            $query->whereIn('users.id', $suppliers);
        })->get(); //The whereHas method ensures that only distinct organizations that have at least one user with role_id 4 are retrieved.

        return view('admin.brand.create_my_brand',compact('organizations'));
    }

    public function CustomValidations($id,$title,$slug)
    {
        if($id)
        {
            $check_name = Brand::where('id','!=',$id)->where('cat_name',$title)->first();

            if($check_name)
            {
                Session::flash('unsuccess', 'Brand name already in use.');
                return redirect()->back()->withInput();
            }

            $check_slug = Brand::where('id','!=',$id)->where('cat_slug',$slug)->first();

            if($check_slug)
            {
                Session::flash('unsuccess', 'Slug already in use.');
                return redirect()->back()->withInput();
            }
        }
        else
        {
            $check_name = Brand::where('cat_name',$title)->first();

            if($check_name)
            {
                Session::flash('unsuccess', 'Brand name already in use.');
                return redirect()->back()->withInput();
            }

            $check_slug = Brand::where('cat_slug',$slug)->first();

            if($check_slug)
            {
                Session::flash('unsuccess', 'Slug already in use.');
                return redirect()->back()->withInput();
            }
        }

        return NULL;
    }

    public function CustomValidationsTypes($id,$title,$slug)
    {
        foreach ($title as $a => $key)
        {
            if(isset($id[$a]))
            {
                if($key && $slug[$a])
                {
                    $check_name = Model1::where('id','!=',$id[$a])->where('cat_name',$key)->first();

                    if($check_name)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug = Model1::where('id','!=',$id[$a])->where('cat_slug',$slug[$a])->first();

                    if($check_slug)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> already in use.');
                        return redirect()->back()->withInput();
                    }
                }
            }
            else
            {
                if($key && $slug[$a])
                {
                    $check_name = Model1::where('cat_name',$key)->first();

                    if($check_name)
                    {
                        Session::flash('unsuccess', 'Type title: <b>'.$key.'</b> already in use.');
                        return redirect()->back()->withInput();
                    }

                    $check_slug = Model1::where('cat_slug',$slug[$a])->first();

                    if($check_slug)
                    {
                        Session::flash('unsuccess', 'Slug: <b>'.$slug[$a].'</b> already in use.');
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        return NULL;
    }

    public function store(StoreValidationRequest7 $request)
    {
        $validations = $this->CustomValidations($request->brand_id ? $request->brand_id : NULL,$request->cat_name,$request->cat_slug);

        if($validations)
        {
            return $validations;
        }

        // $validations = $this->CustomValidationsTypes($request->brand_id ? $request->type_ids : NULL,$request->types,$request->type_slugs);

        // if($validations)
        // {
        //     return $validations;
        // }

        if($request->edit_request_id)
        {
            $post = Brand::where('id',$request->brand_id)->first();
            $post->cat_name = $request->edit_title;
            $post->cat_slug = $request->edit_slug;
            $post->description = $request->edit_description;

            if($request->temp_edit_photo || $request->edit_photo)
            {
                \File::delete(public_path() .'/assets/images/'.$post->photo);

                if($file = $request->file('edit_photo'))
                {
                    $name = time().$file->getClientOriginalName();
                    $file->move('assets/images',$name);
                    $post->photo = $name;
                }
                else
                {
                    $post->photo = $request->temp_edit_photo;
                }
            }

            $post->save();

            foreach($request->types as $t => $key)
            {
                if($key && $request->type_slugs[$t])
                {
                    if($request->removed_rows[$t])
                    {
                        Model1::where('id',$request->type_ids[$t])->delete();
                    }
                    else
                    {
                        $post_type = Model1::where('id',$request->type_ids[$t])->first();

                        if(!$post_type)
                        {
                            $post_type = new Model1;
                        }

                        $post_type->user_id = $post->user_id;
                        $post_type->brand_id = $request->brand_id;
                        $post_type->cat_name = $key;
                        $post_type->cat_slug = $request->type_slugs[$t];
                        $post_type->description = $request->type_descriptions[$t];
                        $post_type->save();
                    }
                }
            }

            brand_edit_requests::where('id',$request->edit_request_id)->delete();
            type_edit_requests::where('brand_id',$request->brand_id)->where('user_id',$request->request_supplier_id)->delete();
            Session::flash('success', 'Task completed successfully.');
        }
        else
        {
            if($request->brand_id)
            {
                $check_brand_request = brand_edit_requests::where('brand_id',$request->brand_id)->first();

                if($check_brand_request)
                {
                    Session::flash('unsuccess', 'Edit request(s) are pending for this brand. Action required!');
                    return redirect()->back();
                }

                $check_types_request = type_edit_requests::where('brand_id',$request->brand_id)->first();

                if($check_types_request)
                {
                    Session::flash('unsuccess', 'Edit request(s) for type(s) of this brand are pending. Action required!');
                    return redirect()->back();
                }

                $cat = Brand::where('id',$request->brand_id)->first();
                $supplier_id = $cat->user_id;

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

                        $type->user_id = $supplier_id;
                        $type->brand_id = $cat->id;
                        $type->cat_name = $temp;
                        $type->cat_slug = $request->type_slugs[$x];
                        $type->description = $request->type_descriptions[$x];
                        $type->save();

                        $type_ids[] = $type->id;
                    }
                }

                $types_delete = Model1::whereNotIn('id',$type_ids)->where('brand_id',$cat->id)->get();

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
                $cat = new Brand();
                $supplier_id = 0;
                Session::flash('success', 'New Brand added successfully.');
            }

            $input = $request->all();
            $input['user_id'] = $supplier_id;
            $input['other_suppliers_organizations'] = isset($input['other_suppliers_organizations']) ? implode(',',$input['other_suppliers_organizations']) : NULL;
            $other_suppliers_organizations_array = $request->other_suppliers_organizations ? $request->other_suppliers_organizations : array();

            $organizations = organizations::whereIn('id', $other_suppliers_organizations_array)->get();

            $related_users = [];
            
            // Step 2: Retrieve related users for each organization
            foreach ($organizations as $organization) {
                $users = $organization->users()->withTrashed()->pluck('users.id')->toArray();
                $related_users = array_merge($related_users, $users);
            }

            $brand_edit_requests = brand_edit_requests::where('brand_id',$request->brand_id)->whereNotIn('user_id',$related_users)->get();

            foreach ($brand_edit_requests as $key)
            {
                if($key->photo != null)
                {
                    \File::delete(public_path() .'/assets/images/'.$key->photo);
                }

                $key->delete();
            }

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

            if(!$request->brand_id)
            {
                foreach ($request->types as $s => $key)
                {
                    if($key && $request->type_slugs[$s])
                    {
                        $type = new Model1;
                        $type->user_id = 0;
                        $type->brand_id = $cat->id;
                        $type->cat_name = $key;
                        $type->cat_slug = $request->type_slugs[$s];
                        $type->description = $request->type_descriptions[$s];
                        $type->save();
                    }
                }
            }
        }

        return redirect()->route('admin-my-brand-index');
    }

    public function edit($id)
    {
        $brand = Brand::where('id',$id)->first();

        if(!$brand)
        {
            return redirect()->back();
        }

        if($brand->user_id)
        {
            $organization_id = $brand->user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');
            $suppliers = User::where('role_id',4)->whereNotIn('id',$related_users)->pluck('id')->toArray();
        }
        else
        {
            $suppliers = User::where('role_id',4)->pluck('id')->toArray();
        }

        $organizations = organizations::whereHas('users', function ($query) use ($suppliers) {
            $query->whereIn('users.id', $suppliers);
        })->get(); //The whereHas method ensures that only distinct organizations that have at least one user with role_id 4 are retrieved.

        if($brand->other_suppliers_organizations)
        {
            $supplier_organization_ids = explode(',',$brand->other_suppliers_organizations);
        }
        else
        {
            $supplier_organization_ids = array();
        }

        $types = Model1::where('brand_id',$id)->get();

        return view('admin.brand.create_my_brand',compact('brand','supplier_organization_ids','organizations','types'));
    }

    public function editRequests($id)
    {
        $requests = brand_edit_requests::leftjoin('users','users.id','=','brand_edit_requests.user_id')
        ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
        ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
        ->where('brand_edit_requests.brand_id',$id)->select('brand_edit_requests.*','organizations.company_name')->get();

        return view('admin.brand.edit_requests',compact('requests'));
    }

    public function editRequest($id)
    {
        $brand = brand::leftjoin("brand_edit_requests","brand_edit_requests.brand_id","=","brands.id")
        ->where('brand_edit_requests.id',$id)
        ->select('brands.*','brand_edit_requests.id as edit_request_id','brand_edit_requests.user_id as request_supplier_id','brand_edit_requests.cat_name as edit_title','brand_edit_requests.cat_slug as edit_slug','brand_edit_requests.photo as edit_photo','brand_edit_requests.description as edit_description')->first();

        $type_edit_requests = type_edit_requests::where('brand_id',$brand->id)->where('user_id',$brand->request_supplier_id)->get();
        $types = Model1::where('brand_id',$brand->id)->get();

        if($brand->user_id)
        {
            $organization_id = $brand->user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');
            $suppliers = User::where('role_id',4)->whereNotIn('id',$related_users)->pluck('id')->toArray();
        }
        else
        {
            $suppliers = User::where('role_id',4)->pluck('id')->toArray();
        }

        $organizations = organizations::whereHas('users', function ($query) use ($suppliers) {
            $query->whereIn('users.id', $suppliers);
        })->get(); //The whereHas method ensures that only distinct organizations that have at least one user with role_id 4 are retrieved.

        if($brand->other_suppliers_organizations)
        {
            $supplier_organization_ids = explode(',',$brand->other_suppliers_organizations);
        }
        else
        {
            $supplier_organization_ids = array();
        }

        return view('admin.brand.create_my_brand',compact('brand','organizations','supplier_organization_ids','type_edit_requests','types'));
    }

    public function deleteEditRequest($id)
    {
        $user_id = brand_edit_requests::where('id',$id)->pluck('user_id')->first();

        brand_edit_requests::where('id',$id)->delete();
        type_edit_requests::where('brand_id',$id)->where('user_id',$user_id)->delete();

        Session::flash('success', 'Request deleted successfully.');

        return redirect()->route('admin-my-brand-index');
    }

    public function destroy($id)
    {
        $cat = Brand::where('id',$id)->first();

        if(!$cat)
        {
            return redirect()->back();
        }

        if($cat->other_suppliers_organizations)
        {
            $cat->user_id = 0;
            $cat->save();

            Model1::where('brand_id',$id)->update(['user_id' => 0]);

            Session::flash('success', 'Brand is used by other suppliers. So brand is now under admin access only but cant be deleted.');
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

            Session::flash('success', 'Brand deleted successfully.');
        }

        return redirect()->back();
    }

}
