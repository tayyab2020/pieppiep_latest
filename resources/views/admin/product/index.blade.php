@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div style="justify-content: flex-end;" class="add-product-header products">
                                        <h2 style="width: 100%;">{{auth()->user()->role_id == 4 ? __('text.My Products') : __('text.Supplier Products')}}</h2>

                                        @if(auth()->user()->role_id == 2)

                                            <a href="{{route('reset-supplier-margins')}}" style="margin: auto;" class="btn btn-success"><i style="margin-right: 5px;" class="fa fa-refresh"></i> {{__('text.Reset Margins')}}</a>

                                        @endif

                                        @if(auth()->user()->role_id == 4)

                                            @if(auth()->user()->can('product-create'))

                                                @if(isset($supplier_global_categories))

                                                    <a style="margin-right: 10px;" href="{{$supplier_global_categories[0] ? route('admin-product-create',['cat' => $supplier_global_categories[0]->cat_name]) : route('admin-product-create',['cat' => $supplier_global_categories[1]->cat_name])}}" class="btn add-newProduct-btn"><i style="font-size: 12px;" class="fa fa-plus"></i> {{__('text.Add New Product')}}</a>

                                                @endif

                                            @endif

                                            @if(auth()->user()->can('product-import'))

                                                <a style="margin-right: 10px;background-color: #5cb85c !important;border-color: #5cb85c !important;" href="{{route('admin-product-import')}}" class="btn add-newProduct-btn">
                                                    <i style="font-size: 12px;" class="fa fa-plus"></i> {{__('text.Import Products')}}</a>

                                            @endif

                                            @if(auth()->user()->can('product-export'))

                                                <a style="background-color: #5bc0de !important;border-color: #5bc0de !important;" href="{{route('admin-product-export')}}" class="btn add-newProduct-btn">
                                                    <i style="font-size: 12px;" class="fa fa-plus"></i> {{__('text.Export Products')}}</a>

                                            @endif

                                        @endif

                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')

                                        @if(auth()->user()->role_id == 2)

                                            <form method="post" action="{{route('store-retailer-margins')}}">

                                                {{csrf_field()}}

                                        @endif

                                                <div class="row">
                                                    <div class="col-sm-12">

                                                        @if(auth()->user()->role_id == 4)

                                                            <div style="display: flex;margin: 10px 0 20px 0;" class="row filters_row">
                                                        
                                                                <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                                    <div style="margin: 0;" class="form-group">
                                                                        <select style="min-width: 125px;" class="form-control filter_category">
                                                                            <option value="">{{__("text.All Categories")}}</option>
                                                                            @foreach($categories as $key)
                                                                                <option value="{{$key->id}}">{{$key->cat_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                </div>
    
                                                                <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                                    <div style="margin: 0;" class="form-group">
                                                                        <select style="min-width: 125px;" class="form-control filter_sub_category">
                                                                            <option value="">{{__("text.All Sub Categories")}}</option>
                                                                            @foreach($categories as $key)
                                                                                @foreach($key->sub_categories as $temp)
                                                                                    <option value="{{$temp->id}}">{{$temp->cat_name}}</option>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                </div>

                                                                <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                                    <div style="margin: 0;" class="form-group">
                                                                        <select style="min-width: 125px;" class="form-control filter_brand">
                                                                            <option value="">{{__("text.All Brands")}}</option>
                                                                            @foreach($brands as $key)
                                                                                <option value="{{$key->id}}">{{$key->cat_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>

                                                                </div>

                                                            </div>

                                                        @endif

                                                        <table id="example"
                                                               class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline"
                                                               role="grid" aria-describedby="product-table_wrapper_info"
                                                               style="width: 100%;" width="100%" cellspacing="0">
                                                            <thead>

                                                            <tr role="row">
                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 344px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    {{__('text.Photo')}}
                                                                </th>
                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    {{__('text.Title')}}
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    {{__('text.Category')}}
                                                                </th>

                                                                @if(auth()->user()->role_id == 4)

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;display: none;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        Category ID
                                                                    </th>

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        {{__('text.Sub Category')}}
                                                                    </th>

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;display: none;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        Sub Category ID
                                                                    </th>

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        {{__('text.Prices')}}
                                                                    </th>

                                                                @else

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        {{__('text.Supplier')}}
                                                                    </th>

                                                                @endif

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    {{__('text.Brand')}}
                                                                </th>

                                                                {{--<th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Model
                                                                </th>--}}

                                                                @if(auth()->user()->role_id == 4)

                                                                    <th class="sorting_asc" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 144px;display: none;" aria-sort="ascending"
                                                                        aria-label="Blood Group Name: activate to sort column descending">
                                                                        Brand ID
                                                                    </th>

                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 314px;"
                                                                        aria-label="Actions: activate to sort column ascending">
                                                                        {{__('text.Actions')}}
                                                                    </th>

                                                                @else

                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 314px;"
                                                                        aria-label="Actions: activate to sort column ascending">
                                                                        {{__('text.Retailer Margin (%)')}}
                                                                    </th>

                                                                    <th class="sorting" tabindex="0"
                                                                        aria-controls="product-table_wrapper" rowspan="1"
                                                                        colspan="1" style="width: 314px;"
                                                                        aria-label="Actions: activate to sort column ascending">
                                                                        {{__('text.Labor Cost')}}
                                                                    </th>

                                                                @endif
                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            @foreach($cats as $i => $cat)
                                                                <tr role="row" class="odd">
                                                                    <td tabindex="0" class="sorting_1"><img
                                                                            src="{{ $cat->photo ? asset('assets/images/'.$cat->photo):'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'}}"
                                                                            alt="Category's Photo" style="max-height: 100px;">
                                                                    </td>
                                                                    <td>{{$cat->title}}</td>
                                                                    <td>{{$cat->category}}</td>

                                                                    @if(auth()->user()->role_id == 4)

                                                                        <td style="display: none;">{{$cat->category_id}}</td>
                                                                        <td>{{$cat->sub_category}}</td>
                                                                        <td style="display: none;">{{$cat->sub_category_id}}</td>
                                                                        <td>{{$cat->models->pluck('estimated_price')->map(function ($price) { if($price !== null && $price != "") return number_format($price, 2, ',', '.'); })->filter()->implode(' | ')}}</td>

                                                                    @else

                                                                        <td>{{$cat->company_name}}</td>

                                                                    @endif

                                                                    <td>{{$cat->brand}}</td>
                                                                    {{--<td>{{$cat->model}}</td>--}}

                                                                    @if(auth()->user()->role_id == 4)

                                                                        <td style="display: none;">{{$cat->brand_id}}</td>
                                                                        <td>
                                                                            <div style="display: flex;">

                                                                                @if(auth()->user()->can('product-edit'))

                                                                                    <a href="{{route('admin-product-edit',$cat->id)}}" style="cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;">
                                                                                        <i style="width: 100%;" class="fa fa-fw fa-edit"></i>
                                                                                    </a>

                                                                                @endif

                                                                                @if(auth()->user()->can('product-delete'))

                                                                                    <a href="{{route('admin-product-delete',$cat->id)}}" style="cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;">
                                                                                        <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                                    </a>

                                                                                @endif

                                                                                @if(auth()->user()->can('product-copy'))

                                                                                    <a href="{{route('admin-product-copy',$cat->id)}}" style="cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;">
                                                                                        <i style="width: 100%;" class="fa fa-fw fa-copy"></i>
                                                                                    </a>

                                                                                @endif
                                                                            
                                                                            </div>

                                                                        </td>

                                                                    @else

                                                                        <td>
                                                                            <input type="hidden" name="product_ids[]" value="{{$cat->id}}">
                                                                            <input min="100" value="{{$cat->retailer_margin ? $cat->retailer_margin : $cat->margin}}" type="number" step="1" name="margin[]" class="form-control">
                                                                        </td>

                                                                        <td>
                                                                            <input value="{{$cat->labor ? $cat->labor : 0}}" type="number" step="1" name="labor[]" class="form-control">
                                                                        </td>

                                                                    @endif

                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>

                                                        @if(auth()->user()->role_id == 2)

                                                            <div style="margin: 0 0 10px 0;text-align: center;" class="row">

                                                                <button type="submit" style="margin: auto;" class="btn btn-success"><i class="fa fa-check"></i>  {{__('text.Submit')}}</button>

                                                            </div>

                                                        @endif

                                                    </div>
                                                </div>

                                        @if(auth()->user()->role_id == 2)

                                             </form>

                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ending of Dashboard data-table area -->
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    @if(auth()->user()->role_id == 2)

        <script type="text/javascript">

            $(document).ready( function () {

                $('#example').DataTable({
                    pageLength: 50,
                    "oLanguage": {
                        "sLengthMenu": "<?php echo __('text.Show') . ' _MENU_ ' . __('text.records'); ?>",
                        "sSearch": "<?php echo __('text.Search') . ':' ?>",
                        "sInfo": "<?php echo __('text.Showing') . ' _START_ ' . __('text.to') . ' _END_ ' . __('text.of') . ' _TOTAL_ ' . __('text.items'); ?>",
                        "sInfoEmpty": "<?php echo __('text.No data available in table'); ?>",
                        "sZeroRecords": "<?php echo __('text.No data available in table'); ?>",
                        "sInfoFiltered": "<?php echo '- ' . __('text.filtered from') . ' _MAX_ ' . __('text.records'); ?>",
                        "oPaginate": {
                            "sPrevious": "<?php echo __('text.Previous'); ?>",
                            "sNext": "<?php echo __('text.Next'); ?>"
                        },
                        "sEmptyTable": '<?php echo __('text.No data available in table'); ?>'
                    }
                });
                
            });

        </script>

    @else
    
        <script type="text/javascript">

            $(document).ready( function () {
    
                $(document).on('keypress', "input[name='margin[]'], input[name='labor[]']", function(e){
    
                    e = e || window.event;
                    var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
                    var val = String.fromCharCode(charCode);
    
                    if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
                    {
                        e.preventDefault();
                        return false;
                    }
    
                    if(e.which == 44)
                    {
                        e.preventDefault();
                        return false;
                    }
    
                });

                var tableId = 'example';
                var catColumn = 3;
                var subCatColumn = 5;
                var brandColumn = 8;
    
                var table = $('#' + tableId).DataTable({
                    pageLength: 50,
                    columnDefs: [{ 'visible': false, 'targets': [catColumn,subCatColumn,brandColumn] }],
                    "oLanguage": {
                        "sLengthMenu": "<?php echo __('text.Show') . ' _MENU_ ' . __('text.records'); ?>",
                        "sSearch": "<?php echo __('text.Search') . ':' ?>",
                        "sInfo": "<?php echo __('text.Showing') . ' _START_ ' . __('text.to') . ' _END_ ' . __('text.of') . ' _TOTAL_ ' . __('text.items'); ?>",
                        "sInfoEmpty": "<?php echo __('text.No data available in table'); ?>",
                        "sZeroRecords": "<?php echo __('text.No data available in table'); ?>",
                        "sInfoFiltered": "<?php echo '- ' . __('text.filtered from') . ' _MAX_ ' . __('text.records'); ?>",
                        "oPaginate": {
                            "sPrevious": "<?php echo __('text.Previous'); ?>",
                            "sNext": "<?php echo __('text.Next'); ?>"
                        },
                        "sEmptyTable": '<?php echo __('text.No data available in table'); ?>'
                    }
                });

                // Custom filtering function which will search data in columns
                $.fn.dataTable.ext.search.push(
                    
                    function( settings, data, dataIndex ) {

                        var filter_category = $(".filter_category").val();
                        var filter_sub_category = $(".filter_sub_category").val();
                        var filter_brand = $(".filter_brand").val();
                        var category = data[catColumn];
                        var sub_category = data[subCatColumn];
                        var brand = data[brandColumn];

                        if (((filter_category == "" && filter_sub_category == "") || ( (filter_category && filter_sub_category) && (filter_category == category && filter_sub_category == sub_category) ) || ( ((filter_category && filter_sub_category == "") && (filter_category == category)) || ((filter_sub_category && filter_category == "") && (filter_sub_category == sub_category)) )) && ((filter_brand == "") || filter_brand == brand))
                        {
                            return true;
                        }
                        else{
                            return false;
                        }

                    }
            
                );
    
                $('.filter_category, .filter_sub_category, .filter_brand').on('change', function () {
                    table.draw();
                });
            
            });

        </script>

    @endif

@endsection
