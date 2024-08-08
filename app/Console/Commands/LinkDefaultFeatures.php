<?php

namespace App\Console\Commands;

use App\features;
use App\features_details;
use App\default_features;
use App\default_features_details;
use App\product_features;
use Illuminate\Console\Command;
use DateTime;

class LinkDefaultFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link-default-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link default features with suppliers features by matching titles';

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

        $features_details = features_details::get();

        foreach ($features_details as $key)
        {
            if(!$key->default_value_id)
            {
                $default_id = features::withTrashed()->where("id",$key->feature_id)->pluck("default_feature_id")->first();
                $flag = 0;

                if($key->sub_feature)
                {
                    $main_id = features_details::withTrashed()->where("id",$key->main_id)->pluck("default_value_id")->first();
                }

                $default_features_details = default_features_details::withTrashed()->where("feature_id",$default_id)->where("sub_feature",$key->sub_feature)->get();

                foreach($default_features_details as $detail)
                {
                    if($detail->title == $key->title)
                    {
                        if($detail->deleted_at)
                        {
                            $key->delete();
                        }
                        else
                        {
                            $key->deleted_at = NULL;
                        }

                        $flag = 1;
                        $default_feature_detail_id = $detail->id;
                    }
                }

                if(!$flag)
                {
                    $detail = new default_features_details;
                    $detail->feature_id = $default_id;
                    $detail->sub_feature = $key->sub_feature;
                    $detail->main_id = $key->sub_feature ? $main_id : NULL;
                    $detail->title = $key->title;
                    $detail->value = $key->value;
                    $detail->price_impact = $key->price_impact;
                    $detail->impact_type = $key->impact_type;
                    $detail->sub_category_ids = $key->sub_category_ids;
                    $detail->factor_value = $key->factor_value;
                    $detail->save();

                    $default_feature_detail_id = $detail->id;
                }

                $key->default_value_id = $default_feature_detail_id;
                $key->save();
            }
        }

        $default_features_details = default_features_details::withTrashed()->get();

        foreach ($default_features_details as $key)
        {
            $suppliers_features = features::withTrashed()->where("default_feature_id",$key->feature_id)->get();

            foreach($suppliers_features as $sf)
            {
                $flag = 0;
                
                if($key->sub_feature)
                {
                    $main_id = features_details::withTrashed()->where("feature_id",$sf->id)->where("default_value_id",$key->main_id)->pluck("id")->first();
                }

                $feature_details = features_details::withTrashed()->where("feature_id",$sf->id)->where("sub_feature",$key->sub_feature)->get();

                foreach($feature_details as $fd)
                {
                    if($fd->title == $key->title)
                    {
                        if($key->deleted_at)
                        {
                            $fd->delete();
                        }
                        else
                        {
                            $fd->deleted_at = NULL;
                        }
                        
                        $flag = 1;
                    }
                }

                if(!$flag)
                {
                    $feature_detail = new features_details;
                    $feature_detail->default_value_id = $key->id;
                    $feature_detail->feature_id = $sf->id;
                    $feature_detail->main_id = $key->sub_feature ? $main_id : NULL;
                    $feature_detail->sub_feature = $key->sub_feature;
                    $feature_detail->title = $key->title;
                    $feature_detail->value = $key->value;
                    $feature_detail->price_impact = $key->price_impact;
                    $feature_detail->impact_type = $key->impact_type;
                    $feature_detail->sub_category_ids = $key->sub_category_ids;
                    $feature_detail->factor_value = $key->factor_value;
                    $feature_detail->deleted_at = $key->deleted_at;
                    $feature_detail->save();
                }
            }
        }

        // $product_features = product_features::get();

        // foreach ($product_features as $key)
        // {
        //     if(!$key->feature_value_id)
        //     {
        //         $default_id = features::where("id",$key->heading_id)->pluck("default_feature_id")->first();
        //         $flag = 0;
    
        //         if($key->sub_feature)
        //         {
        //             $main_id = product_features::where("id",$key->main_id)->pluck("feature_value_id")->first();
        //         }
    
        //         $default_features_details = default_features_details::where("feature_id",$default_id)->where("sub_feature",$key->sub_feature)->get();
    
        //         foreach($default_features_details as $detail)
        //         {
        //             if($detail->title == $key->title)
        //             {
        //                 $flag = 1;
        //                 $default_feature_detail_id = $detail->id;
        //             }
        //         }
    
        //         if(!$flag)
        //         {
        //             $detail = new default_features_details;
        //             $detail->feature_id = $default_id;
        //             $detail->sub_feature = $key->sub_feature;
        //             $detail->main_id = $key->sub_feature ? $main_id : NULL;
        //             $detail->title = $key->title;
        //             $detail->value = $key->value;
        //             $detail->price_impact = 0;
        //             $detail->impact_type = 0;
        //             $detail->save();
    
        //             $default_feature_detail_id = $detail->id;
        //         }
    
        //         $key->feature_value_id = $default_feature_detail_id;
        //         $key->save();
        //     }
        // }

        \Log::info("Job is working fine!");
    }
}
