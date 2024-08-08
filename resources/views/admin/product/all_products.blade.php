@extends('layouts.admin')

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
                                        <h2 style="width: 100%;">All Products</h2>
                                        <button style="margin-right: 10px;" type="button" href="#myModal" role="button" data-toggle="modal" class="btn add-newProduct-btn"><i style="font-size: 12px;" class="fa fa-plus"></i> Create Product</button>
                                        <a style="background-color: #5bc0de !important;border-color: #5bc0de !important;" href="{{route('all-products-export')}}" class="btn add-newProduct-btn"><i style="font-size: 12px;" class="fa fa-plus"></i> Export Products</a>
                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')

                                                <div class="row">
                                                    <div class="col-sm-12">

                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                            <input value="{{Auth::guard('admin')->user()->filter_text}}" type="hidden" name="filter_text" id="filter_text">

                                                            <div style="width: 20%;margin-bottom: 20px;">

                                                                <select class="form-control" style="border: 1px solid #c5c3c3;border-radius: 5px;padding: 7px;width: auto;" id="suppliers">
                                                    
                                                                    <option value="">All Suppliers</option>

                                                                    @foreach($suppliers as $key)

                                                                        <option {{Auth::guard('admin')->user()->filter_supplier == $key->company_name ? "selected" : null}} value="{{$key->company_name}}">{{$key->company_name}}</option>

                                                                    @endforeach

                                                                </select>

                                                            </div>

                                                        </div>

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
                                                                    Photo
                                                                </th>
                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Title
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Supplier
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Category
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Sub Category
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Brand
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Models
                                                                </th>

                                                                <th class="sorting_asc" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 144px;" aria-sort="ascending"
                                                                    aria-label="Blood Group Name: activate to sort column descending">
                                                                    Prices
                                                                </th>

                                                                <th class="sorting" tabindex="0"
                                                                    aria-controls="product-table_wrapper" rowspan="1"
                                                                    colspan="1" style="width: 314px;"
                                                                    aria-label="Actions: activate to sort column ascending">
                                                                    Actions
                                                                </th>
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

                                                                    <td>{{$cat->company_name}}</td>
                                                                    <td>{{$cat->category}}</td>
                                                                    <td>{{$cat->sub_category}}</td>
                                                                    <td>{{$cat->brand}}</td>

                                                                    <td>
                                                                        @foreach($cat->models as $model)
                                                                            <li>{{$model->model}}</li>
                                                                        @endforeach
                                                                    </td>

                                                                    <td>
                                                                        @foreach($cat->models as $model)
                                                                            <li>{{number_format((float)$model->estimated_price, 2, ',', '.')}}</li>
                                                                        @endforeach
                                                                    </td>

                                                                    <td>
                                                                        <a href="{{route('all-product-edit',$cat->id)}}" class="btn btn-primary product-btn"><i class="fa fa-edit"></i> Edit</a>
                                                                        <a href="{{route('all-product-delete',$cat->id)}}" class="btn btn-danger product-btn"><i class="fa fa-trash"></i> Remove</a>
                                                                    </td>

                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>

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

    <div id="myModal" class="modal fade" role="dialog">
		<div style="width: 60%;" class="modal-dialog">

            <form id="create-product-form" method="POST" action="{{route('all-product-create')}}">
                <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                <!-- Modal content-->
			    <div class="modal-content">
				    <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal">&times;</button>
					    <h4 class="modal-title">Select form type & Supplier</h4>
				    </div>
				    <div style="padding: 30px 20px;display: flex;justify-content: center;" class="modal-body">

                        <div class="wrapper-options1">

                            <input checked type="radio" value="{{$is_floor->cat_name}}" class="select-form" name="cat" id="option1-1">

                            <label for="option1-1" class="option1 option1-1">
                                <div class="dot"></div>
                                <span>Floors</span>
                            </label>

                            <input type="radio" value="{{$is_blind->cat_name}}" class="select-form" name="cat" id="option1-2">

                            <label for="option1-2" class="option1 option1-2">
                                <div class="dot"></div>
                                <span>Blinds</span>
                            </label>

                            <select id="supplier_id" name="supplier" class="form-control">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $key)
                                    <option value="{{$key->id}}">{{$key->company_name}}</option>
                                @endforeach
                            </select>
                        
                        </div>

				    </div>
				    <div class="modal-footer">
					    <button type="button" class="btn btn-success product-form-submit">Submit</button>
				    </div>
			    </div>

            </form>

		</div>
	</div>

    <style>

        @import url('https://fonts.googleapis.com/css?family=Lato:400,500,600,700&display=swap');
                            
        .wrapper-options1{
            display: inline-flex;
            width: 100%;
            align-items: center;
            justify-content: space-evenly;
            border-radius: 5px;
            padding: 0;
        }
        .wrapper-options1 .option1{
            background: #fff;
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin: 0 10px;
            border-radius: 5px;
            cursor: pointer;
            padding: 0 10px;
            border: 2px solid lightgrey;
            transition: all 0.3s ease;
        }
        .wrapper-options1 .option1 .dot{
            height: 20px;
            width: 20px;
            background: #d9d9d9;
            border-radius: 50%;
            position: relative;
        }
        .wrapper-options1 .option1 .dot::before{
            position: absolute;
            content: "";
            top: 4px;
            left: 4px;
            width: 12px;
            height: 12px;
            background: #0069d9;
            border-radius: 50%;
            opacity: 0;
            transform: scale(1.5);
            transition: all 0.3s ease;
        }
        .wrapper-options1 input[type="radio"]{
            display: none;
        }
        .wrapper-options1 input[type="radio"]:checked + label{
            border-color: #0069d9;
            background: #0069d9;
        }
        .wrapper-options1 input[type="radio"]:checked + label .dot{
            background: #fff;
        }
        .wrapper-options1 input[type="radio"]:checked + label .dot::before{
            opacity: 1;
            transform: scale(1);
        }
        .wrapper-options1 .option1 span{
            font-size: 20px;
            color: #808080;
            margin-left: 10px;
            margin-top: -1px;
        }
        .wrapper-options1 input[type="radio"]:checked + label span{
            color: #fff;
        }

        .select2-selection, .select2-selection__arrow
        {
            height: 40px !important;
        }

        .select2-selection__rendered
        {
            line-height: 40px !important;
        }

        #example_wrapper
        {
            display: inline-block;
        }

    </style>

