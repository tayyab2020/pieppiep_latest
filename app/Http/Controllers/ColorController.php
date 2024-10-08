<?php

namespace App\Http\Controllers;

use App\Brand;
use App\colors;
use App\estimated_prices;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Model1;
use App\product;
use App\Products;
use App\vats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Category;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\StoreValidationRequest5;
use Auth;
use App\Generalsetting;
use Mollie\Laravel\Facades\Mollie;
use App\service_types;
use App\sub_services;
use App\handyman_products;
use App\carts;
use App\organizations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Excel;

class ColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');
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
        // $organization = organizations::findOrFail($organization_id);
        // $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('user-colors'))
        {
            $colors = colors::leftjoin('products','products.id','=','colors.product_id')->leftjoin('price_tables','price_tables.id','=','colors.table_id')->where('products.organization_id',$organization_id)->orderBy('colors.id','desc')->select('colors.*','products.title as product','price_tables.title as table')->get();

            return view('admin.color.index',compact('colors'));
        }
        else
        {
            return redirect()->route('user-login');
        }
    }

    public function create()
    {
        /*return view('admin.color.create');*/
    }


    public function store(StoreValidationRequest5 $request)
    {
        if($request->id)
        {
            colors::where('id',$request->id)->update(['title' => $request->title, 'color_code' => $request->color_code]);

            Session::flash('success', 'Color edited successfully.');
        }
        else
        {
            $color = new colors;
            $color->title = $request->title;
            $color->color_code = $request->color_code;
            $color->save();

            Session::flash('success', 'Color created successfully.');
        }

        return redirect()->route('admin-color-index');
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
        // $organization = organizations::findOrFail($organization_id);
        // $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('color-edit'))
        {
            $color = colors::leftjoin('products','products.id','=','colors.product_id')->leftjoin('price_tables','price_tables.id','=','colors.table_id')->where('colors.id','=',$id)->where('products.organization_id',$organization_id)->orderBy('colors.id','desc')->select('colors.*','products.title as product','price_tables.title as table')->first();

            if(!$color)
            {
                return redirect()->back();
            }

            return view('admin.color.create',compact('color'));
        }
        else
        {
            return redirect()->route('user-login');
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
        // $organization = organizations::findOrFail($organization_id);
        // $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if($user->can('color-delete'))
        {
            $color = colors::leftjoin('products','products.id','=','colors.product_id')->where('colors.id','=',$id)->where('products.organization_id',$organization_id)->first();

            if(!$color)
            {
                return redirect()->back();
            }

            $color->delete();


            Session::flash('success', 'Color deleted successfully.');
            return redirect()->route('admin-color-index');
        }
        else
        {
            return redirect()->route('user-login');
        }
    }
}
