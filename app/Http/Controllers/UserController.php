<?php

namespace App\Http\Controllers;

use App\Brand;
use App\color;
use App\colors;
use App\custom_quotations;
use App\custom_quotations_data;
use App\customers_details;
use App\email_templates;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use App\Jobs\CopyQuotation;
use App\Jobs\SendOrder;
use App\Jobs\CreateOrder;
use App\Jobs\UpdateDates;
use App\Jobs\ExportCustomersReeleezee;
use App\Jobs\ImportCustomersReeleezee;
use App\model_features;
use App\new_orders;
use App\new_orders_features;
use App\new_orders_sub_products;
use App\product_ladderbands;
use App\features;
use App\handyman_quotes;
use App\handyman_services;
use App\instruction_manual;
use App\items;
use App\Model1;
use App\new_quotations;
use App\new_quotations_data;
use App\new_quotations_data_calculations;
use App\new_orders_calculations;
use App\new_quotations_features;
use App\new_quotations_sub_products;
use App\new_invoices;
use App\new_invoices_data;
use App\new_invoices_data_calculations;
use App\new_invoices_features;
use App\new_invoices_sub_products;
use App\new_negative_invoices;
use App\product;
use App\product_features;
use App\product_models;
use App\Products;
use App\quotation_appointments;
use App\quotation_invoices_data;
use App\quotation_invoices;
use App\quotes;
use App\requests_q_a;
use App\retailer_labor_costs;
use App\retailers_requests;
use App\Service;
use Illuminate\Http\Request;
use App\User;
use App\Category;
use App\sub_categories;
use App\service_types;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Language;
use App\handyman_products;
use App\Generalsetting;
use App\bookings;
use Carbon\Carbon;
use DateTime;
use App\handyman_terminals;
use App\handyman_unavailability;
use App\carts;
use App\invoices;
use Illuminate\Support\Facades\Redirect;
use Crypt;
use App\users;
use App\user_languages;
use App\user_drafts;
use App\booking_images;
use App\Sociallink;
use App\sub_services;
use App\cancelled_invoices;
use App\handyman_unavailability_hours;
use App\supplier_categories;
use File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use PDF;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\Process\Process;
use App\retailer_services;
use Excel;
use App\planning_titles;
use Faker\Generator as Faker;
use App\client_quotation_msgs;
use App\retailer_general_terms;
use App\payment_calculations;
use App\invoice_payment_calculations;
use App\email_drafts;
use App\Imports\CustomersImport;
use App\Exports\CustomersExport;
use App\Exports\InvoicesExport;
use App\saved_emails;
use App\table_widths;
use App\all_invoices;
use View;
use Response;
use App\planning_statuses;
use App\general_ledgers;
use App\other_payments;
use App\retailer_subcategories_ledgers;
use App\vats;
use App\payment_accounts;
use App\notes;
use App\notes_tags;
use App\tasks;
use App\organizations;
use App\user_organizations;
use App\employees_details;
use App\universal_customers_details;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public $lang;
    public $gs;
    public $sl;
    public $reeleezee_username;
    public $reeleezee_password;
    public $array_errors;

    public function __construct()
    {
        $this->middleware('auth:user', ['except' => ['UserServices', 'AddCart', 'Services', 'DeleteSubServices', 'UserSubServices', 'SubServices']]);

        $locale = \App::getLocale() == "en" ? "eng" : "du";
        $this->lang = Language::where('lang', '=', $locale)->first();

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } //whether ip is from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } //whether ip is from remote address
        elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $this->ip_address = $_SERVER['REMOTE_ADDR'];
        }

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user_id = Auth::guard('user')->user()->id;
            $reeleezee_credentials = User::where("id", $this->user_id)->select("reeleezee_username", "reeleezee_password")->first();
            $this->reeleezee_username = $reeleezee_credentials->reeleezee_username;
            $this->reeleezee_password = $reeleezee_credentials->reeleezee_password;

            return $next($request);
        });

        $this->sl = Sociallink::findOrFail(1);
        $this->gs = Generalsetting::where('backend', 1)->first();
        // $this->reeleezee_username = "klaar";
        // $this->reeleezee_username = "woonkasteelnew";
        // $this->reeleezee_password = "Winkel4567";
        $this->array_errors = [];
    }

    public function SelectQuotationsType()
    {
        $type = 1;
        return view('user.select_type', compact('type'));
    }

    public function SelectInvoicesType()
    {
        $type = 2;
        return view('user.select_type', compact('type'));
    }

    public function CreateNewQuotation()
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

        if ($user->can('create-new-quotation')) {
            $customers = customers_details::leftjoin("users", "users.id", "=", "customers_details.user_id")->whereIn('customers_details.retailer_id', $related_users)->select("customers_details.*", "users.email", "users.fake_email")->get();

            if ($user->role_id == 2) {
                $products = array();
                $supplier_ids = retailers_requests::where("retailer_organization", $organization_id)->where('status', 1)->where('active', 1)->pluck('supplier_organization');
                $suppliers = organizations::whereIn('id', $supplier_ids)->get();
            } else {
                return redirect()->route('user-login');
                /*$products = Products::where('user_id',$user_id)->get();
                $suppliers = array();*/
            }

            $plannings = $this->Plannings(1);
            $event_titles = $plannings["event_titles"];
            $event_statuses = $plannings["event_statuses"];
            $quotation_ids = $plannings["quotation_ids"];
            $clients = $plannings["clients"];
            $planning_suppliers = $plannings["suppliers"];
            $employees = $plannings["employees"];
            $responsible_persons = $plannings["responsible_persons"];
            // $plannings = $plannings["plannings"];
            $vats = vats::get();

            return view('user.create_new_quotation1', compact('vats', 'responsible_persons', 'clients', 'employees', 'planning_suppliers', 'suppliers', 'quotation_ids', 'event_titles', 'event_statuses', 'products', 'customers', 'suppliers'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function GetSupplierProducts(Request $request)
    {
        $blind_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
        $data = Products::where('organization_id', $request->id)->where('category_id', $blind_category_id)->get();

        return $data;
    }

    public function SupplierCategories()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;

        if ($user->role_id == 4) {
            $feature_categories = Category::all();
            $my_categories = supplier_categories::where('organization_id', $organization_id)->pluck('category_id')->toArray();

            return view('user.supplier_categories', compact('feature_categories', 'my_categories'));
        }
    }

    public function SupplierCategoriesStore(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;

        if ($request->supplier_categories) {
            $category_ids = $request->supplier_categories;

            foreach ($category_ids as $s => $key) {
                $check = supplier_categories::where('organization_id', $organization_id)->skip($s)->first();

                if ($check) {
                    $check->category_id = $key;
                    $check->save();
                } else {
                    $post = new supplier_categories;
                    $post->category_id = $key;
                    $post->organization_id = $organization_id;
                    $post->save();
                }
            }

            $s = $s + 1;

            $count = supplier_categories::count();
            supplier_categories::where('organization_id', $organization_id)->take($count)->skip($s)->get()->each(function ($row) {
                $row->delete();
            });
        } else {
            supplier_categories::where('organization_id', $organization_id)->delete();
        }

        Session::flash('success', 'List updated successfully!');

        return redirect()->back();
    }

    public function GetColors(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($request->type == 'service') {
            $data = Service::leftjoin('retailer_services', 'retailer_services.service_id', '=', 'services.id')
                ->leftjoin('users', 'users.id', '=', 'retailer_services.retailer_id')
                ->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
                ->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
                ->where('services.id', $request->id)->whereIn('retailer_services.retailer_id', $related_users)
                ->select('services.*', 'organizations.service_general_ledger as ledger', 'retailer_services.sell_rate', 'retailer_services.measure', 'retailer_services.category_id', 'retailer_services.sub_category_ids')->first();
        } elseif ($request->type == 'item') {
            $data = items::where('id', $request->id)->first();
            $sub_category_id = $data->sub_category_ids;
        } else {
            if ($request->model) {
                $data = Products::leftjoin('product_models', 'product_models.product_id', '=', 'products.id')
                    ->where('product_models.id', $request->model)->where('products.id', $request->id)
                    ->select('products.*', 'product_models.measure', 'product_models.estimated_price_per_box', 'product_models.estimated_price_quantity', 'product_models.estimated_price', 'product_models.max_width')->first();
            } else {
                $data = Products::where('id', $request->id)->with('colors')->with('models')->first();
            }

            $sub_category_id = $data->sub_category_id;
        }

        if ($request->type != 'service') {
            $ledger = retailer_subcategories_ledgers::where("sub_id", $sub_category_id)->whereIn("user_id", $related_users)->first();

            $data->ledger = $ledger ? $ledger->ledger_id : "";
        }

        return $data;
    }

    public function GetPrice(Request $request)
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

        if ($request->type != 'floors') {
            $request->width = (int)$request->width;
            $request->height = (int)$request->height;
            $max_x_axis = colors::leftjoin('prices', 'prices.table_id', '=', 'colors.table_id')->where('colors.id', $request->color)->where('colors.product_id', $request->product)->max('prices.x_axis');
            $max_y_axis = colors::leftjoin('prices', 'prices.table_id', '=', 'colors.table_id')->where('colors.id', $request->color)->where('colors.product_id', $request->product)->max('prices.y_axis');

            if ((!is_numeric($max_x_axis) && !is_numeric($max_y_axis)) || ($max_x_axis >= $request->width && $max_y_axis >= $request->height)) {
                $price = colors::leftjoin('prices', 'prices.table_id', '=', 'colors.table_id')->where('colors.id', $request->color)->where('colors.product_id', $request->product)->where('prices.x_axis', '>=', $request->width)->where('prices.y_axis', '>=', $request->height)->select('colors.max_height', 'prices.value')->first();

                if (!$price) {
                    $price = new \stdClass();
                    $price->value = 0;
                    $price->max_height = '';
                }

                if ($price->max_height && ($request->height >= $price->max_height)) {
                    $data[0] = ['value' => 'y_axis', 'max_height' => $price->max_height];
                } else {
                    $features = features::whereHas('features', function ($query) use ($request) {
                        $query->leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')
                            ->where('model_features.model_id', $request->model)->where('model_features.linked', 1)
                            ->where('product_features.product_id', '=', $request->product)
                            ->select('product_features.*');
                    })->with(['features' => function ($query) use ($request) {
                        $query->leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')
                            ->where('model_features.model_id', $request->model)->where('model_features.linked', 1)
                            ->where('product_features.product_id', '=', $request->product)
                            ->select('product_features.*');
                    }])->orderBy('features.quote_order_no', 'ASC')->get();

                    $model = product_models::where('id', $request->model)->with("curtain_variables")->first();

                    if ($request->margin) {
                        $margin = Products::leftJoin('retailer_margins', 'retailer_margins.product_id', '=', 'products.id')->where('products.id', $request->product)->whereIn('retailer_margins.retailer_id', $related_users)->select('products.margin', 'retailer_margins.margin as retailer_margin')->first();
                    } else {
                        $margin = '';
                    }

                    $labor = retailer_labor_costs::where('product_id', $request->product)->whereIn('retailer_id', $related_users)->first();

                    $data = array($price, $features, $margin, $model, $labor);
                }
            } else if ($max_x_axis < $request->width && $max_y_axis < $request->height) {
                $data[0] = ['value' => 'both', 'max_width' => $max_x_axis, 'max_height' => $max_y_axis];
            } else if ($max_x_axis < $request->width) {
                $data[0] = ['value' => 'x_axis', 'max_width' => $max_x_axis];
            } else {
                $data[0] = ['value' => 'y_axis', 'max_height' => $max_y_axis];
            }
        } else {
            $price = '';

            $features = features::whereHas('features', function ($query) use ($request) {
                $query->leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')
                    ->where('model_features.model_id', $request->model)->where('model_features.linked', 1)
                    ->where('product_features.product_id', '=', $request->product)
                    ->select('product_features.*');
            })->with(['features' => function ($query) use ($request) {
                $query->leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')
                    ->where('model_features.model_id', $request->model)->where('model_features.linked', 1)
                    ->where('product_features.product_id', '=', $request->product)
                    ->select('product_features.*');
            }])->orderBy('features.quote_order_no', 'ASC')->get();

            $model = product_models::where('id', $request->model)->first();

            $margin = Products::leftJoin('retailer_margins', 'retailer_margins.product_id', '=', 'products.id')->where('products.id', $request->product)->whereIn('retailer_margins.retailer_id', $related_users)->select('products.margin', 'retailer_margins.margin as retailer_margin')->first();

            $labor = retailer_labor_costs::where('product_id', $request->product)->whereIn('retailer_id', $related_users)->first();

            $data = array($price, $features, $margin, $model, $labor);
        }

        return $data;
    }

    public function GetFeaturePrice(Request $request)
    {
        $feature = product_features::where('id', $request->id)->first();

        $sub_features = product_features::where('main_id', $request->id)->get();

        $data = array($feature, $sub_features);

        return $data;
    }

    public function GetSubProductsSizes(Request $request)
    {
        $data = product_ladderbands::where('product_id', $request->product_id)->get();

        return $data;
    }

    public function PrefixSettings()
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

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $user_id = $user->id;

        if ($user->role_id == 2) {
            $last_quotation_number = new_quotations::whereIn('creator_id', $related_users)->max('quotation_invoice_number');
            $last_order_number = '';
            $last_invoice_number = new_invoices::whereIn('creator_id', $related_users)->max('invoice_number');
            $max_customer_number = customers_details::whereIn("retailer_id", $related_users)->max("customer_number");
        } else {
            $last_quotation_number = '';
            $last_order_number = new_orders::whereIn('supplier_id', $related_users)->latest()->pluck('order_number')->first();
            $last_invoice_number = '';
            $max_customer_number = '';
        }

        return view('user.prefix_settings', compact('user', 'max_customer_number', 'last_quotation_number', 'last_order_number', 'last_invoice_number'));
    }

    public function SavePrefixSettings(Request $request)
    {
        $flag = 0;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $msg = "";
        $check_quotation = 0;
        $check_invoice = 0;
        $check_quotation1 = 0;
        $check_invoice1 = 0;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user->role_id == 2) {
            if ($request->customer_number_counter) {
                $customer_number_check = customers_details::whereIn("retailer_id", $related_users)->where("customer_number", $request->customer_number_counter)->first();

                if ($customer_number_check) {
                    Session::flash('unsuccess', __("text.This customer number is already taken"));
                    return redirect()->back();
                }

                $max_customer_number = customers_details::whereIn("retailer_id", $related_users)->max("customer_number");

                if ($request->customer_number_counter <= $max_customer_number) {
                    Session::flash('unsuccess', "Customer number counter should not be smaller than previous highest customer number in system, which is " . $max_customer_number);
                    return redirect()->back();
                }

                $customer_number_counter = $request->customer_number_counter;
            } else {
                $customer_number_counter = sprintf('%06d', $user->counter_customer_number);
            }

            if ($request->quotation_counter) {
                $quotation_number = date("Y") . "-" . sprintf('%04u', $customer_number_counter) . '-' . $request->quotation_counter;
                $check_quotation = new_quotations::where('quotation_invoice_number', $quotation_number)->whereIn('creator_id', $related_users);

                $quotation_number1 = date("Y") . '-' . $request->quotation_counter;
                $check_quotation1 = new_quotations::where('quotation_invoice_number', $quotation_number1)->whereIn('creator_id', $related_users);

                if ($request->api) {
                    $check_quotation = $check_quotation->where("id", "!=", $request->document_id)->first();
                    $check_quotation1 = $check_quotation1->where("id", "!=", $request->document_id)->first();

                    if ($check_quotation || $check_quotation1) {
                        return 0;
                    } else {
                        return $request->quotation_counter;
                    }
                }
            }

            if ($request->invoice_counter) {
                $invoice_number = date("Y") . "-" . sprintf('%04u', $customer_number_counter) . '-' . $request->invoice_counter;
                $check_invoice = all_invoices::where('invoice_number', $invoice_number)->whereIn('creator_id', $related_users);

                $invoice_number1 = date("Y") . '-' . $request->invoice_counter;
                $check_invoice1 = all_invoices::where('invoice_number', $invoice_number1)->whereIn('creator_id', $related_users);

                if ($request->api) {
                    $check_invoice = $check_invoice->where("id", "!=", $request->document_id)->first();
                    $check_invoice1 = $check_invoice1->where("id", "!=", $request->document_id)->first();

                    if ($check_invoice || $check_invoice1) {
                        return 0;
                    } else {
                        return $request->invoice_counter;
                    }
                }
            }

            $check_quotation = $check_quotation->first();
            $check_quotation1 = $check_quotation1->first();
            $check_invoice = $check_invoice->first();
            $check_invoice1 = $check_invoice1->first();

            if ($check_quotation) {
                $flag = 1;
                $msg .= 'Quotation number: ' . $quotation_number . ' already in system. Kindly change counters accordingly.<br>';
            } elseif ($check_quotation1) {
                $flag = 1;
                $msg .= 'Quotation number: ' . $quotation_number1 . ' already in system. Kindly change quotation counter accordingly.<br>';
            }

            if ($check_invoice) {
                $flag = 1;
                $msg .= 'Invoice number: ' . $invoice_number . ' already in system. Kindly change counters accordingly.';
            } elseif ($check_invoice1) {
                $flag = 1;
                $msg .= 'Invoice number: ' . $invoice_number1 . ' already in system. Kindly change invoice counter accordingly.<br>';
            }

            if ($flag) {
                Session::flash('unsuccess', $msg);
                return redirect()->back();
            }

            $user->organization->update(['counter_customer_number' => ltrim($request->customer_number_counter, '0'), 'quotation_prefix' => $request->quotation_prefix, 'counter' => ltrim($request->quotation_counter, '0'), 'quotation_client_id' => $request->quotation_client_id, 'invoice_prefix' => $request->invoice_prefix, 'counter_invoice' => ltrim($request->invoice_counter, '0'), 'invoice_client_id' => $request->invoice_client_id]);
        } else {
            $order_number = date("Y") . "-" . sprintf('%04u', $user_id) . '-' . $request->order_counter;
            $check_order = new_orders::where('order_number', $order_number)->where('supplier_id', $user_id)->first();

            if ($check_order) {
                $flag = 1;
                $msg .= 'Order number ' . $order_number . ' already in system. Kindly change counter.';
            }

            if ($flag) {
                Session::flash('unsuccess', $msg);
                return redirect()->back();
            }

            $user->organization->update(['order_prefix' => $request->order_prefix, 'counter_order' => ltrim($request->order_counter, '0'), 'order_client_id' => $request->order_client_id]);
        }

        Session::flash('success', __('text.Information updated successfully.'));

        return redirect()->back();
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

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        if ($user->can('show-dashboard')) {
            if ($user->role_id == 2) {
                $suppliers = retailers_requests::where("retailer_organization", $organization_id)->where('status', 1)->where('active', 1)->pluck('supplier_organization');

                $orders = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')
                    ->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')
                    ->leftJoin('organizations', 'organizations.id', '=', 'new_orders.supplier_id')
                    ->whereIn('new_orders.supplier_id', $suppliers)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.finished', 1)->where('new_quotations.deleted_at', NULL)
                    ->orderBy('new_orders.id', 'desc')->take(10)->select('organizations.company_name', 'customers_details.name', 'new_orders.delivery_date', 'new_orders.order_date', 'new_orders.approved', 'new_quotations.*')->get();
            } else {
                $orders = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')
                    ->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'new_quotations.creator_id')
                    ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
                    ->where('new_orders.supplier_id', $organization_id)->where('new_quotations.finished', 1)->where('new_quotations.deleted_at', NULL)
                    ->orderBy('new_orders.id', 'desc')->take(10)->select('organizations.company_name', 'new_orders.order_number', 'new_orders.delivery_date', 'new_orders.order_date', 'new_orders.approved', 'new_quotations.*')->get();
            }

            $commission_percentage = Generalsetting::findOrFail(1);

            $start = strtotime(date('Y-m-01', strtotime('0 month')));
            $end = strtotime(date('Y-m-01', strtotime('-5 month')));

            $dates = array();
            $month = $start;

            while ($end < $month) {
                $dates[] = date('Y-m-01', $month);
                $month = strtotime("-1 month", $month);
            }

            $quotes_chart = array();
            $accepted_chart = array();
            $invoices_chart = array();

            $dates = array_reverse($dates);

            foreach ($dates as $date) {

                $c_date = date('m', strtotime($date));

                $month_chart = new_quotations::whereIn('creator_id', $related_users)->whereMonth('created_at', '=', $c_date)->get();

                $invoice_total = 0;
                // $quotes_count = 0;
                $quotes_total = 0;
                $quotes_accepted_total = 0;

                foreach ($month_chart as $value) {

                    if ($value->invoice) {
                        $invoice_total = $invoice_total + $value->grand_total;
                    }

                    // $quotes_count = $quotes_count + 1;
                    $quotes_total = $quotes_total + $value->grand_total;

                    if ($value->accepted) {
                        $quotes_accepted_total = $quotes_accepted_total + $value->grand_total;
                    }
                }

                /*$invoice_total = number_format((float)$invoice_total, 2, ',', '.');*/

                $quotes_chart[] = array('label' => Carbon::parse($date)->locale('nl')->isoFormat('MMM'), 'y' => $quotes_total);
                $accepted_chart[] = array('label' => Carbon::parse($date)->locale('nl')->isoFormat('MMM'), 'y' => $quotes_accepted_total);
                $invoices_chart[] = array('label' => Carbon::parse($date)->locale('nl')->isoFormat('MMM'), 'y' => $invoice_total);
            }

            ini_set('precision', 10);
            ini_set('serialize_precision', 10);

            $invoices_chart = json_encode($invoices_chart);
            $quotes_chart = json_encode($quotes_chart);
            $accepted_chart = json_encode($accepted_chart);

            return view('user.dashboard', compact('user', 'commission_percentage', 'invoices_chart', 'quotes_chart', 'accepted_chart', 'orders'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function QuotationRequests()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $invoices = array();

        $requests = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->where('quotes.user_id', $user_id)->select('quotes.*', 'categories.cat_name', 'services.title')->orderBy('quotes.created_at', 'desc')->get();

        foreach ($requests as $key) {
            $invoices[] = new_quotations::where('quote_request_id', $key->id)->where('approved', 1)->get();
        }

        return view('user.client_quote_requests', compact('requests', 'invoices'));
    }

    public function HandymanQuotationRequests()
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

        if ($user->can('handyman-quotation-requests')) {
            $invoices = array();

            $requests = handyman_quotes::leftjoin('quotes', 'quotes.id', '=', 'handyman_quotes.quote_id')->leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->where('handyman_quotes.handyman_id', $organization_id)->select('quotes.*', 'categories.cat_name', 'services.title', 'handyman_quotes.quote_id', 'handyman_quotes.handyman_id', 'brands.cat_name as brand_name', 'product_models.model as model_name')->orderBy('quotes.created_at', 'desc')->get();

            foreach ($requests as $key) {

                $invoices[] = new_quotations::where('quote_request_id', $key->quote_id)->whereIn('creator_id', $related_users)->first();
                // $invoices[] = quotation_invoices::where('quote_id', $key->quote_id)->whereIn('handyman_id',$related_users)->first();
            }

            return view('user.quote_requests', compact('requests', 'invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function Retailers()
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

        if ($user->can('supplier-retailers')) {
            $retailers = organizations::leftjoin('retailers_requests', 'retailers_requests.retailer_organization', '=', 'organizations.id')->where('organizations.Type', '=', "Retailer")->where('retailers_requests.supplier_organization', '=', $organization_id)->orderBy('organizations.created_at', 'desc')->select('organizations.*', 'retailers_requests.status', 'retailers_requests.active')->get();

            return view('user.retailers', compact('retailers'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function DetailsRetailer($id)
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

        if ($user->can('retailer-details')) {
            $retailer = organizations::leftjoin('retailers_requests', 'retailers_requests.retailer_organization', '=', 'organizations.id')->where('organizations.Type', '=', "Retailer")->where('retailers_requests.retailer_organization', '=', $id)->where('retailers_requests.supplier_organization', '=', $organization_id)->select('organizations.*')->first();

            if (!$retailer) {
                return redirect()->back();
            }

            return view('user.retailer_details', compact('retailer'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function AcceptRetailerRequest(Request $request)
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
        $retailer_organization = organizations::findOrFail($request->retailer_id);

        retailers_requests::where('retailer_organization', $request->retailer_id)->where('supplier_organization', $organization_id)->update(['status' => 1, 'active' => 1]);

        \Mail::send(array(), array(), function ($message) use ($retailer_organization, $organization) {
            $message->to($retailer_organization->email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject('Request Accepted!')
                ->html("Supplier " . $organization->company_name . " has accepted your request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        Session::flash('success', __('text.Request accepted successfully!'));

        return redirect()->back();
    }

    public function SuspendRetailerRequest(Request $request)
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
        $retailer_organization = organizations::findOrFail($request->retailer_id);

        if ($request->active) {
            retailers_requests::where('retailer_organization', $request->retailer_id)->where('supplier_organization', $organization_id)->update(['status' => 1, 'active' => 1]);

            \Mail::send(array(), array(), function ($message) use ($retailer_organization, $organization) {
                $message->to($retailer_organization->email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Request Accepted!')
                    ->html("Supplier " . $organization->company_name . " has reactivated your request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });

            Session::flash('success', 'Request activated successfully!');
        } else {
            retailers_requests::where('retailer_organization', $request->retailer_id)->where('supplier_organization', $organization_id)->update(['status' => 1, 'active' => 0]);

            \Mail::send(array(), array(), function ($message) use ($retailer_organization, $organization) {
                $message->to($retailer_organization->email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Request Accepted!')
                    ->html("Supplier " . $organization->company_name . " has suspended your request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });

            Session::flash('success', __('text.Request suspended successfully!'));
        }

        return redirect()->back();
    }

    public function DeleteRetailerRequest(Request $request)
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
        $retailer_organization = organizations::findOrFail($request->retailer_id);

        retailers_requests::where('retailer_organization', $request->retailer_id)->where('supplier_organization', $organization_id)->delete();

        \Mail::send(array(), array(), function ($message) use ($retailer_organization, $organization) {
            $message->to($retailer_organization->email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject('Request Accepted!')
                ->html("Supplier " . $organization->company_name . " has deleted your request. You can no longer see details of this supplier.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        Session::flash('success', __('text.Request deleted successfully!'));

        return redirect()->back();
    }

    public function Suppliers()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;

        if ($user->can('retailer-suppliers')) {
            $users = organizations::where('Type', '=', "Supplier")->where(function ($query) use ($organization_id) {
                $query->whereHas('supplierRequests', function ($query) use ($organization_id) {
                    $query->where('retailer_organization', $organization_id);
                })->orWhere('supplier_account_show', 1);
            })->with(['supplierRequests' => function ($query) use ($organization_id) {
                $query->where('retailer_organization', $organization_id);
            }])->orderBy('created_at', 'desc')->get();

            // $products = array();
            // $categories = array();

            // foreach ($users as $key) {

            //     if($key->status && $key->active)
            //     {
            //         $products[] = Products::where('user_id',$key->id)->get();
            //         $categories[] = supplier_categories::leftjoin('categories','categories.id','=','supplier_categories.category_id')->where('supplier_categories.user_id',$key->id)->orderBy('categories.id','desc')->select('categories.cat_name')->get();
            //     }
            //     else
            //     {
            //         $products[] = array();
            //         $categories[] = array();
            //     }

            // }

            $suppliers = organizations::where('Type', '=', "Supplier")->whereHas('supplierRequests', function ($query) use ($organization_id) {
                $query->where('retailer_organization', $organization_id)->where('status', 1)->where('active', 1);
            })->orderBy('created_at', 'desc')->get();

            $supplier_categories = array();

            foreach ($suppliers as $key) {
                $categories = supplier_categories::where('organization_id', $key->id)->pluck('category_id')->toArray();
                $supplier_categories = array_merge($supplier_categories, $categories);
            }

            $supplier_categories = array_unique($supplier_categories);
            $sub_categories = sub_categories::whereIn('parent_id', $supplier_categories)->get();

            return view('user.suppliers', compact('users', "suppliers", "sub_categories"));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function DetailsSupplier($id)
    {
        $user = Auth::guard('user')->user();

        if ($user->can('supplier-details')) {
            $user = organizations::findOrFail($id);

            if (!$user) {
                return redirect()->back();
            }

            return view('user.supplier_details', compact('user'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function SendRequestSupplier(Request $request)
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

        $supplier_organization_id = $request->supplier_id;
        $supplier_organization = organizations::findOrFail($supplier_organization_id);

        $retailer_company_name = $organization->company_name;
        $supplier_email = $supplier_organization->email;

        $check = retailers_requests::where('retailer_organization', $organization_id)->where('supplier_organization', $supplier_organization_id)->first();
        $link = url('/') . '/aanbieder/retailers';

        if ($check) {
            \Mail::send(array(), array(), function ($message) use ($supplier_email, $retailer_company_name, $link) {
                $message->to($supplier_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Retailer Request!')
                    ->html("Retailer " . $retailer_company_name . " request for the client role is pending for your further action. Click <a href='" . $link . "'>here</a> to accept or ignore his request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        } else {
            $post = new retailers_requests;
            $post->retailer_organization = $organization_id;
            $post->supplier_organization = $supplier_organization_id;
            $post->status = 0;
            $post->save();

            \Mail::send(array(), array(), function ($message) use ($supplier_email, $retailer_company_name, $link) {
                $message->to($supplier_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject('Retailer Request!')
                    ->html("A retailer " . $retailer_company_name . " submitted a client request. Click <a href='" . $link . "'>here</a> to accept or ignore his request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        }

        Session::flash('success', __('text.Request submitted successfully!'));

        return redirect()->back();
    }

    public function HandymanQuotations($id = '')
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

        if ($user->can('quotations')) {
            if ($id) {
                $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->whereIn('quotation_invoices.handyman_id', $related_users)->where('quotation_invoices.quote_id', $id)->where('quotation_invoices.invoice', 0)->orderBy('quotation_invoices.id', 'desc')->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivery_date', 'quotation_invoices.id as invoice_id', 'quotation_invoices.invoice', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'users.name', 'users.family_name')->get();
            } else {
                $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->whereIn('quotation_invoices.handyman_id', $related_users)->where('quotation_invoices.invoice', 0)->orderBy('quotation_invoices.id', 'desc')->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivery_date', 'quotation_invoices.id as invoice_id', 'quotation_invoices.invoice', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'users.name', 'users.family_name')->get();
            }

            return view('user.quote_invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function GetQuotationsData(Request $request)
    {
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            if ($user->can('create-new-quotation')) {
                $new_invoices = new_quotations::leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.status', '!=', 3)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date', 'customers_details.name', 'customers_details.family_name', 'quotes.quote_name', 'quotes.quote_familyname')->with('orders')->with('invoices')->with('unseen_messages')->skip($skip)->take($pageLength)->get();
            } else {
                $new_invoices = collect(new new_quotations());
            }

            // $invoices = $invoices->concat($new_invoices)->sortByDesc('created_at');
            $invoices = $new_invoices;
        } else {
            $invoices = new_quotations::leftjoin('new_orders', 'new_orders.quotation_id', '=', 'new_quotations.id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_orders.deleted_at', NULL)->where('new_orders.supplier_id', $organization_id)->where('new_quotations.finished', 1)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_orders.order_sent', 'new_orders.id as data_id', 'new_quotations.created_at as invoice_date', 'new_orders.order_number', 'new_orders.approved as data_approved', 'new_orders.processing as data_processing', 'new_orders.delivered as data_delivered', 'customers_details.name', 'customers_details.family_name', 'quotes.quote_name', 'quotes.quote_familyname')->with('invoices')->skip($skip)->take($pageLength)->get();
            $invoices = $invoices->unique('invoice_id');
        }

        // Search
        $search = $request->search;

        $orderByName = 'quotation_invoice_number';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'quotation_invoice_number';
                break;
            case '1':
                $orderByName = 'description';
                break;
            case '2':
                $orderByName = 'amount';
                break;
        }

        $recordsFiltered = $recordsTotal = $invoices->count();

        return response()->json(["draw" => $request->draw, "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $invoices], 200);
    }

    public function CustomerQuotationAjax(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        $organization_id = $user->organization->id;
        $related_users = \App\User::join('user_organizations', 'user_organizations.user_id', '=', 'users.id')
            ->where('user_organizations.organization_id', $organization_id)
            ->select('users.id')
            ->pluck('id');

        // Base query
        $query = new_quotations::leftJoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')
            ->leftJoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')
            ->whereIn('new_quotations.creator_id', $related_users);

        // Apply filters
        if ($request->filled('year')) {
            $query->whereYear('new_quotations.created_at', $request->year);
        }

        if ($request->filled('month')) {
            $query->whereMonth('new_quotations.created_at', $request->month);
        }

        if ($request->filled('status')) {
            $this->filterStatus($query, $request->status);
        }

        if ($keyword = $request->input('search.value')) {
            $s_statuses = ucwords($request->input('search.value'));
            if (in_array($s_statuses, ['Pending', 'Draft', 'Waiting For Approval', 'Quotation Sent', 'Asking for Review', 'Quotation Accepted', 'Payment Pending', 'Paid', 'Invoice Generated', 'Goods Delivered', 'Goods Received', 'Closed', 'Order Processing', 'Processing', 'Order Delivered', 'Order Confirmed', 'Confirmation Pending'])) {
                $this->filterStatus($query, $s_statuses);
            } else {
                $date_f = date('Y-m-d', strtotime($keyword));
                $keyword = '%' . $keyword . '%';
                $query->where(function ($query) use ($keyword, $date_f) {
                    $query->where('quotes.quote_name', 'LIKE', $keyword)
                        ->orWhere('quotes.quote_familyname', 'LIKE', $keyword)
                        ->orWhere('new_quotations.grand_total', 'LIKE', $keyword)
                        ->orWhere('customers_details.name', 'LIKE', $keyword)
                        ->orWhere('customers_details.family_name', 'LIKE', $keyword)
                        ->orWhere('new_quotations.quotation_invoice_number', 'LIKE', $keyword)
                        ->orWhereDate('new_quotations.created_at', $date_f);
                });
            }
        }

        if ($user_role == 2) {
            // Role-specific filtering and selection
            if ($user->can('create-new-quotation')) {
                $new_invoices = $query->where('new_quotations.status', '!=', 3)
                    ->select('new_quotations.id', 'new_quotations.quotation_invoice_number', 'new_quotations.quote_request_id', 'new_quotations.paid', 'new_quotations.grand_total', 'new_quotations.status', 'new_quotations.received', 'new_quotations.delivered', 'new_quotations.accepted', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.admin_quotation_sent', 'new_quotations.draft', 'new_quotations.processing', 'new_quotations.finished', 'new_quotations.regards', 'new_quotations.invoice', 'new_quotations.delivery_date', 'new_quotations.retailer_delivered', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date', 'customers_details.name', 'customers_details.family_name', 'quotes.quote_name', 'quotes.quote_familyname')
                    ->orderBy('new_quotations.created_at', 'desc');
            } else {
                $new_invoices = collect(); // Empty collection if no permission
            }
            $invoices = $new_invoices;
        } else {
            // Handle the case for non-role 2 users
            $invoices = $query->leftJoin('new_orders', 'new_orders.quotation_id', '=', 'new_quotations.id')
                ->leftJoin('organizations', 'organizations.id', '=', 'new_orders.supplier_id')
                ->whereNull('new_orders.deleted_at')
                ->where('new_orders.supplier_id', $organization_id)
                ->where('new_quotations.finished', 1)
                ->orderBy('new_quotations.created_at', 'desc')
                ->select('organizations.company_name', 'new_quotations.*', 'new_orders.order_sent', 'new_orders.id as data_id', 'new_orders.order_number', 'new_orders.approved as data_approved', 'new_orders.processing as data_processing', 'new_orders.delivered as data_delivered')
                ->with('invoices', 'payment_calculations')
                ->get()
                ->unique('invoice_id');
        }

        return DataTables::of($invoices)
            ->smart(true)
            ->addColumn('document_number', function ($key) use ($user) {
                return '
                        <div style="display: flex; align-items: center;" class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" style="margin: 0;" class="custom-control-input" id="customCheck' . $key->id . '">
                            <input type="hidden" name="quotation_ids[]" class="quotation_ids" value="' . $key->invoice_id . '">
                            <input type="hidden" class="delete_quotations_options" name="delete_quotations_options[]">
                            <label style="margin: 0 0 0 5px; font-weight: 500;" class="custom-control-label" for="customCheck' . $key->id . '">OF# ' . $key->quotation_invoice_number . '</label>
                        </div>';
            })
            ->addColumn('customer_name', function ($key) {
                return $key->quote_request_id ? ($key->paid ? $key->quote_name . ' ' . $key->quote_familyname : 'vloerofferte.nl') : $key->name . ' ' . $key->family_name;
            })
            ->addColumn('grand_total', function ($key) {
                return "€ " . number_format((float)$key->grand_total, 2);
            })
            ->addColumn('paid', function ($key) use ($user) {
                if ($user->role_id != 2) return '';
                $paid = $key->payment_calculations->where('paid_by', '!=', 'Pending')->sum('amount');
                return '€ ' . number_format($paid, 2, '.', ',');
            })
            ->addColumn('all_status_elements', function ($key) use ($user) {
                return $this->getStatusElements($key, $user);
            })
            ->addColumn('date1', function ($key) {
                return date('d-m-Y', strtotime($key->invoice_date));
            })
            ->addColumn('regards', function ($key) use ($user) {
                return $user->role_id == 2 ? '<p class="hovertext">' . nl2br($key->regards) . '</p>' : '';
            })
            ->addColumn('action', function ($key) use ($user_role) {

                return '<div class="dropdown dropdown1">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            ' . __('text.Action') . ' <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            ' . $this->generateActionMenu($key, $user_role) . '
                        </ul>
                    </div>';
            })
            ->rawColumns(['document_number', 'customer_name', 'grand_total', 'paid', 'all_status_elements', 'regards', 'status', 'order_status', 'action'])
            ->make(true);
    }

    private function generateActionMenu($key, $user_role)
    {
        $invoice_id = $key->invoice_id;
        $actions = '';

        if ($user_role == 2) {
            if ($key->draft) {
                $actions .= '<li><a href="' . url('/aanbieder/approve-draft-quotation/' . $invoice_id) . '">' . __('text.Approve Draft') . '</a></li>';
            }

            if (!$key->quote_request_id) {
                $actions .= '<li><a href="' . url('/aanbieder/copy-new-quotation/' . $invoice_id) . '">' . __('text.Copy Quotation') . '</a></li>';
            }

            $actions .= '<li><a class="delete-btn" data-href="' . url('/aanbieder/delete-new-quotation/' . $invoice_id) . '">' . __('text.Delete Quotation') . '</a></li>';
            $actions .= '<li><a href="' . url('/aanbieder/messages/' . $invoice_id) . '">' . __('text.See Messages') . '</a></li>';
            $actions .= '<li><a href="' . url('/aanbieder/sent-emails/' . $invoice_id) . '">' . __('text.Sent Mails') . '</a></li>';
            $actions .= '<li><a href="' . url('/aanbieder/view-new-quotation/' . $invoice_id) . '">' . __('text.View Quotation') . '</a></li>';

            if ($key->accepted) {
                $actions .= '<li><a href="' . url('/aanbieder/view-details/' . $invoice_id) . '">' . __('text.View Details') . '</a></li>';
            }

            if (!$key->invoice) {
                if ((!$key->quote_request_id || $key->paid) && !$key->draft) {
                    $actions .= '<li><a class="create-invoice-btn" data-href="' . url('/aanbieder/create-new-invoice/' . $invoice_id) . '">' . __('text.Create Invoice') . '</a></li>';
                }
            } else {
                $actions .= '<li><a href="' . url('/aanbieder/view-new-invoice/' . $invoice_id) . '">' . __('text.View Invoice') . '</a></li>';
                $actions .= '<li><a href="' . (isset($key->invoices[0]) ? url('/aanbieder/download-invoice-pdf/' . $key->invoices[0]->id) : '#') . '">' . __('text.Download Invoice PDF') . '</a></li>';
            }

            if ($key->paid) {
                $actions .= '<li><a href="' . url('/aanbieder/download-commission-invoice/' . $invoice_id) . '">' . __('text.Download Commission Invoice') . '</a></li>';
            }

            if ($key->status != 2 && $key->status != 3) {
                if ($key->ask_customization) {
                    $actions .= '<li><a onclick="ask(this)" data-text="' . $key->review_text . '" href="javascript:void(0)">' . __('text.Review Reason') . '</a></li>';
                }

                if (!$key->quote_request_id && !$key->draft) {
                    $actions .= '<li><a class="accept-btn" data-href="' . url('/aanbieder/accept-new-quotation/' . $invoice_id) . '">' . __('text.Accept') . '</a></li>';
                }
            }

            if ($key->accepted && !$key->finished) {
                $actions .= '<li><a href="' . url('/aanbieder/discard-quotation/' . $invoice_id) . '">' . __('text.Discard Quotation') . '</a></li>';
            }

            if (!$key->quote_request_id || $key->paid) {
                if (count($key->orders) > 0) {
                    $actions .= '<li><a href="' . url('/aanbieder/view-order/' . $invoice_id) . '">' . __('text.View Order') . '</a></li>';
                }
            }

            if (!$key->quote_request_id || $key->paid) {
                if ($key->accepted && !$key->processing && !$key->finished) {
                    $actions .= '<li><a class="send-new-order" data-id="' . $invoice_id . '" data-date="' . ($key->delivery_date ? date('d-m-Y', strtotime($key->delivery_date)) : '') . '" href="javascript:void(0)">' . __('text.Send Order') . '</a></li>';
                }
            }

            if ($key->received && !$key->retailer_delivered) {
                $actions .= '<li><a href="' . url('/aanbieder/retailer-mark-delivered/' . $invoice_id) . '">' . __('text.Mark as delivered') . '</a></li>';
            }

            if ($key->status == 2) {
                if ($key->finished) {
                    foreach ($key->orders->unique('supplier_id') as $data) {
                        $actions .= '<li><a href="' . url('/aanbieder/download-order-pdf/' . $data->id) . '">' . __('text.Download Supplier (:attribute) Order PDF', ['attribute' => $data->company_name]) . '</a></li>';
                    }
                }

                foreach ($key->orders->unique('supplier_id') as $data) {
                    if ($data->approved) {
                        $actions .= '<li><a href="' . url('/aanbieder/download-order-confirmation-pdf/' . $data->id) . '">' . __('text.Download Supplier (:attribute) Order Confirmation PDF', ['attribute' => $data->company_name]) . '</a></li>';
                    }
                }
            }

            $actions .= '<li><a href="' . url('/aanbieder/download-new-quotation/' . $invoice_id) . '">' . __('text.Download PDF') . '</a></li>';

            if (!$key->quote_request_id || $key->paid) {
                if (!$key->processing && count($key->orders) > 0) {
                    $actions .= '<li><a href="' . url('/aanbieder/download-full-order-pdf/' . $invoice_id) . '">' . __('text.Download Full Order PDF') . '</a></li>';
                }
            }

            if ($key->quote_request_id && !$key->admin_quotation_sent) {
                $actions .= '<li><a href="' . url('/aanbieder/send-quotation-admin/' . $invoice_id) . '">' . __('text.Send Quotation') . '</a></li>';
            }

            if (!$key->quote_request_id) {
                $actions .= '<li><a class="send-new-quotation" data-id="' . $invoice_id . '" href="javascript:void(0)">' . __('text.Send Quotation') . '</a></li>';
            }
        } else {
            $actions .= '<li><a href="' . url('/aanbieder/view-order/' . $invoice_id) . '">' . __('text.View Order') . '</a></li>';
            if (!$key->data_delivered && !$key->data_processing) {
                $actions .= '<li><a href="' . url('/aanbieder/change-delivery-dates/' . $invoice_id) . '">' . __('text.Edit Delivery Dates') . '</a></li>';
            }
            if ($key->data_approved && !$key->data_delivered) {
                $actions .= '<li><a href="' . url('/aanbieder/supplier-order-delivered/' . $invoice_id) . '">' . __('text.Mark as delivered') . '</a></li>';
            }
            if ($key->data_approved) {
                $actions .= '<li><a href="' . url('/aanbieder/download-order-confirmation-pdf/' . $key->data_id) . '">' . __('text.Download Order Confirmation PDF') . '</a></li>';
            }
            $actions .= '<li><a href="' . url('/aanbieder/download-order-pdf/' . $key->data_id) . '">' . __('text.Download Order PDF') . '</a></li>';
        }

        return $actions;
    }

    private function getStatusElements($key, $user)
    {
        $statusElement = "";
        $orderStatusElement = "";

        // Determine status element based on the quotation status
        switch ($key->status) {
            case 3:
                if ($key->received) {
                    $status = "Goods Received";
                    $statusElement = '<span class="btn btn-success">' . __('text.Goods Received') . '</span>';
                } elseif ($key->delivered) {
                    $status = "Goods Delivered";
                    $statusElement = '<span class="btn btn-success">' . __('text.Goods Delivered') . '</span>';
                } else {
                    $status = "Invoice Generated";
                    $statusElement = '<span class="btn btn-success">' . __('text.Invoice Generated') . '</span>';
                }
                break;
            case 2:
                if ($key->accepted) {
                    if (!$key->quote_request_id) {
                        $status = "Quotation Accepted";
                        $statusElement = '<span class="btn btn-primary1">' . __('text.Quotation Accepted') . '</span>';
                    } else {
                        $status = $key->paid ? "Paid" : "Payment Pending";
                        $statusElement = '<span class="btn ' . ($key->paid ? 'btn-success' : 'btn-primary1') . '">' . __($key->paid ? 'text.Paid' : 'text.Payment Pending') . '</span>';
                    }
                } else {
                    $status = "Closed";
                    $statusElement = '<span class="btn btn-success">' . __('text.Closed') . '</span>';
                }
                break;
            default:
                if ($key->ask_customization) {
                    $status = "Asking for Review";
                    $statusElement = '<span class="btn btn-info">' . __('text.Asking for Review') . '</span>';
                } elseif ($key->approved) {
                    $status = "Quotation Sent";
                    $statusElement = '<span class="btn btn-success">' . __('text.Quotation Sent') . '</span>';
                } else {
                    if ($key->quote_request_id && $key->admin_quotation_sent) {
                        $status = "Waiting For Approval";
                        $statusElement = '<span class="btn btn-info">' . __('text.Waiting For Approval') . '</span>';
                    } elseif ($key->draft) {
                        $status = "Draft";
                        $statusElement = '<span class="btn btn-info">' . __('text.Draft') . '</span>';
                    } else {
                        $status = "Pending";
                        $statusElement = '<span class="btn btn-warning">' . __('text.Pending') . '</span>';
                    }
                }
                break;
        }

        // Determine order status element
        if ($key->status != 3) {
            if ($key->processing) {
                $orderStatus = "Order Processing";
                $orderStatusElement = '<br><span class="btn btn-success mt-10">' . __('text.Order Processing') . '</span>';
            } elseif ($key->finished) {
                if ($user->role_id == 2) {
                    $data = $key->orders->unique('supplier_id');
                    $filteredData = $data->where('approved', 1);

                    if ($filteredData->count() === $data->count()) {
                        if ($data->contains('delivered', 1)) {
                            $filteredData2 = $data->where('delivered', 1);

                            if ($filteredData2->count() === $data->count()) {
                                $orderStatus = "Delivered by supplier(s)";
                                $orderStatusElement = '<br><span class="btn btn-success mt-10">' . __('text.Delivered by supplier(s)') . '</span>';
                            } elseif ($filteredData2->isEmpty()) {
                                $orderStatus = "Confirmed by supplier(s)";
                                $orderStatusElement = '<br><span class="btn btn-success mt-10">' . __('text.Confirmed by supplier(s)') . '</span>';
                            } else {
                                $orderStatus = "";
                                $orderStatusElement = '<br><span class="btn btn-success mt-10">' . $filteredData2->count() . '/' . $data->count() . ' ' . __('text.Delivered Order') . '</span>';
                            }
                        } else {
                            $orderStatus = "Confirmed by supplier(s)";
                            $orderStatusElement = '<br><span class="btn btn-success mt-10">' . __('text.Confirmed by supplier(s)') . '</span>';
                        }
                    } elseif ($filteredData->isEmpty()) {
                        $orderStatus = "Confirmation Pending";
                        $orderStatusElement = '<br><span class="btn btn-warning mt-10">' . __('text.Confirmation Pending') . '</span>';
                    } else {
                        $orderStatus = "";
                        $orderStatusElement = '<br><span class="btn btn-success mt-10">' . $filteredData->count() . '/' . $data->count() . ' ' . __('text.Confirmed') . '</span>';
                    }
                } else {
                    if ($key->data_processing) {
                        $orderStatus = "Processing";
                        $orderStatusElement = '<span class="btn btn-warning">' . __('text.Processing') . '</span>';
                    } elseif ($key->data_delivered) {
                        $orderStatus = "Order Delivered";
                        $orderStatusElement = '<span class="btn btn-warning">' . __('text.Order Delivered') . '</span>';
                    } elseif ($key->data_approved) {
                        $orderStatus = "Order Confirmed";
                        $orderStatusElement = '<span class="btn btn-warning">' . __('text.Order Confirmed') . '</span>';
                    } else {
                        $orderStatus = "Confirmation Pending";
                        $orderStatusElement = '<span class="btn btn-warning">' . __('text.Confirmation Pending') . '</span>';
                    }
                }
            }
        }

        // Combine status elements
        return $statusElement . $orderStatusElement;
    }

    public function CustomerQuotations()
    {
        return view('user.quotations');
    }

    private function filterStatus($query, $status)
    {
        $user = Auth::guard('user')->user();
        switch ($status) {
            case 'Goods Received':
                $query->where('new_quotations.status', 3)
                    ->where('new_quotations.received', 1)
                    ->where('new_quotations.delivered', 0);
                break;

            case 'Goods Delivered':
                $query->where('new_quotations.status', 3)
                    ->where('new_quotations.delivered', 1);
                break;

            case 'Invoice Generated':
                $query->where('new_quotations.status', 3)
                    ->where('new_quotations.received', 0)
                    ->where('new_quotations.delivered', 0);
                break;

                // For Status 2
            case 'Quotation Accepted':
                $query->where('new_quotations.status', 2)
                    ->where('new_quotations.accepted', 1)
                    ->where('new_quotations.finished', 0)
                    ->whereNull('new_quotations.quote_request_id');
                break;

            case 'Closed':
                $query->where('new_quotations.status', 2)
                    ->where('new_quotations.accepted', 0);
                break;

            case 'Paid':
                $query->where('new_quotations.status', 2)
                    ->where('new_quotations.accepted', 1)
                    ->whereNotNull('new_quotations.quote_request_id')
                    ->where('new_quotations.paid', 1);
                break;

            case 'Payment Pending':
                $query->where('new_quotations.status', 2)
                    ->where('new_quotations.accepted', 1)
                    ->whereNotNull('new_quotations.quote_request_id')
                    ->where('new_quotations.paid', 0);
                break;

                // Status otherthen 3 & 2
            case 'Order Processing':
                $query->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.processing', 1)
                    ->where('new_quotations.finished', 0);
                break;

            case 'Confirmation Pending':
                $query->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.processing', 0)
                    ->where('new_quotations.finished', 1)
                    ->where(function ($query) use ($user) {
                        if ($user->role_id == 2) {
                            $orders = $query->get()->flatMap->orders->unique('supplier_id');
                            $filteredData = $orders->reject(function ($value) {
                                return $value['approved'] != 1;
                            });
                            if ($filteredData->count() <= 0) {
                                $query->whereIn('supplier_id', $filteredData->pluck('supplier_id'));
                            }
                        }
                    });
                break;


            case 'Processing':
                $query->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.data_processing', 1)
                    ->where('new_quotations.data_delivered', 0)
                    ->where('new_quotations.data_approved', 0)
                    ->where('new_quotations.processing', 0)
                    ->where('new_quotations.finished', 1);
                break;

            case 'Order Delivered':
                $query->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.data_processing', 0)
                    ->where('new_quotations.data_delivered', 1)
                    ->where('new_quotations.data_approved', 0)
                    ->where('new_quotations.processing', 0)
                    ->where('new_quotations.finished', 1);
                break;

            case 'Order Confirmed':
                $query->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.data_processing', 0)
                    ->where('new_quotations.data_delivered', 0)
                    ->where('new_quotations.data_approved', 1)
                    ->where('new_quotations.processing', 0)
                    ->where('new_quotations.finished', 1);
                break;

            case 'Waiting For Approval':
                $query->where('new_quotations.status', '!=', 2)
                    ->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.admin_quotation_sent', 1)
                    ->whereNotNull('new_quotations.quote_request_id')
                    ->where('new_quotations.ask_customization', 0)
                    ->where('new_quotations.approved', 0);
                break;

            case 'Asking for Review':
                $query->where('new_quotations.status', '!=', 2)
                    ->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.ask_customization', 1)
                    ->where('new_quotations.approved', 0);
                break;

            case 'Quotation Sent':
                $query->where('new_quotations.status', '!=', 2)
                    ->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.ask_customization', 0)
                    ->where('new_quotations.approved', 1);
                break;

            case 'Pending':
                $query->where('new_quotations.status', '!=', 2)
                    ->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.draft', 0)
                    ->where('new_quotations.ask_customization', 0)
                    ->where('new_quotations.admin_quotation_sent', 0)
                    ->whereNull('new_quotations.quote_request_id')
                    ->where('new_quotations.approved', 0);
                break;

            case 'Draft':
                $query->where('new_quotations.status', '!=', 2)
                    ->where('new_quotations.status', '!=', 3)
                    ->where('new_quotations.draft', 1)
                    ->where('new_quotations.ask_customization', 0)
                    ->where('new_quotations.admin_quotation_sent', 0)
                    ->whereNull('new_quotations.quote_request_id')
                    ->where('new_quotations.approved', 0);
                break;
            default:
                break;
        }
    }

    public function ApproveDraftQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $quotation = new_quotations::where("id", $id)->whereIn("creator_id", $related_users)->first();

        if (!$quotation) {
            return redirect()->back();
        }

        if (!$quotation->quote_request_id && $quotation->user_id == 0 && $quotation->customer_details == 0) {
            Session::flash('unsuccess', __('text.This is a direct quotation so it should be linked with a customer.'));
        } else {
            $filename = $quotation->quotation_invoice_number . '.pdf';
            $file = public_path() . '/assets/newQuotations/' . $organization_id . '/' . $filename;
            copy($file, public_path() . '/assets/draftQuotations/' . $organization_id . '/' . $filename);

            $quotation->draft = 0;
            $quotation->draft_token = NULL;
            $quotation->save();
            // Session::flash('success', __('text.Quotation Approved'));
        }

        return redirect()->back();
    }

    public function CopyNewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user->organization_email = $user->organization->email;

        $quotation = new_quotations::where("id", $id)->whereIn("creator_id", $related_users)->first();

        if (!$quotation) {
            return redirect()->back();
        }

        CopyQuotation::dispatch($id, $user, $organization, $related_users, $quotation);

        Session::flash('success', __('text.Quotation will soon be copied in background...'));
        return redirect()->back();
    }

    public function DeleteNewQuotations($id, $retailer)
    {
        $organization_id = $retailer->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $quotation = new_quotations::where("id", $id)->whereIn("creator_id", $related_users)->first();

        if ($quotation) {
            $quote_request_id = $quotation->quote_request_id;

            if ($quote_request_id) {
                $other_quotation = new_quotations::where("quote_request_id", $quote_request_id)->whereIn("creator_id", "!=", $related_users)->first();

                if ($other_quotation) {
                    quotes::where("id", $quote_request_id)->update(["status" => 1]);
                } else {
                    quotes::where("id", $quote_request_id)->update(["status" => 0]);
                }
            }

            $quotation->delete();
            new_invoices::where("quotation_id", $id)->whereIn("creator_id", $related_users)->delete();
            new_negative_invoices::where("quotation_id", $id)->whereIn("creator_id", $related_users)->delete();
            new_orders::where("quotation_id", $id)->delete();
        }

        return $quotation;
    }

    public function DeleteNewQuotationsPost(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        $flag = 0;

        foreach ($request->quotation_ids as $i => $key) {
            if ($request->delete_quotations_options[$i]) {
                $delete = $this->DeleteNewQuotations($key, $user);

                if ($delete) {
                    $flag = 1;
                }
            }
        }

        if ($flag) {
            Session::flash('success', __('text.Quotations deleted successfully'));
        }

        return redirect()->back();
    }

    public function DeleteNewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        $quotation = $this->DeleteNewQuotations($id, $user);

        if (!$quotation) {
            return redirect()->back();
        }

        Session::flash('success', __('text.Quotation deleted successfully'));
        return redirect()->back();
    }

    public function DeleteNewInvoices($id, $retailer, $route = NULL)
    {
        $organization_id = $retailer->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($route == NULL) {
            $invoice = new_invoices::where("id", $id)->whereIn("creator_id", $related_users)->first();

            if ($invoice) {
                new_quotations::where("id", $invoice->quotation_id)->whereIn("creator_id", $related_users)->update(["invoice" => 0, "invoice_date" => NULL, "invoice_number" => NULL, "invoice_sent" => 0, "mail_invoice_to" => NULL]);
                // new_negative_invoices::where("quotation_id",$invoice->quotation_id)->where("creator_id",$retailer_id)->delete();
            } else {
                $invoice = new_negative_invoices::where("id", $id)->whereIn("creator_id", $related_users)->first();

                if ($invoice) {
                    new_invoices::where("quotation_id", $invoice->quotation_id)->whereIn("creator_id", $related_users)->update(["has_negative_invoice" => 0, "negative_invoice_sent" => 0, "mail_negative_invoice_to" => NULL]);
                }
            }
        } else {
            if ($route == 'delete-new-invoice') {
                $invoice = new_invoices::where("quotation_id", $id)->whereIn("creator_id", $related_users)->first();

                if ($invoice) {
                    new_quotations::where("id", $id)->whereIn("creator_id", $related_users)->update(["invoice" => 0, "invoice_date" => NULL, "invoice_number" => NULL, "invoice_sent" => 0, "mail_invoice_to" => NULL]);
                    // new_negative_invoices::where("quotation_id",$id)->where("creator_id",$retailer_id)->delete();
                }
            } else {
                $invoice = new_negative_invoices::where("quotation_id", $id)->whereIn("creator_id", $related_users)->first();

                if ($invoice) {
                    new_invoices::where("quotation_id", $id)->whereIn("creator_id", $related_users)->update(["has_negative_invoice" => 0, "negative_invoice_sent" => 0, "mail_negative_invoice_to" => NULL]);
                }
            }
        }

        if ($invoice) {
            $invoice->delete();
        }

        return $invoice;
    }

    public function DeleteNewInvoicesPost(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        $flag = 0;

        foreach ($request->invoice_ids as $i => $key) {
            if ($request->delete_invoices_options[$i]) {
                $delete = $this->DeleteNewInvoices($key, $user);

                if ($delete) {
                    $flag = 1;
                }
            }
        }

        if ($flag) {
            Session::flash('success', __('text.Invoices deleted successfully'));
        }

        return redirect()->back();
    }

    public function DeleteNewInvoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_role = $user->role_id;

        $route = \Route::currentRouteName();
        $invoice = $this->DeleteNewInvoices($id, $user, $route);

        if (!$invoice) {
            return redirect()->back();
        }

        Session::flash('success', __('text.Invoice deleted successfully'));
        return redirect()->back();
    }

    public function UserUpdateFilter(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($request->type == 1) {
            $update = array("filter_text" => $request->filter_text, "filter_month" => $request->filter_month, "filter_year" => $request->filter_year, "filter_status" => $request->filter_status);
        } else {
            $update = array("filter_text_invoice" => $request->filter_text, "filter_month_invoice" => $request->filter_month, "filter_year_invoice" => $request->filter_year);
        }

        User::where("id", $user_id)->update($update);

        // if($request->search)
        // {
        //     if($request->type == 3)
        //     {
        //         $update = array("filter_text" => $request->search["search"]);
        //     }
        //     else
        //     {
        //         $update = array("filter_text_invoice" => $request->search["search"]);
        //     }

        //     User::where("id",$user_id)->update($update);
        // }
        // else
        // {
        //     if($request->type == 1)
        //     {
        //         $update = array("filter_month" => $request->filter_month, "filter_year" => $request->filter_year, "filter_status" => $request->filter_status);
        //     }
        //     else
        //     {
        //         $update = array("filter_month_invoice" => $request->filter_month, "filter_year_invoice" => $request->filter_year);
        //     }

        //     User::where("id",$user_id)->update($update);
        // }

        return;
    }

    public function UpdateTableWidths(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $table_id = $request->table_id;
        $screen_width = $request->screen_width;

        $table_width = table_widths::where("user_id", $user_id)->where("table_id", $table_id)->where("screen_width", $screen_width)->where("ip", $this->ip_address)->first();

        if (!$table_width) {
            $table_width = new table_widths;
        }

        $table_width->user_id = $user_id;
        $table_width->table_id = $table_id;
        $table_width->table_width = $request->table_width;
        $table_width->column_defs = $request->column_defs;
        $table_width->screen_width = $screen_width;
        $table_width->ip = $this->ip_address;
        $table_width->save();

        return;
    }

    public function getTableWidths(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $table_id = $request->table_id;
        $screen_width = $request->screen_width;

        $table_width = table_widths::where("user_id", $user_id)->where("table_id", $table_id)->where("screen_width", $screen_width)->where("ip", $this->ip_address)->first();

        return $table_width;
    }

    public function CustomerInvoices($id = '')
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $new_invoices = new_invoices::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_invoices.quotation_id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_invoices.customer_details')->leftjoin('quotes', 'quotes.id', '=', 'new_invoices.quote_request_id')->where('new_quotations.deleted_at', NULL)->whereIn('new_invoices.creator_id', $related_users)->where('new_invoices.invoice', 1)->orderBy('new_invoices.created_at', 'desc')->select('new_invoices.*', 'new_quotations.quotation_invoice_number', 'new_invoices.id as invoice_id', 'new_invoices.created_at as invoice_date', 'customers_details.name', 'customers_details.family_name', 'quotes.quote_name', 'quotes.quote_familyname')->with('data')->get();
            $new_negative_invoices = new_negative_invoices::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_invoices.quotation_id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_invoices.customer_details')->where('new_quotations.deleted_at', NULL)->whereIn('new_invoices.creator_id', $related_users)->orderBy('new_invoices.created_at', 'desc')->select('new_invoices.*', 'new_quotations.quotation_invoice_number', 'new_invoices.id as invoice_id', 'new_invoices.created_at as invoice_date', 'customers_details.name', 'customers_details.family_name')->with('data')->get();
            $new_invoices = $new_invoices->concat($new_negative_invoices);
        } else {
            $new_invoices = '';
        }

        if ($user->can('customer-invoices')) {
            if ($id) {

                $invoices = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.user_id')->whereIn('custom_quotations.handyman_id', $related_users)->where('custom_quotations.id', $id)->where('custom_quotations.status', '=', 3)->orderBy('custom_quotations.created_at', 'desc')->select('custom_quotations.*', 'custom_quotations.id as invoice_id', 'custom_quotations.created_at as invoice_date', 'users.name', 'users.family_name')->get();

                return view('user.quote_invoices', compact('invoices'));
            } else {
                $invoices = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.user_id')->whereIn('custom_quotations.handyman_id', $related_users)->where('custom_quotations.status', '=', 3)->orderBy('custom_quotations.created_at', 'desc')->select('custom_quotations.*', 'custom_quotations.id as invoice_id', 'custom_quotations.created_at as invoice_date', 'users.name', 'users.family_name')->get();
            }
        } else {
            $invoices = '';
        }

        $invoices = $invoices->concat($new_invoices)->sortByDesc('created_at');

        if ($invoices) {
            return view('user.invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function HandymanQuotationsInvoices($id = '')
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'quotations-invoices') {

            if ($user->can('quotations-invoices')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'commission-invoices') {

            if ($user->can('commission-invoices')) {
                $check = 1;
            }
        }

        if ($check) {
            if ($id) {
                $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->whereIn('quotation_invoices.handyman_id', $related_users)->where('quotation_invoices.quote_id', $id)->where('quotation_invoices.invoice', 1)->orderBy('quotation_invoices.created_at', 'desc')->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivery_date', 'quotation_invoices.delivered', 'quotation_invoices.received', 'quotation_invoices.commission_percentage', 'quotation_invoices.commission', 'quotation_invoices.total_receive', 'quotation_invoices.id as invoice_id', 'quotation_invoices.invoice', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'users.name', 'users.family_name')->get();
            } else {
                $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->whereIn('quotation_invoices.handyman_id', $related_users)->where('quotation_invoices.invoice', 1)->orderBy('quotation_invoices.created_at', 'desc')->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivery_date', 'quotation_invoices.delivered', 'quotation_invoices.received', 'quotation_invoices.commission_percentage', 'quotation_invoices.commission', 'quotation_invoices.total_receive', 'quotation_invoices.id as invoice_id', 'quotation_invoices.invoice', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'users.name', 'users.family_name')->get();
            }

            return view('user.quote_invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function Quotations($id = '')
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $service_fee = $this->gs->service_fee;

        if (\Route::currentRouteName() == 'client-quotations') {
            if ($id) {
                $invoices = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('new_quotations.quote_request_id', $id)->where('new_quotations.approved', 1)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.retailer_delivered', 'new_quotations.customer_received', 'new_quotations.invoice_sent', 'new_quotations.paid', 'new_quotations.invoice', 'new_quotations.quote_request_id', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
            } else {
                $direct = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('new_quotations.user_id', $user_id)->where('new_quotations.approved', 1)->where('new_quotations.quote_request_id', NULL)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
                $in_direct = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('new_quotations.approved', 1)->where('new_quotations.quote_request_id', '!=', NULL)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.retailer_delivered', 'new_quotations.customer_received', 'new_quotations.invoice_sent', 'new_quotations.paid', 'new_quotations.invoice', 'new_quotations.quote_request_id', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();

                $invoices = $direct->concat($in_direct);
            }
        } else {
            if ($id) {
                $invoices = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('quotes.status', '<', 3)->where('new_quotations.quote_request_id', $id)->where('new_quotations.invoice', 0)->where('new_quotations.approved', 1)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
            } else {
                $invoices = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('quotes.status', '<', 3)->where('new_quotations.invoice', 0)->where('new_quotations.approved', 1)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
            }
        }

        return view('user.client_quote_invoices', compact('invoices', 'service_fee'));
    }

    public function ClientNewQuotations($id = "")
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $gs = Generalsetting::where('backend', 1)->first();
        $service_fee = $gs->service_fee;

        if (\Route::currentRouteName() == 'client-quotations' || \Route::currentRouteName() == 'client-new-quotations') {
            if ($id) {
                $invoices = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('new_quotations.quote_request_id', $id)->where('new_quotations.approved', 1)->where('new_quotations.quote_request_id', '!=', NULL)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.retailer_delivered', 'new_quotations.customer_received', 'new_quotations.invoice_sent', 'new_quotations.paid', 'new_quotations.invoice', 'new_quotations.quote_request_id', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
            } else {
                $direct = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('new_quotations.user_id', $user_id)->where('new_quotations.approved', 1)->where('new_quotations.quote_request_id', NULL)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
                $in_direct = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('new_quotations.approved', 1)->where('new_quotations.quote_request_id', '!=', NULL)->orderBy('new_quotations.created_at', 'desc')->select('quotes.*', 'new_quotations.retailer_delivered', 'new_quotations.customer_received', 'new_quotations.invoice_sent', 'new_quotations.paid', 'new_quotations.invoice', 'new_quotations.quote_request_id', 'new_quotations.review_text', 'new_quotations.ask_customization', 'new_quotations.approved', 'new_quotations.accepted', 'new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations.tax_amount as tax', 'new_quotations.subtotal', 'new_quotations.grand_total', 'new_quotations.created_at as invoice_date', 'new_quotations.accept_date', 'new_quotations.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();

                $invoices = $direct->concat($in_direct);
            }
        } else {
            if ($id) {
                $invoices = new_invoices::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_invoices.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('quotes.status', '=', 3)->where('new_invoices.quote_request_id', $id)->where('new_invoices.approved', 1)->where('new_invoices.invoice_sent', 1)->where('new_invoices.quote_request_id', '!=', NULL)->orderBy('new_invoices.created_at', 'desc')->select('quotes.*', 'new_invoices.retailer_delivered', 'new_invoices.customer_received', 'new_invoices.invoice_sent', 'new_invoices.paid', 'new_invoices.invoice', 'new_invoices.quote_request_id', 'new_invoices.review_text', 'new_invoices.ask_customization', 'new_invoices.approved', 'new_invoices.accepted', 'new_invoices.quotation_id as invoice_id', 'new_invoices.invoice_number', 'new_invoices.tax_amount as tax', 'new_invoices.subtotal', 'new_invoices.grand_total', 'new_invoices.created_at as invoice_date', 'new_invoices.accept_date', 'new_invoices.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
            } else {
                $direct = new_invoices::leftjoin('users', 'users.id', '=', 'new_invoices.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('new_invoices.user_id', $user_id)->where('new_invoices.approved', 1)->where('new_invoices.invoice_sent', 1)->where('new_invoices.quote_request_id', NULL)->orderBy('new_invoices.created_at', 'desc')->select('new_invoices.*', 'new_invoices.quotation_id as invoice_id', 'new_invoices.created_at as invoice_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();
                $in_direct = new_invoices::leftjoin('quotes', 'quotes.id', '=', 'new_invoices.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_invoices.creator_id')->leftjoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')->leftjoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')->where('quotes.user_id', $user_id)->where('quotes.status', '=', 3)->where('new_invoices.approved', 1)->where('new_invoices.invoice_sent', 1)->where('new_invoices.quote_request_id', '!=', NULL)->orderBy('new_invoices.created_at', 'desc')->select('quotes.*', 'new_invoices.retailer_delivered', 'new_invoices.customer_received', 'new_invoices.invoice_sent', 'new_invoices.paid', 'new_invoices.invoice', 'new_invoices.quote_request_id', 'new_invoices.review_text', 'new_invoices.ask_customization', 'new_invoices.approved', 'new_invoices.accepted', 'new_invoices.quotation_id as invoice_id', 'new_invoices.invoice_number', 'new_invoices.tax_amount as tax', 'new_invoices.subtotal', 'new_invoices.grand_total', 'new_invoices.created_at as invoice_date', 'new_invoices.accept_date', 'new_invoices.delivery_date', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.phone')->get();

                $invoices = $direct->concat($in_direct);
            }
        }

        return view('user.client_quote_invoices', compact('invoices', 'service_fee'));
    }

    public function CustomQuotations($id = '')
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        if ($id) {
            $invoices = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.handyman_id')->where('custom_quotations.user_id', $user_id)->where('custom_quotations.id', $id)->where('custom_quotations.approved', 1)->orderBy('custom_quotations.created_at', 'desc')->select('custom_quotations.*', 'custom_quotations.id as invoice_id', 'custom_quotations.created_at as invoice_date', 'users.name', 'users.family_name', 'users.address', 'users.postcode', 'users.city', 'users.phone')->get();
        } else {
            $invoices = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.handyman_id')->where('custom_quotations.user_id', $user_id)->where('custom_quotations.approved', 1)->orderBy('custom_quotations.created_at', 'desc')->select('custom_quotations.*', 'custom_quotations.id as invoice_id', 'custom_quotations.created_at as invoice_date', 'users.name', 'users.family_name', 'users.address', 'users.postcode', 'users.city', 'users.phone')->get();
        }

        return view('user.client_quote_invoices', compact('invoices'));
    }

    public function QuotationsInvoices($id = '')
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        if ($id) {
            $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->where('quotes.user_id', $user_id)->where('quotes.status', '=', 3)->where('quotation_invoices.quote_id', $id)->where('quotation_invoices.invoice', 1)->where('quotation_invoices.approved', 1)->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivered', 'quotation_invoices.received', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.id as invoice_id', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'quotation_invoices.accept_date', 'quotation_invoices.delivery_date', 'users.name', 'users.family_name', 'users.address', 'users.postcode', 'users.city', 'users.phone')->orderBy('quotation_invoices.created_at', 'desc')->get();
        } else {
            $invoices = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotation_invoices.handyman_id')->where('quotes.user_id', $user_id)->where('quotes.status', '=', 3)->where('quotation_invoices.invoice', 1)->where('quotation_invoices.approved', 1)->select('quotes.*', 'quotation_invoices.review_text', 'quotation_invoices.delivered', 'quotation_invoices.received', 'quotation_invoices.ask_customization', 'quotation_invoices.approved', 'quotation_invoices.accepted', 'quotation_invoices.id as invoice_id', 'quotation_invoices.quotation_invoice_number', 'quotation_invoices.tax', 'quotation_invoices.subtotal', 'quotation_invoices.grand_total', 'quotation_invoices.created_at as invoice_date', 'quotation_invoices.accept_date', 'quotation_invoices.delivery_date', 'users.name', 'users.family_name', 'users.address', 'users.postcode', 'users.city', 'users.phone')->orderBy('quotation_invoices.created_at', 'desc')->get();
        }

        return view('user.client_quote_invoices', compact('invoices'));
    }

    public function QuoteRequest($id)
    {
        $request = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('models', 'models.id', '=', 'quotes.quote_type')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('colors', 'colors.id', '=', 'quotes.quote_color')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->where('quotes.id', $id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'product_models.model as model_name', 'models.cat_name as type_title', 'colors.title as color')->withCount('quotations')->first();

        $q_a = requests_q_a::where('request_id', $id)->get();

        return view('user.client_quote_request', compact('request',  'q_a'));
    }

    public function DownloadQuoteRequest($id, $user_id = NULL, $role = NULL, $type = NULL)
    {
        if ($type != 'api') {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            $role = $user->role_id;
        }

        $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('models', 'models.id', '=', 'quotes.quote_type')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('colors', 'colors.id', '=', 'quotes.quote_color')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->where('quotes.id', $id)->where('quotes.user_id', $user_id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'product_models.model as model_name', 'models.cat_name as type_title', 'colors.title as color')->first();

        $q_a = requests_q_a::where('request_id', $id)->get();

        if ($quote) {

            $quote_number = $quote->quote_number;

            $filename = $quote_number . '.pdf';

            if ($role == 3) {
                $file = public_path() . '/assets/adminQuotesPDF/' . $filename;
            } else {
                $file = public_path() . '/assets/quotesPDF/' . $filename;
            }

            if (!file_exists($file)) {

                ini_set('max_execution_time', 180);

                $pdf = PDF::loadView('admin.user.pdf_quote', compact('quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                if ($role == 3) {
                    $pdf->save(public_path() . '/assets/adminQuotesPDF/' . $filename);
                } else {
                    $pdf->save(public_path() . '/assets/quotesPDF/' . $filename);
                }
            }

            if ($type != 'api') {
                if ($role == 3) {
                    return response()->download(public_path("assets/adminQuotesPDF/{$filename}"));
                } else {
                    return response()->download(public_path("assets/quotesPDF/{$filename}"));
                }
            } else {
                return 'true';
            }
        } else {

            if ($type != 'api') {
                return redirect('aanbieder/quotation-requests');
            } else {
                return 'Invalid';
            }
        }
    }

    public function DownloadQuoteRequestFile($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role = $user->role_id;

        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        $quote = quotes::where('id', $id)->where('user_id', $user_id)->first();

        if (!$quote) {
            return redirect()->back();
        }

        $filename = $quote->quote_file1;

        if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {

            $url = $this->gs1->site . 'public/assets/quotes_user_files/' . $filename;
        } else {
            $url = 'http://localhost/vloerofferte/public/assets/quotes_user_files/' . $filename;
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        copy($url, $tempFile);

        return response()->download($tempFile, $filename);
    }

    public function DownloadQuoteInvoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user->can('download-quote-invoice')) {
            $invoice = quotation_invoices::where('id', $id)->whereIn('handyman_id', $related_users)->first();

            if (!$invoice) {
                return redirect()->route('quotations');
            }

            $quotation_invoice_number = $invoice->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            if ($user_role == 2 && $invoice->invoice != 1) {
                return response()->download(public_path("assets/quotationsPDF/HandymanQuotations/{$filename}"));
            } else {
                return response()->download(public_path("assets/quotationsPDF/{$filename}"));
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function DownloadCommissionInvoice($id)
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

        $invoice = new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->first();

        if (!$invoice) {
            return redirect()->route('quotations');
        }

        $commission_invoice_number = $invoice->commission_invoice_number;

        $filename = $commission_invoice_number . '.pdf';

        return response()->download(public_path("assets/CommissionInvoices/{$organization_id}/{$filename}"));
    }

    public function DownloadCustomQuotation($id)
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

        if ($user->can('download-custom-quotation')) {
            $invoice = custom_quotations::where('id', $id)->whereIn('handyman_id', $related_users)->first();

            if (!$invoice) {
                return redirect()->back();
            }

            $quotation_invoice_number = $invoice->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            return response()->download(public_path("assets/customQuotations/{$filename}"));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function DownloadClientQuoteInvoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_quotations.id', $id)->where(function ($query) use ($user_id) {
            $query->where('quotes.user_id', $user_id)->orWhere('new_quotations.user_id', $user_id);
        })->first();

        $creator_id = $invoice->creator_id;
        $organization_id = User::where("id", $creator_id)->first()->organization->id;

        if (!$invoice) {
            return redirect()->back();
        }

        $quotation_invoice_number = $invoice->quotation_invoice_number;
        $filename = $quotation_invoice_number . '.pdf';

        if (\Route::currentRouteName() == 'download-client-quote-invoice') {

            // if($invoice->draft && !file_exists(public_path("assets/draftQuotations/{$filename}")))
            // {
            //     copy(public_path("assets/newQuotations/{$filename}"), public_path("assets/draftQuotations/{$filename}"));
            // }

            return response()->download($invoice->draft ? public_path("assets/draftQuotations/{$organization_id}/{$filename}") : public_path("assets/newQuotations/{$organization_id}/{$filename}"));
        } elseif (\Route::currentRouteName() == 'download-client-invoice-pdf') {
            $invoice_number = $invoice->invoice_number;
            $filename = $invoice_number . '.pdf';

            return response()->download(public_path("assets/newInvoices/{$organization_id}/{$filename}"));
        } else {
            return response()->download(public_path("assets/CustomerQuotations/{$organization_id}/{$filename}"));
        }
    }

    public function DownloadClientCustomQuoteInvoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = custom_quotations::where('custom_quotations.id', $id)->where('custom_quotations.user_id', $user_id)->first();

        if (!$invoice) {
            return redirect()->back();
        }

        $quotation_invoice_number = $invoice->quotation_invoice_number;

        $filename = $quotation_invoice_number . '.pdf';

        return response()->download(public_path("assets/customQuotations/{$filename}"));
    }

    public function AskCustomization(Request $request)
    {
        $id = $request->invoice_id;
        $review_text = $request->review_text;

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->where('new_quotations.id', $id)->where(function ($query) use ($user_id) {
            $query->where('quotes.user_id', $user_id)->orWhere('new_quotations.user_id', $user_id);
        })->first();

        if (!$invoice) {
            return redirect()->back();
        }

        new_quotations::where('id', $id)->update(['ask_customization' => 1, 'review_text' => $review_text]);

        $retailer_email = $invoice->email;
        $user_name = $invoice->name;

        \Mail::send(array(), array(), function ($message) use ($retailer_email, $user_name, $invoice, $user) {
            $message->to($retailer_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject(__('text.Quotation Review Request!'))
                ->html("Dear Mr/Mrs " . $user_name . ",<br><br>Mr/Mrs " . $user->name . " submitted review request against your quotation QUO# " . $invoice->quotation_invoice_number . "<br>Kindly take further action on this request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        $admin_email = $this->sl->admin_email;

        \Mail::send(array(), array(), function ($message) use ($admin_email, $user_name, $invoice, $user) {
            $message->to($admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject('Quotation Review Request!')
                ->html("A quotation review request has been submitted by Mr/Mrs " . $user->name . " against quotation QUO# " . $invoice->quotation_invoice_number . "<br>Retailer: " . $user_name . "<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        Session::flash('success', __('text.Request submitted successfully!'));

        return redirect()->back();
    }

    public function SendMsg(Request $request)
    {
        $id = $request->invoice_id;
        $msg = $request->msg;

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->where('new_quotations.id', $id)->where(function ($query) use ($user_id) {
            $query->where('quotes.user_id', $user_id)->orWhere('new_quotations.user_id', $user_id);
        })->first();

        if (!$invoice) {
            return redirect()->back();
        }

        $post = new client_quotation_msgs;
        $post->text = $msg;
        $post->quotation_id = $id;
        $post->save();

        $retailer_email = $invoice->email;
        $user_name = $invoice->name;

        \Mail::send(array(), array(), function ($message) use ($retailer_email, $user_name, $invoice, $user) {
            $message->to($retailer_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                ->subject(__('text.Message for quotation'))
                ->html("Dear Mr/Mrs " . $user_name . ",<br><br>Mr/Mrs " . $user->name . " sent a message regarding your quotation QUO# " . $invoice->quotation_invoice_number . "<br>Kindly take further action on this request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        $admin_email = $this->sl->admin_email;

        \Mail::send(array(), array(), function ($message) use ($admin_email, $user_name, $invoice, $user) {
            $message->to($admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject('Quotation Review Request!')
                ->html("A message was delivered by Mr/Mrs " . $user->name . " against quotation QUO# " . $invoice->quotation_invoice_number . "<br>Retailer: " . $user_name . "<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        Session::flash('success', __('text.Message delivered successfully!'));

        return redirect()->back();
    }

    public function Messages($id)
    {
        client_quotation_msgs::where('quotation_id', $id)->update(["seen" => 1]);
        $messages = client_quotation_msgs::where('quotation_id', $id)->get();

        return view('user.messages', compact('messages'));
    }

    public function CustomQuotationAskCustomization(Request $request)
    {
        $id = $request->invoice_id;
        $review_text = $request->review_text;

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.handyman_id')->where('custom_quotations.id', $id)->where('custom_quotations.user_id', $user_id)->first();

        if (!$invoice) {
            return redirect()->back();
        }

        custom_quotations::where('id', $id)->update(['ask_customization' => 1, 'review_text' => $review_text]);

        $handyman_email = $invoice->email;
        $user_name = $invoice->name;

        \Mail::send(array(), array(), function ($message) use ($handyman_email, $user_name, $invoice, $user) {
            $message->to($handyman_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject(__('text.Quotation Review Request!'))
                ->html("Dear Mr/Mrs " . $user_name . ",<br><br>Mr/Mrs " . $user->name . " submitted review request against your quotation QUO# " . $invoice->quotation_invoice_number . "<br>Kindly take further action on this request.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });


        /*$admin_email = $this->sl->admin_email;

        \Mail::send(array(), array(), function ($message) use($admin_email,$user_name,$invoice,$user) {
            $message->to($admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                ->subject('Quotation Review Request!')
                ->html("A quotation review request has been submitted by Mr/Mrs ".$user->name.' '.$user->family_name." against quotation QUO# ".$invoice->quotation_invoice_number."<br>Handyman: ".$user_name."<br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
        });*/


        Session::flash('success', __('text.Request submitted successfully!'));

        return redirect()->back();
    }

    public function AcceptQuotationPieppiep(Request $request)
    {
        $now = date('d-m-Y H:i:s');
        $time = strtotime($now);
        $time = date('H:i:s', $time);
        $delivery_date = $request->delivery_date . ' ' . $time;

        $time1 = strtotime($now);
        $time1 = date('H:i', $time1);
        $delivery_date1 = date('Y-m-d', strtotime($request->delivery_date)) . ' ' . $time1;

        $time2 = strtotime($now);
        $time2 = date('H:i:s', $time2);
        $delivery_date2 = date('Y-m-d', strtotime($request->delivery_date)) . ' ' . $time2;

        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $invoice = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')
            ->leftjoin('new_quotations_data', 'new_quotations_data.quotation_id', '=', 'new_quotations.id')
            ->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')
            ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
            ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
            ->where('new_quotations.id', $request->invoice_id)->where('quotes.user_id', $user_id)
            ->select('new_quotations_data.*', 'new_quotations.document_date', 'new_quotations.created_at', 'new_quotations.quote_request_id as quote_id', 'new_quotations.description as other_info', 'new_quotations.tax_amount as tax', 'new_quotations.grand_total', 'new_quotations.quotation_invoice_number', 'new_quotations.creator_id', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.email', 'organizations.phone', 'organizations.compressed_photo', 'organizations.tax_number', 'organizations.company_name', 'organizations.registration_number', 'organizations.quotation_prefix', 'organizations.email as organization_email')->get();

        if (count($invoice) == 0) {
            return redirect()->back();
        }

        $find_appointment = quotation_appointments::where('quotation_id', $request->invoice_id)->where('title', 'Delivery Date')->latest('id')->first();

        if ($find_appointment) {
            $find_appointment->start = $delivery_date1;
            $find_appointment->end = $delivery_date2;
            $find_appointment->save();
        } else {
            $delivery_appointment = new quotation_appointments;
            $delivery_appointment->quotation_id = $request->invoice_id;
            $delivery_appointment->user_id = $invoice[0]->creator_id;
            $delivery_appointment->title = 'Delivery Date';
            $delivery_appointment->start = $delivery_date1;
            $delivery_appointment->end = $delivery_date2;
            $delivery_appointment->save();
        }

        quotes::where('id', $invoice[0]->quote_id)->update(['status' => 2, 'quote_delivery' => $delivery_date, 'quote_zipcode' => $request->delivery_address, 'quote_postcode' => $request->postcode, 'quote_city' => $request->city]);

        //        if($request->update == 1)
        //        {
        //            User::where('id',$user_id)->update(['address' => $request->delivery_address,'postcode' => $request->postcode,'city' => $request->city]);
        //        }

        $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('models', 'models.id', '=', 'quotes.quote_type')->leftjoin('colors', 'colors.id', '=', 'quotes.quote_color')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->leftjoin('users', 'users.id', '=', 'quotes.user_id')->leftjoin('universal_customers_details', 'universal_customers_details.user_id', '=', 'users.id')->where('quotes.id', $invoice[0]->quote_id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'product_models.model as model_name', 'models.cat_name as type_title', 'colors.title as color', 'universal_customers_details.postcode', 'universal_customers_details.city', 'universal_customers_details.address')->first();

        $quotation_invoice_number = $invoice[0]->quotation_invoice_number;
        $filename = $quotation_invoice_number . '.pdf';
        $service_fee = $this->gs->service_fee;

        new_quotations::where('id', $request->invoice_id)->update(['service_fee' => $service_fee, 'status' => 2, 'ask_customization' => 0, 'accepted' => 1, 'accept_date' => $now, 'delivery_date' => $delivery_date1, 'delivery_date_end' => $delivery_date2]);

        $request = new_quotations::where('id', $request->invoice_id)->with('data')->first();
        $user = $invoice[0];
        $client = '';
        $delivery_date = date('d-m-Y', strtotime($request->delivery_date)) . ' - ' . date('d-m-Y', strtotime($request->delivery_date_end));
        $installation_date = date('d-m-Y', strtotime($request->installation_date)) . ' - ' . date('d-m-Y', strtotime($request->installation_date_end));

        $request->products = $request->data;
        // $request->retailer_delivery_date = $request->delivery_date;
        $request->total_amount = $request->grand_total;
        $product_titles = array();
        $color_titles = array();
        $model_titles = array();
        $product_descriptions = array();
        $vat_percentages = array();

        foreach ($request->products as $i => $key) {

            $vat_percentages[] = vats::where('id', $key->vat_id)->pluck('vat_percentage')->first();
            $total_discount[$i] = str_replace('.', ',', $key->total_discount);
            $request->total_discount = $total_discount;

            $rate[$i] = $key->rate;
            $request->rate = $rate;

            $qty[$i] = str_replace('.', ',', $key->qty);
            $request->qty = $qty;

            $total[$i] = $key->amount;
            $request->total = $total;

            $measure[$i] = $key->measure;
            $request->measure = $measure;

            $price_before_labor[$i] = $key->price_before_labor;
            $request->price_before_labor = $price_before_labor;
            $request->price_before_labor_old = $price_before_labor;

            $estimated_price_quantity[$i] = $key->box_quantity;
            $request->estimated_price_quantity = $estimated_price_quantity;

            if ($key->item_id != 0) {

                $product_titles[] = items::where('id', $key->item_id)->pluck('cat_name')->first();
                $color_titles[] = '';
                $model_titles[] = '';
            } elseif ($key->service_id != 0) {

                $product_titles[] = Service::where('id', $key->service_id)->pluck('title')->first();
                $color_titles[] = '';
                $model_titles[] = '';
            } else {
                if ($key->product_id != 0) {
                    $product_titles[] = product::where('id', $key->product_id)->pluck('title')->first();
                    $color_titles[] = colors::where('id', $key->color)->pluck('title')->first();
                    $model_titles[] = product_models::where('id', $key->model_id)->pluck('model')->first();
                } else {
                    $product_titles[] = '';
                    $color_titles[] = '';
                    $model_titles[] = '';
                }
            }

            $product_descriptions[] = $key->description;
            $calculations[$i] = $key->calculations()->get();
            $request->calculations = $calculations;

            if ($key->item_id != 0) {
                $request->products[$i] = $key->item_id . 'I';
            } elseif ($key->service_id != 0) {
                $request->products[$i] = $key->service_id . 'S';
            } else {
                $request->products[$i] = $key->product_id;
            }
        }

        ini_set('max_execution_time', 180);

        $date = $invoice[0]->document_date;
        $role = 'retailer';
        $form_type = 1;
        $re_edit = 1;
        $organization_id = User::where("id", $invoice[0]->creator_id)->first()->organization->id;

        $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 're_edit', 'form_type', 'role', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
        $file = public_path() . '/assets/newQuotations/' . $organization_id . '/' . $filename;
        $pdf->save($file);

        $q_a = requests_q_a::where('request_id', $quote->id)->get();

        $quote_number = $quote->quote_number;

        $filename = $quote_number . '.pdf';

        $role = 3;

        ini_set('max_execution_time', 180);

        $pdf = PDF::loadView('admin.user.pdf_quote', compact('delivery_date', 'quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

        $pdf->save(public_path() . '/assets/adminQuotesPDF/' . $filename);

        $role = 2;

        $pdf = PDF::loadView('admin.user.pdf_quote', compact('delivery_date', 'quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

        $pdf->save(public_path() . '/assets/quotesPDF/' . $filename);

        $retailer_email = $invoice[0]->email;
        $user_name = $invoice[0]->name;
        $user_lastName = $invoice[0]->family_name;
        $retailer_name = $user_name . ' ' . $user_lastName;
        $company_name = $invoice[0]->company_name;

        $link = url('/') . '/aanbieder/dashboard';

        if ($this->lang->lang == 'du') {
            $msg = "Beste " . $user_name . ",<br><br>Gefeliciteerd de klant heeft je offerte geaccepteerd QUO# " . $invoice[0]->quotation_invoice_number . "<br>Zodra, de klant het volledig bedrag heeft voldaan ontvang je de contactgegevens, bezorgadres en bezorgmoment. Je ontvang van ons een mail als de klant heeft betaald, tot die tijd adviseren we je de goederen nog niet te leveren. <a href='" . $link . "'>Klik hier</a> om naar je dashboard te gaan.<br><br>Met vriendelijke groeten,<br><br>Pieppiep";
        } else {
            $msg = "Dear " . $user_name . ",<br><br>Your quotation QUO# " . $invoice[0]->quotation_invoice_number . " has been accepted by your client.<br>You can convert your quotation into invoice once job is completed,<br><br>Kind regards,<br><br>Klantenservice<br><br>Pieppiep";
        }

        try {
            \Mail::send(array(), array(), function ($message) use ($msg, $retailer_email, $user_name, $invoice) {
                $message->to($retailer_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject(__('text.Quotation Accepted!'))
                    ->html($msg, 'text/html');
            });
        } catch (\Exception $e) {
        }

        try {
            $admin_email = $this->sl->admin_email;

            \Mail::send(array(), array(), function ($message) use ($admin_email, $user_name, $invoice) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                    ->subject('Quotation Accepted!')
                    ->html("A quotation QUO# " . $invoice[0]->quotation_invoice_number . " has been accepted.<br>Retailer: " . $user_name . "<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        } catch (\Exception $e) {
        }

        Session::flash('success', __('text.Quotation accepted successfully!'));

        return redirect()->back();
    }

    public function AcceptQuotation($request, $user_id)
    {
        $now = date('d-m-Y H:i:s');
        $time = strtotime($now);
        $time = date('H:i:s', $time);
        $delivery_date = $request->delivery_date . ' ' . $time;

        $time1 = strtotime($now);
        $time1 = date('H:i', $time1);
        $delivery_date1 = date('Y-m-d', strtotime($request->delivery_date)) . ' ' . $time1;

        $time2 = strtotime($now);
        $time2 = date('H:i:s', $time2);
        $delivery_date2 = date('Y-m-d', strtotime($request->delivery_date)) . ' ' . $time2;

        $invoice = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')
            ->leftjoin('new_quotations_data', 'new_quotations_data.quotation_id', '=', 'new_quotations.id')
            ->leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')
            ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
            ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
            ->where('new_quotations.id', $request->invoice_id)->where('quotes.user_id', $user_id)
            ->select('new_quotations_data.*', 'new_quotations.document_date', 'new_quotations.created_at', 'new_quotations.quote_request_id as quote_id', 'new_quotations.description as other_info', 'new_quotations.tax_amount as tax', 'new_quotations.grand_total', 'new_quotations.quotation_invoice_number', 'new_quotations.creator_id', 'users.name', 'users.family_name', 'organizations.address', 'organizations.postcode', 'organizations.city', 'organizations.email', 'organizations.phone', 'organizations.compressed_photo', 'organizations.tax_number', 'organizations.company_name', 'organizations.registration_number', 'organizations.quotation_prefix', 'organizations.email as organization_email')->get();

        if (count($invoice) == 0) {
            return 'false';
        }

        $find_appointment = quotation_appointments::where('quotation_id', $request->invoice_id)->where('title', 'Delivery Date')->latest('id')->first();

        if ($find_appointment) {
            $find_appointment->start = $delivery_date1;
            $find_appointment->end = $delivery_date2;
            $find_appointment->save();
        } else {
            $delivery_appointment = new quotation_appointments;
            $delivery_appointment->quotation_id = $request->invoice_id;
            $delivery_appointment->user_id = $invoice[0]->creator_id;
            $delivery_appointment->title = 'Delivery Date';
            $delivery_appointment->start = $delivery_date1;
            $delivery_appointment->end = $delivery_date2;
            $delivery_appointment->save();
        }

        quotes::where('id', $invoice[0]->quote_id)->update(['status' => 2, 'quote_delivery' => $delivery_date, 'quote_zipcode' => $request->delivery_address, 'quote_postcode' => $request->postcode, 'quote_city' => $request->city]);

        //        if($request->update == 1)
        //        {
        //            User::where('id',$user_id)->update(['address' => $request->delivery_address,'postcode' => $request->postcode,'city' => $request->city]);
        //        }

        $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('models', 'models.id', '=', 'quotes.quote_type')->leftjoin('colors', 'colors.id', '=', 'quotes.quote_color')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->leftjoin('universal_customers_details', 'universal_customers_details.user_id', '=', 'quotes.user_id')->where('quotes.id', $invoice[0]->quote_id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'product_models.model as model_name', 'models.cat_name as type_title', 'colors.title as color', 'universal_customers_details.postcode', 'universal_customers_details.city', 'universal_customers_details.address')->first();

        $quotation_invoice_number = $invoice[0]->quotation_invoice_number;
        $filename = $quotation_invoice_number . '.pdf';
        $service_fee = $this->gs->service_fee;

        new_quotations::where('id', $request->invoice_id)->update(['service_fee' => $service_fee, 'status' => 2, 'ask_customization' => 0, 'accepted' => 1, 'accept_date' => $now, 'delivery_date' => $delivery_date1, 'delivery_date_end' => $delivery_date2]);

        $request = new_quotations::where('id', $request->invoice_id)->with('data')->first();
        $user = $invoice[0];
        $client = '';
        $delivery_date = date('d-m-Y', strtotime($request->delivery_date)) . ' - ' . date('d-m-Y', strtotime($request->delivery_date_end));
        $installation_date = date('d-m-Y', strtotime($request->installation_date)) . ' - ' . date('d-m-Y', strtotime($request->installation_date_end));

        $request->products = $request->data;
        // $request->retailer_delivery_date = $request->delivery_date;
        $request->total_amount = $request->grand_total;

        $product_titles = array();
        $color_titles = array();
        $model_titles = array();
        $product_descriptions = array();
        $vat_percentages = array();

        foreach ($request->products as $i => $key) {

            $vat_percentages[] = vats::where('id', $key->vat_id)->pluck('vat_percentage')->first();
            $total_discount[$i] = str_replace('.', ',', $key->total_discount);
            $request->total_discount = $total_discount;

            $rate[$i] = $key->rate;
            $request->rate = $rate;

            $qty[$i] = str_replace('.', ',', $key->qty);
            $request->qty = $qty;

            $total[$i] = $key->amount;
            $request->total = $total;

            $measure[$i] = $key->measure;
            $request->measure = $measure;

            $price_before_labor[$i] = $key->price_before_labor;
            $request->price_before_labor = $price_before_labor;

            $estimated_price_quantity[$i] = $key->box_quantity;
            $request->estimated_price_quantity = $estimated_price_quantity;

            if ($key->item_id != 0) {

                $product_titles[] = items::where('id', $key->item_id)->pluck('cat_name')->first();
                $color_titles[] = '';
                $model_titles[] = '';
            } elseif ($key->service_id != 0) {

                $product_titles[] = Service::where('id', $key->service_id)->pluck('title')->first();
                $color_titles[] = '';
                $model_titles[] = '';
            } else {
                if ($key->product_id != 0) {
                    $product_titles[] = product::where('id', $key->product_id)->pluck('title')->first();
                    $color_titles[] = colors::where('id', $key->color)->pluck('title')->first();
                    $model_titles[] = product_models::where('id', $key->model_id)->pluck('model')->first();
                } else {
                    $product_titles[] = '';
                    $color_titles[] = '';
                    $model_titles[] = '';
                }
            }

            $product_descriptions[] = $key->description;
            $calculations[$i] = $key->calculations()->get();
            $request->calculations = $calculations;

            if ($key->item_id != 0) {
                $request->products[$i] = $key->item_id . 'I';
            } elseif ($key->service_id != 0) {
                $request->products[$i] = $key->service_id . 'S';
            } else {
                $request->products[$i] = $key->product_id;
            }
        }

        ini_set('max_execution_time', 180);

        $date = $invoice[0]->document_date;
        $role = 'retailer';
        $form_type = 1;
        $re_edit = 1;
        $organization_id = User::where("id", $invoice[0]->creator_id)->first()->organization->id;

        $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 're_edit', 'form_type', 'role', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
        $file = public_path() . '/assets/newQuotations/' . $organization_id . '/' . $filename;
        $pdf->save($file);

        $q_a = requests_q_a::where('request_id', $quote->id)->get();

        $quote_number = $quote->quote_number;

        $filename = $quote_number . '.pdf';

        $role = 3;

        ini_set('max_execution_time', 180);

        $pdf = PDF::loadView('admin.user.pdf_quote', compact('delivery_date', 'quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

        $pdf->save(public_path() . '/assets/adminQuotesPDF/' . $filename);

        $role = 2;

        $pdf = PDF::loadView('admin.user.pdf_quote', compact('delivery_date', 'quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

        $pdf->save(public_path() . '/assets/quotesPDF/' . $filename);

        $retailer_email = $invoice[0]->email;
        $user_name = $invoice[0]->name;
        $user_lastName = $invoice[0]->family_name;
        $retailer_name = $user_name . ' ' . $user_lastName;
        $company_name = $invoice[0]->company_name;

        $link = url('/') . '/aanbieder/dashboard';

        if ($this->lang->lang == 'du') {
            $msg = "Beste " . $user_name . ",<br><br>Gefeliciteerd de klant heeft je offerte geaccepteerd QUO# " . $invoice[0]->quotation_invoice_number . "<br>Zodra, de klant het volledig bedrag heeft voldaan ontvang je de contactgegevens, bezorgadres en bezorgmoment. Je ontvang van ons een mail als de klant heeft betaald, tot die tijd adviseren we je de goederen nog niet te leveren. <a href='" . $link . "'>Klik hier</a> om naar je dashboard te gaan.<br><br>Met vriendelijke groeten,<br><br>Pieppiep";
        } else {
            $msg = "Congratulations! Dear Mr/Mrs " . $user_name . ",<br><br>Your quotation QUO# " . $invoice[0]->quotation_invoice_number . " has been accepted by your client.<br>You can convert your quotation into invoice once job is completed,<br><br>Kind regards,<br><br>Klantenservice<br><br>Pieppiep";
        }

        try {
            \Mail::send(array(), array(), function ($message) use ($msg, $retailer_email, $user_name, $invoice) {
                $message->to($retailer_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject(__('text.Quotation Accepted!'))
                    ->html($msg, 'text/html');
            });
        } catch (\Exception $e) {
        }

        try {
            $admin_email = $this->sl->admin_email;

            \Mail::send(array(), array(), function ($message) use ($admin_email, $user_name, $invoice) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                    ->subject('Quotation Accepted!')
                    ->html("A quotation QUO# " . $invoice[0]->quotation_invoice_number . " has been accepted.<br>Retailer: " . $user_name . "<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });
        } catch (\Exception $e) {
        }

        return 'true';
    }

    public function PayQuotation($data, $pay_invoice_id, $language, $user_id)
    {
        $quote_id = $data->id;
        $retailer_id = $data->creator_id;
        $quotation_invoice_number = $data->quotation_invoice_number;

        $total_mollie = number_format((float)$data->grand_total, 2, '.', '');
        $settings = Generalsetting::where('backend', 1)->first();
        $description = 'Payment for Quotation No. ' . $quotation_invoice_number;

        $inv_encrypt = Crypt::encrypt($pay_invoice_id);
        $commission_percentage = $settings->commission_percentage;
        $commission = $total_mollie * ($commission_percentage / 100);
        $commission = number_format((float)$commission, 2, '.', '');
        //        $commission_vat = ($commission/(21 + 100)) * 100;
        //        $commission_vat = $commission - $commission_vat;

        $total_receive = $total_mollie - $commission;
        $total_receive = number_format((float)$total_receive, 2, '.', '');

        $commission_invoice_number = explode('-',  $quotation_invoice_number);

        if (count($commission_invoice_number) > 2) {
            unset($commission_invoice_number[1]);
            $commission_invoice_number = implode("-", $commission_invoice_number);
        } else {
            $commission_invoice_number = $quotation_invoice_number;
        }

        $vloerofferte_url = Generalsetting::where('backend', 0)->pluck('site')->first();

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($settings->mollie);
        $payment = $mollie->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => $total_mollie, // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $description,
            'webhookUrl' => route('webhooks.quotation_payment'),
            'redirectUrl' => $vloerofferte_url . 'aanbieder/quotation-payment-redirect-page/' . $inv_encrypt,
            "metadata" => [
                "invoice_id" => $pay_invoice_id,
                "quote_id" => $quote_id,
                "retailer_id" => $retailer_id,
                "quotation_invoice_number" => $quotation_invoice_number,
                "commission_invoice_number" => $commission_invoice_number,
                "paid_amount" => $total_mollie,
                "commission_percentage" => $commission_percentage,
                "commission" => $commission,
                "total_receive" => $total_receive,
                "language" => $language,
                "user_id" => $user_id
            ],
        ]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function PayQuotationPieppiep(Request $request)
    {
        $pay_invoice_id = $request->pay_invoice_id;
        $data = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_quotations.id', $pay_invoice_id)->select('quotes.*', 'new_quotations.service_fee', 'new_quotations.grand_total', 'new_quotations.creator_id', 'new_quotations.user_id as customer_id', 'new_quotations.quotation_invoice_number', 'new_quotations.accept_date', 'new_quotations.delivery_date')->first();

        $service_fee = $data->service_fee;
        $accept_date = $data->accept_date;
        $delivery_date = $data->delivery_date;

        $second = 1000;
        $minute = $second * 60;
        $hour = $minute * 60;
        $day = $hour * 24;

        if ($accept_date !== NULL && $delivery_date !== NULL) {
            $now1 = date("d-m-Y H:i:s", strtotime('+6 hours', strtotime($accept_date)));
            $now1 = strtotime($now1) * 1000;

            $countDown_accept = strtotime($accept_date) * 1000;
            $countDown_delivery = strtotime($delivery_date) * 1000;

            $dif = $countDown_delivery - $countDown_accept;

            $now = date('d-m-Y H:i:s');
            $now = strtotime($now) * 1000;
            $distance = $countDown_delivery - $now;
            $check = 0;

            if (floor($dif / ($day)) >= 3) {

                if ((floor($distance / ($day)) - 2) < 0) {

                    $check = 1;
                }
            } else {

                $distance1 = $now1 - $now;

                if (floor(($distance1 % ($day)) / ($hour)) <= 0 && floor(($distance1 % ($hour)) / ($minute)) <= 0 && floor(($distance1 % ($minute)) / $second) <= 0) {

                    $check = 1;
                }
            }
        } else {
            $check = 1;
        }

        if ($check == 0) {
            $language = $this->lang->lang;
            $quote_id = $data->id;
            $retailer_id = $data->creator_id;
            $customer_id = $data->customer_id;
            $quotation_invoice_number = $data->quotation_invoice_number;

            $total_mollie = number_format((float)$data->grand_total, 2, '.', '');
            $settings = Generalsetting::where('backend', 1)->first();
            $description = 'Payment for Quotation No. ' . $quotation_invoice_number;

            $inv_encrypt = Crypt::encrypt($pay_invoice_id);
            $commission_percentage = $settings->commission_percentage;
            $commission = $total_mollie * ($commission_percentage / 100);
            $commission = number_format((float)$commission, 2, '.', '');
            //        $commission_vat = ($commission/(21 + 100)) * 100;
            //        $commission_vat = $commission - $commission_vat;

            $total_receive = $total_mollie - $commission;
            $total_receive = number_format((float)$total_receive, 2, '.', '');

            $commission_invoice_number = explode('-',  $quotation_invoice_number);

            if (count($commission_invoice_number) > 2) {
                unset($commission_invoice_number[1]);
                $commission_invoice_number = implode("-", $commission_invoice_number);
            } else {
                $commission_invoice_number = $quotation_invoice_number;
            }

            $api_key = Generalsetting::where('backend', 0)->pluck('mollie')->first();

            $total_mollie = $total_mollie + $service_fee;
            $total_mollie = number_format((float)$total_mollie, 2, '.', '');

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($api_key);
            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $total_mollie, // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'description' => $description,
                'webhookUrl' => route('webhooks.quotation_payment'),
                'redirectUrl' => url('aanbieder/quotation-payment-redirect-page/' . $inv_encrypt),
                "metadata" => [
                    "invoice_id" => $pay_invoice_id,
                    "quote_id" => $quote_id,
                    "retailer_id" => $retailer_id,
                    "customer_id" => $customer_id,
                    "quotation_invoice_number" => $quotation_invoice_number,
                    "commission_invoice_number" => $commission_invoice_number,
                    "paid_amount" => $total_mollie,
                    "commission_percentage" => $commission_percentage,
                    "commission" => $commission,
                    "total_receive" => $total_receive,
                    "language" => $language,
                    "service_fee" => $service_fee
                ],
            ]);

            return redirect($payment->getCheckoutUrl(), 303);
        } else {
            Session::flash('unsuccess', 'Quotation Expired!');
            return redirect()->back();
        }
    }

    public function CustomQuotationAcceptQuotation($id)
    {

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.handyman_id')->where('custom_quotations.id', $id)->where('custom_quotations.user_id', $user_id)->where('custom_quotations.status', 1)->first();

        if (!$invoice) {
            return redirect()->back();
        }


        custom_quotations::where('id', $id)->update(['status' => 2, 'ask_customization' => 0, 'accepted' => 1]);

        $handyman_email = $invoice->email;
        $user_name = $invoice->name;

        \Mail::send(array(), array(), function ($message) use ($handyman_email, $user_name, $invoice, $user) {
            $message->to($handyman_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject(__('text.Quotation Accepted!'))
                ->html("Congratulations! Dear Mr/Mrs " . $user_name . ",<br><br>Mr/Mrs " . $user->name . " has accepted your quotation QUO# " . $invoice->quotation_invoice_number . "<br>You can convert your quotation into invoice once job is completed,<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });


        /*$admin_email = $this->sl->admin_email;

        \Mail::send(array(), array(), function ($message) use($admin_email,$user_name,$invoice,$user) {
            $message->to($admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                ->subject('Quotation Accepted!')
                ->html("A quotation QUO# ".$invoice->quotation_invoice_number." has been accepted by Mr/Mrs ".$user->name.' '.$user->family_name."<br>Handyman: ".$user_name."<br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte", 'text/html');
        });*/

        Session::flash('success', __('text.Quotation accepted successfully!'));

        return redirect()->back();
    }

    public function AcceptNewQuotationMail($id)
    {
        $id = Crypt::decrypt($id);

        $invoice = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->where('new_quotations.id', $id)->where('new_quotations.status', 1)->first();

        if (!$invoice) {
            return redirect()->route('front.index');
        }

        new_quotations::where('id', $id)->update(['status' => 2, 'ask_customization' => 0, 'accepted' => 1]);

        $creator_email = $invoice->email;
        $creator_name = $invoice->name;

        if ($this->lang->lang == 'du') {
            $msg = "Beste" . $creator_name . ",<br><br> Quotation QUO# " . $invoice->quotation_invoice_number . " is geaccepteerd.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
        } else {
            $msg = "Dear" . $creator_name . ",<br><br> Quotation QUO# " . $invoice->quotation_invoice_number . " has been accepted.<br><br>Kind regards,<br><br>Customer service<br><br> Pieppiep";
        }

        try {
            \Mail::send(array(), array(), function ($message) use ($msg, $creator_email, $creator_name, $invoice) {
                $message->to($creator_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject(__('text.Quotation Accepted!'))
                    ->html($msg, 'text/html');
            });
        } catch (\Exception $e) {
        }

        return view('front.thankyou1');
    }

    public function AcceptNewQuotation($id)
    {
        $now = date('d-m-Y H:i:s');
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        if ($user_role == 2) {
            // $main_id = $user->main_id;

            // if($main_id)
            // {
            //     $user = User::where('id',$main_id)->first();
            //     $user_id = $user->id;
            // }

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $retailer_name = $user->name;

            $invoice = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.user_id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->where('new_quotations.id', $id)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.draft', 0)->select('new_quotations.mail_to', 'new_quotations.quotation_invoice_number', 'users.email', 'users.fake_email', 'customers_details.name', 'customers_details.family_name')->first();

            if (!$invoice) {
                return redirect()->back();
            }

            new_quotations::where('id', $id)->update(['status' => 2, 'ask_customization' => 0, 'accepted' => 1, 'accept_date' => $now]);

            $client_name = $invoice->name;
            $valid_email = $invoice->fake_email;
            $mail_to = $invoice->mail_to;
            $client_email = $valid_email ? $mail_to : $invoice->email;

            if ($this->lang->lang == 'du') {
                $msg = "Beste " . $client_name . ",<br><br><b>" . $organization->company_name . "</b> heeft namens jou je offerte met offertenummer <b>" . $invoice->quotation_invoice_number . "</b> geaccepteerd.<br><br>Met vriendelijke groet,<br><br>$retailer_name<br><br>$organization->company_name";
            } else {
                $msg = "Dear " . $client_name . ",<br><br><b>" . $organization->company_name . "</b> has accepted Quotation: <b>" . $invoice->quotation_invoice_number . "</b> on your behalf.<br><br>Kind regards,<br><br>$retailer_name<br><br>$organization->company_name";
            }

            try {
                \Mail::send(array(), array(), function ($message) use ($msg, $client_email, $client_name, $invoice, $organization) {
                    $message
                        ->to($client_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com', $organization->company_name)
                        ->replyTo($organization->email, $organization->company_name)
                        ->subject(__('text.Quotation Accepted!'))
                        ->html($msg, 'text/html');
                });
            } catch (\Exception $e) {
            }
        } else {
            $invoice = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->where('new_quotations.id', $id)->where('new_quotations.user_id', $user_id)->where('new_quotations.status', 1)->select("new_quotations.*", "users.name", "users.email")->first();

            if (!$invoice) {
                return redirect()->back();
            }

            new_quotations::where('id', $id)->update(['status' => 2, 'ask_customization' => 0, 'accepted' => 1, 'accept_date' => $now]);

            $creator_organization = User::where("id", $invoice->creator_id)->first()->organization;
            $creator_email = $creator_organization->email;
            $creator_company_name = $creator_organization->company_name;

            if ($this->lang->lang == 'du') {
                $msg = "Beste " . $creator_company_name . ",<br><br>" . $user->name . " heeft offerte" . $invoice->quotation_invoice_number . "geaccepteerd.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
            } else {
                $msg = "Dear " . $creator_company_name . ",<br><br>Mr/Mrs " . $user->name . " has accepted your quotation QUO# " . $invoice->quotation_invoice_number . "<br><br>Kind regards,<br><br>Customer service<br><br> Pieppiep";
            }

            try {
                \Mail::send(array(), array(), function ($message) use ($msg, $creator_email, $creator_company_name, $invoice, $user) {
                    $message->to($creator_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                        ->subject(__('text.Quotation Accepted!'))
                        ->html($msg, 'text/html');
                });
            } catch (\Exception $e) {
            }

            /*$admin_email = $this->sl->admin_email;

                \Mail::send(array(), array(), function ($message) use($admin_email,$user_name,$invoice,$user) {
                    $message->to($admin_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.nl')
                        ->subject('Quotation Accepted!')
                        ->html("A quotation QUO# ".$invoice->quotation_invoice_number." has been accepted by Mr/Mrs ".$user->name.' '.$user->family_name."<br>Handyman: ".$user_name."<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
                });*/
        }

        Session::flash('success', __('text.Quotation accepted successfully!'));
        return redirect()->back();
    }

    public function DiscardQuotation($id)
    {
        $now = date('d-m-Y H:i:s');
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $retailer_name = $user->name;

        $invoice = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.user_id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->where('new_quotations.id', $id)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.accepted', 1)->where('new_quotations.finished', 0)->select('new_quotations.mail_to', 'new_quotations.quotation_invoice_number', 'users.email', 'users.fake_email', 'customers_details.name', 'customers_details.family_name')->first();

        if (!$invoice) {
            return redirect()->back();
        }

        $quote_request_id = new_quotations::where('id', $id)->pluck("quote_request_id")->first();

        quotes::where('id', $quote_request_id)->update(['status' => 1]);
        new_quotations::where('id', $id)->update(['status' => 1, 'accepted' => 0, 'accept_date' => NULL]);

        // $client_name = $invoice->name;
        // $valid_email = $invoice->fake_email;
        // $mail_to = $invoice->mail_to;
        // $client_email = $valid_email ? $mail_to : $invoice->email;

        // if($this->lang->lang == 'du')
        // {
        //     $msg = "Beste " . $client_name . ",<br><br><b>" . $user->company_name . "</b> heeft namens jou je offerte met offertenummer <b>" . $invoice->quotation_invoice_number . "</b> geaccepteerd.<br><br>Met vriendelijke groet,<br><br>$retailer_name<br><br>$user->company_name";
        // }
        // else
        // {
        //     $msg = "Dear " . $client_name . ",<br><br><b>" . $user->company_name . "</b> has accepted Quotation: <b>" . $invoice->quotation_invoice_number . "</b> on your behalf.<br><br>Kind regards,<br><br>$retailer_name<br><br>$user->company_name";
        // }

        // \Mail::send(array(), array(), function ($message) use ($msg, $client_email, $client_name, $invoice, $user) {
        //     $message
        //         ->to($client_email)
        //         ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com', $user->company_name)
        //         ->replyTo($user->email, $user->company_name)
        //         ->subject(__('text.Quotation Discarded!'))
        //         ->html($msg,'text/html');
        // });

        Session::flash('success', __('text.Quotation has been discarded.'));

        return redirect()->back();
    }

    public function HandymanQuoteRequest($id)
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

        if ($user->can('handyman-quote-request')) {
            $request = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('product_models', 'product_models.id', '=', 'quotes.quote_model')->leftjoin('models', 'models.id', '=', 'quotes.quote_type')->leftjoin('colors', 'colors.id', '=', 'quotes.quote_color')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->where('quotes.id', $id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'product_models.model as model_name', 'models.cat_name as type_title', 'colors.title as color')->first();

            $q_a = requests_q_a::where('request_id', $id)->get();

            $invoice = quotation_invoices::where('quote_id', $request->id)->whereIn('handyman_id', $related_users)->first();

            $products = Products::all();

            return view('user.quote_request', compact('request', 'products', 'invoice', 'q_a'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function HandymanCreateQuote($id = NULL)
    {
        if ($id) {
            $request_id = Crypt::decrypt($id);
            $quote = quotes::where('id', $request_id)->first();
            $quote_qty = $quote->quote_qty;

            if ($quote->quote_service) {
                $color = colors::where('id', $quote->quote_color)->pluck('title')->first();

                if ($quote->quote_model) {
                    $model = product_models::where('id', $quote->quote_model)->pluck('model')->first();
                    $product_request = Products::leftjoin('product_models', 'product_models.product_id', '=', 'products.id')
                        ->leftjoin('colors', 'colors.product_id', '=', 'products.id')
                        ->leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                        ->where('product_models.model', $model)->where('colors.title', $color)->where('products.sub_category_id', $quote->quote_service)->where('products.brand_id', $quote->quote_brand)->where('products.model_id', $quote->quote_type)
                        ->select('products.*', 'organizations.id as supplier_id', 'organizations.company_name', 'product_models.id as model_id', 'product_models.model', 'product_models.measure', 'product_models.estimated_price_per_box', 'product_models.estimated_price_quantity', 'product_models.estimated_price', 'product_models.max_width', 'colors.id as color_id', 'colors.title as color')->first();
                } else {
                    $product_request = Products::leftjoin('colors', 'colors.product_id', '=', 'products.id')
                        ->leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                        ->where('colors.title', $color)->where('products.sub_category_id', $quote->quote_service)->where('products.brand_id', $quote->quote_brand)->where('products.model_id', $quote->quote_type)
                        ->select('products.*', 'organizations.id as supplier_id', 'organizations.company_name', 'colors.id as color_id', 'colors.title as color')->first();
                    $product_request->model_id = 0;
                }
            } else {
                $product_request = Service::leftjoin('retailer_services', 'retailer_services.service_id', '=', 'services.id')->where('services.id', $quote->quote_service1)->select('services.*', 'retailer_services.sell_rate')->first();
            }
        } else {
            $request_id = '';
            $product_request = '';
            $quote = '';
            $quote_qty = '';
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'create-custom-quotation') {
            if ($user->can('create-custom-quotation')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'create-direct-invoice') {
            if ($user->can('create-direct-invoice')) {
                $check = 1;
            }
        }

        if ($check) {
            if ($user_role == 2) {

                $customers = customers_details::leftjoin("users", "users.id", "=", "customers_details.user_id")->whereIn('customers_details.retailer_id', $related_users)->select("customers_details.*", "users.email", "users.fake_email")->get();
                $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();

                $suppliers = retailers_requests::leftJoin('supplier_categories', 'supplier_categories.organization_id', '=', 'retailers_requests.supplier_organization')
                    ->where("retailers_requests.retailer_organization", $organization_id)
                    ->where('supplier_categories.category_id', $floor_category_id)
                    ->where('retailers_requests.status', 1)->where('retailers_requests.active', 1)
                    ->pluck('retailers_requests.supplier_organization');

                $products = Products::leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                    ->whereIn('organizations.id', $suppliers)->where('products.category_id', $floor_category_id)->with('colors')->with('models')
                    ->select('products.*', 'organizations.company_name')->get();

                $services = Service::leftjoin('retailer_services', 'retailer_services.service_id', '=', 'services.id')->whereIn('retailer_services.retailer_id', $related_users)->select('services.*', 'retailer_services.sell_rate as rate')->get();
                $items = items::leftjoin('categories', 'categories.id', '=', 'items.category_id')->whereIn('items.user_id', $related_users)->select('items.*', 'categories.cat_name as category')->get();

                $plannings = $this->Plannings(1);
                $event_titles = $plannings["event_titles"];
                $event_statuses = $plannings["event_statuses"];
                $quotation_ids = $plannings["quotation_ids"];
                $clients = $plannings["clients"];
                $planning_suppliers = $plannings["suppliers"];
                $employees = $plannings["employees"];
                $responsible_persons = $plannings["responsible_persons"];
                // $plannings = $plannings["plannings"];
                $general_terms = retailer_general_terms::whereIn("retailer_id", $related_users)->where("type", 1)->first();
                $general_ledgers = general_ledgers::whereIn('user_id', $related_users)->get();
                $vats = vats::get();

                return view('user.create_custom_quote1', compact('vats', 'general_ledgers', 'responsible_persons', 'planning_suppliers', 'general_terms', 'clients', 'suppliers', 'employees', 'quotation_ids', 'event_titles', 'event_statuses', 'products', 'customers', 'suppliers', 'services', 'items', 'request_id', 'product_request', 'quote_qty', 'quote'));
            } else {
                return redirect()->back();
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function Customers()
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

        if ($user->can('customers')) {
            $customers = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->whereIn('customers_details.retailer_id', $related_users)->select('customers_details.*', 'users.email', 'users.fake_email')->get();
            // $customers = customers_details::where('retailer_id',$user_id)->get();

            return view('user.customers', compact('customers'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ReeleezeeCredentials(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        User::where("id", $user_id)->update(["reeleezee_username" => $request->username, "reeleezee_password" => $request->password]);
        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function ExportCustomersToReeleezee()
    {
        $username = $this->reeleezee_username;
        $password = $this->reeleezee_password;
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

        ExportCustomersReeleezee::dispatch($username, $password, $user, $related_users);

        Session::flash('success', __("text.Customers will be exported in background."));
        return redirect()->back();
    }

    public function ReeleezeeCustomersAPI($response, $user_id, $username, $password)
    {
        foreach ($response["value"] as $c => $key) {
            if ($key["Name"]) {
                $id = $key["id"];
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}?$" . "expand=*($" . "levels=max)";

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization: Basic ' . base64_encode($username . ":" . $password)
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);

                $reeleezee_status = $response["CustomerStatus"]["Name"];
                $entity_type = $response["EntityType"]["IsPerson"] ? 1 : 2;
                $entity_description = $response["EntityType"]["Description"];
                $entity_description_nl = $response["EntityType"]["DescriptionNL"];
                $entity_name = $response["EntityType"]["Name"];
                $phone = [];
                $address = $key["FullAddress"];
                $city = NULL;
                $postcode = NULL;
                $street_name = NULL;
                $street_number = NULL;

                if (isset($response["CommunicationChannelList"]) && $response["CommunicationChannelList"]) {
                    foreach ($response["CommunicationChannelList"] as $temp) {
                        if ($temp["CommunicationType"] == 9) {
                            $phone[] = isset($temp["FormattedValue"]) ? $temp["FormattedValue"] : NULL;
                        }
                    }
                }

                $phone = count($phone) ? implode(",", $phone) : NULL;

                if (isset($response["AddressList"]) && $response["AddressList"]) {
                    foreach ($response["AddressList"] as $temp1) {
                        $address = isset($temp1["FullAddress"]) ? $temp1["FullAddress"] : $address;
                        $city = isset($temp1["City"]) ? $temp1["City"] : NULL;
                        $postcode = isset($temp1["Postcode"]) ? $temp1["Postcode"] : NULL;
                        $street_name = isset($temp1["Street"]) ? $temp1["Street"] : NULL;
                        $street_number = isset($temp1["Number"]) ? $temp1["Number"] : NULL;
                    }
                }

                $org_id = '';
                $check = customers_details::where("reeleezee_guid", $key["id"])->first();

                if (!$check) {
                    if ($key["IdentifierNumber"]) {
                        $check_numbers = $this->check_numbers(NULL, $key["IdentifierNumber"], NULL, $user_id);

                        $found_external_number = current(array_filter($check_numbers, function ($item) {
                            return isset($item['case']) && 2 == $item['case'];
                        }));

                        if ($found_external_number) {
                            $check = customers_details::where("id", $found_external_number["id"])->first();
                            $org_id = $check->id;
                        }
                    } elseif ($key["EMail"]) {
                        $check = customers_details::where('email_address', $key["EMail"])->where("retailer_id", $user_id)->first();

                        if ($check) {
                            $org_id = $check->id;
                        }
                    }
                } else {
                    $org_id = $check->id;
                }

                if ($reeleezee_status == "Inactief") {
                    if ($org_id) {
                        $retailer = User::where("id", $user_id)->first();
                        $this->delete_customer_detail($org_id, $retailer);
                    }
                } else {
                    // if($org_id)
                    // {
                    //     customers_details::where("id",$org_id)->update(["deleted_at" => NULL]);
                    // }

                    $contact_person_url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons";
                    $headers = array(
                        'Content-Type:application/json',
                        'Authorization: Basic ' . base64_encode($username . ":" . $password)
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $contact_person_url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
                    $contact_person_names = [];
                    $contact_person_phone_numbers = [];
                    $contact_person_emails = [];

                    foreach ($response["value"] as $cp) {
                        $cp_id = $cp["id"];
                        $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons/{$cp_id}?$" . "expand=*($" . "levels=max)";
                        $headers = array(
                            'Content-Type:application/json',
                            'Authorization: Basic ' . base64_encode($username . ":" . $password)
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response1 = curl_exec($ch);
                        curl_close($ch);
                        $response1 = json_decode($response1, true);
                        $phone_numbers = [];
                        $emails = [];

                        if ($response1["CommunicationChannelList"]) {
                            foreach ($response1["CommunicationChannelList"] as $ccl) {
                                if ($ccl["CommunicationType"] == 9) {
                                    $phone_numbers[] = $ccl["FormattedValue"];
                                }

                                if ($ccl["CommunicationType"] == 10) {
                                    $emails[] = $ccl["FormattedValue"];
                                }
                            }
                        }

                        $phone_numbers = implode(",", $phone_numbers);
                        $emails = implode(",", $emails);
                        $contact_person_names[] = $response1["Name"];
                        $contact_person_phone_numbers[] = $phone_numbers;
                        $contact_person_emails[] = $emails;
                    }

                    $projects_url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Projects";
                    $headers = array(
                        'Content-Type:application/json',
                        'Authorization: Basic ' . base64_encode($username . ":" . $password)
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $projects_url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
                    $project_names = [];
                    $project_is_active = [];
                    $project_start_dates = [];
                    $project_end_dates = [];
                    $project_descriptions = [];
                    $project_comments = [];
                    $project_totals = [];

                    foreach ($response["value"] as $p) {
                        $project_names[] = $p["Name"];
                        $project_is_active[] = $p["IsActive"];
                        $project_start_dates[] = $p["BeginDate"];
                        $project_end_dates[] = $p["EndDate"];
                        $project_descriptions[] = $p["Description"];
                        $project_comments[] = $p["Comment"];
                        $project_totals[] = $p["TotalAmount"];
                    }

                    $myRequest = new Request();
                    $myRequest->query->add(['reeleezee_status' => $reeleezee_status, 'entity_type' => $entity_type, 'entity_description' => $entity_description, 'entity_description_nl' => $entity_description_nl, 'entity_name' => $entity_name, 'project_names' => $project_names, 'project_is_active' => $project_is_active, 'project_start_dates' => $project_start_dates, 'project_end_dates' => $project_end_dates, 'project_descriptions' => $project_descriptions, 'project_comments' => $project_comments, 'project_totals' => $project_totals, 'contact_person_names' => $contact_person_names, 'contact_person_phone_numbers' => $contact_person_phone_numbers, 'contact_person_emails' => $contact_person_emails, 'org_id' => $org_id, 'reeleezee_id' => $key["id"], 'email' => $key["EMail"], 'phone' => $phone, 'name' => $key["Name"], 'address' => $address, 'street_name' => $street_name, 'street_number' => $street_number, 'city' => $city, 'postcode' => $postcode, 'external_relation_number' => $key["IdentifierNumber"]]);
                    $this->PostCustomer($myRequest, $user_id);

                    if (Session::get("unsuccess")) {
                        $this->array_errors[] = Session::get("unsuccess");
                        Session::forget('unsuccess');
                    }
                }
            }
        }

        return $this->array_errors;
    }

    public function ImportReeleezeeCustomers()
    {
        $username = $this->reeleezee_username;
        $password = $this->reeleezee_password;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        ImportCustomersReeleezee::dispatch($username, $password, $user, $user_id);

        Session::flash('success', __("text.Customers will be imported in background."));
        return redirect()->back();
    }

    public function ImportCustomers()
    {
        $user = Auth::guard('user')->user();

        if ($user->can('edit-customer')) {
            return view('user.import_customers');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function PostImportCustomers(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        $extension = strtolower($request->excel_file->getClientOriginalExtension());

        if (!in_array($extension, ['xls', 'xlsx'])) {
            return redirect()->back()->withErrors("File should be of format xlsx or xls")->withInput();
        }

        $import = new CustomersImport;
        Excel::import($import, request()->file('excel_file'));

        if (count($import->conflict_rows) > 0) {
            $msg = "";

            foreach ($import->conflict_rows as $data) {
                $msg .= 'Row: ' . $data["row"] . ', Number: ' . $data["number"] . ', Reason: ' . $data["reason"] . '<br>';
            }

            Session::flash('unsuccess', $msg);
        }

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

        $max_customer_number = customers_details::whereIn("retailer_id", $related_users)->max("customer_number");
        $max_customer_number = $max_customer_number + 1;
        $user->organization->update(["counter_customer_number" => $max_customer_number]);

        Session::flash('success', trans_choice('text.Number of rows imported.', $import->rows_imported));
        return redirect()->route('customers');
    }

    public function ExportCustomers(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);
        $export_by = $request->export_by;

        return Excel::download(new CustomersExport($export_by), 'customer_details.xlsx');
    }

    public function ExportInvoicesXML(Request $request)
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

        $data = all_invoices::leftjoin("customers_details", "customers_details.id", "=", "new_invoices.customer_details")->whereIn('new_invoices.creator_id', $related_users);

        if ($request->export_xml_by == 2) {
            $data = $data->orderBy("new_invoices.created_at", "desc");
        } else {
            $from_date = date('Y-m-d', strtotime($request->export_from_date));
            $to_date = date('Y-m-d', strtotime($request->export_to_date));
            $data = $data->whereDate('new_invoices.created_at', '>=', $from_date)->whereDate('new_invoices.created_at', '<=', $to_date)->orderBy("new_invoices.created_at", "desc");
        }

        $data = $data->select("new_invoices.*", "customers_details.name", "customers_details.family_name", "customers_details.business_name", "customers_details.address", "customers_details.postcode", "customers_details.city", "customers_details.phone", "customers_details.email_address", "customers_details.external_relation_number", "customers_details.customer_number")->get();

        $filename = "InvoicesXML.xml";
        $output = View::make('user.invoices_xml')->with(compact('data'))->render();

        $response = Response::make($output, 200);
        $response->header('Content-Type', 'text/xml');
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . $filename . '');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;

        return response()->view('user.invoices_xml', [
            'data' => $data,

        ])->header('Content-Type', 'text/xml');
    }

    public function ExportInvoices(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600);

        return Excel::download(new InvoicesExport($request->all()), 'invoices.xlsx');
    }

    public function Employees()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        if ($user->can('employees')) {
            $employees = $user->organization->users()->where('users.id', '!=', $user_id)->get();

            return view('user.employees', compact('employees'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function EmployeePermissions($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('employee-permissions')) {
            if ($user_id != $id) {
                $permissions = Permission::all();
                $user = $user->organization->users()->where('users.id', $id)->first();

                if ($user) {
                    return view('user.employee_permission', compact('permissions', 'user'));
                } else {
                    return redirect()->route('user-dashboard');
                }
            } else {
                return redirect()->route('user-dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function EmployeePermissionStore(Request $request)
    {
        $user = User::find($request->user_id);

        $user->syncPermissions($request->permissions);

        Session::flash('success', __('text.Permission(s) assigned successfully.'));
        return redirect()->route('employee-permissions', $request->user_id);
    }

    public function CreateCustomerForm()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        if ($user->can('handyman-user-create')) {
            $counter_customer_number = $this->get_next_customer_number($user_id);
            return view('user.create_customer', compact("counter_customer_number"));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function CreateEmployeeForm()
    {
        $user = Auth::guard('user')->user();
        $ep_counter = Generalsetting::pluck("ep_counter")->first();
        $fr_counter = Generalsetting::pluck("fr_counter")->first();

        if ($user->can('employee-create')) {
            return view('user.create_employee', compact("ep_counter", "fr_counter"));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function EditCustomer($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        if ($user->can('edit-customer')) {
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $customer = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $id)->whereIn('customers_details.retailer_id', $related_users)->select('customers_details.*', 'users.email', 'users.fake_email')->first();

            if ($customer) {
                return view('user.create_customer', compact('customer'));
            } else {
                return redirect()->route('user-dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function EditEmployee($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('edit-employee')) {
            if ($user_id != $id) {
                $employee = $user->organization->users()->where('users.id', $id)->first();

                $ep_counter = Generalsetting::pluck("ep_counter")->first();
                $fr_counter = Generalsetting::pluck("fr_counter")->first();

                if ($employee) {
                    return view('user.create_employee', compact('employee', 'ep_counter', 'fr_counter'));
                } else {
                    return redirect()->route('user-dashboard');
                }
            } else {
                return redirect()->route('user-dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function delete_customer_detail($id, $retailer)
    {
        $organization_id = $retailer->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $customer_details = customers_details::where('id', $id)->whereIn('retailer_id', $related_users)->first();
        $customer_id = $customer_details->user_id;

        $delete = $customer_details->delete();

        $other_retailers_link = customers_details::where('user_id', $customer_id)->whereNotIn('retailer_id', $related_users)->first();

        if (!$other_retailers_link) {
            User::where("id", $customer_id)->whereIn("parent_id", $related_users)->delete();
        }

        return $delete;
    }

    public function DeleteCustomer($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('delete-customer')) {
            $delete = $this->delete_customer_detail($id, $user);

            if ($delete) {
                Session::flash('success', __('text.Customer deleted successfully'));
                return redirect()->route('customers');
            } else {
                return redirect()->route('user-dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function CustomerManagePost(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('delete-customer')) {
            $flag = 0;

            foreach ($request->customers as $i => $key) {
                if ($request->delete_customers[$i]) {
                    $delete = $this->delete_customer_detail($key, $user);

                    if ($delete) {
                        $flag = 1;
                    }
                }
            }

            if ($flag) {
                Session::flash('success', __('text.Customers deleted successfully'));
            }

            return redirect()->route('customers');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function DeleteEmployee($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('delete-employee')) {
            if ($user_id != $id) {
                $user = $user->organization->users()->where('users.id', $id)->first();
                $user->employee->delete();
                $delete = $user->delete();

                if ($delete) {
                    Session::flash('success', 'Employee deleted successfully');
                    return redirect()->route('employees');
                } else {
                    return redirect()->route('user-dashboard');
                }
            } else {
                return redirect()->route('user-dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function check_numbers($customer_number, $external_relation_number, $cd_id = NULL, $user_id)
    {
        $organization_id = User::where("id", $user_id)->first()->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $flags = array();

        if ($customer_number) {
            $check_customer_number = new customers_details;

            if ($cd_id) {
                $check_customer_number = $check_customer_number->where("id", "!=", $cd_id);
            }

            $check_customer_number = $check_customer_number->whereIn("retailer_id", $related_users)->where("customer_number", $customer_number)->first();

            if ($check_customer_number) {
                $flags[] = ["case" => 1, "id" => $check_customer_number->id];
            }
        }

        if ($external_relation_number) {
            $check_external_relation_number = new customers_details;

            if ($cd_id) {
                $check_external_relation_number = $check_external_relation_number->where("id", "!=", $cd_id);
            }

            $check_external_relation_number = $check_external_relation_number->whereIn("retailer_id", $related_users)->where("external_relation_number", $external_relation_number)->first();

            if ($check_external_relation_number) {
                $flags[] = ["case" => 2, "id" => $check_external_relation_number->id];
            }
        }

        return $flags;
    }

    public function get_next_customer_number($user_id)
    {
        $user = User::where("id", $user_id)->first();
        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $counter_customer_number = $user->counter_customer_number;

        $quotation_number = date("Y") . "-" . sprintf('%04u', $counter_customer_number) . '-' . sprintf('%06u', $user->counter);
        $check_quotation_number = new_quotations::where('quotation_invoice_number', $quotation_number)->whereIn('creator_id', $related_users)->first();

        while ($check_quotation_number) {
            $counter_customer_number = $counter_customer_number + 1;
            $quotation_number = date("Y") . "-" . sprintf('%04u', $counter_customer_number) . '-' . sprintf('%06u', $user->counter);
            $check_quotation_number = new_quotations::where('quotation_invoice_number', $quotation_number)->whereIn('creator_id', $related_users)->first();
        }

        $customer_number_check = customers_details::whereIn("retailer_id", $related_users)->where("customer_number", $counter_customer_number)->first();

        while ($customer_number_check) {
            $counter_customer_number = $counter_customer_number + 1;
            $customer_number_check = customers_details::wherein("retailer_id", $related_users)->where("customer_number", $counter_customer_number)->first();
        }

        return $counter_customer_number;
    }

    public function increment_customer_number($user_id)
    {
        $user = User::where("id", $user_id)->first();

        $counter_customer_number = $user->counter_customer_number;
        $counter_customer_number = $counter_customer_number + 1;

        $user->organization->update(["counter_customer_number" => $counter_customer_number]);
    }

    public function ContactPersonPost(Request $request)
    {
        $contact_persons = [];

        if ($request->contact_person_names) {
            foreach ($request->contact_person_names as $xp => $cp) {
                $contact_persons[] = [
                    'name' => $cp,
                    'phone' => $request->contact_person_phone_numbers[$xp],
                    'email' => $request->contact_person_emails[$xp]
                ];
            }
        }

        $contact_persons = count($contact_persons) ? json_encode($contact_persons) : NULL;

        customers_details::where("id", $request->customer_id)->update(["contact_persons" => $contact_persons]);
    }

    public function ProjectPost(Request $request)
    {
        $customer_details = customers_details::where("id", $request->customer_id)->first();
        $projects = [];

        if ($request->project_names) {
            foreach ($request->project_names as $xp => $p) {
                $projects[] = [
                    'name' => $p ? $p : ($customer_details->family_name ? $customer_details->name . " " . $customer_details->family_name : $customer_details->name),
                    'is_active' => $request->project_active[$xp] ? true : false,
                    'start_date' => $request->project_start_dates[$xp],
                    'end_date' => $request->project_end_dates[$xp],
                    'description' => $request->project_descriptions[$xp],
                    'comment' => $request->project_comments[$xp],
                    'total' => $request->project_totals[$xp],
                ];
            }
        }

        $projects = count($projects) ? json_encode($projects) : NULL;

        $customer_details->projects = $projects;
        $customer_details->save();
    }

    public function PostCustomer(Request $request, $user_id = NULL)
    {
        $entity_description = isset($request->entity_description) ? $request->entity_description : ($request->entity_type == 1 ? "Natural person" : "Private Company");
        $entity_description_nl = isset($request->entity_description_nl) ? $request->entity_description_nl : ($request->entity_type == 1 ? "Natuurlijk persoon" : "Besloten Vennootschap");
        $entity_name = isset($request->entity_name) ? $request->entity_name : ($request->entity_type == 1 ? "Person" : "BV");

        $contact_persons = [];

        if ($request->contact_person_names) {
            foreach ($request->contact_person_names as $xp => $cp) {
                $contact_persons[] = [
                    'name' => $cp,
                    'phone' => $request->contact_person_phone_numbers[$xp],
                    'email' => $request->contact_person_emails[$xp]
                ];
            }
        }

        $contact_persons = count($contact_persons) ? json_encode($contact_persons) : NULL;

        $projects = [];

        if ($request->project_names) {
            foreach ($request->project_names as $p => $pj) {
                $projects[] = [
                    'name' => $pj ? $pj : ($request->family_name ? $request->name . " " . $request->family_name : $request->name),
                    'is_active' => $request->project_is_active[$p] ? true : false,
                    'start_date' => $request->project_start_dates[$p],
                    'end_date' => $request->project_end_dates[$p],
                    'description' => $request->project_descriptions[$p],
                    'comment' => $request->project_comments[$p],
                    'total' => $request->project_totals[$p],
                ];
            }
        }

        $projects = count($projects) ? json_encode($projects) : NULL;

        if (!$projects && $request->is_form) {
            $p_name = $request->family_name ? $request->name . " " . $request->family_name : $request->name;
            $p_start_date = date("Y-m-d") . "T00:00:00";
            $p_end_date = date("Y-m-d", strtotime("+1 year", strtotime(date("Y-m-d")))) . "T00:00:00";
            $projects = [["name" => $p_name, "is_active" => true, "start_date" => $p_start_date, "end_date" => $p_end_date, "description" => null, "comment" => null, "total" => "0"]];
            $projects = json_encode($projects);
        }

        if (!$user_id) {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            // $main_id = $user->main_id;

            // if($main_id)
            // {
            //     $user = User::where("id",$main_id)->first();
            //     $user_id = $user->id;
            // }

            $organization_id = $user->organization->id;
        } else {
            $organization_id = User::where("id", $user_id)->first()->organization->id;
        }

        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $counter_customer_number = $this->get_next_customer_number($user_id);

        if ($request->org_id) {
            $check_numbers = $this->check_numbers($request->customer_number, $request->external_relation_number, $request->org_id, $user_id);
            $flag = 0;
            $msg = "";

            $found_customer_number = current(array_filter($check_numbers, function ($item) {
                return isset($item['case']) && 1 == $item['case'];
            }));

            $found_external_number = current(array_filter($check_numbers, function ($item) {
                return isset($item['case']) && 2 == $item['case'];
            }));

            if ($found_customer_number) {
                $msg .= __('text.This customer number is already taken') . ": " . $request->customer_number . "<br>";
                $flag = 1;
            }

            if ($found_external_number) {
                $msg .= __('text.This external relation number is already taken') . ": " . $request->external_relation_number;
                $flag = 1;
            }

            if ($flag) {
                Session::flash('unsuccess', $msg);

                if ($request->is_form) {
                    return redirect()->route('customers');
                } else {
                    return;
                }
            }

            $customer_id = customers_details::where('id', $request->org_id)->pluck('user_id')->first();

            $check = User::where('email', $request->email)->where('id', '!=', $customer_id)->first();
            $f_e = User::where('id', $customer_id)->first();

            if ($check) {
                Session::flash('unsuccess', __('text.This email address is already taken') . ": " . $request->email);
            } else {
                if (!$request->email) {
                    if ($f_e->fake_email) {
                        $user_email = $f_e->email;
                    } else {
                        $faker = \Faker\Factory::create();
                        $user_email = $faker->unique()->email;
                    }

                    $fake_email = 1;
                } else {
                    $user_email = $request->email;
                    $fake_email = 0;
                }

                $check1 = User::where('email', $request->email)->where('id', $customer_id)->first();

                if ($check1) {
                    customers_details::where('id', $request->org_id)->update(['entity_type' => $request->entity_type, 'entity_description' => $entity_description, 'entity_description_nl' => $entity_description_nl, 'entity_name' => $entity_name, 'projects' => $projects, 'contact_persons' => $contact_persons, 'reeleezee_guid' => $request->reeleezee_id, 'email_address' => $request->email, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone, 'external_relation_number' => $request->external_relation_number, 'customer_number' => $request->customer_number ? $request->customer_number : $counter_customer_number]);

                    if (!$request->customer_number) {
                        $this->increment_customer_number($user_id);
                    }
                } else {
                    $em = User::where("id", $customer_id)->where("fake_email", 0)->pluck("email")->first();
                    $other_retailers_link = customers_details::where('user_id', $customer_id)->whereNotIn('retailer_id', $related_users)->first();

                    if ($other_retailers_link) {
                        $user_name = $request->name;

                        $retailer = User::where('id', $user_id)->first();
                        $retailer_name = $retailer->name;
                        $company_name = $retailer->company_name;
                        $retailer_email = $retailer->email;

                        $org_password = Str::random(8);
                        $password = Hash::make($org_password);

                        if ($em != $user_email) {
                            $user = new User;
                            $user->category_id = 20;
                            $user->role_id = 3;
                            $user->password = $password;
                            $user->temp_password = $org_password;
                            $user->name = $request->name;
                            $user->family_name = $request->family_name ? $request->family_name : '';
                            // $user->business_name = $request->business_name;
                            // $user->address = $request->address;
                            // $user->postcode = $request->postcode;
                            // $user->city = $request->city;
                            // $user->phone = $request->phone;
                            $user->email = $user_email;
                            $user->parent_id = $user_id;
                            $user->allowed = 0;
                            $user->fake_email = $fake_email;
                            $user->reeleezee_guid = $request->reeleezee_id;
                            $user->save();

                            $customer_id = $user->id;

                            $universal_customers_details = new universal_customers_details;
                            $universal_customers_details->user_id = $customer_id;
                            $universal_customers_details->business_name = $request->business_name;
                            $universal_customers_details->address = $request->address;
                            $universal_customers_details->postcode = $request->postcode;
                            $universal_customers_details->city = $request->city;
                            $universal_customers_details->phone = $request->phone;
                            $universal_customers_details->save();
                        }

                        customers_details::where('id', $request->org_id)->update(['entity_type' => $request->entity_type, 'entity_description' => $entity_description, 'entity_description_nl' => $entity_description_nl, 'entity_name' => $entity_name, 'projects' => $projects, 'contact_persons' => $contact_persons, 'reeleezee_guid' => $request->reeleezee_id, 'email_address' => $request->email, 'user_id' => $customer_id, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone, 'external_relation_number' => $request->external_relation_number, 'customer_number' => $request->customer_number ? $request->customer_number : $counter_customer_number]);

                        if (!$request->customer_number) {
                            $this->increment_customer_number($user_id);
                        }

                        // if($request->email)
                        // {
                        //     $link = url('/') . '/aanbieder/client-new-quotations';

                        //     if($this->lang->lang == 'du')
                        //     {
                        //         $msg = "Beste $user_name,<br><br>Er is een account voor je gecreëerd door " . $company_name . ". Hier kan je offertes bekijken, verzoek tot aanpassen of de offerte accepteren. <a href='" . $link . "'>Klik hier</a>, om je naar je persoonlijke dashboard te gaan.<br><br><b>Wachtwoord:</b><br><br>Je wachtwoord is: " . $org_password . "<br><br>Met vriendelijke groeten,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                        //     }
                        //     else
                        //     {
                        //         $msg = "Dear Mr/Mrs " . $user_name . ",<br><br>Your account has been created by retailer " . $retailer_name . " for quotations. Kindly complete your profile and change your password. You can go to your dashboard through <a href='" . $link . "'>here.</a><br><br>Your Password: " . $org_password . "<br><br>Kind regards,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                        //     }

                        //     \Mail::send(array(), array(), function ($message) use ($msg, $user_email, $user_name, $retailer_name, $link, $org_password, $company_name, $retailer_email) {
                        //         $message->to($user_email)
                        //             ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl',$company_name)
                        //             ->replyTo($retailer_email,$company_name)
                        //             ->subject(__('text.Account Created!'))
                        //             ->html($msg,'text/html');
                        //     });
                        // }
                    } else {
                        if ($em != $user_email) {
                            User::where('id', $customer_id)->update(["email" => $user_email, "fake_email" => $fake_email]);
                        }

                        customers_details::where('id', $request->org_id)->update(['entity_type' => $request->entity_type, 'entity_description' => $entity_description, 'entity_description_nl' => $entity_description_nl, 'entity_name' => $entity_name, 'projects' => $projects, 'contact_persons' => $contact_persons, 'reeleezee_guid' => $request->reeleezee_id, 'email_address' => $request->email, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone, 'external_relation_number' => $request->external_relation_number, 'customer_number' => $request->customer_number ? $request->customer_number : $counter_customer_number]);

                        if (!$request->customer_number) {
                            $this->increment_customer_number($user_id);
                        }
                    }
                }
            }

            Session::flash('success', __('text.Customer details updated successfully'));

            if ($request->is_form) {
                return redirect()->route('customers');
            } else {
                return;
            }
        } else {
            $flag = 0;

            if ($request->customer_number || $request->external_relation_number) {
                $check_numbers = $this->check_numbers($request->customer_number, $request->external_relation_number, NULL, $user_id);

                $found_customer_number = current(array_filter($check_numbers, function ($item) {
                    return isset($item['case']) && 1 == $item['case'];
                }));

                $found_external_number = current(array_filter($check_numbers, function ($item) {
                    return isset($item['case']) && 2 == $item['case'];
                }));

                if ($found_customer_number) {
                    $details = customers_details::where("id", $found_customer_number["id"])->first();
                    $flag = 1;
                } elseif ($found_external_number) {
                    $details = customers_details::where("id", $found_external_number["id"])->first();
                    $flag = 1;
                }
            } elseif ($request->reeleezee_id) {
                $details = customers_details::where("reeleezee_guid", $request->reeleezee_id)->whereIn("retailer_id", $related_users)->first();

                if ($details) {
                    $flag = 1;
                }
            }

            $check = User::where('email', $request->email)->first();

            if ($check) {
                if ($check->role_id == 3) {
                    // if($check->parent_id == $user_id)
                    // {
                    //     Session::flash('unsuccess', __('text.User already created') . ": " . $request->name);
                    // }

                    $check1 = customers_details::where('user_id', $check->id)->whereIn('retailer_id', $related_users)->first();

                    if ($check1) {
                        Session::flash('unsuccess', __('text.This email is already linked with your customer account. Kindly update that specific account from customers page.'));
                    } else {
                        if (!$flag) {
                            $details = new customers_details();
                        }

                        $details->user_id = $check->id;
                        $details->retailer_id = $user_id;
                        $details->name = $request->name;
                        $details->family_name = $request->family_name ? $request->family_name : '';
                        $details->business_name = $request->business_name;
                        $details->postcode = $request->postcode;
                        $details->address = $request->address;
                        $details->street_name = $request->street_name;
                        $details->street_number = $request->street_number;
                        $details->city = $request->city;
                        $details->phone = $request->phone;
                        $details->email_address = $request->email;
                        $details->external_relation_number = $request->external_relation_number;
                        $details->customer_number = $request->customer_number ? $request->customer_number : $counter_customer_number;
                        $details->reeleezee_guid = $request->reeleezee_id;
                        $details->contact_persons = $contact_persons;
                        $details->projects = $projects;
                        $details->entity_type = $request->entity_type;
                        $details->entity_description = $entity_description;
                        $details->entity_description_nl = $entity_description_nl;
                        $details->entity_name = $entity_name;
                        $details->save();

                        if (!$request->customer_number) {
                            $this->increment_customer_number($user_id);
                        }

                        Session::flash('success', __('text.Customer account created successfully'));
                    }
                } else {
                    Session::flash('unsuccess', __('text.This email address is already taken') . ": " . $request->email);
                }
            } else {
                $user_name = $request->name;

                if (!$request->email) {
                    $faker = \Faker\Factory::create();
                    $user_email = $faker->unique()->email;
                } else {
                    $user_email = $request->email;
                }

                $retailer = User::where('id', $user_id)->first();
                $retailer_name = $retailer->name;
                $company_name = $retailer->company_name;
                $retailer_email = $retailer->email;

                $org_password = Str::random(8);
                $password = Hash::make($org_password);

                if ($flag) {
                    $user = User::where("id", $details->user_id)->whereIn("parent_id", $related_users)->first();
                } else {
                    $details = new customers_details();
                    $user = "";
                }

                if (!$user) {
                    $user = new User;
                    $user->category_id = 20;
                    $user->role_id = 3;
                    $user->password = $password;
                    $user->temp_password = $org_password;
                    $user->allowed = 0;
                    $user->reeleezee_guid = $request->reeleezee_id;
                    $user->parent_id = $user_id;

                    $universal_customers_details = new universal_customers_details;
                } else {
                    $universal_customers_details = universal_customers_details::where("user_id", $user->id)->first();
                }

                $user->name = $request->name;
                $user->family_name = $request->family_name ? $request->family_name : '';
                // $user->business_name = $request->business_name;
                // $user->address = $request->address;
                // $user->postcode = $request->postcode;
                // $user->city = $request->city;
                // $user->phone = $request->phone;
                $user->email = $user_email;
                $user->fake_email = !$request->email ? 1 : 0;
                $user->save();

                $universal_customers_details->user_id = $user->id;
                $universal_customers_details->business_name = $request->business_name;
                $universal_customers_details->address = $request->address;
                $universal_customers_details->postcode = $request->postcode;
                $universal_customers_details->city = $request->city;
                $universal_customers_details->phone = $request->phone;
                $universal_customers_details->save();

                $input = $request->all();

                $details->user_id = $user->id;
                $details->retailer_id = $user_id;
                $details->name = $request->name;
                $details->family_name = $request->family_name ? $request->family_name : '';
                $details->business_name = $request->business_name;
                $details->postcode = $request->postcode;
                $details->address = $request->address;
                $details->street_name = $request->street_name;
                $details->street_number = $request->street_number;
                $details->city = $request->city;
                $details->phone = $request->phone;
                $details->email_address = $request->email;
                $details->external_relation_number = $request->external_relation_number;
                $details->customer_number = $request->customer_number ? $request->customer_number : $counter_customer_number;

                if ($request->reeleezee_id) {
                    $details->reeleezee_guid = $request->reeleezee_id;
                }

                $details->contact_persons = $contact_persons;
                $details->projects = $projects;
                $details->entity_type = $request->entity_type;
                $details->entity_description = $entity_description;
                $details->entity_description_nl = $entity_description_nl;
                $details->entity_name = $entity_name;
                $details->save();

                if (!$request->customer_number) {
                    $this->increment_customer_number($user_id);
                }

                $input['id'] = $user->id;

                // if($request->email)
                // {
                //     $link = url('/') . '/aanbieder/client-new-quotations';

                //     if($this->lang->lang == 'du')
                //     {
                //         $msg = "Beste $user_name,<br><br>Er is een account voor je gecreëerd door " . $company_name . ". Hier kan je offertes bekijken, verzoek tot aanpassen of de offerte accepteren. <a href='" . $link . "'>Klik hier</a>, om je naar je persoonlijke dashboard te gaan.<br><br><b>Wachtwoord:</b><br><br>Je wachtwoord is: " . $org_password . "<br><br>Met vriendelijke groeten,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                //     }
                //     else
                //     {
                //         $msg = "Dear Mr/Mrs " . $user_name . ",<br><br>Your account has been created by retailer " . $retailer_name . " for quotations. Kindly complete your profile and change your password. You can go to your dashboard through <a href='" . $link . "'>here.</a><br><br>Your Password: " . $org_password . "<br><br>Kind regards,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                //     }

                //     \Mail::send(array(), array(), function ($message) use ($msg, $user_email, $user_name, $retailer_name, $link, $org_password, $company_name, $retailer_email) {
                //         $message->to($user_email)
                //             ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl',$company_name)
                //             ->replyTo($retailer_email,$company_name)
                //             ->subject(__('text.Account Created!'))
                //             ->html($msg,'text/html');
                //     });
                // }

                if ($flag) {
                    Session::flash('success', __('text.Customer details updated successfully'));
                } else {
                    Session::flash('success', __('text.Customer account created successfully'));
                }
            }

            if ($request->is_form) {
                return redirect()->route('customers');
            } else {
                return;
            }
        }
    }

    public function PostEmployee(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where("id",$main_id)->first();
        //     $user_id = $user->id;
        // }

        $company_name = $user->company_name;

        // if($request->profile_type == 1)
        // {
        //     if($request->employee_number <= 0)
        //     {
        //         Session::flash('unsuccess', __("text.Employee number is invalid."));
        //         return redirect()->back();
        //     }

        //     if($request->employee_number)
        //     {
        //         if($request->emp_id)
        //         {
        //             $check_em_number = employees_details::where("user_id","!=",$request->emp_id)->where("employee_number",$request->employee_number)->first();
        //             $check_em_number_draft = user_drafts::where("user_id","!=",$request->emp_id)->where("employee_number",$request->employee_number)->first();
        //         }
        //         else
        //         {
        //             $check_em_number = employees_details::where("employee_number",$request->employee_number)->first();
        //             $check_em_number_draft = user_drafts::where("employee_number",$request->employee_number)->first();
        //         }

        //         if($check_em_number || $check_em_number_draft)
        //         {
        //             Session::flash('unsuccess', __("text.Employee number already taken."));
        //             return redirect()->back();
        //         }
        //     }
        // }
        // else
        // {
        //     if($request->freelancer_number <= 0)
        //     {
        //         Session::flash('unsuccess', __("text.Freelancer number is invalid."));
        //         return redirect()->back();
        //     }

        //     if($request->freelancer_number)
        //     {
        //         if($request->emp_id)
        //         {
        //             $check_fr_number = employees_details::where("user_id","!=",$request->emp_id)->where("freelancer_number",$request->freelancer_number)->first();
        //             $check_fr_number_draft = user_drafts::where("user_id","!=",$request->emp_id)->where("freelancer_number",$request->freelancer_number)->first();
        //         }
        //         else
        //         {
        //             $check_fr_number = employees_details::where("freelancer_number",$request->freelancer_number)->first();
        //             $check_fr_number_draft = user_drafts::where("freelancer_number",$request->freelancer_number)->first();
        //         }

        //         if($check_fr_number || $check_fr_number_draft)
        //         {
        //             Session::flash('unsuccess', __("text.Freelancer number already taken."));
        //             return redirect()->back();
        //         }
        //     }
        // }

        $input = $request->all();

        if ($request->emp_id) {
            $rules = [
                'email' => [
                    'required',
                    'string',
                    'email',
                    // Rule::unique('users')->where(function($query) use($request) {
                    //     $query->where('allowed', '=', '1')->where('deleted_at', NULL)->where('id', '!=', $request->emp_id);
                    // })
                    Rule::unique('users')->where(function ($query) use ($request) {
                        $query->where('deleted_at', NULL)->where('id', '!=', $request->emp_id);
                    })
                ],
                'name' => 'required|regex:/(^[A-Za-z ]+$)+/|max:15',
                'family_name' => 'required|regex:/(^[A-Za-z ]+$)+/|max:15',
                /*'company_name' => 'required',*/
                // 'registration_number' => 'required',
                'postcode' => 'required',
                'city' => 'required',
                /*'bank_account' => 'required',*/
                /*'tax_number' => 'required',*/
                'address' => 'required',
                'phone' => 'required',
            ];

            // Add password validation rules only if password is not empty
            if ($request->password) {
                $rules['password'] = 'min:8|confirmed';
            }

            // Custom error messages
            $messages = [
                'email.required' => $this->lang->erv,
                'email.unique' => $this->lang->euv,
                'name.required' => $this->lang->nrv,
                'name.max' => $this->lang->nmv,
                'name.regex' => $this->lang->niv,
                'family_name.required' => $this->lang->fnrv,
                'family_name.max' => $this->lang->fnmrv,
                'family_name.regex' => $this->lang->fniv,
                /*'company_name.required' => $this->lang->cnrv,*/
                // 'registration_number.required' => $this->lang->rnrv,
                /*'bank_account.required' => $this->lang->barv,
                'tax_number.required' => $this->lang->tnrv,*/
                'postcode.required' => $this->lang->pcrv,
                'city.required' => $this->lang->crv,
                'address.required' => $this->lang->arv,
                'phone.required' => $this->lang->prv
            ];

            // Validate the request
            $this->validate($request, $rules, $messages);

            $employee = User::where("id", $request->emp_id)->first();
            $data = ['name' => $request->name, 'family_name' => $request->family_name];

            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }

            User::where('id', $request->emp_id)->update($data);

            $employee_details = $employee->employee;
            $employee_details->profile_type = $request->profile_type;
            $employee_details->contract = $request->profile_type == 1 ? "Employee" : "Freelancer";
            $employee_details->name = $request->name;
            $employee_details->email = $request->email;
            $employee_details->postcode = $request->postcode;
            $employee_details->city = $request->city;
            $employee_details->phone = $request->phone;
            $employee_details->address = $request->address;
            $employee_details->contract_number = $request->contract_number;
            // $employee_details->employee_number = $request->profile_type == 1 ? $request->employee_number : NULL;
            // $employee_details->freelancer_number = $request->profile_type == 2 ? $request->freelancer_number : NULL;
            $employee_details->personal_number = $request->profile_type == 1 ? $request->personal_number : NULL;
            $employee_details->freelancer_registration_number = $request->profile_type == 2 ? $request->freelancer_registration_number : NULL;
            $employee_details->business_name = $request->profile_type == 2 ? $request->business_name : NULL;
            $employee_details->tax_number = $request->profile_type == 2 ? $request->tax_number : NULL;
            $employee_details->bank_account = $request->profile_type == 2 ? $request->bank_account : NULL;
            $employee_details->save();

            Session::flash('success', 'Employee information updated successfully');
        } else {
            $this->validate(
                $request,
                [
                    'email' => [
                        'required',
                        'string',
                        'email',
                        // Rule::unique('users')->where(function($query) {
                        //     $query->where('allowed', '=', '1')->where('deleted_at', NULL);
                        // })
                        Rule::unique('users')->where(function ($query) {
                            $query->where('deleted_at', NULL);
                        })
                    ],
                    'name'   => 'required|regex:/(^[A-Za-z ]+$)+/|max:15',
                    'family_name' => 'required|regex:/(^[A-Za-z ]+$)+/|max:15',
                    /*'company_name' => 'required',*/
                    // 'registration_number' => 'required',
                    'postcode' => 'required',
                    'city' => 'required',
                    /*'bank_account' => 'required',*/
                    /*'tax_number' => 'required',*/
                    'address' => 'required',
                    'phone' => 'required',
                    'password' => 'required|min:8|confirmed',
                ],
                [
                    'email.required' => $this->lang->erv,
                    'email.unique' => $this->lang->euv,
                    'name.required' => $this->lang->nrv,
                    'name.max' => $this->lang->nmv,
                    'name.regex' => $this->lang->niv,
                    'family_name.required' => $this->lang->fnrv,
                    'family_name.max' => $this->lang->fnmrv,
                    'family_name.regex' => $this->lang->fniv,
                    /*'company_name.required' => $this->lang->cnrv,*/
                    // 'registration_number.required' => $this->lang->rnrv,
                    /*'bank_account.required' => $this->lang->barv,
                'tax_number.required' => $this->lang->tnrv,*/
                    'postcode.required' => $this->lang->pcrv,
                    'city.required' => $this->lang->crv,
                    'address.required' => $this->lang->arv,
                    'phone.required' => $this->lang->prv,
                    'password.required' => $this->lang->parv,
                    'password.min' => $this->lang->pamv,
                    'password.confirmed' => $this->lang->pacv,
                ]
            );

            $employee = new User;
            $employee->category_id = $request->category_id;
            $employee->role_id = $role_id;
            $employee->password = bcrypt($request['password']);
            $employee->name = $request->name;
            $employee->family_name = $request->family_name;
            // $employee->address = $request->address;
            // $employee->postcode = $request->postcode;
            // $employee->city = $request->city;
            // $employee->phone = $request->phone;
            $employee->email = $request->email;
            // $employee->main_id = $user_id;
            $employee->status = 1;
            $employee->active = 1;
            $employee->is_featured = 1;
            $employee->featured = 1;
            $employee->verified = 1;
            // $employee->business_name = $request->profile_type == 2 ? $request->business_name : NULL;
            // $employee->tax_number = $request->profile_type == 2 ? $request->tax_number : NULL;
            // $employee->bank_account = $request->profile_type == 2 ? $request->bank_account : NULL;
            $employee->save();

            user_organizations::create([
                'user_id' => $employee->id,
                'organization_id' => $user->organization->id,
            ]);

            $employee_details = new employees_details;
            $employee_details->user_id = $employee->id;
            $employee_details->profile_type = $request->profile_type;
            $employee_details->contract = $request->profile_type == 1 ? "Employee" : "Freelancer";
            $employee_details->name = $request->name;
            $employee_details->email = $request->email;
            $employee_details->postcode = $request->postcode;
            $employee_details->city = $request->city;
            $employee_details->phone = $request->phone;
            $employee_details->address = $request->address;
            $employee_details->contract_number = $request->contract_number;
            // $employee_details->employee_number = $request->profile_type == 1 ? $request->employee_number : NULL;
            // $employee_details->freelancer_number = $request->profile_type == 2 ? $request->freelancer_number : NULL;
            $employee_details->personal_number = $request->profile_type == 1 ? $request->personal_number : NULL;
            $employee_details->freelancer_registration_number = $request->profile_type == 2 ? $request->freelancer_registration_number : NULL;
            $employee_details->business_name = $request->profile_type == 2 ? $request->business_name : NULL;
            $employee_details->tax_number = $request->profile_type == 2 ? $request->tax_number : NULL;
            $employee_details->bank_account = $request->profile_type == 2 ? $request->bank_account : NULL;
            $employee_details->save();

            $check_permission = Permission::where('name', '=', 'show-dashboard')->first();

            if (!$check_permission) {
                Permission::create(['guard_name' => 'user', 'name' => 'show-dashboard']);
            }

            $employee->givePermissionTo('show-dashboard');

            \Mail::send(array(), array(), function ($message) use ($request, $company_name) {
                $message->to($request->email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject("Account Created!")
                    ->html("Dear Mr/Mrs " . $request->name . ",<br><br>Your company <b>" . $company_name . "</b> has created an employee account for you. Here is your username and password <br>Username: " . $request->email . "<br>Password: " . $request->password . "<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });

            Session::flash('success', __('text.Employee created successfully!'));
        }

        // if($request->profile_type == 1 && $request->employee_number)
        // {
        //     $this->increment_employee_number();
        // }

        // if($request->profile_type == 2 && $request->freelancer_number)
        // {
        //     $this->increment_freelancer_number();
        // }

        return redirect()->route('employees');
    }

    public function InstructionManual()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }


        if ($user->can('instruction-manual')) {
            $data = instruction_manual::first();

            return view('user.instruction_manual', compact('data'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function CreateCustomer(Request $request)
    {
        /*$this->validate($request, [
            'password' => 'required|min:8',
        ],

            [
                'password.min' => $this->lang->pamv,

            ]);*/

        $input = $request->all();
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

        $counter_customer_number = $this->get_next_customer_number($user_id);

        $flag = 0;
        $flag1 = 0;

        if ($request->customer_id) {
            $customer_user_id = customers_details::where('id', $request->customer_id)->pluck('user_id')->first();

            $check = User::where('email', $request->email)->where('id', '!=', $customer_user_id)->first();

            $f_e = User::where('id', $customer_user_id)->first();

            if ($check) {
                $response = array('data' => $check, 'message' => __('text.This email address is already taken'));
                return $response;
            } else {
                if (!$request->email) {
                    if ($f_e->fake_email) {
                        $user_email = $f_e->email;
                    } else {
                        $faker = \Faker\Factory::create();
                        $user_email = $faker->unique()->email;
                    }

                    $fake_email = 1;
                } else {
                    $user_email = $request->email;
                    $fake_email = 0;
                }

                $check1 = User::where('email', $request->email)->where('id', $customer_user_id)->first();

                if ($check1) {
                    customers_details::where('id', $request->customer_id)->update(['email_address' => $request->email, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone]);
                } else {
                    $other_retailers_link = customers_details::where('user_id', $customer_user_id)->whereNotIn('retailer_id', $related_users)->first();

                    if ($other_retailers_link) {
                        $user_name = $request->name;
                        $org_password = Str::random(8);
                        $password = Hash::make($org_password);

                        $user = new User;
                        $user->category_id = 20;
                        $user->role_id = 3;
                        $user->password = $password;
                        $user->temp_password = $org_password;
                        $user->name = $request->name;
                        $user->family_name = $request->family_name ? $request->family_name : '';
                        // $user->business_name = $request->business_name;
                        // $user->address = $request->address;
                        // $user->postcode = $request->postcode;
                        // $user->city = $request->city;
                        // $user->phone = $request->phone;
                        $user->email = $user_email;
                        $user->parent_id = $user_id;
                        $user->allowed = 0;
                        $user->fake_email = $fake_email;
                        $user->save();

                        $universal_customers_details = new universal_customers_details;
                        $universal_customers_details->user_id = $user->id;
                        $universal_customers_details->business_name = $request->business_name;
                        $universal_customers_details->address = $request->address;
                        $universal_customers_details->postcode = $request->postcode;
                        $universal_customers_details->city = $request->city;
                        $universal_customers_details->phone = $request->phone;
                        $universal_customers_details->save();

                        customers_details::where('id', $request->customer_id)->update(['email_address' => $request->email, 'user_id' => $user->id, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone]);
                    } else {
                        User::where('id', $customer_user_id)->update(["email" => $user_email, "fake_email" => $fake_email]);
                        customers_details::where('id', $request->customer_id)->update(['email_address' => $request->email, 'name' => $request->name, 'family_name' => $request->family_name ? $request->family_name : '', 'business_name' => $request->business_name, 'address' => $request->address, 'street_name' => $request->street_name, 'street_number' => $request->street_number, 'postcode' => $request->postcode, 'city' => $request->city, 'phone' => $request->phone]);
                    }
                }
            }

            $input['id'] = $request->customer_id;

            $response = array('data' => $input, 'message' => __('text.Customer details updated successfully'));
        } else {
            $check = User::where('email', $request->email)->first();

            if ($check) {

                /*if($check->role_id == 3)
                {
                    $check1 = customers_details::where('user_id',$check->id)->where('retailer_id',$user_id)->first();

                    if($check1)
                    {
                        $response = array('data' => $check, 'message' => __('text.User already created'));
                        return $response;
                    }
                    else
                    {
                        $flag1 = 1;
                    }
                }
                else
                {
                    $response = array('data' => $check, 'message' => 'This email address is already taken');
                    return $response;
                }*/

                if ($check->role_id != 3) {
                    $response = array('data' => $check, 'message' => 'This email address is already taken');
                    return $response;
                }

                $check1 = customers_details::where('user_id', $check->id)->whereIn('retailer_id', $related_users)->first();

                if ($check1) {
                    $response = array('data' => $check, 'message' => __('text.A customer is already linked with this email address.'));
                    return $response;
                } else {
                    $flag1 = 1;
                }
            } else {
                $flag = 1;
            }

            $p_name = $request->family_name ? $request->name . " " . $request->family_name : $request->name;
            $p_start_date = date("Y-m-d") . "T00:00:00";
            $p_end_date = date("Y-m-d", strtotime("+1 year", strtotime(date("Y-m-d")))) . "T00:00:00";
            $projects = [["name" => $p_name, "is_active" => true, "start_date" => $p_start_date, "end_date" => $p_end_date, "description" => null, "comment" => null, "total" => "0"]];
            $projects = json_encode($projects);

            if ($flag) {
                $user = new User;
                $user_name = $request->name;

                if (!$request->email) {
                    $faker = \Faker\Factory::create();
                    $user_email = $faker->unique()->email;
                } else {
                    $user_email = $request->email;
                }

                $retailer = User::where('id', $user_id)->first();
                $retailer_name = $retailer->name;
                $company_name = $retailer->company_name;
                $retailer_email = $retailer->email;

                $org_password = Str::random(8);
                $password = Hash::make($org_password);

                $user->role_id = 3;
                $user->category_id = 20;
                $user->name = $request->name;
                $user->family_name = $request->family_name ? $request->family_name : '';
                // $user->business_name = $request->business_name;
                // $user->postcode = $request->postcode;
                // $user->address = $request->address;
                // $user->city = $request->city;
                // $user->phone = $request->phone;
                $user->email = $user_email;
                $user->password = $password;
                $user->temp_password = $org_password;
                $user->parent_id = $user_id;
                $user->allowed = 0;
                $user->fake_email = !$request->email ? 1 : 0;
                $user->save();

                $universal_customers_details = new universal_customers_details;
                $universal_customers_details->user_id = $user->id;
                $universal_customers_details->business_name = $request->business_name;
                $universal_customers_details->address = $request->address;
                $universal_customers_details->postcode = $request->postcode;
                $universal_customers_details->city = $request->city;
                $universal_customers_details->phone = $request->phone;
                $universal_customers_details->save();

                $details = new customers_details();
                $details->user_id = $user->id;
                $details->retailer_id = $user_id;
                $details->name = $request->name;
                $details->family_name = $request->family_name ? $request->family_name : '';
                $details->business_name = $request->business_name;
                $details->postcode = $request->postcode;
                $details->address = $request->address;
                $details->street_name = $request->street_name;
                $details->street_number = $request->street_number;
                $details->city = $request->city;
                $details->phone = $request->phone;
                $details->email_address = $request->email;
                $details->customer_number = $counter_customer_number;
                $details->projects = $projects;
                $details->save();
                $this->increment_customer_number($user_id);

                $input['id'] = $details->id;

                // if($request->email)
                // {
                //     $link = url('/') . '/aanbieder/client-new-quotations';

                //     if($this->lang->lang == 'du')
                //     {
                //         $msg = "Beste $user_name,<br><br>Er is een account voor je gecreëerd door " . $company_name . ". Hier kan je offertes bekijken, verzoek tot aanpassen of de offerte accepteren. <a href='" . $link . "'>Klik hier</a>, om je naar je persoonlijke dashboard te gaan.<br><br><b>Wachtwoord:</b><br><br>Je wachtwoord is: " . $org_password . "<br><br>Met vriendelijke groeten,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                //     }
                //     else
                //     {
                //         $msg = "Dear Mr/Mrs " . $user_name . ",<br><br>Your account has been created by retailer " . $retailer_name . " for quotations. Kindly complete your profile and change your password. You can go to your dashboard through <a href='" . $link . "'>here.</a><br><br>Your Password: " . $org_password . "<br><br>Kind regards,<br><br>$retailer_name<br><br>Klantenservice<br><br>$company_name";
                //     }

                //     \Mail::send(array(), array(), function ($message) use ($msg, $user_email, $user_name, $retailer_name, $link, $org_password, $company_name, $retailer_email) {
                //         $message->to($user_email)
                //             ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl', $company_name)
                //             ->replyTo($retailer_email, $company_name)
                //             ->subject(__('text.Account Created!'))
                //             ->html($msg, 'text/html');
                //     });

                // }

            }

            if ($flag1) {
                $details = new customers_details();
                $details->user_id = $check->id;
                $details->retailer_id = $user_id;
                $details->name = $request->name;
                $details->family_name = $request->family_name ? $request->family_name : '';
                $details->business_name = $request->business_name;
                $details->postcode = $request->postcode;
                $details->address = $request->address;
                $details->street_name = $request->street_name;
                $details->street_number = $request->street_number;
                $details->city = $request->city;
                $details->phone = $request->phone;
                $details->email_address = $request->email;
                $details->customer_number = $counter_customer_number;
                $details->projects = $projects;
                $details->save();
                $this->increment_customer_number($user_id);

                $input['id'] = $details->id;
            }

            $response = array('data' => $input, 'message' => __('text.Customer account created successfully'));
        }

        return $response;
    }

    public function GetCustomerEmail(Request $request)
    {
        $id = $request->id;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $current_user_name = $user->name;
        // $main_id = $user->main_id;
        $req_type = $request->type;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $user = User::where('id',$main_id)->first();
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (isset($request->customer_type) && $request->customer_type == 0) {
            $data = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_quotations.id', $id)->select('new_quotations.*', 'quotes.quote_name as name', 'quotes.quote_email as email')->first();
        } else {
            $data = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.user_id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->where('new_quotations.id', $id)->select('new_quotations.*', 'customers_details.name', 'users.email', 'users.fake_email')->first();
        }

        if ($req_type == 'quotation') {
            $check_draft = email_drafts::where('type', 'quotation')->where('quotation_id', $id)->first();

            if ($check_draft) {
                $mail_subject_template = $check_draft->subject;
                $mail_body_template = $check_draft->body;
                $data->fake_email = 0;
                $data->email = $check_draft->mail_to;
                $data->ccs = $check_draft->ccs;
            } else {
                $mail_template = email_templates::where('type', 'quotation')->whereIn('user_id', $related_users)->first();

                if (!$mail_template) {
                    $mail_subject_template = 'Offerte: {offerte_nummer}';
                    $mail_body_template = '<div class="OutlineElement Ltr  BCX0 SCXW193241479" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW193241479 BCX0" paraid="1734808987" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{183}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">Beste {aan_voornaam},</span></span></p><p class="Paragraph SCXW193241479 BCX0" paraid="1734808987" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{183}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;"><br></span></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW193241479" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr;"><p class="Paragraph SCXW193241479 BCX0" paraid="2025117577" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{193}" style="font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px; color: windowtext; margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">Hierbij de offerte met offertenummer {offerte_nummer}.</span></span></p><p class="Paragraph SCXW193241479 BCX0" paraid="2025117577" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{193}" style="font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px; color: windowtext; margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;"><br></span></span></p><p class="Paragraph SCXW193241479 BCX0" paraid="2025117577" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{193}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none;"><span style="color: rgb(85, 85, 85); font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-size: 16px; font-variant-ligatures: none;">{Klik hier om de offerte in je account te bekijken}</span></p><p class="Paragraph SCXW193241479 BCX0" paraid="2025117577" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{193}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none;"><span style="color: rgb(85, 85, 85); font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-size: 16px; font-variant-ligatures: none;"><br></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW193241479" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW193241479 BCX0" paraid="588099994" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{36}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;">Met vriendelijke groet,</span><span class="LineBreakBlob BlobObject DragDrop SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><br class="SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_voornaam}</span></span><span class="LineBreakBlob BlobObject DragDrop SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><br class="SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW193241479 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW193241479 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_bedrijfsnaam}</span></span></p></div>';
                } else {
                    $mail_subject_template = $mail_template->subject;
                    $mail_body_template = $mail_template->body;
                }
            }

            $mail_deliver_to = "";
            $mail_delivery_date = "";

            $link = route('accept-new-quotation-mail', ['id' => Crypt::encrypt($data->id)]);
            $quotations_link = route('client-new-quotations');

            $mail_subject_template = str_replace('{offerte_nummer}', $data->quotation_invoice_number, $mail_subject_template);
            $mail_body_template = str_replace('{aan_voornaam}', $data->name, $mail_body_template);
            $mail_body_template = str_replace('{offerte_nummer}', $data->quotation_invoice_number, $mail_body_template);
            $mail_body_template = str_replace('{Click here to accept this quote directly online or click here to view quotes in your account}', 'Click <a style="color: blue;" href="' . $link . '">here</a> to accept this quote directly online or click <a style="color: blue;" href="' . $quotations_link . '">here</a> to view quotes in your account', $mail_body_template);
            $mail_body_template = str_replace('{Klik hier om de offerte in je account te bekijken}', 'Klik <a style="color: blue;" href="' . $quotations_link . '">hier</a> om de offerte in je account te bekijken', $mail_body_template);
            $mail_body_template = str_replace('{van_voornaam}', $current_user_name, $mail_body_template);
            $mail_body_template = str_replace('{van_bedrijfsnaam}', $user->company_name, $mail_body_template);
        } else if ($req_type == 'order') {
            $check_draft = email_drafts::where('type', 'order')->where('quotation_id', $id)->first();

            if ($check_draft) {
                $mail_subject_template = $check_draft->subject;
                $mail_body_template = $check_draft->body;
                $mail_deliver_to = $check_draft->deliver_to;
                $mail_delivery_date = $check_draft->delivery_date;
                $data->ccs = $check_draft->ccs;
            } else {
                $mail_template = email_templates::where('type', 'order')->whereIn('user_id', $related_users)->first();

                if (!$mail_template) {
                    $mail_subject_template = 'Order: {order_nummer}';
                    $mail_body_template = '<div class="OutlineElement Ltr  BCX0 SCXW88000976" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW88000976 BCX0" paraid="1492123990" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{70}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW88000976 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">Beste {aan_voornaam},</span></span></p><p class="Paragraph SCXW88000976 BCX0" paraid="1492123990" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{70}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW88000976 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;"><br></span></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW88000976" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW88000976 BCX0" paraid="175123627" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{80}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span class="LineBreakBlob BlobObject DragDrop SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><span class="NormalTextRun SCXW140321656 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-variant-ligatures: none;">Hiebij de order met ordernummer {order_nummer}.</span></span></p><p class="Paragraph SCXW88000976 BCX0" paraid="175123627" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{80}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span class="LineBreakBlob BlobObject DragDrop SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><span class="NormalTextRun SCXW140321656 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-variant-ligatures: none;"><br></span></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW88000976" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW88000976 BCX0" paraid="1552157293" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{102}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;">Graag ontvangen we hier een orderbevestiging van.</span></p></div><div class="OutlineElement Ltr  BCX0 SCXW88000976" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW88000976 BCX0" paraid="2113674558" paraeid="{067a829f-0db6-4a3a-8ad2-66efb8a422c4}{108}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;">Met vriendelijke groet,</span><span class="LineBreakBlob BlobObject DragDrop SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><br class="SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW88000976 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_voornaam}</span></span><span class="LineBreakBlob BlobObject DragDrop SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><br class="SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW88000976 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW88000976 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_bedrijfsnaam}</span></span></p></div>';
                } else {
                    $mail_subject_template = $mail_template->subject;
                    $mail_body_template = $mail_template->body;
                }

                $mail_deliver_to = "";
                $mail_delivery_date = "";
            }
        } else {
            if ($req_type == 'invoice') {
                $type = 'invoice';
            } else {
                $type = 'negative-invoice';
            }

            $check_draft = email_drafts::where('type', $type)->where('quotation_id', $id)->first();

            if ($check_draft) {
                $mail_subject_template = $check_draft->subject;
                $mail_body_template = $check_draft->body;
                $data->fake_email = 0;
                $data->email = $check_draft->mail_to;
                $data->ccs = $check_draft->ccs;
            } else {
                $mail_template = email_templates::where('type', $type)->whereIn('user_id', $related_users)->first();

                if (!$mail_template) {
                    $mail_subject_template = 'Factuur: {factuur_nummer}';
                    $mail_body_template = '<div class="OutlineElement Ltr  BCX0 SCXW91431922" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW91431922 BCX0" paraid="1500201224" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{95}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW91431922 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">Beste {aan_voornaam},</span></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW91431922" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW91431922 BCX0" paraid="44738701" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{105}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW91431922 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;"><br></span></span></p><p class="Paragraph SCXW91431922 BCX0" paraid="44738701" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{105}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span class="LineBreakBlob BlobObject DragDrop SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><span class="NormalTextRun  BCX0 SCXW141786481" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-variant-ligatures: none;">Hierbij de factuur met factuurnummer {factuur_nummer}.</span></span></p><p class="Paragraph SCXW91431922 BCX0" paraid="44738701" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{105}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span class="LineBreakBlob BlobObject DragDrop SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><span class="NormalTextRun  BCX0 SCXW141786481" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif; font-variant-ligatures: none;"><br></span></span></p></div><div class="OutlineElement Ltr  BCX0 SCXW91431922" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow: visible; cursor: text; clear: both; position: relative; direction: ltr; color: rgb(0, 0, 0); font-family: &quot;Segoe UI&quot;, &quot;Segoe UI Web&quot;, Arial, Verdana, sans-serif; font-size: 12px;"><p class="Paragraph SCXW91431922 BCX0" paraid="1765816471" paraeid="{d22048a8-261e-4878-810c-cb993567f9d4}{151}" style="margin-bottom: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; overflow-wrap: break-word; vertical-align: baseline; font-kerning: none; color: windowtext;"><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;">Met vriendelijke groet,</span><span class="LineBreakBlob BlobObject DragDrop SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><span class="SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;">&nbsp;</span><br class="SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW91431922 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_voornaam}</span></span><span class="LineBreakBlob BlobObject DragDrop SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-size: 12pt; line-height: 18px; font-family: WordVisiCarriageReturn_MSFontService, open_sansregular, open_sansregular_EmbeddedFont, sans-serif; color: rgb(85, 85, 85);"><br class="SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; white-space: pre !important;"></span><span data-contrast="none" xml:lang="NL-NL" lang="NL-NL" class="TextRun SCXW91431922 BCX0" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent; font-variant-ligatures: none !important; color: rgb(85, 85, 85); font-size: 12pt; line-height: 18px; font-family: open_sansregular, open_sansregular_EmbeddedFont, sans-serif;"><span class="NormalTextRun SCXW91431922 BCX0" data-ccp-parastyle="Normal (Web)" style="margin: 0px; padding: 0px; user-select: text; -webkit-user-drag: none; -webkit-tap-highlight-color: transparent;">{van_bedrijfsnaam}</span></span></p></div>';
                } else {
                    $mail_subject_template = $mail_template->subject;
                    $mail_body_template = $mail_template->body;
                }

                $mail_subject_template = str_replace('{factuur_nummer}', $data->invoice_number, $mail_subject_template);
                $mail_body_template = str_replace('{aan_voornaam}', $data->name, $mail_body_template);
                $mail_body_template = str_replace('{factuur_nummer}', $data->invoice_number, $mail_body_template);
                $mail_body_template = str_replace('{van_voornaam}', $current_user_name, $mail_body_template);
                $mail_body_template = str_replace('{van_bedrijfsnaam}', $user->company_name, $mail_body_template);
            }

            $mail_deliver_to = "";
            $mail_delivery_date = "";
        }

        $post = array($data->email, $mail_subject_template, $mail_body_template, $data->fake_email, $mail_deliver_to, $mail_delivery_date, $data->ccs);

        return $post;
    }

    public function DownloadHandymanQuoteRequest($id)
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

        if ($user->can('download-handyman-quote-request')) {
            $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->leftjoin('brands', 'brands.id', '=', 'quotes.quote_brand')->leftjoin('models', 'models.id', '=', 'quotes.quote_model')->leftjoin('services', 'services.id', '=', 'quotes.quote_service1')->leftjoin('handyman_quotes', 'handyman_quotes.quote_id', '=', 'quotes.id')->where('quotes.id', $id)->where('handyman_quotes.handyman_id', $organization_id)->select('quotes.*', 'categories.cat_name', 'services.title', 'brands.cat_name as brand_name', 'models.cat_name as model_name')->first();

            $q_a = requests_q_a::where('request_id', $id)->get();

            if ($quote) {

                $date = strtotime($quote->created_at);

                $quote_number = $quote->quote_number;

                $filename = $quote_number . '.pdf';

                $file = public_path() . '/assets/quotesPDF/' . $filename;

                if (!file_exists($file)) {

                    $role = 2;

                    ini_set('max_execution_time', 180);

                    $pdf = PDF::loadView('admin.user.pdf_quote', compact('quote', 'q_a', 'role'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                    $pdf->save(public_path() . '/assets/quotesPDF/' . $filename);
                }

                return response()->download(public_path("assets/quotesPDF/{$filename}"));
            } else {
                return redirect('aanbieder/dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function CreateQuotation($id)
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

        if ($user->can('create-quotation')) {
            $quote = quotes::leftjoin('handyman_quotes', 'handyman_quotes.quote_id', '=', 'quotes.id')->where('quotes.id', $id)->where('handyman_quotes.handyman_id', $organization_id)->select('quotes.*')->first();

            $all_products = Products::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'products.id')->leftjoin('categories', 'categories.id', '=', 'products.category_id')->whereIn('handyman_products.handyman_id', $related_users)->select('products.*', 'categories.cat_name', 'handyman_products.sell_rate as rate')->get();
            $all_services = Service::leftjoin('handyman_services', 'handyman_services.service_id', '=', 'services.id')->whereIn('handyman_services.handyman_id', $related_users)->select('services.*', 'handyman_services.sell_rate as rate')->get();
            $items = items::whereIn('user_id', $related_users)->get();

            $settings = Generalsetting::findOrFail(1);

            $vat_percentage = $settings->vat;

            if ($quote) {
                return view('user.create_quotation', compact('quote', 'vat_percentage', 'items', 'all_services', 'all_products', 'user_id'));
            } else {
                return redirect('aanbieder/dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ViewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'view-handyman-quotation') {
            if ($user->can('view-handyman-quotation')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'edit-handyman-quotation') {
            if ($user->can('edit-handyman-quotation')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'create-handyman-invoice') {
            if ($user->can('create-handyman-invoice')) {
                $check = 1;
            }
        }

        if ($check) {
            $settings = Generalsetting::findOrFail(1);

            $vat_percentage = $settings->vat;

            $quotation = quotation_invoices::leftjoin('quotation_invoices_data', 'quotation_invoices_data.quotation_id', '=', 'quotation_invoices.id')->leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->where('quotation_invoices.id', $id)->whereIn('quotation_invoices.handyman_id', $related_users)->select('quotation_invoices.*', 'quotes.quote_zipcode', 'quotes.quote_postcode', 'quotes.quote_city', 'quotes.id as quote_id', 'quotes.quote_number', 'quotes.created_at as quote_date', 'quotation_invoices_data.id as data_id', 'quotation_invoices_data.product_title', 'quotation_invoices_data.s_i_id', 'quotation_invoices_data.b_i_id', 'quotation_invoices_data.m_i_id', 'quotation_invoices_data.item', 'quotation_invoices_data.is_service', 'quotation_invoices_data.service', 'quotation_invoices_data.brand', 'quotation_invoices_data.model', 'quotation_invoices_data.rate', 'quotation_invoices_data.qty', 'quotation_invoices_data.description as data_description', 'quotation_invoices_data.estimated_date', 'quotation_invoices_data.amount')->get();

            $all_products = Products::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'products.id')->leftjoin('categories', 'categories.id', '=', 'products.category_id')->whereIn('handyman_products.handyman_id', $related_users)->select('products.*', 'categories.cat_name', 'handyman_products.sell_rate as rate')->get();
            $all_services = Service::leftjoin('handyman_services', 'handyman_services.service_id', '=', 'services.id')->whereIn('handyman_services.handyman_id', $related_users)->select('services.*', 'handyman_services.sell_rate as rate')->get();
            $items = items::whereIn('user_id', $related_users)->get();

            if (count($quotation) != 0) {

                return view('user.quotation', compact('quotation', 'vat_percentage', 'user_id', 'all_products', 'all_services', 'items'));
            } else {
                return redirect('aanbieder/dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ViewCustomQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'view-custom-quotation') {
            if ($user->can('view-custom-quotation')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'edit-custom-quotation') {
            if ($user->can('edit-custom-quotation')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'create-custom-invoice') {
            if ($user->can('create-custom-invoice')) {
                $check = 1;
            }
        }

        if ($check) {
            $settings = Generalsetting::findOrFail(1);

            $vat_percentage = $settings->vat;

            $quotation = custom_quotations::leftjoin('custom_quotations_data', 'custom_quotations_data.quotation_id', '=', 'custom_quotations.id')->where('custom_quotations.id', $id)->whereIn('custom_quotations.handyman_id', $related_users)->select('custom_quotations.*', 'custom_quotations_data.id as data_id', 'custom_quotations_data.product_title', 'custom_quotations_data.s_i_id', 'custom_quotations_data.b_i_id', 'custom_quotations_data.m_i_id', 'custom_quotations_data.item', 'custom_quotations_data.is_service', 'custom_quotations_data.service', 'custom_quotations_data.brand', 'custom_quotations_data.model', 'custom_quotations_data.rate', 'custom_quotations_data.qty', 'custom_quotations_data.description as data_description', 'custom_quotations_data.estimated_date', 'custom_quotations_data.amount')->get();

            $all_products = Products::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'products.id')->leftjoin('categories', 'categories.id', '=', 'products.category_id')->whereIn('handyman_products.handyman_id', $related_users)->select('products.*', 'categories.cat_name', 'handyman_products.sell_rate as rate')->get();
            $all_services = Service::leftjoin('handyman_services', 'handyman_services.service_id', '=', 'services.id')->whereIn('handyman_services.handyman_id', $related_users)->select('services.*', 'handyman_services.sell_rate as rate')->get();
            $items = items::whereIn('user_id', $related_users)->get();

            if (count($quotation) != 0) {

                return view('user.quotation', compact('quotation', 'all_products', 'all_services', 'vat_percentage', 'items', 'user_id'));
            } else {
                return redirect('aanbieder/dashboard');
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ViewClientQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $check = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_quotations.id', $id)->where(function ($query) use ($user_id) {
            $query->where('quotes.user_id', $user_id)->orWhere('new_quotations.user_id', $user_id);
        })->first();

        if ($check) {
            // $invoice = new_quotations_data::leftjoin('new_quotations','new_quotations.id','=','new_quotations_data.quotation_id')->leftjoin('products','products.id','=','new_quotations_data.product_id')->where('new_quotations.id', $id)->where('new_quotations.deleted_at',NULL)->select('new_quotations.*','new_quotations_data.item_id','new_quotations_data.service_id','new_quotations.delivery_date as retailer_delivery_date','new_quotations.installation_date as retailer_installation_date','new_quotations.id as invoice_id','new_quotations_data.box_quantity','new_quotations_data.measure','new_quotations_data.max_width','new_quotations_data.order_number','new_quotations_data.discount','new_quotations_data.labor_discount','new_quotations_data.total_discount','new_quotations_data.price_before_labor','new_quotations_data.labor_impact','new_quotations_data.model_impact_value','new_quotations_data.childsafe','new_quotations_data.childsafe_question','new_quotations_data.childsafe_answer','new_quotations_data.childsafe_x','new_quotations_data.childsafe_y','new_quotations_data.childsafe_diff','new_quotations_data.model_id','new_quotations_data.delivery_days','new_quotations_data.delivery_date','new_quotations_data.id','new_quotations_data.supplier_id','new_quotations_data.product_id','new_quotations_data.row_id','new_quotations_data.rate','new_quotations_data.basic_price','new_quotations_data.qty','new_quotations_data.amount','new_quotations_data.color','new_quotations_data.width','new_quotations_data.width_unit','new_quotations_data.height','new_quotations_data.height_unit','new_quotations_data.price_based_option','new_quotations_data.base_price','new_quotations_data.supplier_margin','new_quotations_data.retailer_margin','products.ladderband','products.ladderband_value','products.ladderband_price_impact','products.ladderband_impact_type')
            //     ->with(['features' => function($query)
            //     {
            //         $query->leftjoin('features','features.id','=','new_quotations_features.feature_id')
            //             /*->where('new_quotations_features.sub_feature',0)*/
            //             ->select('new_quotations_features.*','features.title','features.comment_box');
            //     }])
            //     ->with(['sub_features' => function($query)
            //     {
            //         $query->leftjoin('product_features','product_features.id','=','new_quotations_features.feature_id')
            //             /*->where('new_quotations_features.sub_feature',1)*/
            //             ->select('new_quotations_features.*','product_features.title');
            //     }])->with('calculations')->get();

            // if (!$invoice) {
            //     return redirect()->back();
            // }

            // $supplier_products = array();
            // $product_titles = array();
            // $item_titles = array();
            // $service_titles = array();
            // $color_titles = array();
            // $model_titles = array();
            // $product_suppliers = array();
            // $sub_products = array();
            // $colors = array();
            // $models = array();
            // $features = array();
            // $sub_features = array();

            // $f = 0;
            // $s = 0;

            // foreach ($invoice as $i => $item)
            // {
            //     $product_titles[] = product::where('id',$item->product_id)->pluck('title')->first();
            //     $item_titles[] = items::leftjoin('categories','categories.id','=','items.category_id')->where('items.id',$item->item_id)->select('items.cat_name','categories.cat_name as category')->first();
            //     $service_titles[] = Service::where('id',$item->service_id)->pluck('title')->first();
            //     $color_titles[] = colors::where('id',$item->color)->pluck('title')->first();
            //     $model_titles[] = product_models::where('id',$item->model_id)->pluck('model')->first();
            //     $product_suppliers[] = User::where('id',$item->supplier_id)->first();

            //     foreach ($item->features as $feature)
            //     {
            //         $features[$f] = product_features::leftjoin('model_features','model_features.product_feature_id','=','product_features.id')->where('product_features.product_id',$item->product_id)->where('product_features.heading_id',$feature->feature_id)->where('product_features.sub_feature',0)->where('model_features.model_id',$item->model_id)->where('model_features.linked',1)->select('product_features.*')->get();

            //         if($feature->ladderband)
            //         {
            //             $sub_products[$i] = new_quotations_sub_products::leftjoin('product_ladderbands','product_ladderbands.id','=','new_quotations_sub_products.sub_product_id')->where('new_quotations_sub_products.feature_row_id',$feature->id)->select('new_quotations_sub_products.*','product_ladderbands.title','product_ladderbands.code')->get();
            //         }

            //         $f = $f + 1;
            //     }

            //     foreach ($item->sub_features as $sub_feature)
            //     {
            //         $sub_features[$s] = product_features::where('product_id',$item->product_id)->where('main_id',$sub_feature->feature_id)->get();
            //         $s = $s + 1;
            //     }
            // }

            // return view('user.client_new_quotation', compact('product_titles','color_titles','model_titles','product_suppliers','features','sub_features','invoice','sub_products'));

            $quotation_invoice_number = $check->quotation_invoice_number;
            $filename = $quotation_invoice_number . '.pdf';
            $retailer_id = $check->creator_id;
            $organization_id = User::where("id", $retailer_id)->first()->organization->id;

            if ($check->draft) {
                // if(!file_exists(public_path("assets/draftQuotations/{$filename}")))
                // {
                //     copy(public_path("assets/newQuotations/{$filename}"), public_path("assets/draftQuotations/{$filename}"));
                // }

                $file = "assets/draftQuotations/" . $organization_id . "/" . $filename;
            } else {
                $file = "assets/newQuotations/" . $organization_id . "/" . $filename;
            }

            return view('user.client_new_quotation', compact('file'));
        } else {
            return redirect()->back();
        }
    }

    public function ViewClientCustomQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $settings = Generalsetting::findOrFail(1);

        $vat_percentage = $settings->vat;

        $quotation = custom_quotations::leftjoin('custom_quotations_data', 'custom_quotations_data.quotation_id', '=', 'custom_quotations.id')->where('custom_quotations.id', $id)->where('custom_quotations.user_id', $user_id)->select('custom_quotations.*', 'custom_quotations_data.id as data_id', 'custom_quotations_data.s_i_id', 'custom_quotations_data.b_i_id', 'custom_quotations_data.m_i_id', 'custom_quotations_data.item', 'custom_quotations_data.service', 'custom_quotations_data.brand', 'custom_quotations_data.model', 'custom_quotations_data.rate', 'custom_quotations_data.qty', 'custom_quotations_data.description as data_description', 'custom_quotations_data.estimated_date', 'custom_quotations_data.amount')->get();

        if (count($quotation) != 0) {
            $services = Category::all();

            $items = items::all();

            return view('user.client_quotation', compact('quotation', 'services', 'vat_percentage', 'items'));
        } else {
            return redirect('aanbieder/quotation-requests');
        }
    }

    public function NewOrders()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $invoices = new_quotations::whereIn('creator_id', $related_users)->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date')->with('orders')->get();

            return view('user.quote_invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function NewInvoices()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $invoices = new_quotations::leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.invoice', 1)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.invoice_date', 'customers_details.name', 'customers_details.family_name')->with('data')->get();

            return view('user.quote_invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function NewQuotations()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user->can('create-new-quotation')) {
            if ($user_role == 2) {
                $invoices = new_quotations::leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.status', '!=', 3)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations.created_at as invoice_date', 'customers_details.name', 'customers_details.family_name')->with('data')->get();
            } else {
                $invoices = new_quotations::leftjoin('new_quotations_data', 'new_quotations_data.quotation_id', '=', 'new_quotations.id')->leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->where('new_quotations_data.supplier_id', $organization_id)->where('new_quotations.finished', 1)->orderBy('new_quotations.created_at', 'desc')->select('new_quotations.*', 'new_quotations.id as invoice_id', 'new_quotations_data.id as data_id', 'new_quotations.created_at as invoice_date', 'new_quotations_data.order_number', 'new_quotations_data.approved as data_approved', 'new_quotations_data.processing as data_processing', 'new_quotations_data.delivered as data_delivered', 'customers_details.name', 'customers_details.family_name')->get();
                $invoices = $invoices->unique('invoice_id');
            }

            return view('user.quote_invoices', compact('invoices'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function EditNewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'view-new-quotation') {
            $check = new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->first();
        } else {
            $check = new_invoices::where('quotation_id', $id)->whereIn('creator_id', $related_users)->first();
        }

        if ($check) {
            $negative_payment_calculations = array();
            $customers = customers_details::leftjoin("users", "users.id", "=", "customers_details.user_id")->whereIn('customers_details.retailer_id', $related_users)->select("customers_details.*", "users.email", "users.fake_email")->get();

            if ($check->form_type == 1) {
                if ($user_role == 2) {
                    $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();

                    $suppliers = retailers_requests::leftJoin('supplier_categories', 'supplier_categories.organization_id', '=', 'retailers_requests.supplier_organization')
                        ->where("retailers_requests.retailer_organization", $organization_id)
                        ->where('supplier_categories.category_id', $floor_category_id)
                        ->where('retailers_requests.status', 1)->where('retailers_requests.active', 1)
                        ->pluck('retailers_requests.supplier_organization');

                    $products = Products::leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                        ->whereIn('organizations.id', $suppliers)->where('products.category_id', $floor_category_id)->with('colors')->with('models')
                        ->select('products.*', 'organizations.company_name')->get();
                } else {
                    $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();
                    $products = Products::where('organization_id', $organization_id)->where('category_id', $floor_category_id)->with('colors')->with('models')->get();
                }
            } else {
                if ($user_role == 2) {
                    $suppliers = organizations::where('Type', '=', "Supplier")->whereHas('supplierRequests', function ($query) use ($organization_id) {
                        $query->where('retailer_organization', $organization_id)->where('status', 1)->where('active', 1);
                    })->orderBy('created_at', 'desc')->get();
                    $products = array();
                } else {
                    $blinds_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
                    $products = Products::where('organization_id', $organization_id)->where('category_id', $blinds_category_id)->with('colors')->with('models')->get();
                    $suppliers = array();
                }
            }

            if (\Route::currentRouteName() == 'view-new-quotation') {
                $invoice = new_quotations_data::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_quotations_data.quotation_id')->leftjoin('products', 'products.id', '=', 'new_quotations_data.product_id')->where('new_quotations.id', $id)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.deleted_at', NULL)->select('new_quotations.*', 'new_quotations.quotation_invoice_number as document_number', 'new_quotations_data.vat_id', 'new_quotations_data.ledger_id', 'new_quotations_data.cutting_size', 'new_quotations_data.cutting_variables', 'new_quotations_data.secondary_title', 'new_quotations_data.description as product_description', 'new_quotations_data.item_id', 'new_quotations_data.service_id', 'new_quotations.delivery_date as retailer_delivery_date', 'new_quotations.installation_date as retailer_installation_date', 'new_quotations.id as invoice_id', 'new_quotations_data.box_quantity', 'new_quotations_data.total_boxes_total', 'new_quotations_data.grand_totaal', 'new_quotations_data.grand_totaal_st', 'new_quotations_data.measure', 'new_quotations_data.max_width', 'new_quotations_data.order_number', 'new_quotations_data.discount', 'new_quotations_data.discount_option', 'new_quotations_data.labor_discount', 'new_quotations_data.total_discount', 'new_quotations_data.price_before_labor', 'new_quotations_data.labor_impact', 'new_quotations_data.model_factor_max_width', 'new_quotations_data.model_impact_value', 'new_quotations_data.childsafe', 'new_quotations_data.childsafe_question', 'new_quotations_data.childsafe_answer', 'new_quotations_data.childsafe_x', 'new_quotations_data.childsafe_y', 'new_quotations_data.childsafe_diff', 'new_quotations_data.model_id', 'new_quotations_data.delivery_days', 'new_quotations_data.delivery_date', 'new_quotations_data.id', 'new_quotations_data.supplier_id', 'new_quotations_data.product_id', 'new_quotations_data.row_id', 'new_quotations_data.rate', 'new_quotations_data.basic_price', 'new_quotations_data.qty', 'new_quotations_data.amount', 'new_quotations_data.color', 'new_quotations_data.width', 'new_quotations_data.width_unit', 'new_quotations_data.height', 'new_quotations_data.height_unit', 'new_quotations_data.price_based_option', 'new_quotations_data.base_price', 'new_quotations_data.supplier_margin', 'new_quotations_data.retailer_margin', 'products.ladderband', 'products.ladderband_value', 'products.ladderband_price_impact', 'products.ladderband_impact_type')
                    ->with(['features' => function ($query) {
                        $query->leftjoin('features', 'features.id', '=', 'new_quotations_features.feature_id')
                            /*->where('new_quotations_features.sub_feature',0)*/
                            ->select('new_quotations_features.*', 'features.title', 'features.comment_box');
                    }])
                    ->with(['sub_features' => function ($query) {
                        $query->leftjoin('product_features', 'product_features.id', '=', 'new_quotations_features.feature_id')
                            /*->where('new_quotations_features.sub_feature',1)*/
                            ->select('new_quotations_features.*', 'product_features.title');
                    }])->with('calculations')->get();
            } else {
                $invoice = array();

                if (\Route::currentRouteName() == 'create-new-negative-invoice' || \Route::currentRouteName() == 'view-negative-invoice') {
                    $invoice = new_invoices_data::leftjoin('new_invoices', 'new_invoices.id', '=', 'new_invoices_data.invoice_id')->leftjoin('products', 'products.id', '=', 'new_invoices_data.product_id')->where('new_invoices.negative_invoice', 1)->where('new_invoices.quotation_id', $id)->where('new_invoices.deleted_at', NULL)->whereIn('new_invoices.creator_id', $related_users)->select('new_invoices.*', 'new_invoices.invoice_number as document_number', 'new_invoices_data.vat_id', 'new_invoices_data.ledger_id', 'new_invoices_data.cutting_size', 'new_invoices_data.cutting_variables', 'new_invoices_data.secondary_title', 'new_invoices_data.description as product_description', 'new_invoices_data.item_id', 'new_invoices_data.service_id', 'new_invoices.delivery_date as retailer_delivery_date', 'new_invoices.installation_date as retailer_installation_date', 'new_invoices.id as invoice_id', 'new_invoices_data.box_quantity', 'new_invoices_data.total_boxes_total', 'new_invoices_data.grand_totaal', 'new_invoices_data.grand_totaal_st', 'new_invoices_data.measure', 'new_invoices_data.max_width', 'new_invoices_data.discount', 'new_invoices_data.discount_option', 'new_invoices_data.labor_discount', 'new_invoices_data.total_discount', 'new_invoices_data.price_before_labor', 'new_invoices_data.labor_impact', 'new_invoices_data.model_factor_max_width', 'new_invoices_data.model_impact_value', 'new_invoices_data.childsafe', 'new_invoices_data.childsafe_question', 'new_invoices_data.childsafe_answer', 'new_invoices_data.childsafe_x', 'new_invoices_data.childsafe_y', 'new_invoices_data.childsafe_diff', 'new_invoices_data.model_id', 'new_invoices_data.delivery_days', 'new_invoices_data.delivery_date', 'new_invoices_data.id', 'new_invoices_data.supplier_id', 'new_invoices_data.product_id', 'new_invoices_data.row_id', 'new_invoices_data.rate', 'new_invoices_data.basic_price', 'new_invoices_data.qty', 'new_invoices_data.amount', 'new_invoices_data.color', 'new_invoices_data.width', 'new_invoices_data.width_unit', 'new_invoices_data.height', 'new_invoices_data.height_unit', 'new_invoices_data.price_based_option', 'new_invoices_data.base_price', 'new_invoices_data.supplier_margin', 'new_invoices_data.retailer_margin', 'products.ladderband', 'products.ladderband_value', 'products.ladderband_price_impact', 'products.ladderband_impact_type')
                        ->with(['features' => function ($query) {
                            $query->leftjoin('features', 'features.id', '=', 'new_invoices_features.feature_id')
                                /*->where('new_quotations_features.sub_feature',0)*/
                                ->select('new_invoices_features.*', 'features.title', 'features.comment_box');
                        }])
                        ->with(['sub_features' => function ($query) {
                            $query->leftjoin('product_features', 'product_features.id', '=', 'new_invoices_features.feature_id')
                                /*->where('new_quotations_features.sub_feature',1)*/
                                ->select('new_invoices_features.*', 'product_features.title');
                        }])
                        ->with('calculations')->get();

                    $negative_payment_calculations = new_negative_invoices::where('quotation_id', $id)->whereIn('creator_id', $related_users)->first();
                    $negative_payment_calculations = $negative_payment_calculations ? $negative_payment_calculations->payment_calculations : array();
                }

                if (count($invoice) == 0) {
                    $invoice = new_invoices_data::leftjoin('new_invoices', 'new_invoices.id', '=', 'new_invoices_data.invoice_id')->leftjoin('products', 'products.id', '=', 'new_invoices_data.product_id')->where('new_invoices.negative_invoice', 0)->where('new_invoices.quotation_id', $id)->where('new_invoices.deleted_at', NULL)->whereIn('new_invoices.creator_id', $related_users)->select('new_invoices.*', 'new_invoices.invoice_number as document_number', 'new_invoices_data.vat_id', 'new_invoices_data.ledger_id', 'new_invoices_data.cutting_size', 'new_invoices_data.cutting_variables', 'new_invoices_data.secondary_title', 'new_invoices_data.description as product_description', 'new_invoices_data.item_id', 'new_invoices_data.service_id', 'new_invoices.delivery_date as retailer_delivery_date', 'new_invoices.installation_date as retailer_installation_date', 'new_invoices.id as invoice_id', 'new_invoices_data.box_quantity', 'new_invoices_data.total_boxes_total', 'new_invoices_data.grand_totaal', 'new_invoices_data.grand_totaal_st', 'new_invoices_data.measure', 'new_invoices_data.max_width', 'new_invoices_data.discount', 'new_invoices_data.discount_option', 'new_invoices_data.labor_discount', 'new_invoices_data.total_discount', 'new_invoices_data.price_before_labor', 'new_invoices_data.labor_impact', 'new_invoices_data.model_factor_max_width', 'new_invoices_data.model_impact_value', 'new_invoices_data.childsafe', 'new_invoices_data.childsafe_question', 'new_invoices_data.childsafe_answer', 'new_invoices_data.childsafe_x', 'new_invoices_data.childsafe_y', 'new_invoices_data.childsafe_diff', 'new_invoices_data.model_id', 'new_invoices_data.delivery_days', 'new_invoices_data.delivery_date', 'new_invoices_data.id', 'new_invoices_data.supplier_id', 'new_invoices_data.product_id', 'new_invoices_data.row_id', 'new_invoices_data.rate', 'new_invoices_data.basic_price', 'new_invoices_data.qty', 'new_invoices_data.amount', 'new_invoices_data.color', 'new_invoices_data.width', 'new_invoices_data.width_unit', 'new_invoices_data.height', 'new_invoices_data.height_unit', 'new_invoices_data.price_based_option', 'new_invoices_data.base_price', 'new_invoices_data.supplier_margin', 'new_invoices_data.retailer_margin', 'products.ladderband', 'products.ladderband_value', 'products.ladderband_price_impact', 'products.ladderband_impact_type')
                        ->with(['features' => function ($query) {
                            $query->leftjoin('features', 'features.id', '=', 'new_invoices_features.feature_id')
                                /*->where('new_quotations_features.sub_feature',0)*/
                                ->select('new_invoices_features.*', 'features.title', 'features.comment_box');
                        }])
                        ->with(['sub_features' => function ($query) {
                            $query->leftjoin('product_features', 'product_features.id', '=', 'new_invoices_features.feature_id')
                                /*->where('new_quotations_features.sub_feature',1)*/
                                ->select('new_invoices_features.*', 'product_features.title');
                        }])->with('calculations')->get();

                    if (\Route::currentRouteName() == 'create-new-negative-invoice') {
                        $invoice->create_negative_invoice = 1;
                    }
                }
            }

            if (!$invoice) {
                return redirect()->back();
            }

            $supplier_products = array();
            $product_titles = array();
            $item_titles = array();
            $service_titles = array();
            $color_titles = array();
            $model_titles = array();
            $product_suppliers = array();
            $sub_products = array();
            $colors = array();
            $models = array();
            $features = array();
            $sub_features = array();

            $f = 0;
            $s = 0;

            foreach ($invoice as $i => $item) {
                if ($check->form_type == 1) {
                    $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();
                    $product_titles[] = product::where('id', $item->product_id)->where('category_id', $floor_category_id)->pluck('title')->first();
                    $item_titles[] = items::leftjoin('categories', 'categories.id', '=', 'items.category_id')->where('items.id', $item->item_id)->select('items.cat_name', 'categories.cat_name as category')->first();
                    $service_titles[] = Service::where('id', $item->service_id)->pluck('title')->first();
                    $color_titles[] = colors::where('id', $item->color)->pluck('title')->first();
                    $model_titles[] = product_models::where('id', $item->model_id)->pluck('model')->first();
                    $product_suppliers[] = organizations::where('id', $item->supplier_id)->first();
                } else {
                    $blinds_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
                    $supplier_products[$i] = Products::where('organization_id', $item->supplier_id)->where('category_id', $blinds_category_id)->get();
                    $colors[$i] = colors::where('product_id', $item->product_id)->get();
                    $models[$i] = product_models::where('product_id', $item->product_id)->get();
                }

                foreach ($item->features as $feature) {
                    $features[$f] = product_features::leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')->where('product_features.product_id', $item->product_id)->where('product_features.heading_id', $feature->feature_id)->where('product_features.sub_feature', 0)->where('model_features.model_id', $item->model_id)->where('model_features.linked', 1)->select('product_features.*')->get();

                    if ($feature->ladderband) {
                        if (\Route::currentRouteName() == 'view-new-quotation') {
                            $sub_products[$i] = new_quotations_sub_products::leftjoin('product_ladderbands', 'product_ladderbands.id', '=', 'new_quotations_sub_products.sub_product_id')->where('new_quotations_sub_products.feature_row_id', $feature->id)->select('new_quotations_sub_products.*', 'product_ladderbands.title', 'product_ladderbands.code')->get();
                        } else {
                            $sub_products[$i] = new_invoices_sub_products::leftjoin('product_ladderbands', 'product_ladderbands.id', '=', 'new_invoices_sub_products.sub_product_id')->where('new_invoices_sub_products.feature_row_id', $feature->id)->select('new_invoices_sub_products.*', 'product_ladderbands.title', 'product_ladderbands.code')->get();
                        }
                    }

                    $f = $f + 1;
                }

                foreach ($item->sub_features as $sub_feature) {
                    $sub_features[$s] = product_features::where('product_id', $item->product_id)->where('main_id', $sub_feature->feature_id)->get();
                    $s = $s + 1;
                }
            }

            if ($user_role == 2) {
                $plannings = $this->Plannings(1);
                $event_titles = $plannings["event_titles"];
                $event_statuses = $plannings["event_statuses"];
                $quotation_ids = $plannings["quotation_ids"];
                $clients = $plannings["clients"];
                $planning_suppliers = $plannings["suppliers"];
                $employees = $plannings["employees"];
                $responsible_persons = $plannings["responsible_persons"];
                // $plannings = $plannings["plannings"];
                $general_ledgers = general_ledgers::whereIn('user_id', $related_users)->get();
            }

            $vats = vats::get();

            if ($check->form_type == 1) {
                $general_terms = retailer_general_terms::whereIn("retailer_id", $related_users)->where("type", 1)->first();

                if ($user_role == 2) {
                    $services = Service::leftjoin('retailer_services', 'retailer_services.service_id', '=', 'services.id')->whereIn('retailer_services.retailer_id', $related_users)->select('services.*', 'retailer_services.sell_rate as rate')->get();
                    $items = items::leftjoin('categories', 'categories.id', '=', 'items.category_id')->whereIn('items.user_id', $related_users)->select('items.*', 'categories.cat_name as category')->get();

                    return view('user.create_custom_quote1', compact('vats', 'general_ledgers', 'responsible_persons', 'negative_payment_calculations', 'check', 'general_terms', 'employees', 'planning_suppliers', 'clients', 'quotation_ids', 'event_titles', 'event_statuses', 'products', 'product_titles', 'item_titles', 'service_titles', 'color_titles', 'model_titles', 'product_suppliers', 'features', 'sub_features', 'customers', 'invoice', 'sub_products', 'services', 'items'));
                } else {
                    return view('user.create_custom_quote1', compact('vats', 'negative_payment_calculations', 'check', 'general_terms', 'products', 'product_titles', 'color_titles', 'model_titles', 'product_suppliers', 'features', 'sub_features', 'customers', 'invoice', 'sub_products'));
                }
            } else {
                return view('user.create_new_quotation1', compact('vats', 'responsible_persons', 'negative_payment_calculations', 'check', 'employees', 'suppliers', 'clients', 'quotation_ids', 'event_titles', 'event_statuses', 'planning_suppliers', 'products', 'supplier_products', 'suppliers', 'colors', 'models', 'features', 'sub_features', 'customers', 'invoice', 'sub_products'));
            }
        } else {
            return redirect()->back();
        }
    }

    public function EditOrder($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'edit-order') {
            if ($user_role == 2) {
                $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_orders.id', $id)->where('new_quotations.deleted_at', NULL)->whereIn('new_quotations.creator_id', $related_users)->select('new_quotations.*', 'new_orders.approved', 'new_orders.order_sent', 'new_orders.supplier_id', 'new_orders.quotation_id')->first();
            } else {
                $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_quotations.deleted_at', NULL)->where('new_orders.quotation_id', $id)->where('new_orders.supplier_id', $organization_id)->select('new_quotations.*', 'new_orders.approved', 'new_orders.order_sent', 'new_orders.supplier_id', 'new_orders.quotation_id')->first();
            }
        } else {
            if ($user_role == 2) {
                $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_orders.quotation_id', $id)->where('new_quotations.deleted_at', NULL)->whereIn('new_quotations.creator_id', $related_users)->select('new_quotations.*', 'new_orders.approved', 'new_orders.order_sent', 'new_orders.supplier_id', 'new_orders.quotation_id')->first();
            } else {
                $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_quotations.deleted_at', NULL)->where('new_orders.quotation_id', $id)->where('new_orders.supplier_id', $organization_id)->select('new_quotations.*', 'new_orders.approved', 'new_orders.order_sent', 'new_orders.supplier_id', 'new_orders.quotation_id')->first();
            }
        }

        if ($check) {
            $quotation_id = $check->quotation_id;
            $supplier_id = $check->supplier_id;

            if (\Route::currentRouteName() == 'edit-order') {
                if ($check->form_type == 1) {
                    $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();

                    $products = Products::leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                        ->where('organizations.id', $supplier_id)->where('products.category_id', $floor_category_id)->with('colors')->with('models')
                        ->select('products.*', 'organizations.company_name')->get();
                } else {
                    $blinds_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
                    $products = Products::where('organization_id', $supplier_id)->where('category_id', $blinds_category_id)->with('colors')->with('models')->get();
                }

                $suppliers = array();

                $invoice = new_orders::leftjoin('products', 'products.id', '=', 'new_orders.product_id')->where('new_orders.quotation_id', $quotation_id);

                if ($user_role == 4) {
                    $invoice = $invoice->where('new_orders.supplier_id', $supplier_id);
                }

                $invoice = $invoice->select('new_orders.*', 'products.ladderband', 'products.ladderband_value', 'products.ladderband_price_impact', 'products.ladderband_impact_type')
                    ->with(['features' => function ($query) {
                        $query->leftjoin('features', 'features.id', '=', 'new_orders_features.feature_id')
                            ->select('new_orders_features.*', 'features.title', 'features.comment_box');
                    }])
                    ->with(['sub_features' => function ($query) {
                        $query->leftjoin('product_features', 'product_features.id', '=', 'new_orders_features.feature_id')
                            ->select('new_orders_features.*', 'product_features.title');
                    }])->with('calculations')->get();
            } else {
                if ($user_role == 2) {
                    if ($check->form_type == 1) {
                        $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();

                        $products = Products::leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
                            ->where('organizations.id', $supplier_id)->where('products.category_id', $floor_category_id)->with('colors')->with('models')
                            ->select('products.*', 'organizations.company_name')->get();
                    } else {
                        $blinds_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
                        $products = Products::where('organization_id', $supplier_id)->where('category_id', $blinds_category_id)->with('colors')->with('models')->get();
                    }
                } else {
                    $products = array();
                }

                if ($check->form_type == 1) {
                    $suppliers = array();
                } else {
                    $suppliers = organizations::where('Type', '=', "Supplier")->whereHas('supplierRequests', function ($query) use ($organization_id) {
                        $query->where('retailer_organization', $organization_id)->where('status', 1)->where('active', 1);
                    })->orderBy('created_at', 'desc')->get();
                }

                $invoice = new_orders::leftjoin('products', 'products.id', '=', 'new_orders.product_id')->where('new_orders.quotation_id', $quotation_id);

                if ($user_role == 4) {
                    $invoice = $invoice->where('new_orders.supplier_id', $supplier_id);
                }

                $invoice = $invoice->select('new_orders.*', 'products.ladderband', 'products.ladderband_value', 'products.ladderband_price_impact', 'products.ladderband_impact_type')
                    ->with(['features' => function ($query) {
                        $query->leftjoin('features', 'features.id', '=', 'new_orders_features.feature_id')
                            ->select('new_orders_features.*', 'features.title', 'features.comment_box');
                    }])
                    ->with(['sub_features' => function ($query) {
                        $query->leftjoin('product_features', 'product_features.id', '=', 'new_orders_features.feature_id')
                            ->select('new_orders_features.*', 'product_features.title');
                    }])->with('calculations')->get();
            }

            if (!$invoice) {
                return redirect()->back();
            }

            $supplier_products = array();
            $product_suppliers = array();
            $sub_products = array();
            $colors = array();
            $models = array();
            $features = array();
            $sub_features = array();

            $f = 0;
            $s = 0;

            foreach ($invoice as $i => $item) {
                if ($check->form_type == 1) {
                    if (\Route::currentRouteName() == 'view-order') {
                        $floor_category_id = Category::where('cat_name', 'LIKE', '%Floors%')->orWhere('cat_name', 'LIKE', '%Vloeren%')->pluck('id')->first();
                        $supplier_products[] = Products::where('organization_id', $item->supplier_id)->where('category_id', $floor_category_id)->get();
                    }

                    $product_titles[] = product::where('id', $item->product_id)->pluck('title')->first();
                    $color_titles[] = colors::where('id', $item->color)->pluck('title')->first();
                    $model_titles[] = product_models::where('id', $item->model_id)->pluck('model')->first();
                    $product_suppliers[] = organizations::where('id', $item->supplier_id)->first();
                } else {
                    if (\Route::currentRouteName() == 'view-order') {
                        $blinds_category_id = Category::where('cat_name', 'LIKE', '%Blinds%')->orWhere('cat_name', 'LIKE', '%Binnen zonwering%')->pluck('id')->first();
                        $supplier_products[$i] = Products::where('organization_id', $item->supplier_id)->where('category_id', $blinds_category_id)->get();
                    }

                    $colors[$i] = colors::where('product_id', $item->product_id)->get();
                    $models[$i] = product_models::where('product_id', $item->product_id)->get();
                }

                foreach ($item->features as $feature) {
                    $features[$f] = product_features::leftjoin('model_features', 'model_features.product_feature_id', '=', 'product_features.id')->where('product_features.product_id', $item->product_id)->where('product_features.heading_id', $feature->feature_id)->where('product_features.sub_feature', 0)->where('model_features.model_id', $item->model_id)->where('model_features.linked', 1)->select('product_features.*')->get();

                    if ($feature->ladderband) {
                        $sub_products[$i] = new_orders_sub_products::leftjoin('product_ladderbands', 'product_ladderbands.id', '=', 'new_orders_sub_products.sub_product_id')->where('new_orders_sub_products.feature_row_id', $feature->id)->select('new_orders_sub_products.*', 'product_ladderbands.title', 'product_ladderbands.code')->get();
                    }

                    $f = $f + 1;
                }

                foreach ($item->sub_features as $sub_feature) {
                    $sub_features[$s] = product_features::where('product_id', $item->product_id)->where('main_id', $sub_feature->feature_id)->get();
                    $s = $s + 1;
                }
            }

            if ($check->form_type == 1) {
                return view('user.edit_order1', compact('product_titles', 'color_titles', 'model_titles', 'product_suppliers', 'check', 'suppliers', 'supplier_products', 'products', 'colors', 'models', 'features', 'sub_features', 'invoice', 'sub_products'));
            } else {
                return view('user.edit_order', compact('check', 'suppliers', 'supplier_products', 'products', 'colors', 'models', 'features', 'sub_features', 'invoice', 'sub_products'));
            }
        } else {
            return redirect()->back();
        }
    }

    public function PlanningResponsiblePersons()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        $organization_id = $user->organization->id;

        $responsible_persons = User::where('role_id', 2)->whereHas('organization', function ($query) use ($organization_id) {
            $query->where('organizations.id', $organization_id);
        })->get();

        return view('user.responsible_persons', compact('responsible_persons'));
    }

    public function EditPlanningResponsiblePerson($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        $organization_id = $user->organization->id;

        $person = User::where("id", $id)->whereHas('organization', function ($query) use ($organization_id) {
            $query->where('organizations.id', $organization_id);
        })->first();

        if (!$person) {
            Session::flash('unsuccess', __('text.Invalid Request'));
            return redirect()->back();
        }

        return view('user.responsible_person', compact('person'));
    }

    public function StorePlanningResponsiblePerson(Request $request)
    {
        User::where("id", $request->person_id)->update(["agenda_font_color" => $request->color]);

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->route('planning-responsible-persons');
    }

    public function GetPlannings(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $plannings = quotation_appointments::leftjoin('new_quotations', 'new_quotations.id', '=', 'quotation_appointments.quotation_id')
            ->leftjoin('customers_details as t1', 't1.id', '=', 'new_quotations.customer_details')
            ->leftjoin('customers_details as t2', 't2.id', '=', 'quotation_appointments.retailer_client_id')
            ->leftJoin('organizations', 'organizations.id', '=', 'quotation_appointments.supplier_id')
            ->leftjoin('users as t4', 't4.id', '=', 'quotation_appointments.employee_id')
            ->leftjoin('users as t5', 't5.id', '=', 'quotation_appointments.responsible_id')
            ->leftjoin('planning_statuses', 'planning_statuses.id', '=', 'quotation_appointments.status_id')
            ->where('new_quotations.deleted_at', NULL)->whereIn('quotation_appointments.user_id', $related_users);

        if ($request->filter_responsible) {
            $plannings = $plannings->where("quotation_appointments.responsible_id", $request->filter_responsible);
        }

        $plannings = $plannings->select('quotation_appointments.*', 'quotation_appointments.title as org_title', 't1.name as client_quotation_fname', 't1.family_name as client_quotation_lname', 't2.name as client_fname', 't2.family_name as client_lname', 'organizations.company_name', 't4.name as employee_fname', 't4.family_name as employee_lname', 't5.agenda_font_color as font_color', 'planning_statuses.title as status', 'planning_statuses.bg_color')->get();

        $plannings->transform(function ($i) {
            if ($i->bg_color != NULL) {
                $i->color = $i->bg_color;
            }
            return $i;
        });

        // $plannings = json_encode($plannings);

        return $plannings;
    }

    public function Plannings($data = 0)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $event_titles = planning_titles::whereIn('user_id', $related_users)->get();
        $event_statuses = planning_statuses::whereIn('user_id', $related_users)->get();

        // $plannings = $this->GetPlannings(new Request());
        // $plannings = json_encode($plannings);

        $quotation_ids = new_quotations::leftjoin('customers_details', 'customers_details.id', '=', 'new_quotations.customer_details')->whereIn('new_quotations.creator_id', $related_users)->select('new_quotations.id', 'new_quotations.quotation_invoice_number', 'customers_details.name', 'customers_details.family_name')->get();
        $clients = customers_details::leftjoin("users", "users.id", "=", "customers_details.user_id")->whereIn('customers_details.retailer_id', $related_users)->select("customers_details.*", "users.email", "users.fake_email")->get();

        $planning_suppliers = organizations::where('Type', '=', "Supplier")->whereHas('supplierRequests', function ($query) use ($organization_id) {
            $query->where('retailer_organization', $organization_id)->where('status', 1)->where('active', 1);
        })->orderBy('created_at', 'desc')->get();

        $employees = User::where('role_id', 2)->where('id', '!=', $user->id)->whereIn("id", $related_users)->get();

        $responsible_persons = User::where('role_id', 2)->whereIn("id", $related_users)->get();

        if ($data) {
            $data = [];
            $data["event_titles"] = $event_titles;
            $data["event_statuses"] = $event_statuses;
            // $data["plannings"] = $plannings;
            $data["quotation_ids"] = $quotation_ids;
            $data["clients"] = $clients;
            $data["suppliers"] = $planning_suppliers;
            $data["employees"] = $employees;
            $data["responsible_persons"] = $responsible_persons;

            return $data;
        } else {
            return view('user.plannings', compact('responsible_persons', 'employees', 'planning_suppliers', 'clients', 'event_titles', 'event_statuses', 'quotation_ids'));
        }
    }

    public function removePlannings(Request $request)
    {
        quotation_appointments::where('id', $request->id)->delete();
    }

    public function StorePlannings(Request $request, $qt_id = NULL)
    {
        if ($qt_id) {
            $delivery_date_start = NULL;
            $delivery_date_end = NULL;
            $installation_date_start = NULL;
            $installation_date_end = NULL;
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $last_id = '';
        $appointments_data = json_decode($request->appointment_data, true);

        if ($appointments_data) {
            // $ap_array = [];

            foreach ($appointments_data as $i => $key) {
                $end_date = date('s', strtotime($key['end'])) == '00' ? $key['end'] . ':01' : $key['end'];

                if ((isset($key['group_id']) && $key['group_id']) && !$key['main_id']) {
                    $non_continuous_end = date('s', strtotime($key['non_continuous_end'])) == '00' ? $key['non_continuous_end'] . ':01' : $key['non_continuous_end'];

                    if ($key['title'] == 'Delivery Date') {
                        if ($key['quotation_id'] == $qt_id) {
                            $delivery_date_start = $key['start'];
                            $delivery_date_end = $non_continuous_end;
                        }

                        new_quotations::where('id', $key['quotation_id'])->update(['delivery_date' => $key['non_continuous_start'], 'delivery_date_end' => $non_continuous_end]);
                    } elseif ($key['title'] == 'Installation Date') {
                        if ($key['quotation_id'] == $qt_id) {
                            $installation_date_start = $key['start'];
                            $installation_date_end = $non_continuous_end;
                        }

                        new_quotations::where('id', $key['quotation_id'])->update(['installation_date' => $key['non_continuous_start'], 'installation_date_end' => $non_continuous_end]);
                    }
                } else if (!(isset($key['group_id']) && $key['group_id'])) {
                    if ($key['title'] == 'Delivery Date') {
                        if ($key['quotation_id'] == $qt_id) {
                            $delivery_date_start = $key['start'];
                            $delivery_date_end = $end_date;
                        }

                        new_quotations::where('id', $key['quotation_id'])->update(['delivery_date' => $key['start'], 'delivery_date_end' => $end_date]);
                    } elseif ($key['title'] == 'Installation Date') {
                        if ($key['quotation_id'] == $qt_id) {
                            $installation_date_start = $key['start'];
                            $installation_date_end = $end_date;
                        }

                        new_quotations::where('id', $key['quotation_id'])->update(['installation_date' => $key['start'], 'installation_date_end' => $end_date]);
                    }
                }

                if (isset($key['id']) && $key['id']) {
                    $appointment = quotation_appointments::where('id', $key['id'])->first();
                    // $ap_array[] = $appointment->id;
                } else {
                    $appointment = new quotation_appointments;
                    $appointment->user_id = $user_id;
                    // $ap_array[] = $appointment->id;
                }

                if ($appointment) {
                    $appointment->responsible_id = $key['responsible_id'] ? $key['responsible_id'] : NULL;
                    $appointment->group_id = $key['group_id'];
                    $appointment->main_id = $key['main_id'];
                    $appointment->non_continuous_start = $key['non_continuous_start'];
                    $appointment->non_continuous_end = $key['non_continuous_end'];
                    $appointment->quotation_id = $key['quotation_id'] ? $key['quotation_id'] : NULL;
                    $appointment->title = $key['title'];
                    // $appointment->status = $key['status'];
                    $appointment->status_id = $key['status_id'] ? $key['status_id'] : NULL;
                    $appointment->start = $key['start'];
                    $appointment->end = $end_date;
                    $appointment->description = $key['description'] ? $key['description'] : NULL;
                    $appointment->tags = $key['tags'] ? $key['tags'] : NULL;
                    $appointment->retailer_client_id = $key['retailer_client_id'] ? $key['retailer_client_id'] : NULL;
                    $appointment->supplier_id = $key['supplier_id'] ? $key['supplier_id'] : NULL;
                    $appointment->employee_id = $key['employee_id'] ? $key['employee_id'] : NULL;
                    $appointment->event_type = $key['event_type'];
                    $appointment->save();

                    if ((count($appointments_data) - 1) == $i) {
                        $last_id = $appointment->id;
                    }
                }
            }

            // quotation_appointments::whereNotIn('id',$ap_array)->where('user_id',$user_id)->delete();
        }

        if ($qt_id) {
            $dates = [];
            $dates["delivery_date_start"] = $delivery_date_start;
            $dates["delivery_date_end"] = $delivery_date_end;
            $dates["installation_date_start"] = $installation_date_start;
            $dates["installation_date_end"] = $installation_date_end;

            return $dates;
        } else {
            if (!$request->runtime) {
                Session::flash('success', __('text.Task completed successfully.'));
                return redirect()->back();
            } else {
                return $last_id;
            }
        }
    }

    public function PlanningTitles()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $titles = planning_titles::whereIn('user_id', $related_users)->get();

        return view('user.planning_titles', compact('titles'));
    }

    public function AddPlanningTitle($id = NULL)
    {
        if ($id) {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            $role_id = $user->role_id;
            // $main_id = $user->main_id;

            // if($main_id)
            // {
            //     $user_id = $main_id;
            //     $employees = User::where("main_id",$main_id)->pluck("id");
            // }
            // else
            // {
            //     $employees = User::where("main_id",$user_id)->pluck("id");
            // }

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $title = planning_titles::where("id", $id)->whereIn('user_id', $related_users)->first();

            if (!$title) {
                Session::flash('unsuccess', __('text.Invalid Request'));
                return redirect()->back();
            }

            return view('user.create_planning_title', compact('title'));
        } else {
            return view('user.create_planning_title');
        }
    }

    public function StorePlanningTitle(Request $request)
    {
        if (str_contains(strtoupper($request->title), 'DELIVERY DATE') || str_contains(strtoupper($request->title), 'INSTALLATION DATE')) {
            Session::flash('unsuccess', 'This string is already reserved by admin.');
            return redirect()->route('planning-titles');
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        if ($request->title_id) {
            $post = planning_titles::where('id', $request->title_id)->first();
        } else {
            $post = new planning_titles;
            $post->user_id = $user_id;
        }

        $post->title = $request->title;
        $post->save();

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->route('planning-titles');
    }

    public function DeletePlanningTitle($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $title = planning_titles::where("id", $id)->whereIn('user_id', $related_users)->first();

        if (!$title) {
            return redirect()->back();
        }

        planning_titles::where('id', $id)->delete();

        Session::flash('success', __('text.Title deleted successfully.'));
        return redirect()->route('planning-titles');
    }

    public function PlanningStatuses()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $role_id = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $statuses = planning_statuses::whereIn('user_id', $related_users)->get();

        return view('user.planning_statuses', compact('statuses'));
    }

    public function AddPlanningStatus($id = NULL)
    {
        if ($id) {
            $user = Auth::guard('user')->user();
            $user_id = $user->id;
            $role_id = $user->role_id;
            // $main_id = $user->main_id;

            // if($main_id)
            // {
            //     $user_id = $main_id;
            //     $employees = User::where("main_id",$main_id)->pluck("id");
            // }
            // else
            // {
            //     $employees = User::where("main_id",$user_id)->pluck("id");
            // }

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $status = planning_statuses::where("id", $id)->whereIn('user_id', $related_users)->first();

            if (!$status) {
                Session::flash('unsuccess', __('text.Invalid Request'));
                return redirect()->back();
            }

            return view('user.create_planning_status', compact('status'));
        } else {
            return view('user.create_planning_status');
        }
    }

    public function StorePlanningStatus(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        if ($request->status_id) {
            $post = planning_statuses::where('id', $request->status_id)->first();
        } else {
            $post = new planning_statuses;
            $post->user_id = $user_id;
        }

        $post->title = $request->title;
        $post->bg_color = $request->bg_color;
        $post->save();

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->route('planning-statuses');
    }

    public function DeletePlanningStatus($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $status = planning_statuses::where("id", $id)->whereIn("user_id", $related_users)->first();

        if (!$status) {
            return redirect()->back();
        }

        planning_statuses::where('id', $id)->delete();

        Session::flash('success', __('text.Status deleted successfully.'));
        return redirect()->route('planning-statuses');
    }

    public function DownloadNewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $invoice = new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->first();

        if (!$invoice) {
            return redirect()->back();
        }

        $quotation_invoice_number = $invoice->quotation_invoice_number;
        $filename = $quotation_invoice_number . '.pdf';

        if (\Route::currentRouteName() == "show-quotation-pdf") {
            $url = asset("assets/newQuotations/{$organization_id}/{$filename}");
            return $url;
        } else {
            $file = public_path("assets/newQuotations/" . $organization_id . "/" . $filename);
            return response()->download($file);
        }
    }

    public function DownloadInvoicePDF($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        if ($user_role == 2) {
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }

            $retailer_organization_id = $user->organization->id;
            $organization = organizations::findOrFail($retailer_organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $invoice = new_invoices::where('id', $id)->whereIn('creator_id', $related_users)->first();
        } else {
            $invoice = new_invoices::where('id', $id)->where('user_id', $user_id)->first();
        }

        if (!$invoice) {
            return redirect()->back();
        }

        $creator_id = $invoice->creator_id;
        $organization_id = User::where("id", $creator_id)->first()->organization->id;
        $invoice_number = $invoice->invoice_number;
        $filename = $invoice_number . '.pdf';

        if (\Route::currentRouteName() == "show-invoice-pdf") {
            $url = asset("assets/newInvoices/{$organization_id}/{$filename}");
            return $url;
        } else {
            return response()->download(public_path("assets/newInvoices/{$organization_id}/{$filename}"));
        }
    }

    public function DownloadNegativeInvoicePDF($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        if ($user_role == 2) {
            // if($main_id)
            // {
            //     $user_id = $main_id;
            // }

            $retailer_organization_id = $user->organization->id;
            $retailer_organization = organizations::findOrFail($retailer_organization_id);
            $related_users = $retailer_organization->users()->withTrashed()->select('users.id')->pluck('id');

            if (\Route::currentRouteName() == "show-negative-invoice-pdf") {
                $invoice = new_negative_invoices::where('id', $id)->whereIn('creator_id', $related_users)->first();
            } else {
                $invoice = new_negative_invoices::where('quotation_id', $id)->where('creator_id', $related_users)->first();
            }
        } else {
            $invoice = new_negative_invoices::where('quotation_id', $id)->where('user_id', $user_id)->first();
        }

        if (!$invoice) {
            return redirect()->back();
        }

        $creator_id = $invoice->creator_id;
        $organization_id = User::where("id", $creator_id)->first()->organization->id;
        $invoice_number = $invoice->invoice_number;
        $filename = $invoice_number . '.pdf';

        if (\Route::currentRouteName() == "show-negative-invoice-pdf") {
            $url = asset("assets/newNegativeInvoices/{$organization_id}/{$filename}");
            return $url;
        } else {
            return response()->download(public_path("assets/newNegativeInvoices/{$organization_id}/{$filename}"));
        }
    }

    public function DownloadFullOrderPDF($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $check = new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->first();
        } else {
            $check = '';
        }

        if (!$check) {
            return redirect()->back();
        }

        $order_number = $check->quotation_invoice_number;
        $filename = $order_number . '.pdf';

        return response()->download(public_path("assets/Orders/{$organization_id}/{$filename}"));
    }

    public function DownloadOrderPDF($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_orders.id', $id)->where('new_quotations.deleted_at', NULL)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.finished', 1)->select("new_orders.*")->first();
            // $check = new_quotations_data::leftjoin('new_quotations','new_quotations.id','=','new_quotations_data.quotation_id')->where('new_quotations_data.id',$id)->where('new_quotations.deleted_at',NULL)->whereIn('new_quotations.creator_id',$related_users)->where('new_quotations.finished',1)->first();
        } else {
            $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_quotations.deleted_at', NULL)->where('new_orders.id', $id)->where('new_orders.supplier_id', $organization_id)->where('new_quotations.finished', 1)->select("new_orders.*")->first();
            // $check = new_quotations_data::leftjoin('new_quotations','new_quotations.id','=','new_quotations_data.quotation_id')->where('new_quotations.deleted_at',NULL)->where('new_quotations_data.id',$id)->where('new_quotations_data.supplier_id',$organization_id)->where('new_quotations.finished',1)->first();
        }

        if (!$check) {
            return redirect()->back();
        }

        $supplier_id = $check->supplier_id;
        $order_number = $check->order_number;
        $filename = $order_number . '.pdf';

        return response()->download(public_path("assets/supplierQuotations/{$supplier_id}/{$filename}"));
    }

    public function DownloadOrderConfirmationPDF($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($user_role == 2) {
            $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_orders.id', $id)->where('new_quotations.deleted_at', NULL)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations.finished', 1)->select("new_orders.*")->first();
            // $check = new_quotations_data::leftjoin('new_quotations','new_quotations.id','=','new_quotations_data.quotation_id')->where('new_quotations_data.id',$id)->where('new_quotations.deleted_at',NULL)->whereIn('new_quotations.creator_id',$related_users)->where('new_quotations.finished',1)->first();
        } else {
            $check = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_quotations.deleted_at', NULL)->where('new_orders.id', $id)->where('new_orders.supplier_id', $organization_id)->where('new_orders.approved', 1)->select("new_orders.*")->first();
            // $check = new_quotations_data::leftjoin('new_quotations','new_quotations.id','=','new_quotations_data.quotation_id')->where('new_quotations.deleted_at',NULL)->where('new_quotations_data.id',$id)->where('new_quotations_data.supplier_id',$organization_id)->where('new_quotations_data.approved',1)->first();
        }

        if (!$check) {
            return redirect()->back();
        }

        $supplier_id = $check->supplier_id;
        $order_number = $check->order_number;
        $filename = $order_number . '.pdf';

        return response()->download(public_path("assets/supplierApproved/{$supplier_id}/{$filename}"));
    }

    public function DownloadClientNewQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = new_quotations::where('id', $id)->where('user_id', $user_id)->first();

        if (!$invoice) {
            return redirect()->route('client-new-quotations');
        }

        $quotation_invoice_number = $invoice->quotation_invoice_number;

        $filename = $quotation_invoice_number . '.pdf';
        $retailer_id = $invoice->creator_id;
        $organization_id = User::where("id", $retailer_id)->first()->organization->id;

        return response()->download(public_path("assets/newQuotations/{$organization_id}/{$filename}"));
    }

    public function StoreNewOrder(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user->organization_email = $user->organization->email;

        $products = $request->products;
        $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $request->customer)->select('customers_details.*', 'users.email', 'users.fake_email')->first();

        if ($request->form_type == 2) {
            $order_number = new_orders::where('quotation_id', $request->quotation_id)->where('supplier_id', $request->supplier_id)->first();
            $order_number = $order_number->order_number;
            $order_ids = new_orders::where('quotation_id', $request->quotation_id)->where('supplier_id', $request->supplier_id)->pluck('id');
            new_orders::where('quotation_id', $request->quotation_id)->where('supplier_id', $request->supplier_id)->delete();
        } else {
            $order_ids = new_orders::where('quotation_id', $request->quotation_id)->pluck('id');
            new_orders::where('quotation_id', $request->quotation_id)->delete();
            $order_numbers = array();
        }

        $order_feature_ids = new_orders_features::whereIn('order_data_id', $order_ids)->pluck('id');
        new_orders_features::whereIn('order_data_id', $order_ids)->delete();
        new_orders_sub_products::whereIn('feature_row_id', $order_feature_ids)->delete();

        if ($request->category == 1) {
            new_orders_calculations::whereIn('order_id', $order_ids)->delete();
        }

        $product_titles = array();
        $color_titles = array();
        $model_titles = array();
        $suppliers = array();

        foreach ($products as $i => $key) {

            $sub_titles[$i] = '';
            $row_id = $request->row_id[$i];
            $product_titles[] = product::where('id', $key)->pluck('title')->first();
            $color_titles[] = colors::where('id', $request->colors[$i])->pluck('title')->first();
            $model_titles[] = product_models::where('id', $request->models[$i])->pluck('model')->first();

            // date_default_timezone_set('Europe/Amsterdam');
            // $delivery_date = date('Y-m-d', strtotime( $request->retailer_delivery_date . ' -1 day' ));
            // $is_weekend = date('N', strtotime($delivery_date)) >= 6;

            // while($is_weekend)
            // {
            //     $delivery_date = date('Y-m-d', strtotime($delivery_date. '- 1 day'));
            //     $is_weekend = date('N', strtotime($delivery_date)) >= 6;
            // }

            if ($request->form_type == 1) {
                $suppliers[] = organizations::where('id', $request->suppliers[$i])->first();

                if ($request->order_number[$i]) {
                    $order_number = $request->order_number[$i];
                } else {
                    $order_number = new_orders::where('quotation_id', $request->quotation_id)->where('supplier_id', $request->suppliers[$i])->pluck('order_number')->first();

                    if (!$order_number) {
                        $counter_order = $suppliers[$i]->counter_order;
                        $order_number = $suppliers[$i]->order_client_id ? date("Y") . "-" . sprintf('%04u', $suppliers[$i]->id) . '-' . sprintf('%06u', $counter_order) : date("Y") . "-" . sprintf('%06u', $counter_order);
                        $counter_order = $counter_order + 1;
                        organizations::where('id', $request->suppliers[$i])->update(['counter_order' => $counter_order]);
                    }
                }

                $order_numbers[$i] = $order_number;
            } else {
                $supplier_data = organizations::where('id', $request->supplier_id)->first();
            }

            $order = new new_orders;
            $order->order_number = $order_number;
            $order->quotation_id = $request->quotation_id;
            $order->supplier_id = $request->form_type == 1 ? $request->suppliers[$i] : $request->supplier_id;
            $order->product_id = (int)$key;
            $order->row_id = $row_id;
            $order->model_id = $request->models[$i];
            $order->model_impact_value = 0;
            $order->color = $request->colors[$i];
            $order->rate = 0;
            $order->basic_price = 0;
            $order->qty = $request->qty[$i] ? str_replace(',', '.', $request->qty[$i]) : 0;
            $order->amount = 0;
            // $order->delivery_days = $request->delivery_days[$i] ? $request->delivery_days[$i] : 1;
            // $order->delivery_date = $delivery_date;
            // $order->retailer_delivery_date = $delivery_date;
            $order->labor_impact = 0;
            $order->discount = 0;
            $order->labor_discount = 0;
            $order->total_discount = 0;
            $order->base_price = 0;
            $order->supplier_margin = 0;
            $order->retailer_margin = 0;

            if ($request->category == 2) {
                $order->width = str_replace(',', '.', $request->width[$i]);
                $order->width_unit = $request->width_unit[$i];
                $order->height = str_replace(',', '.', $request->height[$i]);
                $order->height_unit = $request->height_unit[$i];
                $order->box_quantity = NULL;
                $order->total_boxes_total = NULL;
                $order->grand_totaal = NULL;
                $order->grand_totaal_st = NULL;
                $order->measure = NULL;
                $order->max_width = NULL;
                $order->price_before_labor = 0;
            } else {
                $order->width = 0;
                $order->width_unit = "";
                $order->height = 0;
                $order->height_unit = "";
                $order->box_quantity = $request->estimated_price_quantity[$i];
                $order->total_boxes_total = $request->total_boxes_total[$i];
                $order->grand_totaal = $request->grand_totaal[$i];
                $order->grand_totaal_st = $request->grand_totaal_st[$i];
                $order->measure = $request->measure[$i];
                $order->max_width = $request->max_width[$i];
                $order->price_before_labor = str_replace(',', '.', str_replace('.', '', $request->price_before_labor[$i]));
            }

            if ($request->childsafe[$i]) {
                $order->childsafe = $request->childsafe[$i];

                $childsafe_question = 'childsafe_option' . $row_id;
                $order->childsafe_question = $request->$childsafe_question;

                $childsafe_diff = 'childsafe_diff' . $row_id;
                $order->childsafe_diff = $request->$childsafe_diff;

                $childsafe_answer = 'childsafe_answer' . $row_id;
                $order->childsafe_answer = $request->$childsafe_answer;

                $childsafe_x = 'childsafe_x' . $row_id;
                $order->childsafe_x = $request->$childsafe_x;

                $childsafe_y = 'childsafe_y' . $row_id;
                $order->childsafe_y = $request->$childsafe_y;
            }

            $order->save();

            if ($request->category == 1) {
                $calculator_row = 'calculator_row' . $row_id;
                $calculator_row = $request->$calculator_row;

                foreach ($calculator_row as $c => $cal) {
                    $description = 'attribute_description' . $row_id;
                    $width = 'width' . $row_id;
                    $height = 'height' . $row_id;
                    $cutting_lose = 'cutting_lose_percentage' . $row_id;
                    $box_quantity_supplier = 'box_quantity_supplier' . $row_id;
                    $box_quantity = 'box_quantity' . $row_id;
                    $total_boxes = 'total_boxes' . $row_id;
                    $total_inc_cuttinglose = 'total_inc_cuttinglose' . $row_id;
                    $max_width = 'max_width' . $row_id;
                    $turn = 'turn' . $row_id;

                    if (is_numeric($cal) && floor($cal) != $cal) {
                        $parent_row = floor($cal);
                    } else {
                        $parent_row = NULL;
                    }

                    $order_calculations = new new_orders_calculations;
                    $order_calculations->order_id = $order->id;
                    $order_calculations->calculator_row = $cal;
                    $order_calculations->parent_row = $parent_row;
                    $order_calculations->description = $request->$description[$c];
                    $order_calculations->width = $request->$width[$c] ? str_replace(',', '.', $request->$width[$c]) : NULL;
                    $order_calculations->height = $request->$height[$c] ? str_replace(',', '.', $request->$height[$c]) : NULL;
                    $order_calculations->cutting_lose = $request->$cutting_lose[$c];
                    $order_calculations->box_quantity_supplier = $request->$box_quantity_supplier[$c] ? str_replace(',', '.', $request->$box_quantity_supplier[$c]) : NULL;
                    $order_calculations->box_quantity = $request->$box_quantity[$c] ? str_replace(',', '.', $request->$box_quantity[$c]) : NULL;
                    $order_calculations->total_boxes = $request->$total_boxes[$c] ? str_replace(',', '.', $request->$total_boxes[$c]) : NULL;
                    $order_calculations->total_inc_cutting = $request->$total_inc_cuttinglose[$c] ? str_replace(',', '.', $request->$total_inc_cuttinglose[$c]) : NULL;
                    $order_calculations->max_width = $request->$max_width[$c];
                    $order_calculations->turn = $request->$turn[$c];
                    $order_calculations->save();
                }
            }

            $feature_row = 'features' . $row_id;
            $features = $request->$feature_row;

            if ($features) {
                foreach ($features as $f => $key1) {
                    $f_row = 'f_id' . $row_id;
                    $f_ids = $request->$f_row;

                    $f_row1 = 'f_price' . $row_id;
                    $f_prices = $request->$f_row1;

                    $is_sub = 'sub_feature' . $row_id;
                    $is_sub_feature = $request->$is_sub;

                    $comment = 'comment-' . $row_id . '-' . $f_ids[$f];
                    $comment = $request->$comment;

                    if ($f_ids[$f] == 0) {
                        $post_order_features = new new_orders_features;
                        $post_order_features->order_data_id = $order->id;
                        $post_order_features->price = $f_prices[$f];
                        $post_order_features->feature_id = $f_ids[$f];
                        $post_order_features->feature_sub_id = 0;
                        $post_order_features->ladderband = $key1;
                        $post_order_features->save();

                        if ($key1) {
                            $size1 = 'sizeA' . $row_id[$f];
                            $size1_value = $request->$size1;

                            $size2 = 'sizeB' . $row_id[$f];
                            $size2_value = $request->$size2;

                            $sub = 'sub_product_id' . $row_id[$f];
                            $sub_value = $request->$sub;

                            foreach ($sub_value as $s => $key2) {
                                $post_orders_sub_products = new new_orders_sub_products;
                                $post_orders_sub_products->feature_row_id = $post_order_features->id;
                                $post_orders_sub_products->sub_product_id = $key2;
                                $post_orders_sub_products->size1_value = $size1_value[$s];
                                $post_orders_sub_products->size2_value = $size2_value[$s];
                                $post_orders_sub_products->save();

                                if ($size1_value[$s] == 1 || $size2_value[$s] == 1) {
                                    $sub_titles[$i] = product_ladderbands::where('product_id', $key)->where('id', $key2)->first();

                                    if ($size1_value[$s] == 1) {
                                        $sub_titles[$i]->size = '38mm';
                                    } else {
                                        $sub_titles[$i]->size = '25mm';
                                    }
                                }
                            }
                        }
                    } else {
                        $post_order_features = new new_orders_features;
                        $post_order_features->order_data_id = $order->id;
                        $post_order_features->price = $f_prices[$f];
                        $post_order_features->feature_id = $f_ids[$f];
                        $post_order_features->feature_sub_id = $key1;
                        $post_order_features->sub_feature = $is_sub_feature[$f];
                        $post_order_features->comment = $comment;
                        $post_order_features->save();
                    }

                    $feature_sub_titles[$i][] = product_features::leftjoin('features', 'features.id', '=', 'product_features.heading_id')->where('product_features.product_id', $key)->where('product_features.id', $key1)->select('product_features.*', 'features.title as main_title', 'features.order_no', 'features.id as f_id')->first();
                }
            } else {
                $feature_sub_titles[$i] = array();
            }
        }

        if ($request->form_type == 1) {
            $quotation_invoice_number = $request->quotation_invoice_number;
            $filename = $quotation_invoice_number . '.pdf';
            ini_set('max_execution_time', 180);

            $role = 'supplier2';
            $date = $request->created_at;

            if ($request->category == 1) {
                $form_type = 1;
                $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('suppliers', 'order_numbers', 'form_type', 'role', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            } else {
                $form_type = 2;
                $pdf = PDF::loadView('user.pdf_new_quotation', compact('form_type', 'suppliers', 'order_numbers', 'role', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'landscape')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            }

            $retailer_orders_folder_path = public_path() . '/assets/Orders/' . $organization_id;

            if (!file_exists($retailer_orders_folder_path)) {
                mkdir($retailer_orders_folder_path, 0775, true);
            }

            $file = $retailer_orders_folder_path . '/' . $filename;
            $pdf->save($file);

            Session::flash('success', __('text.Order has been updated successfully!'));
            return redirect()->route('customer-quotations');
        } else {

            $quotation_id = $request->quotation_id;
            $request->products = new_orders::where('quotation_id', $quotation_id)->where('supplier_id', $request->supplier_id)->get();
            $product_titles = array();
            $color_titles = array();
            $model_titles = array();
            $sub_titles = array();
            $qty = array();
            $width = array();
            $width_unit = array();
            $height = array();
            $height_unit = array();
            $comments = array();
            $delivery = array();
            $labor_impact = array();
            $price_before_labor = array();
            $discount = array();
            $rate = array();
            $labor_discount = array();
            $total = array();
            $total_discount = array();
            $feature_sub_titles = array();
            $deliver_to = array();

            foreach ($request->products as $x => $temp) {
                $feature_sub_titles[$x][] = array();
                $product_titles[] = product::where('id', $temp->product_id)->pluck('title')->first();
                $color_titles[] = colors::where('id', $temp->color)->pluck('title')->first();
                $model_titles[] = product_models::where('id', $temp->model_id)->pluck('model')->first();
                $qty[] = $temp->qty;
                $width[] = $temp->width;
                $width_unit[] = $temp->width_unit;
                $height[] = $temp->height;
                $height_unit[] = $temp->height_unit;
                $delivery[] = $temp->delivery_date;
                $labor_impact[] = $temp->labor_impact;
                $price_before_labor[] = $temp->price_before_labor;
                $discount[] = $temp->discount;
                $rate[] = $temp->rate;
                $labor_discount[] = $temp->labor_discount;
                $total[] = $temp->amount;
                $total_discount[] = $temp->total_discount;
                $deliver_to[] = $temp->deliver_to;

                $features = new_orders_features::where('order_data_id', $temp->id)->get();

                foreach ($features as $f => $feature) {
                    if ($feature->feature_id == 0) {
                        if ($feature->ladderband) {
                            $sub_product = new_orders_sub_products::where('feature_row_id', $feature->id)->get();

                            foreach ($sub_product as $sub) {
                                if ($sub->size1_value == 1 || $sub->size2_value == 1) {
                                    $sub_titles[$x] = product_ladderbands::where('product_id', $temp->product_id)->where('id', $sub->sub_product_id)->first();

                                    if ($sub->size1_value == 1) {
                                        $sub_titles[$x]->size = '38mm';
                                    } else {
                                        $sub_titles[$x]->size = '25mm';
                                    }
                                }
                            }
                        }
                    }

                    $feature_sub_titles[$x][] = product_features::leftjoin('features', 'features.id', '=', 'product_features.heading_id')->where('product_features.product_id', $temp->product_id)->where('product_features.id', $feature->feature_sub_id)->select('product_features.*', 'features.title as main_title', 'features.order_no', 'features.id as f_id')->first();
                    $comments[$x][] = $feature->comment;
                }
            }

            $request->qty = $qty;
            $request->width = $width;
            $request->width_unit = $width_unit;
            $request->height = $height;
            $request->height_unit = $height_unit;
            $request->delivery_date = $delivery;
            $request->labor_impact = $labor_impact;
            $request->price_before_labor = $price_before_labor;
            $request->discount = $discount;
            $request->rate = $rate;
            $request->labor_discount = $labor_discount;
            $request->total = $total;
            $request->total_discount = $total_discount;
            $request->deliver_to = $deliver_to;

            $supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $request->supplier_id;

            if (!file_exists($supplier_orders_folder_path)) {
                mkdir($supplier_orders_folder_path, 0775, true);
            }

            $quotation_invoice_number = $request->quotation_invoice_number;
            $filename = $order_number . '.pdf';
            $file = $supplier_orders_folder_path . '/' . $filename;

            ini_set('max_execution_time', 180);

            $date = $request->created_at;

            if ($request->category == 1) {
                $role = 'supplier3';
                $form_type = 1;
                $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('supplier_data', 'form_type', 'role', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number', 'order_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            } else {
                $role = 'supplier1';
                $form_type = 2;
                $pdf = PDF::loadView('user.pdf_new_quotation', compact('supplier_data', 'form_type', 'role', 'comments', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number', 'order_number'))->setPaper('letter', 'landscape')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            }

            $pdf->save($file);

            Session::flash('success', __('text.Order has been updated successfully!'));

            if ($user_role == 2) {
                return redirect()->route('new-orders');
            } else {
                return redirect()->route('customer-quotations');
            }
        }
    }

    function is_NaN($value)
    {
        return is_string($value) && strtolower($value) === 'nan';
    }

    public function StoreNewQuotation(Request $request)
    {
        $ajax_request = 0;
        $token = $request->draft_token;
        $response = array();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $ajax_request = 1;

            if (!$request->is_invoice) {
                $draft_data = new_quotations::where('draft_token', $token)->first();
            } else {
                if (!$request->negative_invoice) {
                    $draft_data = new_invoices::where('draft_token', $token)->first();
                } else {
                    $draft_data = new_negative_invoices::where('draft_token', $token)->first();
                }
            }

            $request->quotation_id = $draft_data ? $draft_data->id : $request->quotation_id;
        }

        $user_id = $request->user_id;
        // $user = Auth::guard('user')->user();
        $user = User::where("id", $user_id)->first();
        // $main_id = $user->main_id;
        $form_type = $request->form_type;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user->organization_email = $user->organization->email;

        $user_name = $user->name;
        $counter = $user->counter;

        $company_email = $organization->email;
        $company_name = $organization->company_name;
        $products = $request->products;
        $document_date = date('Y-m-d', strtotime($request->document_date));
        $expire_date = $request->expire_date ? date('Y-m-d', strtotime($request->expire_date)) : '';

        $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $request->customer)->select('customers_details.*', 'users.email', 'users.fake_email', 'users.temp_password')->first();

        if ($request->quotation_id) {
            if ($request->is_invoice) {
                if (!$request->negative_invoice) {
                    $quotation_id = new_invoices::where('id', $request->quotation_id)->pluck('quotation_id')->first();
                } else {
                    if (!$request->negative_invoice_id) {
                        $quotation_id = new_invoices::where('id', $request->quotation_id)->pluck('quotation_id')->first();
                    } else {
                        $quotation_id = new_negative_invoices::where('id', $request->negative_invoice_id)->pluck('quotation_id')->first();
                    }
                }
            } else {
                $quotation_id = $request->quotation_id;
            }

            $appointments_dates = $this->StorePlannings($request, $quotation_id);
            $delivery_date_start = $appointments_dates["delivery_date_start"];
            $delivery_date_end = $appointments_dates["delivery_date_end"];
            $installation_date_start = $appointments_dates["installation_date_start"];
            $installation_date_end = $appointments_dates["installation_date_end"];

            $document_number = date("Y") . '-' . $request->document_number;

            if ($request->is_invoice) {
                $flag_dc_update = 1;

                if ($form_type == 2) {
                    if (!$request->negative_invoice) {
                        new_invoices::where('id', $request->quotation_id)->update(['document_date' => $document_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => $this->is_NaN($request->labor_cost_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->labor_cost_total)), 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->customer, 'user_id' => $client->user_id, 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                    } else {
                        if ($request->negative_invoice_id) {
                            new_negative_invoices::where('id', $request->negative_invoice_id)->update(['document_date' => $document_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => $this->is_NaN($request->labor_cost_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->labor_cost_total)), 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->customer, 'user_id' => $client->user_id, 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                        }
                    }
                } else {
                    if (!$request->negative_invoice) {
                        new_invoices::where('id', $request->quotation_id)->update(['document_date' => $document_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => 0, 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->quote_request_id ? 0 : $request->customer, 'user_id' => $request->quote_request_id ? 0 : $client->user_id, 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                    } else {
                        if ($request->negative_invoice_id) {
                            new_negative_invoices::where('id', $request->negative_invoice_id)->update(['document_date' => $document_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => 0, 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->quote_request_id ? 0 : $request->customer, 'user_id' => $request->quote_request_id ? 0 : $client->user_id, 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                        }
                    }
                }

                if (!$request->negative_invoice) {
                    // $data_ids = new_invoices_data::where('invoice_id',$request->quotation_id)->pluck('id');
                    // $feature_ids = new_invoices_features::whereIn('invoice_data_id',$data_ids)->pluck('id');

                    // new_invoices_data::where('invoice_id',$request->quotation_id)->delete();

                    // if($form_type == 1)
                    // {
                    //     new_invoices_data_calculations::whereIn('invoice_data_id',$data_ids)->delete();
                    // }

                    // new_invoices_features::whereIn('invoice_data_id',$data_ids)->delete();
                    // new_invoices_sub_products::whereIn('feature_row_id',$feature_ids)->delete();
                    // invoice_payment_calculations::where('invoice_id',$request->quotation_id)->delete();

                    $invoice = new_invoices::where('id', $request->quotation_id)->first();
                    $invoice_number = $invoice->invoice_number;
                } else {
                    if (!$request->negative_invoice_id) {
                        $flag_dc_update = 0;
                        new_invoices::where('id', $request->quotation_id)->update(['has_negative_invoice' => 1]);

                        $counter_negative_invoice = $user->counter_invoice;
                        $invoice_number = date("Y") . '-' . sprintf('%06u', $counter_negative_invoice);
                        $check_i_number = all_invoices::where('invoice_number', $invoice_number)->where('creator_id', $user_id)->first();

                        while ($check_i_number) {
                            $counter_negative_invoice = $counter_negative_invoice + 1;
                            $invoice_number = date("Y") . '-' . sprintf('%06u', $counter_negative_invoice);
                            $check_i_number = all_invoices::where('invoice_number', $invoice_number)->where('creator_id', $user_id)->first();
                        }

                        // $client_id = $client && $client->customer_number ? "-" . sprintf('%04u', $client->customer_number) : "";
                        // $invoice_number = $user->invoice_client_id ? date("Y") . $client_id . '-' . sprintf('%06u', $counter_negative_invoice) : date("Y") . '-' . sprintf('%06u', $counter_negative_invoice);

                        $org_invoice_data = new_invoices::where('id', $request->quotation_id)->first();
                        $org_invoice_data->invoice_number = $invoice_number;
                        $org_invoice_data->negative_invoice = 1;
                        $org_invoice_data->vat_percentage = 21;
                        $org_invoice_data->subtotal = $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount));
                        $org_invoice_data->grand_total = $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount));
                        $org_invoice_data->price_before_labor_total = $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total));
                        $org_invoice_data->description = $request->description;

                        if ($form_type == 2) {
                            $org_invoice_data->user_id = $client->user_id;
                            $org_invoice_data->customer_details = $request->customer;
                            $org_invoice_data->labor_cost_total = $this->is_NaN($request->labor_cost_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->labor_cost_total));
                        } else {
                            $org_invoice_data->user_id = $request->quote_request_id ? 0 : $client->user_id;
                            $org_invoice_data->customer_details = $request->quote_request_id ? 0 : $request->customer;
                            $org_invoice_data->labor_cost_total = 0;
                        }

                        $org_invoice_data->document_date = $document_date;
                        $org_invoice_data->general_terms = $request->general_terms;
                        $org_invoice_data->net_amount = $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount));
                        $org_invoice_data->tax_amount = $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount));
                        $org_invoice_data->delivery_date = $delivery_date_start;
                        $org_invoice_data->delivery_date_end = $delivery_date_end;
                        $org_invoice_data->installation_date = $installation_date_start;
                        $org_invoice_data->installation_date_end = $installation_date_end;
                        $org_invoice_data->invoice_date = date("Y-m-d");
                        $org_invoice_data->draft_token = $token;
                        $org_invoice_data->taxes_json = $request->taxes_json;
                        $org_invoice_data->payment_total_percentage = str_replace(',', '.', str_replace('.', '', $request->pc_percentages_total));
                        $org_invoice_data->payment_total_amount = str_replace(',', '.', str_replace('.', '', $request->pc_amounts_total));
                        $invoice = $org_invoice_data->replicate($except = ['copied_from', 'reeleezee_guid', 'reeleezee_exported_at']);
                        $invoice->setTable('new_invoices');
                        $invoice->save();

                        $user->organization->update(['counter_invoice' => $counter_negative_invoice + 1]);
                    } else {
                        $invoice = new_negative_invoices::where('id', $request->negative_invoice_id)->first();
                        $invoice_number = $invoice->invoice_number;
                        // invoice_payment_calculations::where('invoice_id',$request->negative_invoice_id)->delete();
                    }

                    // $data_ids = new_invoices_data::where('invoice_id',$request->negative_invoice_id)->pluck('id');
                    // $feature_ids = new_invoices_features::whereIn('invoice_data_id',$data_ids)->pluck('id');

                    // new_invoices_data::where('invoice_id',$request->negative_invoice_id)->delete();

                    // if($form_type == 1)
                    // {
                    //     new_invoices_data_calculations::whereIn('invoice_data_id',$data_ids)->delete();
                    // }

                    // new_invoices_features::whereIn('invoice_data_id',$data_ids)->delete();
                    // new_invoices_sub_products::whereIn('feature_row_id',$feature_ids)->delete();
                }

                if ($flag_dc_update) {
                    // $user = User::where('id',$user_id)->first();
                    $user->organization->update(["counter_invoice" => ltrim($request->document_number, '0') + 1]);
                }

                $response[0] = $invoice_number;
                $response[1] = $request->document_date;
            } else {
                $ask = new_quotations::where('id', $request->quotation_id)->pluck('ask_customization')->first();

                if ($form_type == 2) {
                    new_quotations::where('id', $request->quotation_id)->update(['expire_date' => $expire_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'draft' => 1, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => $this->is_NaN($request->labor_cost_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->labor_cost_total)), 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->customer ? $request->customer : 0, 'user_id' => $client ? $client->user_id : 0, 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                } else {
                    new_quotations::where('id', $request->quotation_id)->update(['expire_date' => $expire_date, 'taxes_json' => $request->taxes_json, 'regards' => $request->regards, 'draft' => 1, 'general_terms' => $request->general_terms, 'description' => $request->description, 'delivery_date' => $delivery_date_start, 'delivery_date_end' => $delivery_date_end, 'installation_date' => $installation_date_start, 'installation_date_end' => $installation_date_end, 'price_before_labor_total' => $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total)), 'labor_cost_total' => 0, 'net_amount' => $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount)), 'tax_amount' => $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount)), 'customer_details' => $request->quote_request_id ? 0 : ($request->customer ? $request->customer : 0), 'user_id' => $request->quote_request_id ? 0 : ($client ? $client->user_id : 0), 'ask_customization' => 0, 'subtotal' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'grand_total' => $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount)), 'mail_to' => $request->mail_to, 'payment_total_percentage' => $request->pc_percentages_total, 'payment_total_amount' => $request->pc_amounts_total]);
                }

                $invoice = new_quotations::where('id', $request->quotation_id)->first();
                $quotation_invoice_number = $invoice->quotation_invoice_number;

                // $data_ids = new_quotations_data::where('quotation_id',$request->quotation_id)->pluck('id');
                // $feature_ids = new_quotations_features::whereIn('quotation_data_id',$data_ids)->pluck('id');

                // $order_ids = new_orders::where('quotation_id',$request->quotation_id)->pluck('id');
                // $order_feature_ids = new_orders_features::whereIn('order_data_id',$order_ids)->pluck('id');

                // new_quotations_data::where('quotation_id',$request->quotation_id)->delete();

                // if($form_type == 1)
                // {
                //     new_quotations_data_calculations::whereIn('quotation_data_id',$data_ids)->delete();

                //     if(!$invoice->finished)
                //     {
                //         new_orders_calculations::whereIn('order_id',$order_ids)->delete();
                //     }
                // }

                // payment_calculations::where('quotation_id',$request->quotation_id)->delete();
                // new_quotations_features::whereIn('quotation_data_id',$data_ids)->delete();
                // new_quotations_sub_products::whereIn('feature_row_id',$feature_ids)->delete();

                // if(!$invoice->finished)
                // {
                //     new_orders::where('quotation_id',$request->quotation_id)->delete();
                //     new_orders_features::whereIn('order_data_id',$order_ids)->delete();
                //     new_orders_sub_products::whereIn('feature_row_id',$order_feature_ids)->delete();
                // }

                //  We dont want to update the counter, when we edit a document.
                //  User::where("id",$user_id)->update(["counter" => ltrim($request->document_number, '0') + 1]);

                $response[0] = $quotation_invoice_number;
                $response[1] = $request->document_date;
            }
        } else {
            $quotation_invoice_number = date("Y") . '-' . sprintf('%06u', $counter);
            $check_q_number = new_quotations::where('quotation_invoice_number', $quotation_invoice_number)->where('creator_id', $user_id)->first();

            while ($check_q_number) {
                $counter = $counter + 1;
                $quotation_invoice_number = date("Y") . '-' . sprintf('%06u', $counter);
                $check_q_number = new_quotations::where('quotation_invoice_number', $quotation_invoice_number)->where('creator_id', $user_id)->first();
            }

            // $client_id = $client && $client->customer_number ? "-" . sprintf('%04u', $client->customer_number) : "";
            // $quotation_invoice_number = $user->quotation_client_id ? date("Y") . $client_id . '-' . sprintf('%06u', $counter) : date("Y") . '-' . sprintf('%06u', $counter);

            $invoice = new new_quotations();
            $invoice->draft_token = $token;
            $invoice->regards = $request->regards;
            $invoice->draft = 1;
            $invoice->general_terms = $request->general_terms;
            $invoice->quote_request_id = $request->quote_request_id;
            $invoice->form_type = $form_type;
            $invoice->quotation_invoice_number = $quotation_invoice_number;
            $invoice->creator_id = $user_id;
            $invoice->vat_percentage = 21;
            $invoice->subtotal = $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount));
            $invoice->grand_total = $this->is_NaN($request->total_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->total_amount));
            $invoice->price_before_labor_total = $this->is_NaN($request->price_before_labor_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor_total));
            $invoice->description = $request->description;
            $invoice->taxes_json = $request->taxes_json;

            if ($form_type == 2) {
                $invoice->user_id = $client ? $client->user_id : 0;
                $invoice->customer_details = $request->customer ? $request->customer : 0;
                $invoice->labor_cost_total = $this->is_NaN($request->labor_cost_total) ? 0 : str_replace(',', '.', str_replace('.', '', $request->labor_cost_total));
            } else {
                $invoice->user_id = $request->quote_request_id ? 0 : ($client ? $client->user_id : 0);
                $invoice->customer_details = $request->quote_request_id ? 0 : ($request->customer ? $request->customer : 0);
                $invoice->labor_cost_total = 0;
            }

            $invoice->document_date = $document_date;
            $invoice->expire_date = $expire_date;
            $invoice->net_amount = $this->is_NaN($request->net_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->net_amount));
            $invoice->tax_amount = $this->is_NaN($request->tax_amount) ? 0 : str_replace(',', '.', str_replace('.', '', $request->tax_amount));
            $invoice->payment_total_percentage = $request->pc_percentages_total;
            $invoice->payment_total_amount = $request->pc_amounts_total;
            $invoice->save();

            $counter = $counter + 1;
            // $user = User::where('id',$user_id)->first();
            $user->organization->update(['counter' => $counter]);

            $appointments_dates = $this->StorePlannings($request, $invoice->id);
            $delivery_date_start = $appointments_dates["delivery_date_start"];
            $delivery_date_end = $appointments_dates["delivery_date_end"];
            $installation_date_start = $appointments_dates["installation_date_start"];
            $installation_date_end = $appointments_dates["installation_date_end"];

            if ($form_type == 1 && $request->quote_request_id) {
                $quote = quotes::where('id', $request->quote_request_id)->update(['status' => 1]);
            }

            $response[0] = $quotation_invoice_number;
            $response[1] = $request->document_date;
        }

        $payment_calculations_ids = array();
        $payment_calculations = "";

        foreach ($request->pc_percentage as $pc => $key) {
            if ($request->quotation_id) {
                if (!$request->is_invoice) {
                    $payment_calculations = payment_calculations::where('quotation_id', $request->quotation_id)->skip($pc)->first();
                } else {
                    if (!$request->negative_invoice) {
                        $payment_calculations = invoice_payment_calculations::where('invoice_id', $request->quotation_id)->skip($pc)->first();
                    } else if ($request->negative_invoice_id) {
                        $payment_calculations = invoice_payment_calculations::where('invoice_id', $request->negative_invoice_id)->skip($pc)->first();
                    }
                }
            }

            if (!$payment_calculations) {
                $payment_calculations = !$request->is_invoice ? new payment_calculations : new invoice_payment_calculations;
            }

            !$request->is_invoice ? $payment_calculations->quotation_id = $invoice->id : $payment_calculations->invoice_id = $invoice->id;
            $payment_calculations->percentage = $key ? ($key == "-" || $key == "-," || $key == "," ? 0 : str_replace(',', '.', $key)) : 0;
            $payment_calculations->amount = $request->pc_amount[$pc] ? ($request->pc_amount[$pc] == "-" || $request->pc_amount[$pc] == "-," || $request->pc_amount[$pc] == "," ? 0 : str_replace(',', '.', str_replace('.', '', $request->pc_amount[$pc]))) : 0;
            $payment_calculations->date = date("Y-m-d", strtotime($request->pc_date[$pc]));
            $payment_calculations->paid_by = $request->pc_paid_by[$pc];
            $payment_calculations->description = $request->pc_description[$pc];
            $payment_calculations->save();

            $payment_calculations_ids[] = $payment_calculations->id;
        }

        $order_numbers = array();
        $feature_sub_titles = array();

        date_default_timezone_set('Europe/Amsterdam');
        $delivery_date = date('Y-m-d', strtotime($delivery_date_start . ' +1 day'));
        $is_weekend = date('N', strtotime($delivery_date)) >= 6;

        while ($is_weekend) {
            $delivery_date = date('Y-m-d', strtotime($delivery_date . '+ 1 day'));
            $is_weekend = date('N', strtotime($delivery_date)) >= 6;
        }

        $orderPDF_delivery_date = $delivery_date;

        // $delivery_date = date('Y-m-d', strtotime("+".$request->delivery_days[$i].' days'));
        // $is_weekend = date('N', strtotime($delivery_date)) >= 6;

        // while($is_weekend)
        // {
        //     $delivery_date = date('Y-m-d', strtotime($delivery_date. '+ 1 days'));
        //     $is_weekend = date('N', strtotime($delivery_date)) >= 6;
        // }

        $quotation_data_ids = array();
        $orders_ids_array = array();
        $invoice_data_ids = array();
        $calculation_ids = array();
        $order_calculation_ids = array();
        $features_ids = array();
        $order_features_ids_array = array();
        $sub_product_ids = array();
        $order_sub_product_ids = array();
        $invoice_calculation_ids = array();
        $invoice_features_ids = array();
        $invoice_sub_product_ids = array();
        $product_titles = array();
        $color_titles = array();
        $model_titles = array();
        $suppliers = array();
        $product_descriptions = array();
        $vat_percentages = array();

        foreach ($products as $i => $key) {

            $vat_percentages[] = vats::where('id', $request->vats[$i])->pluck('vat_percentage')->first();
            /*$feature_titles[$i][] = 'empty';*/
            $feature_sub_titles[$i][] = array();
            $sub_titles[$i] = '';
            $row_id = $request->row_id[$i];

            if (strpos($key, 'I') > -1) {

                $product_titles[] = items::where('id', (int)$key)->pluck('cat_name')->first();
                $color_titles[] = '';
                $model_titles[] = '';
                $suppliers[] = NULL;
            } elseif (strpos($key, 'S') > -1) {

                $product_titles[] = Service::where('id', (int)$key)->pluck('title')->first();
                $color_titles[] = '';
                $model_titles[] = '';
                $suppliers[] = NULL;
            } else {
                if ($key && $key != 0) {
                    $product_titles[] = product::where('id', $key)->pluck('title')->first();
                    $color_titles[] = colors::where('id', $request->colors[$i])->pluck('title')->first();
                    $model_titles[] = product_models::where('id', $request->models[$i])->pluck('model')->first();
                    $suppliers[] = organizations::where('id', $request->suppliers[$i])->first();
                } else {
                    $product_titles[] = '';
                    $color_titles[] = '';
                    $model_titles[] = '';
                    $suppliers[] = NULL;
                }
            }

            $product_descriptions[] = $form_type == 1 ? $request->product_descriptions[$i] : "";

            if (!$request->is_invoice) {
                $invoice_items = new_quotations_data::where("quotation_id", $request->quotation_id)->skip($i)->first();

                if (!$invoice_items) {
                    $invoice_items = new new_quotations_data;
                }

                $invoice_items->quotation_id = $invoice->id;

                if (!$key) {
                    $order_numbers[$i] = '';

                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } elseif (strpos($key, 'I') > -1) {

                    $order_numbers[$i] = '';

                    $invoice_items->item_id = (int)$key;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } elseif (strpos($key, 'S') > -1) {

                    $order_numbers[$i] = '';

                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = (int)$key;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } else {

                    if (!$request->order_number[$i]) {
                        $order_number = new_orders::where('quotation_id', $invoice->id)->where('supplier_id', $suppliers[$i]->id)->pluck('order_number')->first();

                        if (!$order_number) {
                            $counter_order = $suppliers[$i]->counter_order;
                            $order_number = $suppliers[$i]->order_client_id ? date("Y") . "-" . sprintf('%04u', $suppliers[$i]->id) . '-' . sprintf('%06u', $counter_order) : date("Y") . '-' . sprintf('%06u', $counter_order);
                            $counter_order = $counter_order + 1;
                            $suppliers[$i]->update(['counter_order' => $counter_order]);
                        }
                    } else {
                        $order_number = $request->order_number[$i];
                    }

                    $order_numbers[$i] = $order_number;

                    $invoice_items->order_number = $order_number;
                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = $request->suppliers[$i];
                    $invoice_items->product_id = (int)$key;
                    $invoice_items->model_id = $request->models[$i];
                    $invoice_items->color = $request->colors[$i];
                }

                if ($form_type == 2) {
                    $cutting_variables = array();
                    $curtain_variable_title = "curtain_variable_titles" . $row_id;
                    $curtain_variable_titles = $request->$curtain_variable_title;

                    if ($curtain_variable_titles) {
                        foreach ($curtain_variable_titles as $cv => $key) {
                            $cutting_variables[] = array("title" => $key, "value" => $request->curtain_variable_values1[$cv]);
                        }
                    }

                    $invoice_items->cutting_size = isset($request->cutting_sizes[$i]) ? $request->cutting_sizes[$i] : NULL;
                    $invoice_items->cutting_variables = count($cutting_variables) > 0 ? json_encode($cutting_variables) : NULL;
                    $invoice_items->model_impact_value = $request->model_impact_value[$i];
                    $invoice_items->model_factor_max_width = $request->model_factor_max_width[$i] ? $request->model_factor_max_width[$i] : 100;
                    $invoice_items->width = str_replace(',', '.', $request->width[$i]);
                    $invoice_items->width_unit = $request->width_unit[$i];
                    $invoice_items->height = str_replace(',', '.', $request->height[$i]);
                    $invoice_items->height_unit = $request->height_unit[$i];
                    $invoice_items->price_based_option = $request->price_based_option[$i];
                    $invoice_items->labor_impact = $request->labor_impact[$i] ? str_replace(',', '.', $request->labor_impact[$i]) : 0;
                    $invoice_items->supplier_margin = $request->supplier_margin[$i] ? $request->supplier_margin[$i] : 0;
                    $invoice_items->retailer_margin = $request->retailer_margin[$i] ? $request->retailer_margin[$i] : 0;
                    $invoice_items->labor_discount = $request->labor_discount[$i] ? $request->labor_discount[$i] : 0;
                    $invoice_items->basic_price = $request->basic_price[$i];
                    $invoice_items->box_quantity = NULL;
                    $invoice_items->total_boxes_total = NULL;
                    $invoice_items->grand_totaal = NULL;
                    $invoice_items->grand_totaal_st = NULL;
                    $invoice_items->measure = NULL;
                    $invoice_items->max_width = NULL;
                } else {
                    $invoice_items->secondary_title = $request->secondary_titles[$i];
                    $invoice_items->description = $request->product_descriptions[$i];
                    $invoice_items->model_impact_value = 0;
                    $invoice_items->model_factor_max_width = 100;
                    $invoice_items->width = 0;
                    $invoice_items->width_unit = "";
                    $invoice_items->height = 0;
                    $invoice_items->height_unit = "";
                    $invoice_items->price_based_option = 0;
                    $invoice_items->labor_impact = 0;
                    $invoice_items->supplier_margin = 0;
                    $invoice_items->retailer_margin = 0;
                    $invoice_items->labor_discount = 0;
                    $invoice_items->basic_price = 0;
                    $invoice_items->box_quantity = $request->estimated_price_quantity[$i] ? $request->estimated_price_quantity[$i] : 0;
                    $invoice_items->total_boxes_total = $request->total_boxes_total[$i] ? $request->total_boxes_total[$i] : 0;
                    $invoice_items->grand_totaal = $request->grand_totaal[$i] ? $request->grand_totaal[$i] : 0;
                    $invoice_items->grand_totaal_st = $request->grand_totaal_st[$i] ? $request->grand_totaal_st[$i] : 0;
                    $invoice_items->measure = $request->measure[$i] ? $request->measure[$i] : 0;
                    $invoice_items->max_width = $request->max_width[$i] ? $request->max_width[$i] : 0;
                    $invoice_items->discount_option = $request->discount_option_values[$i] ? 1 : 0;
                    $invoice_items->ledger_id = $request->general_ledgers[$i];
                }

                $invoice_items->row_id = $row_id;
                $invoice_items->rate = $request->rate[$i];
                $invoice_items->qty = $request->qty[$i] ? ($request->qty[$i] == "-" || $request->qty[$i] == "-," || $request->qty[$i] == "," ? 0 : str_replace(',', '.', $request->qty[$i])) : 0;
                $invoice_items->amount = $this->is_NaN($request->total[$i]) ? 0 : $request->total[$i];
                $invoice_items->delivery_days = $request->delivery_days[$i] ? $request->delivery_days[$i] : 1;
                $invoice_items->delivery_date = $delivery_date;
                $invoice_items->price_before_labor = $request->price_before_labor[$i] ? ($request->price_before_labor[$i] == "-" || $request->price_before_labor[$i] == "-," || $request->price_before_labor[$i] == "," ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor[$i]))) : 0;
                $invoice_items->discount = $request->discount[$i] ? ($request->discount[$i] == "-" || $request->discount[$i] == "-," || $request->discount[$i] == "," ? 0 : str_replace(',', '.', $request->discount[$i])) : 0;
                $invoice_items->total_discount = $request->total_discount[$i] ? str_replace(',', '.', $request->total_discount[$i]) : 0;
                $invoice_items->base_price = $request->base_price[$i] ? $request->base_price[$i] : 0;
                $invoice_items->vat_id = $request->vats[$i];

                if ($key && strpos($key, 'I') == 0 && strpos($key, 'S') == 0) {

                    $order = new_orders::where("quotation_id", $request->quotation_id)->skip($i)->first();

                    if (!$order) {
                        $order = new new_orders;
                    }

                    $order->order_number = $order_number;
                    $order->quotation_id = $invoice->id;
                    $order->supplier_id = $request->suppliers[$i];
                    $order->product_id = (int)$key;
                    $order->row_id = $row_id;
                    $order->model_id = $request->models[$i];
                    $order->color = $request->colors[$i];
                    $order->rate = $request->rate[$i];
                    $order->qty = $request->qty[$i] ? ($request->qty[$i] == "-" || $request->qty[$i] == "-," || $request->qty[$i] == "," ? 0 : str_replace(',', '.', $request->qty[$i])) : 0;
                    $order->amount = $this->is_NaN($request->total[$i]) ? 0 : $request->total[$i];
                    $order->delivery_days = $request->delivery_days[$i] ? $request->delivery_days[$i] : 1;
                    // $order->delivery_date = $delivery_date;
                    // $order->retailer_delivery_date = $delivery_date;
                    $order->price_before_labor = $request->price_before_labor[$i] ? ($request->price_before_labor[$i] == "-" || $request->price_before_labor[$i] == "-," || $request->price_before_labor[$i] == "," ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor[$i]))) : 0;
                    $order->discount = $request->discount[$i] ? ($request->discount[$i] == "-" || $request->discount[$i] == "-," || $request->discount[$i] == "," ? 0 : str_replace(',', '.', $request->discount[$i])) : 0;
                    $order->total_discount = $request->total_discount[$i] ? str_replace(',', '.', $request->total_discount[$i]) : 0;
                    $order->base_price = $request->base_price[$i] ? $request->base_price[$i] : 0;

                    if ($request->childsafe[$i]) {
                        $invoice_items->childsafe = $request->childsafe[$i];
                        $order->childsafe = $request->childsafe[$i];

                        $childsafe_question = 'childsafe_option' . $row_id;
                        $invoice_items->childsafe_question = $request->$childsafe_question;
                        $order->childsafe_question = $request->$childsafe_question;

                        $childsafe_diff = 'childsafe_diff' . $row_id;
                        $invoice_items->childsafe_diff = $request->$childsafe_diff;
                        $order->childsafe_diff = $request->$childsafe_diff;

                        $childsafe_answer = 'childsafe_answer' . $row_id;
                        $invoice_items->childsafe_answer = $request->$childsafe_answer;
                        $order->childsafe_answer = $request->$childsafe_answer;

                        $childsafe_x = 'childsafe_x' . $row_id;
                        $invoice_items->childsafe_x = $request->$childsafe_x;
                        $order->childsafe_x = $request->$childsafe_x;

                        $childsafe_y = 'childsafe_y' . $row_id;
                        $invoice_items->childsafe_y = $request->$childsafe_y;
                        $order->childsafe_y = $request->$childsafe_y;
                    }

                    if ($form_type == 2) {
                        $order->model_impact_value = $request->model_impact_value[$i];
                        $order->model_factor_max_width = $request->model_factor_max_width[$i] ? $request->model_factor_max_width[$i] : 100;
                        $order->width = str_replace(',', '.', $request->width[$i]);
                        $order->width_unit = $request->width_unit[$i];
                        $order->height = str_replace(',', '.', $request->height[$i]);
                        $order->height_unit = $request->height_unit[$i];
                        $order->price_based_option = $request->price_based_option[$i];
                        $order->labor_impact = $request->labor_impact[$i] ? str_replace(',', '.', $request->labor_impact[$i]) : 0;
                        $order->labor_discount = $request->labor_discount[$i] ? $request->labor_discount[$i] : 0;
                        $order->supplier_margin = $request->supplier_margin[$i] ? $request->supplier_margin[$i] : 0;
                        $order->retailer_margin = $request->retailer_margin[$i] ? $request->retailer_margin[$i] : 0;
                        $order->basic_price = $request->basic_price[$i];
                        $order->box_quantity = NULL;
                        $order->total_boxes_total = NULL;
                        $order->grand_totaal = NULL;
                        $order->grand_totaal_st = NULL;
                        $order->measure = NULL;
                        $order->max_width = NULL;
                    } else {
                        $order->secondary_title = $request->secondary_titles[$i];
                        $order->description = $request->product_descriptions[$i];
                        $order->model_impact_value = 0;
                        $order->model_factor_max_width = 100;
                        $order->width = 0;
                        $order->width_unit = "";
                        $order->height = 0;
                        $order->height_unit = "";
                        $order->price_based_option = 0;
                        $order->labor_impact = 0;
                        $order->labor_discount = 0;
                        $order->supplier_margin = 0;
                        $order->retailer_margin = 0;
                        $order->basic_price = 0;
                        $order->box_quantity = $request->estimated_price_quantity[$i];
                        $order->total_boxes_total = $request->total_boxes_total[$i] ? $request->total_boxes_total[$i] : 0;
                        $order->grand_totaal = $request->grand_totaal[$i];
                        $order->grand_totaal_st = $request->grand_totaal_st[$i];
                        $order->measure = $request->measure[$i];
                        $order->max_width = $request->max_width[$i];
                        $order->discount_option = $request->discount_option_values[$i] ? 1 : 0;
                    }

                    $invoice_items->save();
                    $quotation_data_ids[] = $invoice_items->id;

                    if (!$invoice->finished) {
                        $order->save();
                        $orders_ids_array[] = $order->id;
                    }

                    if ($form_type == 1) {
                        $calculator_row = 'calculator_row' . $row_id;
                        $calculator_row = $request->$calculator_row ? $request->$calculator_row : [];

                        foreach ($calculator_row as $c => $cal) {
                            $description = 'attribute_description' . $row_id;
                            $width = 'width' . $row_id;
                            $height = 'height' . $row_id;
                            $cutting_lose = 'cutting_lose_percentage' . $row_id;
                            $box_quantity_supplier = 'box_quantity_supplier' . $row_id;
                            $box_quantity = 'box_quantity' . $row_id;
                            $total_boxes = 'total_boxes' . $row_id;
                            $total_inc_cuttinglose = 'total_inc_cuttinglose' . $row_id;
                            $max_width = 'max_width' . $row_id;
                            $turn = 'turn' . $row_id;

                            if (is_numeric($cal) && floor($cal) != $cal) {
                                $parent_row = floor($cal);
                            } else {
                                $parent_row = NULL;
                            }

                            $calculations = new_quotations_data_calculations::where("quotation_data_id", $invoice_items->id)->skip($c)->first();

                            if (!$calculations) {
                                $calculations = new new_quotations_data_calculations;
                            }

                            $calculations->quotation_data_id = $invoice_items->id;
                            $calculations->calculator_row = $cal;
                            $calculations->parent_row = $parent_row;
                            $calculations->description = $request->$description[$c];
                            $calculations->width = $request->$width[$c] ? str_replace(',', '.', $request->$width[$c]) : NULL;
                            $calculations->height = $request->$height[$c] ? str_replace(',', '.', $request->$height[$c]) : NULL;
                            $calculations->cutting_lose = $request->$cutting_lose[$c];
                            $calculations->box_quantity_supplier = $request->$box_quantity_supplier[$c] ? str_replace(',', '.', $request->$box_quantity_supplier[$c]) : NULL;
                            $calculations->box_quantity = $request->$box_quantity[$c] ? str_replace(',', '.', $request->$box_quantity[$c]) : NULL;
                            $calculations->total_boxes = $request->$total_boxes[$c] ? str_replace(',', '.', $request->$total_boxes[$c]) : NULL;
                            $calculations->total_inc_cutting = $request->$total_inc_cuttinglose[$c] ? str_replace(',', '.', $request->$total_inc_cuttinglose[$c]) : NULL;
                            $calculations->max_width = $request->$max_width[$c];
                            $calculations->turn = $request->$turn[$c];
                            $calculations->save();
                            $calculation_ids[] = $calculations->id;

                            $order_calculations = new_orders_calculations::where("order_id", $order->id)->skip($c)->first();

                            if (!$order_calculations) {
                                $order_calculations = new new_orders_calculations;
                            }

                            if (!$invoice->finished) {
                                $order_calculations->order_id = $order->id;
                                $order_calculations->calculator_row = $cal;
                                $order_calculations->parent_row = $parent_row;
                                $order_calculations->description = $request->$description[$c];
                                $order_calculations->width = $request->$width[$c] ? str_replace(',', '.', $request->$width[$c]) : NULL;
                                $order_calculations->height = $request->$height[$c] ? str_replace(',', '.', $request->$height[$c]) : NULL;
                                $order_calculations->cutting_lose = $request->$cutting_lose[$c];
                                $order_calculations->box_quantity_supplier = $request->$box_quantity_supplier[$c] ? str_replace(',', '.', $request->$box_quantity_supplier[$c]) : NULL;
                                $order_calculations->box_quantity = $request->$box_quantity[$c] ? str_replace(',', '.', $request->$box_quantity[$c]) : NULL;
                                $order_calculations->total_boxes = $request->$total_boxes[$c] ? str_replace(',', '.', $request->$total_boxes[$c]) : NULL;
                                $order_calculations->total_inc_cutting = $request->$total_inc_cuttinglose[$c] ? str_replace(',', '.', $request->$total_inc_cuttinglose[$c]) : NULL;
                                $order_calculations->max_width = $request->$max_width[$c];
                                $order_calculations->turn = $request->$turn[$c];
                            } else {
                                $order_calculations->description = $request->$description[$c];
                            }

                            $order_calculations->save();
                            $order_calculation_ids[] = $order_calculations->id;
                        }
                    }

                    $feature_row = 'features' . $row_id;
                    $features = $request->$feature_row;

                    if ($features) {
                        foreach ($features as $f => $key1) {
                            $f_row = 'f_id' . $row_id;
                            $f_ids = $request->$f_row;

                            $f_row1 = 'f_price' . $row_id;
                            $f_prices = $request->$f_row1;

                            $is_sub = 'sub_feature' . $row_id;
                            $is_sub_feature = $request->$is_sub;

                            $comment = 'comment-' . $row_id . '-' . $f_ids[$f];
                            $comment = $request->$comment;

                            if ($f_ids[$f] == 0) {
                                $post = new_quotations_features::where("quotation_data_id", $invoice_items->id)->skip($f)->first();

                                if (!$post) {
                                    $post = new new_quotations_features;
                                }

                                $post->quotation_data_id = $invoice_items->id;
                                $post->price = $f_prices[$f];
                                $post->feature_id = $f_ids[$f];
                                $post->feature_sub_id = 0;
                                $post->ladderband = $key1;
                                $post->save();
                                $features_ids[] = $post->id;

                                if (!$invoice->finished) {
                                    $post_order_features = new_orders_features::where("order_data_id", $order->id)->skip($f)->first();

                                    if (!$post_order_features) {
                                        $post_order_features = new new_orders_features;
                                    }

                                    $post_order_features->order_data_id = $order->id;
                                    $post_order_features->price = $f_prices[$f];
                                    $post_order_features->feature_id = $f_ids[$f];
                                    $post_order_features->feature_sub_id = 0;
                                    $post_order_features->ladderband = $key1;
                                    $post_order_features->save();
                                    $order_features_ids_array[] = $post_order_features->id;
                                }

                                if ($key1) {
                                    $size1 = 'sizeA' . $row_id[$f];
                                    $size1_value = $request->$size1;

                                    $size2 = 'sizeB' . $row_id[$f];
                                    $size2_value = $request->$size2;

                                    $sub = 'sub_product_id' . $row_id[$f];
                                    $sub_value = $request->$sub;

                                    foreach ($sub_value as $s => $key2) {
                                        $post1 = new_quotations_sub_products::where("feature_row_id", $post->id)->skip($s)->first();

                                        if (!$post1) {
                                            $post1 = new new_quotations_sub_products;
                                        }

                                        $post1->feature_row_id = $post->id;
                                        $post1->sub_product_id = $key2;
                                        $post1->size1_value = $size1_value[$s];
                                        $post1->size2_value = $size2_value[$s];
                                        $post1->save();
                                        $sub_product_ids[] = $post1->id;

                                        if (!$invoice->finished) {
                                            $post_orders_sub_products = new_orders_sub_products::where("feature_row_id", $post_order_features->id)->skip($s)->first();

                                            if (!$post_orders_sub_products) {
                                                $post_orders_sub_products = new new_orders_sub_products;
                                            }

                                            $post_orders_sub_products->feature_row_id = $post_order_features->id;
                                            $post_orders_sub_products->sub_product_id = $key2;
                                            $post_orders_sub_products->size1_value = $size1_value[$s];
                                            $post_orders_sub_products->size2_value = $size2_value[$s];
                                            $post_orders_sub_products->save();
                                            $order_sub_product_ids[] = $post_orders_sub_products->id;
                                        }

                                        if ($size1_value[$s] == 1 || $size2_value[$s] == 1) {
                                            $sub_titles[$i] = product_ladderbands::where('product_id', $key)->where('id', $key2)->first();

                                            if ($size1_value[$s] == 1) {
                                                $sub_titles[$i]->size = '38mm';
                                            } else {
                                                $sub_titles[$i]->size = '25mm';
                                            }
                                        }
                                    }
                                }
                            } else {
                                $post = new_quotations_features::where("quotation_data_id", $invoice_items->id)->skip($f)->first();

                                if (!$post) {
                                    $post = new new_quotations_features;
                                }

                                $post->quotation_data_id = $invoice_items->id;
                                $post->price = $f_prices[$f];
                                $post->feature_id = $f_ids[$f];
                                $post->feature_sub_id = $key1;
                                $post->sub_feature = $is_sub_feature[$f];
                                $post->comment = $comment;
                                $post->save();
                                $features_ids[] = $post->id;

                                if (!$invoice->finished) {
                                    $post_order_features = new_orders_features::where("order_data_id", $order->id)->skip($f)->first();

                                    if (!$post_order_features) {
                                        $post_order_features = new new_orders_features;
                                    }

                                    $post_order_features->order_data_id = $order->id;
                                    $post_order_features->price = $f_prices[$f];
                                    $post_order_features->feature_id = $f_ids[$f];
                                    $post_order_features->feature_sub_id = $key1;
                                    $post_order_features->sub_feature = $is_sub_feature[$f];
                                    $post_order_features->comment = $comment;
                                    $post_order_features->save();
                                    $order_features_ids_array[] = $post_order_features->id;
                                }
                            }

                            /*$feature_titles[$i][] = features::where('id',$f_ids[$f])->first();*/
                            $feature_sub_titles[$i][] = product_features::leftjoin('features', 'features.id', '=', 'product_features.heading_id')->where('product_features.product_id', $key)->where('product_features.id', $key1)->select('product_features.*', 'features.title as main_title', 'features.order_no', 'features.id as f_id')->first();
                        }
                    } else {
                        $feature_sub_titles[$i] = array();
                    }
                } else {
                    $invoice_items->save();
                    $quotation_data_ids[] = $invoice_items->id;
                    $feature_sub_titles[$i] = array();
                }
            } else {
                if (!$request->negative_invoice) {
                    $invoice_items = new_invoices_data::where('invoice_id', $request->quotation_id)->skip($i)->first();
                } else {
                    $invoice_items = new_invoices_data::where('invoice_id', $request->negative_invoice_id)->skip($i)->first();
                }

                if (!$invoice_items) {
                    $invoice_items = new new_invoices_data;
                }

                $invoice_items->invoice_id = $invoice->id;
                $invoice_items->row_id = $row_id;

                if (!$key) {
                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } else if (strpos($key, 'I') > -1) {

                    $invoice_items->item_id = (int)$key;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } elseif (strpos($key, 'S') > -1) {

                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = (int)$key;
                    $invoice_items->supplier_id = 0;
                    $invoice_items->product_id = 0;
                    $invoice_items->model_id = 0;
                    $invoice_items->color = 0;
                } else {

                    $invoice_items->item_id = 0;
                    $invoice_items->service_id = 0;
                    $invoice_items->supplier_id = $request->suppliers[$i];
                    $invoice_items->product_id = (int)$key;
                    $invoice_items->model_id = $request->models[$i];
                    $invoice_items->color = $request->colors[$i];
                }

                if ($form_type == 2) {
                    $cutting_variables = array();
                    $curtain_variable_title = "curtain_variable_titles" . $row_id;
                    $curtain_variable_titles = $request->$curtain_variable_title;

                    if ($curtain_variable_titles) {
                        foreach ($curtain_variable_titles as $cv => $key) {
                            $cutting_variables[] = array("title" => $key, "value" => $request->curtain_variable_values1[$cv]);
                        }
                    }

                    $invoice_items->cutting_size = $request->cutting_sizes[$i] ? $request->cutting_sizes[$i] : NULL;
                    $invoice_items->cutting_variables = count($cutting_variables) > 0 ? json_encode($cutting_variables) : NULL;
                    $invoice_items->model_impact_value = $request->model_impact_value[$i];
                    $invoice_items->model_factor_max_width = $request->model_factor_max_width[$i] ? $request->model_factor_max_width[$i] : 100;
                    $invoice_items->width = str_replace(',', '.', $request->width[$i]);
                    $invoice_items->width_unit = $request->width_unit[$i];
                    $invoice_items->height = str_replace(',', '.', $request->height[$i]);
                    $invoice_items->height_unit = $request->height_unit[$i];
                    $invoice_items->price_based_option = $request->price_based_option[$i];
                    $invoice_items->labor_impact = $request->labor_impact[$i] ? str_replace(',', '.', $request->labor_impact[$i]) : 0;
                    $invoice_items->supplier_margin = $request->supplier_margin[$i] ? $request->supplier_margin[$i] : 0;
                    $invoice_items->retailer_margin = $request->retailer_margin[$i] ? $request->retailer_margin[$i] : 0;
                    $invoice_items->labor_discount = $request->labor_discount[$i] ? $request->labor_discount[$i] : 0;
                    $invoice_items->basic_price = $request->basic_price[$i];
                    $invoice_items->box_quantity = NULL;
                    $invoice_items->total_boxes_total = NULL;
                    $invoice_items->grand_totaal = NULL;
                    $invoice_items->grand_totaal_st = NULL;
                    $invoice_items->measure = NULL;
                    $invoice_items->max_width = NULL;
                } else {
                    $invoice_items->secondary_title = $request->secondary_titles[$i];
                    $invoice_items->description = $request->product_descriptions[$i];
                    $invoice_items->model_impact_value = 0;
                    $invoice_items->model_factor_max_width = 100;
                    $invoice_items->width = 0;
                    $invoice_items->width_unit = "";
                    $invoice_items->height = 0;
                    $invoice_items->height_unit = "";
                    $invoice_items->price_based_option = 0;
                    $invoice_items->labor_impact = 0;
                    $invoice_items->supplier_margin = 0;
                    $invoice_items->retailer_margin = 0;
                    $invoice_items->labor_discount = 0;
                    $invoice_items->basic_price = 0;
                    $invoice_items->box_quantity = $request->estimated_price_quantity[$i];
                    $invoice_items->total_boxes_total = $request->total_boxes_total[$i] ? $request->total_boxes_total[$i] : 0;
                    $invoice_items->grand_totaal = $request->grand_totaal[$i];
                    $invoice_items->grand_totaal_st = $request->grand_totaal_st[$i];
                    $invoice_items->measure = $request->measure[$i];
                    $invoice_items->max_width = $request->max_width[$i];
                    $invoice_items->ledger_id = $request->general_ledgers[$i];
                    $invoice_items->discount_option = $request->discount_option_values[$i] ? 1 : 0;
                }

                $invoice_items->rate = $this->is_NaN($request->rate[$i]) ? 0 : $request->rate[$i];
                $invoice_items->qty = $request->qty[$i] ? ($request->qty[$i] == "-" || $request->qty[$i] == "-," || $request->qty[$i] == "," ? 0 : str_replace(',', '.', $request->qty[$i])) : 0;
                $invoice_items->amount = $this->is_NaN($request->total[$i]) ? 0 : $request->total[$i];
                $invoice_items->delivery_days = $request->delivery_days[$i] ? $request->delivery_days[$i] : 1;
                $invoice_items->delivery_date = $delivery_date;
                $invoice_items->price_before_labor = $request->price_before_labor[$i] ? ($request->price_before_labor[$i] == "-" || $request->price_before_labor[$i] == "-," || $request->price_before_labor[$i] == "," ? 0 : str_replace(',', '.', str_replace('.', '', $request->price_before_labor[$i]))) : 0;
                $invoice_items->discount = $request->discount[$i] ? ($request->discount[$i] == "-" || $request->discount[$i] == "-," || $request->discount[$i] == "," ? 0 : str_replace(',', '.', $request->discount[$i])) : 0;
                $invoice_items->total_discount = $request->total_discount[$i] ? str_replace(',', '.', $request->total_discount[$i]) : 0;
                $invoice_items->base_price = $request->base_price[$i] ? $request->base_price[$i] : 0;
                $invoice_items->vat_id = $request->vats[$i];

                if ($key && strpos($key, 'I') == 0 && strpos($key, 'S') == 0) {

                    if ($request->childsafe[$i]) {
                        $invoice_items->childsafe = $request->childsafe[$i];

                        $childsafe_question = 'childsafe_option' . $row_id;
                        $invoice_items->childsafe_question = $request->$childsafe_question;

                        $childsafe_diff = 'childsafe_diff' . $row_id;
                        $invoice_items->childsafe_diff = $request->$childsafe_diff;

                        $childsafe_answer = 'childsafe_answer' . $row_id;
                        $invoice_items->childsafe_answer = $request->$childsafe_answer;

                        $childsafe_x = 'childsafe_x' . $row_id;
                        $invoice_items->childsafe_x = $request->$childsafe_x;

                        $childsafe_y = 'childsafe_y' . $row_id;
                        $invoice_items->childsafe_y = $request->$childsafe_y;
                    }

                    $invoice_items->save();
                    $invoice_data_ids[] = $invoice_items->id;

                    if ($form_type == 1) {
                        $calculator_row = 'calculator_row' . $row_id;
                        $calculator_row = $request->$calculator_row ? $request->$calculator_row : [];

                        foreach ($calculator_row as $c => $cal) {
                            $description = 'attribute_description' . $row_id;
                            $width = 'width' . $row_id;
                            $height = 'height' . $row_id;
                            $cutting_lose = 'cutting_lose_percentage' . $row_id;
                            $box_quantity_supplier = 'box_quantity_supplier' . $row_id;
                            $box_quantity = 'box_quantity' . $row_id;
                            $total_boxes = 'total_boxes' . $row_id;
                            $total_inc_cuttinglose = 'total_inc_cuttinglose' . $row_id;
                            $max_width = 'max_width' . $row_id;
                            $turn = 'turn' . $row_id;

                            if (is_numeric($cal) && floor($cal) != $cal) {
                                $parent_row = floor($cal);
                            } else {
                                $parent_row = NULL;
                            }

                            $calculations = new_invoices_data_calculations::where("invoice_data_id", $invoice_items->id)->skip($c)->first();

                            if (!$calculations) {
                                $calculations = new new_invoices_data_calculations;
                            }

                            $calculations->invoice_data_id = $invoice_items->id;
                            $calculations->calculator_row = $cal;
                            $calculations->parent_row = $parent_row;
                            $calculations->description = $request->$description[$c];
                            $calculations->width = $request->$width[$c] ? str_replace(',', '.', $request->$width[$c]) : NULL;
                            $calculations->height = $request->$height[$c] ? str_replace(',', '.', $request->$height[$c]) : NULL;
                            $calculations->cutting_lose = $request->$cutting_lose[$c];
                            $calculations->box_quantity_supplier = $request->$box_quantity_supplier[$c] ? str_replace(',', '.', $request->$box_quantity_supplier[$c]) : NULL;
                            $calculations->box_quantity = $request->$box_quantity[$c] ? str_replace(',', '.', $request->$box_quantity[$c]) : NULL;
                            $calculations->total_boxes = $request->$total_boxes[$c] ? str_replace(',', '.', $request->$total_boxes[$c]) : NULL;
                            $calculations->total_inc_cutting = $request->$total_inc_cuttinglose[$c] ? str_replace(',', '.', $request->$total_inc_cuttinglose[$c]) : NULL;
                            $calculations->max_width = $request->$max_width[$c];
                            $calculations->turn = $request->$turn[$c];
                            $calculations->save();
                            $invoice_calculation_ids[] = $calculations->id;
                        }
                    }

                    $feature_row = 'features' . $row_id;
                    $features = $request->$feature_row;

                    if ($features) {
                        foreach ($features as $f => $key1) {
                            $f_row = 'f_id' . $row_id;
                            $f_ids = $request->$f_row;

                            $f_row1 = 'f_price' . $row_id;
                            $f_prices = $request->$f_row1;

                            $is_sub = 'sub_feature' . $row_id;
                            $is_sub_feature = $request->$is_sub;

                            $comment = 'comment-' . $row_id . '-' . $f_ids[$f];
                            $comment = $request->$comment;

                            if ($f_ids[$f] == 0) {
                                $post = new_invoices_features::where("invoice_data_id", $invoice_items->id)->skip($f)->first();

                                if (!$post) {
                                    $post = new new_invoices_features;
                                }

                                $post->invoice_data_id = $invoice_items->id;
                                $post->price = $f_prices[$f];
                                $post->feature_id = $f_ids[$f];
                                $post->feature_sub_id = 0;
                                $post->ladderband = $key1;
                                $post->save();
                                $invoice_features_ids[] = $post->id;

                                if ($key1) {
                                    $size1 = 'sizeA' . $row_id[$f];
                                    $size1_value = $request->$size1;

                                    $size2 = 'sizeB' . $row_id[$f];
                                    $size2_value = $request->$size2;

                                    $sub = 'sub_product_id' . $row_id[$f];
                                    $sub_value = $request->$sub;

                                    foreach ($sub_value as $s => $key2) {
                                        $post1 = new_invoices_sub_products::where("feature_row_id", $post->id)->skip($s)->first();

                                        if (!$post1) {
                                            $post1 = new new_invoices_sub_products;
                                        }

                                        $post1->feature_row_id = $post->id;
                                        $post1->sub_product_id = $key2;
                                        $post1->size1_value = $size1_value[$s];
                                        $post1->size2_value = $size2_value[$s];
                                        $post1->save();
                                        $invoice_sub_product_ids[] = $post1->id;

                                        if ($size1_value[$s] == 1 || $size2_value[$s] == 1) {
                                            $sub_titles[$i] = product_ladderbands::where('product_id', $key)->where('id', $key2)->first();

                                            if ($size1_value[$s] == 1) {
                                                $sub_titles[$i]->size = '38mm';
                                            } else {
                                                $sub_titles[$i]->size = '25mm';
                                            }
                                        }
                                    }
                                }
                            } else {
                                $post = new_invoices_features::where("invoice_data_id", $invoice_items->id)->skip($f)->first();

                                if (!$post) {
                                    $post = new new_invoices_features;
                                }

                                $post = new new_invoices_features;
                                $post->invoice_data_id = $invoice_items->id;
                                $post->price = $f_prices[$f];
                                $post->feature_id = $f_ids[$f];
                                $post->feature_sub_id = $key1;
                                $post->sub_feature = $is_sub_feature[$f];
                                $post->comment = $comment;
                                $post->save();
                                $invoice_features_ids[] = $post->id;
                            }

                            /*$feature_titles[$i][] = features::where('id',$f_ids[$f])->first();*/
                            $feature_sub_titles[$i][] = product_features::leftjoin('features', 'features.id', '=', 'product_features.heading_id')->where('product_features.product_id', $key)->where('product_features.id', $key1)->select('product_features.*', 'features.title as main_title', 'features.order_no', 'features.id as f_id')->first();
                        }
                    } else {
                        $feature_sub_titles[$i] = array();
                    }
                } else {
                    $invoice_items->save();
                    $invoice_data_ids[] = $invoice_items->id;
                    $feature_sub_titles[$i] = array();
                }
            }
        }

        if ($request->quotation_id) {
            if (!$request->is_invoice) {
                $data_ids = new_quotations_data::where('quotation_id', $request->quotation_id)->pluck('id');
                $feature_ids = new_quotations_features::whereIn('quotation_data_id', $data_ids)->pluck('id');

                $order_ids = new_orders::where('quotation_id', $request->quotation_id)->pluck('id');
                $order_feature_ids = new_orders_features::whereIn('order_data_id', $order_ids)->pluck('id');

                payment_calculations::where('quotation_id', $request->quotation_id)->whereNotIn("id", $payment_calculations_ids)->delete();
                new_quotations_data::where('quotation_id', $request->quotation_id)->whereNotIn("id", $quotation_data_ids)->delete();
                new_quotations_features::whereIn('quotation_data_id', $data_ids)->whereNotIn("id", $features_ids)->delete();
                new_quotations_sub_products::whereIn('feature_row_id', $feature_ids)->whereNotIn("id", $sub_product_ids)->delete();

                if ($form_type == 1) {
                    new_quotations_data_calculations::whereIn('quotation_data_id', $data_ids)->whereNotIn("id", $calculation_ids)->delete();

                    if (!$invoice->finished) {
                        new_orders_calculations::whereIn('order_id', $order_ids)->whereNotIn("id", $order_calculation_ids)->delete();
                    }
                }

                if (!$invoice->finished) {
                    new_orders::where('quotation_id', $request->quotation_id)->whereNotIn("id", $orders_ids_array)->delete();
                    new_orders_features::whereIn('order_data_id', $order_ids)->whereNotIn("id", $order_features_ids_array)->delete();
                    new_orders_sub_products::whereIn('feature_row_id', $order_feature_ids)->whereNotIn("id", $order_sub_product_ids)->delete();
                }
            } else {
                $data_flag = 0;

                if (!$request->negative_invoice) {
                    $data_flag = 1;
                    $data_ids = new_invoices_data::where('invoice_id', $request->quotation_id)->pluck('id');

                    invoice_payment_calculations::where('invoice_id', $request->quotation_id)->whereNotIn("id", $payment_calculations_ids)->delete();
                    new_invoices_data::where('invoice_id', $request->quotation_id)->whereNotIn("id", $invoice_data_ids)->delete();
                } else {
                    if ($request->negative_invoice_id) {
                        $data_flag = 1;
                        $data_ids = new_invoices_data::where('invoice_id', $request->negative_invoice_id)->pluck('id');

                        invoice_payment_calculations::where('invoice_id', $request->negative_invoice_id)->whereNotIn("id", $payment_calculations_ids)->delete();
                        new_invoices_data::where('invoice_id', $request->negative_invoice_id)->whereNotIn("id", $invoice_data_ids)->delete();
                    }
                }

                if ($data_flag) {
                    $feature_ids = new_invoices_features::whereIn('invoice_data_id', $data_ids)->pluck('id');

                    if ($form_type == 1) {
                        new_invoices_data_calculations::whereIn('invoice_data_id', $data_ids)->whereNotIn("id", $invoice_calculation_ids)->delete();
                    }

                    new_invoices_features::whereIn('invoice_data_id', $data_ids)->whereNotIn("id", $invoice_features_ids)->delete();
                    new_invoices_sub_products::whereIn('feature_row_id', $feature_ids)->whereNotIn("id", $invoice_sub_product_ids)->delete();
                }
            }
        }

        $delivery_date = $delivery_date_start ? date('d-m-Y', strtotime($delivery_date_start)) . ' - ' . date('d-m-Y', strtotime($delivery_date_end)) : '';
        $installation_date = $installation_date_start ? date('d-m-Y', strtotime($installation_date_start)) . ' - ' . date('d-m-Y', strtotime($installation_date_end)) : '';

        if (!$request->is_invoice) {
            $filename = $quotation_invoice_number . '.pdf';

            if ($request->quotation_id) {
                if ($ask && !$request->quote_request_id) {
                    if ($this->lang->lang == 'du') {
                        $msg = "Offerte QUO# <b>" . $quotation_invoice_number . "</b> is op je verzoek aangepast door de retailer.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Vloerofferte";
                    } else {
                        $msg = "Quotation QUO# <b>" . $quotation_invoice_number . "</b> have been updated by retailer on your review request.<br><br>Kind regards,<br><br>Customer service<br><br> Vloerofferte";
                    }

                    \Mail::send(array(), array(), function ($message) use ($msg, $client, $quotation_invoice_number, $company_email, $company_name) {
                        $message->to($client->email)
                            ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com', $company_name)
                            ->replyTo($company_email, $company_name)
                            ->subject(__('text.Quotation updated!'))
                            ->html($msg, 'text/html');
                    });
                }

                if (!$ajax_request) {
                    Session::flash('success', __('text.Quotation has been updated successfully. Order will be updated soon in background process.'));
                }
            } else {
                if (!$ajax_request) {
                    Session::flash('success', __('text.Quotation has been created successfully. Order will be created soon in background process.'));
                }
            }

            ini_set('max_execution_time', 180);

            $date = $invoice->document_date;
            $role = 'retailer';
            $retailer_quotations_folder_path = public_path() . '/assets/newQuotations/' . $organization_id;
            $retailer_drafts_folder_path = public_path() . '/assets/draftQuotations/' . $organization_id;

            if (!file_exists($retailer_quotations_folder_path)) {
                mkdir($retailer_quotations_folder_path, 0775, true);
            }

            $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 'form_type', 'role', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            $file = $retailer_quotations_folder_path . '/' . $filename;
            $pdf->save($file);

            if (!file_exists($retailer_drafts_folder_path)) {
                mkdir($retailer_drafts_folder_path, 0775, true);
            }

            if (!file_exists($retailer_drafts_folder_path . '/' . $filename)) {
                copy($file, $retailer_drafts_folder_path . '/' . $filename);
            }

            if (!$invoice->finished) {

                if (array_filter($suppliers)) {

                    $invoice->processing = 1;
                    $invoice->save();
                    $quotation_id = $invoice->id;
                    $date = $invoice->created_at;

                    if ($form_type == 1) {
                        $role = 'order';
                        $copy = 0;
                        CreateOrder::dispatch($orderPDF_delivery_date, $quotation_id, $form_type, $role, $product_titles, $color_titles, $model_titles, $feature_sub_titles, $sub_titles, $date, $client, $user, json_encode($request->all()), $quotation_invoice_number, $suppliers, $order_numbers, $copy);
                    } else {
                        $role = 'supplier2';
                        $copy = 0;
                        CreateOrder::dispatch($orderPDF_delivery_date, $quotation_id, $form_type, $role, $product_titles, $color_titles, $model_titles, $feature_sub_titles, $sub_titles, $date, $client, $user, json_encode($request->all()), $quotation_invoice_number, $suppliers, $order_numbers, $copy);
                    }
                }
            }
        } else {
            $filename = $invoice_number . '.pdf';

            ini_set('max_execution_time', 180);

            $date = $invoice->document_date;
            $role = 'invoice1';

            if (!$request->negative_invoice) {
                $retailer_invoices_folder_path = public_path() . '/assets/newInvoices/' . $organization_id;

                if (!file_exists($retailer_invoices_folder_path)) {
                    mkdir($retailer_invoices_folder_path, 0775, true);
                }

                $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 'form_type', 'role', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
                $file = $retailer_invoices_folder_path . '/' . $filename;
                $pdf->save($file);

                if (!$ajax_request) {
                    Session::flash('success', __('text.Invoice has been updated successfully.'));
                }
            } else {
                $retailer_negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $organization_id;

                if (!file_exists($retailer_negative_invoices_folder_path)) {
                    mkdir($retailer_negative_invoices_folder_path, 0775, true);
                }

                $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 'form_type', 'role', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
                $file = $retailer_negative_invoices_folder_path . '/' . $filename;
                $pdf->save($file);

                if (!$ajax_request) {
                    if ($request->negative_invoice_id) {
                        Session::flash('success', __('text.Negative Invoice has been updated successfully.'));
                    } else {
                        Session::flash('success', __('text.Negative Invoice has been created successfully.'));
                    }
                }
            }
        }

        if (!$request->is_invoice) {
            if ($ajax_request) {
                $response[2] = $request->quotation_id ? $request->quotation_id : $invoice->id;
                return $response;
            } else {
                return redirect()->route('customer-quotations');
            }
        } else {
            if ($ajax_request) {
                $response[2] = $request->negative_invoice ? $invoice->id : $request->quotation_id;
                return $response;
            } else {
                return redirect()->route('customer-invoices');
            }
        }
    }

    public function RetailerGeneralTerms()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $uer_role = $user->role_id;
        // $main_id = $user->main_id;

        if ($uer_role != 2 || !$user->can('retailer-general-terms')) {
            return redirect()->route('user-login');
        }

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $general_terms = retailer_general_terms::whereIn("retailer_id", $related_users)->where("type", 1)->first();
        $general_terms_invoice = retailer_general_terms::whereIn("retailer_id", $related_users)->where("type", 2)->first();

        return view('user.retailer_general_terms', compact('general_terms', 'general_terms_invoice'));
    }

    public function RetailerGeneralTermsPost(Request $request)
    {
        $id = $request->id;
        $invoice_terms_id = $request->invoice_terms_id;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($id) {
            retailer_general_terms::whereIn('retailer_id', $related_users)->where("type", 1)->update(['description' => $request->description]);
        } else {
            $post = new retailer_general_terms;
            $post->retailer_id = $user_id;
            $post->description = $request->description;
            $post->type = 1;
            $post->save();
        }

        if ($invoice_terms_id) {
            retailer_general_terms::whereIn('retailer_id', $related_users)->where("type", 2)->update(['description' => $request->invoice_terms_description]);
        } else {
            $post = new retailer_general_terms;
            $post->retailer_id = $user_id;
            $post->description = $request->invoice_terms_description;
            $post->type = 2;
            $post->save();
        }

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function EmailTemplates()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $uer_role = $user->role_id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($uer_role != 2) {
            return redirect()->route('user-login');
        }

        $quotation_email_template = email_templates::whereIn('user_id', $related_users)->where('type', 'quotation')->first();
        $order_email_template = email_templates::whereIn('user_id', $related_users)->where('type', 'order')->first();
        $invoice_email_template = email_templates::whereIn('user_id', $related_users)->where('type', 'invoice')->first();

        return view('user.email_templates', compact('quotation_email_template', 'order_email_template', 'invoice_email_template'));
    }

    public function SaveEmailTemplate(Request $request)
    {
        $type = $request->type == 1 ? 'quotation' : ($request->type == 2 ? 'order' : 'invoice');
        $template_id = $request->template_id;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if ($template_id) {
            email_templates::whereIn('user_id', $related_users)->where('type', $type)->update(['subject' => $request->mail_subject, 'body' => $request->mail_body]);

            Session::flash('success', __('text.Email template updated successfully!'));
            return redirect()->back();
        } else {
            $post = new email_templates;
            $post->type = $type;
            $post->subject = $request->mail_subject;
            $post->body = $request->mail_body;
            $post->user_id = $user_id;
            $post->save();

            Session::flash('success', __('text.Email template saved successfully!'));
            return redirect()->back();
        }
    }

    public function StoreQuotation(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user_name = $user->name;
        $counter = $user->counter;

        $name = \Route::currentRouteName();

        $services = $request->item;

        if ($name == 'store-quotation') {

            $quote = quotes::where('id', $request->quote_id)->first();
            $quote->status = 1;
            $quote->save();

            $requested_quote_number = $quote->quote_number;
            $quotation_invoice_number = $user->quotation_client_id ? date("Y") . "-" . sprintf('%04u', $organization_id) . '-' . sprintf('%06u', $counter) : date("Y") . '-' . sprintf('%06u', $counter);

            $invoice = new quotation_invoices;
            $invoice->quote_id = $request->quote_id;
            $invoice->quotation_invoice_number = $quotation_invoice_number;
            $invoice->handyman_id = $user_id;
            $invoice->vat_percentage = $request->vat_percentage;
            $invoice->tax = str_replace(",", ".", $request->tax_amount);
            $invoice->subtotal = str_replace(",", ".", $request->sub_total);
            $invoice->grand_total = str_replace(",", ".", $request->grand_total);
            $invoice->description = $request->other_info;
            $invoice->delivery_date = $quote->quote_delivery;
            $invoice->save();

            foreach ($services as $i => $key) {

                $invoice_items = new quotation_invoices_data;
                $invoice_items->quotation_id = $invoice->id;
                $invoice_items->s_i_id = (int)$key;
                $invoice_items->service = $request->service_title[$i];
                $invoice_items->product_title = $request->productInput[$i];

                if (strpos($services[$i], 'I') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->item = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } elseif (strpos($services[$i], 'S') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->is_service = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } else {

                    $invoice_items->b_i_id = (int)$request->brand[$i];
                    $invoice_items->m_i_id = (int)$request->model[$i];
                    $invoice_items->brand = $request->brand_title[$i];
                    $invoice_items->model = $request->model_title[$i];
                }

                $invoice_items->rate = str_replace(",", ".", $request->cost[$i]);
                $invoice_items->qty = str_replace(",", ".", $request->qty[$i]);
                $invoice_items->description = $request->description[$i];
                $invoice_items->estimated_date = $request->date;
                $invoice_items->amount = str_replace(",", ".", $request->amount[$i]);
                $invoice_items->save();
            }


            $counter = $counter + 1;
            $user = User::where('id', $user_id)->first();
            $user->organization->update(['counter' => $counter]);

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/quotationsPDF/' . $filename;

            $type = 'new';

            $handyman_role = 1;

            if (!file_exists($file)) {

                ini_set('max_execution_time', 180);

                $pdf = PDF::loadView('user.pdf_quotation', compact('quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                $pdf->save(public_path() . '/assets/quotationsPDF/' . $filename);
            }

            $file1 = public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename;

            if (!file_exists($file1)) {

                $pdf = PDF::loadView('user.pdf_quotation', compact('handyman_role', 'quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                $pdf->save(public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename);
            }

            $admin_email = $this->sl->admin_email;

            \Mail::send(
                'user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $admin_email, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com');
                    $message->to($admin_email)->subject(__('text.Quotation Created!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );


            Session::flash('success', __('text.Quotation has been created successfully!'));
            return redirect()->route('handyman-quotation-requests');
        } elseif ($name == 'update-quotation') {

            $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->where('quotes.id', $request->quote_id)->select('quotes.*', 'categories.cat_name')->first();

            $quotation = quotation_invoices::where('quote_id', $request->quote_id)->whereIn('handyman_id', $related_users)->first();
            $quotation->ask_customization = 0;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->subtotal = str_replace(",", ".", $request->sub_total);
            $quotation->tax = str_replace(",", ".", $request->tax_amount);
            $quotation->grand_total = str_replace(",", ".", $request->grand_total);
            $quotation->description = $request->other_info;
            $quotation->save();

            quotation_invoices_data::where('quotation_id', $quotation->id)->delete();

            foreach ($services as $i => $key) {

                $item = new quotation_invoices_data;
                $item->quotation_id = $quotation->id;
                $item->s_i_id = (int)$key;
                $item->service = $request->service_title[$i];
                $item->product_title = $request->productInput[$i];

                if (strpos($services[$i], 'I') > -1) {

                    $item->b_i_id = 0;
                    $item->m_i_id = 0;
                    $item->item = 1;
                    $item->brand = '';
                    $item->model = '';
                } elseif (strpos($services[$i], 'S') > -1) {

                    $item->b_i_id = 0;
                    $item->m_i_id = 0;
                    $item->is_service = 1;
                    $item->brand = '';
                    $item->model = '';
                } else {

                    $item->b_i_id = (int)$request->brand[$i];
                    $item->m_i_id = (int)$request->model[$i];
                    $item->brand = $request->brand_title[$i];
                    $item->model = $request->model_title[$i];
                }

                $item->rate = str_replace(",", ".", $request->cost[$i]);
                $item->qty = str_replace(",", ".", $request->qty[$i]);
                $item->description = $request->description[$i];
                $item->estimated_date = $request->date;
                $item->amount = str_replace(",", ".", $request->amount[$i]);
                $item->save();
            }

            $requested_quote_number = $quote->quote_number;

            $quotation_invoice_number = $quotation->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/quotationsPDF/' . $filename;

            $type = 'edit';

            $handyman_role = 1;

            ini_set('max_execution_time', 180);

            $pdf = PDF::loadView('user.pdf_quotation', compact('quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/quotationsPDF/' . $filename);

            $file1 = public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename;

            $pdf = PDF::loadView('user.pdf_quotation', compact('handyman_role', 'quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename);

            $client_name = $quote->quote_name;
            $client_email = $quote->quote_email;

            $type = 'edit client';

            \Mail::send(
                'user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject(__('text.Quotation Edited!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            $admin_email = $this->sl->admin_email;
            $type = 'edit';

            \Mail::send(
                'user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $admin_email, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com');
                    $message->to($admin_email)->subject(__('text.Quotation Edited!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            Session::flash('success', __('text.Quotation has been edited and sent to client successfully!'));
            return redirect()->route('handyman-quotation-requests');
        } else {

            $quote = quotes::leftjoin('categories', 'categories.id', '=', 'quotes.quote_service')->where('quotes.id', $request->quote_id)->select('quotes.*', 'categories.cat_name')->first();

            $quote->status = 3;
            $quote->save();

            $quotation = quotation_invoices::where('quote_id', $request->quote_id)->whereIn('handyman_id', $related_users)->first();
            $quotation->ask_customization = 0;
            $quotation->invoice = 1;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->subtotal = str_replace(",", ".", $request->sub_total);
            $quotation->tax = str_replace(",", ".", $request->tax_amount);
            $quotation->grand_total = str_replace(",", ".", $request->grand_total);
            $quotation->description = $request->other_info;
            $quotation->save();

            quotation_invoices_data::where('quotation_id', $quotation->id)->delete();

            foreach ($services as $i => $key) {

                if (strpos($services[$i], 'I') > -1) {
                    $x = 1;
                } else {
                    $x = 0;
                }

                $item = new quotation_invoices_data;
                $item->quotation_id = $quotation->id;
                $item->s_i_id = (int)$key;
                $item->item = $x;
                $item->service = $request->service_title[$i];
                $item->rate = str_replace(",", ".", $request->cost[$i]);
                $item->qty = str_replace(",", ".", $request->qty[$i]);
                $item->description = $request->description[$i];
                $item->estimated_date = $request->date;
                $item->amount = str_replace(",", ".", $request->amount[$i]);
                $item->save();
            }

            $requested_quote_number = $quote->quote_number;

            $quotation_invoice_number = $quotation->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/quotationsPDF/' . $filename;

            $type = 'invoice';

            $handyman_role = 1;

            ini_set('max_execution_time', 180);

            $pdf = PDF::loadView('user.pdf_quotation', compact('quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/quotationsPDF/' . $filename);

            $file1 = public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename;

            $pdf = PDF::loadView('user.pdf_quotation', compact('handyman_role', 'quote', 'type', 'request', 'quotation_invoice_number', 'requested_quote_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/quotationsPDF/HandymanQuotations/' . $filename);

            $client_name = $quote->quote_name;
            $client_email = $quote->quote_email;

            $type = 'invoice client';

            \Mail::send(
                'user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject('Invoice Generated!');

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            $admin_email = $this->sl->admin_email;
            $type = 'invoice';

            \Mail::send(
                'user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $admin_email, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com');
                    $message->to($admin_email)->subject(__('text.Invoice Generated!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            Session::flash('success', 'Invoice has been generated successfully!');
            return redirect()->route('handyman-quotation-requests');
        }
    }

    public function SendCustomQuotation($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        if ($user->can('send-custom-quotation')) {
            // if($main_id)
            // {
            //     $user = User::where('id',$main_id)->first();
            //     $user_id = $user->id;
            // }

            $organization_id = $user->organization->id;
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $user_name = $user->name;
            $user_email = $user->email;
            $company_name = $user->company_name;

            $result = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.user_id')
                ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
                ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
                ->where('custom_quotations.id', $id)
                ->select('organizations.company_name', 'users.id', 'users.name', 'users.family_name', 'users.email', 'custom_quotations.*')->first();
            $result->approved = 1;
            $result->status = 1;
            $result->save();

            $quotation_invoice_number = $result->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/customQuotations/' . $filename;

            $type = 'new';

            $client_email = $result->email;
            $client_name = $result->name;

            \Mail::send(
                'user.custom_quotation_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'company_name' => $company_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $user_email, $user_name, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject(__('text.Quotation Created!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            Session::flash('success', __('text.Quotation has been sent to customer'));
            return redirect()->route('customer-quotations');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function SendQuotationAdmin($id)
    {
        $quotation = new_quotations::where('id', $id)->first();
        $quotation->admin_quotation_sent = 1;
        $quotation->save();

        $admin_email = $this->sl->admin_email;

        if ($this->lang->lang == 'du') {
            $msg = "Nieuwe update: nieuwe offerte QUO# <b>" . $quotation->quotation_invoice_number . "</b> is ter goedkeuring verstuurd.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
            $sub_mail = "Offerte ter goedkeuring";
        } else {
            $msg = "Recent activity: New quotation QUO# <b>" . $quotation->quotation_invoice_number . "</b> has been submitted for approval.<br><br>Kind regards,<br><br>Customer service<br><br> Pieppiep";
            $sub_mail = "Quotation waiting for approval";
        }

        \Mail::send(array(), array(), function ($message) use ($msg, $sub_mail, $admin_email, $quotation) {
            $message->to($admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                ->subject($sub_mail)
                ->html($msg, 'text/html');
        });

        Session::flash('success', 'Quotation submitted for approval.');
        return redirect()->back();
    }

    public function Actual()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $general_ledgers = general_ledgers::whereIn("user_id", $related_users)->get();

        $other_payments = other_payments::leftjoin("general_ledgers", "general_ledgers.id", "=", "other_payments.general_ledger")->whereIn("other_payments.user_id", $related_users)->select("other_payments.*", "general_ledgers.title", "general_ledgers.number")->get();

        $quotations = new_quotations::leftjoin("customers_details", "customers_details.id", "=", "new_quotations.customer_details")->whereIn("new_quotations.creator_id", $related_users)->select("new_quotations.*", "customers_details.name", "customers_details.family_name")->get();
        $invoices = all_invoices::leftjoin("customers_details", "customers_details.id", "=", "new_invoices.customer_details")->whereIn("new_invoices.creator_id", $related_users)->select("new_invoices.*", "customers_details.name", "customers_details.family_name")->get();
        $data = $quotations->concat($invoices)->sortByDesc('created_at');

        // $quotation_payment_calculations = payment_calculations::leftjoin("new_quotations","new_quotations.id","=","payment_calculations.quotation_id")->leftjoin("customers_details","customers_details.id","=","new_quotations.customer_details")->where("new_quotations.creator_id",$user_id)->where("payment_calculations.paid_by","!=","Pending")->select("payment_calculations.*","new_quotations.quotation_invoice_number","customers_details.name","customers_details.family_name")->get();
        // $invoice_payment_calculations = invoice_payment_calculations::leftjoin("new_invoices","new_invoices.id","=","invoice_payment_calculations.invoice_id")->leftjoin("customers_details","customers_details.id","=","new_invoices.customer_details")->where("new_invoices.creator_id",$user_id)->where("invoice_payment_calculations.paid_by","!=","Pending")->select("invoice_payment_calculations.*","new_invoices.invoice_number","customers_details.name","customers_details.family_name")->get();
        // $payment_calculations = $quotation_payment_calculations->concat($invoice_payment_calculations);

        return view("user.actual", compact("data", "general_ledgers", "other_payments"));
    }

    public function SubmitPayment(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $pc_amount = str_replace(',', '.', $request->pc_amount);

        if ($request->invoice_type == 1) {
            if (strpos($request->quotation_invoice_id, 'Q')) {
                $id = trim($request->quotation_invoice_id, 'Q');
                $check = new_quotations::where("id", $id)->first();
                $check->payment_total_percentage = str_replace(',', '.', str_replace('.', '', $check->payment_total_percentage));
                $check->payment_total_amount = str_replace(',', '.', str_replace('.', '', $check->payment_total_amount));

                if ($request->payment_id) {
                    $post = payment_calculations::where("id", $request->payment_id)->first();
                    $check->payment_total_percentage = $check->payment_total_percentage - $post->percentage;
                    $check->payment_total_amount = $check->payment_total_amount - $post->amount;
                } else {
                    $post = new payment_calculations;
                    $post->quotation_id = $id;
                }
            } else {
                $id = trim($request->quotation_invoice_id, 'I');
                $check = all_invoices::where("id", $id)->first();
                $check->payment_total_percentage = str_replace(',', '.', str_replace('.', '', $check->payment_total_percentage));
                $check->payment_total_amount = str_replace(',', '.', str_replace('.', '', $check->payment_total_amount));

                if ($request->payment_id) {
                    $post = invoice_payment_calculations::where("id", $request->payment_id)->first();
                    $check->payment_total_percentage = $check->payment_total_percentage - $post->percentage;
                    $check->payment_total_amount = $check->payment_total_amount - $post->amount;
                } else {
                    $post = new invoice_payment_calculations;
                    $post->invoice_id = $id;
                }
            }

            $grand_total = $check->grand_total;
            $percentage = $grand_total == 0 ? 0 : ($pc_amount / $grand_total) * 100;
            $post->percentage = $percentage;
            $post->amount = $pc_amount;
            $post->date = date("Y-m-d", strtotime($request->pc_date));
            $post->paid_by = $request->pc_paid_by;
            $post->description = $request->pc_description;
            $post->description1 = $request->pc_description1;
            $post->general_ledger = $request->general_ledger;
            $post->save();

            $total_percentage = $check->payment_total_percentage + $percentage;
            $total_amount = $check->payment_total_amount + $pc_amount;
            $check->payment_total_percentage = number_format((float)$total_percentage, 2, ',', '.');
            $check->payment_total_amount = number_format((float)$total_amount, 2, ',', '.');
            $check->save();

            if (strpos($request->quotation_invoice_id, 'Q')) {
                $data = new_quotations::leftjoin("customers_details", "customers_details.id", "=", "new_quotations.customer_details")->where("new_quotations.id", $id)->select("new_quotations.*", "customers_details.name", "customers_details.family_name")->first();
            } else {
                $data = all_invoices::leftjoin("customers_details", "customers_details.id", "=", "new_invoices.customer_details")->where("new_invoices.id", $id)->select("new_invoices.*", "customers_details.name", "customers_details.family_name")->first();
            }

            $response["payment_id"] = $post->id;
            $response["payment_type"] = "A";
            $response["date_sort"] = strtotime($request->pc_date) + $post->id;
            $response["amount"] = "€ " . number_format((float)$post->amount, 2, ',', '.');
            $response["description"] = ($data->name ? $data->name . ($data->family_name ? " " . $data->family_name : "") . ", " : "") . ($data->getTable() == 'new_quotations' ? "QUO# " . $data->quotation_invoice_number : "INV# " . $data->invoice_number);
        } else {
            $post = $request->payment_id ? other_payments::where("id", $request->payment_id)->first() : new other_payments;
            $post->user_id = $user_id;
            $post->amount = $pc_amount;
            $post->date = date("Y-m-d", strtotime($request->pc_date));
            $post->paid_by = $request->pc_paid_by;
            $post->invoice_type = $request->invoice_type;
            $post->description1 = $request->pc_description1;
            $post->general_ledger = $request->general_ledger;
            $post->save();

            $response["payment_id"] = $post->id;
            $response["payment_type"] = "B";
            $response["date_sort"] = strtotime($request->pc_date) + $post->id;
            $response["amount"] = "€ " . number_format((float)$post->amount, 2, ',', '.');
            $response["description"] = $post->description1 . ($post->title ? ($post->description1 ? ", " . $post->title : $post->title) : "");
        }

        return $response;
    }

    public function DeletePayment(Request $request)
    {
        $type = $request->type;
        $payment_id = $request->payment_id;

        if ($type == 1) {
            $quotation = $request->quotation;

            if ($quotation) {
                $payment = payment_calculations::where("id", $payment_id)->first();
                $check = new_quotations::where("id", $payment->quotation_id)->first();
                payment_calculations::where("id", $payment_id)->delete();
            } else {
                $payment = invoice_payment_calculations::where("id", $payment_id)->first();
                $check = all_invoices::where("id", $payment->invoice_id)->first();
                invoice_payment_calculations::where("id", $payment_id)->delete();
            }

            $check->payment_total_percentage = str_replace(',', '.', str_replace('.', '', $check->payment_total_percentage));
            $check->payment_total_amount = str_replace(',', '.', str_replace('.', '', $check->payment_total_amount));

            $total_percentage = $check->payment_total_percentage - $payment->percentage;
            $total_amount = $check->payment_total_amount - $payment->amount;
            $check->payment_total_percentage = number_format((float)$total_percentage, 2, ',', '.');
            $check->payment_total_amount = number_format((float)$total_amount, 2, ',', '.');
            $check->save();
        } else {
            other_payments::where("id", $payment_id)->delete();
        }

        return true;
    }

    public function GeneralCategories()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where("id",$main_id)->first();
        //     $user_id = $user->id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        // $suppliers = User::leftjoin('retailers_requests','retailers_requests.supplier_id','=','users.id')->where('retailers_requests.retailer_id',$user_id)->where('retailers_requests.status',1)->where('retailers_requests.active',1)->pluck('supplier_id');
        // $categories = Category::leftjoin('supplier_categories','supplier_categories.category_id','=','categories.id')->whereIn('supplier_categories.user_id',$suppliers)->select('categories.cat_name')->get();

        $categories = Category::get();

        $general_ledgers = general_ledgers::whereIn("user_id", $related_users)->get();

        $retailer_ledgers = retailer_subcategories_ledgers::whereIn("user_id", $related_users)->get();

        return view("user.general_categories", compact("organization", "categories", "general_ledgers", "retailer_ledgers"));
    }

    public function SaveGeneralCategories(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where("id",$main_id)->first();
        //     $user_id = $user->id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $organization->update(["service_general_ledger" => $request->service_general_ledger]);

        $sub_categories = $request->sub_ids;
        $ledger_ids = $request->general_ledgers;

        foreach ($sub_categories as $i => $key) {
            $ledger = retailer_subcategories_ledgers::where("sub_id", $key)->whereIn("user_id", $related_users)->first();

            if ($ledger_ids[$i]) {
                if (!$ledger) {
                    $ledger = new retailer_subcategories_ledgers;
                    $ledger->user_id = $user->id;
                    $ledger->sub_id = $key;
                }

                $ledger->ledger_id = $ledger_ids[$i];
                $ledger->save();
            } else {
                if ($ledger) {
                    $ledger->delete();
                }
            }
        }

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function GeneralLedgers()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $general_ledgers = general_ledgers::whereIn("user_id", $related_users)->get();

        return view('user.general_ledgers', compact('general_ledgers'));
    }

    public function CreateLedger()
    {
        return view('user.create_ledger');
    }

    public function EditLedger($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $ledger = general_ledgers::where("id", $id)->whereIn("user_id", $related_users)->first();

        if (!$ledger) {
            return redirect()->route('general-ledgers');
        }

        return view('user.create_ledger', compact('ledger'));
    }

    public function DeleteLedger($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $ledger = general_ledgers::where("id", $id)->whereIn("user_id", $related_users)->first();

        if (!$ledger) {
            return redirect()->route('general-ledgers');
        }

        $ledger->delete();

        Session::flash('success', __('text.General Ledger deleted successfully.'));
        return redirect()->route('general-ledgers');
    }

    public function SaveGeneralLedgers(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $main_id = $user_id;
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $check = general_ledgers::where("number", $request->number)->whereIn("user_id", $related_users)->first();

        if ($check) {
            Session::flash('unsuccess', __('text.Ledger number already taken!'));
            return redirect()->back();
        }

        if ($request->ledger_id) {
            $post = general_ledgers::where("id", $request->ledger_id)->first();

            Session::flash('success', __('text.General Ledger updated successfully.'));
        } else {
            $post = new general_ledgers;
            $post->user_id = $user_id;

            Session::flash('success', __('text.General Ledger created successfully.'));
        }

        $post->title = $request->title;
        $post->number = $request->number;
        $post->save();

        return redirect()->route("general-ledgers");
    }

    public function PaymentAccounts()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $general_ledgers = general_ledgers::whereIn("user_id", $related_users)->get();

        $payment_accounts = payment_accounts::whereIn("user_id", $related_users)->get();

        return view("user.payment_accounts", compact("payment_accounts", "general_ledgers"));
    }

    public function StorePaymentAccounts(Request $request)
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

        foreach ($request->payment_accounts as $i => $key) {
            $post = payment_accounts::whereIn("user_id", $related_users)->where("title", $key)->first();

            if (!$post) {
                $post = new payment_accounts;
                $post->user_id = $user_id;
            }

            $post->title = $key;
            $post->reeleezee_title = $request->reeleezee_payment_accounts[$i];
            // $post->ledger_id = $request->general_ledgers[$i];
            $post->save();
        }

        Session::flash('success', __('text.Payment accounts updated successfully.'));
        return redirect()->back();
    }

    public function SendNewQuotation(Request $request)
    {
        $ccs = array_values(array_filter($request->mail_cc));
        // $encoded_ccs = json_encode($ccs);
        $encoded_ccs = count($ccs) == 0 ? NULL : implode(",", $ccs);

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $ccs[] = $user->organization->email;

        $mail_to = $request->mail_to;
        $subject = $request->mail_subject;
        $mail_body = $request->mail_body;

        $draft = email_drafts::where('quotation_id', $request->quotation_id3)->where("type", "quotation")->first();

        if (!$draft) {
            $draft = new email_drafts;
            $response = array("response" => __('text.Draft saved'));
        } else {
            $response = array("response" => __('text.Draft updated'));
        }

        $draft->mail_to = $mail_to;
        $draft->ccs = $encoded_ccs;
        $draft->type = "quotation";
        $draft->subject = $subject;
        $draft->body = $mail_body;
        $draft->quotation_id = $request->quotation_id3;
        $draft->save();

        if ($request->draft) {
            return $response;
        }

        $check = new_quotations::where('id', $request->quotation_id3)->whereIn('creator_id', $related_users)->first();

        if ($check) {
            $user_name = $user->name;
            $user_email = $user->email;
            $company_name = $user->company_name;

            $result = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.user_id')
                ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'new_quotations.creator_id')
                ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
                ->where('new_quotations.id', $request->quotation_id3)
                ->select('new_quotations.*', 'organizations.company_name', 'users.id', 'users.name', 'users.family_name', 'users.email')->first();

            $quotation_invoice_number = $result->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/newQuotations/' . $organization_id . '/' . $filename;

            $type = 'new';

            $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.user_id', $result->id)->whereIn('customers_details.retailer_id', $related_users)->select('customers_details.*', 'users.email', 'users.fake_email', 'users.temp_password')->first();

            if ($result->id && !$client->fake_email && !$client->first_quote) {
                $link = url('/') . '/aanbieder/client-new-quotations';
                $client_name = $client->name;
                $client_email = $client->email;
                $org_password = $client->temp_password;

                if ($this->lang->lang == 'du') {
                    $msg = "Beste $client_name,<br><br>Er is een account voor je gecreëerd door " . $company_name . ". Hier kan je offertes bekijken, verzoek tot aanpassen of de offerte accepteren. <a href='" . $link . "'>Klik hier</a>, om je naar je persoonlijke dashboard te gaan.<br><br><b>Wachtwoord:</b><br><br>Je wachtwoord is: " . $org_password . "<br><br>Met vriendelijke groeten,<br><br>$user_name<br><br>Klantenservice<br><br>$company_name";
                } else {
                    $msg = "Dear Mr/Mrs " . $client_name . ",<br><br>Your account has been created by retailer " . $user_name . " for quotations. Kindly complete your profile and change your password. You can go to your dashboard through <a href='" . $link . "'>here.</a><br><br>Your Password: " . $org_password . "<br><br>Kind regards,<br><br>$user_name<br><br>Klantenservice<br><br>$company_name";
                }

                \Mail::send(array(), array(), function ($message) use ($msg, $user_email, $client_email, $company_name, $check) {
                    $message->to($client_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : ($check->quote_request_id ? 'info@vloerofferte.nl' : 'info@pieppiep.com'), $company_name)
                        ->replyTo($user_email, $company_name)
                        ->subject(__('text.Account Created!'))
                        ->html($msg, 'text/html');
                });

                User::where("id", $client->user_id)->update(["temp_password" => NULL, "allowed" => 1]);
                customers_details::where("id", $client->id)->update(["first_quote" => 1]);
            }

            $client_email = $result->email;
            $client_name = $result->name;

            // $images = $request->file('fileUpload') ? $request->file('fileUpload') : [];
            $documents = $request->file('myfiles') ? $request->file('myfiles') : [];

            \Mail::send(
                'user.global_mail',
                array(
                    'msg' => $mail_body,
                ),
                function ($message) use ($request, $mail_to, $subject, $mail_body, $file, $filename, $user_name, $user_email, $company_name, $ccs, $documents) {
                    $message->to($mail_to)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com', $company_name)
                        ->cc($ccs)
                        ->replyTo($user_email, $company_name)
                        ->subject($subject)
                        ->attach($file, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                        ]);
                    // if(count($images) > 0) {
                    //     foreach($images as $image) {
                    //         $message->attach($image->getRealPath(), array(
                    //             'as' => $image->getClientOriginalName(),
                    //             'mime' => $image->getMimeType())
                    //         );
                    //     }
                    // }
                    if (count($documents) > 0) {
                        foreach ($documents as $document) {
                            $message->attach(
                                $document->getRealPath(),
                                array(
                                    'as' => $document->getClientOriginalName(),
                                    'mime' => $document->getMimeType()
                                )
                            );
                        }
                    }
                }
            );

            if (!$check->accepted) {
                new_quotations::where('id', $request->quotation_id3)->update(['status' => 1, 'approved' => 1, 'mail_to' => $request->mail_to]);
            } else {
                new_quotations::where('id', $request->quotation_id3)->update(['mail_to' => $request->mail_to]);
            }

            $save_email = new saved_emails;
            $save_email->mail_to = $mail_to;
            $save_email->ccs = $encoded_ccs;
            $save_email->type = "quotation";
            $save_email->subject = $subject;
            $save_email->body = $mail_body;
            $save_email->quotation_id = $request->quotation_id3;
            $save_email->save();

            /*\Mail::send('user.custom_quotation_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'company_name' => $company_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ), function ($message) use ($file, $client_email, $user_email, $user_name, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com');
                    $message->to($client_email)->subject(__('text.Quotation Created!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);

                }
            );*/

            Session::flash('success', __('text.Quotation has been sent to customer'));

            if ($request->current_route == "create-custom-quotation") {
                return redirect()->route("view-new-quotation", ["id" => $request->quotation_id3]);
            } else {
                return redirect()->back();
            }
        } else {
            return redirect()->route('user-login');
        }
    }

    public function SendOrder(Request $request)
    {
        $ccs = array_values(array_filter($request->mail_cc));
        // $encoded_ccs = json_encode($ccs);
        $encoded_ccs = count($ccs) == 0 ? NULL : implode(",", $ccs);

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $ccs[] = $user->email;

        $subject = $request->mail_subject1;
        $msg = $request->mail_body1;

        $draft = email_drafts::where('quotation_id', $request->quotation_id1)->where("type", "order")->first();

        if (!$draft) {
            $draft = new email_drafts;
            $response = array("response" => __('text.Draft saved'));
        } else {
            $response = array("response" => __('text.Draft updated'));
        }

        $draft->type = "order";
        $draft->ccs = $encoded_ccs;
        $draft->subject = $subject;
        $draft->body = $msg;
        $draft->quotation_id = $request->quotation_id1;
        $draft->deliver_to = $request->deliver_to;
        $draft->delivery_date = $request->delivery_date;
        $draft->save();

        if ($request->draft) {
            return $response;
        }

        // $images = $request->file('fileUpload1');
        $check = new_quotations::where('id', $request->quotation_id1)->whereIn('creator_id', $related_users)->first();

        if ($check) {
            $check->processing = 1;
            $check->save();

            SendOrder::dispatch($request->quotation_id1, $user, $organization, $related_users, $request->mail_subject1, $request->mail_body1, $request->delivery_date, $request->deliver_to, $ccs, $encoded_ccs);

            Session::flash('success', __('text.Order will be sent to supplier(s) soon...'));
            return redirect()->route('customer-quotations');
        } else {
            return redirect()->route('user-login');
        }

        /*event(new \App\Events\SendOrder($id));*/
    }

    public function FetchCustomerQuotations(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $check = customers_details::whereIn("retailer_id", $related_users)->where("id", $request->id)->first();

        if (!$check) {
            return;
        } else {
            $quotations = new_quotations::where("customer_details", $request->id)->get();
            return $quotations;
        }
    }

    public function ViewDetails($id = NULL, Request $request)
    {
        $flag = 0;

        if (!$id) {
            $id = $request->id;
            $flag = 1;
        }

        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $suppliers = organizations::where('Type', '=', "Supplier")->whereHas('supplierRequests', function ($query) use ($organization_id) {
            $query->where('retailer_organization', $organization_id)->where('status', 1)->where('active', 1);
        })->orderBy('created_at', 'desc')->get();

        $invoices = new_quotations::leftjoin('new_quotations_data', 'new_quotations_data.quotation_id', '=', 'new_quotations.id')->leftjoin('items', 'items.id', '=', 'new_quotations_data.item_id')->leftjoin('services', 'services.id', '=', 'new_quotations_data.service_id')->where('new_quotations.id', $id)->whereIn('new_quotations.creator_id', $related_users)->where('new_quotations_data.product_id', 0)->select('new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_quotations_data.*', 'items.cat_name as item', 'services.title as service')->get();
        $new_invoices = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')
            ->leftjoin('organizations', 'organizations.id', '=', 'new_orders.supplier_id')
            ->leftjoin('products', 'products.id', '=', 'new_orders.product_id')
            ->leftjoin('product_models', 'product_models.id', '=', 'new_orders.model_id')
            ->leftjoin('colors', 'colors.id', '=', 'new_orders.color')
            ->where('new_quotations.id', $id)->whereIn('new_quotations.creator_id', $related_users)
            ->select('new_quotations.id as invoice_id', 'new_quotations.quotation_invoice_number', 'new_orders.*', 'organizations.live', 'organizations.company_name', 'products.title as product_title', 'product_models.model', 'colors.title as color_title')->get();

        $invoices = $invoices->concat($new_invoices);

        $rows = "";

        foreach ($invoices as $i => $item) {
            $description = "";

            if ($item->item_id) {
                $description = $item->item . ', Item';
            } elseif ($item->service_id) {
                $description = $item->service . ', Service';
            } else {
                $description = $item->product_id ? $item->product_title . ', ' . $item->model . ', ' . $item->color_title : $item->description;
            }

            $order_date = "";

            if (isset($item->item_id) || isset($item->service_id) || (isset($item->live) && !$item->live)) {
                $order_date = '<input style="border: 0;outline: none;width: 100%;" autocomplete="off" readonly value="' . ($item->order_date ? date('d-m-Y', strtotime($item->order_date)) : null) . '" type="text" class="order_date" name="order_dates[]">';
            } else {
                $order_date = $item->order_date ? date('d-m-Y', strtotime($item->order_date)) : null;
            }

            $ordered = "";

            if (isset($item->item_id) || isset($item->service_id) || (isset($item->live) && !$item->live)) {
                $ordered = '<input type="hidden" value="' . $item->id . '" name="data_id[]">
                    <input type="hidden" value="' . (isset($item->live) ? 0 : 1) . '" name="supplier_live[]">
                    <select class="form-control" name="order_sent[]">
                        <option ' . ($item->order_sent == 0 ? "selected" : null) . ' value="0">' . __('text.No') . '</option>
                        <option ' . ($item->order_sent == 1 ? "selected" : null) . ' value="1">' . __('text.Yes') . '</option>
                    </select>';
            } else {
                $ordered = '<input type="hidden" value="' . $item->id . '" name="order_id[]">' . ($item->order_sent ? __('text.Yes') : __('text.No'));
            }

            $delivery_date = "";

            if (isset($item->item_id) || isset($item->service_id) || (isset($item->live) && !$item->live)) {
                if (isset($item->live) && !$item->live) {
                    $delivery_date = '<input style="border: 0;outline: none;width: 100%;" autocomplete="off" readonly value="' . ($item->retailer_delivery_date ? date('d-m-Y', strtotime($item->retailer_delivery_date)) : null) . '" type="text" class="delivery_date dd" name="delivery_dates[]">';
                } else {
                    $delivery_date = '<input style="border: 0;outline: none;width: 100%;" autocomplete="off" readonly value="' . ($item->delivery_date ? date('d-m-Y', strtotime($item->delivery_date)) : null) . '" type="text" class="delivery_date dd" name="delivery_dates[]">';
                }
            } else {
                $delivery_date = '<input style="border: 0;outline: none;width: 100%;" autocomplete="off" readonly value="' . ($item->retailer_delivery_date ? date('d-m-Y', strtotime($item->retailer_delivery_date)) : null) . '" type="text" class="delivery_date dd" name="order_delivery_dates[]">';
            }

            $supplier_delivery_date = "";

            if (isset($item->item_id) || isset($item->service_id) || (isset($item->live) && !$item->live)) {
                if ((isset($item->live) && !$item->live) || ($item->id || (!$item->product_id && !$item->service_id))) {
                    $supplier_delivery_date = '<input style="border: 0;outline: none;width: 100%;" autocomplete="off" readonly value="' . ((isset($item->live) && !$item->live) ? ($item->delivery_date ? date('d-m-Y', strtotime($item->delivery_date)) : null) : ($item->supplier_delivery_date ? date('d-m-Y', strtotime($item->supplier_delivery_date)) : null)) . '" type="text" class="delivery_date" name="supplier_delivery_dates[]">';
                } else {
                    $supplier_delivery_date = '<input style="border: 0;outline: none;width: 100%;" readonly value="" type="text" name="supplier_delivery_dates[]">';
                }
            } else {
                $supplier_delivery_date = $item->delivery_date ? date('d-m-Y', strtotime($item->delivery_date)) : null;
            }

            $suppliers_data = '';

            if (isset($item->item_id) || isset($item->service_id)) {
                $suppliers_list = "";

                foreach ($suppliers as $key) {
                    $suppliers_list .= '<option ' . ($item->supplier_id == $key->id ? "selected" : null) . ' value="' . $key->id . '">' . $key->company_name . '</option>';
                }

                $suppliers_data = '<select class="form-control" name="suppliers[]"><option value="">' . __('text.Select Supplier') . '</option>' . $suppliers_list . '</select>';
            } else {
                if (isset($item->live) && !$item->live) {
                    $suppliers_data = '<input type="hidden" name="suppliers[]">';
                }

                $suppliers_data .= $item->company_name;
            }

            $rows .= '<tr class="active">
                <td>' . ($i + 1) . '</td>
                <td class="products">
                    ' . $description . '
                </td>
                <td>' . str_replace(".", ",", floatval($item->qty)) . '</td>
                <td>' . $order_date . '</td>
                <td>' . $ordered . '</td>
                <td style="padding: 0;">' . $delivery_date . '</td>
                <td>' . $supplier_delivery_date . '</td>
                <td>' . $suppliers_data . '</td>
            </tr>';
        }

        $quotation_id = count($invoices) ? $invoices[0]->invoice_id : "";
        $quotation_number = count($invoices) ? "Quo # " . $invoices[0]->quotation_invoice_number : "";

        if (!$flag) {
            return view('user.quotation_details', compact('suppliers', 'rows', 'quotation_id', 'quotation_number'));
        } else {
            return [$quotation_id, $quotation_number, $rows];
        }
    }

    public function UpdateDetails(Request $request)
    {
        $data_ids = $request->data_id;
        $order_ids = $request->order_id;

        if ($data_ids) {
            foreach ($data_ids as $x => $key) {
                if ($request->supplier_live[$x]) {
                    new_quotations_data::where('id', $key)->update(['supplier_id' => $request->suppliers[$x] ? $request->suppliers[$x] : 0, 'order_date' => $request->order_dates[$x], 'order_sent' => $request->order_sent[$x], 'delivery_date' => $request->delivery_dates[$x], 'supplier_delivery_date' => $request->supplier_delivery_dates[$x]]);
                } else {
                    new_orders::where('id', $key)->update(['order_date' => $request->order_dates[$x], 'order_sent' => $request->order_sent[$x], 'retailer_delivery_date' => $request->delivery_dates[$x], 'delivery_date' => $request->supplier_delivery_dates[$x]]);
                }
            }
        }

        if ($order_ids) {
            foreach ($order_ids as $x => $key) {
                new_orders::where('id', $key)->update(['retailer_delivery_date' => $request->order_delivery_dates[$x]]);
            }
        }

        Session::flash('success', 'Updated successfully.');
        return redirect()->back();
    }

    public function ChangeDeliveryDates($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $data = new_quotations::leftjoin('new_orders', 'new_orders.quotation_id', '=', 'new_quotations.id')->where('new_quotations.id', $id)->where('new_orders.deleted_at', NULL)->where('new_orders.supplier_id', $organization_id)->where('new_orders.processing', '!=', 1)->where('new_orders.delivered', '!=', 1)->select("new_quotations.*")->first();

        if ($data) {
            $invoice = new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->leftjoin('products', 'products.id', '=', 'new_orders.product_id')->leftjoin('product_models', 'product_models.id', '=', 'new_orders.model_id')->leftjoin('colors', 'colors.id', '=', 'new_orders.color')->where('new_quotations.id', $id)->where('new_quotations.deleted_at', NULL)->where('new_orders.supplier_id', $organization_id)->with("calculations")->select('colors.title as color_title', 'product_models.model', 'new_quotations.*', 'new_quotations.id as invoice_id', 'new_orders.approved', 'new_orders.delivery_days', 'new_orders.delivery_date', 'new_orders.retailer_delivery_date', 'new_orders.id', 'new_orders.supplier_id', 'new_orders.product_id', 'new_orders.color', 'new_orders.qty', 'new_orders.measure', 'products.title as product_title')->get();

            return view('user.change_delivery_date', compact('data', 'invoice'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function UpdateDeliveryDates(Request $request)
    {
        $rows = $request->data_id;
        $user = Auth::guard('user')->user();

        new_orders::whereIn('id', $rows)->update(['processing' => 1]);

        UpdateDates::dispatch($request->all(), $user);

        Session::flash('success', __('text.Processing...'));
        return redirect()->route('customer-quotations');
    }

    public function SupplierOrderDelivered($id)
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
        $supplier_company = $organization->company_name;
        $supplier_email = $organization->email;

        $data = new_quotations::leftjoin('new_orders', 'new_orders.quotation_id', '=', 'new_quotations.id')->where('new_quotations.id', $id)->where('new_orders.deleted_at', NULL)->where('new_orders.supplier_id', $organization_id)->where('new_orders.processing', '!=', 1)->where('new_orders.approved', 1)->where('new_orders.delivered', '!=', 1)->select('new_quotations.*', 'new_orders.order_number')->first();

        if ($data) {
            new_orders::leftjoin('new_quotations', 'new_quotations.id', '=', 'new_orders.quotation_id')->where('new_quotations.id', $id)->where('new_quotations.deleted_at', NULL)->where('new_orders.supplier_id', $organization_id)->update(['new_orders.delivered' => 1]);

            $delivered = new_orders::where('quotation_id', $id)->get();
            $flag = 0;

            foreach ($delivered as $key) {
                if (!$key->delivered) {
                    $flag = 1;
                }
            }

            if ($flag == 0) {
                new_quotations::where('id', $id)->update(['delivered' => 1]);
            }

            $retailer = User::where('id', $data->creator_id)->first()->organization;
            $retailer_company = $retailer->company_name;
            $retailer_email = $retailer->email;
            $order_number = $data->order_number;

            if ($this->lang->lang == 'du') {
                $msg = "Status update: Beste " . $retailer_company . ", goederen zijn geleverd door leverancier <b>" . $supplier_company . "</b><br> Order No: <b>" . $order_number . "</b>.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
                $sub_mail = "Order is gemarkeerd als bezorgd";
            } else {
                $msg = "Recent activity: Dear " . $retailer_company . ", order has been delivered by supplier <b>" . $supplier_company . "</b><br> Order No: <b>" . $order_number . "</b>.<br><br>Kind regards,<br><br>Customer service<br><br> Pieppiep";
                $sub_mail = "Order marked as delivered by supplier!";
            }

            \Mail::send(array(), array(), function ($message) use ($msg, $sub_mail, $retailer_email, $retailer_company, $supplier_company, $order_number, $supplier_email) {
                $message->to($retailer_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com', $supplier_company)
                    ->replyTo($supplier_email, $supplier_company)
                    ->subject($sub_mail)
                    ->html($msg, 'text/html');
            });

            Session::flash('success', __('text.Order marked as delivered.'));
            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function RetailerMarkDelivered($id)
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
        $retailer_company = $organization->company_name;
        $retailer_email = $organization->email;

        $data = new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->first();

        if ($data) {
            new_quotations::where('id', $id)->whereIn('creator_id', $related_users)->update(['retailer_delivered' => 1]);

            if ($data->quote_request_id) {
                $quote = quotes::where('id', $data->quote_request_id)->first();
                $client = new \stdClass();
                $client_name = $quote->quote_name . ' ' . $quote->quote_familyname;
                $client_email = $quote->quote_email;
            } else {
                $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $data->customer_details)->select('customers_details.*', 'users.email', 'users.fake_email')->first();
                $client_name = $client->name . ' ' . $client->family_name;
                $client_email = $client->fake_email ? $data->mail_to : $client->email;
            }

            $quotation_invoice_number = $data->quotation_invoice_number;

            if ($this->lang->lang == 'du') {
                $msg = "Beste " . $client_name . ",<br><br> Status update: goederen zijn afgeleverd. <b>" . $retailer_company . "Conform offerte <b>" . $quotation_invoice_number . "</b>.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br>$retailer_company";
                $sub_mail = "Status gewijzigd naar bezorgd";
            } else {
                $msg = "Recent activity: Hi " . $client_name . ", quotation has been delivered by retailer <b>" . $retailer_company . "</b><br> Quotation No: <b>" . $quotation_invoice_number . "</b>.<br><br>Kind regards,<br><br>Customer service<br><br> $retailer_company";
                $sub_mail = "Quotation marked as delivered by retailer!";
            }

            \Mail::send(array(), array(), function ($message) use ($msg, $sub_mail, $client_email, $retailer_company, $client_name, $quotation_invoice_number, $retailer_email) {
                $message->to($client_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : ($data->quote_request_id ? 'info@vloerofferte.nl' : 'noreply@pieppiep.com'), $retailer_company)
                    ->replyTo($retailer_email, $retailer_company)
                    ->subject($sub_mail)
                    ->html($msg, 'text/html');
            });

            Session::flash('success', 'Quotation marked as delivered.');
            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function SendInvoice(Request $request)
    {
        $ccs = array_values(array_filter($request->mail_cc));
        // $encoded_ccs = json_encode($ccs);
        $encoded_ccs = count($ccs) == 0 ? NULL : implode(",", $ccs);

        $user = Auth::guard('user')->user();
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $ccs[] = $user->email;

        $user_id = $user->id;
        $mail_to = $request->mail_to2;
        $subject = $request->mail_subject2;
        $msg = $request->mail_body2;

        $draft = email_drafts::where('quotation_id', $request->quotation_id2)->where("type", "invoice")->first();

        if (!$draft) {
            $draft = new email_drafts;
            $response = array("response" => __('text.Draft saved'));
        } else {
            $response = array("response" => __('text.Draft updated'));
        }

        $draft->mail_to = $mail_to;
        $draft->ccs = $encoded_ccs;
        $draft->type = "invoice";
        $draft->subject = $subject;
        $draft->body = $msg;
        $draft->quotation_id = $request->quotation_id2;
        $draft->save();

        if ($request->draft) {
            return $response;
        }

        $check = new_invoices::where('quotation_id', $request->quotation_id2)->whereIn('creator_id', $related_users)->where('invoice', 1)->first();

        if (!$check) {
            return redirect()->back();
        }

        new_quotations::where('id', $request->quotation_id2)->whereIn('creator_id', $related_users)->where('invoice', 1)->update(['invoice_sent' => 1]);
        $check->invoice_sent = 1;
        $check->save();

        if ($check->quote_request_id) {
            $quote = quotes::where('id', $check->quote_request_id)->first();
            $client = new \stdClass();
            $client_name = $quote->quote_name . ' ' . $quote->quote_familyname;
            $client_email = $quote->quote_email;
        } else {
            $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $check->customer_details)->select('customers_details.*', 'users.email')->first();
            $client_name = $client->name . ' ' . $client->family_name;
            $client_email = $client->email;
        }

        $creator_id = $check->creator_id;
        $company_name = $organization->company_name;
        $company_email = $organization->email;
        $invoice_number = $check->invoice_number;
        $filename = $invoice_number . '.pdf';
        $file = public_path('assets/newInvoices/' . $organization_id . '/' . $filename);

        // $images = $request->file('fileUpload') ? $request->file('fileUpload') : [];
        $documents = $request->file('myfiles') ? $request->file('myfiles') : [];

        \Mail::send(
            'user.global_mail',
            array(
                'msg' => $msg,
            ),
            function ($message) use ($request, $mail_to, $subject, $msg, $file, $filename, $company_name, $company_email, $check, $ccs, $documents) {
                $message->to($mail_to)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : ($check->quote_request_id ? 'info@vloerofferte.nl' : 'noreply@pieppiep.com'), $company_name)
                    ->cc($ccs)
                    ->replyTo($company_email, $company_name)
                    ->subject($subject)
                    ->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                // if(count($images) > 0) {
                //     foreach($images as $image) {
                //         $message->attach($image->getRealPath(), array(
                //             'as' => $image->getClientOriginalName(),
                //             'mime' => $image->getMimeType())
                //         );
                //     }
                // }
                if (count($documents) > 0) {
                    foreach ($documents as $document) {
                        $message->attach(
                            $document->getRealPath(),
                            array(
                                'as' => $document->getClientOriginalName(),
                                'mime' => $document->getMimeType()
                            )
                        );
                    }
                }
            }
        );

        new_quotations::where('id', $request->quotation_id2)->update(['mail_invoice_to' => $request->mail_to2]);
        new_invoices::where('quotation_id', $request->quotation_id2)->update(['mail_invoice_to' => $request->mail_to2]);

        $save_email = new saved_emails;
        $save_email->mail_to = $mail_to;
        $save_email->ccs = $encoded_ccs;
        $save_email->type = "invoice";
        $save_email->subject = $subject;
        $save_email->body = $msg;
        $save_email->quotation_id = $request->quotation_id2;
        $save_email->save();

        Session::flash("success", __('text.Invoice sent to customer successfully!'));
        return redirect()->back();
    }

    public function SendNegativeInvoice(Request $request)
    {
        $ccs = array_values(array_filter($request->mail_cc));
        // $encoded_ccs = json_encode($ccs);
        $encoded_ccs = count($ccs) == 0 ? NULL : implode(",", $ccs);

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
        $ccs[] = $user->email;

        $mail_to = $request->mail_to3;
        $subject = $request->mail_subject3;
        $msg = $request->mail_body3;

        $draft = email_drafts::where('quotation_id', $request->quotation_id3)->where("type", "negative-invoice")->first();

        if (!$draft) {
            $draft = new email_drafts;
            $response = array("response" => __('text.Draft saved'));
        } else {
            $response = array("response" => __('text.Draft updated'));
        }

        $draft->mail_to = $mail_to;
        $draft->ccs = $encoded_ccs;
        $draft->type = "negative-invoice";
        $draft->subject = $subject;
        $draft->body = $msg;
        $draft->quotation_id = $request->quotation_id3;
        $draft->save();

        if ($request->draft) {
            return $response;
        }

        $check = new_invoices::where('quotation_id', $request->quotation_id3)->whereIn('creator_id', $related_users)->first();

        if (!$check) {
            return redirect()->back();
        }

        //        new_quotations::where('id', $request->quotation_id3)->whereIn('creator_id',$related_users)->update(['negative_invoice_sent' => 1]);
        $check->negative_invoice_sent = 1;
        $check->save();

        $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $check->customer_details)->select('customers_details.*', 'users.email')->first();
        $client_name = $client->name . ' ' . $client->family_name;
        $client_email = $client->email;
        $company_name = $organization->company_name;
        $company_email = $organization->email;

        $invoice = new_negative_invoices::where('quotation_id', $request->quotation_id3)->whereIn('creator_id', $related_users)->first();
        $invoice_number = $invoice->invoice_number;
        $invoice->negative_invoice_sent = 1;
        $invoice->save();

        $creator_id = $invoice->creator_id;
        $retailer_negative_invoices_folder_path = public_path() . '/assets/newNegativeInvoices/' . $organization_id;

        if (!file_exists($retailer_negative_invoices_folder_path)) {
            mkdir($retailer_negative_invoices_folder_path, 0775, true);
        }

        $filename = $invoice_number . '.pdf';
        $file = $retailer_negative_invoices_folder_path . '/' . $filename;

        // $images = $request->file('fileUpload1') ? $request->file('fileUpload1') : [];
        $documents = $request->file('myfiles') ? $request->file('myfiles') : [];

        \Mail::send(
            'user.global_mail',
            array(
                'msg' => $msg,
            ),
            function ($message) use ($request, $mail_to, $subject, $msg, $file, $filename, $ccs, $documents, $company_name, $company_email) {
                $message->to($mail_to)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->cc($ccs)
                    ->replyTo($company_email, $company_name)
                    ->subject($subject)
                    ->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                // if(count($images) > 0) {
                //     foreach($images as $image) {
                //         $message->attach($image->getRealPath(), array(
                //             'as' => $image->getClientOriginalName(),
                //             'mime' => $image->getMimeType())
                //         );
                //     }
                // }
                if (count($documents) > 0) {
                    foreach ($documents as $document) {
                        $message->attach(
                            $document->getRealPath(),
                            array(
                                'as' => $document->getClientOriginalName(),
                                'mime' => $document->getMimeType()
                            )
                        );
                    }
                }
            }
        );

        //        new_quotations::where('id', $request->quotation_id3)->update(['mail_negative_invoice_to' => $request->mail_to3]);
        new_invoices::where('quotation_id', $request->quotation_id3)->update(['mail_negative_invoice_to' => $request->mail_to3]);
        new_negative_invoices::where('quotation_id', $request->quotation_id3)->update(['mail_negative_invoice_to' => $request->mail_to3]);

        $save_email = new saved_emails;
        $save_email->mail_to = $mail_to;
        $save_email->ccs = $encoded_ccs;
        $save_email->type = "negative-invoice";
        $save_email->subject = $subject;
        $save_email->body = $msg;
        $save_email->quotation_id = $request->quotation_id3;
        $save_email->save();

        Session::flash("success", __('text.Negative Invoice sent to customer successfully!'));
        return redirect()->back();
    }

    public function ReviewReasons()
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

        $review_reasons = new_quotations::whereIn("creator_id", $related_users)->where("review_text", "!=", NULL)->get();

        return view("user.messages_content", compact("review_reasons"));
    }

    public function Chat()
    {
        return view("user.chat");
    }

    // public function Notes()
    // {
    //     $user = Auth::guard('user')->user();
    //     $user_id = $user->id;
    //     $main_id = $user->main_id;

    //     // Define the relationship dynamically based on the main_id
    //     // $related_users = ($main_id) ? User::where('main_id', $main_id)->pluck('id')->toArray() : $user->subordinateUsers()->pluck('id')->toArray();

    //     // Include main user's own ID
    //     // $related_users[] = $user->id;

    //     // Retrieve all notes that belong to any of the user IDs in the list
    //     $notes = notes::whereIn('user_id', $related_users)->get();

    //     // Return the view with the notes
    //     return view("user.page_notes", ['notes' => $notes]);
    // }

    public function Notes()
    {
        return view("user.page_notes");
    }

    public function CustomerMessages()
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

        $customer_messages = client_quotation_msgs::leftjoin("new_quotations", "new_quotations.id", "=", "client_quotation_msgs.quotation_id")->where("new_quotations.deleted_at", NULL)->whereIn("new_quotations.creator_id", $related_users)->select("client_quotation_msgs.*", "new_quotations.quotation_invoice_number")->get();

        return view("user.messages_content", compact("customer_messages"));
    }

    public function SentMails($id = NULL)
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

        $quotations_sent_mails = saved_emails::leftjoin("new_quotations", "new_quotations.id", "=", "saved_emails.quotation_id")->where("new_quotations.deleted_at", NULL)->whereIn("new_quotations.creator_id", $related_users)->where("saved_emails.type", "quotation");
        $orders_sent_mails = saved_emails::leftjoin("new_quotations", "new_quotations.id", "=", "saved_emails.quotation_id")->leftjoin("new_orders", "new_orders.quotation_id", "=", "saved_emails.quotation_id")->where("new_orders.deleted_at", NULL)->whereIn("new_quotations.creator_id", $related_users)->where("saved_emails.type", "order");
        $invoices_sent_mails = saved_emails::leftjoin("new_quotations", "new_quotations.id", "=", "saved_emails.quotation_id")->leftjoin("new_invoices", "new_invoices.quotation_id", "=", "saved_emails.quotation_id")->where("new_invoices.deleted_at", NULL)->whereIn("new_quotations.creator_id", $related_users)->where("saved_emails.type", "invoice");

        if ($id) {
            $quotations_sent_mails = $quotations_sent_mails->where("new_quotations.id", $id);
            $orders_sent_mails = $orders_sent_mails->where("new_quotations.id", $id);
            $invoices_sent_mails = $invoices_sent_mails->where("new_quotations.id", $id);
        }

        $quotations_sent_mails = $quotations_sent_mails->select("saved_emails.*", "new_quotations.quotation_invoice_number")->get();
        $orders_sent_mails = $orders_sent_mails->select("saved_emails.*", "new_orders.order_number", "new_quotations.quotation_invoice_number")->get();
        $invoices_sent_mails = $invoices_sent_mails->select("saved_emails.*", "new_invoices.invoice_number", "new_quotations.quotation_invoice_number")->get();

        return view("user.messages_content", compact("quotations_sent_mails", "orders_sent_mails", "invoices_sent_mails"));
    }

    public function CreateNewInvoice($id)
    {
        $invoice_id = $id;
        $user = Auth::guard('user')->user();
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user->organization_email = $user->organization->email;

        $user_id = $user->id;
        $counter_invoice = $user->counter_invoice;
        $invoice_number = date("Y") . '-' . sprintf('%06u', $counter_invoice);
        $check_i_number = all_invoices::where('invoice_number', $invoice_number)->whereIn('creator_id', $related_users)->first();

        while ($check_i_number) {
            $counter_invoice = $counter_invoice + 1;
            $invoice_number = date("Y") . '-' . sprintf('%06u', $counter_invoice);
            $check_i_number = all_invoices::where('invoice_number', $invoice_number)->whereIn('creator_id', $related_users)->first();
        }

        $data = new_quotations::where('id', $invoice_id)->whereIn('creator_id', $related_users)->where('invoice', '!=', 1)->first();

        if ($data) {
            if ($data->quote_request_id) {
                $quote = quotes::where('id', $data->quote_request_id)->first();
                $client = new \stdClass();
                $client->address = $quote->quote_zipcode;
                $client->name = $quote->quote_name;
                $client->family_name = $quote->quote_familyname;
                $client->postcode = $quote->quote_postcode;
                $client->city = $quote->quote_city;
                $client->email = $quote->quote_email;
                $client->fake_email = 0;
            } else {
                $client = customers_details::leftjoin('users', 'users.id', '=', 'customers_details.user_id')->where('customers_details.id', $data->customer_details)->select('customers_details.*', 'users.email', 'users.fake_email')->first();
            }

            $general_terms = retailer_general_terms::whereIn("retailer_id", $related_users)->where("type", 2)->first();
            $general_terms = $general_terms ? $general_terms->description : NULL;

            $date = date("Y-m-d");
            $invoice_data = new_quotations::where('id', $data->id)->first();
            // $client_id = !$data->quote_request_id && $client->customer_number ? "-" . sprintf('%04u', $client->customer_number) : "";
            // $invoice_number = $user->invoice_client_id ? date("Y") . $client_id . '-' . sprintf('%06u', $counter_invoice) : date("Y") . '-' . sprintf('%06u', $counter_invoice);
            $invoice_data->quotation_id = $data->id;
            $invoice_data->invoice_number = $invoice_number;
            $invoice_data->invoice_date = $date;
            $invoice_data->document_date = date("Y-m-d");
            $invoice_data->invoice = 1;
            $invoice_data->general_terms = $general_terms;
            $new_invoice = $invoice_data->replicate($except = ['quotation_invoice_number', 'copied_from', 'draft_token', 'reeleezee_guid', 'reeleezee_exported_at', 'expire_date']);
            $new_invoice->setTable('new_invoices');
            $new_invoice->save();

            $payment_calculations_data = payment_calculations::where('quotation_id', $data->id)->get();

            foreach ($payment_calculations_data as $pc) {
                $pc->invoice_id = $new_invoice->id;
                $invoice_payment_calculations = $pc->replicate($except = ['quotation_id']);
                $invoice_payment_calculations->setTable('invoice_payment_calculations');
                $invoice_payment_calculations->save();
            }

            $request = new_quotations::where('id', $invoice_id)->select('new_quotations.*', 'new_quotations.subtotal as total_amount')->first();
            $request->products = new_quotations_data::where('quotation_id', $invoice_id)->get();
            $request->general_terms = $general_terms;

            $delivery_date = $request->delivery_date ? date('d-m-Y', strtotime($request->delivery_date)) . ' - ' . date('d-m-Y', strtotime($request->delivery_date_end)) : "";
            $installation_date = $request->installation_date ? date('d-m-Y', strtotime($request->installation_date)) . ' - ' . date('d-m-Y', strtotime($request->installation_date_end)) : "";

            $product_titles = array();
            $product_descriptions = array();
            $color_titles = array();
            $model_titles = array();
            $sub_titles = array();
            $qty = array();
            $width = array();
            $width_unit = array();
            $height = array();
            $height_unit = array();
            $comments = array();
            $delivery = array();
            $feature_sub_titles = array();
            $labor_impact = array();
            $discount = array();
            $rate = array();
            $labor_discount = array();
            $total = array();
            $total_discount = array();
            $price_before_labor = array();
            $vat_percentages = array();

            foreach ($request->products as $x => $temp) {
                $vat_percentages[] = vats::where('id', $temp->vat_id)->pluck('vat_percentage')->first();

                $temp->invoice_id = $new_invoice->id;
                $new_invoice_data = $temp->replicate($except = ['order_number', 'quotation_id']);
                $new_invoice_data->setTable('new_invoices_data');
                $new_invoice_data->save();

                $calculations = new_quotations_data_calculations::where('quotation_data_id', $temp->id)->get();

                foreach ($calculations as $cal) {
                    $cal->invoice_data_id = $new_invoice_data->id;
                    $new_invoice_data_calculation = $cal->replicate($except = ['quotation_data_id']);
                    $new_invoice_data_calculation->setTable('new_invoices_data_calculations');
                    $new_invoice_data_calculation->save();
                }

                $feature_sub_titles[$x][] = array();

                if ($temp->item_id != 0) {
                    $product_titles[] = items::where('id', $temp->item_id)->pluck('cat_name')->first();
                    $color_titles[] = '';
                    $model_titles[] = '';
                } elseif ($temp->service_id != 0) {
                    $product_titles[] = Service::where('id', $temp->service_id)->pluck('title')->first();
                    $color_titles[] = '';
                    $model_titles[] = '';
                } else {
                    if ($temp->product_id != 0) {
                        $product_titles[] = product::where('id', $temp->product_id)->pluck('title')->first();
                        $color_titles[] = colors::where('id', $temp->color)->pluck('title')->first();
                        $model_titles[] = product_models::where('id', $temp->model_id)->pluck('model')->first();
                    } else {
                        $product_titles[] = '';
                        $color_titles[] = '';
                        $model_titles[] = '';
                    }
                }

                $product_descriptions[] = $temp->description;
                $qty[] = $temp->qty;
                $width[] = $temp->width;
                $width_unit[] = $temp->width_unit;
                $height[] = $temp->height;
                $height_unit[] = $temp->height_unit;
                $delivery[] = $temp->delivery_date;
                $labor_impact[] = $temp->labor_impact;
                $discount[] = $temp->discount;
                $rate[] = $temp->rate;
                $labor_discount[] = $temp->labor_discount;
                $total[] = $temp->amount;
                $total_discount[] = $temp->total_discount;
                $price_before_labor[] = $temp->price_before_labor;

                $features = new_quotations_features::where('quotation_data_id', $temp->id)->get();

                foreach ($features as $f => $feature) {
                    $feature->invoice_data_id = $new_invoice_data->id;
                    $new_invoice_feature = $feature->replicate($except = ['quotation_data_id']);
                    $new_invoice_feature->setTable('new_invoices_features');
                    $new_invoice_feature->save();

                    if ($feature->feature_id == 0) {
                        if ($feature->ladderband) {
                            $sub_product = new_quotations_sub_products::where('feature_row_id', $feature->id)->get();

                            foreach ($sub_product as $sub) {
                                $sub->feature_row_id = $new_invoice_feature->id;
                                $new_invoice_sub_product = $sub->replicate();
                                $new_invoice_sub_product->setTable('new_invoices_sub_products');
                                $new_invoice_sub_product->save();

                                if ($sub->size1_value == 1 || $sub->size2_value == 1) {
                                    $sub_titles[$x] = product_ladderbands::where('product_id', $temp->product_id)->where('id', $sub->sub_product_id)->first();

                                    if ($sub->size1_value == 1) {
                                        $sub_titles[$x]->size = '38mm';
                                    } else {
                                        $sub_titles[$x]->size = '25mm';
                                    }
                                }
                            }
                        }
                    }

                    $feature_sub_titles[$x][] = product_features::leftjoin('features', 'features.id', '=', 'product_features.heading_id')->where('product_features.product_id', $temp->product_id)->where('product_features.id', $feature->feature_sub_id)->select('product_features.*', 'features.title as main_title', 'features.order_no', 'features.id as f_id')->first();
                    $comments[$x][] = $feature->comment;
                }
            }

            $request->qty = $qty;
            $request->width = $width;
            $request->width_unit = $width_unit;
            $request->height = $height;
            $request->height_unit = $height_unit;
            $request->delivery_date = $delivery;
            $request->labor_impact = $labor_impact;
            $request->price_before_labor = $price_before_labor;
            $request->discount = $discount;
            $request->rate = $rate;
            $request->labor_discount = $labor_discount;
            $request->total = $total;
            $request->total_discount = $total_discount;
            $request->price_before_labor_old = $price_before_labor;

            $creator_id = $new_invoice->creator_id;
            $retailer_invoices_folder_path = public_path() . '/assets/newInvoices/' . $organization_id;

            if (!file_exists($retailer_invoices_folder_path)) {
                mkdir($retailer_invoices_folder_path, 0775, true);
            }

            $quotation_invoice_number = $request->quotation_invoice_number;
            $order_number = $invoice_number;
            $filename = $order_number . '.pdf';
            $file = $retailer_invoices_folder_path . '/' . $filename;

            ini_set('max_execution_time', 180);

            $role = 'invoice';
            $form_type = $request->form_type;

            $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages', 'delivery_date', 'installation_date', 'form_type', 'role', 'comments', 'product_descriptions', 'product_titles', 'color_titles', 'model_titles', 'feature_sub_titles', 'sub_titles', 'date', 'client', 'user', 'request', 'quotation_invoice_number', 'order_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160, 'isRemoteEnabled' => true]);
            $pdf->save($file);

            $counter_invoice = $counter_invoice + 1;
            $organization->update(['counter_invoice' => $counter_invoice]);
            $data->invoice_number = $order_number;
            $data->invoice_date = $date;
            $data->invoice = 1;
            $data->save();

            Session::flash('success', __('text.Invoice created successfully!'));
            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function StoreNewNote(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $note_id = $request->note_id;
        $title = $request->title;
        $details = $request->details;
        $tag = $request->tag;
        $customer_id = $request->customer;
        $supplier_id = $request->supplier;
        $employee_id = $request->employee;

        if ($note_id) {
            $post = notes::where("id", $note_id)->first();
        } else {
            $post = new notes;
        }

        $post->user_id = $user_id;
        $post->customer_id = $customer_id;
        $post->supplier_id = $supplier_id;
        $post->employee_id = $employee_id;
        $post->title = $title;
        $post->details = $details;
        $post->tag = $tag;
        $post->save();

        $post->modified_created_at = date("l, d/m/Y H:i", strtotime($post->created_at));
        $tag = notes_tags::where("id", $tag)->first();
        $cs = "";

        if ($employee_id) {
            $employee = User::where("id", $employee_id)->first();
            $employee = $employee->name . ($employee->family_name ? " " . $employee->family_name : "");
        } else {
            $employee = "";
        }

        if ($supplier_id) {
            $supplier = User::where("id", $supplier_id)->first();
            $supplier = $supplier->name . ($supplier->family_name ? " " . $supplier->family_name : "");
            $cs = $supplier;
        }

        if ($customer_id) {
            $customer = customers_details::where("id", $customer_id)->first();
            $customer = $customer->name . ($customer->family_name ? " " . $customer->family_name : "");
            $cs = $customer;
        }

        $post->employee = $employee;
        $post->cs = $cs;
        $post->tag_id = $tag->id;
        $post->tag_title = $tag->title;
        $post->background = $tag->background;

        return $post;
    }

    public function DeleteNote(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $note_id = $request->note_id;

        notes::where("id", $note_id)->where("user_id", $user_id)->delete();

        return 1;
    }

    public function StoreNewTag(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $tag_id = $request->tag_id;
        $title = $request->title;
        $background = $request->background;

        if ($tag_id) {
            $post = notes_tags::where("id", $tag_id)->first();
        } else {
            $post = new notes_tags;
        }

        $post->user_id = $user_id;
        $post->title = $title;
        $post->background = $background;
        $post->save();

        return $post;
    }

    public function StoreNewTask(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $task_id = $request->task_id;

        if ($task_id) {
            $post = tasks::where("id", $task_id)->first();
        } else {
            $post = new tasks;
        }

        if ($request->type == 1 || $request->type == 2) {
            $finished = $request->finished;

            if ($request->type == 1) {
                $title = $request->title;
                $details = $request->details;
                $date = $request->date;
                $date = $date ? date("Y-m-d", strtotime($date)) : date("Y-m-d");
                $customer_id = $request->customer;
                $supplier_id = $request->supplier;
                $employee_id = $request->employee;

                $post->user_id = $user_id;
                $post->customer_id = $customer_id;
                $post->supplier_id = $supplier_id;
                $post->employee_id = $employee_id;
                $post->title = $title;
                $post->details = $details;
                $post->date = $date;
            }

            $post->finished = $finished;
            $post->save();
        } else {
            $post->delete();
        }

        $remaining_tasks = tasks::where("user_id", $user_id)->where("date", $post->date)->where("finished", 0)->count();

        $post->modified_date = strtotime($post->date);
        $post->modified_date1 = date("d-m-Y", strtotime($post->date));
        $post->modified_date2 = date("d/m/Y", strtotime($post->date));
        $post->remaining_tasks = trans_choice('text.Task remaining', $remaining_tasks);

        return $post;
    }

    public function StoreCustomQuotation(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user_name = $user->name;
        $user_email = $user->email;
        $company_name = $user->company_name;
        $counter = $user->counter;

        $name = \Route::currentRouteName();

        $services = $request->item;

        $client = User::where('id', $request->customer)->first();

        if ($name == 'store-custom-quotation') {

            $quotation_invoice_number = $user->quotation_client_id ? date("Y") . "-" . sprintf('%04u', $organization_id) . '-' . sprintf('%06u', $counter) : date("Y") . '-' . sprintf('%06u', $counter);

            $invoice = new custom_quotations;
            $invoice->quotation_invoice_number = $quotation_invoice_number;
            $invoice->handyman_id = $user_id;
            $invoice->user_id = $request->customer;
            $invoice->vat_percentage = $request->vat_percentage;
            $invoice->tax = str_replace(",", ".", $request->tax_amount);
            $invoice->subtotal = str_replace(",", ".", $request->sub_total);
            $invoice->grand_total = str_replace(",", ".", $request->grand_total);
            $invoice->description = $request->other_info;
            $invoice->save();

            foreach ($services as $i => $key) {

                $invoice_items = new custom_quotations_data;
                $invoice_items->quotation_id = $invoice->id;
                $invoice_items->s_i_id = (int)$key;
                $invoice_items->service = $request->service_title[$i];
                $invoice_items->product_title = $request->productInput[$i];

                if (strpos($services[$i], 'I') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->item = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } elseif (strpos($services[$i], 'S') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->is_service = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } else {

                    $invoice_items->b_i_id = (int)$request->brand[$i];
                    $invoice_items->m_i_id = (int)$request->model[$i];
                    $invoice_items->brand = $request->brand_title[$i];
                    $invoice_items->model = $request->model_title[$i];
                }

                $invoice_items->rate = str_replace(",", ".", $request->cost[$i]);
                $invoice_items->qty = str_replace(",", ".", $request->qty[$i]);
                $invoice_items->description = $request->description[$i];
                $invoice_items->estimated_date = $request->date;
                $invoice_items->amount = str_replace(",", ".", $request->amount[$i]);
                $invoice_items->save();
            }

            $counter = $counter + 1;

            $user = User::where('id', $user_id)->first();
            $user->organization->update(['counter' => $counter]);

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/customQuotations/' . $filename;

            $type = 'new';

            if (!file_exists($file)) {

                ini_set('max_execution_time', 180);

                $date = $invoice->created_at;

                $pdf = PDF::loadView('user.pdf_custom_quotation', compact('date', 'client', 'user', 'type', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                $pdf->save(public_path() . '/assets/customQuotations/' . $filename);
            }

            /*$admin_email = $this->sl->admin_email;

            \Mail::send('user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ), function ($message) use($file,$admin_email,$filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($admin_email)->subject('Quotation Created!');

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);

                });*/


            Session::flash('success', __('text.Quotation has been created successfully!'));
            return redirect()->route('customer-quotations');
        } elseif ($name == 'store-direct-invoice') {

            $quotation_invoice_number = $user->quotation_client_id ? date("Y") . "-" . sprintf('%04u', $organization_id) . '-' . sprintf('%06u', $counter) : date("Y") . '-' . sprintf('%06u', $counter);

            $invoice = new custom_quotations;
            $invoice->quotation_invoice_number = $quotation_invoice_number;
            $invoice->status = 3;
            $invoice->approved = 1;
            $invoice->accepted = 1;
            $invoice->invoice = 1;
            $invoice->handyman_id = $user_id;
            $invoice->user_id = $request->customer;
            $invoice->vat_percentage = $request->vat_percentage;
            $invoice->tax = str_replace(",", ".", $request->tax_amount);
            $invoice->subtotal = str_replace(",", ".", $request->sub_total);
            $invoice->grand_total = str_replace(",", ".", $request->grand_total);
            $invoice->description = $request->other_info;
            $invoice->save();

            foreach ($services as $i => $key) {

                $invoice_items = new custom_quotations_data;
                $invoice_items->quotation_id = $invoice->id;
                $invoice_items->s_i_id = (int)$key;
                $invoice_items->service = $request->service_title[$i];
                $invoice_items->product_title = $request->productInput[$i];

                if (strpos($services[$i], 'I') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->item = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } elseif (strpos($services[$i], 'S') > -1) {

                    $invoice_items->b_i_id = 0;
                    $invoice_items->m_i_id = 0;
                    $invoice_items->is_service = 1;
                    $invoice_items->brand = '';
                    $invoice_items->model = '';
                } else {

                    $invoice_items->b_i_id = (int)$request->brand[$i];
                    $invoice_items->m_i_id = (int)$request->model[$i];
                    $invoice_items->brand = $request->brand_title[$i];
                    $invoice_items->model = $request->model_title[$i];
                }

                $invoice_items->rate = str_replace(",", ".", $request->cost[$i]);
                $invoice_items->qty = str_replace(",", ".", $request->qty[$i]);
                $invoice_items->description = $request->description[$i];
                $invoice_items->estimated_date = $request->date;
                $invoice_items->amount = str_replace(",", ".", $request->amount[$i]);
                $invoice_items->save();
            }

            $counter = $counter + 1;

            $user = User::where('id', $user_id)->first();
            $user->organization->update(['counter' => $counter]);

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/customQuotations/' . $filename;

            $type = 'direct-invoice';

            if (!file_exists($file)) {

                ini_set('max_execution_time', 180);

                $date = $invoice->created_at;

                $pdf = PDF::loadView('user.pdf_custom_quotation', compact('date', 'client', 'user', 'type', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

                $pdf->save(public_path() . '/assets/customQuotations/' . $filename);
            }

            /*$admin_email = $this->sl->admin_email;

            \Mail::send('user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ), function ($message) use($file,$admin_email,$filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($admin_email)->subject('Quotation Created!');

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);

                });*/

            $client_name = $client->name;
            $client_email = $client->email;

            \Mail::send(
                'user.custom_quotation_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'company_name' => $company_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $user_email, $user_name, $filename, $company_name) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject(__('text.Direct Invoice Created!') . $company_name);

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );


            Session::flash('success', __('text.Direct invoice has been created successfully!'));
            return redirect()->route('customer-invoices');
        } elseif ($name == 'update-custom-quotation') {

            $quotation = custom_quotations::where('id', $request->quotation_id)->whereIn('handyman_id', $related_users)->first();
            $quotation->ask_customization = 0;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->subtotal = str_replace(",", ".", $request->sub_total);
            $quotation->tax = str_replace(",", ".", $request->tax_amount);
            $quotation->grand_total = str_replace(",", ".", $request->grand_total);
            $quotation->description = $request->other_info;
            $quotation->save();

            $items = custom_quotations_data::where('quotation_id', $quotation->id)->delete();

            foreach ($services as $i => $key) {

                $item = new custom_quotations_data;
                $item->quotation_id = $quotation->id;
                $item->s_i_id = (int)$key;
                $item->service = $request->service_title[$i];
                $item->product_title = $request->productInput[$i];

                if (strpos($services[$i], 'I') > -1) {

                    $item->b_i_id = 0;
                    $item->m_i_id = 0;
                    $item->item = 1;
                    $item->brand = '';
                    $item->model = '';
                } elseif (strpos($services[$i], 'S') > -1) {

                    $item->b_i_id = 0;
                    $item->m_i_id = 0;
                    $item->is_service = 1;
                    $item->brand = '';
                    $item->model = '';
                } else {

                    $item->b_i_id = (int)$request->brand[$i];
                    $item->m_i_id = (int)$request->model[$i];
                    $item->brand = $request->brand_title[$i];
                    $item->model = $request->model_title[$i];
                }

                $item->rate = str_replace(",", ".", $request->cost[$i]);
                $item->qty = str_replace(",", ".", $request->qty[$i]);
                $item->description = $request->description[$i];
                $item->estimated_date = $request->date;
                $item->amount = str_replace(",", ".", $request->amount[$i]);
                $item->save();
            }

            $quotation_invoice_number = $quotation->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/customQuotations/' . $filename;

            $type = 'edit';

            ini_set('max_execution_time', 180);

            $date = $quotation->created_at;

            $pdf = PDF::loadView('user.pdf_custom_quotation', compact('date', 'client', 'user', 'type', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/customQuotations/' . $filename);

            $client_name = $client->name;
            $client_email = $client->email;

            $type = 'edit client';

            \Mail::send(
                'user.custom_quotation_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'company_name' => $company_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $user_email, $user_name, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject(__('text.Quotation Edited!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            /*$admin_email = $this->sl->admin_email;
            $type = 'edit';

            \Mail::send('user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ), function ($message) use($file,$admin_email,$filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($admin_email)->subject('Quotation Edited!');

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);

                });*/


            Session::flash('success', __('text.Quotation has been edited and sent to client successfully!'));
            return redirect()->route('customer-quotations');
        } else {
            $quotation = custom_quotations::where('id', $request->quotation_id)->whereIn('handyman_id', $related_users)->first();
            $quotation->status = 3;
            $quotation->ask_customization = 0;
            $quotation->invoice = 1;
            $quotation->vat_percentage = $request->vat_percentage;
            $quotation->subtotal = str_replace(",", ".", $request->sub_total);
            $quotation->tax = str_replace(",", ".", $request->tax_amount);
            $quotation->grand_total = str_replace(",", ".", $request->grand_total);
            $quotation->description = $request->other_info;
            $quotation->save();

            $items = custom_quotations_data::where('quotation_id', $quotation->id)->delete();

            foreach ($services as $i => $key) {

                if (strpos($services[$i], 'I') > -1) {

                    $x = 1;
                    $brand_id = 0;
                    $model_id = 0;

                    $brand_title = $request->brand_title;

                    $brand_title[$i] = $request->item_brand[$i];
                    $request->merge(['brand_title' => $brand_title]);

                    $model_title = $request->model_title;
                    $model_title[$i] = $request->item_model[$i];
                    $request->merge(['model_title' => $model_title]);
                } else {
                    $x = 0;
                    $brand_id = (int)$request->brand[$i];
                    $model_id = (int)$request->model[$i];
                }

                $item = new custom_quotations_data;
                $item->quotation_id = $quotation->id;
                $item->s_i_id = (int)$key;
                $item->b_i_id = $brand_id;
                $item->m_i_id = $model_id;
                $item->item = $x;
                $item->service = $request->service_title[$i];
                $item->brand = $request->brand_title[$i];
                $item->model = $request->model_title[$i];
                $item->rate = str_replace(",", ".", $request->cost[$i]);
                $item->qty = str_replace(",", ".", $request->qty[$i]);
                $item->description = $request->description[$i];
                $item->estimated_date = $request->date;
                $item->amount = str_replace(",", ".", $request->amount[$i]);
                $item->save();
            }

            $quotation_invoice_number = $quotation->quotation_invoice_number;

            $filename = $quotation_invoice_number . '.pdf';

            $file = public_path() . '/assets/customQuotations/' . $filename;

            $type = 'invoice';

            ini_set('max_execution_time', 180);

            $date = $quotation->created_at;

            $pdf = PDF::loadView('user.pdf_custom_quotation', compact('date', 'client', 'user', 'type', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 140, 'isRemoteEnabled' => true]);

            $pdf->save(public_path() . '/assets/customQuotations/' . $filename);

            $client_name = $client->name;
            $client_email = $client->email;

            $type = 'invoice client';

            \Mail::send(
                'user.custom_quotation_mail',
                array(
                    'username' => $user_name,
                    'client' => $client_name,
                    'company_name' => $company_name,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ),
                function ($message) use ($file, $client_email, $user_email, $user_name, $filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($client_email)->subject(__('text.Invoice Generated!'));

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);
                }
            );

            /*$admin_email = $this->sl->admin_email;
            $type = 'invoice';

            \Mail::send('user.quotation_invoice_mail',
                array(
                    'username' => $user_name,
                    'quote_number' => $requested_quote_number,
                    'quotation_invoice_number' => $quotation_invoice_number,
                    'type' => $type
                ), function ($message) use($file,$admin_email,$filename) {
                    $message->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl');
                    $message->to($admin_email)->subject('Invoice Generated!');

                    $message->attach($file, [
                        'as' => $filename,
                        'mime' => 'application/pdf',
                    ]);

                });*/


            Session::flash('success', 'Invoice has been generated successfully!');
            return redirect()->route('customer-quotations');
        }
    }

    public function Invoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = invoices::leftjoin('bookings', 'bookings.invoice_id', '=', 'invoices.id')->leftjoin('categories', 'categories.id', '=', 'bookings.service_id')->leftjoin('service_types', 'service_types.id', '=', 'bookings.rate_id')->where('invoices.id', '=', $id)->Select('invoices.id', 'invoices.handyman_id', 'invoices.user_id', 'categories.cat_name', 'service_types.type', 'bookings.service_rate', 'bookings.rate', 'invoices.booking_date', 'bookings.total', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.status', 'invoices.total as inv_total', 'invoices.created_at as inv_date', 'invoices.invoice_number', 'invoices.service_fee', 'invoices.vat_percentage', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.amount_refund', 'invoices.commission_percentage')->get();

        $user = invoices::leftjoin('users', 'users.id', '=', 'invoices.user_id')->where('invoices.id', '=', $id)->first();

        $handyman = invoices::leftjoin('users', 'users.id', '=', 'invoices.handyman_id')->where('invoices.id', '=', $id)->first();

        if ($user_role == 2) {
            return view('user.invoice', compact('invoice', 'user', 'handyman'));
        } else {
            return view('user.client_invoice', compact('invoice', 'user', 'handyman'));
        }
    }


    public function CancelledInvoice($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $invoice = invoices::leftjoin('bookings', 'bookings.invoice_id', '=', 'invoices.id')->leftjoin('categories', 'categories.id', '=', 'bookings.service_id')->leftjoin('service_types', 'service_types.id', '=', 'bookings.rate_id')->where('invoices.id', '=', $id)->Select('invoices.id', 'invoices.handyman_id', 'invoices.user_id', 'categories.cat_name', 'service_types.type', 'bookings.service_rate', 'bookings.rate', 'invoices.booking_date', 'bookings.total', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.status', 'invoices.total as inv_total', 'invoices.created_at as inv_date', 'invoices.invoice_number', 'invoices.service_fee', 'invoices.vat_percentage', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.amount_refund', 'invoices.commission_percentage')->get();

        $invoice_number = cancelled_invoices::where('invoice_id', $id)->first();

        $invoice_number = $invoice_number->invoice_number;

        $user = invoices::leftjoin('users', 'users.id', '=', 'invoices.user_id')->where('invoices.id', '=', $id)->first();

        $handyman = invoices::leftjoin('users', 'users.id', '=', 'invoices.handyman_id')->where('invoices.id', '=', $id)->first();

        if ($user_role == 2) {
            return view('user.cancelled_invoice', compact('invoice', 'user', 'handyman', 'invoice_number'));
        } else {
            return view('user.client_cancelled_invoice', compact('invoice', 'user', 'handyman', 'invoice_number'));
        }
    }

    public function Images($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $user_role = $user->role_id;

        $data = bookings::leftjoin('booking_images', 'booking_images.booking_id', '=', 'bookings.id')->leftjoin('categories', 'categories.id', '=', 'bookings.service_id')->where('bookings.invoice_id', '=', $id)->Select('categories.cat_name', 'booking_images.image', 'booking_images.description')->get();

        if ($user_role == 2) {
            return view('user.images', compact('data'));
        } else {
            return view('user.client_images', compact('data'));
        }
    }


    public function ClientIndex()
    {
        $user = Auth::guard('user')->user();

        if ($user->role_id == 2) {
            return redirect()->route('user-login');
        }

        return view('user.client_dashboard', compact('user'));
    }

    public function HandymanBookings()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        // $users_bookings = bookings::leftjoin('users','users.id','=','bookings.user_id')->leftjoin('categories','categories.id','=','bookings.service_id')->leftjoin('service_types','service_types.id','=','bookings.rate_id')->where('handyman_id','=',$user_id)->Select('bookings.id','bookings.handyman_id','users.name','users.email','users.photo','users.family_name','categories.cat_name','service_types.type','bookings.service_rate','bookings.rate','bookings.booking_date','bookings.total','bookings.is_booked','bookings.is_completed','bookings.pay_req','bookings.is_paid','bookings.status')->get();

        $users_bookings = invoices::leftjoin('users', 'users.id', '=', 'invoices.user_id')->where('invoices.handyman_id', '=', $user_id)->Select('invoices.id', 'invoices.user_id', 'invoices.handyman_id', 'invoices.invoice_number', 'invoices.total', 'users.name', 'users.email', 'users.photo', 'users.family_name', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.reply', 'invoices.status', 'invoices.created_at as inv_date', 'invoices.booking_date', 'invoices.service_fee', 'invoices.commission_percentage')->orderBy('id', 'desc')->get();

        // $bookings_dates =  array();

        // $i = 0;

        // foreach ($users_bookings as $key) {

        //     $bookings_dates = bookings::where('invoice_id','=',$key->id)->get();

        //     foreach ($bookings_dates as $temp) {

        //          $dates[$i] = array('id' => $temp->invoice_id,'date' => $temp->booking_date);

        //          $i++;
        //         # code...
        //     }
        // }

        return view('user.bookings', compact('users_bookings'));
    }

    public function PurchasedBookings()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $users_bookings = invoices::leftjoin('users', 'users.id', '=', 'invoices.handyman_id')->where('invoices.user_id', '=', $user_id)->Select('invoices.id', 'invoices.user_id', 'invoices.handyman_id', 'invoices.invoice_number', 'invoices.total', 'users.name', 'users.email', 'users.photo', 'users.family_name', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.reply', 'invoices.status', 'invoices.created_at as inv_date', 'invoices.booking_date')->orderBy('id', 'desc')->get();

        return view('user.purchased_bookings', compact('users_bookings'));
    }

    public function ClientBookings()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->role_id == 2) {
            return redirect()->route('user-login');
        }

        $users_bookings = invoices::leftjoin('users', 'users.id', '=', 'invoices.handyman_id')->where('invoices.user_id', '=', $user_id)->Select('invoices.id', 'invoices.user_id', 'invoices.handyman_id', 'invoices.invoice_number', 'invoices.total', 'users.name', 'users.email', 'users.photo', 'users.family_name', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.reply', 'invoices.status', 'invoices.created_at as inv_date', 'invoices.booking_date')->orderBy('id', 'desc')->get();

        return view('user.client_bookings', compact('users_bookings'));
    }

    public function HandymanStatusUpdate(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $client_email = $request->user_email;

        $client = User::where('email', '=', $client_email)->first();

        $client_name = $client->name . " " . $client->family_name;

        $user_name = $user->name;
        $user_familyname = $user->family_name;

        $name = $user_name . ' ' . $user_familyname;

        $handyman_dash = url('/') . '/aanbieder/dashboard';

        $client_dash = url('/') . '/aanbieder/quotation-requests';

        if ($request->statusSelect == 1) {

            $post = bookings::where('invoice_id', '=', $request->item_id)->update(['is_booked' => 1]);
            $post = invoices::where('id', '=', $request->item_id)->update(['is_booked' => 1]);

            if ($this->lang->lang == 'eng') // English Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Booking status changed!";
                $msg = "Dear Mr/Mrs " . $client_name . ",<br><br>Your requested handyman Mr/Mrs " . $name . " recently changed the status regarding your booking. You can see your current booking status by visiting your profile through <a href='" . $client_dash . "'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($client_email, $subject, $msg, $headers);
            } else // Dutch Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Klus status gewijzigd!";
                $msg = "Beste " . $client_name . ",<br><br>Je stoffeerder " . $name . " heeft de status van je klus gewijzigd. Klik op account om de status van je klus te bekijken <a href='" . $client_dash . "'>account.</a><br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($client_email, $subject, $msg, $headers);
            }

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $subject = "Booking status changed!";
            $msg = "Dear Nordin Adoui, Recent Activity: Status changed for handyman Mr/Mrs " . $name . ". Kindly visit your admin dashboard to view all bookings statuses.";
            mail($this->sl->admin_email, $subject, $msg, $headers);
        } elseif ($request->statusSelect == 3) {

            $post = bookings::where('invoice_id', '=', $request->item_id)->update(['is_booked' => 1, 'is_completed' => 1]);
            $post = invoices::where('id', '=', $request->item_id)->update(['is_booked' => 1, 'is_completed' => 1]);

            if ($this->lang->lang == 'eng') // English Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Booking status changed!";
                $msg = "Dear Mr/Mrs " . $client_name . ",<br><br>Your handyman Mr/Mrs " . $name . " recently changed the status regarding your booking. Current status for the ongoing job is updated as completed by the handyman, If the job has been completed by this handyman than kindly change the status for this job so that we can transfer funds to handyman account. You can see your current booking status by visiting your profile through <a href='" . $client_dash . "'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($client_email, $subject, $msg, $headers);
            } else // Dutch Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Klus status gewijzigd!";
                $msg = "Beste " . $client_name . ",<br><br>Je stoffeerder " . $name . " heeft de status van je klus gewijzigd. De status is gewijzigd naar afgerond, als je akkoord bent graag ook de status wijzigen naar klus voldaan. Indien, je niet tevreden bent laat dit ons graag binnen 48 uur weten zodat wij contact op kunnen nemen met de stoffeerder. Om de status van je klus te bekijken klik op account <a href='" . $client_dash . "'>account.</a><br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($client_email, $subject, $msg, $headers);
            }

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $subject = "Booking status changed!";
            $msg = "Dear Nordin Adoui, Recent activity: Status changed for handyman Mr/Mrs " . $name . ". Kindly visit your admin dashboard to view all bookings statuses.";
            mail($this->sl->admin_email, $subject, $msg, $headers);
        }

        Session::flash('success', $this->lang->hbsm);

        return redirect()->route('handyman-bookings');
    }


    public function ClientStatusUpdate(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $user_name = $user->name;
        $user_familyname = $user->family_name;

        $name = $user_name . ' ' . $user_familyname;

        $handyman_email = $request->user_email;

        $handyman = User::where('email', '=', $handyman_email)->first();

        $handyman_name = $handyman->name . " " . $handyman->family_name;

        $handyman_dash = url('/') . '/aanbieder/dashboard';

        $client_dash = url('/') . '/aanbieder/quotation-requests';

        if ($request->statusSelect == 1) {

            $post = bookings::where('invoice_id', '=', $request->item_id)->update(['is_booked' => 1, 'is_completed' => 1, 'pay_req' => 1]);

            $post = invoices::where('id', '=', $request->item_id)->update(['is_booked' => 1, 'is_completed' => 1, 'pay_req' => 1, 'rating' => $request->rate]);

            $rating = invoices::where('handyman_id', $request->handyman_id)->where('pay_req', 1)->get();
            $t_rating = 0;

            $i = 0;

            foreach ($rating as $key) {

                $t_rating = $t_rating + $key->rating;

                $i++;
            }

            $avg_rating = $t_rating / $i;
            $avg_rating = round($avg_rating);

            $user = User::where('id', $request->handyman_id)->update(['rating' => $avg_rating]);

            if ($this->lang->lang == 'eng') // English Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Booking status changed!";
                $msg = "Dear Mr/Mrs " . $handyman_name . ",<br><br>Your client Mr/Mrs. " . $name . " has changed an ongoing job status to Finished. You will get your payment in your account after approval from backoffice in next 48 hours. You can visit your profile dashboard to view your booking status. You can see your current booking status by visiting your profile through <a href='" . $handyman_dash . "'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($handyman_email, $subject, $msg, $headers);
            } else // Dutch Email Template
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $subject = "Klus status gewijzigd!";
                $msg = "Beste " . $handyman_name . ",<br><br>Je opdrachtgever " . $name . " heeft de status van je klus gewijzigd naar klus voldaan. Je factuur wordt binnen 5 werkdagen uitbetaald. Klik op account om de status van je reservering te bekijken <a href='" . $handyman_dash . "'>account.</a><br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte";
                mail($handyman_email, $subject, $msg, $headers);
            }

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $subject = "Booking status changed!";
            $msg = "Dear Nordin Adoui, Recent activity: Status changed for handyman Mr/Mrs. " . $name . ". Kindly visit your admin dashboard to view all bookings statuses.";
            mail($this->sl->admin_email, $subject, $msg, $headers);

            Session::flash('success', $this->lang->cbsm);

            return redirect()->route('client-bookings');
        }

        if ($request->statusSelect == 3) {

            $post = invoices::where('id', '=', $request->item_id)->first();

            $user = User::where('id', '=', $post->handyman_id)->first();
            $user1 = User::where('id', '=', $post->user_id)->first();

            $handyman_email = $user->email;
            $user_email = $user1->email;

            $handyman_name = $user->name . ' ' . $user->family_name;
            $user_name = $user1->name . ' ' . $user1->family_name;

            $item_id = $request->item_id;

            $rem_amount = $post->total - ($post->total * 0.3);
            $rem_amount = number_format((float)$rem_amount, 2, '.', '');
            $inv_encrypt = Crypt::encrypt($item_id);
            $language = $this->lang->lang;

            $description = 'Remaining partial payment to admin for Invoice No. ' . $post->invoice_number;

            $api_key = Generalsetting::findOrFail(1);

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($api_key->mollie);
            $payment = $mollie->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => $rem_amount, // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'description' => $description,
                'webhookUrl' => route('webhooks.last'),
                'redirectUrl' => url('/thankyou/' . $inv_encrypt),
                "metadata" => [
                    "invoice_id" => $item_id,
                    "user_email" => $user_email,
                    "handyman_email" => $handyman_email,
                    "client_name" => $user_name,
                    "handyman_name" => $handyman_name,
                    "language" => $language,
                ],
            ]);

            $payment_url = $payment->getCheckoutUrl();
            $invoice_update = invoices::where('id', '=', $item_id)->update(['partial_paymentLink' => $payment_url]);

            return Redirect::to($payment_url);
        }

        if ($request->statusSelect == -1) {

            $post = invoices::where('id', '=', $request->item_id)->update(['cancel_req' => 1, 'reason' => $request->reason]);

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'From: Vloerofferte <info@vloerofferte.nl>' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $subject = "Booking cancellation request!";
            $msg = "Dear Nordin Adoui, Recent activity: Job cancellation request has been posted for handyman Mr/Mrs. " . $name . " due to following reason: ' " . $request->reason . " '. Kindly visit your admin dashboard to take further actions.";
            mail($this->sl->admin_email, $subject, $msg, $headers);

            Session::flash('success', $this->lang->cbjcm);

            return redirect()->route('client-bookings');
        }
    }

    public function Services(Request $request)
    {
        $service = Category::leftjoin('service_types', 'service_types.id', '=', 'categories.service_type')->where('categories.id', '=', $request->id)->select('service_types.id', 'service_types.type', 'service_types.text', 'categories.vat_percentage')->first();

        return $service;
    }

    public function GetQuotationData(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $id = $request->id;
        if (strpos($id, 'I')) {
            $id = str_replace("I", "", $id);
            $post = items::where('id', $request->id)->first();
        } else {
            if ($request->type == "service") {
                $post = Category::where('id', $request->id)->first();
            } elseif ($request->type == "brand") {
                $post = Brand::where('id', $request->id)->first();
            } else {
                $post = handyman_products::leftjoin('products', 'products.id', '=', 'handyman_products.product_id')->leftjoin('models', 'models.id', '=', 'products.model_id')->where('products.category_id', $request->cat)->where('products.brand_id', $request->brand)->where('products.model_id', $id)->where('handyman_products.handyman_id', $user_id)->select('handyman_products.sell_rate as rate', 'models.cat_name')->first();
            }
        }


        return $post;
    }

    public function SubServices(Request $request)
    {
        $post = handyman_products::leftjoin('categories', 'categories.id', '=', 'handyman_products.product_id')->leftjoin('service_types', 'service_types.id', '=', 'categories.service_type')->where('handyman_products.handyman_id', $request->handyman_id)->where('handyman_products.product_id', $request->id)->where('handyman_products.main_id', $request->main)->select('handyman_products.rate', 'handyman_products.description', 'service_types.type', 'service_types.text', 'service_types.id as rate_id')->first();

        return $post;
    }

    public function UserServices(Request $request)
    {
        $post = Category::query()->where('id', '=', $request->id)->first();
        $service = service_types::query()->where('id', '=', $post->service_type)->first();

        $service_rate = handyman_products::where('handyman_id', '=', $request->h_id)->where('service_id', '=', $request->id)->first();

        $data[] = array('service' => $service, 'service_rate' => $service_rate);


        return $data;
    }

    public function UserSubServices(Request $request)
    {

        $sub_services = handyman_products::leftjoin('categories', 'categories.id', '=', 'handyman_products.product_id')->leftjoin('service_types', 'service_types.id', '=', 'categories.service_type')->where('handyman_products.handyman_id', $request->handyman_id)->where('handyman_products.main_id', $request->service)->select('categories.cat_name', 'categories.cat_slug', 'categories.id')->get();

        return $sub_services;
    }

    public function DeleteServices(Request $request)
    {

        $service = handyman_products::query()->where('id', '=', $request->id)->delete();

        return 'Success!';
    }

    public function DeleteSubServices(Request $request)
    {

        $service = sub_services::query()->where('id', '=', $request->id)->delete();

        return 'Success!';
    }

    public function AddCart(Request $request)
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } //whether ip is from proxy

        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } //whether ip is from remote address

        else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        $img_desc = $request->file('file');
        $size = 0;
        $no = 0;
        $uploadedFiles = array(); // return value

        if ($img_desc) {
            foreach ($img_desc as $img) {
                $no = $no + 1;
                if ($img->getSize() == '') // Size of single image from list is greater than 2mb
                {
                    $msg = $this->lang->tpe;
                    $type = 1;
                    $cart = carts::where('user_ip', '=', $ip_address)->get();
                    $cart_count = count($cart);
                    $data = array('msg' => $msg, 'type' => $type, 'count' => $cart_count);
                    return $data;
                }

                $size = $img->getSize() + $size;

                /* Location */

                $location = public_path() . '/assets/bookingImages/' . $img->getClientOriginalName();
                $uploadOk = 1;
                $imageFileType = pathinfo($location, PATHINFO_EXTENSION);

                /* Valid Extensions */
                $valid_extensions = array("jpg", "jpeg", "png", "pdf");
                /* Check file extension */

                if (!in_array(strtolower($imageFileType), $valid_extensions)) {

                    $msg = $this->lang->fte;
                    $type = 1;
                    $cart = carts::where('user_ip', '=', $ip_address)->get();
                    $cart_count = count($cart);

                    $data = array('msg' => $msg, 'type' => $type, 'count' => $cart_count);

                    return $data;
                }
            }

            if ($no > 5) {
                $msg = $this->lang->mie;
                $type = 1;
                $cart = carts::where('user_ip', '=', $ip_address)->get();
                $cart_count = count($cart);

                $data = array('msg' => $msg, 'type' => $type, 'count' => $cart_count);
                return $data;
            }

            if ($size > '2097152‬') {
                $msg = $this->lang->tpe;
                $type = 1;
                $cart = carts::where('user_ip', '=', $ip_address)->get();
                $cart_count = count($cart);
                $data = array('msg' => $msg, 'type' => $type, 'count' => $cart_count);
                return $data;
            }

            foreach ($img_desc as $img) {
                $fileName = date('YmdHis', time()) . mt_rand() . '.' . pathinfo($img->getClientOriginalName(), PATHINFO_EXTENSION);
                /* Upload file */
                $img->move(public_path() . '/assets/bookingImages/', $fileName);

                array_push($uploadedFiles, $fileName);
            }
        }

        if ($request->service_questions) {
            $purpose = $request->purpose;

            if ($purpose == 1) {
                if ($request->vat_percentage == 21) {
                    $vat_percentage = $request->vat_percentage;
                    $sell_rate = $request->sell_rate;
                } else {
                    $service_rate = $request->service_rate;
                    $vat_percentage = 21;
                    $sell_rate = $service_rate * ($vat_percentage / 100);
                    $sell_rate = $sell_rate + $service_rate;
                }
            } else {
                if ($request->purpose_type == 1) {
                    if ($request->vat_percentage == 21) {
                        $vat_percentage = $request->vat_percentage;
                        $sell_rate = $request->sell_rate;
                    } else {
                        $service_rate = $request->service_rate;
                        $vat_percentage = 21;
                        $sell_rate = $service_rate * ($vat_percentage / 100);
                        $sell_rate = $sell_rate + $service_rate;
                    }
                } else {
                    $service_rate = $request->service_rate;
                    $vat_percentage = 9;
                    $sell_rate = $service_rate * ($vat_percentage / 100);
                    $sell_rate = $sell_rate + $service_rate;
                }
            }
        } else {
            $vat_percentage = $request->vat_percentage;
            $sell_rate = $request->sell_rate;
        }

        $check = carts::where('user_ip', '=', $ip_address)->first();

        if ($check) {
            if ($check->handyman_id == $request->handyman_id) {
                $to_update = carts::where('user_ip', '=', $ip_address)->where('service_id', '=', $request->service)->where('handyman_id', '=', $request->handyman_id)->first();

                if ($to_update) {
                    $qty = $to_update->rate + $request->rate;
                    carts::where('user_ip', '=', $ip_address)->where('service_id', '=', $request->service)->where('handyman_id', '=', $request->handyman_id)->update(['rate' => $qty, 'vat_percentage' => $vat_percentage, 'sell_rate' => $sell_rate]);
                    $sub_service = $request->sub_service;

                    if ($sub_service) {
                        $date = new DateTime($request->date);
                        $date = $date->format('Y-m-d H:i');

                        foreach ($sub_service as $i => $key) {

                            $sub_service_id = $key;

                            $to_update_sub = carts::where('user_ip', '=', $ip_address)->where('service_id', '=', $sub_service_id)->where('main_id', '=', $request->service)->where('handyman_id', '=', $request->handyman_id)->first();

                            if ($to_update_sub) {
                                $qty_sub = $to_update_sub->rate + $request->sub_rate[$i];
                                carts::where('user_ip', '=', $ip_address)->where('service_id', '=', $sub_service_id)->where('main_id', '=', $request->service)->where('handyman_id', '=', $request->handyman_id)->update(['rate' => $qty_sub]);
                            } else {
                                $cart = new carts;
                                $cart->user_ip = $ip_address;
                                $cart->handyman_id = $request->handyman_id;
                                $cart->service_id = $sub_service_id;
                                $cart->main_id = $request->service;
                                $cart->rate_id = $request->sub_rate_id[$i];
                                $cart->rate = $request->sub_rate[$i];
                                $cart->service_rate = $request->sub_service_rate[$i];
                                $cart->booking_date = $date;
                                $cart->save();
                            }
                        }
                    }

                    if (!empty($_FILES['file'])) {
                        $x = 0;

                        foreach ($img_desc as $img) {
                            $images = new booking_images;
                            $images->cart_id = $to_update->id;
                            $images->image = $uploadedFiles[$x];
                            $images->description = $request->description;
                            $images->save();
                            $x++;
                        }
                    } else {

                        if ($request->description) {
                            $images = new booking_images;
                            $images->cart_id = $to_update->id;
                            $images->description = $request->description;
                            $images->save();
                        }
                    }
                } else {
                    $date = new DateTime($request->date);
                    $date = $date->format('Y-m-d H:i');

                    $post = new carts();
                    $post->user_ip = $ip_address;
                    $post->handyman_id = $request->handyman_id;
                    $post->service_id = $request->service;
                    $post->rate_id = $request->rate_id;
                    $post->rate = $request->rate;
                    $post->service_rate = $request->service_rate;
                    $post->booking_date = $date;
                    $post->vat_percentage = $vat_percentage;
                    $post->sell_rate = $sell_rate;
                    $post->save();

                    $sub_service = $request->sub_service;

                    if ($sub_service) {
                        $i = 0;
                        $date = new DateTime($request->date);
                        $date = $date->format('Y-m-d H:i');

                        foreach ($sub_service as $key) {

                            $cart = new carts;
                            $cart->user_ip = $ip_address;
                            $cart->handyman_id = $request->handyman_id;
                            $cart->service_id = $key;
                            $cart->main_id = $request->service;
                            $cart->rate_id = $request->sub_rate_id[$i];
                            $cart->rate = $request->sub_rate[$i];
                            $cart->service_rate = $request->sub_service_rate[$i];
                            $cart->booking_date = $date;
                            $cart->save();
                            $i++;
                        }
                    }

                    if (!empty($_FILES['file'])) {
                        $x = 0;

                        foreach ($img_desc as $img) {
                            $images = new booking_images;
                            $images->cart_id = $post->id;
                            $images->image = $uploadedFiles[$x];
                            $images->description = $request->description;
                            $images->save();
                            $x++;
                        }
                    } else {

                        if ($request->description) {
                            $images = new booking_images;
                            $images->cart_id = $post->id;
                            $images->description = $request->description;
                            $images->save();
                        }
                    }
                }

                $type = 0;

                // $msg = 'Service added to cart successfully!';
                $msg = $this->lang->acm;
            } else {
                // $msg = 'Sorry, You can only add multiple services of same handyman into your cart!';
                $msg = $this->lang->ace;
                $type = 1;
            }
        } else {
            $date = new DateTime($request->date);
            $date = $date->format('Y-m-d H:i');

            $post = new carts();
            $post->user_ip = $ip_address;
            $post->handyman_id = $request->handyman_id;
            $post->service_id = $request->service;
            $post->rate_id = $request->rate_id;
            $post->rate = $request->rate;
            $post->service_rate = $request->service_rate;
            $post->booking_date = $date;
            $post->vat_percentage = $vat_percentage;
            $post->sell_rate = $sell_rate;
            $post->save();

            $sub_service = $request->sub_service;

            if ($sub_service) {
                $i = 0;

                $date = new DateTime($request->date);
                $date = $date->format('Y-m-d H:i');

                foreach ($sub_service as $key) {

                    $cart = new carts;
                    $cart->user_ip = $ip_address;
                    $cart->handyman_id = $request->handyman_id;
                    $cart->service_id = $key;
                    $cart->main_id = $request->service;
                    $cart->rate_id = $request->sub_rate_id[$i];
                    $cart->rate = $request->sub_rate[$i];
                    $cart->service_rate = $request->sub_service_rate[$i];
                    $cart->booking_date = $date;
                    $cart->save();
                    $i++;
                }
            }

            if (!empty($_FILES['file'])) {
                $x = 0;

                foreach ($img_desc as $img) {
                    $images = new booking_images;
                    $images->cart_id = $post->id;
                    $images->image = $uploadedFiles[$x];
                    $images->description = $request->description;
                    $images->save();
                    $x++;
                }
            } else {
                if ($request->description) {
                    $images = new booking_images;
                    $images->cart_id = $post->id;
                    $images->description = $request->description;
                    $images->save();
                }
            }

            $type = 0;

            // $msg = 'Service added to cart successfully!';
            $msg = $this->lang->acm;
        }

        $cart = carts::where('user_ip', '=', $ip_address)->get();
        $cart_count = count($cart);

        $data = array('msg' => $msg, 'type' => $type, 'count' => $cart_count);
        return $data;
    }

    public function BookHandyman(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($request->handyman_id == $user_id) {

            Session::flash('unsuccess', $this->lang->pdc);
            return redirect()->back();
        }


        $language = $this->lang->lang;

        $payment_option = $request->payment_option;

        $service_rate = $request->service_rate;
        $service_rate1 = $request->service_rate;
        $service_rate = json_encode($service_rate);


        $handyman_id = $request->handyman_id;
        $counter = Generalsetting::findOrFail(1);
        $min_amount = $counter->min_amount;

        $t_amount = $request->sub_total;


        if ($min_amount != '') {
            if ($min_amount > $t_amount) {

                Session::flash('unsuccess', $this->lang->ma . $min_amount . '!');
                return redirect()->back();
            }
        }

        $counter = $counter->counter;

        $invoice_no = sprintf('%04u', $counter);

        $description = 'Payment for Invoice No. ' . $invoice_no;


        $rate_id = $request->rate_id;
        $rate_id = json_encode($rate_id);

        $cart_id = $request->cart_id;
        $cart_id = json_encode($cart_id);

        $service_id = $request->service_id;
        $service_id = json_encode($service_id);

        $rate = $request->rate;
        $rate1 = $request->rate;
        $rate = json_encode($rate);


        $service_total = $request->service_total;

        for ($i = 0; $i < count($service_rate1); $i++) {
            $service_rate1[$i] = $service_rate1[$i];
            $rate1[$i] = $rate1[$i];
            $service_total1[$i] = $service_rate1[$i] * $rate1[$i];
        }

        $service_total = json_encode($service_total1);

        $service_fee = $request->service_fee;
        $vat_percentage = $request->vat_percentage;

        if ($payment_option == 2) {
            $total1 = $request->total_payment1;
            $total = $request->sub_total;
            $total_mollie = number_format((float)$total1, 2, '.', '');
        } else {
            $total = $request->sub_total;
            $total_mollie = number_format((float)$total, 2, '.', '');
        }

        $paid_amount = str_replace('.', ',', number_format($total_mollie, 2));

        $date = $request->date;
        $date = json_encode($date);

        $commission_percentage = $this->gs->commission_percentage;
        // $date = new DateTime($request->date);

        // $date = $date->format('Y-m-d H:m');

        $msg_encrypt = Crypt::encrypt($handyman_id);

        $api_key = Generalsetting::findOrFail(1);

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($api_key->mollie);
        $payment = $mollie->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => $total_mollie, // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $description,
            'webhookUrl' => route('webhooks.mollie'),
            'redirectUrl' => url('/thankyou-page/' . $msg_encrypt),
            "metadata" => [
                "user_id" => $user_id,
                "handyman_id" => $handyman_id,
                "service_id" => $service_id,
                "rate_id" => $rate_id,
                "rate" => $rate,
                "date" => $date,
                "service_rate" => $service_rate,
                "service_total" => $service_total,
                "total" => $total,
                "invoice_no" => $invoice_no,
                "ip" => $request->ip,
                "payment_option" => $payment_option,
                "language" => $language,
                "service_fee" => $service_fee,
                "vat_percentage" => $vat_percentage,
                "commission_percentage" => $commission_percentage,
                "cart_id" => $cart_id,
                "paid_amount" => $paid_amount,


            ],
        ]);

        return redirect($payment->getCheckoutUrl(), 303);


        // $date = new DateTime($request->date);


        // $post = new bookings;
        // $post->user_id = $user_id;
        // $post->handyman_id = $request->handyman_id;
        // $post->is_booked = 1;
        // $post->service_id = $request->service;
        // $post->rate_id = $request->rate_id;
        // $post->rate = $request->rate;
        // $post->booking_date = $date;
        // $post->service_rate = $service_rate;
        // $post->total = $total;
        // $post->save();

        // Session::flash('success', 'Handyman booked successfully!');
        // return redirect()->back();

    }

    public function profile()
    {
        $user = Auth::guard('user')->user();
        // $user = new_quotations::leftjoin("users","users.id","=","new_quotations.creator_id")
        // ->leftJoin('user_organizations', 'user_organizations.user_id', '=', 'users.id')
        // ->leftJoin('organizations', 'organizations.id', '=', 'user_organizations.organization_id')
        // ->where("new_quotations.id",1)->select("users.*","organizations.phone")->first();
        // echo $user->name;
        // echo $user->phone;
        // exit();

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        if ($user->can('edit-profile')) {
            $ep_counter = Generalsetting::pluck("ep_counter")->first();
            $fr_counter = Generalsetting::pluck("fr_counter")->first();

            if (\Route::currentRouteName() == 'company-info') {
                $user = $user->organization;
            }

            return view('user.profile', compact('user', 'ep_counter', 'fr_counter'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function AvailabilityManager()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $post = handyman_unavailability::where('handyman_id', '=', $user_id)->select('date')->get();


        $unavailable_dates = $post->pluck('date')->implode(',');


        $hours = handyman_unavailability_hours::where('handyman_id', '=', $user_id)->get();

        $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user_id)->get();


        return view('user.availability_management', compact('user', 'services', 'unavailable_dates', 'hours'));
    }

    public function RadiusManagement()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        if ($user->can('radius-management')) {
            $organization_id = $user->organization->id;
            $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user_id)->get();
            $terminal = organizations::where('id', $organization_id)->first();

            return view('user.radius_management', compact('user', 'services', 'terminal'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ClientProfile()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 2) {
            return redirect()->route('user-login');
        }

        $cats = Category::all();

        return view('user.client_profile', compact('user', 'cats'));
    }

    public function MyProducts()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'user-products') {
            if ($user->can('user-products')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'product-create') {
            if ($user->can('product-create')) {
                $check = 1;
            }
        }

        if ($check) {
            $products_array = array();

            $products_selected = handyman_products::leftjoin('products', 'products.id', '=', 'handyman_products.product_id')->leftjoin('categories', 'categories.id', '=', 'products.category_id')->leftjoin('brands', 'brands.id', '=', 'products.brand_id')->leftjoin('models', 'models.id', '=', 'products.model_id')->whereIn('handyman_products.handyman_id', $related_users)->orderBy('products.id', 'desc')->select('products.*', 'categories.cat_name as category', 'brands.cat_name as brand', 'models.cat_name as model', 'handyman_products.rate', 'handyman_products.vat_percentage', 'handyman_products.sell_rate', 'handyman_products.id', 'handyman_products.product_id', 'handyman_products.size_rates', 'handyman_products.size_sell_rates')->get();

            foreach ($products_selected as $key) {
                $products_array[] = array($key->product_id);
            }

            $products = Products::leftjoin('categories', 'categories.id', '=', 'products.category_id')->leftjoin('brands', 'brands.id', '=', 'products.brand_id')->leftjoin('models', 'models.id', '=', 'products.model_id')->whereNotIn('products.id', $products_array)->orderBy('products.id', 'desc')->select('products.*', 'categories.cat_name as category', 'brands.cat_name as brand', 'models.cat_name as model')->get();

            return view('user.my_products', compact('user', 'products_selected', 'products'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ProductCreate()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $ids = array();

        $my_products = handyman_products::where('handyman_id', $user_id)->get();

        foreach ($my_products as $key) {
            $ids[] = array('id' => $key->product_id);
        }

        $products = Products::whereNotIn('id', $ids)->get();

        return view('user.create_product', compact('products'));
    }

    public function ProductEdit($id)
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

        if ($user->can('product-edit')) {
            $my_product = handyman_products::leftjoin('products', 'products.id', '=', 'handyman_products.product_id')->leftjoin('categories', 'categories.id', '=', 'products.category_id')->leftjoin('brands', 'brands.id', '=', 'products.brand_id')->leftjoin('models', 'models.id', '=', 'products.model_id')->where('handyman_products.id', $id)->select('products.*', 'handyman_products.*', 'categories.cat_name as category', 'brands.cat_name as brand', 'models.cat_name as model')->first();

            $ids = array();

            $my_products = handyman_products::whereIn('handyman_id', $related_users)->where('id', '!=', $id)->get();

            foreach ($my_products as $key) {
                $ids[] = array('id' => $key->product_id);
            }

            $products = Products::whereNotIn('id', $ids)->get();

            return view('user.create_product', compact('products', 'my_product'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ProductDetails(Request $request)
    {
        $details = Products::leftjoin('categories', 'categories.id', '=', 'products.category_id')->leftjoin('brands', 'brands.id', '=', 'products.brand_id')->leftjoin('models', 'models.id', '=', 'products.model_id')->where('products.id', $request->id)->select('products.*', 'categories.cat_name as category', 'brands.cat_name as brand', 'models.cat_name as model')->first();

        return $details;
    }

    public function ProductStore(Request $request)
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

        if ($request->handyman_product_id) {
            $sizes = explode(',', $request->size);

            $post = handyman_products::where('id', $request->handyman_product_id)->first();
            $post->handyman_id = $user_id;
            $post->product_id = $request->product_id;
            $post->rate = str_replace(",", ".", $request->product_rate[0]);
            $post->sell_rate = str_replace(",", ".", $request->product_sell_rate[0]);
            $post->vat_percentage = $request->product_vat;
            /*$post->model_number = $request->model_number;*/

            $new_rates = [];
            $new_sell_rates = [];

            foreach ($sizes as $y => $key1) {
                array_push($new_rates, str_replace(",", ".", $request->product_rate[$y]));
                array_push($new_sell_rates, str_replace(",", ".", $request->product_sell_rate[$y]));
            }

            $size_rates = implode(',', $new_rates);
            $size_sell_rates = implode(',', $new_sell_rates);
            $post->size_rates = $size_rates;
            $post->size_sell_rates = $size_sell_rates;
            $post->save();

            Session::flash('success', __('text.Product edited successfully.'));
        } else {
            foreach ($request->product_checkboxes as $x => $key) {
                $sizes = explode(',', $request->sizes[$x]);

                $new_rates = [];
                $new_sell_rates = [];

                foreach ($sizes as $y => $key1) {
                    array_push($new_rates, number_format((float)$request->product_rate[$key], 2, '.', ''));
                    array_push($new_sell_rates, number_format((float)$request->product_sell_rate[$key], 2, '.', ''));
                }

                $size_rates = implode(',', $new_rates);
                $size_sell_rates = implode(',', $new_sell_rates);

                $post = new handyman_products;
                $post->handyman_id = $user_id;
                $post->product_id = $request->product_id[$key];
                $post->rate = $request->product_rate[$key];
                $post->sell_rate = $request->product_sell_rate[$key];
                $post->size_rates = $size_rates;
                $post->size_sell_rates = $size_sell_rates;
                $post->vat_percentage = 21;
                /*$post->model_number = $request->model_number[$x];*/
                $post->save();
            }

            Session::flash('success', __('text.New Product(s) added successfully.'));
        }

        return redirect()->route('user-products');
    }

    public function ProductDelete($id)
    {
        $user = Auth::guard('user')->user();

        if ($user->can('product-delete')) {
            $my_product = handyman_products::findOrFail($id);
            $my_product->delete();

            Session::flash('success', __('text.Product deleted successfully.'));
            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function MyServices()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $check = 0;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        if (\Route::currentRouteName() == 'my-services') {
            if ($user->can('my-services')) {
                $check = 1;
            }
        }

        if (\Route::currentRouteName() == 'service-create') {
            if ($user->can('service-create')) {
                $check = 1;
            }
        }

        if ($check) {
            $services_array = array();

            $services_selected = retailer_services::leftjoin('services', 'services.id', '=', 'retailer_services.service_id')->whereIn('retailer_services.retailer_id', $related_users)->orderBy('services.id', 'desc')->select('services.*', 'retailer_services.rate', 'retailer_services.sell_rate', 'retailer_services.id', 'retailer_services.service_id')->get();

            foreach ($services_selected as $key) {
                $services_array[] = array($key->service_id);
            }

            $services = Service::whereNotIn('id', $services_array)->orderBy('services.id', 'desc')->get();

            return view('user.my_services', compact('user', 'services_selected', 'services'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function SaveMyServices(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $services = $request->service_ids;
        $ledger_ids = $request->general_ledgers;

        foreach ($services as $i => $key) {
            retailer_services::where("id", $key)->update(["ledger_id" => $ledger_ids[$i]]);
        }

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->back();
    }

    public function ServiceStore(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        if ($request->retailer_service_id) {
            $sub_categories = implode(',', $request->sub_category_id);

            $post = retailer_services::where('id', $request->retailer_service_id)->first();
            $post->retailer_id = $user_id;
            $post->service_id = $request->service_id;
            $post->rate = str_replace(",", ".", $request->product_rate);
            $post->sell_rate = str_replace(",", ".", $request->product_sell_rate);
            $post->measure = $request->measure;
            $post->category_id = $request->category_id;
            $post->sub_category_ids = $sub_categories ? $sub_categories : NULL;
            $post->save();

            Session::flash('success', 'Service edited successfully.');
        } else {
            foreach ($request->service_checkboxes as $x => $key) {
                $post = new retailer_services;
                $post->retailer_id = $user_id;
                $post->service_id = $request->service_id[$key];
                $post->rate = $request->product_rate[$key];
                $post->sell_rate = $request->product_sell_rate[$key];
                $post->measure = $request->measure[$key];
                $post->category_id = $request->category_id[$key];
                $post->sub_category_ids = $request->sub_category_id[$key];
                $post->save();
            }

            Session::flash('success', 'New Service(s) added successfully.');
        }

        return redirect()->route('my-services');
    }

    public function ServiceEdit($id)
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

        if ($user->can('service-edit')) {
            $my_service = retailer_services::leftjoin('services', 'services.id', '=', 'retailer_services.service_id')->where('retailer_services.id', $id)->select('services.*', 'retailer_services.*')->first();

            $ids = array();

            $my_services = retailer_services::whereIn('retailer_id', $related_users)->where('id', '!=', $id)->get();

            foreach ($my_services as $key) {
                $ids[] = array('id' => $key->service_id);
            }

            $services = Service::whereNotIn('id', $ids)->get();
            $categories = Category::get();
            $sub_categories = sub_categories::where('parent_id', $my_service->category_id)->get();

            return view('user.create_service', compact('services', 'my_service', 'categories', 'sub_categories'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ServiceDelete($id)
    {
        $user = Auth::guard('user')->user();

        if ($user->can('service-delete')) {
            $my_service = retailer_services::findOrFail($id);

            $my_service->delete();
            Session::flash('success', 'Service deleted successfully.');
            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function MyItems()
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

        if ($user->can('user-items')) {
            $items = items::leftjoin("categories", "categories.id", "=", "items.sub_category_ids")->whereIn('items.user_id', $related_users)->orderBy('items.id', 'Desc')->select("items.*", "categories.cat_name as sub_category")->get();

            return view('user.my_items', compact('user_id', 'items'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function CreateItem()
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

        if ($user->can('create-item')) {
            $categories = Category::get();

            $suppliers = retailers_requests::where("retailer_organization", $organization_id)
                ->where('status', 1)->where('active', 1)
                ->pluck('supplier_organization');

            $retailer_products = Products::whereIn('organization_id', $suppliers)
                ->select('products.*')->get();

            return view('user.create_item', compact('categories', 'retailer_products'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function StoreItem(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        if ($request->item_id) {
            $item = items::where('id', $request->item_id)->first();

            if ($item->photo != null) {
                \File::delete(public_path() . '/assets/item_images/' . $item->photo);
            }

            Session::flash('success', __('text.Item updated successfully.'));
        } else {
            $item = new items;
            Session::flash('success', __('text.Item added successfully.'));
        }

        $photo = '';

        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/item_images', $name);
            $photo = $name;
        }

        $sub_categories = implode(',', $request->sub_category_id);
        $products = implode(',', $request->products);

        $item->user_id = $user_id;
        $item->category_id = $request->category_id;
        $item->sub_category_ids = $sub_categories ? $sub_categories : NULL;
        $item->cat_name = $request->item;
        $item->photo = $photo;
        $item->description = $request->description;
        $item->rate = str_replace(",", ".", $request->rate);
        $item->sell_rate = str_replace(",", ".", $request->sell_rate);
        $item->products = $products ? $products : NULL;
        $item->product_id = $request->product_id;
        $item->supplier = $request->supplier;
        $item->save();

        return redirect()->route('user-items');
    }

    public function EditItem($id)
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

        if ($user->can('edit-item')) {
            $item = items::where('id', $id)->whereIn('user_id', $related_users)->first();
            $categories = Category::get();
            $sub_categories = sub_categories::where('parent_id', $item->category_id)->get();

            $suppliers = retailers_requests::where("retailer_organization", $organization_id)
                ->where('status', 1)->where('active', 1)
                ->pluck('supplier_organization');

            $retailer_products = Products::whereIn('organization_id', $suppliers)
                ->select('products.*')->get();

            return view('user.create_item', compact('item', 'categories', 'sub_categories', 'retailer_products'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function UpdateItem(Request $request, $id)
    {
        $item = items::findOrFail($id);
        $input = $request->all();

        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/item_images', $name);
            if ($item->photo != null) {
                \File::delete(public_path() . '/assets/item_images/' . $item->photo);
            }
            $input['photo'] = $name;
        } else {
            if ($item->photo != null) {
                \File::delete(public_path() . '/assets/item_images/' . $item->photo);
            }

            $input['photo'] = '';
        }

        $item = items::where('id', $id)->update(['cat_name' => $request->item, 'photo' => $input['photo'], 'description' => $request->description, 'rate' => str_replace(",", ".", $request->rate)]);

        Session::flash('success', __('text.Item updated successfully.'));
        return redirect()->route('user-items');
    }

    public function DestroyItem($id)
    {
        $user = Auth::guard('user')->user();

        if ($user->can('delete-item')) {
            $item = items::findOrFail($id);

            if ($item->photo == null) {
                $item->delete();
                Session::flash('success', 'Item deleted successfully.');
                return redirect()->route('user-items');
            }

            \File::delete(public_path() . '/assets/item_images/' . $item->photo);
            $item->delete();
            Session::flash('success', __('text.Item deleted successfully.'));
            return redirect()->route('user-items');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function importItems()
    {
        $user = Auth::guard('user')->user();

        if ($user->can('create-item')) {
            return view('user.import_item');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function PostItemsImport(Request $request)
    {
        ini_set('memory_limit', '-1');
        $extension = strtolower($request->excel_file->getClientOriginalExtension());

        if (!in_array($extension, ['xls', 'xlsx'])) {
            return redirect()->back()->withErrors("File should be of format xlsx or xls")->withInput();
        }

        $import = new ItemsImport;
        Excel::import($import, request()->file('excel_file'));

        //        if(count($import->data) > 0)
        //        {
        //            $product = items::where('excel',1)->whereNotIn('id', $import->data)->get();
        //
        //            foreach ($product as $key)
        //            {
        //                if($key->photo != null){
        //                    \File::delete(public_path() .'/assets/item_images/'.$key->photo);
        //                }
        //
        //                $key->delete();
        //            }
        //        }

        Session::flash('success', __('text.Task completed successfully.'));
        return redirect()->route('user-items');
    }

    public function ExportItems()
    {
        $user = Auth::guard('user')->user();

        if ($user->can('create-item')) {
            ini_set('memory_limit', '-1');
            return Excel::download(new ItemsExport(), 'items.xlsx');
        } else {
            return redirect()->route('user-login');
        }
    }

    public function MySubServices()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }


        // $sub_cats = Category::leftjoin('handyman_products','handyman_products.product_id','=','categories.id')->leftjoin('sub_services','sub_services.sub_id','=','handyman_products.product_id')->where('handyman_products.handyman_id',$user_id)->where('categories.main_service',0)->select('categories.id','categories.cat_name','sub_services.cat_id','sub_services.sub_id','handyman_products.rate','handyman_products.description')->get();

        $main_cats_selected = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', $user_id)->select('categories.id', 'categories.cat_name', 'handyman_products.product_id')->get();

        foreach ($main_cats_selected as $key => $value) {

            $sub_cats[$value->id] = Category::leftjoin('sub_services', 'sub_services.sub_id', '=', 'categories.id')->where('sub_services.cat_id', $value->id)->select('categories.id', 'categories.cat_name', 'sub_services.cat_id', 'sub_services.sub_id')->get();

            $sub_selected[$value->id] = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->leftjoin('service_types', 'service_types.id', '=', 'categories.service_type')->where('handyman_products.handyman_id', $user_id)->where('handyman_products.main_id', $value->id)->select('categories.id', 'categories.cat_name', 'handyman_products.id as h_id', 'handyman_products.rate', 'handyman_products.description', 'handyman_products.main_id', 'service_types.type', 'handyman_products.vat_percentage', 'handyman_products.sell_rate')->get();

            # code...
        }


        return view('user.my_subservices', compact('user', 'sub_cats', 'main_cats_selected', 'sub_selected'));
    }

    public function GetID(Request $request)
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        $sub_cats = Category::leftjoin('sub_services', 'sub_services.sub_id', '=', 'categories.id')->where('sub_services.cat_id', $request->id)->select('categories.id', 'categories.cat_name', 'sub_services.cat_id', 'sub_services.sub_id')->get();

        return $sub_cats;
    }

    public function CompleteProfile()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        if ($user->can('user-complete-profile')) {
            $cats = Category::all();

            $services_selected = handyman_products::query()->where('handyman_id', '=', $user_id)->get();

            $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user_id)->get();


            return view('user.complete_profile', compact('user', 'cats', 'services_selected', 'services'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ExperienceYears()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $cats = Category::all();

        $services_selected = handyman_products::query()->where('handyman_id', '=', $user_id)->get();

        $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user_id)->get();


        return view('user.experience_years', compact('user', 'services_selected', 'services', 'cats'));
    }

    public function Insurance()
    {
        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }

        $cats = Category::all();

        $services_selected = handyman_products::query()->where('handyman_id', '=', $user_id)->get();

        $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user_id)->get();


        return view('user.insurance', compact('user', 'services_selected', 'services', 'cats'));
    }

    public function resetform()
    {
        $user = Auth::guard('user')->user();
        $user_role = Auth::guard('user')->user()->role_id;

        if ($user_role == 2) {

            $services = Category::leftjoin('handyman_products', 'handyman_products.product_id', '=', 'categories.id')->where('handyman_products.handyman_id', '=', $user->id)->get();
        } else {
            $services = "";
        }

        return view('user.reset', compact('user', 'services', 'user_role'));
    }

    public function reset(Request $request)
    {
        $input = $request->all();
        $user = Auth::guard('user')->user();
        if ($request->cpass) {
            if (Hash::check($request->cpass, $user->password)) {
                if ($request->newpass == $request->renewpass) {
                    $input['password'] = Hash::make($request->newpass);
                } else {
                    Session::flash('unsuccess', $this->lang->cpnm);
                    return redirect()->back();
                }
            } else {
                Session::flash('unsuccess', $this->lang->cpnm);
                return redirect()->back();
            }
        }
        $user->update($input);
        Session::flash('success', $this->lang->suyp);
        return redirect()->back();
    }

    public function compressImage($source, $destination, $quality)
    {

        $info = getimagesize($source);

        if (isset($info['mime']) && $info['mime'] == 'image/jpeg') {
            $source = imagecreatefromjpeg($source);
        }

        $img = Image::make($source);

        $img->resize(300, 300, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destination);

        return;
    }

    public function compress_image()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->photo) {
                $path = public_path('assets/images/' . $user->photo);

                if (File::exists($path)) {
                    $tmpFilePath = 'assets/images/';
                    $hardPath =  time() . '.' . pathinfo($path, PATHINFO_EXTENSION);

                    $target_file = $tmpFilePath . $hardPath;

                    $this->compressImage($path, $target_file, 20);
                    $user->compressed_photo = $hardPath;
                    $user->save();
                }
            }
        }
    }

    public function increment_employee_number()
    {
        $users_max_employee_number = employees_details::where("employee_number", "!=", NULL)->max("employee_number");
        $drafts_max_employee_number = user_drafts::where("employee_number", "!=", NULL)->max("employee_number");

        $max = max($users_max_employee_number, $drafts_max_employee_number) ? max($users_max_employee_number, $drafts_max_employee_number) + 1 : 2;

        Generalsetting::where("backend", 0)->update(["ep_counter" => $max]);
        Generalsetting::where("backend", 1)->update(["ep_counter" => $max]);
    }

    public function increment_freelancer_number()
    {
        $users_max_freelancer_number = employees_details::where("freelancer_number", "!=", NULL)->max("freelancer_number");
        $drafts_max_freelancer_number = user_drafts::where("freelancer_number", "!=", NULL)->max("freelancer_number");

        $max = max($users_max_freelancer_number, $drafts_max_freelancer_number) ? max($users_max_freelancer_number, $drafts_max_freelancer_number) + 1 : 2;
        Generalsetting::where("backend", 0)->update(["fr_counter" => $max]);
        Generalsetting::where("backend", 1)->update(["fr_counter" => $max]);
    }

    public function TemporaryProfileUpdate(Request $request)
    {
        $input = $request->all();
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        $organization_id = $user->organization->id;

        if ($request->registration_number && $request->company_name) {
            // if($main_id)
            // {
            //     $user_data = User::where("id",$main_id)->first();
            // }
            // else
            // {
            //     $user_data = $user;
            // }

            $checkRNCN = checkCombinationRNCN($input, $user); // check if combination of registration number and company name exists in users and user_organizations tables

            if ($checkRNCN) {
                Session::flash('unsuccess', __('text.The combination of registration number and company name already exists.'));
                return redirect()->back();
            }
        }

        if ($input['longitude'] && $input['latitude']) {
            $latitude = $input['latitude'];
            $longitude = $input['longitude'];
        } else {
            $latitude = $user->organization->latitude;
            $longitude = $user->organization->longitude;
        }

        // $check = user_drafts::where('user_id', $user->id)->where('company_profile',$input['company_profile'])->first();

        // if (strpos($request->address, '&') === true) {
        //     $input['address'] = str_replace("&", "and", $request->address);
        // }

        if (!empty($request->special)) {
            $input['special'] = implode(',', $request->special);
        }

        if (empty($request->special)) {
            $input['special'] = null;
        }

        $this->profileupdate((object)$request, $input);

        Session::flash('success', __("text.Profile updated successfully"));

        return redirect()->back();
    }

    public function ProfileUpdateRequests()
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $main_id = $user->main_id;

        if ($main_id) {
            return redirect()->route('user-login');
        } else {
            $all_employees = User::where("main_id", $user_id)->pluck("id");
        }

        $users_requests = user_drafts::leftjoin('users', 'users.id', '=', 'user_drafts.user_id')->whereIn("user_drafts.user_id", $all_employees)->select('users.name', 'users.family_name', 'users.email', 'user_drafts.created_at as Date', 'user_drafts.updated_at as UpdateDate', 'users.photo', 'user_drafts.id', 'user_drafts.company_profile')->orderBy('user_drafts.id', 'desc')->get();

        return view("user.requests", compact("users_requests"));
    }

    public function ProfileUpdateRequest($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        $main_id = $user->main_id;

        $request = user_drafts::leftjoin('users', 'users.id', '=', 'user_drafts.user_id')->where('user_drafts.id', '=', $id)->select("user_drafts.*", "users.main_id")->first();

        if ($request && ($request->main_id == $user_id)) {
            return view('user.request', compact('request'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function ProfileUpdateRequestPost(Request $request, $api = 0)
    {
        $input = $request->all();

        $req_id = $input["req_id"];
        $draft = user_drafts::where("id", $req_id)->first();

        // if($draft->company_profile)
        // {
        //     $company_id = User::where("id",$draft->user_id)->pluck("main_id")->first();
        //     $user = User::where('id',$company_id)->first();
        // }
        // else
        // {
        //     $user = User::where('id',$draft->user_id)->first();
        // }

        $user = User::where('id', $draft->user_id)->first();

        $this->profileupdate(new Request(), $input, $user);
        $user_id = $user->id;

        if ($input['latitude'] && $input['longitude']) {
            $user->organization->update(['terminal_latitude' => $input['latitude'], 'terminal_longitude' => $input['longitude'], 'terminal_zipcode' => $input['postcode'], 'terminal_city' => $input['city']]);
        }

        user_drafts::where('id', $req_id)->delete();
        $user = User::findOrFail($draft->user_id);
        $email = $user->email;
        $user_name = $user->name;
        $handyman_dash = url('/') . '/aanbieder/dashboard';

        // \Mail::send(array(), array(), function ($message) use ($email, $user_name, $handyman_dash) {
        //     $message->to($email)
        //         ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
        //         ->subject("Profile Information Updated Successfully!")
        //         ->html("Dear Mr/Mrs ". $user_name .",<br><br>Your profile information update request has been approved. For further details visit your handyman panel through <a href='".$handyman_dash."'>here.</a><br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        // });

        \Mail::send(array(), array(), function ($message) use ($email, $user_name) {
            $message->to($email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject("Profiel informatie is geupdate")
                ->html("Beste " . $user_name . ",<br><br>Je wijziging in je profiel is goedgekeurd.<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
        });

        if (!$api) {
            Session::flash('success', __("text.Profile updated successfully"));
            return redirect()->route('profile-update-requests');
        } else {
            return 1;
        }
    }

    public function profileupdate(Request $request, $input = NULL, $user = NULL)
    {
        if (!$user) {
            $user = Auth::guard('user')->user();
        }

        $flag = 1;

        if (!$input) {
            $input = $request->all();
        } else {
            $flag = 0;
        }

        $photo = !$input['company_profile'] ? $user->getRawOriginal('photo') : $user->photo;
        $compressed_photo = !$input['company_profile'] ? $user->getRawOriginal('compressed_photo') : $user->compressed_photo;

        if ($file = $request->file('photo')) {

            $tmpFilePath = 'assets/images/';

            if ($photo != null) {
                \File::delete(public_path() . '/assets/images/' . $photo);
            }

            if ($compressed_photo != null) {
                \File::delete(public_path() . '/assets/images/' . $compressed_photo);
            }

            $image = $request->file('photo');
            $compressed_name = time() . $image->getClientOriginalName();
            $target_file = $tmpFilePath . $compressed_name;
            $this->compressImage($image, $target_file, 20);

            $name = time() . $file->getClientOriginalName();
            $file->move($tmpFilePath, $name);

            $input['photo'] = $name;
            $input['compressed_photo'] = $compressed_name;
        } else {
            $input['photo'] = !$input['company_profile'] ? $user->getRawOriginal('photo') : $user->photo;
            $input['compressed_photo'] = !$input['company_profile'] ? $user->getRawOriginal('compressed_photo') : $user->compressed_photo;
        }

        if (!empty($request->special)) {
            $input['special'] = implode(',', $request->special);
        }

        if (empty($request->special)) {
            $input['special'] = null;
        }

        if (!$input['company_profile']) {
            $user->name = $input["name"];
            $user->family_name = $input["family_name"];
            $user->photo = $input["photo"];
            $user->compressed_photo = $input["compressed_photo"];
            $user->description = $input["description"];
            $user->language = $input["language"];
            isset($input["age"]) ? $user->age = $input["age"] : NULL;
            $user->education = $input["education"];
            $user->profession = $input["profession"];
            // $user->city = $input["city"];
            // $user->address = $input["address"];
            // $user->phone = $input["phone"];
            $user->web = $input["web"];
            $user->special = $input["special"];
            // $user->postcode = $input["postcode"];
            $user->save();

            $employee_details = $user->employee;

            if (isset($input["profile_type"])) {
                $employee_details->profile_type = $input['profile_type'];
                $employee_details->contract = $input['profile_type'] == 1 ? "Employee" : "Freelancer";
                // $employee_details->employee_number = (!isset($input['profile_type']) || $input['profile_type'] == 1) ? $input['employee_number'] : NULL;
                // $employee_details->freelancer_number = (!isset($input['profile_type']) || $input['profile_type'] == 2) ? $input['freelancer_number'] : NULL;
                $employee_details->personal_number = $input['profile_type'] == 1 ? $input['personal_number'] : NULL;
                $employee_details->contract_number = $input['contract_number'];
                $employee_details->freelancer_registration_number = $input['profile_type'] == 2 ? $input['freelancer_registration_number'] : NULL;
                $employee_details->business_name = $input['profile_type'] != 1 ? $input["business_name"] : NULL;
                $employee_details->tax_number = $input['profile_type'] != 1 ? $input["tax_number"] : NULL;
                $employee_details->bank_account = $input['profile_type'] != 1 ? $input["bank_account"] : NULL;
            }

            $employee_details->name = $input["name"];
            $employee_details->email = $input["email"];
            $employee_details->postcode = $input["postcode"];
            $employee_details->city = $input["city"];
            $employee_details->phone = $input["phone"];
            $employee_details->address = $input["address"];
            $employee_details->save();
        } else {
            $organization = $user->organization;
            $organization->company_name = $input["company_name"];
            $organization->registration_number = $input["registration_number"];
            $organization->phone = $input["phone"];
            $organization->web = $input["web"];
            $organization->address = $input["address"];
            $organization->city = $input["city"];
            $organization->postcode = $input["postcode"];
            $organization->photo = $input["photo"];
            $organization->compressed_photo = $input["compressed_photo"];
            $organization->email = $input["email"];
            $organization->business_name = $input["business_name"];
            $organization->tax_number = $input["tax_number"];
            $organization->bank_account = $input["bank_account"];
            $organization->save();
        }

        if ($flag) {
            Session::flash('success', $this->lang->success);
            return redirect()->route('user-profile');
        } else {
            return;
        }
    }

    public function AvailabilityUpdate(Request $request)
    {
        $input = $request->all();

        $user = Auth::guard('user')->user();

        $handyman_unavailability = handyman_unavailability::where('handyman_id', '=', $user->id)->delete();

        if ($request->multiple_dates != '') {
            $myArray = explode(',', $request->multiple_dates);

            foreach ($myArray as $key) {
                $handyman_unavailability = new handyman_unavailability();
                $handyman_unavailability->handyman_id = $user->id;
                $handyman_unavailability->date = $key;
                $handyman_unavailability->save();
            }
        }

        $handyman_unavailability_hours = handyman_unavailability_hours::where('handyman_id', '=', $user->id)->delete();

        if ($request->hours != '') {

            foreach ($request->hours as $key) {
                $handyman_unavailability_hours = new handyman_unavailability_hours();
                $handyman_unavailability_hours->handyman_id = $user->id;
                $handyman_unavailability_hours->hour = $key;
                $handyman_unavailability_hours->save();
            }
        }

        Session::flash('success', $this->lang->success);
        return redirect()->route('user-availability');
    }

    public function RadiusUpdate(Request $request)
    {
        $input = $request->all();

        $user = Auth::guard('user')->user();

        $user->organization->update(['terminal_zipcode' => $input['postal_code'], 'terminal_longitude' => $input['longitude'], 'terminal_latitude' => $input['latitude'], 'terminal_radius' => $input['radius'], 'terminal_city' => $input['terminal_city']]);

        Session::flash('success', $this->lang->success);
        return redirect()->route('radius-management');
    }

    public function InsuranceUpload(Request $request)
    {
        $input = $request->all();

        $user = Auth::guard('user')->user();


        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/InsurancePod', $name);
            if ($user->photo != null) {
                unlink(public_path() . '/assets/InsurancePod/' . $user->photo);
            }
            $input['photo'] = $name;
        }

        $post = User::where('id', '=', $user->id)->update(['insurance_pod' => $input['photo']]);

        $user_name = $user->name;
        $user_familyname = $user->family_name;

        $name = $user_name . ' ' . $user_familyname;

        \Mail::send(array(), array(), function ($message) use ($name) {
            $message->to($this->sl->admin_email)
                ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                ->subject("Insurance POD Uploaded!")
                ->html("Dear Nordin Adoui, Recent activity: A handyman Mr/Mrs. " . $name . " uploaded a pod for his/her insurance, kindly visit your admin dashboard in order to take further actions.", 'text/html');
        });

        Session::flash('success', $this->lang->success);
        return redirect()->route('insurance');
    }

    public function ClientProfileUpdate(Request $request)
    {
        $input = $request->all();

        $user = Auth::guard('user')->user();

        if ($file = $request->file('photo')) {
            $name = time() . $file->getClientOriginalName();
            $file->move('assets/images', $name);
            if ($user->photo != null) {
                unlink(public_path() . '/assets/images/' . $user->photo);
            }
            $input['photo'] = $name;
        }
        if (strpos($request->address, '&') === true) {
            $input['address'] = str_replace("&", "and", $request->address);
        }

        if (!empty($request->special)) {
            $input['special'] = implode(',', $request->special);
        }

        if (empty($request->special)) {
            $input['special'] = null;
        }

        $user->update($input);
        $universal_customers_details = universal_customers_details::where("user_id", $user->id)->first();
        $universal_customers_details->business_name = $request->business_name;
        $universal_customers_details->tax_number = $request->tax_number;
        $universal_customers_details->address = $request->address;
        $universal_customers_details->postcode = $request->postcode;
        $universal_customers_details->city = $request->city;
        $universal_customers_details->phone = $request->phone;
        $universal_customers_details->save();

        Session::flash('success', $this->lang->success);
        return redirect()->route('client-profile');
    }

    public function MyServicesUpdate(Request $request)
    {
        $input = $request->all();

        $user_id = Auth::guard('user')->user()->id;

        $post = handyman_products::query()->where('handyman_id', '=', $user_id)->first();


        if ($post != "") {
            for ($i = 0; $i < sizeof($input['title']); $i++) {
                if ($input['hs_id'][$i] == 0) {
                    $post = new handyman_products();
                    $post->handyman_id = $user_id;
                    $post->service_id = $input['title'][$i];
                    $post->rate = $input['details'][$i];
                    $post->vat_percentage = $input['vat_percentages'][$i];
                    $post->sell_rate = $input['sell_rates'][$i];
                    $post->description = $input['description'][$i];
                    $post->save();
                } else {
                    $post = handyman_products::query()->where('id', '=', $input['hs_id'][$i])->update(['service_id' => $input['title'][$i], 'rate' => $input['details'][$i], 'vat_percentage' => $input['vat_percentages'][$i], 'sell_rate' => $input['sell_rates'][$i], 'description' => $input['description'][$i]]);
                }
            }
        } else {
            for ($i = 0; $i < sizeof($input['title']); $i++) {
                $post = new handyman_products();
                $post->handyman_id = $user_id;
                $post->service_id = $input['title'][$i];
                $post->rate = $input['details'][$i];
                $post->vat_percentage = $input['vat_percentages'][$i];
                $post->sell_rate = $input['sell_rates'][$i];
                $post->description = $input['description'][$i];
                $post->save();
            }
        }

        Session::flash('success', $this->lang->success);
        return redirect()->route('user-services');
    }

    public function MySubServicesUpdate(Request $request)
    {
        $input = $request->all();

        $user_id = Auth::guard('user')->user()->id;

        for ($i = 0; $i < sizeof($input['title']); $i++) {
            if ($input['hs_id'][$i] == 0) {
                $check = handyman_products::where('handyman_id', $user_id)->where('service_id', $input['title'][$i])->where('main_id', $input['main_id'][$i])->first();

                if ($check) {

                    $post = handyman_products::query()->where('id', '=', $check->id)->update(['service_id' => $input['title'][$i], 'rate' => $input['details'][$i], 'vat_percentage' => $input['vat_percentages'][$i], 'sell_rate' => $input['sell_rates'][$i], 'description' => $input['description'][$i]]);
                } else {
                    $post = new handyman_products();
                    $post->handyman_id = $user_id;
                    $post->service_id = $input['title'][$i];
                    $post->main_id = $input['main_id'][$i];
                    $post->rate = $input['details'][$i];
                    $post->vat_percentage = $input['vat_percentages'][$i];
                    $post->sell_rate = $input['sell_rates'][$i];
                    $post->description = $input['description'][$i];
                    $post->save();
                }
            } else {
                $post = handyman_products::query()->where('id', '=', $input['hs_id'][$i])->update(['service_id' => $input['title'][$i], 'rate' => $input['details'][$i], 'vat_percentage' => $input['vat_percentages'][$i], 'sell_rate' => $input['sell_rates'][$i], 'description' => $input['description'][$i]]);
            }
        }

        Session::flash('success', $this->lang->success);
        return redirect()->route('user-subservices');
    }

    public function PostExperienceYears(Request $request)
    {
        $input = $request->all();

        $user_id = Auth::guard('user')->user()->id;
        $post = User::query()->where('id', '=', $user_id)->update(['experience_years' => $request->years]);

        Session::flash('success', $this->lang->success);
        return redirect()->route('experience-years');
    }


    public function CompleteProfileUpdate(Request $request)
    {
        $input = $request->all();

        $registration_fee = $this->gs->registration_fee;

        if ($registration_fee == '' || $registration_fee == 0) {
            $registration_fee = "0.01";
        } else {
            $registration_fee = number_format((float)$registration_fee, 2, '.', '');
        }

        $consumerName = $input['full_name'];
        $current_date = date("Y-m-d");

        $user = Auth::guard('user')->user();
        $user_id = Auth::guard('user')->user()->id;
        $role_id = $user->role_id;
        $api_key = Generalsetting::findOrFail(1);
        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($api_key->mollie);

        $customer = $mollie->customers->create([
            "name" => $consumerName,
            "email" => $input['email'],
        ]);

        //         $mandate = $mollie->customers->get($customer->id)->createMandate([
        //    "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
        //    "consumerName" => "John Doe",
        //    "consumerAccount" => "NL55INGB0000000000",
        // ]);

        $payment = $mollie->customers->get($customer->id)->createPayment([
            "amount" => [
                "currency" => "EUR",
                "value" => $registration_fee,
            ],
            "description" => "Registration Fee Payment",
            "redirectUrl" => route('user-complete-profile'),
            "webhookUrl" => route('webhooks.first'),
            "metadata" => [
                "customer_id" => $customer->id,
                "consumer_name" => $consumerName,
                "user_id" => $user_id,
                "role_id" => $role_id
            ],
        ]);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function publish()
    {
        $user = Auth::guard('user')->user();
        $user->status = 1;
        $user->active = 1;
        $user->update();
        return redirect(route('user-dashboard'))->with('success', 'Successfully Published The Profile.');
    }

    public function feature()
    {
        $user = Auth::guard('user')->user();
        $user->is_featured = 1;
        $user->featured = 1;
        $user->update();
        return redirect(route('user-dashboard'))->with('success', 'Successfully Featured The Profile.');
    }

    public function Ratings()
    {

        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->role_id == 3) {
            return redirect()->route('user-login');
        }


        if ($user->can('ratings')) {
            $ratings = invoices::leftjoin('users', 'users.id', '=', 'invoices.user_id')->where('invoices.handyman_id', '=', $user_id)->where('invoices.pay_req', 1)->Select('invoices.id', 'invoices.user_id', 'invoices.handyman_id', 'invoices.invoice_number', 'invoices.total', 'users.name', 'users.family_name', 'invoices.rating as client_rating', 'users.email', 'users.photo', 'users.family_name', 'invoices.is_booked', 'invoices.is_completed', 'invoices.pay_req', 'invoices.is_paid', 'invoices.is_partial', 'invoices.is_cancelled', 'invoices.cancel_req', 'invoices.reply', 'invoices.status', 'invoices.created_at as inv_date', 'invoices.booking_date')->get();


            return view('user.ratings', compact('ratings'));
        } else {
            return redirect()->route('user-login');
        }
    }

    public function MarkDelivered($id)
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

        if ($user->can('mark-delivered')) {
            $now = date('d-m-Y H:i:s');
            $check = quotation_invoices::where('id', $id)->whereIn('handyman_id', $related_users)->where('invoice', 1)->update(['delivered' => 1, 'delivered_date' => $now]);

            if ($check) {
                $client = quotation_invoices::leftjoin('quotes', 'quotes.id', '=', 'quotation_invoices.quote_id')->leftjoin('users', 'users.id', '=', 'quotes.user_id')->where('quotation_invoices.id', $id)->select('users.*', 'quotation_invoices.quotation_invoice_number')->first();

                $admin_email = $this->sl->admin_email;

                $link = url('/') . '/aanbieder/quotation-requests';

                if ($this->lang->lang == 'du') {
                    $msg = "Beste $client->name,<br><br>De status van je bestelling met factuur INV# <b>" . $client->quotation_invoice_number . "</b> is zojuist gewijzigd naar afgeleverd. Je kan de status naar ontvangen wijzigen in je <a href='$link'>dashboard</a>. Doe dit alleen als je de goederen hebt ontvangen. Mocht, je de goederen op de bezorgdatum niet hebben ontvangen neem dan contact met ons op.<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte";
                } else {
                    $msg = "Dear <b>Mr/Mrs " . $client->name . "</b>,<br><br>Goods for quotation INV# <b>" . $client->quotation_invoice_number . "</b> have been marked as delivered. You can change this quotation status to 'Received' if goods have been delivered to you. After 7 days from now on it will automatically be marked as 'Received'.<br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte";
                }

                \Mail::send(array(), array(), function ($message) use ($msg, $client) {
                    $message->to($client->email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                        ->subject(__('text.Invoice Status Changed'))
                        ->html($msg, 'text/html');
                });

                \Mail::send(array(), array(), function ($message) use ($admin_email, $client) {
                    $message->to($admin_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                        ->subject('Invoice Status Changed')
                        ->html("Recent activity: Goods for quotation INV# <b>" . $client->quotation_invoice_number . "</b> have been marked as delivered.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
                });

                Session::flash('success', __('text.Status Updated Successfully!'));
            }

            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }

    public function MarkReceived($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $now = date('d-m-Y H:i:s');
        $check = new_quotations::leftjoin('quotes', 'quotes.id', '=', 'new_quotations.quote_request_id')->where('new_quotations.id', $id)->where(function ($query) use ($user_id) {
            $query->where('quotes.user_id', $user_id)->orWhere('new_quotations.user_id', $user_id);
        })->update(['new_quotations.customer_received' => 1, 'new_quotations.received_date' => $now]);

        if ($check) {
            $retailer = new_quotations::leftjoin('users', 'users.id', '=', 'new_quotations.creator_id')->where('new_quotations.id', $id)->select('users.*', 'new_quotations.quotation_invoice_number')->first();

            $admin_email = $this->sl->admin_email;

            if ($this->lang->lang == 'du') {
                $msg = "Beste $retailer->name,<br><br>Je klant heeft de status voor factuur INV# <b>" . $retailer->quotation_invoice_number . "</b> gewijzigd naar goederen ontvangen.<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Pieppiep";
            } else {
                $msg = "Dear <b>Mr/Mrs " . $retailer->name . "</b>,<br><br>Goods for quotation INV# <b>" . $retailer->quotation_invoice_number . "</b> have been marked as received.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep";
            }

            \Mail::send(array(), array(), function ($message) use ($msg, $retailer) {
                $message->to($retailer->email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject(__('text.Invoice Status Changed'))
                    ->html($msg, 'text/html');
            });

            \Mail::send(array(), array(), function ($message) use ($admin_email, $retailer) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                    ->subject('Invoice Status Changed')
                    ->html("Recent activity: Goods for quotation INV# <b>" . $retailer->quotation_invoice_number . "</b> have been marked as received.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });

            Session::flash('success', __('text.Status Updated Successfully!'));
        }

        return redirect()->back();
    }


    public function CustomMarkDelivered($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        if ($user->can('custom-mark-delivered')) {
            $now = date('d-m-Y H:i:s');
            $check = custom_quotations::where('id', $id)->where('handyman_id', $user_id)->where('invoice', 1)->update(['delivered' => 1, 'delivered_date' => $now]);

            if ($check) {
                $client = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.user_id')->where('custom_quotations.id', $id)->select('users.*', 'custom_quotations.quotation_invoice_number')->first();

                $admin_email = $this->sl->admin_email;

                $link = url('/') . '/aanbieder/quotation-requests';

                if ($this->lang->lang == 'du') {
                    $msg = "Beste $client->name,<br><br>De status van je bestelling met factuur INV# <b>" . $client->quotation_invoice_number . "</b> is zojuist gewijzigd naar afgeleverd. Je kan de status naar ontvangen wijzigen in je <a href='$link'>dashboard</a>. Doe dit alleen als je de goederen hebt ontvangen. Mocht, je de goederen op de bezorgdatum niet hebben ontvangen neem dan contact met ons op.<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Vloerofferte";
                } else {
                    $msg = "Dear <b>Mr/Mrs " . $client->name . "</b>,<br><br>Goods for quotation INV# <b>" . $client->quotation_invoice_number . "</b> have been marked as delivered. You can change this quotation status to 'Received' if goods have been delivered to you. After 7 days from now on it will automatically be marked as 'Received'.<br><br>Kind regards,<br><br>Klantenservice<br><br> Vloerofferte";
                }

                \Mail::send(array(), array(), function ($message) use ($msg, $client) {
                    $message->to($client->email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@vloerofferte.nl')
                        ->subject(__('text.Invoice Status Changed'))
                        ->html($msg, 'text/html');
                });

                \Mail::send(array(), array(), function ($message) use ($admin_email, $client) {
                    $message->to($admin_email)
                        ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                        ->subject('Invoice Status Changed')
                        ->html("Recent activity: Goods for quotation INV# <b>" . $client->quotation_invoice_number . "</b> have been marked as delivered.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
                });

                Session::flash('success', __('text.Status Updated Successfully!'));
            }

            return redirect()->back();
        } else {
            return redirect()->route('user-login');
        }
    }


    public function CustomMarkReceived($id)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;

        $now = date('d-m-Y H:i:s');
        $check = custom_quotations::where('id', $id)->where('user_id', $user_id)->where('invoice', 1)->update(['received' => 1, 'received_date' => $now]);

        if ($check) {
            $handyman = custom_quotations::leftjoin('users', 'users.id', '=', 'custom_quotations.handyman_id')->where('custom_quotations.id', $id)->select('users.*', 'custom_quotations.quotation_invoice_number')->first();

            $admin_email = $this->sl->admin_email;

            if ($this->lang->lang == 'du') {
                $msg = "Beste $handyman->name,<br><br>Je klant heeft de status voor factuur INV# <b>" . $handyman->quotation_invoice_number . "</b> gewijzigd naar goederen ontvangen.<br><br>Met vriendelijke groeten,<br><br>Klantenservice<br><br> Piepiep";
            } else {
                $msg = "Dear <b>Mr/Mrs " . $handyman->name . "</b>,<br><br>Goods for quotation INV# <b>" . $handyman->quotation_invoice_number . "</b> have been marked as received.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep";
            }

            \Mail::send(array(), array(), function ($message) use ($msg, $handyman) {
                $message->to($handyman->email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
                    ->subject(__('text.Invoice Status Changed'))
                    ->html($msg, 'text/html');
            });

            \Mail::send(array(), array(), function ($message) use ($admin_email, $handyman) {
                $message->to($admin_email)
                    ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'info@pieppiep.com')
                    ->subject('Invoice Status Changed')
                    ->setBody("Recent activity: Goods for quotation INV# <b>" . $handyman->quotation_invoice_number . "</b> have been marked as received.<br><br>Kind regards,<br><br>Klantenservice<br><br> Pieppiep", 'text/html');
            });

            Session::flash('success', __('text.Status Updated Successfully!'));
        }

        return redirect()->back();
    }

    public function cash()
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

        $cash = new_quotations::leftjoin('payment_calculations', 'payment_calculations.quotation_id', '=', 'new_quotations.id')->whereIn('new_quotations.creator_id', $related_users)->where('payment_calculations.deleted_at', NULL)->select('payment_calculations.*')->get();

        return view('user.cash', compact('cash'));
    }

    public function tax()
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

        $tax = new_quotations::leftjoin('payment_calculations', 'payment_calculations.quotation_id', '=', 'new_quotations.id')->whereIn('new_quotations.creator_id', $related_users)->where('payment_calculations.deleted_at', NULL)->select('payment_calculations.*')->get();

        return view('user.tax', compact('tax'));
    }
}
