<?php

namespace App\Exports;

use App\Products;
use App\organizations;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Auth;

class ProductsExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function headings(): array
    {
        return ["Article ID", "DB ID", "Title", "Slug", "Category", "Brand", "Type", "Size", "Additional Info", "Floor type", "Floor type 2", "Description"];
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

        $products = Products::where('organization_id',$organization_id)->where('article_code','!=',NULL)->latest('article_code')->first();

        if($products)
        {
            $article_code = $products->article_code;
        }
        else
        {
            $article_code = 999;
        }

        $to_increment = Products::where('organization_id',$organization_id)->where('article_code','=',NULL)->get();

        foreach ($to_increment as $key)
        {
            $article_code = $article_code + 1;

            $key->article_code = $article_code;
            $key->save();
        }

        return Products::leftjoin('categories','categories.id','=','products.category_id')->leftjoin('brands','brands.id','=','products.brand_id')->leftjoin('models','models.id','=','products.model_id')->where('products.organization_id',$organization_id)->select('products.article_code','products.id','products.title','products.slug','categories.cat_name','brands.cat_name as brand_name','models.cat_name as model_name','products.size','products.additional_info','products.floor_type','products.floor_type2','products.description')->orderBy('products.article_code','Asc')->get();
    }
}
