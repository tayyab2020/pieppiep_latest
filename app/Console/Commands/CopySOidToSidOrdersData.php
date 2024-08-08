<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\new_orders;

class CopySOidToSidOrdersData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-soid-to-sid-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing supplier_id column ids with supplier organization ids in new_orders table';

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
        \DB::statement('ALTER TABLE new_orders ADD supplier_id_old INT NULL');

        // Copy the values from supplier_id to supplier_id_old
        \DB::statement('UPDATE new_orders SET supplier_id_old = supplier_id');
        
        $new_orders = new_orders::get();

        foreach($new_orders as $key)
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
