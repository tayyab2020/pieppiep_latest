<?php

namespace App\Providers;

use App\Category;
use App\handyman_quotes;
use App\pages;
use App\Products;
use App\Service;
use App\documents;
use App\Generalsetting;
use App\Blog;
use App\Sociallink;
use App\Seotool;
use App\Pagesetting;
use App\Language;
use App\Advertise;
use App\user_languages;
use Auth;
use App\how_it_works;
use App\reasons_to_book;
use App\retailers_requests;
use App\notes;
use App\notes_tags;
use App\tasks;
use App\customers_details;
use App\User;
use App\organizations;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    protected $language; // Add this line

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }

        //whether ip is from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        //whether ip is from remote address
        else
        {
            $ip_address = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
        }

        $language = user_languages::where('ip','=',$ip_address)->first();

        if($language == '')
        {
            $language = new user_languages;
            $language->ip = $ip_address;
            $language->lang = 'du';
            $language->save();

            \App::setLocale('du');
        }
        else
        {
            if($language->lang == 'du')
            {
                \App::setLocale('du');
            }
            else
            {
                \App::setLocale('en');
            }
        }

        $this->language = $language;

        view()->composer('*',function($settings){

            if (Auth::check()) {

                $user = Auth::guard('user')->user();

                if($user)
                {
                    $user_id = $user->id;
                    $user_role = $user->role_id;

                    if($user_role != 3)
                    {
                        $organization_id = $user->organization->id;
                        $organization = organizations::findOrFail($organization_id);
                        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');
                    }

                    $allData = $this->getData();
                    $settings->with('allData', $allData);

                    if($user_role == 2)
                    {
                        $no_requests = handyman_quotes::leftjoin('quotes', 'quotes.id', '=', 'handyman_quotes.quote_id')->where('handyman_quotes.handyman_id',$organization_id)->where('quotes.status',0)->get();
                        $settings->with('no_requests', $no_requests);
                    }

                    if($user_role == 4)
                    {
                        $no_retailers = retailers_requests::where('supplier_organization', $organization_id)->where('status',0)->get();
                        $settings->with('no_retailers', $no_retailers);

                        // $main_id = $user->main_id;

                        // if($main_id)
                        // {
                        //     $user_id = $main_id;
                        // }

                        $is_floor = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where(function($query) {
                            $query->where('categories.cat_name','LIKE', '%Floors%')->orWhere('categories.cat_name','LIKE', '%Vloeren%');
                        })->select('categories.cat_name')->first();

                        $is_blind = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->where('supplier_categories.organization_id',$organization_id)->where(function($query) {
                            $query->where('categories.cat_name','LIKE', '%Blinds%')->orWhere('categories.cat_name','LIKE', '%Binnen zonwering%');
                        })->select('categories.cat_name')->first();

                        $supplier_global_categories = array($is_floor,$is_blind);

                        $settings->with('supplier_global_categories', $supplier_global_categories);
                    }

                    $settings->with('currentUser', Auth::user());
                }

            }else {
                $settings->with('currentUser', null);
            }

            // if($settings->currentUser == '')
            // {
            //     $settings->with('gs', Generalsetting::where('backend',0)->first());
            // }
            // else {
            //     $settings->with('gs', Generalsetting::where('backend',1)->first());
            // }

            $settings->with('gs', Generalsetting::where('backend',1)->first()); //pieppiep
            $settings->with('gs1', Generalsetting::where('backend',0)->first()); //vloerofferte

            if (in_array(\Route::currentRouteName(), ['front.index', 'front.products', 'front.product', 'front.services', 'front.service']))
            {
                $quote_cats = Category::get();
                $quote_products = Products::leftjoin('categories','categories.id','=','products.category_id')->select('products.id','products.title','categories.cat_name')->get();
                $quote_services = Service::all();
                $quote_data = documents::where("role",2)->where('document_type',1)->first();

                $settings->with('quote_cats', $quote_cats);
                $settings->with('quote_products', $quote_products);
                $settings->with('quote_services', $quote_services);
                $settings->with('quote_data', $quote_data);
            }

            $settings->with('menu', pages::orderBy('order_no','ASC')->get());
            $settings->with('sl', Sociallink::find(1));
            $settings->with('seo', Seotool::find(1));
            $settings->with('ps', Pagesetting::find(1));
            $settings->with('lang', Language::where('lang','=',$this->language->lang)->first());
            $settings->with('hiw', how_it_works::findOrFail(1));
            $settings->with('rtb', reasons_to_book::findOrFail(1));
            $settings->with('lblogs', Blog::orderBy('created_at', 'desc')->limit(4)->get());
            $settings->with('ad728x90', Advertise::inRandomOrder()->where('size','728x90')->where('status',1)->first());
            $settings->with('ad300x250', Advertise::inRandomOrder()->where('size','300x250')->where('status',1)->first());
        });
    }

    public function getData()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;
        
        $notes = notes::leftjoin("notes_tags","notes_tags.id","=","notes.tag")->leftjoin("users as t1","t1.id","=","notes.employee_id")->leftjoin("users as t2","t2.id","=","notes.supplier_id")->leftjoin("customers_details","customers_details.id","=","notes.customer_id");

        $customers = "";
        $suppliers = "";
        $employees = "";

        if($user_role != 3)
        {
            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $notes = $notes->whereIn('notes.user_id',$related_users);
            $tags = notes_tags::whereIn('notes_tags.user_id',$related_users)->get();
    
            if($user_role == 2)
            {
                $customers = customers_details::leftjoin("users","users.id","=","customers_details.user_id")->whereIn('customers_details.retailer_id',$related_users)->select("customers_details.*","users.email","users.fake_email")->get();
    
                $suppliers = User::leftjoin("user_organizations","user_organizations.user_id","=","users.id")
                ->leftjoin("retailers_requests","retailers_requests.supplier_organization","=","user_organizations.organization_id")
                ->where("retailers_requests.retailer_organization",$organization_id)->where('users.role_id','=',4)->orderBy('users.created_at','desc')->select('users.*')->get();
    
                $employees = User::where('role_id',2)->where('id','!=',$user->id)->whereIn('id',$related_users)->get();
            }
            else if($user_role == 4)
            {
                $employees = User::where('role_id',4)->where('id','!=',$user->id)->whereIn('id',$related_users)->get();
            }
        }
        else
        {
            $notes = $notes->where('notes.user_id',$user_id);
            $tags = notes_tags::where('notes_tags.user_id',$user_id)->get();
        }

        $notes = $notes->orderBy("notes.id","Desc")->select("notes.*","t1.name as employee_fname","t1.family_name as employee_lname","t2.name as supplier_fname","t2.family_name as supplier_lname","customers_details.name as customer_fname","customers_details.family_name as customer_lname","notes_tags.id as tag_id","notes_tags.title as tag_title","notes_tags.background")->get();
        
        // $notes = notes::leftJoin("notes_tags", "notes_tags.id", "=", "notes.tag")
        // ->leftJoin("users as t1", "t1.id", "=", "notes.employee_id")
        // ->leftJoin("users as t2", "t2.id", "=", "notes.supplier_id")
        // ->leftJoin("customers_details", "customers_details.id", "=", "notes.customer_id")
        // ->leftJoin("users", "users.id", "=", "notes.user_id") // Join users table again
        // ->where(function($query) use ($user_id, $main_id) {
        //     $query->where("notes.user_id", $user_id)
        //         ->orWhere("users.main_id", $main_id); // Include users with the same main_id
        //     })
        //     ->orderBy("notes.id", "DESC")
        //     ->select("notes.*", "t1.name as employee_fname", "t1.family_name as employee_lname", "t2.name as supplier_fname", "t2.family_name as supplier_lname", "customers_details.name as customer_fname", "customers_details.family_name as customer_lname", "notes_tags.id as tag_id", "notes_tags.title as tag_title", "notes_tags.background")
        //     ->get();

        // $tags = notes_tags::where(function($query) use ($user_id, $main_id) {
        //         $query->where("user_id", $user_id)
        //               ->orWhereHas('user', function($query) use ($main_id) {
        //                   $query->where('main_id', $main_id);
        //               });
        //         })
        //         ->get();

        $task_groups = tasks::where("user_id",$user_id)->orderBy('date', 'desc')->orderBy('id', 'desc')->get()->groupBy('date');

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        return [$notes,$tags,$customers,$suppliers,$employees,$task_groups];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
