<?php

namespace App\Jobs;

use App\colors;
use App\customers_details;
use App\new_quotations;
use App\new_quotations_data;
use App\new_orders;
use App\new_orders_features;
use App\new_orders_sub_products;
use App\new_orders_calculations;
use App\new_quotations_features;
use App\new_quotations_sub_products;
use App\product;
use App\product_features;
use App\product_ladderbands;
use App\product_models;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\saved_emails;
use App\organizations;
use App\EmailSetting;
use Illuminate\Support\Facades\Crypt;

class SendOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    private $user = null;
    private $organization = null;
    private $related_users = null;
    private $mail_subject = null;
    private $mail_body = null;
    private $retailer_delivery_date = null;
    private $deliver_to = 1;
    private $ccs = null;
    private $encoded_ccs = null;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$user,$organization,$related_users,$mail_subject,$mail_body,$retailer_delivery_date,$deliver_to,$ccs,$encoded_ccs)
    {
        $this->id = $id;
        $this->user = $user;
        $this->organization = $organization;
        $this->related_users = $related_users;
        $this->mail_subject = $mail_subject;
        $this->mail_body = $mail_body;
        $this->retailer_delivery_date = $retailer_delivery_date;
        $this->deliver_to = $deliver_to;
        $this->ccs = $ccs;
        $this->encoded_ccs = $encoded_ccs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $organization = $this->organization;
        $organization_id = $organization->id;
        $fromAddress = "";
        $emailSettings = EmailSetting::where('organization_id', $organization_id)->first();

        if ($emailSettings) {
            // Override the mail configuration
            $emailSettings->password = Crypt::decryptString($emailSettings->password);
    
            config([
                'mail.driver' => 'smtp',
                'mail.host' => $emailSettings->host,
                'mail.port' => $emailSettings->port,
                'mail.encryption' => $emailSettings->encryption,
                'mail.username' => $emailSettings->username,
                'mail.password' => $emailSettings->password
            ]);

            $fromAddress = $emailSettings->username;
        }

        $id = $this->id;
        $user = $this->user;
        $user_id = $user->id;
        // $main_id = $user->main_id;
        $mail_subject = $this->mail_subject;
        $mail_body = $this->mail_body;
        $retailer_delivery_date = $this->retailer_delivery_date;
        $deliver_to = $this->deliver_to;
        $ccs = $this->ccs;
        $encoded_ccs = $this->encoded_ccs;
        $sup_mail = array();
        $related_users = $this->related_users;

        // if($main_id)
        // {
        //     $user = User::where('id',$main_id)->first();
        //     $user_id = $user->id;
        // }

        $user->organization_email = $user->organization->email;

        $retailer_name = $user->name . ' ' . $user->family_name;
        $company_name = $organization->company_name;
        $company_email = $organization->email;

        $check = new_quotations::where('id',$id)->whereIn('creator_id',$related_users)->first();

        if($check->customer_details)
        {
            $client = customers_details::leftjoin('users','users.id','=','customers_details.user_id')->where('customers_details.id', $check->customer_details)->select('customers_details.*','users.email','users.fake_email')->first();
        }
        else
        {
            $client = '';
        }

        $suppliers = new_orders::leftjoin('new_quotations','new_quotations.id','=','new_orders.quotation_id')->where('new_quotations.id',$id)->where('new_quotations.deleted_at',NULL)->whereIn('new_quotations.creator_id',$related_users)->pluck('new_orders.supplier_id');
        $suppliers = $suppliers->unique();

        foreach ($suppliers as $i => $key)
        {
            $supplier_data = organizations::where('id',$key)->first();
            $supplier_email = $supplier_data->email;
            // $counter = $supplier_data->counter_order;

            $request = new_quotations::where('id',$id)->select('new_quotations.*','new_quotations.subtotal as total_amount')->first();
            $request->products = new_orders::where('quotation_id',$id)->where('supplier_id',$key)->get();

            $form_type = $request->form_type;
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
            $deliver_to1 = array();
            $feature_sub_titles = array();
            $calculator_rows = array();

            foreach ($request->products as $x => $temp)
            {
                $feature_sub_titles[$x][] = array();
                $product_titles[] = product::where('id',$temp->product_id)->pluck('title')->first();
                $color_titles[] = colors::where('id',$temp->color)->pluck('title')->first();
                $model_titles[] = product_models::where('id',$temp->model_id)->pluck('model')->first();
                $qty[] = $temp->qty;
                $width[] = $temp->width;
                $width_unit[] = $temp->width_unit;
                $height[] = $temp->height;
                $height_unit[] = $temp->height_unit;
                $delivery[] = date('d-m-Y',strtotime($retailer_delivery_date));
                $labor_impact[] = $temp->labor_impact;
                $price_before_labor[] = $temp->price_before_labor;
                $discount[] = $temp->discount;
                $rate[] = $temp->rate;
                $labor_discount[] = $temp->labor_discount;
                $total[] = $temp->amount;
                $total_discount[] = $temp->total_discount;
                $deliver_to1[] = $deliver_to;

                if($form_type == 1)
                {
                    $calculator_rows[] = new_orders_calculations::where('order_id',$temp->id)->get();
                }

                $features = new_orders_features::where('order_data_id',$temp->id)->get();

                foreach ($features as $f => $feature)
                {
                    if($feature->feature_id == 0)
                    {
                        if($feature->ladderband)
                        {
                            $sub_product = new_orders_sub_products::where('feature_row_id',$feature->id)->get();

                            foreach ($sub_product as $sub)
                            {
                                if($sub->size1_value == 1 || $sub->size2_value == 1)
                                {
                                    $sub_titles[$x] = product_ladderbands::where('product_id',$temp->product_id)->where('id',$sub->sub_product_id)->first();

                                    if($sub->size1_value == 1)
                                    {
                                        $sub_titles[$x]->size = '38mm';
                                    }
                                    else
                                    {
                                        $sub_titles[$x]->size = '25mm';
                                    }
                                }
                            }
                        }
                    }

                    $feature_sub_titles[$x][] = product_features::leftjoin('features','features.id','=','product_features.heading_id')->where('product_features.product_id',$temp->product_id)->where('product_features.id',$feature->feature_sub_id)->select('product_features.*','features.title as main_title','features.order_no','features.id as f_id')->first();
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
            $request->deliver_to = $deliver_to1;

            $quotation_invoice_number = $request->quotation_invoice_number;
            $order_number = new_orders::where('quotation_id',$id)->where('supplier_id',$key)->first();
            $order_number = $order_number->order_number;

            $supplier_orders_folder_path = public_path() . '/assets/supplierQuotations/' . $key;

            if (!file_exists($supplier_orders_folder_path)) {
                mkdir($supplier_orders_folder_path, 0775, true);
            }
            
            $filename = $order_number . '.pdf';
            $file = $supplier_orders_folder_path . '/' . $filename;

            // new_quotations_data::where('quotation_id',$id)->where('supplier_id',$key)->update(['order_sent' => 1,'order_date' => date('Y-m-d')]);
            new_orders::where('quotation_id',$id)->where('supplier_id',$key)->update(['order_sent' => 1,'order_date' => date('Y-m-d'),'retailer_delivery_date' => date('Y-m-d',strtotime($retailer_delivery_date)),'deliver_to' => $deliver_to]);

            ini_set('max_execution_time', 180);

            $date = $request->created_at;
            $role = 'supplier1';

            if($form_type == 1)
            {
                $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('supplier_data','calculator_rows','form_type','role','product_titles','color_titles','model_titles','feature_sub_titles','sub_titles','date','client','user','request','quotation_invoice_number','order_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160]);
            }
            else
            {
                $pdf = PDF::loadView('user.pdf_new_quotation', compact('supplier_data','form_type','role','comments','product_titles','color_titles','model_titles','feature_sub_titles','sub_titles','date','client', 'user', 'request', 'quotation_invoice_number','order_number'))->setPaper('letter', 'landscape')->setOptions(['dpi' => 160]);
            }

            $pdf->save($file);

            // if($this->attempts() == 1)
            // {
            //     $supplier_data->counter_order = $counter + 1;
            //     $supplier_data->save();
            // }

            $sup_mail[] = array('email' => $supplier_email,'file' => $file,'file_name' => $filename,'order_number' => $order_number);
        }

        foreach ($sup_mail as $sup)
        {
            $mail_subject = str_replace('{order_nummer}',$sup['order_number'],$mail_subject);
            $mail_body = str_replace('{aan_voornaam}',$company_name,$mail_body);
            $mail_body = str_replace('{order_nummer}',$sup['order_number'],$mail_body);
            $mail_body = str_replace('{van_voornaam}',$retailer_name,$mail_body);
            $mail_body = str_replace('{van_bedrijfsnaam}',$company_name,$mail_body);

            \Mail::send('user.global_mail',
                array(
                    'msg' => $mail_body,
                ), function ($message) use ($sup,$mail_subject,$company_name,$company_email,$ccs,$fromAddress) {
                    $message->to($sup['email'])
                        ->from($fromAddress ? $fromAddress : 'noreply@pieppiep.com', $company_name)
                        ->cc($ccs)
                        ->replyTo($company_email, $company_name)
                        ->subject($mail_subject)
                        ->attach($sup['file'], [
                            'as' => $sup['file_name'],
                            'mime' => 'application/pdf',
                        ]);
                }
            );

            $save_email = new saved_emails;
            $save_email->mail_to = $sup['email'];
            $save_email->ccs = $encoded_ccs;
            $save_email->type = "order";
            $save_email->subject = $mail_subject;
            $save_email->body = $mail_body;
            $save_email->quotation_id = $id;
            $save_email->deliver_to = $deliver_to;
            $save_email->delivery_date = $retailer_delivery_date;
            $save_email->save();

            /*\Mail::send('user.custom_quotation_mail',
                array(
                    'supplier' => $company_name,
                    'retailer' => $retailer_name,
                    'company_name' => $company_name,
                    'order_number' => $sup['order_number'],
                    'type' => 'new-quotation'
                ), function ($message) use ($sup,$fromAddress) {
                    $message->from($fromAddress ? $fromAddress : 'info@pieppiep.com');
                    $message->to($sup['email'])->subject('New order received!');

                    $message->attach($sup['file'], [
                        'as' => $sup['file_name'],
                        'mime' => 'application/pdf',
                    ]);

                });*/
        }

        new_quotations::where('id',$id)->update(['processing' => 0, 'finished' => 1]);
    }

    public function failed()
    {
        $id = $this->id;
        new_quotations::where('id',$id)->update(['processing' => 0, 'failed' => 1]);

        $msg = 'Job failed for sending order to supplier(s) <br> Quotation ID: ' . $id;

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
