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
use App\payment_accounts;
use App\payment_calculations;
use App\invoice_payment_calculations;
use App\other_payments;

class ExportPaymentsReeleezee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $username = null;
    private $password = null;
    private $user = null;
    private $related_users = [];
    public $timeout = 0;
    public $reeleezee_payment_accounts = null;
    public $payments = null;
    public $export_by = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($username,$password,$user,$related_users,$reeleezee_payment_accounts,$payments,$export_by)
    {
        $this->username = $username;
        $this->password = $password;
        $this->user = $user;
        $this->related_users = $related_users;
        $this->reeleezee_payment_accounts = $reeleezee_payment_accounts;
        $this->payments = $payments;
        $this->export_by = $export_by;
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
        $reeleezee_payment_accounts = $this->reeleezee_payment_accounts;
        $payments = $this->payments;
        $export_by = $this->export_by;
        $array_statuses = array();

        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', -1);

        $headers = array(
            'Content-Type:application/json',
            'Authorization: Basic '. base64_encode($username.":".$password)
        );

        foreach($payments as $groups)
        {
            $reeleezee_payment_account_index = array_search($groups[0]["paid_by"], array_column($reeleezee_payment_accounts, 'title'));
            $reeleezee_payment_account_id = $reeleezee_payment_accounts[$reeleezee_payment_account_index]["id"];

            $url = "https://apps.reeleezee.nl/api/v1/PaymentTransactions";
    
            $dataArray = ['orderby' => 'BookDate desc','$filter' => 'PaymentAccount/id eq '.$reeleezee_payment_account_id,'$count' => 'true'];
            $data = http_build_query($dataArray);
            $url = $url."?".$data;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response_transactions = curl_exec($ch);
            curl_close($ch);
            $response_transactions = json_decode($response_transactions, true);

            foreach($groups as $our)
            {
                if(($export_by == 1 || $export_by == 2) || ((!$our["reeleezee_exported_at"]) || (strtotime($our["updated_at"]) > strtotime($our["reeleezee_exported_at"]))))
                {
                    $our_id = $our["id"];
                    $our_date = date('Y-m-d H:i:s',strtotime($our["date"]));
                    $our_amount = $our["amount"];
                    $our_description = (isset($our["other_payments"]) ? ($our["description1"] . ($our["title"] ? ($our["description1"] ? ", ".$our["title"] : $our["title"]) : "")) : (($our["name"] ? $our["name"] . ($our["family_name"] ? " " . $our["family_name"] : "") . ", " : "") . (isset($our["payment_calculations"]) ? "QUO# ".$our["quotation_invoice_number"] : "INV# ".$our["invoice_number"])));
    
                    $is_deleted = $our["deleted_at"];
                    $reeleezee_guid = strtolower($our["reeleezee_guid"]);
    
                    if(!isset($response_transactions["value"]))
                    {
                        $search_by_id_index = "";
                    }
                    else
                    {
                        $search_by_id_index = array_search($reeleezee_guid, array_column($response_transactions["value"], 'id'));
                    }
    
                    if(is_numeric($search_by_id_index))
                    {
                        $id = $reeleezee_guid ? $reeleezee_guid : $response_transactions["value"][$search_by_id_index]["id"];
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
    
                    if($is_deleted || $our_amount == 0)
                    {
                        if(is_numeric($search_by_id_index))
                        {
                            $url = "https://apps.reeleezee.nl/api/v1/PaymentTransactions/{$id}";
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
                                $array_statuses[] = $response["Message"] . "-> Delete Error -> Payment ID: " . $our_id;
                            }
                        }
                    }
                    else
                    {
                        $data = array("Reference" => $our_description, "Amount" => $our_amount, "BookDate" => $our_date, "PaymentAccount" => array("id" => $reeleezee_payment_account_id));
                        $data_json = json_encode($data);
        
                        $url = "https://apps.reeleezee.nl/api/v1/PaymentTransactions/{$id}";
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
                            $array_statuses[] = $response["Message"] . "-> PUT Error -> Payment ID: " . $our_id;
                        }
                        else
                        {
                            if(isset($our["payment_calculations"]))
                            {
                                payment_calculations::where("id",$our_id)->update(["reeleezee_guid" => $id,"reeleezee_exported_at" => date('Y-m-d H:i:s')]);
                            }
                            elseif(isset($our["invoice_payment_calculations"]))
                            {
                                invoice_payment_calculations::where("id",$our_id)->update(["reeleezee_guid" => $id,"reeleezee_exported_at" => date('Y-m-d H:i:s')]);

                                $invoice_guid = $our["invoice_guid"];

                                $url = "https://apps.reeleezee.nl/api/v1/PaymentTransactions/{$id}?$"."expand=*($"."levels=max)";
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                $response = curl_exec($ch);
                                curl_close($ch);
                                $response_payment = json_decode($response, true);

                                if(!$response_payment["IsComplete"])
                                {
                                    $invoice_number = $our["invoice_number"];
                                    $invoice_number = explode('-', $invoice_number);
                
                                    if(count($invoice_number) > 2)
                                    {
                                        unset($invoice_number[1]);
                                    }
                
                                    $invoice_number = implode("-",$invoice_number);
                                    $invoice_number = str_replace('-', '', $invoice_number);
                                    
                                    $url = "https://apps.reeleezee.nl/api/v1/PaymentItems?%24filter=((contains(tolower(DocumentCategory%2FName)%2C%27".$invoice_number."%27)%20or%20contains(tolower(Document%2FReference)%2C%27".$invoice_number."%27)%20or%20contains(tolower(Document%2FHeader)%2C%27".$invoice_number."%27)%20or%20contains(tolower(Document%2FEntity%2FSearchName)%2C%27".$invoice_number."%27)))";
            
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                    $response_invoice = curl_exec($ch);
                                    curl_close($ch);
                                    $response_invoice = json_decode($response_invoice, true);
            
                                    if(count($response_invoice["value"]))
                                    {
                                        $document_id = $response_invoice["value"][0]["id"];
                                        $data = array("Type" => 15, "PaymentItemList" => array(array("id" => $document_id)), "LinkedAmount" => $our_amount);
                                        $data_json = json_encode($data);
            
                                        $url = "https://apps.reeleezee.nl/api/v1/PaymentTransactions/{$id}/actions";
                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        $response = curl_exec($ch);
                                        curl_close($ch);
                                        $response = json_decode($response, true);
                        
                                        if($response)
                                        {
                                            $array_statuses[] = $response["Message"] . "-> Payment linking Error -> Payment ID: " . $our_id . " -> Date: " . $our_date;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                other_payments::where("id",$our_id)->update(["reeleezee_guid" => $id,"reeleezee_exported_at" => date('Y-m-d H:i:s')]);
                            }
                        }
                    }
                }
            }
        }

        $array_statuses[] = __("text.Payments exported successfully");
        
        $msg = "";
        
        foreach($array_statuses as $i => $key)
        {
            $msg .= $i != 0 ? "<br>".$key : $key;
        }

        \Mail::send(array(), array(), function ($message) use ($user,$msg) {
            $message->to("tayyabkhurram62@gmail.com")
                ->from('noreply@pieppiep.com')
                ->subject(__("text.Export payments to reeleezee job status!"))
                ->html($msg, 'text/html');
        });
    }

    public function failed()
    {
        $user = $this->user;

        $msg = 'Job failed for exporting payments to Reeleezee <br> Retailer: ' . $user->name .' ('.$user->company_name.')';

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
