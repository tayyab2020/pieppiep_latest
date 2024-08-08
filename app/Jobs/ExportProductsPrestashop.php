<?php

namespace App\Jobs;

use Auth;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\Products;
use App\retailer_mapped_categories;
use App\Category;
use App\all_categories;
use App\prestashop_attribute_values_exports;
use App\prestashop_products_exports;
use App\prestashop_features_exports;
use App\prestashop_features_values_exports;
use App\features;
use App\color_images;
use App\organizations;
use App\product_features;
use App\product_models;
use App\EmailSetting;
use Illuminate\Support\Facades\Crypt;

class ExportProductsPrestashop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $access_key = null;
    private $prestashop_url = null;
    private $suppliers = null;
    private $user_id = null;
    private $user = null;
    private $organization_id = null;
    private $organization = null;
    private $related_users = null;
    private $site = null;
    public $timeout = 0;
    public $prestashop_attributes = array();
    public $prestashop_categories = array();
    public $prestashop_brands = array();
    public $prestashop_features = array();
    public $prestashop_feature_values = array();
    public $prestashop_attribute_values = array();
    public $headers = null;
    public $sub_categories = NULL;
    public $base_url = "";

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($access_key,$prestashop_url,$suppliers,$user_id,$user,$organization_id,$organization,$related_users,$site,$sub_categories)
    {
        $this->access_key = $access_key;
        $this->prestashop_url = $prestashop_url;
        $this->suppliers = $suppliers;
        $this->user_id = $user_id;
        $this->user = $user;
        $this->organization_id = $organization_id;
        $this->organization = $organization;
        $this->related_users = $related_users;
        $this->site = $site;
        $this->sub_categories = $sub_categories;
        $this->headers = array(
            'Output-Format: JSON',
            // 'Authorization: Basic '. $access_key
        );

        $this->base_url = parse_url($prestashop_url);
        $apiKey = base64_decode($access_key);

        if (isset($this->base_url['scheme']) && isset($this->base_url['host'])) {
            
            // Construct the new URL with the API key
            $this->base_url = $this->base_url['scheme'] . '://' . $apiKey . '@' . $this->base_url['host'];

        }
    }

    public function recursiveArraySearch($needle, $haystack) {

        $flag = 0;

        foreach ($haystack as $key => $value) {
            if(isset($value["name"]))
            {
                if(is_array($value["name"]))
                {
                    $result = $this->recursiveArraySearch($needle, $value["name"]);
                    if ($result) {
                        $flag = $value["id"];
                    }
                }
                else
                {
                    if($value["name"] == $needle)
                    {
                        $flag = $value["id"];
                    }
                }
            }
            else if(isset($value["value"]))
            {
                if(is_array($value["value"]))
                {
                    $result = $this->recursiveArraySearch($needle, $value["value"]);
                    if ($result) {
                        $flag = $value["id"];
                    }
                }
                else
                {
                    if($value["value"] == $needle)
                    {
                        $flag = $value["id"];
                    }
                }
            }
        }

        return $flag;
    }

    public function create_brand_xml($title,$description,$id = NULL)
    {
        if($id)
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <manufacturer>
                    <id><![CDATA[$id]]></id>
                    <active><![CDATA[1]]></active>
                    <name><![CDATA[$title]]></name>
                    <description>
                        <language id="1"><![CDATA[$description]]></language>
                        <language id="2"><![CDATA[$description]]></language>
                    </description>
                </manufacturer>
            </prestashop>
            XML;
        }
        else
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <manufacturer>
                    <active><![CDATA[1]]></active>
                    <name><![CDATA[$title]]></name>
                    <description>
                        <language id="1"><![CDATA[$description]]></language>
                        <language id="2"><![CDATA[$description]]></language>
                    </description>
                </manufacturer>
            </prestashop>
            XML;
        }
    }

    public function create_category_xml($title)
    {
        return  <<<XML
                <?xml version="1.0" encoding="UTF-8"?>
                <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                    <category>
                        <active><![CDATA[1]]></active>
                        <id_parent><![CDATA[2]]></id_parent>
                        <name>
                            <language id="1"><![CDATA[$title]]></language>
                            <language id="2"><![CDATA[$title]]></language>
                        </name>
                        <link_rewrite>
                            <language id="1"><![CDATA[$title]]></language>
                            <language id="2"><![CDATA[$title]]></language>
                        </link_rewrite>
                    </category>
                </prestashop>
                XML;
    }

    public function create_features_xml($exported_data)
    {
        $features_xml = "";

        foreach($exported_data as $x => $ex)
        {
            foreach($ex[1] as $v_ids)
            {
                $features_xml .= <<<XML
                <product_feature>
                    <id><![CDATA[$ex[0]]]></id>
                    <id_feature_value><![CDATA[$v_ids]]></id_feature_value>
                </product_feature>
                XML;
            }
        }

        return $features_xml;
    }

    public function create_update_feature($feature,$check_prestashop_feature,$user_id,$related_users,$type,$product_id)
    {
        $feature_heading = $feature->title;
        $default_feature_id = $feature->default_feature_id;

        if(!$check_prestashop_feature)
        {
            $check_prestashop_feature = new prestashop_features_exports;
            $check_prestashop_feature->product_id = $product_id;
            $check_prestashop_feature->retailer_id = $user_id;
            $check_prestashop_feature->feature_id = $feature->id;
        }
        
        if($type == 1)
        {
            $feature_search = $this->feature_search($check_prestashop_feature->prestashop_feature_id,$default_feature_id);
        }
        else
        {
            $feature_search = $this->feature_search(NULL,$default_feature_id);
        }

        if($feature_search)
        {
            $heading_id_XML = <<<XML
            <id><![CDATA[$feature_search]]></id>
            XML;
            $req_type = "PUT";
        }
        else
        {
            $heading_id_XML = "";
            $req_type = "POST";
        }

        $xmlData = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <product_feature>
                $heading_id_XML
                <name>
                    <language id="1"><![CDATA[$feature_heading]]></language>
                    <language id="2"><![CDATA[$feature_heading]]></language>
                </name>
            </product_feature>
        </prestashop>
        XML;

        $apiUrl = $this->base_url."/api/product_features";
        // Initialize cURL session
        $ch = curl_init($apiUrl);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        $prestashop_feature_id = $response["product_feature"]["id"];
        $check_prestashop_feature->prestashop_feature_id = $prestashop_feature_id;
        $check_prestashop_feature->save();

        $f_v_ids = [];
        $export_values = [];
        
        // Initialize cURL multi handle
        $mh = curl_multi_init();
        $handles = [];
        
        foreach($feature->features as $feature_value)
        {
            if(!$feature_value->sub_feature && $feature_value->status)
            {
                $value = $feature_value->title;
                $default_value_id = $feature_value->default_value_id;

                $check_features_values = prestashop_features_values_exports::whereIn("retailer_id",$related_users)->where("heading_id",$check_prestashop_feature->id)->where("feature_value_id",$feature_value->id)->first();

                if($check_features_values)
                {
                    $value_id = $check_features_values->prestashop_feature_value_id;
                    $feature_value_search = $this->feature_value_search($value_id,$default_value_id,$prestashop_feature_id);
                }
                else
                {
                    $feature_value_search = $this->feature_value_search(NULL,$default_value_id,$prestashop_feature_id);
                }

                if($feature_value_search)
                {
                    $value_id_XML = <<<XML
                    <id><![CDATA[$feature_value_search]]></id>
                    XML;
                    $req_type = "PUT";
                }
                else
                {
                    $value_id_XML = "";
                    $req_type = "POST";
                }

                $xmlData = <<<XML
                <?xml version="1.0" encoding="UTF-8"?>
                <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                    <product_feature_value>
                        $value_id_XML
                        <id_feature><![CDATA[$prestashop_feature_id]]></id_feature>
                        <value>
                            <language id="1"><![CDATA[$value]]></language>
                            <language id="2"><![CDATA[$value]]></language>
                        </value>
                    </product_feature_value>
                </prestashop>
                XML;

                $feature_value_id = $feature_value->id;

                $apiUrl = $this->base_url . "/api/product_feature_values";
                $ch = curl_init($apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);

                $start = microtime(true); // Start time

                $prestashop_feature_value_id = $response["product_feature_value"]["id"];
                $check_features_value = $check_features_values;
            
                if (!$check_features_value) {
                    $check_features_value = new prestashop_features_values_exports;
                    $check_features_value->retailer_id = $user_id;
                }
            
                $check_features_value->heading_id = $check_prestashop_feature->id;
                $check_features_value->prestashop_feature_value_id = $prestashop_feature_value_id;
                $check_features_value->feature_value_id = $feature_value_id;
                $check_features_value->save();
    
                $this->prestashop_feature_values[$prestashop_feature_value_id] = [
                    'id' => $prestashop_feature_value_id,
                    'id_feature' => $prestashop_feature_id,
                    'value' => $value,
                    'default_value_id' => $default_value_id
                ];
            
                $f_v_ids[] = $check_features_value->id;
                $export_values[] = $prestashop_feature_value_id;
            
                $end = microtime(true); // End time
                $executionTime = $end - $start; // Calculate execution time
            
                \Log::info('Execution time for feature value creation: ' . $executionTime . ' seconds.');
            }
        }
        
        // Clean up any old feature values not in current batch
        prestashop_features_values_exports::whereNotIn('id', $f_v_ids)
        ->where('heading_id', $check_prestashop_feature->id)
        ->whereIn('retailer_id', $related_users)
        ->delete();

        $this->prestashop_features[$prestashop_feature_id] = [
            'id' => $prestashop_feature_id,
            'name' => $feature_heading,
            'default_id' => $default_feature_id
        ];

        $export_data = [$prestashop_feature_id, $export_values];
        
        return [$check_prestashop_feature->id, $export_data];
    }

    public function create_attribute_xml($title,$title_ducth)
    {
        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <product_option>
                <is_color_group><![CDATA[0]]></is_color_group>
                <group_type><![CDATA[select]]></group_type>
                <name>
                    <language id="1"><![CDATA[$title]]></language>
                    <language id="2"><![CDATA[$title_ducth]]></language>
                </name>
                <public_name>
                    <language id="1"><![CDATA[$title]]></language>
                    <language id="2"><![CDATA[$title_ducth]]></language>
                </public_name>
            </product_option>
        </prestashop>
        XML;
    }

    public function create_attribute_value_xml($title,$attribute_id,$id = NULL)
    {
        if($id)
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product_option_value>
                    <id><![CDATA[$id]]></id>
                    <id_attribute_group><![CDATA[$attribute_id]]></id_attribute_group>
                    <name>
                        <language id="1"><![CDATA[$title]]></language>
                        <language id="2"><![CDATA[$title]]></language>
                    </name>
                </product_option_value>
            </prestashop>
            XML;
        }
        else
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product_option_value>
                    <id_attribute_group><![CDATA[$attribute_id]]></id_attribute_group>
                    <name>
                        <language id="1"><![CDATA[$title]]></language>
                        <language id="2"><![CDATA[$title]]></language>
                    </name>
                </product_option_value>
            </prestashop>
            XML;
        }
    }

    public function getAttributes()
    {
        $start = microtime(true); // Start time

        $url = $this->base_url."/api/product_options";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_attributes = json_decode($response, true);

        if(isset($response_attributes["product_options"]))
        {
            foreach($response_attributes["product_options"] as $key)
            {
                $url = $this->base_url."/api/product_options/".$key["id"];
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_attribute = json_decode($response, true);

                $this->prestashop_attributes[] = $response_attribute["product_option"];
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for getAttributes: ' . $executionTime . ' seconds.');

        return;
    }

    public function attributes_search($attribute,$attribute_dutch,$attributes)
    {
        $start = microtime(true); // Start time

        $attribute_search = $this->recursiveArraySearch($attribute, $attributes);

        if(!$attribute_search)
        {
            $xmlData = $this->create_attribute_xml($attribute,$attribute_dutch);
                    
            // API endpoint URL
            $apiUrl = $this->base_url."/api/product_options";
            
            // Initialize cURL session
            $ch = curl_init($apiUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $response = curl_exec($ch);
            
            // Check for cURL errors
            // if (curl_errno($ch)) {
            //     echo "cURL Error: " . curl_error($ch);
            // }
            
            // Close cURL session
            curl_close($ch);
            $response = json_decode($response, true);

            $this->prestashop_attributes[] = $response["product_option"];
            $attribute_search = $response["product_option"]["id"];            
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for attributes_search: ' . $executionTime . ' seconds.');

        return $attribute_search;
    }

    public function attribute_value_search($id,$default_model_detail_id = NULL,$value = NULL)
    {
        $response = "";
        
        if($id)
        {
            if (isset($this->prestashop_attribute_values[$id])) {
                $response = $this->prestashop_attribute_values[$id]["id"];
            }
        }

        if(!$response)
        {
            foreach ($this->prestashop_attribute_values as $attribute_value) {
                if (($default_model_detail_id && $attribute_value['default_id'] == $default_model_detail_id) || ($value && $attribute_value['name'] == $value)) {
                    $response = $attribute_value['id'];
                    break;
                }
            }
        }

        return $response;
    }

    public function feature_search($id,$default_feature_id)
    {
        $start = microtime(true); // Start time

        $response = "";

        if ($id) {
            // Search in cached features by ID
            if (isset($this->prestashop_features[$id])) {
                $response = $this->prestashop_features[$id]["id"];
            }
        }
        
        if(!$response)
        {
            // Search in cached features by feature heading
            foreach ($this->prestashop_features as $feature) {
                if ($feature['default_id'] == $default_feature_id) {
                    $response = $feature['id'];
                    break;
                }
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for features search: ' . $executionTime . ' seconds.');

        return $response;
    }

    public function getAttributeValues()
    {
        $url = $this->base_url . "/api/product_option_values";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $response_attribute_values = json_decode($response, true);

        if (isset($response_attribute_values["product_option_values"])) {
            foreach ($response_attribute_values["product_option_values"] as $attribute_value) {
                $av_id = $attribute_value["id"];
                $url = $this->base_url . "/api/product_option_values/" . $av_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_attribute_value = curl_exec($ch);
                curl_close($ch);
                $response_attribute_value = json_decode($response_attribute_value, true);
                
                if ($response_attribute_value) {
                    $model_id = prestashop_attribute_values_exports::where("prestashop_attribute_value_id",$av_id)->pluck("model_id")->first();
                    $response_attribute_value["product_option_value"]["default_id"] = product_models::leftjoin("predefined_models_details","predefined_models_details.id","=","product_models.size_id")->where("product_models.id",$model_id)->pluck("predefined_models_details.default_model_detail_id")->first();
                    $this->prestashop_attribute_values[$av_id] = $response_attribute_value["product_option_value"];
                }
            }
        }
    }

    public function getFeatures()
    {
        $url = $this->base_url . "/api/product_features";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $response_features = json_decode($response, true);

        if (isset($response_features["product_features"])) {
            foreach ($response_features["product_features"] as $feature) {
                $f_id = $feature["id"];
                $url = $this->base_url . "/api/product_features/" . $f_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_feature = curl_exec($ch);
                curl_close($ch);
                $response_feature = json_decode($response_feature, true);
                
                if ($response_feature) {
                    $feature_id = prestashop_features_exports::where("prestashop_feature_id",$f_id)->pluck("feature_id")->first();
                    $response_feature["product_feature"]["default_id"] = features::where("id",$feature_id)->pluck("default_feature_id")->first();
                    $this->prestashop_features[$f_id] = $response_feature["product_feature"];
                }
            }
        }
    }

    public function getFeatureValues()
    {
        $url = $this->base_url . "/api/product_feature_values";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
    
        $response_feature_values = json_decode($response, true);

        if (isset($response_feature_values["product_feature_values"])) {
            foreach ($response_feature_values["product_feature_values"] as $feature_value) {
                $fv_id = $feature_value["id"];
                $url = $this->base_url . "/api/product_feature_values/" . $fv_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_feature_value = curl_exec($ch);
                curl_close($ch);
                $response_feature_value = json_decode($response_feature_value, true);
                
                if ($response_feature_value) {
                    $feature_value_id = prestashop_features_values_exports::where("prestashop_feature_value_id",$fv_id)->pluck("feature_value_id")->first();
                    $response_feature_value["product_feature_value"]["default_value_id"] = product_features::leftjoin("features_details","features_details.id","=","product_features.feature_value_id")->where("product_features.id",$feature_value_id)->pluck("features_details.default_value_id")->first();
                    $this->prestashop_feature_values[$fv_id] = $response_feature_value["product_feature_value"];
                }
            }
        }
    }

    public function feature_value_search($id,$default_value_id,$prestashop_feature_id)
    {   
        $start = microtime(true); // Start time

        $response = "";

        if($id)
        {
            if (isset($this->prestashop_feature_values[$id])) {
                $response = $this->prestashop_feature_values[$id]["id"];
            }
        }

        if(!$response)
        {
            foreach ($this->prestashop_feature_values as $feature_value) {
                if (($feature_value['id_feature'] == $prestashop_feature_id) && ($feature_value['default_value_id'] == $default_value_id)) {
                    $response = $feature_value['id'];
                    break;
                }
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for features value search: ' . $executionTime . ' seconds.');

        return $response;
    }

    public function product_search($id)
    {
        $url = $this->base_url."/api/products/".$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response;
    }

    public function getBrands()
    {
        $start = microtime(true); // Start time

        $url = $this->base_url."/api/manufacturers";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_brands = json_decode($response, true);

        if(isset($response_brands["manufacturers"]))
        {
            foreach($response_brands["manufacturers"] as $brand)
            {
                $brand_id = $brand["id"];
    
                $url = $this->base_url."/api/manufacturers/".$brand_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_brand = json_decode($response, true);
    
                $this->prestashop_brands[] = $response_brand["manufacturer"];
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for getBrands: ' . $executionTime . ' seconds.');

        return;
    }

    public function brands_search($brand,$brand_description,$brands)
    {
        $brand_search = $this->recursiveArraySearch($brand,$brands);

        if($brand_search)
        {
            $xmlData = $this->create_brand_xml($brand,$brand_description,$brand_search);
            $req_type = "PUT";
        }
        else
        {
            $xmlData = $this->create_brand_xml($brand,$brand_description);
            $req_type = "POST";
        }

        $apiUrl = $this->base_url."/api/manufacturers";
            
        // Initialize cURL session
        $ch = curl_init($apiUrl);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        
        // Check for cURL errors
        // if (curl_errno($ch)) {
        //     echo "cURL Error: " . curl_error($ch);
        // }
        
        // Close cURL session
        curl_close($ch);
        $response = json_decode($response, true);

        $this->prestashop_brands[] = $response["manufacturer"];
        $brand_search = $response["manufacturer"]["id"];

        return $brand_search;
    }

    public function getCategories()
    {
        $start = microtime(true); // Start time

        $url = $this->base_url."/api/categories";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_categories = json_decode($response, true);

        if(isset($response_categories["categories"]))
        {
            foreach($response_categories["categories"] as $cat)
            {
                $cat_id = $cat["id"];
    
                $url = $this->base_url."/api/categories/".$cat_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_category = json_decode($response, true);
    
                $this->prestashop_categories[] = $response_category["category"];
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for getCategories: ' . $executionTime . ' seconds.');

        return;
    }

    public function categories_search($category,$categories)
    {
        $main_category_search = $this->recursiveArraySearch($category, $categories);

        if(!$main_category_search)
        {
            $xmlData = $this->create_category_xml($category);
            
            // API endpoint URL
            $apiUrl = $this->base_url."/api/categories";
            
            // Initialize cURL session
            $ch = curl_init($apiUrl);
            
            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            $response = curl_exec($ch);
            
            // Check for cURL errors
            // if (curl_errno($ch)) {
            //     echo "cURL Error: " . curl_error($ch);
            // }
            
            // Close cURL session
            curl_close($ch);
            $response = json_decode($response, true);

            $this->prestashop_categories[] = $response["category"];
            $main_category_search = $response["category"]["id"];
        }

        return $main_category_search;
    }

    public function create_product_xml($main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$id,$description,$tax_rule_group,$default_image_id,$brand_id)
    {
        if($default_image_id)
        {
            $default_image_XML = <<<XML
            <id_default_image><![CDATA[$default_image_id]]></id_default_image>
            XML;
        }
        else
        {
            $default_image_XML = "";
        }

        if($unit)
        {
            $unit_XML = <<<XML
            <unity><![CDATA[$unit]]></unity>
            XML;
        }
        else
        {
            $unit_XML = "";
        }

        if($tax_rule_group)
        {
            $tax_rule_group = <<<XML
            <id_tax_rules_group><![CDATA[$tax_rule_group]]></id_tax_rules_group>
            XML;
        }
        else
        {
            $tax_rule_group = "";
        }

        if($id)
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <id><![CDATA[$id]]></id>
                    $default_image_XML
                    <id_category_default><![CDATA[$main_category_id]]></id_category_default>
                    <id_manufacturer><![CDATA[$brand_id]]></id_manufacturer>
                    <state><![CDATA[1]]></state>
                    <available_for_order><![CDATA[1]]></available_for_order>
                    <show_price><![CDATA[1]]></show_price>
                    $tax_rule_group
                    $unit_XML
                    <price><![CDATA[$price]]></price>
                    <unit_price><![CDATA[$unit_price]]></unit_price>
                    <active><![CDATA[1]]></active>
                    <name>
                        <language id="1"><![CDATA[$title]]></language>
                        <language id="2"><![CDATA[$title]]></language>
                    </name>
                    <description>
                        <language id="1"><![CDATA[$description]]></language>
                        <language id="2"><![CDATA[$description]]></language>
                    </description>
                    <associations>
                        <categories>$categories_xml</categories>
                        <product_features>$features_xml</product_features>
                    </associations>
                </product>
            </prestashop>
            XML;
        }
        else
        {
            return <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <id_category_default><![CDATA[$main_category_id]]></id_category_default>
                    <id_manufacturer><![CDATA[$brand_id]]></id_manufacturer>
                    <new><![CDATA[1]]></new>
                    <state><![CDATA[1]]></state>
                    <available_for_order><![CDATA[1]]></available_for_order>
                    <show_price><![CDATA[1]]></show_price>
                    $tax_rule_group
                    $unit_XML
                    <price><![CDATA[$price]]></price>
                    <unit_price><![CDATA[$unit_price]]></unit_price>
                    <active><![CDATA[1]]></active>
                    <name>
                        <language id="1"><![CDATA[$title]]></language>
                        <language id="2"><![CDATA[$title]]></language>
                    </name>
                    <description>
                        <language id="1"><![CDATA[$description]]></language>
                        <language id="2"><![CDATA[$description]]></language>
                    </description>
                    <associations>
                        <categories>$categories_xml</categories>
                        <product_features>$features_xml</product_features>
                    </associations>
                </product>
            </prestashop>
            XML;
        }
    }

    public function create_combination_xml($product_id,$model,$color,$model_price,$combination_id,$image = NULL,$default_color_id,$default_flag)
    {
        $combinationData = "";
        $xmlData = "";
        $imageData = "";
        $defaultCombination = "";

        if($default_flag == 0 && $default_color_id && ($default_color_id === $color))
        {
            $default_flag = 1;

            $defaultCombination .= <<<XML
            <default_on><![CDATA[1]]></default_on>
            XML;
        }

        if($combination_id)
        {
            $combinationData .= <<<XML
            <id><![CDATA[$combination_id]]></id>
            XML;
        }

        if($image)
        {
            $imageData .= <<<XML
            <images>
                <image>
                    <id><![CDATA[$image]]></id>
                </image>
            </images>
            XML;
        }

        if($model)
        {
            $xmlData .= <<<XML
            <product_option_value>
                <id><![CDATA[$model]]></id>
            </product_option_value>
            XML;
        }

        if($color)
        {
            $xmlData .= <<<XML
            <product_option_value>
                <id><![CDATA[$color]]></id>
            </product_option_value>
            XML;
        }
        
        return [<<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
            <combination>
                $combinationData
                <id_product><![CDATA[$product_id]]></id_product>
                <minimal_quantity><![CDATA[1]]></minimal_quantity>
                <price><![CDATA[$model_price]]></price>
                $defaultCombination
                <associations>
                    <product_option_values>$xmlData</product_option_values>
                    $imageData
                </associations>
            </combination>
        </prestashop>
        XML,$default_flag];
    }

    public function check_combinations($product_id,$model_id = NULL,$color_id = NULL)
    {
        $url = $this->base_url."/api/products/".$product_id;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        if(isset($response["product"]["associations"]["combinations"]))
        {
            $mh = curl_multi_init();
            $handles = [];
        
            // Initialize cURL handles for each combination request
            foreach ($response["product"]["associations"]["combinations"] as $comb) {
                $comb_id = $comb["id"];
        
                $url = $this->base_url . "/api/combinations/" . $comb_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_multi_add_handle($mh, $ch);
                $handles[$comb_id] = $ch;
            }
        
            // Execute all requests concurrently
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);
        
            // Fetch responses and remove handles
            foreach ($handles as $comb_id => $handle) {
                $response_combination = json_decode(curl_multi_getcontent($handle), true);
                curl_multi_remove_handle($mh, $handle);
                
                $combinations = $response_combination["combination"]["associations"]["product_option_values"];
                $foundIds = [];
                $idsToCheck = array_filter([$model_id, $color_id]);
        
                if(is_array($combinations))
                {
                    foreach ($combinations as $item) {
                        if (in_array($item['id'], $idsToCheck)) {
                            $foundIds[] = $item['id'];
                        }
                    }

                    if ((count($foundIds) == count($idsToCheck)) && (count($idsToCheck) == count($combinations))) {
                        curl_multi_close($mh);
                        return $response_combination["combination"]["id"];
                    }
                }
            }
        
            // Close the multi-handle
            curl_multi_close($mh);
            
            // Code before optimization
            /*foreach($response["product"]["associations"]["combinations"] as $comb)
            {
                $comb_id = $comb["id"];
                $foundIds = [];
    
                $url = $this->base_url."/api/combinations/".$comb_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);
                curl_close($ch);
                $response_combination = json_decode($response, true);
    
                $combinations = $response_combination["combination"]["associations"]["product_option_values"];

                if($model_id && $color_id)
                {
                    $idsToCheck = [$model_id, $color_id];
                }
                elseif($model_id)
                {
                    $idsToCheck = [$model_id];
                }
                else
                {
                    $idsToCheck = [$color_id];
                }

                $i = 0;

                foreach ($combinations as $item) {
                    if (in_array($item['id'], $idsToCheck)) {
                        $foundIds[] = $item['id'];
                    }
                    $i++;
                }
                
                if ((count($foundIds) == count($idsToCheck)) && (count($idsToCheck) == $i)) {
                    return $response_combination["combination"]["id"];
                }
            }*/
        }

        return false;
    }

    // Function to add a cURL handle to the multi-handle
    public function addCurlHandle(&$mh, &$handles, $apiUrl, $req_type, $xmlData, $model = null, $color = null) {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_multi_add_handle($mh, $ch);
        $handles[] = [
            'handle' => $ch,
            'model' => $model,
            'color' => $color,
        ];
    }

    public function executeCurlRequest($url, $customRequest, $postFields = null) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        if ($postFields) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }    

    // Function to delete images concurrently
    public function deleteImages($product_id, $images) {
        $mh = curl_multi_init();
        $handles = [];
    
        foreach ($images as $del) {
            $urlImage = $this->base_url."/api/images/products/".$product_id."/".$del["id"];
            $ch = curl_init($urlImage);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }
    
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
    
        foreach ($handles as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
    
        curl_multi_close($mh);
    }

    public function getMimeType($image_path) {
        $imageType = exif_imagetype($image_path);
        $mimeTypes = [
            IMAGETYPE_GIF => 'image/gif',
            IMAGETYPE_JPEG => 'image/jpeg',
            IMAGETYPE_PNG => 'image/png',
            IMAGETYPE_BMP => 'image/bmp',
            IMAGETYPE_WEBP => 'image/webp'
        ];
        return $mimeTypes[$imageType] ?? 'application/octet-stream';
    }
    
    public function prepareCurlHandle($urlImage, $image_path, $image_mime, $access_key) {
        $args['image'] = new \CurlFile($image_path, $image_mime);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, base64_decode($access_key) . ':');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_URL, $urlImage);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        return $ch;
    }
    
    public function processMultiCurl($multiHandle, $handles) {
        $running = null;
        do {
            curl_multi_exec($multiHandle, $running);
            curl_multi_select($multiHandle);
        } while ($running > 0);
    
        $results = [];
        foreach ($handles as $color_id => $ch) {
            $result = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);

            $results[] = ['result' => $result, 'httpCode' => $httpCode, 'color_id' => $color_id];
        }
    
        return $results;
    }

    public function create_update_product($product_export,$main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$user_id,$related_users,$temp,$ncm,$model_ids,$color_ids,$model_prices,$color,$description,$supplier_id,$tax_rule_group,$allImages,$brand_id)
    {
        $default_image_id = "";
        
        if($product_export)
        {
            $product_search = $this->product_search($product_export->prestashop_product_id);

            if($product_search)
            {
                $xmlData = $this->create_product_xml($main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$product_export->prestashop_product_id,$description,$tax_rule_group,NULL,$brand_id);
                $req_type = "PUT";
            }
            else
            {
                $xmlData = $this->create_product_xml($main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,NULL,$description,$tax_rule_group,NULL,$brand_id);
                $req_type = "POST";
            }
        }
        else
        {
            $xmlData = $this->create_product_xml($main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,NULL,$description,$tax_rule_group,NULL,$brand_id);
            $req_type = "POST";

            $product_export = new prestashop_products_exports;
            $product_export->supplier_id = $supplier_id;
            $product_export->retailer_id = $user_id;
            $product_export->product_id = $temp->id;
            $product_export->package_id = NULL;
            $product_export->shop_url = $this->base_url;
        }

        if($ncm)
        {
            $product_export->non_combination_model_id = $ncm->id;
        }

        if($color)
        {
            $product_export->color_id = $color->id;
            $default_color_id = prestashop_attribute_values_exports::where("product_id",$temp->id)->where("color_id",$color->id)->whereIn("retailer_id",$related_users)->pluck("prestashop_attribute_value_id")->first();
            $default_color_id = intval($default_color_id);
        }
        else
        {
            $default_color_id = "";
        }

        if($allImages)
        {
            $color_images = $allImages;
        }
        else
        {
            $color_images = array();
        }

        $apiUrl = $this->base_url."/api/products";
        $response = $this->executeCurlRequest($apiUrl, $req_type, $xmlData);
        
        $product_id = $response["product"]["id"];
        $images = $response["product"]["associations"]["images"] ?? [];
        $shop_id = $response["product"]["id_shop_default"];
        $product_export->prestashop_product_id = $product_id;
        
        $exported_images_ids = [];
        
        if (count($images)) {
            $this->deleteImages($product_id, $images);
        }
        
        if (count($color_images)) {

            $urlImage = $this->base_url . "/api/images/products/" . $product_id;

            $multiHandle = curl_multi_init();
            $handles = [];
            $cids = [];

            foreach ($color_images as $img) {
                if (($color->id == $img->color_id) || !in_array($img->color_id,$cids)) {
                    $image_path = public_path() . '/assets/colorImages/' . $img->image;
            
                    if (file_exists($image_path)) {

                        if (filesize($image_path) < 1.5 * 1024 * 1024) { // check if less than 1.5mb

                            if(!$ncm && !in_array($img->color_id,$cids))
                            {
                                $cids[] = $img->color_id;
                            }
    
                            $image_mime = $this->getMimeType($image_path);
                            $ch = $this->prepareCurlHandle($urlImage, $image_path, $image_mime, $this->access_key);
                            $handles[$img->color_id] = $ch;
                            curl_multi_add_handle($multiHandle, $ch);

                        }
                    }
                }
            }
            
            $results = $this->processMultiCurl($multiHandle, $handles);
            curl_multi_close($multiHandle);
            
            foreach ($results as $result) {
                $httpCode = $result['httpCode'];
                $resultContent = $result['result'];
                if (!$ncm && $httpCode == 200) {
                    $startPosition = strpos($resultContent, '<?xml');
                    if ($startPosition !== false) {
                        $xmlPart = substr($resultContent, $startPosition);
                        $xml = simplexml_load_string($xmlPart);
                        $exported_images_ids[$result['color_id']] = (string)$xml->image->id;
                        if($color->id == $result['color_id'])
                        {
                            $default_image_id = (string)$xml->image->id;
                        }
                    } else {
                        $exported_images_ids[$result['color_id']] = "";
                    }
                }
            }
        }

        //Code before optimization
        /*$apiUrl = $this->base_url."/api/products";
        $ch = curl_init($apiUrl);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        // Check for cURL errors
        // if (curl_errno($ch)) {
        //     echo "cURL Error: " . curl_error($ch);
        // }
        
        // Close cURL session
        curl_close($ch);
        $response = json_decode($response, true);
        $product_id = $response["product"]["id"];
        $images = isset($response["product"]["associations"]["images"]) ? $response["product"]["associations"]["images"] : [];
        $shop_id = $response["product"]["id_shop_default"];
        $product_export->prestashop_product_id = $product_id;

        $exported_images_ids = [];

        if(count($images))
        {
            foreach($images as $del)
            {
                $urlImage = $this->base_url."/api/images/products/".$product_id."/".$del["id"];
                $ch = curl_init($urlImage);
                    
                // Set cURL options
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
            }
        }

        if(count($color_images))
        {
            $urlImage = $this->base_url."/api/images/products/".$product_id;
            $cids = [];

            foreach($color_images as $ci => $img)
            {
                if(($color->id == $img->color_id) || !in_array($img->color_id,$cids))
                {
                    // $image_path = asset('/assets/colorImages/'.$color_images[0]->image);
                    $image_path = public_path() . '/assets/colorImages/'.$img->image;
            
                    if(file_exists($image_path))
                    {
                        $imageType = exif_imagetype($image_path);
            
                        // Map image type to MIME type
                        $mimeTypes = [
                            IMAGETYPE_GIF => 'image/gif',
                            IMAGETYPE_JPEG => 'image/jpeg',
                            IMAGETYPE_PNG => 'image/png',
                            IMAGETYPE_BMP => 'image/bmp',
                            IMAGETYPE_WEBP => 'image/webp'
                        ];
                        
                        // Get the MIME type from the image type
                        $image_mime = $mimeTypes[$imageType] ?? 'application/octet-stream';
                        // $image_path = urlencode($image_path);
                        
                        $args['image'] = new \CurlFile($image_path, $image_mime);
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_HEADER, 1);
                        curl_setopt($ch, CURLOPT_USERPWD, base64_decode($this->access_key).':');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
                        curl_setopt($ch, CURLOPT_URL, $urlImage);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
                        $result = curl_exec($ch);
        
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
    
                        if(!$ncm && $httpCode == 200 && !in_array($img->color_id,$cids))
                        {
                            $cids[] = $img->color_id;
                            $startPosition = strpos($result, '<?xml');
    
                            if($startPosition)
                            {
                                $xmlPart = substr($result, $startPosition);
                                $xml = simplexml_load_string($xmlPart);
                                $exported_images_ids[$img->color_id] = (string)$xml->image->id;
                            }
                            else
                            {
                                $exported_images_ids[$img->color_id] = "";
                            }
                        }
                    }
                }
            }
        }*/
        
        if(!$ncm)
        {
            $default_flag = 0;

            $requests = [];
            $handles = [];
            
            // Initialize cURL multi-handle
            $mh = curl_multi_init();
            
            if (count($model_ids) && count($color_ids)) {
                foreach ($model_ids as $m => $model) {
                    foreach ($color_ids as $c => $color) {
                        $image_id = $exported_images_ids[$c] ?? "";
                        $combination_id = $this->check_combinations($product_id, $model, $color);
            
                        if (!$combination_id) {
                            $req_type = "POST";
                            $combinationResponse = $this->create_combination_xml($product_id, $model, $color, $model_prices[$m], null, $image_id, $default_color_id, $default_flag);
                        } else {
                            $req_type = "PUT";
                            $combinationResponse = $this->create_combination_xml($product_id, $model, $color, $model_prices[$m], $combination_id, $image_id, $default_color_id, $default_flag);
                        }
            
                        $xmlData = $combinationResponse[0];
                        $default_flag = $combinationResponse[1];
                        $apiUrl = $this->base_url . "/api/combinations";
                        $this->addCurlHandle($mh, $handles, $apiUrl, $req_type, $xmlData, $model, $color);
                    }
                }
            } elseif (count($model_ids)) {
                foreach ($model_ids as $m => $model) {
                    $combination_id = $this->check_combinations($product_id, $model);
            
                    if (!$combination_id) {
                        $req_type = "POST";
                        $combinationResponse = $this->create_combination_xml($product_id, $model, null, $model_prices[$m], null, null, $default_color_id, $default_flag);
                    } else {
                        $req_type = "PUT";
                        $combinationResponse = $this->create_combination_xml($product_id, $model, null, $model_prices[$m], $combination_id, null, $default_color_id, $default_flag);
                    }
            
                    $xmlData = $combinationResponse[0];
                    $default_flag = $combinationResponse[1];
                    $apiUrl = $this->base_url . "/api/combinations";
                    $this->addCurlHandle($mh, $handles, $apiUrl, $req_type, $xmlData, $model);
                }
            } elseif (count($color_ids)) {
                foreach ($color_ids as $c => $color) {
                    $image_id = $exported_images_ids[$c] ?? "";
                    $combination_id = $this->check_combinations($product_id, null, $color);
            
                    if (!$combination_id) {
                        $req_type = "POST";
                        $combinationResponse = $this->create_combination_xml($product_id, null, $color, 0, null, $image_id, $default_color_id, $default_flag);
                    } else {
                        $req_type = "PUT";
                        $combinationResponse = $this->create_combination_xml($product_id, null, $color, 0, $combination_id, $image_id, $default_color_id, $default_flag);
                    }
            
                    $xmlData = $combinationResponse[0];
                    $default_flag = $combinationResponse[1];
                    $apiUrl = $this->base_url . "/api/combinations";
                    $this->addCurlHandle($mh, $handles, $apiUrl, $req_type, $xmlData, null, $color);
                }
            }
            
            // Execute all requests concurrently
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);
            
            // Fetch responses and remove handles
            foreach ($handles as $handle_info) {
                $response = json_decode(curl_multi_getcontent($handle_info['handle']), true);
                curl_multi_remove_handle($mh, $handle_info['handle']);
            
                if (isset($response["combination"]["id"])) {
                    $combination_id = $response["combination"]["id"];
                    // You can save the combination_id or perform other operations here if needed
                }
            }
            
            // Close the multi-handle
            curl_multi_close($mh);

            // Code before optimization
            /*if(count($model_ids) && count($color_ids))
            {
                foreach($model_ids as $m => $model)
                {
                    foreach($color_ids as $c => $color)
                    {
                        $image_id = isset($exported_images_ids[$c]) ? $exported_images_ids[$c] : "";
                        $combination_id = $this->check_combinations($product_id,$model,$color);
    
                        if(!$combination_id)
                        {
                            $req_type = "POST";
                            $combinationResponse = $this->create_combination_xml($product_id,$model,$color,$model_prices[$m],NULL,$image_id,$default_color_id,$default_flag);
                            $xmlData = $combinationResponse[0];
                            $default_flag = $combinationResponse[1];
                        }
                        else
                        {
                            $req_type = "PUT";
                            $combinationResponse = $this->create_combination_xml($product_id,$model,$color,$model_prices[$m],$combination_id,$image_id,$default_color_id,$default_flag);
                            $xmlData = $combinationResponse[0];
                            $default_flag = $combinationResponse[1];
                        }
    
                        $apiUrl = $this->base_url."/api/combinations";
                        $ch = curl_init($apiUrl);
                        
                        // Set cURL options
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        $response = json_decode($response, true);
                        $combination_id = $response["combination"]["id"];
                    }
                }
            }
            elseif(count($model_ids))
            {
                foreach($model_ids as $m => $model)
                {
                    $combination_id = $this->check_combinations($product_id,$model);
    
                    if(!$combination_id)
                    {
                        $req_type = "POST";
                        $combinationResponse = $this->create_combination_xml($product_id,$model,NULL,$model_prices[$m],NULL,NULL,$default_color_id,$default_flag);
                        $xmlData = $combinationResponse[0];
                        $default_flag = $combinationResponse[1];
                    }
                    else
                    {
                        $req_type = "PUT";
                        $combinationResponse = $this->create_combination_xml($product_id,$model,NULL,$model_prices[$m],$combination_id,NULL,$default_color_id,$default_flag);
                        $xmlData = $combinationResponse[0];
                        $default_flag = $combinationResponse[1];
                    }
    
                    $apiUrl = $this->base_url."/api/combinations";
                    $ch = curl_init($apiUrl);
                    
                    // Set cURL options
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
                    $combination_id = $response["combination"]["id"];
                }
            }
            elseif(count($color_ids))
            {
                foreach($color_ids as $c => $color)
                {
                    $image_id = isset($exported_images_ids[$c]) ? $exported_images_ids[$c] : "";
                    $combination_id = $this->check_combinations($product_id,NULL,$color);
    
                    if(!$combination_id)
                    {
                        $req_type = "POST";
                        $combinationResponse = $this->create_combination_xml($product_id,NULL,$color,0,NULL,$image_id,$default_color_id,$default_flag);
                        $xmlData = $combinationResponse[0];
                        $default_flag = $combinationResponse[1];
                    }
                    else
                    {
                        $req_type = "PUT";
                        $combinationResponse = $this->create_combination_xml($product_id,NULL,$color,0,$combination_id,$image_id,$default_color_id,$default_flag);
                        $xmlData = $combinationResponse[0];
                        $default_flag = $combinationResponse[1];
                    }
    
                    $apiUrl = $this->base_url."/api/combinations";
                    $ch = curl_init($apiUrl);
                    
                    // Set cURL options
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $response = json_decode($response, true);
                    $combination_id = $response["combination"]["id"];
                }
            }*/

            if($default_image_id)
            {
                $xmlData = $this->create_product_xml($main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$product_id,$description,$tax_rule_group,$default_image_id,$brand_id);
                $req_type = "PUT";
                $apiUrl = $this->base_url."/api/products";
                $response = $this->executeCurlRequest($apiUrl, $req_type, $xmlData);
            }
        }

        if($ncm && ($ncm->measure == "M1" || $ncm->measure == "M2"))
        {
            $check_package = "";

            if($product_export->package_id)
            {
                $check_package = $this->check_package($product_export->package_id);
            }
    
            if(!$check_package)
            {
                $package_id = $this->create_update_package(NULL,$product_id,$shop_id,$ncm);
            }
            else
            {
                $package_id = $this->create_update_package($check_package,$product_id,$shop_id,$ncm);
            }

            $product_export->package_id = $package_id;
        }
        
        $product_export->save();
        
        return $product_export->id;
    }

    public function check_package($package_id)
    {
        $url = $this->base_url."/api/pap_product/".$package_id;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        if(isset($response["product"]))
        {
            return $response["product"]["id"];
        }

        return false;
    }

    public function create_update_package($package_id,$product_id,$shop_id,$ncm)
    {
        $typeData = "";
        
        if($ncm->measure == "M2")
        {
            $typeData .= <<<XML
            <pack_area><![CDATA[$ncm->estimated_price_quantity]]></pack_area>
            <area_price><![CDATA[$ncm->estimated_price_per_box]]></area_price>
            <roll_height><![CDATA[0.00]]></roll_height>
            <roll_width><![CDATA[0.00]]></roll_width>
            XML;
        }
        else
        {
            $typeData .= <<<XML
            <pack_area><![CDATA[0]]></pack_area>
            <area_price><![CDATA[0]]></area_price>
            <roll_height><![CDATA[$ncm->max_height]]></roll_height>
            <roll_width><![CDATA[$ncm->max_width]]></roll_width>
            XML;
        }

        $calculation_type = $ncm->measure == "M2" ? "normal" : "rolls";

        if($package_id)
        {
            $xmlData = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <id><![CDATA[$package_id]]></id>
                    <id_product><![CDATA[$product_id]]></id_product>
                    <id_shop><![CDATA[$shop_id]]></id_shop>
                    <id_pap_unit_default><![CDATA[0]]></id_pap_unit_default>
                    <enabled><![CDATA[1]]></enabled>
                    <calculation_type><![CDATA[$calculation_type]]></calculation_type>
                    <dynamic_price><![CDATA[0]]></dynamic_price>
                    <unit_conversion_enabled><![CDATA[0]]></unit_conversion_enabled>
                    <unit_conversion_operator><![CDATA[]]></unit_conversion_operator>
                    <unit_conversion_value><![CDATA[0.00]]></unit_conversion_value>
                    $typeData
                    <pattern_repeat><![CDATA[0.0000]]></pattern_repeat>
                    <coverage><![CDATA[0.00]]></coverage>
                    <wastage_options><![CDATA[]]></wastage_options>
                </product>
            </prestashop>
            XML;

            $req_type = "PUT";
        }
        else
        {
            $xmlData = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <id_product><![CDATA[$product_id]]></id_product>
                    <id_shop><![CDATA[$shop_id]]></id_shop>
                    <id_pap_unit_default><![CDATA[0]]></id_pap_unit_default>
                    <enabled><![CDATA[1]]></enabled>
                    <calculation_type><![CDATA[$calculation_type]]></calculation_type>
                    <dynamic_price><![CDATA[0]]></dynamic_price>
                    <unit_conversion_enabled><![CDATA[0]]></unit_conversion_enabled>
                    <unit_conversion_operator><![CDATA[]]></unit_conversion_operator>
                    <unit_conversion_value><![CDATA[0.00]]></unit_conversion_value>
                    $typeData
                    <pattern_repeat><![CDATA[0.0000]]></pattern_repeat>
                    <coverage><![CDATA[0.00]]></coverage>
                    <wastage_options><![CDATA[]]></wastage_options>
                </product>
            </prestashop>
            XML;
            $req_type = "POST";
        }

        $apiUrl = $this->base_url."/api/pap_product";
        $ch = curl_init($apiUrl);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        // Check for cURL errors
        // if (curl_errno($ch)) {
            //     echo "cURL Error: " . curl_error($ch);
        // }
        
        // Close cURL session
        curl_close($ch);
        $response = json_decode($response, true);
        $package_id = $response["product"]["id"];

        return $package_id;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $organization_id = $this->organization_id;
        // $fromAddress = "";
        // $emailSettings = EmailSetting::where('organization_id', $organization_id)->first();

        // if ($emailSettings) {
        //     // Override the mail configuration
        //     $emailSettings->password = Crypt::decryptString($emailSettings->password);
    
        //     config([
        //         'mail.driver' => 'smtp',
        //         'mail.host' => $emailSettings->host,
        //         'mail.port' => $emailSettings->port,
        //         'mail.encryption' => $emailSettings->encryption,
        //         'mail.username' => $emailSettings->username,
        //         'mail.password' => $emailSettings->password
        //     ]);

        //     $fromAddress = $emailSettings->username;
        // }

        $access_key = $this->access_key;
        $suppliers = $this->suppliers;
        $sub_categories = $this->sub_categories;
        $user_id = $this->user_id;
        $user = $this->user;
        $related_users = $this->related_users;
        $floor_category_id = Category::where('cat_name','LIKE', '%Floors%')->orWhere('cat_name','LIKE', '%Vloeren%')->pluck('id')->first();
        $array_statuses = array();

        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', -1);

        $this->getAttributes();
        $sizes_attribute_id = $this->attributes_search("Sizes","afmetingen",$this->prestashop_attributes);
        $colors_attribute_id = $this->attributes_search("Colors","kleuren",$this->prestashop_attributes);
        $this->getCategories();
        $this->getBrands();
        $this->getFeatures();
        $this->getFeatureValues();
        $this->getAttributeValues();

        $start = microtime(true); // Start time
        
        $tax_rule_group = "";

        $url = $this->base_url."/api/tax_rules";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response_tax_rules = curl_exec($ch);
        curl_close($ch);
        $response_tax_rules = json_decode($response_tax_rules, true);

        // Code before optimization
        /*if(isset($response_tax_rules["tax_rules"]))
        {
            foreach($response_tax_rules["tax_rules"] as $rules)
            {
                $url = $this->base_url."/api/tax_rules/".$rules["id"];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_tax_rule = curl_exec($ch);
                curl_close($ch);
                $response_tax_rule = json_decode($response_tax_rule, true);

                $url = $this->base_url."/api/taxes/".$response_tax_rule["tax_rule"]["id_tax"];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response_tax = curl_exec($ch);
                curl_close($ch);
                $response_tax = json_decode($response_tax, true);

                if($response_tax["tax"]["rate"] == 21)
                {
                    $tax_rule_group = $response_tax_rule["tax_rule"]["id_tax_rules_group"];
                    break;
                }
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for tax api: ' . $executionTime . ' seconds.'); */

        if (isset($response_tax_rules["tax_rules"])) {
            $mh = curl_multi_init();
            $handles = [];
            $results = [];
    
            foreach ($response_tax_rules["tax_rules"] as $rules) {
                $tax_rule_url = $this->base_url . "/api/tax_rules/" . $rules["id"];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $tax_rule_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_multi_add_handle($mh, $ch);
                $handles[$rules["id"]] = $ch;
            }
    
            // Execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
    
            // Fetch responses and remove handles
            foreach ($handles as $id => $handle) {
                $results[$id] = json_decode(curl_multi_getcontent($handle), true);
                curl_multi_remove_handle($mh, $handle);
            }
            curl_multi_close($mh);
    
            // Second round of requests for tax details
            $mh = curl_multi_init();
            $handles = [];
            $tax_results = [];
    
            foreach ($results as $rule_id => $response_tax_rule) {
                if($response_tax_rule)
                {
                    $tax_url = $this->base_url . "/api/taxes/" . $response_tax_rule["tax_rule"]["id_tax"];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $tax_url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_multi_add_handle($mh, $ch);
                    $handles[$rule_id] = $ch;
                }
            }
    
            // Execute all queries simultaneously, and continue when all are complete
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while ($running);
    
            // Fetch responses and remove handles
            foreach ($handles as $rule_id => $handle) {
                $tax_results[$rule_id] = json_decode(curl_multi_getcontent($handle), true);
                curl_multi_remove_handle($mh, $handle);
            }
            curl_multi_close($mh);
    
            // Process results
            foreach ($tax_results as $rule_id => $response_tax) {
                if($response_tax)
                {
                    if ($response_tax["tax"]["rate"] == 21) {
                        $response_tax_rule = $results[$rule_id];
                        $tax_rule_group = $response_tax_rule["tax_rule"]["id_tax_rules_group"];
                        break;
                    }
                }
            }
        }

        $end = microtime(true); // End time
        $executionTime = $end - $start; // Calculate execution time

        \Log::info('Execution time for tax api: ' . $executionTime . ' seconds.');

        foreach($suppliers as $s => $key)
        {
            $supplier_organization = organizations::findOrFail($key);
            // $supplier_related_users = $supplier_organization->users()->withTrashed()->select('users.id')->pluck('id');
            
            $products = Products::leftjoin("brands","brands.id","=","products.brand_id")->where("products.organization_id",$supplier_organization->id)->where("products.category_id",$floor_category_id);
            // $products = Products::where("id",2516)->get();

            if($sub_categories)
            {
                $products = $products->whereIn("products.sub_category_id",$sub_categories);
            }

            $products = $products->withTrashed()->select("products.*","brands.cat_name as brand_name","brands.description as brand_description","brands.photo as brand_photo")->get();
            
            $f_array = array();
            $x_array = array();
            $p_array = array();
            $y_array = array();

            foreach($products as $p => $temp)
            {
                $product_search = "";
                $check_update = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("retailer_id",$related_users)->where("product_id",$temp->id)->first();
                
                if($check_update)
                {
                    $product_search = $this->product_search($check_update->prestashop_product_id);
                }
                
                if (!$product_search || ($temp->updated_at > $check_update->updated_at))
                {
                    $p_array[] = $temp->id;

                    if(!$temp->deleted_at)
                    {
                        $main_category = retailer_mapped_categories::whereIn("retailer_id",$related_users)->where("cat_id",$temp->category_id)->pluck("title")->first();
    
                        if(!$main_category)
                        {
                            $main_category = all_categories::where("id",$temp->category_id)->pluck("cat_name")->first();
                        }
    
                        $start = microtime(true); // Start time
        
                        $main_category_id = $this->categories_search($main_category, $this->prestashop_categories);
    
                        $end = microtime(true); // End time
                        $executionTime = $end - $start; // Calculate execution time
                
                        \Log::info('Execution time for main categories_search: ' . $executionTime . ' seconds.');
        
                        $categories_xml = <<<XML
                        <category>
                            <id><![CDATA[$main_category_id]]></id>
                        </category>
                        XML;
        
                        if($temp->sub_category_id)
                        {
                            $start = microtime(true); // Start time
    
                            $sub_category = retailer_mapped_categories::whereIn("retailer_id",$related_users)->where("cat_id",$temp->sub_category_id)->pluck("title")->first();
        
                            if(!$sub_category)
                            {
                                $sub_category = all_categories::where("id",$temp->sub_category_id)->pluck("cat_name")->first();
                            }
        
                            $sub_category_id = $this->categories_search($sub_category, $this->prestashop_categories);
        
                            $categories_xml .= <<<XML
                            <category>
                                <id><![CDATA[$sub_category_id]]></id>
                            </category>
                            XML;
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for sub categories_search: ' . $executionTime . ' seconds.');
                        }
    
                        $start = microtime(true); // Start time
    
                        $prestashop_brand_id = $this->brands_search($temp->brand_name,$temp->brand_description,$this->prestashop_brands);
                        
                        if($temp->brand_photo)
                        {
                            $urlBrandImage = $this->base_url . "/api/images/manufacturers/" . $prestashop_brand_id;        
                            $brand_image_path = public_path() . '/assets/images/' . $temp->brand_photo;
                            
                            if (file_exists($brand_image_path)) {
        
                                if (filesize($brand_image_path) < 1.5 * 1024 * 1024) { // check if less than 1.5mb
            
                                    $brand_image_mime = $this->getMimeType($brand_image_path);
                                    $ch = $this->prepareCurlHandle($urlBrandImage, $brand_image_path, $brand_image_mime, $this->access_key);
                                    $result = curl_exec($ch);
                        
                                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    curl_close($ch);
        
                                }
                            }
                        }
    
                        $end = microtime(true); // End time
                        $executionTime = $end - $start; // Calculate execution time
                
                        \Log::info('Execution time for brands_search: ' . $executionTime . ' seconds.');
        
                        $non_combination_based_models = $temp->models->filter(function ($item) {
                            return $item['combination'] == 0;
                        });
        
                        $combination_based_models = $temp->models->filter(function ($item) {
                            return $item['combination'] == 1;
                        });
        
                        $features = features::leftjoin("default_features","default_features.id","=","features.default_feature_id")->whereHas('features', function($query) use($temp)
                        {
                            $query->where('product_features.product_id','=',$temp->id);
            
                        })->with(['features' => function($query) use($temp)
                        {
                            $query->where('product_features.product_id','=',$temp->id);
            
                        }])->select("features.*","default_features.title")->get();
        
                        $model_ids = array();
                        $model_prices = array();
        
                        foreach($combination_based_models as $model)
                        {
                            $start = microtime(true); // Start time
                            $default_model_detail_id = $model->default_model_detail_id;
                            $model_value = $model->size_id ? $model->size_title : $model->model;
    
                            $check_prestashop_av = prestashop_attribute_values_exports::where("product_id",$temp->id)->whereIn("retailer_id",$related_users)->where("model_id",$model->id)->first();
                    
                            if($check_prestashop_av)
                            {
                                $attribute_value_search = $this->attribute_value_search($check_prestashop_av->prestashop_attribute_value_id,$default_model_detail_id,$model_value);
                            }
                            else
                            {
                                $attribute_value_search = $this->attribute_value_search(NULL,$default_model_detail_id,$model_value);
                            }
    
                            if($attribute_value_search)
                            {
                                $xmlData = $this->create_attribute_value_xml($model_value,$sizes_attribute_id,$attribute_value_search);
                                $req_type = "PUT";
                            }
                            else
                            {
                                $xmlData = $this->create_attribute_value_xml($model_value,$sizes_attribute_id);
                                $req_type = "POST";
                            }
                            
                            $apiUrl = $this->base_url."/api/product_option_values";
                            // Initialize cURL session
                            $ch = curl_init($apiUrl);
                            
                            // Set cURL options
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                            $response = curl_exec($ch);
                            
                            // Check for cURL errors
                            // if (curl_errno($ch)) {
                            //     echo "cURL Error: " . curl_error($ch);
                            // }
                            
                            // Close cURL session
                            curl_close($ch);
                            $response = json_decode($response, true);
                
                            $attribute_value_id = $response["product_option_value"]["id"];
                        
                            if (!$check_prestashop_av) {
                                $check_prestashop_av = new prestashop_attribute_values_exports;
                                $check_prestashop_av->product_id = $temp->id;
                                $check_prestashop_av->retailer_id = $user_id;
                                $check_prestashop_av->model_id = $model->id;
                            }
                        
                            $check_prestashop_av->prestashop_attribute_value_id = $attribute_value_id;
                            $check_prestashop_av->save();
                
                            $this->prestashop_attribute_values[$attribute_value_id] = [
                                'id' => $attribute_value_id,
                                'id_attribute_group' => $sizes_attribute_id,
                                'name' => $model_value,
                                'default_id' => $default_model_detail_id
                            ];
    
                            $model_ids[] = $attribute_value_id;
                            $model_prices[] = $model->estimated_price ? sprintf('%.6f', ($model->estimated_price/1.21)) : 0;
                            $x_array[] = $check_prestashop_av->id;
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for combination_based_models: ' . $executionTime . ' seconds.');
                        }
        
                        $color_ids = array();
        
                        foreach($temp->colors as $color)
                        {
                            $start = microtime(true); // Start time
    
                            $check_prestashop_av = prestashop_attribute_values_exports::where("product_id",$temp->id)->whereIn("retailer_id",$related_users)->where("color_id",$color->id)->first();
                    
                            if($check_prestashop_av)
                            {
                                $attribute_value_search = $this->attribute_value_search($check_prestashop_av->prestashop_attribute_value_id);
        
                                if($attribute_value_search)
                                {
                                    $xmlData = $this->create_attribute_value_xml($color->title,$colors_attribute_id,$check_prestashop_av->prestashop_attribute_value_id);
                                    $req_type = "PUT";
                                }
                                else
                                {
                                    $xmlData = $this->create_attribute_value_xml($color->title,$colors_attribute_id);
                                    $req_type = "POST";
                                }
                            }
                            else
                            {
                                $xmlData = $this->create_attribute_value_xml($color->title,$colors_attribute_id);
                                $req_type = "POST";
                                $check_prestashop_av = new prestashop_attribute_values_exports;
                                $check_prestashop_av->product_id = $temp->id;
                                $check_prestashop_av->retailer_id = $user_id;
                                $check_prestashop_av->color_id = $color->id;
                            }
                            
                            $apiUrl = $this->base_url."/api/product_option_values";
                            // Initialize cURL session
                            $ch = curl_init($apiUrl);
                            
                            // Set cURL options
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_type);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                            $response = curl_exec($ch);
                            
                            // Check for cURL errors
                            // if (curl_errno($ch)) {
                            //     echo "cURL Error: " . curl_error($ch);
                            // }
                            
                            // Close cURL session
                            curl_close($ch);
                            $response = json_decode($response, true);
                
                            $attribute_value_id = (int)$response["product_option_value"]["id"];
                            $color_ids[$color->id] = $attribute_value_id;
        
                            $check_prestashop_av->prestashop_attribute_value_id = $attribute_value_id;
                            $check_prestashop_av->save();
                            $x_array[] = $check_prestashop_av->id;
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for colors: ' . $executionTime . ' seconds.');
                        }
        
                        $exported_data = array();
        
                        foreach($features as $feature)
                        {
                            $start = microtime(true); // Start time
    
                            $check_prestashop_feature = prestashop_features_exports::where("product_id",$temp->id)->whereIn("retailer_id",$related_users)->where("feature_id",$feature->id)->first();
                    
                            if($check_prestashop_feature)
                            {
                                $feature_data = $this->create_update_feature($feature,$check_prestashop_feature,$user_id,$related_users,1,$temp->id);
                            }
                            else
                            {
                                $feature_data = $this->create_update_feature($feature,NULL,$user_id,$related_users,2,$temp->id);
                            }
        
                            $f_array[] = $feature_data[0];
                            $exported_data[] = $feature_data[1];
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for features: ' . $executionTime . ' seconds.');
                        }
        
                        $features_xml = $this->create_features_xml($exported_data);
        
                        $description = $temp->description;
                        //Next replace unify all new-lines into unix LF:
                        $description = str_replace("\r","\n", $description);
                        $description = str_replace("\n\n","\n", $description);
        
                        //Next replace all new lines with the <br>:
                        $description = str_replace("\n","<br>", $description);
                        $colors = $temp->colors;
                        
                        if(count($non_combination_based_models))
                        {
                            $start = microtime(true); // Start time
    
                            foreach($non_combination_based_models as $ncm)
                            {
                                $unit_price = $ncm->estimated_price ? sprintf('%.6f', ($ncm->estimated_price/1.21)) : 0;
                                $price = $ncm->estimated_price_per_box ? sprintf('%.6f', ($ncm->estimated_price_per_box/1.21)) : 0;
                                $unit = $ncm->measure == "M1" ? "m¹" : ($ncm->measure == "M2" ? "m²" : $ncm->measure);
        
                                if(count($colors))
                                {
                                    foreach($colors as $color)
                                    {
                                        $allImages = $color->images;
                                        $title = $temp->title . " (" . $ncm->model . ") (" . $color->title . ")";
                
                                        $product_export = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("retailer_id",$related_users)->where("product_id",$temp->id)->where("non_combination_model_id",$ncm->id)->where("color_id",$color->id)->first();
                                        $y_array[] = $this->create_update_product($product_export,$main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$user_id,$related_users,$temp,$ncm,$model_ids,$color_ids,$model_prices,$color,$description,$key,$tax_rule_group,$allImages,$prestashop_brand_id);
                                    }
                                }
                                else
                                {
                                    $title = $temp->title . " (" . $ncm->model . ")";
            
                                    $product_export = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("retailer_id",$related_users)->where("product_id",$temp->id)->where("non_combination_model_id",$ncm->id)->where("color_id",NULL)->first();
                                    $y_array[] = $this->create_update_product($product_export,$main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$user_id,$related_users,$temp,$ncm,$model_ids,$color_ids,$model_prices,NULL,$description,$key,$tax_rule_group,NULL,$prestashop_brand_id);
                                }
                            }
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for non_combination_based_models: ' . $executionTime . ' seconds.');
                        }
        
                        if(count($combination_based_models) || (count($non_combination_based_models) == 0 && count($combination_based_models) == 0))
                        {
                            $start = microtime(true); // Start time
    
                            $unit_price = 0;
                            $price = 0;
                            $unit = "";
        
                            if(count($colors))
                            {
                                foreach($colors as $color)
                                {
                                    $currentImages = color_images::where('color_id', $color->id)->where("product_id",$temp->id)->get();
                                    $otherImages = color_images::where('color_id', '!=', $color->id)->where("product_id",$temp->id)->orderBy('id', 'asc')->get();
                                    $allImages = $currentImages->merge($otherImages);
    
                                    $title = $temp->title . " (" . $color->title . ")";
                
                                    $product_export = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("retailer_id",$related_users)->where("product_id",$temp->id)->where("non_combination_model_id",NULL)->where("color_id",$color->id)->first();
                                    $y_array[] = $this->create_update_product($product_export,$main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$user_id,$related_users,$temp,NULL,$model_ids,$color_ids,$model_prices,$color,$description,$key,$tax_rule_group,$allImages,$prestashop_brand_id);
                                }
                            }
                            else
                            {
                                $title = $temp->title;
            
                                $product_export = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("retailer_id",$related_users)->where("product_id",$temp->id)->where("non_combination_model_id",NULL)->where("color_id",NULL)->first();
                                $y_array[] = $this->create_update_product($product_export,$main_category_id,$title,$price,$unit_price,$unit,$categories_xml,$features_xml,$user_id,$related_users,$temp,NULL,$model_ids,$color_ids,$model_prices,NULL,$description,$key,$tax_rule_group,NULL,$prestashop_brand_id);
                            }
    
                            $end = microtime(true); // End time
                            $executionTime = $end - $start; // Calculate execution time
                    
                            \Log::info('Execution time for create update product: ' . $executionTime . ' seconds.');
                        }
                    }
                }
            }

            $unmatched_avs = prestashop_attribute_values_exports::whereIn("product_id",$p_array)->whereNotIn("id",$x_array)->whereIn("retailer_id",$related_users)->get();
            $mh = curl_multi_init();
            $handles = [];

            foreach($unmatched_avs as $un)
            {
                $apiUrl = $this->base_url."/api/product_option_values/".$un->prestashop_attribute_value_id;
                $ch = curl_init($apiUrl);
                
                // Set cURL options
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_multi_add_handle($mh, $ch);
                $handles[$un->prestashop_attribute_value_id] = $ch;
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);
        
            // Fetch responses and remove handles
            foreach ($handles as $id => $ch) {
                $response = curl_multi_getcontent($ch);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
        
                $response = json_decode($response, true);
                // Handle the response if needed
            }

            prestashop_attribute_values_exports::whereIn("product_id",$p_array)->whereNotIn("id",$x_array)->whereIn("retailer_id",$related_users)->delete();

            // $unmatched_features = prestashop_features_exports::whereIn("product_id",$p_array)->whereNotIn("id",$f_array)->where("retailer_id",$user_id)->get();
    
            // foreach($unmatched_features as $un)
            // {
            //     $apiUrl = $this->base_url."/api/product_features/".$un->prestashop_feature_id;
            //     $ch = curl_init($apiUrl);
                
            //     // Set cURL options
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            //     curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            //     $response = curl_exec($ch);
            //     curl_close($ch);
            //     $response = json_decode($response, true);
            // }

            $featues_delete = prestashop_features_exports::whereIn("product_id",$p_array)->whereNotIn("id",$f_array)->whereIn("retailer_id",$related_users)->get();

            foreach($featues_delete as $fd)
            {
                prestashop_features_values_exports::where("heading_id",$fd->id)->whereIn("retailer_id",$related_users)->delete();
                $fd->delete();
            }

            $unmatched_products = prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("product_id",$p_array)->whereNotIn("id",$y_array)->where("supplier_id",$key)->whereIn("retailer_id",$related_users)->get();
            $mh = curl_multi_init();
            $handles = [];
            $start = microtime(true); // Start time

            foreach($unmatched_products as $un)
            {
                $apiUrl = $this->base_url."/api/products/".$un->prestashop_product_id;
                $ch = curl_init($apiUrl);
                
                // Set cURL options
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
                curl_multi_add_handle($mh, $ch);
                $handles[$un->prestashop_attribute_value_id] = $ch;
            }

            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);
        
            // Fetch responses and remove handles
            foreach ($handles as $id => $ch) {
                $response = curl_multi_getcontent($ch);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
        
                $response = json_decode($response, true);
                // Handle the response if needed
            }

            prestashop_products_exports::where("shop_url",$this->base_url)->whereIn("product_id",$p_array)->whereNotIn("id",$y_array)->where("supplier_id",$key)->whereIn("retailer_id",$related_users)->delete();

            $end = microtime(true); // End time
            $executionTime = $end - $start; // Calculate execution time
    
            \Log::info('Execution time for unmatched products delete: ' . $executionTime . ' seconds.');
        }

        \Mail::send(array(), array(), function ($message) {
            $message->to("tayyabkhurram62@gmail.com")
                ->from('noreply@pieppiep.com')
                ->subject(__("text.Export products to prestashop job status!"))
                ->html(__("text.Products exported successfully"), 'text/html');
        });
    }

    public function failed()
    {
        $user = $this->user;

        $msg = 'Job failed for exporting products to Prestashop <br> Retailer: ' . $user->name .' ('.$user->company_name.')';

        // \Mail::send(array(), array(), function ($message) use ($msg) {
        //     $message->to('tayyabkhurram62@gmail.com')
        //         ->from('info@pieppiep.com')
        //         ->subject('Job Failed')
        //         ->html($msg, 'text/html');
        // });
    }
}