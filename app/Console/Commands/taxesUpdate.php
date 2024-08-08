<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\new_quotations;
use App\all_invoices;

class taxesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taxesUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $quotations = new_quotations::withTrashed()->get();
        $invoices = all_invoices::withTrashed()->get();
        ini_set( 'serialize_precision', -1 );

        foreach($quotations as $key)
        {
            $tax = $key->tax_amount;

            $taxes = [["percentage" => 21,"tax" => (float)number_format((float)$tax, 2, '.', '')]];
            $key->taxes_json = $taxes;
            $key->save();
        }

        foreach($invoices as $key)
        {
            $tax = $key->tax_amount;

            $taxes = [["percentage" => 21,"tax" => (float)number_format((float)$tax, 2, '.', '')]];
            $key->taxes_json = $taxes;
            $key->save();
        }
    }
}
