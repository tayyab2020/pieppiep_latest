<?php

namespace App\Console\Commands;

use App\features;
use App\features_details;
use App\default_features;
use App\default_features_details;
use App\product_features;
use Illuminate\Console\Command;
use DateTime;

class ProductFeaturesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product-features-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'replace features value ids (default) with suppliers features value ids';

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

        $product_features = product_features::leftjoin("products","products.id","=","product_features.product_id")->select("product_features.*","products.user_id")->get();

        foreach ($product_features as $key)
        {
            $supplier_feature_value_id = features_details::leftjoin("features","features.id","=","features_details.feature_id")->where("features.user_id",$key->user_id)->where("features_details.default_value_id",$key->feature_value_id)->pluck("features_details.id")->first();

            if($supplier_feature_value_id)
            {
                $key->feature_value_id = $supplier_feature_value_id;
                $key->save();
            }
        }

        \Log::info("Job is working fine!");
    }
}
