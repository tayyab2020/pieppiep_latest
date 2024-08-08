<?php

namespace App\Console\Commands;

use App\new_quotations;
use App\all_invoices;
use App\retailer_subcategories_ledgers;
use App\Products;
use App\items;
use App\User;
use Illuminate\Console\Command;
use DateTime;

class PropagateLedgers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propagate-ledgers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Propagate ledgers in quotations and invoices lines';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ini_set( 'serialize_precision', -1 );
        $quotations = new_quotations::withTrashed()->get();
        $invoices = all_invoices::withTrashed()->get();

        foreach($quotations as $key)
        {
            $creator_id = $key->creator_id;
            $all_employees = User::where("main_id",$creator_id)->pluck("id");

            foreach($key->data as $temp)
            {
                if(($temp->product_id || $temp->item_id || $temp->service_id) && !$temp->ledger_id)
                {
                    if(!$temp->service_id)
                    {
                        if($temp->product_id)
                        {
                            if($temp->model_id)
                            {
                                $data = Products::withTrashed()->leftjoin('product_models','product_models.product_id','=','products.id')->where('product_models.id',$temp->model_id)->where('products.id',$temp->product_id)->select('products.*')->first();
                            }
                            else
                            {
                                $data = Products::withTrashed()->where('id',$temp->product_id)->first();
                            }
    
                            $sub_category_id = $data ? $data->sub_category_id : NULL;
                        }
                        else
                        {
                            $data = items::withTrashed()->where('id',$temp->item_id)->first();
                            $sub_category_id = $data->sub_category_ids;
                        }
    
                        $ledger = retailer_subcategories_ledgers::where("sub_id",$sub_category_id)->where(function($query) use ($creator_id,$all_employees) {
                            $query->whereIn('user_id', $all_employees)->orWhere('user_id',$creator_id);
                        })->first();
                        
                        if($sub_category_id)
                        {
                            $data->ledger = $ledger ? $ledger->ledger_id : NULL;
                        }
                    }
                    else
                    {
                        $data = User::withTrashed()->where('id',$creator_id)->first();
                        $data->ledger = $data->organization->service_general_ledger;
                    }
    
                    if($data)
                    {
                        $temp->ledger_id = $data->ledger;
                        $temp->save();
                    }
                }
            }
        }

        foreach($invoices as $key)
        {
            $creator_id = $key->creator_id;
            $all_employees = User::where("main_id",$creator_id)->pluck("id");

            foreach($key->data as $temp)
            {
                if(($temp->product_id || $temp->item_id || $temp->service_id) && !$temp->ledger_id)
                {
                    if(!$temp->service_id)
                    {
                        if($temp->product_id)
                        {
                            if($temp->model_id)
                            {
                                $data = Products::withTrashed()->leftjoin('product_models','product_models.product_id','=','products.id')->where('product_models.id',$temp->model_id)->where('products.id',$temp->product_id)->select('products.*')->first();
                            }
                            else
                            {
                                $data = Products::withTrashed()->where('id',$temp->product_id)->first();
                            }
    
                            $sub_category_id = $data ? $data->sub_category_id : NULL;
                        }
                        else
                        {
                            $data = items::withTrashed()->where('id',$temp->item_id)->first();
                            $sub_category_id = $data->sub_category_ids;
                        }
    
                        $ledger = retailer_subcategories_ledgers::where("sub_id",$sub_category_id)->where(function($query) use ($creator_id,$all_employees) {
                            $query->whereIn('user_id', $all_employees)->orWhere('user_id',$creator_id);
                        })->first();

                        if($sub_category_id)
                        {
                            $data->ledger = $ledger ? $ledger->ledger_id : NULL;
                        }
                    }
                    else
                    {
                        $data = User::withTrashed()->where('id',$creator_id)->first();
                        $data->ledger = $data->organization->service_general_ledger;
                    }
    
                    if($data)
                    {
                        $temp->ledger_id = $data->ledger;
                        $temp->save();
                    }
                }
            }
        }

        \Log::info("Job is working fine!");
    }
}
