<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\customers_details;

class CreateCustomersProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:customers-project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $customers = customers_details::where("projects",NULL)->get();

        foreach($customers as $key)
        {
            $p_name = $key->family_name ? $key->name . " " . $key->family_name : $key->name;
            $p_start_date = date("Y-m-d") . "T00:00:00";
            $p_end_date = date("Y-m-d",strtotime("+1 year", strtotime(date("Y-m-d")))) . "T00:00:00";
            $projects = [["name" => $p_name,"is_active" => true,"start_date" => $p_start_date,"end_date" => $p_end_date,"description" => null,"comment" => null,"total" => "0"]];
            $projects = json_encode($projects);

            $key->projects = $projects;
            $key->save();
        }
    }
}
