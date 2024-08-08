<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Brand;
use Illuminate\Support\Facades\Schema;

class OtherSuppliersFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'other-suppliers-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing other_suppliers column with other_suppliers_organizations in brands table';

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
        if (!Schema::hasColumn('brands', 'other_suppliers_organizations'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE brands ADD other_suppliers_organizations TEXT NULL');
        }

        $brands = Brand::get();

        foreach($brands as $key)
        {
            $other_suppliers = $key->other_suppliers;

            if($other_suppliers)
            {
                $other_suppliers_array = explode(",",$other_suppliers);
                $other_suppliers_organizations = array();

                foreach($other_suppliers_array as $userId)
                {
                    $supplier = User::where("id",$userId)->withTrashed()->first();
                    $other_suppliers_organizations[] = $supplier->organization->id;
                }

                $other_suppliers_organizations = implode(",",$other_suppliers_organizations);

                $key->other_suppliers_organizations = $other_suppliers_organizations;
                $key->save();
            }
        }
    }
}
