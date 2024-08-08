<?php

namespace App\Exports;

use App\new_invoices;
use App\new_invoices_data;
use App\all_invoices;
use App\organizations;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class InvoicesExport implements FromCollection,WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $request;

    function __construct($request) {
        $this->request = $request;
    }

    public function headings(): array
    {
        return ["Row ID", "Rate", "Qty", "Amount", "Discount", "Description", "Invoice Date", "Invoice Number", "VAT %", "Description", "Name", "Family Name", "Business Name", "Address", "Postcode", "City", "Phone", "Email Address", "External Relation Number", "Customer Number"];
    }

    public function collection()
    {
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

        $data = all_invoices::leftjoin("new_invoices_data","new_invoices_data.invoice_id","=","new_invoices.id")->leftjoin("customers_details","customers_details.id","=","new_invoices.customer_details")->whereIn('new_invoices.creator_id',$related_users);

        if($this->request["export_by"] == 2)
        {
            $data = $data->orderBy("new_invoices.created_at","desc");
        }
        else
        {
            $from_date = date('Y-m-d', strtotime($this->request["export_from_date"]));
            $to_date = date('Y-m-d', strtotime($this->request["export_to_date"]));
            $data = $data->whereDate('new_invoices.created_at', '>=', $from_date)->whereDate('new_invoices.created_at', '<=', $to_date)->orderBy("new_invoices.created_at","desc");
        }

        $data = $data->select("new_invoices_data.*","new_invoices.negative_invoice","new_invoices.invoice_date","new_invoices.invoice_number","new_invoices.vat_percentage","new_invoices.description as invoice_description","customers_details.name","customers_details.family_name","customers_details.business_name","customers_details.address","customers_details.postcode","customers_details.city","customers_details.phone","customers_details.email_address","customers_details.external_relation_number","customers_details.customer_number")->get();
        $last_invoice_id = "";
        $i = 1;
        $x = 1;

        foreach ($data as $key)
        {
            if($key->invoice_id == $last_invoice_id)
            {
                $x = $x + 1;
                $key->row_id = ($i-1).".".$x;
            }
            else
            {
                $key->row_id = $i.".1";
                $i = $i + 1;
                $x = 1;
            }

            $last_invoice_id = $key->invoice_id;

            $key->rate = $key->negative_invoice ? "-".number_format((float)$key->rate, 2, ',', '.') : number_format((float)$key->rate, 2, ',', '.');
            $key->qty = number_format((float)$key->qty, 2, ',', '.');
            $key->amount = $key->negative_invoice ? "-".number_format((float)$key->amount, 2, ',', '.') : number_format((float)$key->amount, 2, ',', '.');
            $key->total_discount = number_format((float)$key->total_discount, 2, ',', '.');
        }

        $data = $data->map(function ($data) {
            return $data->only(['row_id', 'rate', 'qty', 'amount', 'total_discount', 'description', 'invoice_date', 'invoice_number', 'vat_percentage', 'invoice_description', "name", "family_name", "business_name", "address", "postcode", "city", "phone", "email_address", "external_relation_number", "customer_number"]);
        });

        return $data;
    }
}
