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
use App\Jobs\ExportQuotationsReeleezee;
use App\Jobs\ExportInvoicesReeleezee;
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
use Auth;
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
use App\Jobs\ExportPaymentsReeleezee;
use App\organizations;

class APIController extends Controller
{
    public $reeleezee_username;
    public $reeleezee_password;

    public function __construct()
    {
        $this->middleware('auth:user');

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user_id = Auth::guard('user')->user()->id;
            $reeleezee_credentials = User::where("id",$this->user_id)->select("reeleezee_username","reeleezee_password")->first();
            $this->reeleezee_username = $reeleezee_credentials->reeleezee_username;
            $this->reeleezee_password = $reeleezee_credentials->reeleezee_password;

            return $next($request);
        });

        // $this->reeleezee_username = "klaar";
        // $this->reeleezee_username = "woonkasteelnew";
        // $this->reeleezee_password = "Winkel4567";
    }

    public function createCommunicationChannelList($CommunicationChannelList,$phone_numbers,$email_address)
    {
        if($CommunicationChannelList)
        {
            $ccl_array = [];

            if(count($phone_numbers))
            {
                $p_array = [];
                $ccl = 0;

                foreach($CommunicationChannelList as $x => $temp)
                {
                    if($temp["CommunicationType"] == 9)
                    {
                        if(isset($phone_numbers[$ccl]))
                        {
                            $CommunicationChannelList[$x]["FormattedValue"] = $phone_numbers[$ccl];
                            $ccl_array[] = $x;
                            $p_array[] = $ccl;
                            $ccl++;
                        }
                    }
                    else
                    {
                        $ccl_array[] = $x;
                    }
                }

                foreach($phone_numbers as $p => $phone)
                {
                    if(!array_key_exists($p,$p_array))
                    {
                        $CommunicationChannelList[] = array("FormattedValue" => $phone,"CommunicationType" => 9);
                        $ccl_array[] = count($CommunicationChannelList) - 1;
                    }
                }
            }
            else
            {
                foreach($CommunicationChannelList as $x => $key)
                {
                    if($key["CommunicationType"] != 9)
                    {
                        $ccl_array[] = $x;
                    }
                }
            }

            foreach($CommunicationChannelList as $x => $key)
            {
                if(!array_key_exists($x,$ccl_array))
                {
                    unset($CommunicationChannelList[$x]);
                }
            }
        }
        else
        {
            $CommunicationChannelList = [];
            
            foreach($phone_numbers as $phone)
            {
                $CommunicationChannelList[] = array("FormattedValue" => $phone,"CommunicationType" => 9);
            }
        }

        $reversed_array = array_reverse($CommunicationChannelList,true);
        $find_last_email_index = array_search(10, array_column($reversed_array, 'CommunicationType'));

        if(is_numeric($find_last_email_index))
        {
            if($email_address)
            {
                $count_array = count($CommunicationChannelList) - 1;
                $index = $count_array - $find_last_email_index;
                $CommunicationChannelList[$index]["FormattedValue"] = $email_address;
            }
            else
            {
                foreach($CommunicationChannelList as $x => $key)
                {
                    if($key["CommunicationType"] == 10)
                    {
                        unset($CommunicationChannelList[$x]);
                    }
                }
            }
        }
        else
        {
            $CommunicationChannelList[] = array("FormattedValue" => $email_address,"CommunicationType" => 10);
        }

        return $CommunicationChannelList;
    }

    public function exportContactPersons($reeleezee_contact_persons,$contact_persons,$id,$headers)
    {
        $cp_array = [];

        foreach($contact_persons as $i => $cp)
        {
            $cp_phone_numbers = explode(",",$cp->phone);
            $cp_emails = explode(",",$cp->email);
            $cp_CommunicationChannelList = [];

            foreach($cp_phone_numbers as $cp_ph)
            {
                $cp_CommunicationChannelList[] = array("FormattedValue" => $cp_ph,"CommunicationType" => 9);
            }

            foreach($cp_emails as $cp_e)
            {
                $cp_CommunicationChannelList[] = array("FormattedValue" => $cp_e,"CommunicationType" => 10);
            }

            if(isset($reeleezee_contact_persons[$i]))
            {
                if($reeleezee_contact_persons[$i]["CommunicationChannelList"])
                {
                    foreach($reeleezee_contact_persons[$i]["CommunicationChannelList"] as $ccl => $channel)
                    {
                        if($channel["CommunicationType"] == 9 || $channel["CommunicationType"] == 10)
                        {
                            unset($reeleezee_contact_persons[$i]["CommunicationChannelList"][$ccl]);
                        }
                    }

                    $re_cp = array_values($reeleezee_contact_persons[$i]["CommunicationChannelList"]);
                    $cp_CommunicationChannelList = array_merge($cp_CommunicationChannelList,$re_cp);
                }

                $cp_array[] = $i;

                $cp_guid = $reeleezee_contact_persons[$i]["id"];
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons/{$cp_guid}";
            }
            else
            {
                if(function_exists('com_create_guid') === true)
                {
                    $random_guid = trim(com_create_guid(), '{}');
                }
                else
                {
                    $random_guid = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                }

                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons/{$random_guid}";
            }

            $data_contact_person = array('Name' => $cp->name, 'SearchName' => $cp->name, 'CommunicationChannelList' => $cp_CommunicationChannelList);
            $data_contact_person_json = json_encode($data_contact_person);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_contact_person_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);
        }

        foreach($reeleezee_contact_persons as $x => $cp)
        {
            if(!array_key_exists($x,$cp_array))
            {
                $cp_guid = $cp["id"];
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons/{$cp_guid}";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
            }
        }

        return true;
    }

    public function exportProjects($reeleezee_projects,$projects,$id,$headers)
    {
        $p_array = [];

        foreach($projects as $i => $pr)
        {
            if(isset($reeleezee_projects[$i]))
            {
                $p_array[] = $i;

                $p_guid = $reeleezee_projects[$i]["id"];
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Projects/{$p_guid}";
            }
            else
            {
                if(function_exists('com_create_guid') === true)
                {
                    $random_guid = trim(com_create_guid(), '{}');
                }
                else
                {
                    $random_guid = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                }
                
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Projects/{$random_guid}";
            }

            $data_project = array('Name' => $pr->name, 'IsActive' => $pr->is_active, 'BeginDate' => $pr->start_date ? $pr->start_date : "2023-02-16T00:00:00", 'EndDate' => $pr->end_date ? $pr->end_date : "2023-02-16T00:00:00", 'Description' => $pr->description, 'Comment' => $pr->comment, 'TotalAmount' => $pr->total);
            $data_project_json = json_encode($data_project);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_project_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);
        }

        foreach($reeleezee_projects as $x => $p)
        {
            if(!array_key_exists($x,$p_array))
            {
                $p_guid = $p["id"];
                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Projects/{$p_guid}";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
            }
        }

        return true;
    }

    public function TaxRates($headers)
    {
        $url = "https://apps.reeleezee.nl/api/v1/TaxRates";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function Ledgers($headers)
    {
        $url = "https://apps.reeleezee.nl/api/v1/Ledgers";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function DocumentCategoryAccounts($headers)
    {
        $url = "https://apps.reeleezee.nl/api/v1/DocumentCategoryAccounts?$"."expand=*($"."levels=max)";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function VatSettings($headers)
    {
        $url = "https://apps.reeleezee.nl/api/v1/AdministrationSettings";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function ExportQuotationsReeleezee(Request $request)
    {
        $username = $this->reeleezee_username;
        $password = $this->reeleezee_password;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $export_by = $request->export_by;
        $created_start_date = $request->created_start_date;
        $created_end_date = $request->created_end_date;
        $updated_start_date = $request->updated_start_date;
        $updated_end_date = $request->updated_end_date;
        $last_start_date = $request->last_start_date;

        if($export_by == 1)
        {
            if(!$created_start_date && !$created_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }

            if($created_end_date && ($created_start_date > $created_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }
        else if($export_by == 2)
        {
            if(!$updated_start_date && !$updated_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }
            
            if($updated_end_date && ($updated_start_date > $updated_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        ExportQuotationsReeleezee::dispatch($username,$password,$user,$related_users,$export_by,$created_start_date,$created_end_date,$updated_start_date,$updated_end_date,$last_start_date);

        Session::flash('success', __("text.Quotations will be exported in background."));
        return redirect()->back();
    }

    public function ExportInvoicesReeleezee(Request $request)
    {
        $username = $this->reeleezee_username;
        $password = $this->reeleezee_password;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $export_by = $request->export_by;
        $created_start_date = $request->created_start_date;
        $created_end_date = $request->created_end_date;
        $updated_start_date = $request->updated_start_date;
        $updated_end_date = $request->updated_end_date;
        $last_start_date = $request->last_start_date;

        if($export_by == 1)
        {
            if(!$created_start_date && !$created_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }

            if($created_end_date && ($created_start_date > $created_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }
        else if($export_by == 2)
        {
            if(!$updated_start_date && !$updated_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }
            
            if($updated_end_date && ($updated_start_date > $updated_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        ExportInvoicesReeleezee::dispatch($username,$password,$user,$related_users,$export_by,$created_start_date,$created_end_date,$updated_start_date,$updated_end_date,$last_start_date);

        Session::flash('success', __("text.Invoices will be exported in background."));
        return redirect()->back();
    }

    public function ExportPaymentsReeleezee(Request $request)
    {
        $username = $this->reeleezee_username;
        $password = $this->reeleezee_password;
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $export_by = $request->export_by;
        $created_start_date = $request->created_start_date;
        $created_end_date = $request->created_end_date;
        $updated_start_date = $request->updated_start_date;
        $updated_end_date = $request->updated_end_date;
        $last_start_date = $request->last_start_date;
        $paid_by_filter = $request->paid_by_filter;

        if($export_by == 1)
        {
            if(!$created_start_date && !$created_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }

            if($created_end_date && ($created_start_date > $created_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }
        else if($export_by == 2)
        {
            if(!$updated_start_date && !$updated_end_date)
            {
                Session::flash('unsuccess', __("text.At least one date is required."));
                return redirect()->back();
            }
            
            if($updated_end_date && ($updated_start_date > $updated_end_date))
            {
                Session::flash('unsuccess', __("text.Start date should not be bigger than end date."));
                return redirect()->back();
            }
        }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        // if($main_id)
        // {
        //     $user_id = $main_id;
        //     $all_employees = User::where("main_id",$main_id)->pluck("id");
        // }
        // else
        // {
        //     $all_employees = User::where("main_id",$user_id)->pluck("id");
        // }

        $quotation_payments = payment_calculations::leftjoin("new_quotations","new_quotations.id","=","payment_calculations.quotation_id")->leftjoin("customers_details","customers_details.id","=","new_quotations.customer_details")->whereIn("new_quotations.creator_id",$related_users)->where("new_quotations.invoice",0)->where("payment_calculations.paid_by","!=","Pending")->select("payment_calculations.*","customers_details.name","customers_details.family_name","new_quotations.quotation_invoice_number");
        $invoices_payments = invoice_payment_calculations::leftjoin("new_invoices","new_invoices.id","=","invoice_payment_calculations.invoice_id")->leftjoin("customers_details","customers_details.id","=","new_invoices.customer_details")->whereIn("new_invoices.creator_id",$related_users)->where("invoice_payment_calculations.paid_by","!=","Pending")->select("invoice_payment_calculations.*","customers_details.name","customers_details.family_name","new_invoices.reeleezee_guid as invoice_guid","new_invoices.invoice_number","new_invoices.negative_invoice");
        // $other_payments = other_payments::leftjoin("general_ledgers","general_ledgers.id","=","other_payments.general_ledger")->where(function($query) use ($user_id,$all_employees) {
        //     $query->whereIn('other_payments.user_id', $all_employees)->orWhere('other_payments.user_id',$user_id);
        // })->select("other_payments.*","general_ledgers.title","general_ledgers.number");
        $other_payments = other_payments::leftjoin("general_ledgers","general_ledgers.id","=","other_payments.general_ledger")->whereIn('other_payments.user_id',$related_users)->select("other_payments.*","general_ledgers.title","general_ledgers.number");

        if($export_by == 1)
        {
            if($created_start_date)
            {
                $quotation_payments = $quotation_payments->whereDate('payment_calculations.date', '>=', $created_start_date);
                $invoices_payments = $invoices_payments->whereDate('invoice_payment_calculations.date', '>=', $created_start_date);
                $other_payments = $other_payments->whereDate('other_payments.date', '>=', $created_start_date);
            }

            if($created_end_date)
            {
                $quotation_payments = $quotation_payments->whereDate('payment_calculations.date', '<=', $created_end_date);
                $invoices_payments = $invoices_payments->whereDate('invoice_payment_calculations.date', '<=', $created_end_date);
                $other_payments = $other_payments->whereDate('other_payments.date', '<=', $created_end_date);
            }
        }
        else if($export_by == 2)
        {
            if($updated_start_date)
            {
                $quotation_payments = $quotation_payments->whereDate('payment_calculations.updated_at', '>=', $updated_start_date);
                $invoices_payments = $invoices_payments->whereDate('invoice_payment_calculations.updated_at', '>=', $updated_start_date);
                $other_payments = $other_payments->whereDate('other_payments.updated_at', '>=', $updated_start_date);
            }

            if($updated_end_date)
            {
                $quotation_payments = $quotation_payments->whereDate('payment_calculations.updated_at', '<=', $updated_end_date);
                $invoices_payments = $invoices_payments->whereDate('invoice_payment_calculations.updated_at', '>=', $updated_start_date);
                $other_payments = $other_payments->whereDate('other_payments.updated_at', '>=', $updated_start_date);
            }
        }
        else
        {
            if($last_start_date)
            {
                $quotation_payments = $quotation_payments->whereDate('payment_calculations.date', '>=', $created_start_date);
                $invoices_payments = $invoices_payments->whereDate('invoice_payment_calculations.date', '>=', $created_start_date);
                $other_payments = $other_payments->whereDate('other_payments.date', '>=', $created_start_date);
            }
        }

        if($paid_by_filter)
        {
            $quotation_payments = $quotation_payments->where('payment_calculations.paid_by',$paid_by_filter);
            $invoices_payments = $invoices_payments->where('invoice_payment_calculations.paid_by',$paid_by_filter);
            $other_payments = $other_payments->where('other_payments.paid_by',$paid_by_filter);
        }

        $all_quotation_payments = $quotation_payments->withTrashed()->get();
        $all_invoices_payments = $invoices_payments->withTrashed()->get();
        $all_other_payments = $other_payments->withTrashed()->get();

        $quotation_payments = $quotation_payments->get();
        $invoices_payments = $invoices_payments->get();
        $other_payments = $other_payments->get();

        $payments = $quotation_payments->concat($invoices_payments)->concat($other_payments)->sortByDesc('date')->groupBy("paid_by");
        $payments_array = $all_quotation_payments->concat($all_invoices_payments)->concat($all_other_payments)->sortByDesc('date')->groupBy("paid_by")->toArray();

        $url = "https://apps.reeleezee.nl/api/v1/PaymentAccounts?$"."expand=*($"."levels=max)";

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($username.":".$password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        $array = array();
        $reeleezee_payment_accounts = array();

        foreach($payments as $payment)
        {
            $reeleezee_title = payment_accounts::where("title",$payment[0]->paid_by)->whereIn("user_id",$related_users)->pluck("reeleezee_title")->first();
            $reeleezee_payment_account = array_search($reeleezee_title, array_column($response["value"], 'PaymentAccountName'));

            if(!is_numeric($reeleezee_payment_account))
            {
                $array[] = $payment[0]->paid_by;
            }
            else
            {
                $reeleezee_payment_accounts[] = array("id" => $response["value"][$reeleezee_payment_account]["id"], "title" => $payment[0]->paid_by);
            }
        }

        if(count($array))
        {
            $msg = "Following payment method(s) does not match with any Reeleezee payment account names. Make sure you have configured reeleezee titles in payment account page: <a href=".route("payment-accounts").">here</a>";

            foreach($array as $i => $key)
            {
                $msg .= "<br><b>".$key."</b>";
            }

            Session::flash('unsuccess', $msg);
            return redirect()->back();
        }
        
        ExportPaymentsReeleezee::dispatch($username,$password,$user,$related_users,$reeleezee_payment_accounts,$payments_array,$export_by);

        Session::flash('success', __("text.Payments will be exported in background."));
        return redirect()->back();
    }

    public function ExportCustomersReeleezee($user,$related_users,$username,$password,$customer_id = NULL)
    {
        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', -1);

        $array_statuses = array();

        if($customer_id)
        {
            $our_customers = customers_details::where("id",$customer_id)->withTrashed()->get();
        }
        else
        {
            $our_customers = customers_details::whereIn("retailer_id",$related_users)->withTrashed()->get();
        }

        $url = "https://apps.reeleezee.nl/api/v1/customers";

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($username.":".$password)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_customers = json_decode($response, true);

        if(!isset($response_customers["value"]))
        {
            $array_statuses[] = $response_customers["Message"];
        }
        else
        {
            foreach($our_customers as $our)
            {
                if($customer_id || ((!$our->reeleezee_exported_at) || (strtotime($our->updated_at) > strtotime($our->reeleezee_exported_at))))
                {
                    $our_id = $our->id;
                    $our_name = $our->family_name ? $our->name . " " . $our->family_name : $our->name;
                    $our_email = $our->email_address;
                    $phone_numbers = $our->phone;
                    $phone_numbers = $phone_numbers ? explode(",",$phone_numbers) : [];
                    $address = $our->address;
                    $street_name = $our->street_name;
                    $street_number = $our->street_number;
                    $city = $our->city;
                    $postcode = $our->postcode;
                    $external_relation_number = $our->external_relation_number;
                    $entity_is_person = $our->entity_type == 1 ? true : false;
                    $entity_description = $our->description;
                    $entity_description_nl = $our->entity_description_nl;
                    $entity_name = $our->entity_name;
                    $is_deleted = $our->deleted_at;
                    $contact_persons = $our->contact_persons ? json_decode($our->contact_persons) : [];
                    $projects = $our->projects ? json_decode($our->projects) : [];
                    $CommunicationChannelList = NULL;
                    $AddressList = NULL;
                    $reeleezee_guid = strtolower($our->reeleezee_guid);
        
                    $search_by_id_index = array_search($reeleezee_guid, array_column($response_customers["value"], 'id'));
                    $search_by_email_index = array_search($our->email_address, array_column($response_customers["value"], 'EMail'));
                    $search_by_relation_number = array_search($external_relation_number, array_column($response_customers["value"], "IdentifierNumber"));

                    if(is_numeric($search_by_id_index) || ($our_email && is_numeric($search_by_email_index)) || ($external_relation_number && is_numeric($search_by_relation_number)))
                    {
                        $index = $search_by_id_index ? $search_by_id_index : ($search_by_email_index ? $search_by_email_index : $search_by_relation_number);
                        $id = $response_customers["value"][$index]["id"];
                        $email = $our->email_address;
        
                        $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}?$"."expand=*($"."levels=max)";
                    
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $response = json_decode($response, true);
        
                        if(isset($response["CommunicationChannelList"]) && $response["CommunicationChannelList"])
                        {
                            $CommunicationChannelList = $response["CommunicationChannelList"];
                        }
        
                        if(isset($response["AddressList"]) && $response["AddressList"])
                        {
                            $AddressList = $response["AddressList"];
                        }
                    }
                    else
                    {
                        if(function_exists('com_create_guid') === true)
                        {
                            $id = trim(com_create_guid(), '{}');
                        }
                        else
                        {
                            $id = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                        }
                    }
        
                    $url = "https://apps.reeleezee.nl/api/v1/EntityTypes";
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
        
                    $entity_index = array_search($entity_name, array_column($response["value"], 'Name'));
                    $EntityType = $response["value"][$entity_index];
        
                    $CommunicationChannelList = $this->createCommunicationChannelList($CommunicationChannelList,$phone_numbers,$our_email);
        
                    if($is_deleted)
                    {
                        $customer_status = "Inactief";
                    }
                    else
                    {
                        $customer_status = "Actief";
                    }
        
                    $url = "https://apps.reeleezee.nl/api/v1/CustomerStatuses";
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
        
                    $status_index = array_search($customer_status, array_column($response["value"], 'Name'));
                    $reeleezee_status = $response["value"][$status_index];
        
                    if($is_deleted)
                    {
                        $data = array('CustomerStatus' => $reeleezee_status);
                    }
                    else
                    {
                        $data = array('IdentifierNumber' => $external_relation_number, 'Name' => $our_name, 'SearchName' => $our_name, 'EntityType' => $EntityType, 'CommunicationChannelList' => $CommunicationChannelList, 'CustomerStatus' => $reeleezee_status);
                    }
        
                    $data_json = json_encode($data);
            
                    $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}?$"."expand=*($"."levels=max)";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
        
                    if($response)
                    {
                        $array_statuses[] = str_replace('Customer','External',$response["Message"]) . " " . $our_name;
                    }
                    else
                    {
                        if(!$is_deleted)
                        {
                            if($AddressList)
                            {
                                $address_guid = end($AddressList)["id"];
                                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Addresses/{$address_guid}";
                            }
                            else
                            {
                                if(function_exists('com_create_guid') === true)
                                {
                                    $random_guid = trim(com_create_guid(), '{}');
                                }
                                else
                                {
                                    $random_guid = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                                }

                                $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Addresses/{$random_guid}";
                            }
                
                            $data_address = array('FullAddress' => $address, 'Street' => $street_name, 'Number' => $street_number, 'City' => $city, 'Postcode' => $postcode);
                            $data_address_json = json_encode($data_address);
                    
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_address_json);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $response = json_decode($response, true);
                
                            $contact_person_url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/ContactPersons?$"."expand=*($"."levels=max)";
                    
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $contact_person_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $response = json_decode($response, true);
                
                            $reeleezee_contact_persons = NULL;
                
                            if(isset($response["value"]) && $response["value"])
                            {
                                $reeleezee_contact_persons = $response["value"];
                            }
                
                            if($reeleezee_contact_persons)
                            {
                                $this->exportContactPersons($reeleezee_contact_persons,$contact_persons,$id,$headers);
                            }
                            else
                            {
                                $reeleezee_contact_persons = [];
                                $this->exportContactPersons($reeleezee_contact_persons,$contact_persons,$id,$headers);
                            }
                
                            $project_url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}/Projects?$"."expand=*($"."levels=max)";
                    
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $project_url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $response = json_decode($response, true);
                
                            $reeleezee_projects = NULL;
                
                            if(isset($response["value"]) && $response["value"])
                            {
                                $reeleezee_projects = $response["value"];
                            }
                
                            if($reeleezee_projects)
                            {
                                $this->exportProjects($reeleezee_projects,$projects,$id,$headers);
                            }
                            else
                            {
                                $reeleezee_projects = [];
                                $this->exportProjects($reeleezee_projects,$projects,$id,$headers);
                            }
                        }

                        $our->reeleezee_guid = $id;
                        $our->reeleezee_exported_at = date('Y-m-d H:i:s');
                        $our->save();
                    }

                    if($customer_id)
                    {
                        $url = "https://apps.reeleezee.nl/api/v1/Customers/{$id}?$"."expand=*($"."levels=max)";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        return $response;
                    }
                }
            }

            $array_statuses[] = __("text.Customers exported successfully");
        }
        
        $msg = "";
        
        foreach($array_statuses as $i => $key)
        {
            $msg .= $i != 0 ? "<br>".$key : $key;
        }

        // \Mail::send(array(), array(), function ($message) use ($user,$msg) {
        //     $message->to($user->email)
        //         ->from(app()->make('mailFromAddress') ? app()->make('mailFromAddress') : 'noreply@pieppiep.com')
        //         ->subject(__("text.Export customers to reeleezee job status!"))
        //         ->html($msg, 'text/html');
        // });
    }
}



