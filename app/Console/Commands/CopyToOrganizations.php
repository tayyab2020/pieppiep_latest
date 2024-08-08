<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\organizations;
use App\user_organizations;

class CopyToOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-to-organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy retailers/suppliers company related info from users table to organizations table';

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
        $users = User::where("main_id",NULL)->withTrashed()->where("role_id","!=",3)->get();

        foreach($users as $key)
        {
            $organization = new organizations;
            $organization->company_name = $key->company_name;
            $organization->Type = $key->role_id == 2 ? "Retailer" : "Supplier";
            $organization->registration_number = $key->registration_number;
            $organization->phone = $key->phone;
            $organization->web = $key->web;
            $organization->address = $key->address;
            $organization->city = $key->city;
            $organization->postcode = $key->postcode;
            $organization->business_name = $key->business_name;
            $organization->tax_number = $key->tax_number;
            $organization->bank_account = $key->bank_account;
            $organization->counter = $key->counter;
            $organization->counter_order = $key->counter_order;
            $organization->counter_invoice = $key->counter_invoice;
            $organization->quotation_prefix = $key->quotation_prefix;
            $organization->order_prefix = $key->order_prefix;
            $organization->invoice_prefix = $key->invoice_prefix;
            $organization->quotation_client_id = $key->quotation_client_id;
            $organization->order_client_id = $key->order_client_id;
            $organization->invoice_client_id = $key->invoice_client_id;
            $organization->email = $key->email;
            $organization->save();

            user_organizations::create([
                'user_id' => $key->id,
                'organization_id' => $organization->id,
            ]);

            $employees_ids = User::where("main_id",$key->id)->withTrashed()->get()->pluck("id");

            foreach($employees_ids as $emp)
            {
                user_organizations::create([
                    'user_id' => $emp,
                    'organization_id' => $organization->id,
                ]);
            }
        }
    }
}
