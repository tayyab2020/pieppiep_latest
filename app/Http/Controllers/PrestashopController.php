<?php

namespace App\Http\Controllers;

use App\Products;
use App\retailers_requests;
use Illuminate\Http\Request;
use App\User;
use App\Category;
use Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Language;
use App\Generalsetting;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Redirect;
use Crypt;
use App\users;
use File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use PDF;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Process\Process;
use View;
use Response;
use App\Jobs\ExportProductsPrestashop;
use App\retailer_mapped_categories;
use App\all_categories;
use App\organizations;

class PrestashopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:user');

        $this->middleware('auth');

        $this->gs = Generalsetting::where('backend',1)->first();
    }

    public function prestashopCredentials(Request $request)
    {
        $user = Auth::guard('user')->user();

        $request->validate([
            'prestashop_url' => ['regex:/^https?:\/\/.+$/']
        ],[
            'prestashop_url.regex' => 'The URL must be a valid URL starting with http:// or https://'
        ]);

        $user->organization->update(["prestashop_url" => $request->prestashop_url,"prestashop_access_key" => $request->prestashop_access_key]);
        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function categoriesMapping()
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

        $categories = all_categories::get();
        $mapped_categories = retailer_mapped_categories::whereIn("retailer_id",$related_users)->get();

        return view("user.mapped_categories",compact("categories","mapped_categories"));
    }

    public function storeMappedCategories(Request $request)
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

        foreach($request->category_ids as $i => $key)
        {
            $post = retailer_mapped_categories::where("cat_id",$key)->whereIn("retailer_id",$related_users)->first();

            if(!$post)
            {
                $post = new retailer_mapped_categories;
                $post->retailer_id = $user_id;
                $post->cat_id = $key;
            }

            $post->title = $request->mapped_category_titles[$i];
            $post->save();
        }

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function exportProductsPrestashop(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $prestashop_access_key = base64_encode($user->organization->prestashop_access_key);
        $prestashop_url = $user->organization->prestashop_url;

        if(!$prestashop_url && !$prestashop_access_key)
        {
            Session::flash('unsuccess', "Prestashop URL and Access Key is missing!");
            return redirect()->back();
        }
        else if(!$prestashop_url)
        {
            Session::flash('unsuccess', "Prestashop URL is missing!");
            return redirect()->back();
        }
        else if(!$prestashop_access_key)
        {
            Session::flash('unsuccess', "Prestashop Access Key is missing!");
            return redirect()->back();
        }

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $suppliers = $request->suppliers;
        $sub_categories = $request->sub_categories;

        if(!$suppliers)
        {
            Session::flash('unsuccess', __("text.Select at least one supplier."));
            return redirect()->back();
        }

        ExportProductsPrestashop::dispatch($prestashop_access_key,$prestashop_url,$suppliers,$user_id,$user,$organization_id,$organization,$related_users,$this->gs->site,$sub_categories);

        Session::flash('success', __("text.Products will be exported in background."));
        return redirect()->back();
    }
}



