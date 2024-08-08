<?php

namespace App\Console\Commands;

use App\features;
use App\features_details;
use App\default_features;
use App\default_features_details;
use App\product_features;
use Illuminate\Console\Command;
use DateTime;

class SubFeaturesStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sub-features-status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update status of those sub features from enabled to disabled of which main features are disabled';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        ini_set( 'serialize_precision', -1 );

        $product_features = product_features::where("sub_feature",0)->where("status",0)->get();

        foreach ($product_features as $key)
        {
            $product_sub_features = product_features::where("main_id",$key->id)->update(["status" => 0]);
        }

        \Log::info("Job is working fine!");
    }
}
