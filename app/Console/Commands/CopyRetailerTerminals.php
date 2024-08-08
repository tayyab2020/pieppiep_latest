<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\organizations;
use App\handyman_terminals;
use Illuminate\Support\Facades\Schema;

class CopyRetailerTerminals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-retailer-terminals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy retailer terminals data to organizations table';

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
        if (!Schema::hasColumn('organizations', 'terminal_zipcode'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD terminal_zipcode VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('organizations', 'terminal_longitude'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD terminal_longitude VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('organizations', 'terminal_latitude'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD terminal_latitude VARCHAR(255) NULL');
        }

        if (!Schema::hasColumn('organizations', 'terminal_radius'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD terminal_radius INT NULL');
        }

        if (!Schema::hasColumn('organizations', 'terminal_city'))
        {
            // Add the new column
            \DB::statement('ALTER TABLE organizations ADD terminal_city TEXT NULL');
        }
        
        $terminals = handyman_terminals::leftjoin("users","users.id","=","handyman_terminals.handyman_id")->where("users.main_id",NULL)->select("handyman_terminals.*")->get();

        foreach($terminals as $key)
        {
            $user = User::where("id",$key->handyman_id)->withTrashed()->first();
            $organization_id = $user->organization ? $user->organization->id : "";

            if($organization_id)
            {
                organizations::where("id",$organization_id)->update(["terminal_zipcode" => $key->zipcode, "terminal_longitude" => $key->longitude, "terminal_latitude" => $key->latitude, "terminal_radius" => $key->radius, "terminal_city" => $key->city]);
            }
        }
    }
}
