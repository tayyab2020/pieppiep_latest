<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\organizations;
use Illuminate\Support\Facades\Schema;

class CopySupplierAccountShowToOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-supplier-account-show-to-organizations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy supplier_account_show, live, counter_customer_number, service_general_ledger & prestashop_access_key columns data from users table to organizations table';

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
        if (!Schema::hasColumn('organizations', 'supplier_account_show'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD supplier_account_show INT NOT NULL DEFAULT 1');
        }

        if (!Schema::hasColumn('organizations', 'live'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD live INT NOT NULL DEFAULT 1');
        }

        if (!Schema::hasColumn('organizations', 'counter_customer_number'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD counter_customer_number INT NOT NULL DEFAULT 1');
        }

        if (!Schema::hasColumn('organizations', 'service_general_ledger'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD service_general_ledger INT NULL');
        }

        if (!Schema::hasColumn('organizations', 'prestashop_access_key'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD prestashop_access_key TEXT NULL');
        }

        if (!Schema::hasColumn('organizations', 'prestashop_url'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD prestashop_url TEXT NULL');
        }
        
        $users = User::where("main_id",NULL)->where("role_id","!=",3)->withTrashed()->get();

        foreach($users as $key)
        {
            if($key->role_id == 4)
            {
                organizations::where("id",$key->organization->id)->update(["supplier_account_show" => $key->supplier_account_show, "live" => $key->live]);
            }
            else
            {
                organizations::where("id",$key->organization->id)->update(["counter_customer_number" => $key->counter_customer_number, "service_general_ledger" => $key->service_general_ledger, "prestashop_access_key" => $key->prestashop_access_key]);
            }
        }
    }
}
