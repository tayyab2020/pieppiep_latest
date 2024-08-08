<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\supplier_categories;
use Illuminate\Support\Facades\Schema;

class SupplierCategoriesFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier-categoires-fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing user_id column with organization_id in supplier_categories table';

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
        if (!Schema::hasColumn('supplier_categories', 'organization_id'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE supplier_categories ADD organization_id INT NOT NULL');
        }

        $supplier_categories = supplier_categories::get();

        foreach($supplier_categories as $key)
        {
            $supplier = User::where("id",$key->user_id)->withTrashed()->first();
            $key->organization_id = $supplier->organization->id;
            $key->save();
        }
    }
}
