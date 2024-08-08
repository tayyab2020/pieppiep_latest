<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\prestashop_products_exports;

class PrestashopProductsSuppliers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestashop-products-suppliers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing supplier_id column ids with supplier organization ids in prestashop_products_exports table';

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
        // Add the new column
        \DB::statement('ALTER TABLE prestashop_products_exports ADD supplier_id_old INT NULL');

        // Copy the values from supplier_id to supplier_id_old
        \DB::statement('UPDATE prestashop_products_exports SET supplier_id_old = supplier_id');
        
        $prestashop_products_exports = prestashop_products_exports::get();

        foreach($prestashop_products_exports as $key)
        {
            if($key->supplier_id)
            {
                $supplier_organization_id = User::where("id",$key->supplier_id)->withTrashed()->first()->organization->id;
            
                $key->supplier_id = $supplier_organization_id;
                $key->save();
            }
        }
    }
}
