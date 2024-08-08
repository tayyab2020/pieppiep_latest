<?php

namespace App\Console\Commands;

use App\new_negative_invoices;
use Illuminate\Console\Command;
use DateTime;

class FixNegativeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-negative-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix negative invoices';

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
        $negative_invoices = new_negative_invoices::whereIn('invoice_number',["2022-000023","2022-000077","2022-000088","2022-000121","2022-000122","2022-000136","2023-000176","2023-000201","2023-000270","2023-000281","2023-000293","2023-000299","2023-000310"])->get();

        foreach ($negative_invoices as $key)
        {
            $key->subtotal = $key->subtotal * -1;
            $key->grand_total = $key->grand_total * -1;
            $key->net_amount = $key->net_amount * -1;
            $key->tax_amount = $key->tax_amount * -1;
            $tax_array = json_decode($key->taxes_json,true);
            
            foreach($tax_array as &$js){
                $js["tax"] = $js["tax"] * -1;
            }

            $key->taxes_json = json_encode($tax_array);
            $payment_total_amount = str_replace(",",".",$key->payment_total_amount) * -1;
            $key->payment_total_amount = str_replace(".",",",$payment_total_amount);

            foreach($key->data as $data)
            {
                $data->rate = $data->rate * -1;
                $data->qty = $data->qty * -1;
                $data->amount = $data->amount * -1;
                $data->discount = $data->discount * -1;
                $data->labor_discount = $data->labor_discount * -1;
                $data->total_discount = $data->total_discount * -1;
                $data->save();
            }

            foreach($key->payment_calculations as $payment)
            {
                $payment->amount = $payment->amount * -1;
                $payment->save();
            }

            $key->save();
        }

        \Log::info("Job is working fine!");
    }
}
