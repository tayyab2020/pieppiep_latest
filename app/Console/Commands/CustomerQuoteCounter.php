<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\universal_customers_details;
use Illuminate\Support\Facades\Schema;

class CustomerQuoteCounter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer-quote-counter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing counter column with quote_req_counter for customer records in universal_customers_details table';

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
        $customers = User::where("role_id",3)->withTrashed()->get();

        foreach($customers as $key)
        {
            $post = new universal_customers_details;
            $post->user_id = $key->id;
            $post->city = $key->city;
            $post->address = $key->address;
            $post->phone = $key->phone;
            $post->postcode = $key->postcode;
            $post->business_name = $key->business_name;
            $post->tax_number = $key->tax_number;
            $post->quote_req_counter = isset($key->counter) ? $key->counter : 1;
            $post->save();
        }
    }
}
