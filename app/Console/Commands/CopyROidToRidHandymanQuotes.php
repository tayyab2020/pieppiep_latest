<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\handyman_quotes;

class CopyROidToRidHandymanQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-ro-id-handyman-quotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replacing handyman_id column ids with retailer organization ids in handyman_quotes table';

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
        \DB::statement('ALTER TABLE handyman_quotes ADD retailer_id_old INT NULL');

        // Copy the values from retailer_id to retailer_id_old
        \DB::statement('UPDATE handyman_quotes SET retailer_id_old = handyman_id');
        
        $handyman_quotes = handyman_quotes::get();

        foreach($handyman_quotes as $key)
        {
            if($key->handyman_id)
            {
                $retailer_organization_id = User::where("id",$key->handyman_id)->withTrashed()->first()->organization->id;
            
                $key->handyman_id = $retailer_organization_id;
                $key->save();
            }
        }
    }
}