@endsection

@section('scripts')

    <script type="text/javascript">

        $(document).ready( function () {

            var table = $('#example').DataTable({
                stateSave: true,
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

            function filter()
            {
                // Custom filtering function which will search data in column five between two values
                $.fn.dataTable.ext.search.push(

                    function( settings, data, dataIndex ) {

                        var filter_supplier = $("#suppliers").val();
                        var supplier = data[2];

                        if(filter_supplier == "" || filter_supplier == supplier)
                        {
                            return true;
                        }
                        else{
                            return false;
                        }
                        
                    }
            
                );
                
                var filter_text = $("#filter_text").val();
                table.search(filter_text).draw();
                var filter_supplier = $("#suppliers").val();
                
                $.ajax({

                    type: "GET",
                    data: "filter_text=" + filter_text + "&filter_supplier=" + filter_supplier,
                    url: "<?php echo route('admin-update-products-filter'); ?>",
                    success: function (data) {},
                    error: function (data) {}
                
                });

            }
    
            $('.dataTables_filter input').on('input', function () {
                var value = $(this).val();
                $("#filter_text").val(value);
                filter();
            });

            $('#suppliers').on('change', function () {
                filter();
            });

            $(".product-form-submit").click(function () {

                var supplier_id = $("#supplier_id").val();

                if(!supplier_id)
                {
                    $("#create-product-form").find(".select2-selection").css("border-color","red");
                }
                else
                {
                    $("#create-product-form").find(".select2-selection").css("border-color","");
                    $("#create-product-form").submit();
                }

            });

            $("#supplier_id").select2({
                width: '100%',
                placeholder: "Select Supplier",
                allowClear: true,
            });

            $("#suppliers").select2({
                width: '100%',
                placeholder: "All Suppliers",
                allowClear: true,
            });

        });

    </script>

@endsection
