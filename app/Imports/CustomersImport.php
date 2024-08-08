<?php

namespace App\Imports;

use App\User;
use App\customers_details;
use App\retailers_requests;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
// use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Faker\Generator as Faker;
use App\Http\Controllers\UserController;
use App\organizations;

class CustomersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public $conflict_rows = array();
    public $rows_imported = 0;

    public function collection(Collection $rows)
    {
        $user = Auth::guard('user')->user();
        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user_controller = new UserController();
        $counter_customer_number = $user_controller->get_next_customer_number($user_id);

        foreach ($rows as $x => $row)
        {
            $flag = 0;
            $user_name = $row["name"];
            $family_name = $row["family_name"] ? $row["family_name"] : '';
            $business_name = isset($row["business_name"]) ? $row["business_name"] : NULL;
            $address = isset($row["address"]) ? $row["address"] : NULL;
            $postcode = isset($row["postcode"]) ? $row["postcode"] : NULL;
            $city = isset($row["city"]) ? $row["city"] : NULL;
            $phone = isset($row["phone"]) ? $row["phone"] : NULL;

            if(!$row["email"])
            {
                $faker = \Faker\Factory::create();
                $user_email = $faker->unique()->email;
                $fake_email = 1;
            }
            else
            {
                $user_email = $row["email"];
                $fake_email = 0;
            }

            $org_password = Str::random(8);
            $password = Hash::make($org_password);

            if($row["name"])
            {
                $check = User::where('email',$row['email'])->where("role_id",3)->first();

                if($check)
                {
                    $details = customers_details::where('user_id',$check->id)->whereIn('retailer_id',$related_users)->first();
    
                    if(!$details)
                    {
                        $details = new customers_details();
                        
                        if(isset($row["klantnummer"]) || isset($row["externe_relatienummer"]))
                        {
                            $check_numbers = $user_controller->check_numbers(isset($row["klantnummer"]) ? $row["klantnummer"] : "",isset($row["externe_relatienummer"]) ? $row["externe_relatienummer"] : "",NULL,$user_id);
                        }
                    }
                    else
                    {
                        if(isset($row["klantnummer"]) || isset($row["externe_relatienummer"]))
                        {
                            $check_numbers = $user_controller->check_numbers(isset($row["klantnummer"]) ? $row["klantnummer"] : "",isset($row["externe_relatienummer"]) ? $row["externe_relatienummer"] : "",$details->id,$user_id);
                        }
                    }

                    if(isset($row["klantnummer"]) || isset($row["externe_relatienummer"]))
                    {
                        $found_customer_number = current(array_filter($check_numbers, function($item) {
                            return isset($item['case']) && 1 == $item['case'];
                        }));

                        if($found_customer_number)
                        {
                            $this->conflict_rows[] = ["row" => $x + 2, "reason" => __('text.This customer number is already taken'), "number" => $row["klantnummer"]];
                            $flag = 1;
                        }
                        
                        $found_external_number = current(array_filter($check_numbers, function($item) {
                            return isset($item['case']) && 2 == $item['case'];
                        }));

                        if($found_external_number)
                        {
                            $this->conflict_rows[] = ["row" => $x + 2, "reason" => __('text.This external relation number is already taken'), "number" => $row["externe_relatienummer"]];
                            $flag = 1;
                        }
                    }

                    $customer_id = $check->id;
                }
                else
                {
                    if(isset($row["klantnummer"]) || isset($row["externe_relatienummer"]))
                    {
                        $check_numbers = $user_controller->check_numbers(isset($row["klantnummer"]) ? $row["klantnummer"] : "",isset($row["externe_relatienummer"]) ? $row["externe_relatienummer"] : "",NULL,$user_id);

                        $found_customer_number = current(array_filter($check_numbers, function($item) {
                            return isset($item['case']) && 1 == $item['case'];
                        }));

                        $found_external_number = current(array_filter($check_numbers, function($item) {
                            return isset($item['case']) && 2 == $item['case'];
                        }));

                        if($found_customer_number)
                        {
                            $details = customers_details::where("id",$found_customer_number["id"])->first();
                            $customer_id = $details->user_id;
                        }
                        elseif($found_external_number)
                        {
                            $details = customers_details::where("id",$found_external_number["id"])->first();
                            $customer_id = $details->user_id;
                        }
                        else
                        {
                            $details = new customers_details();
                            $customer_id = "";
                        }
                    }
                }

                if(!$flag)
                {
                    $em = User::where("id",$customer_id)->where("fake_email",0)->pluck("email")->first();

                    if($em != $user_email)
                    {
                        $other_retailers_link = customers_details::where('user_id',$customer_id)->whereNotIn('retailer_id',$related_users)->first();

                        if($other_retailers_link || $customer_id == "")
                        {
                            $user = new User;
                            $user->category_id = 20;
                            $user->role_id = 3;
                            $user->password = $password;
                            $user->temp_password = $org_password;
                            $user->name = $user_name;
                            $user->family_name = $family_name;
                            // $user->business_name = $business_name;
                            // $user->address = $address;
                            // $user->postcode = $postcode;
                            // $user->city = $city;
                            // $user->phone = $phone;
                            $user->email = $user_email;
                            $user->parent_id = $user_id;
                            $user->allowed = 0;
                            $user->fake_email = $fake_email;
                            $user->save();
        
                            $customer_id = $user->id;
                        }
                        else
                        {
                            User::where('id',$customer_id)->update(["email" => $user_email, "fake_email" => $fake_email]);
                        }
                    }

                    $details->user_id = $customer_id;
                    $details->retailer_id = $user_id;
                    $details->name = $user_name;
                    $details->family_name = $family_name;
                    $details->business_name = $business_name;
                    $details->postcode = $postcode;
                    $details->address = $address;
                    $details->city = $city;
                    $details->phone = $phone;
                    $details->email_address = isset($row["email"]) ? $row["email"] : NULL;
                    $details->external_relation_number = isset($row["externe_relatienummer"]) ? $row["externe_relatienummer"] : NULL;

                    if(isset($details->customer_number))
                    {
                        if(isset($row["klantnummer"]))
                        {
                            if(!$details->customer_number)
                            {
                                if($row["klantnummer"] == "")
                                {
                                    $details->customer_number = $counter_customer_number;
                                    $user_controller->increment_customer_number($user_id);
                                }
                                else
                                {
                                    $details->customer_number = $row["klantnummer"];
                                }
                            }
                            else
                            {
                                if($details->customer_number != $row["klantnummer"])
                                {
                                    $this->conflict_rows[] = ["row" => $x + 2, "reason" => __('text.Customer Number could not be updated. You have to update it manually'), "number" => $row["klantnummer"]];   
                                }
                            }
                        }
                    }
                    else
                    {
                        if(isset($row["klantnummer"]))
                        {
                            if($row["klantnummer"])
                            {
                                $details->customer_number = $row["klantnummer"];
                            }
                            else
                            {
                                $details->customer_number = $counter_customer_number;
                                $user_controller->increment_customer_number($user_id);
                            }
                        }
                        else
                        {
                            $details->customer_number = $counter_customer_number;
                            $user_controller->increment_customer_number($user_id);
                        }
                    }
                    
                    $details->save();
                    $this->rows_imported++;
                }

            }
        }
    }
}
