<?php

use App\organizations;

function checkCombinationRNCN($input, $user = NULL) // check if combination of registration number and company name exists in users and user_organizations tables
{
    if($user)
    {
        $organization_id = $user->organization->id;
        $rn_exists = organizations::where("id","!=",$organization_id)->where('registration_number', $input["registration_number"])->get();
    }
    else
    {
        $rn_exists = organizations::where('registration_number', $input["registration_number"])->get();
    }

    if (count($rn_exists)) {

        foreach($rn_exists as $key)
        {
            $cn_exists = $key->company_name == $input["company_name"] ? 1 : 0;

            if($cn_exists)
            {
                return 1;
            }
        }
    }
}