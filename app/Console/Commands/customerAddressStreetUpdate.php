<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\customers_details;

class customerAddressStreetUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer-address-street-update';

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
        $customers = customers_details::where("address","!=",NULL)->where("street_name",NULL)->where("street_number",NULL)->get();

        foreach($customers as $key)
        {
            $search = $key->address;

            $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDnNrRbo2J8d60OLHlolqpP_jZm7WVxpA8&address=".urlencode($search)."&sensor=false";
    
            $result_string = file_get_contents($url);
            $result = json_decode($result_string, true);
            $street_name = NULL;
            $street_number = NULL;

            for($i=0; $i < count($result["results"][0]["address_components"]); $i++)
            {
                if($result["results"][0]["address_components"][$i]["types"][0] == "route")
                {
                    $street_name = $result["results"][0]["address_components"][$i]["long_name"];
                }

                if($result["results"][0]["address_components"][$i]["types"][0] == "street_number")
                {
                    $street_number = $result["results"][0]["address_components"][$i]["long_name"];
                }
            }

            $key->street_name = $street_name;
            $key->street_number = $street_number;
            $key->save();
        }
    }
}
