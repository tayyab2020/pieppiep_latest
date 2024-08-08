<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\customers_details;
use Illuminate\Support\Facades\Schema;

class CopyToCustomersDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-to-customers-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy some specific columns data of customers from users table to customers details table';

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
        if (!Schema::hasColumn('customers_details', 'mollie_customer_id'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE customers_details ADD mollie_customer_id VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('customers_details', 'mollie_method'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE customers_details ADD mollie_method VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('customers_details', 'payment_id'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE customers_details ADD payment_id VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('customers_details', 'payment_status'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE customers_details ADD payment_status VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('customers_details', 'first_quote'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE customers_details ADD first_quote INT NOT NULL DEFAULT 0');
        }

        $customers = User::where("role_id",3)->withTrashed()->get();

        foreach($customers as $key)
        {
            $customers_details = customers_details::where("user_id",$key->id)->withTrashed()->first();

            if($customers_details)
            {
                $customers_details->mollie_customer_id = $key->mollie_customer_id;
                $customers_details->mollie_method = $key->mollie_method;
                $customers_details->payment_id = $key->payment_id;
                $customers_details->payment_status = $key->payment_status;
                $customers_details->first_quote = $key->first_quote;
                $customers_details->save();
            }
        }
    }
}
