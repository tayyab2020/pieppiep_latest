<?php

namespace App\Imports;

use App\Brand;
use App\Category;
use App\estimated_prices;
use App\items;
use App\Model1;
use App\product;
use App\Products;
use App\retailers_requests;
use App\sub_categories;
use App\organizations;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Auth;

class ItemsImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     *
     */

    public $data = array();

    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
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

        $suppliers = retailers_requests::where('retailer_organization',$organization_id)->where('status',1)->where('active',1)->pluck('supplier_organization');

        if($row[0] && $row[2] && $row[6])
        {
            $sell_rate = number_format((float)$row[6], 2, '.', '');
            $rate = $sell_rate/((100 + 21)/100);
            $rate = number_format((float)$rate, 2, '.', '');

            if($row[1])
            {
                $sub_categories = explode(',', $row[1]);
            }
            else
            {
                $sub_categories = [];
            }

            if($row[8])
            {
                $related_products = explode(',', $row[8]);
            }
            else
            {
                $related_products = [];
            }

            $category = Category::where('cat_name', $row[0])->first();
            $sub_categories = sub_categories::whereIn('cat_name', $sub_categories)->pluck('id')->toArray();

            if(count($sub_categories) == 0)
            {
                $sub_categories = NULL;
            }
            else
            {
                $sub_categories = implode(",",$sub_categories);
            }

            $related_products = Products::whereIn('title',$related_products)->whereIn('organization_id',$suppliers)->where('deleted_at',NULL)->pluck('id')->toArray();
            
            $retailer_products = Products::whereIn('title', $related_products)->whereIn('organization_id',$suppliers)->where('deleted_at',NULL)->pluck('id')->toArray();

            if(count($related_products) == 0)
            {
                $related_products = NULL;
            }
            else
            {
                $related_products = implode(",",$related_products);
            }

            if($category)
            {
                $check = items::where('category_id',$category->id)->where('cat_name',$row[2])->whereIn('user_id',$related_products)->first();

                if(!$check)
                {
                    $check = new items;
                }

                $check->user_id = $user_id;
                $check->category_id = $category->id;
                $check->sub_category_ids = $sub_categories;
                $check->cat_name = $row[2];
                $check->description = $row[7];
                $check->rate = $rate;
                $check->sell_rate = $sell_rate;
                $check->products = $related_products;
                $check->product_id = $row[3];
                $check->supplier = $row[4];
                $check->excel = 1;
                $check->save();

                $this->data[] = $check->id;

            }
        }
    }
}
