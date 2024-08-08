<?php

namespace App\Exports;

use App\Products;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Generalsetting;

class AllProductsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function view(): View
    {
        $gs = Generalsetting::where('backend',1)->first();
        
        $products = Products::leftJoin('organizations', 'organizations.id', '=', 'products.organization_id')
        ->leftjoin('categories as t1','t1.id','=','products.category_id')
        ->leftjoin('categories as t2','t2.id','=','products.sub_category_id')
        ->leftjoin('brands','brands.id','=','products.brand_id')
        ->leftjoin('models','models.id','=','products.model_id')
        ->leftjoin('colors','colors.product_id','=','products.id')
        ->leftjoin('color_images','color_images.color_id','=','colors.id')
        ->leftjoin('product_models','product_models.product_id','=','products.id')
        ->select('products.id','products.article_code','products.title','products.slug','t1.cat_name','t2.cat_name as sub_category','brands.cat_name as brand_name','product_models.model','product_models.value as model_value','product_models.max_size','product_models.max_width','product_models.max_height','product_models.estimated_price_per_box','product_models.estimated_price_quantity','product_models.estimated_price','models.cat_name as model_name','organizations.company_name','colors.title as color','color_images.image as color_image','products.margin','products.size','products.additional_info','products.floor_type','products.floor_type2','products.description')->orderBy('products.article_code','Asc')->get();

        return view('admin.exports.products', [
            'products' => $products,
            'gs' => $gs
        ]);
    }
}
