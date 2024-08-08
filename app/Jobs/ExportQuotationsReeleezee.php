<?php

namespace App\Jobs;

use Auth;
use App\customers_details;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\Http\Controllers\APIController;
use App\new_quotations;
use App\Products;
use App\items;
use App\Service;
use App\vats;
use App\general_ledgers;

class ExportQuotationsReeleezee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $username = null;
    private $password = null;
    private $user = null;
    private $related_users = [];
    public $timeout = 0;
    public $export_by = null;
    public $created_start_date = null;
    public $created_end_date = null;
    public $updated_start_date = null;
    public $updated_end_date = null;
    public $last_start_date = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username,$password,$user,$related_users,$export_by,$created_start_date,$created_end_date,$updated_start_date,$updated_end_date,$last_start_date)
    {
        $this->username = $username;
        $this->password = $password;
        $this->user = $user;
        $this->related_users = $related_users;
        $this->export_by = $export_by;
        $this->created_start_date = $created_start_date;
        $this->created_end_date = $created_end_date;
        $this->updated_start_date = $updated_start_date;
        $this->updated_end_date = $updated_end_date;
        $this->last_start_date = $last_start_date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $username = $this->username;
        $password = $this->password;
        $user = $this->user;
        $related_users = $this->related_users;
        $api_controller = new APIController();
        $export_by = $this->export_by;
        $created_start_date = $this->created_start_date;
        $created_end_date = $this->created_end_date;
        $updated_start_date = $this->updated_start_date;
        $updated_end_date = $this->updated_end_date;
        $last_start_date = $this->last_start_date;
        $array_statuses = array();

        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', -1);

        $url = "https://apps.reeleezee.nl/api/v1/offerings";

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
        $response_quotations = json_decode($response, true);

        if(!isset($response_quotations["value"]))
        {
            $array_statuses[] = $response_quotations["Message"];
        }
        else
        {
            $quotations = new_quotations::whereIn("creator_id",$related_users);

            if($export_by == 1)
            {
                if($created_start_date)
                {
                    $quotations = $quotations->whereDate('document_date', '>=', $created_start_date);
                }

                if($created_end_date)
                {
                    $quotations = $quotations->whereDate('document_date', '<=', $created_end_date);
                }
            }
            else if($export_by == 2)
            {
                if($updated_start_date)
                {
                    $quotations = $quotations->whereDate('updated_at', '>=', $updated_start_date);
                }

                if($updated_end_date)
                {
                    $quotations = $quotations->whereDate('updated_at', '<=', $updated_end_date);
                }
            }
            else
            {
                if($last_start_date)
                {
                    $quotations = $quotations->whereDate('document_date', '>=', $last_start_date);
                }
            }

            // $quotations = $quotations->whereDate('created_at', '=', date('Y-m-d'))->withTrashed()->get();
            $quotations = $quotations->withTrashed()->get();

            $vat_settings = $api_controller->VatSettings($headers);
            $vat_settings = $vat_settings["value"][0]["SalesVATIncluded"];

            $tax_rates = $api_controller->TaxRates($headers);
            $tax_rates = $tax_rates["value"];

            foreach($quotations as $our)
            {
                $customer_id = $our->customer_details;
    
                if(($export_by == 1 || $export_by == 2) || ((!$our->reeleezee_exported_at) || (strtotime($our->updated_at) > strtotime($our->reeleezee_exported_at))))
                {
                    $our_id = $our->id;
                    $our_description = strip_tags($our->description);
                    $is_deleted = $our->deleted_at;
                    $create_date = $our->created_at;
                    $document_date = $our->document_date;
                    $customer_entity = NULL;
                    $line_tax = null;
                    $ledger_id = null;
                    // $dca_id = null;
                    $line_project_id = null;
                    $quotation_invoice_number = $our->quotation_invoice_number;
                    $quotation_invoice_number = explode('-', $quotation_invoice_number);

                    if(count($quotation_invoice_number) > 2)
                    {
                        unset($quotation_invoice_number[1]);
                    }

                    $quotation_invoice_number = implode("-",$quotation_invoice_number);
                    $quotation_invoice_number = str_replace('-', '', $quotation_invoice_number);
                    $header = $our->regards;

                    $reeleezee_guid = strtolower($our->reeleezee_guid);
                    $search_by_id_index = array_search($reeleezee_guid, array_column($response_quotations["value"], 'id'));
        
                    if(is_numeric($search_by_id_index))
                    {
                        $index = $search_by_id_index;
                        $id = $reeleezee_guid ? $reeleezee_guid : $response_quotations["value"][$index]["id"];

                        $url = "https://apps.reeleezee.nl/api/v1/Offerings/{$id}";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $response_quotation = json_decode($response, true);

                        $is_vat_included = $response_quotations["value"][$index]["Status"] != 1 ? $response_quotation["IsVatIncluded"] : $vat_settings;
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

                        $is_vat_included = $vat_settings;
                    }

                    if($is_deleted)
                    {
                        if(is_numeric($search_by_id_index))
                        {
                            $url = "https://apps.reeleezee.nl/api/v1/Offerings/{$id}";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $response = json_decode($response, true);
    
                            if($response)
                            {
                                $array_statuses[] = $response["Message"] . " Deletion error-> Quotation No: " . $our->quotation_invoice_number;
                            }
                        }
                    }
                    else
                    {
                        if($customer_id)
                        {
                            $customer_entity = $api_controller->ExportCustomersReeleezee($user,$related_users,$username,$password,$customer_id);
                            $customer_entity = json_decode($customer_entity);
    
                            if(isset($customer_entity->ProjectList[0]))
                            {
                                $line_project_id = $customer_entity->ProjectList[0]->id;
                            }
                        }
    
                        $lines = $our->data;
                        $data_lines = array();
    
                        foreach($lines as $i => $line)
                        {
                            if(!$line->description)
                            {
                                if($line->product_id)
                                {
                                    $line->description = Products::where("id",$line->product_id)->pluck("title")->first();
                                }
                                else if($line->item_id)
                                {
                                    $line->description = items::where("id",$line->item_id)->pluck("cat_name")->first();
                                }
                                else if($line->service_id)
                                {
                                    $line->description = Service::where("id",$line->service_id)->pluck("title")->first();
                                }
                            }
    
                            if($line->ledger_id)
                            {
                                $ledgers = $api_controller->Ledgers($headers);
                                $ledgers = $ledgers["value"];
    
                                $ledger = general_ledgers::where("id",$line->ledger_id)->first();
                                $ledger_number = $ledger->number;
                                $ledger_desc = $ledger->title;
    
                                $find_ledger_index = array_search($ledger_number, array_column($ledgers, 'AccountNumber'),true);
    
                                if(is_numeric($find_ledger_index))
                                {
                                    $ledger_data = $ledgers[$find_ledger_index];
                                    $ledger_id = $ledger_data["id"];
                                }
                                else
                                {
                                    $ledger_data = array("IndentLevel" => 3, "AccountNumber" => $ledger_number, "ExternalGeneralLedgerNumber" => $ledger_number, "AccountType" => 1, "Description" => $ledger_desc, "Comment" => $ledger_desc);
                                    $ledger_json = json_encode($ledger_data);
    
                                    if(function_exists('com_create_guid') === true)
                                    {
                                        $ledger_id = trim(com_create_guid(), '{}');
                                    }
                                    else
                                    {
                                        $ledger_id = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                                    }
                
                                    $url = "https://apps.reeleezee.nl/api/v1/Ledgers/{$ledger_id}";
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                                    curl_setopt($ch, CURLOPT_POSTFIELDS,$ledger_json);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    $response = curl_exec($ch);
                                    curl_close($ch);
                                    $response = json_decode($response, true);
                                }
    
                                // $document_category_accounts = $api_controller->DocumentCategoryAccounts($headers);
                                // $document_category_accounts = $document_category_accounts["value"];
    
                                // foreach($document_category_accounts as $d => $dca)
                                // {
                                //     if($dca["Account"]["id"] == $ledger_id)
                                //     {
                                //         $document_category_accounts = $document_category_accounts[$d];
                                //         $dca_id = $document_category_accounts["id"];
                                //         break;
                                //     }
                                // }
                            }
    
                            if($line->vat_id)
                            {
                                $vat = vats::where("id",$line->vat_id)->first();
                                $vat_percentage = $vat->vat_percentage;
                                $vat_percentage = $vat_percentage/100;
                            }
                            else
                            {
                                $vat_percentage = 0.0;
                            }
    
                            $find_vat_index = array_search($vat_percentage, array_column($tax_rates, 'Percentage'));
        
                            if(is_numeric($find_vat_index))
                            {
                                $line_tax = $tax_rates[$find_vat_index];
                            }
    
                            if(function_exists('com_create_guid') === true)
                            {
                                $line_id = trim(com_create_guid(), '{}');
                            }
                            else
                            {
                                $line_id = strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
                            }
    
                            if(!$is_vat_included)
                            {
                                $price = $line->price_before_labor/(1+$vat_percentage);
                                $price = number_format((float)$price, 2, '.', '');
                            }
                            else
                            {
                                $price = $line->price_before_labor;
                            }
    
                            $data_lines[$i] = array('id' => $line_id, 'Quantity' => $line->qty, 'Price' => $price, 'Description' => $line->description, 'TaxRate' => $line_tax);
    
                            if($line->discount_option)
                            {
                                $data_lines[$i] += array('DiscountAmount' => abs($line->total_discount));
                            }
                            else
                            {
                                $data_lines[$i] += array('DiscountPercentage' => $line->discount/100);
                            }
                            
                            if($ledger_id)
                            {
                                $data_lines[$i] += array('Account' => array("id" => $ledger_id));
                            }
    
                            if($line_project_id)
                            {
                                $data_lines[$i] += array('Project' => array("id" => $line_project_id));
                            }
                        }
    
                        $data = array('Header' => $header, 'Bottom' => $our_description, 'Footer' => $our_description, 'Description' => $our_description, 'IsVatIncluded' => $is_vat_included, 'Reference' => $quotation_invoice_number, 'Entity' => $customer_entity, 'DocumentLineList' => $data_lines, 'BookDate' => $document_date, 'Date' => $document_date, 'Origin' => 2);
                        $data_json = json_encode($data);
                
                        $url = "https://apps.reeleezee.nl/api/v1/Offerings/{$id}?$"."expand=*($"."levels=max)";
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
                            $array_statuses[] = $response["Message"] . " Create/Update error-> Quotation No: " . $our->quotation_invoice_number;
                        }
                        else
                        {
                            $our->reeleezee_guid = $id;
                            $our->reeleezee_exported_at = date('Y-m-d H:i:s');
                            $our->save();
                        }

                        $action_flag = 0;

                        if($our->draft)
                        {
                            $data_status = array('Type' => 19);

                            if((!is_numeric($search_by_id_index) || (isset($response_quotation) && $response_quotation["Status"] == 1)))
                            {
                                $action_flag = 1;
                            }
                        }
                        else
                        {
                            $data_status = array('Type' => 17);

                            if(isset($response_quotation) && $response_quotation["Status"] == 2)
                            {
                                $action_flag = 1;
                            }
                        }

                        if(!$action_flag)
                        {
                            $data_status_json = json_encode($data_status);
                    
                            $url = "https://apps.reeleezee.nl/api/v1/Offerings/{$id}/actions";
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_status_json);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            $response = json_decode($response, true);
                            
                            if($response)
                            {
                                $array_statuses[] = $response["Message"] . " Action error-> Quotation No: " . $our->quotation_invoice_number;
                            }
                        }
                    }
                }
            }

            $array_statuses[] = __("text.Quotations exported successfully");
        }
        
        $msg = "";
        
        foreach($array_statuses as $i => $key)
        {
            $msg .= $i != 0 ? "<br>".$key : $key;
        }

        \Mail::send(array(), array(), function ($message) use ($user,$msg) {
            $message->to("tayyabkhurram62@gmail.com")
                ->from('noreply@pieppiep.com')
                ->subject(__("text.Export quotations to reeleezee job status!"))
                ->html($msg, 'text/html');
        });
    }

    public function failed()
    {
        $user = $this->user;

        $msg = 'Job failed for exporting quotations to Reeleezee <br> Retailer: ' . $user->name .' ('.$user->company_name.')';

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
