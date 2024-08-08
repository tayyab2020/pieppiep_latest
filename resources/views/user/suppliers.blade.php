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
                                        <h2 style="width: 100%;">{{__('text.Suppliers')}}</h2>
                                        @if(auth()->user()->role_id == 2)

                                            <button style="background-color: #4d8dd5 !important;border-color: #ffffff00 !important;margin-right: 5px;" type="button" href="#credentialsModal" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Prestashop Credentials')}}</button>
                                            <button style="background-color: #4dd5b2 !important;border-color: #ffffff00 !important;margin-right: 5px;" type="button" href="#exportModal" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export to Prestashop')}}</button>
                                            <a href="{{route('categories-mapping')}}" class="btn btn-success"><i style="margin-right: 5px;" class="fa fa-refresh"></i> {{__('text.Categories Mapping')}}</a>

                                        @endif
                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                    <thead>
                                                    <tr role="row">

                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Supplier ID')}}</th>
                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Supplier\'s Photo')}}</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 171px;" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Supplier\'s Company Name')}}</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Email')}}</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Status')}}</th>
{{--                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 95px;" aria-label="City: activate to sort column ascending">{{__('text.Products')}}</th>--}}
{{--                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 95px;" aria-label="City: activate to sort column ascending">{{__('text.Categories')}}</th>--}}
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 240px;" aria-label="Actions: activate to sort column ascending">{{__('text.Actions')}}</th>

                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    <?php $x = 0; ?>

                                                    @foreach($users as $user)
                                                        <tr role="row" class="odd">

                                                            <td>{{$user->id}}</td>

                                                            <td tabindex="0" class="sorting_1"><img src="{{ $user->photo ? asset('assets/images/'.$user->photo) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'}}" alt="User's Photo" style="height: 180px; width: 200px;"></td>
                                                            <td>{{$user->company_name}}</td>

                                                            <td>{{$user->email}}</td>

                                                            <td>

                                                                @if($user->supplierRequests->isEmpty())

                                                                    <button class="btn btn-info">{{__('text.Not Requested')}}</button>

                                                                @elseif(!$user->supplierRequests[0]->status)

                                                                    <button class="btn btn-warning">{{__('text.Response Pending')}}</button>

                                                                @else

                                                                    @if(!$user->supplierRequests[0]->active)

                                                                        <button class="btn btn-warning">{{__('text.Suspended')}}</button>

                                                                    @else

                                                                        <button class="btn btn-success">{{__('text.Active')}}</button>

                                                                    @endif

                                                                @endif

                                                            </td>

{{--                                                            <td>--}}

{{--                                                                @if(count($products[$x]) > 0)--}}

{{--                                                                    <select style="padding: 10px;">--}}

{{--                                                                        @foreach($products[$x] as $product)--}}

{{--                                                                            <option value="{{$product->title}}">{{$product->title}}</option>--}}

{{--                                                                        @endforeach--}}

{{--                                                                    </select>--}}

{{--                                                                @endif--}}

{{--                                                            </td>--}}

{{--                                                            <td>--}}

{{--                                                                @if(count($categories[$x]) > 0)--}}

{{--                                                                    <select style="padding: 10px;">--}}

{{--                                                                        @foreach($categories[$x] as $category)--}}

{{--                                                                            <option value="{{$category->cat_name}}">{{$category->cat_name}}</option>--}}

{{--                                                                        @endforeach--}}

{{--                                                                    </select>--}}

{{--                                                                @endif--}}

{{--                                                            </td>--}}


                                                            <td>

                                                                <div class="dropdown">

                                                                    <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                        <span class="caret"></span>
                                                                    </button>

                                                                    <ul class="dropdown-menu">

                                                                        @if($user->supplierRequests->isEmpty() || !$user->supplierRequests[0]->status)

                                                                            <li><a href="javascript:void(0)" data-id="{{$user->id}}" class="send-request">{{__('text.Send Request')}}</a></li>

                                                                        @endif

                                                                        @if(auth()->user()->can('supplier-details'))

                                                                            @if($user->supplierRequests->isNotEmpty() && $user->supplierRequests[0]->active)

                                                                                @if(auth()->user()->can('edit-customer'))

                                                                                    <li><a href="{{route('retailer-supplier-products',$user->id)}}">{{__('text.Products')}}</a></li>

                                                                                @endif

                                                                                @if(auth()->user()->can('delete-customer'))

                                                                                    <li><a href="{{route('supplier-details',$user->id)}}">{{__('text.Details')}}</a></li>

                                                                                @endif

                                                                            @endif

                                                                        @endif

                                                                    </ul>

                                                                </div>

                                                            </td>
                                                        </tr>

                                                        <?php $x++; ?>

                                                    @endforeach
                                                    </tbody>
                                                </table></div></div>
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

    <div class="modal fade" id="myModal" role="dialog" style="background-color: #0000008c;">
        <div class="modal-dialog" style="margin-top: 130px;width: 75%;">

            <form action="{{route('send-request-supplier')}}" method="POST" id="request_form">

                {{csrf_field()}}
                <input name="supplier_id" id="supplier_id" type="hidden">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body" style="display: inline-block;width: 100%;">

                        <div style="text-align: center;font-size: 18px;margin: 20px;">

                            <div style="width: 100%;">

                                <h3 style="text-align: center;width: 95%;margin: auto;margin-bottom: 15px;">{{__('text.If you approve this action than you will agree to share your details with this supplier!')}}</h3>

                            </div>

                        </div>

                        <div style="border: 0;text-align: center;" class="modal-footer">
                            <button style="padding: 10px 30px;font-size: 20px;" type="submit" class="btn btn-success">{{__('text.Approve')}}</button>
                        </div>
                    </div>

                </div>

            </form>

        </div>

    </div>

    <style type="text/css">

        .product-btn
        {
            margin: 5px;
        }

        .checked {
            color: orange !important;
        }

        .table.products > tbody > tr > td
        {
            text-align: center;
        }

        .table>caption+thead>tr:first-child>td, .table>caption+thead>tr:first-child>th, .table>colgroup+thead>tr:first-child>td, .table>colgroup+thead>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th
        {
            text-align: center;
        }

    </style>

    @if(auth()->user()->role_id == 2)

        <div id="exportModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
    
                <form method="POST" action="{{route('export-products-prestashop')}}">
                    <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{__('text.Export Products')}}</h4>
                        </div>
                        <div style="padding: 20px;" class="modal-body">
    
                            <h3>{{__("text.Suppliers")}}</h3>
                            <div style="display: flex;flex-wrap: wrap;margin-bottom: 40px;">
                                @foreach($suppliers as $s => $user)
    
                                    <div style="display: flex;align-items: center;justify-content: flex-start;margin: 5px 0;" class="col-md-4 custom-control custom-checkbox mb-3">
                                        <input style="margin: 0;" type="checkbox" name="suppliers[]" value="{{$user->id}}" class="custom-control-input" id="customCheck{{$user->id}}">
                                        <label style="margin: 0 0 0 5px;font-size: 16px;" class="custom-control-label" for="customCheck{{$user->id}}">{{$user->company_name}}</label>
                                    </div>
    
                                @endforeach
                            </div>

                            <h3>{{__("text.Sub Categories")}}</h3>
                            <div style="display: flex;flex-wrap: wrap;">
                                @foreach($sub_categories as $sc => $sub_category)
    
                                    <div style="display: flex;align-items: center;justify-content: flex-start;margin: 5px 0;" class="col-md-4 custom-control custom-checkbox mb-3">
                                        <input style="margin: 0;" type="checkbox" name="sub_categories[]" value="{{$sub_category->id}}" class="custom-control-input" id="subCheck{{$sub_category->id}}">
                                        <label style="margin: 0 0 0 5px;font-size: 16px;" class="custom-control-label" for="subCheck{{$sub_category->id}}">{{$sub_category->cat_name}}</label>
                                    </div>
    
                                @endforeach
                            </div>
    
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{__('text.Submit')}}</button>
                        </div>
                    </div>
    
                </form>
    
            </div>
        </div>

        <div id="credentialsModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
    
                <form method="POST" action="{{route('prestashop-credentials')}}">
                    <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{__('text.Prestashop Credentials')}}</h4>
                        </div>
                        <div style="padding: 30px 20px;" class="modal-body">
    
                            <div class="form-group">
                                <label>Prestashop URL</label>
                                <input placeholder="Prestashop URL" class="form-control" type="text" name="prestashop_url" value="{{auth()->user()->organization->prestashop_url}}">
                            </div>

                            <div class="form-group">
                                <label>Prestashop Access Key</label>
                                <input placeholder="Prestashop Access Key" class="form-control" type="text" name="prestashop_access_key" value="{{auth()->user()->organization->prestashop_access_key}}">
                            </div>
    
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">{{__('text.Submit')}}</button>
                        </div>
                    </div>
    
                </form>
    
            </div>
        </div>

    @endif

@endsection

@section('scripts')

    <script type="text/javascript">

        $('.send-request').click(function(){

            var id = $(this).data('id');

            $('#supplier_id').val(id);

            $('#myModal').modal('toggle');

        });

        $('#example').DataTable({
            order: [[0, 'desc']],
            "aoColumns": [
                { "sWidth": "" }, // 1st column width
                { "sWidth": "200px" }, // 2nd column width
                { "sWidth": "" },
                { "sWidth": "" },
                { "sWidth": "100px" },
                { "sWidth": "" },
            ],
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
    </script>

@endsection
