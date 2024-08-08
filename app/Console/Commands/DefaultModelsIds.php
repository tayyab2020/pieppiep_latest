<?php

namespace App\Console\Commands;

use App\predefined_models;
use App\predefined_models_details;
use App\default_predefined_models;
use App\default_predefined_models_details;
use App\product_models;
use Illuminate\Console\Command;
use DateTime;
use App\Products;
use App\organizations;

class DefaultModelsIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'default-models-ids-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'store default models ids in predefined_models and predefined_models_details table';

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

        $models = predefined_models::get();

        foreach ($models as $key)
        {
            if(!$key->default_model_id)
            {
                $find_default_model = default_predefined_models::where("model",$key->model)->first();

                if(!$find_default_model)
                {
                    $find_default_model = new default_predefined_models;
                    $find_default_model->model = $key->model;
                    $find_default_model->category_ids = $key->category_ids;
                    $find_default_model->save();
                }

                $key->default_model_id = $find_default_model->id;
                $key->save();
            }

            $details = predefined_models_details::where("model_id",$key->id)->get();

            foreach($details as $dt)
            {
                $find_default_model_details = default_predefined_models_details::where("model_id",$key->default_model_id)->where("model",$dt->model)->first();

                if(!$find_default_model_details)
                {
                    $find_default_model_details = new default_predefined_models_details;
                    $find_default_model_details->model_id = $key->default_model_id;
                    $find_default_model_details->model = $dt->model;
                    $find_default_model_details->value = $dt->value;
                    $find_default_model_details->measure = $dt->measure;
                    $find_default_model_details->price_impact = $dt->price_impact;
                    $find_default_model_details->impact_type = $dt->impact_type;
                    $find_default_model_details->m1_impact = $dt->m1_impact;
                    $find_default_model_details->m2_impact = $dt->m2_impact;
                    $find_default_model_details->factor = $dt->factor;
                    $find_default_model_details->factor_value = $dt->factor_value;
                    $find_default_model_details->save();
                }

                $dt->default_model_detail_id = $find_default_model_details->id;
                $dt->save();
            }
        }

        $product_models = product_models::get();

        foreach($product_models as $key)
        {
            $organization_id = Products::where("id",$key->product_id)->pluck("organization_id")->first();
            $organization = organizations::findOrFail($organization_id);
            $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

            $find_size = predefined_models_details::leftjoin("predefined_models","predefined_models.id","=","predefined_models_details.model_id")->whereIn("predefined_models.user_id",$related_users)->where('predefined_models_details.model','=',$key->model)->select("predefined_models_details.*")->first();
            
            if($find_size)
            {
                $key->size = 1;
                $key->size_id = $find_size->id;
                $key->save();
            }
        }

        \Log::info("Job is working fine!");
    }
}
