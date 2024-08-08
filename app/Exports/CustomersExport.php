<?php

namespace App\Exports;

use App\User;
use App\customers_details;
use App\organizations;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class CustomersExport implements FromCollection,WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $export_by;

    function __construct($export_by) {
        $this->export_by = $export_by;
    }

    public function headings(): array
    {
        return ["name", "family_name", "business_name", "address", "postcode", "city", "phone", "email", "externe_relatienummer", "klantnummer"];
    }

    public function collection()
    {
        $export_by = $this->export_by == 1 ? "created_at" : ($this->export_by == 2 ? "updated_at" : "exported_at");

        $user = Auth::guard('user')->user();
        $organization_id = $user->organization->id;
        $organization = organizations::findOrFail($organization_id);
        $related_users = $organization->users()->withTrashed()->select('users.id')->pluck('id');

        $user_id = $user->id;
        // $main_id = $user->main_id;

        // if($main_id)
        // {
        //     $user_id = $main_id;
        // }

        $data = customers_details::whereIn('retailer_id',$related_users)->orderBy($export_by,"Desc")->get();
        customers_details::whereIn('retailer_id',$related_users)->update(["exported_at" => date('Y-m-d H:i:s')]);

        $data = $data->map(function ($data) {
            return $data->only(['name', 'family_name', 'business_name', 'address', 'postcode', 'city', 'phone', 'email_address', 'external_relation_number', 'customer_number']);
        });

        return $data;
    }
}
