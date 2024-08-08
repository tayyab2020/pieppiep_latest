<?php

namespace App\Jobs;

use App\colors;
use App\customers_details;
use App\new_quotations;
use App\new_quotations_data;
use App\new_quotations_features;
use App\new_quotations_sub_products;
use App\new_orders;
use App\new_orders_features;
use App\new_orders_sub_products;
use App\new_orders_calculations;
use App\product;
use App\product_features;
use App\product_ladderbands;
use App\product_models;
use App\User;
use PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\user_languages;
use App\Language;
use App\organizations;
use App\EmailSetting;
use Illuminate\Support\Facades\Crypt;

class UpdateDates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $request = null;
    private $user = null;
    public $timeout = 0;
    public $lang;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request,$user)
    {
        $this->request = $request;
        $this->user = $user;

        $locale = \App::getLocale() == "en" ? "eng" : "du";
        $this->lang = Language::where('lang', '=', $locale)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $supplier = $this->user;
        $organization_id = $supplier->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $supplier_id = $organization->id;
        $supplier_company_name = $organization->company_name;
        $supplier_email = $organization->email;

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

        $request = $this->request;
        $rows = $request['data_id'];
        $delivery_dates = $request['delivery_dates'];

        $is_approved = new_orders::where('id',$rows[0])->pluck('approved')->first();

        $invoice_id = $request['invoice_id'];
        
        $user = User::where('id',$request['creator_id'])->first();
        $user_id = $user->id;

        $retailer_organization_id = $user->organization->id;
        $retailer_organization = organizations::findOrFail($retailer_organization_id);
        $retailer_related_users = $retailer_organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user->organization_email = $user->organization->email;
        $retailer_email = $retailer_organization->email;
        $retailer_company = $retailer_organization->company_name;

        $check = new_quotations::where('id',$invoice_id)->whereIn('creator_id',$retailer_related_users)->first();

        if($check->customer_details)
        {
            $client = customers_details::leftjoin('users','users.id','=','customers_details.user_id')->where('customers_details.id', $check->customer_details)->select('customers_details.*','users.email','users.fake_email')->first();
        }
        else
        {
            $client = '';
        }

        $request = new_quotations::where('id',$invoice_id)->first();
        $request->products = new_orders::where('quotation_id',$invoice_id)->where('supplier_id',$supplier_id)->get();
        $order_number = $request->products[0]->order_number;
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
        $feature_sub_titles = array();
        $calculator_rows = array();
        $deliver_to = array();

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
            $delivery[] = $delivery_dates[$x];
            $deliver_to[] = $temp->deliver_to;

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

                $feature_sub_titles[$x][] = product_features::leftjoin('features','features.id','=','product_features.heading_id')->where('product_features.product_id',$temp->product_id)->where('product_features.heading_id',$feature->feature_id)->select('product_features.*','features.title as main_title','features.order_no','features.id as f_id')->first();
                $comments[$x][] = $feature->comment;
            }
        }

        $request->qty = $qty;
        $request->width = $width;
        $request->width_unit = $width_unit;
        $request->height = $height;
        $request->height_unit = $height_unit;
        $request->delivery_date = $delivery;
        $request->deliver_to = $deliver_to;

        $supplier_approved_orders_folder_path = public_path() . '/assets/supplierApproved/' . $supplier_id;

        if (!file_exists($supplier_approved_orders_folder_path)) {
            mkdir($supplier_approved_orders_folder_path, 0775, true);
        }

        $quotation_invoice_number = $request->quotation_invoice_number;
        $filename = $order_number . '.pdf';
        $file = $supplier_approved_orders_folder_path . '/' . $filename;

        ini_set('max_execution_time', 180);

        $date = $request->created_at;
        $role = 'supplier';
        $supplier_data = $organization;

        if($form_type == 1)
        {
            $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('supplier_data','calculator_rows','form_type','order_number','role','comments','product_titles','color_titles','model_titles','feature_sub_titles','sub_titles','date','client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160]);
        }
        else
        {
            $pdf = PDF::loadView('user.pdf_new_quotation', compact('supplier_data','form_type','order_number','role','comments','product_titles','color_titles','model_titles','feature_sub_titles','sub_titles','date','client', 'user', 'request', 'quotation_invoice_number'))->setPaper('letter', 'landscape')->setOptions(['dpi' => 160]);
        }

        $pdf->save($file);

        if($is_approved)
        {
            if($this->lang->lang == 'du')
            {
                $msg = " Beste".$retailer_company.", <br><br>Update: leverdatum is bijgewerkt door ".$supplier_company_name." voor offerte: <b>" . $quotation_invoice_number . "</b>.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
            }
            else
            {
                $msg = " Dear ".$retailer_company.", <br><br>Recent activity: delivery date(s) has been updated by supplier ".$supplier_company_name." for quotation: <b>" . $quotation_invoice_number . "</b>.<br><br>Kind regards,<br><br>Customer sercvice<br><br> Pieppiep";
            }
            
            \Mail::send(array(), array(), function ($message) use ($retailer_email, $msg, $retailer_company, $supplier_company_name, $quotation_invoice_number, $supplier_email, $fromAddress) {
                $message->to($retailer_email)
                    ->from($fromAddress ? $fromAddress : 'noreply@pieppiep.com', $supplier_company_name)
                    ->replyTo($supplier_email, $supplier_company_name)
                    ->subject(__('text.Order Approved!'))
                    ->html($msg, 'text/html');
            });
        }
        else
        {
            if($this->lang->lang == 'du')
            {
                $msg= "Beste ".$retailer_company.", <br><br> Update: je bestelling is bevestigd door <b>".$supplier_company_name."</b> voor offerte: <b>" . $quotation_invoice_number . "</b>.<br><br>Met vriendelijke groet,<br><br>Klantenservice<br><br> Pieppiep";
            } 
            else
            {
                $msg= "Dear ".$retailer_company.", <br><br> Recent activity: order has been approved by supplier <b>".$supplier_company_name."</b> for quotation: <b>" . $quotation_invoice_number . "</b>.<br><br>Kind regards,<br><br>Customer service<br><br> Pieppiep"; 
            }
            
            \Mail::send(array(), array(), function ($message) use ($retailer_email, $msg, $retailer_company, $supplier_company_name, $quotation_invoice_number, $supplier_email, $fromAddress) {
                $message->to($retailer_email)
                    ->from($fromAddress ? $fromAddress : 'noreply@pieppiep.com', $supplier_company_name)
                    ->replyTo($supplier_email, $supplier_company_name)
                    ->subject(__('text.Order Approved!'))
                    ->html($msg, 'text/html');
            });
        }

        foreach ($rows as $i => $key)
        {
            new_orders::where('id',$key)->update(['approved' => 1, 'delivery_date' => $delivery_dates[$i], 'processing' => 0, 'finished' => 1]);
        }

        $approved = new_orders::where('quotation_id',$invoice_id)->get();
        $flag = 0;

        foreach ($approved as $key)
        {
            if(!$key->approved)
            {
                $flag = 1;
            }
        }

        if($flag == 0)
        {
            new_quotations::where('id',$invoice_id)->update(['received' => 1]);
        }
    }

    public function failed()
    {
        $request = $this->request;
        $rows = $request['data_id'];
        new_orders::whereIn('id',$rows)->update(['processing' => 0, 'failed' => 1]);

        $msg = 'Job failed for updating delivery dates in pdfs <br> Quotation Data IDs: ' . implode(",",$rows);

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
