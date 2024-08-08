<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\organizations;
use App\Products;
use Illuminate\Support\Facades\Schema;

class ProductsOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store-organization-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store supplier organization id in products table';

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
        if (!Schema::hasColumn('products', 'organization_id'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE products ADD organization_id INT NOT NULL');
        }
        
        $products = Products::withTrashed()->get();

        foreach($products as $key)
        {
            $organization_id = User::where("id",$key->user_id)->first()->organization->id;
            $key->organization_id = $organization_id;
            $key->save();
        }
    }
}
