<?php

namespace App\Jobs;

use App\colors;
use App\customers_details;
use App\new_quotations;
use App\new_quotations_data;
use App\new_quotations_data_calculations;
use App\new_orders;
use App\new_orders_features;
use App\new_orders_sub_products;
use App\new_orders_calculations;
use App\new_quotations_features;
use App\new_quotations_sub_products;
use App\payment_calculations;
use App\quotation_appointments;
use App\product;
use App\product_features;
use App\product_ladderbands;
use App\product_models;
use App\User;
use App\Service;
use App\items;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\vats;
use App\organizations;

class CopyQuotation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id = null;
    private $user = null;
    private $quotation = null;
    private $organization = null;
    private $related_users = null;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id,$user,$organization,$related_users,$quotation)
    {
        $this->id = $id;
        $this->user = $user;
        $this->quotation = $quotation;
        $this->organization = $organization;
        $this->related_users = $related_users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $user = $this->user;
        $quotation = $this->quotation;
        $organization = $this->organization;
        $related_users = $this->related_users;
        $user_id = $user->id;
        $sup_mail = array();

        $client = customers_details::leftjoin('users','users.id','=','customers_details.user_id')->where('customers_details.id',$quotation->customer_details)->select('customers_details.*','users.email','users.fake_email')->first();

        $counter = $user->counter;
        $quotation_invoice_number = date("Y") . '-' . sprintf('%06u', $counter);
        $check_q_number = new_quotations::where('quotation_invoice_number',$quotation_invoice_number)->whereIn('creator_id',$related_users)->first();
        
        while($check_q_number)
        {
            $counter = $counter + 1;
            $quotation_invoice_number = date("Y") . '-' . sprintf('%06u', $counter);
            $check_q_number = new_quotations::where('quotation_invoice_number',$quotation_invoice_number)->whereIn('creator_id',$related_users)->first();
        }
        // $client_id = $client && $client->customer_number ? "-" . sprintf('%04u', $client->customer_number) : "";
        // $quotation_invoice_number = $user->quotation_client_id ? date("Y") . $client_id . '-' . sprintf('%06u', $counter) : date("Y") . '-' . sprintf('%06u', $counter);

        $quotation->quotation_invoice_number = $quotation_invoice_number;
        $quotation->status = 0;
        $quotation->copied_from = $id;
        $new_quotation = $quotation->replicate($except = ['admin_quotation_sent','approved','accepted','paid','invoice','invoice_sent','delivered','retailer_delivered','received','customer_received','processing','finished','failed','ask_customization','review_text','commission_invoice_number','commission_percentage','commission','invoice_number','delivered_date','received_date','invoice_date','mail_to','mail_invoice_to','accept_date','payment_date','payment_id','total_receive','draft_token','reeleezee_guid','reeleezee_exported_at']);
        $new_quotation->setTable('new_quotations');
        $new_quotation->save();

        $counter = $counter + 1;
        $user->organization->update(['counter' => $counter]);
        
        $date = $new_quotation->document_date;
        $retailer_id = $new_quotation->creator_id;
        $organization_id = $organization->id;

        // $appointments_data = quotation_appointments::where('quotation_id',$id)->get();

        // foreach($appointments_data as $ap)
        // {
        //     $ap->quotation_id = $new_quotation->id;
        //     $appointments = $ap->replicate();
        //     $appointments->setTable('quotation_appointments');
        //     $appointments->save();
        // }

        $payment_calculations = new payment_calculations;
        $payment_calculations->quotation_id = $new_quotation->id;
        $payment_calculations->percentage = 100.00;
        $payment_calculations->amount = $new_quotation->grand_total;
        $payment_calculations->date = date('Y-m-d', strtotime(' +1 day'));
        $payment_calculations->paid_by = "Pending";
        $payment_calculations->description = "By accepting";
        $payment_calculations->save();

        // $payment_calculations_data = payment_calculations::where('quotation_id',$id)->get();

        // foreach($payment_calculations_data as $pc)
        // {
        //     $pc->quotation_id = $new_quotation->id;
        //     $payment_calculations = $pc->replicate();
        //     $payment_calculations->setTable('payment_calculations');
        //     $payment_calculations->save();
        // }

        $quotation_data = new_quotations_data::where("quotation_id",$id)->get();

        $request = new_quotations::where('id',$id)->select('new_quotations.*','new_quotations.subtotal as total_amount')->first();
        $request->products = new_quotations_data::where('quotation_id',$id)->get();
        $delivery_date = $request->delivery_date ? date('d-m-Y',strtotime($request->delivery_date)) . ' - ' . date('d-m-Y',strtotime($request->delivery_date_end)) : "";
        $installation_date = $request->installation_date ? date('d-m-Y',strtotime($request->installation_date)) . ' - ' . date('d-m-Y',strtotime($request->installation_date_end)) : "";

        $product_titles = array();
        $product_descriptions = array();
        $calculations = array();
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
        $suppliers = array();
        $order_numbers = array();
        $box_quantity = array();
        $measure = array();
        $order_supplier_ids = array();
        $vat_percentages = array();

        $orderPDF_delivery_date = $request->products[0]->delivery_date;

        foreach ($request->products as $x => $temp)
        {
            $vat_percentages[] = vats::where('id',$temp->vat_id)->pluck('vat_percentage')->first();

            $temp->quotation_id = $new_quotation->id;
            $new_quotation_data = $temp->replicate($except = ['order_number','processing','finished','failed','approved','delivered','order_date','order_sent']);
            $new_quotation_data->setTable('new_quotations_data');
            $new_quotation_data->save();

            $calculations = new_quotations_data_calculations::where('quotation_data_id',$temp->id)->get();

            foreach($calculations as $cal)
            {
                $cal->quotation_data_id = $new_quotation_data->id;
                $new_quotation_data_calculation = $cal->replicate();
                $new_quotation_data_calculation->setTable('new_quotations_data_calculations');
                $new_quotation_data_calculation->save();
            }

            $feature_sub_titles[$x][] = array();

            if ($temp->item_id != 0) {
                $product_titles[] = items::where('id',$temp->item_id)->pluck('cat_name')->first();
                $color_titles[] = '';
                $model_titles[] = '';
                $suppliers[] = NULL;
                $order_supplier_ids[] = '';
                $order_number = '';
            }
            elseif ($temp->service_id != 0) {
                $product_titles[] = Service::where('id',$temp->service_id)->pluck('title')->first();
                $color_titles[] = '';
                $model_titles[] = '';
                $suppliers[] = NULL;
                $order_supplier_ids[] = '';
                $order_number = '';
            }
            else
            {
                if($temp->product_id != 0)
                {
                    $search_supplier = in_array($temp->supplier_id,$order_supplier_ids); //this search should always be before the part where order supplier id array is changed
                    $found_index = array_search($temp->supplier_id,$order_supplier_ids); //Should always be above order supplier id array change
                    $product_titles[] = product::where('id',$temp->product_id)->pluck('title')->first();
                    $color_titles[] = colors::where('id',$temp->color)->pluck('title')->first();
                    $model_titles[] = product_models::where('id',$temp->model_id)->pluck('model')->first();
                    $suppliers[] = organizations::where('id',$temp->supplier_id)->first();
                    $order_supplier_ids[] = $temp->supplier_id;

                    if(!$search_supplier)
                    {
                        $counter_order = $suppliers[$x]->counter_order;
                        $order_number = $suppliers[$x]->order_client_id ? date("Y") . "-" . sprintf('%04u', $suppliers[$x]->id) . '-' . sprintf('%06u', $counter_order) : date("Y") . '-' . sprintf('%06u', $counter_order);
                        $counter_order = $counter_order + 1;
                        $suppliers[$x]->update(['counter_order' => $counter_order]);
                    }
                    else
                    {
                        $order_number = $order_numbers[$found_index];
                    }
                }
                else
                {
                    $product_titles[] = '';
                    $color_titles[] = '';
                    $model_titles[] = '';
                    $suppliers[] = NULL;
                    $order_supplier_ids[] = '';
                    $order_number = '';
                }
            }

            if($order_number)
            {
                new_quotations_data::where("id",$new_quotation_data->id)->update(["order_number" => $order_number]);
            }
            
            $order_numbers[$x] = $order_number;
            $calculations1[$x] = $temp->calculations()->get();
            
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
            $box_quantity[] = $temp->box_quantity;
            $measure[] = $temp->measure;

            $features = new_quotations_features::where('quotation_data_id',$temp->id)->get();

            foreach ($features as $f => $feature)
            {
                $feature->quotation_data_id = $new_quotation_data->id;
                $new_quotation_feature = $feature->replicate();
                $new_quotation_feature->setTable('new_quotations_features');
                $new_quotation_feature->save();

                if($feature->feature_id == 0)
                {
                    if($feature->ladderband)
                    {
                        $sub_product = new_quotations_sub_products::where('feature_row_id',$feature->id)->get();

                        foreach ($sub_product as $sub)
                        {
                            $sub->feature_row_id = $new_quotation_feature->id;
                            $new_quotation_sub_product = $sub->replicate();
                            $new_quotation_sub_product->setTable('new_quotations_sub_products');
                            $new_quotation_sub_product->save();

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

            $temp_product = $temp;

            if($temp->item_id != 0)
            {
                $request->products[$x] = $temp->item_id.'I';
            }
            elseif($temp->service_id != 0)
            {
                $request->products[$x] = $temp->service_id.'S';
            }
            elseif($temp->product_id != 0)
            {
                $request->products[$x] = $temp->product_id;
            }
            else
            {
                $request->products[$x] = "";
            }
        }

        // $order_numbers = array_filter($order_numbers);
        $new_order_numbers = array_values(array_filter($order_numbers));
        $new_orders = new_orders::where('quotation_id',$id)->get();

        foreach($new_orders as $o => $key)
        {
            $key->quotation_id = $new_quotation->id;
            $key->order_number = $new_order_numbers[$o];
            $new_order_data = $key->replicate($except = ['order_sent','processing','failed','finished','approved','delivered','order_date','retailer_delivery_date','deliver_to']);
            $new_order_data->setTable('new_orders');
            $new_order_data->save();

            $order_calculations = new_orders_calculations::where('order_id',$key->id)->get();

            foreach($order_calculations as $order_cal)
            {
                $order_cal->order_id = $new_order_data->id;
                $new_order_calculation = $order_cal->replicate();
                $new_order_calculation->setTable('new_orders_calculations');
                $new_order_calculation->save();
            }

            $order_features = new_orders_features::where('order_data_id',$key->id)->get();

            foreach ($order_features as $of => $order_feature)
            {
                $order_feature->order_data_id = $new_order_data->id;
                $new_order_feature = $order_feature->replicate();
                $new_order_feature->setTable('new_orders_features');
                $new_order_feature->save();

                if($order_feature->feature_id == 0)
                {
                    if($order_feature->ladderband)
                    {
                        $order_sub_product = new_orders_sub_products::where('feature_row_id',$order_feature->id)->get();

                        foreach ($order_sub_product as $order_sub)
                        {
                            $order_sub->feature_row_id = $new_order_feature->id;
                            $new_order_sub_product = $order_sub->replicate();
                            $new_order_sub_product->setTable('new_orders_sub_products');
                            $new_order_sub_product->save();
                        }
                    }
                }
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
        $request->estimated_price_quantity = $box_quantity;
        $request->measure = $measure;
        $request->calculations = $calculations1;

        $filename = $quotation_invoice_number . '.pdf';

        ini_set('max_execution_time', 180);
        $role = 'retailer';
        $form_type = $new_quotation->form_type;
        $copy = 1;

        $pdf = PDF::loadView('user.pdf_new_quotation_1', compact('vat_percentages','copy','delivery_date','installation_date','form_type','role','product_descriptions','product_titles','color_titles','model_titles','feature_sub_titles','sub_titles','date','client','user','request','quotation_invoice_number'))->setPaper('letter', 'portrait')->setOptions(['dpi' => 160,'isRemoteEnabled' => true]);
        $file = public_path() . '/assets/newQuotations/' . $organization_id . '/' . $filename;
        $pdf->save($file);

        if (array_filter($suppliers)) {

            $new_request = json_encode($request);

            $new_quotation->processing = 1;
            $new_quotation->save();
            $quotation_id = $new_quotation->id;
            $date = $new_quotation->created_at;

            if($form_type == 1)
            {
                $role = 'order';
                CreateOrder::dispatch($orderPDF_delivery_date,$quotation_id,$form_type,$role,$product_titles,$color_titles,$model_titles,$feature_sub_titles,$sub_titles,$date,$client,$user,$new_request,$quotation_invoice_number,$suppliers,$order_numbers,$copy);
            }
            else
            {
                $role = 'supplier2';
                CreateOrder::dispatch($orderPDF_delivery_date,$quotation_id,$form_type,$role,$product_titles,$color_titles,$model_titles,$feature_sub_titles,$sub_titles,$date,$client,$user,$new_request,$quotation_invoice_number,$suppliers,$order_numbers,$copy);
            }
       }
        
    }

    public function failed()
    {
        $id = $this->id;

        $msg = 'Job failed for copying quotation <br> Quotation ID: ' . $id;

        \Mail::send(array(), array(), function ($message) use ($msg) {
            $message->to('tayyabkhurram62@gmail.com')
                ->from('info@pieppiep.com')
                ->subject('Job Failed')
                ->html($msg, 'text/html');
        });
    }
}
