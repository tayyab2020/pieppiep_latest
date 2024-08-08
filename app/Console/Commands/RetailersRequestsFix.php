<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\retailers_requests;
use Illuminate\Support\Facades\Schema;

class RetailersRequestsFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retailers-requests-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing retailer_id & supplier_id columns with retailer_organization & supplier_organization in retailers_requests table';

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
        if (!Schema::hasColumn('retailers_requests', 'retailer_organization'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE retailers_requests ADD retailer_organization INT NOT NULL');
        }

        if (!Schema::hasColumn('retailers_requests', 'supplier_organization'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE retailers_requests ADD supplier_organization INT NOT NULL');
        }

        $retailers_requests = retailers_requests::get();

        foreach($retailers_requests as $key)
        {
            $retailer_organization_id = User::where("id",$key->retailer_id)->withTrashed()->first()->organization->id;
            $supplier_organization_id = User::where("id",$key->supplier_id)->withTrashed()->first()->organization->id;
            
            $key->retailer_organization = $retailer_organization_id;
            $key->supplier_organization = $supplier_organization_id;
            $key->save();
        }
    }
}
