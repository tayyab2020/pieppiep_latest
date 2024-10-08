<?php

namespace App\Exports;

use App\Category;
use App\items;
use App\product;
use App\Products;
use App\sub_categories;
use App\organizations;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class ItemsExport implements FromCollection,WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return ["Category", "Sub Category", "Title", "Product ID", "Supplier", "Price ex. VAT", "Selling Price", "Description", "Related Products"];
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

        $data = items::whereIn('user_id',$related_users)->get();

        foreach ($data as $key)
        {
            $key->category = Category::where('id',$key->category_id)->pluck('cat_name');
            $key->category = $key->category[0];
            $sub_category_ids = explode(',',$key->sub_category_ids);
            $key->sub_categories = sub_categories::whereIn('id',$sub_category_ids)->pluck('cat_name')->toArray();
            $key->sub_categories = implode(",",$key->sub_categories);

            $related_products = explode(',',$key->products);
            $key->related_products = product::whereIn('id',$related_products)->pluck('title')->toArray();
            $key->related_products = implode(",",$key->related_products);

            $key->rate = number_format((float)$key->rate, 2, ',', '');
            $key->sell_rate = number_format((float)$key->sell_rate, 2, ',', '');
        }

        $data = $data->map(function ($data) {
            return $data->only(['category', 'sub_categories', 'cat_name', 'product_id', 'supplier', 'rate', 'sell_rate', 'description', 'related_products']);
        });

        return $data;
    }
}
