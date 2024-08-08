<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\quotation_appointments;
use Illuminate\Support\Facades\Schema;

class SupplierOrganizationPlannings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier-organization-plannings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing supplier_id column ids with supplier organization ids in quotation_appointments table';

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
        if (!Schema::hasColumn('quotation_appointments', 'supplier_id_old'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE quotation_appointments ADD supplier_id_old INT NULL');

            // Copy the values from supplier_id to supplier_id_old
            \DB::statement('UPDATE quotation_appointments SET supplier_id_old = supplier_id');
        }
        
        $quotation_appointments = quotation_appointments::get();

        foreach($quotation_appointments as $key)
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
