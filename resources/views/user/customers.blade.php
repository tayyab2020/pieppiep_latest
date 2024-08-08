@extends('layouts.handyman')

@section('content')

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div style="padding: 0;" class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div style="justify-content: space-between;" class="add-product-header products">
                                        <h2>{{__('text.Customers')}}</h2>
                                        
                                        <div class="dropdown">
                                            <button style="outline: none;" class="btn btn-success" type="button" href="#myModal1" role="button" data-toggle="modal">{{__("text.Reeleezee Credentials")}}</button>
                                            <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__("text.Action")}}
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu custom-dropdown">
                                                @if(auth()->user()->can('handyman-user-create'))
                                                    <li>
                                                        <a style="background-color: #5cb85c;" href="{{route('handyman-user-create')}}" class="btn btn-success"><i class="fa fa-plus"></i> {{__('text.Create Customer')}}</a>
                                                    </li>
                                                    <li>
                                                        <a style="background-color: #F68D2E;border-color: #F68D2E;" href="{{route('import-reeleezee-customers')}}" class="btn btn-primary"><i class="fa fa-download"></i> {{__('text.Import Reeleezee Customers')}}</a>
                                                    </li>
                                                    <li>
                                                        <a style="background-color: #F68D2E;border-color: #F68D2E;" href="{{route('export-customers-to-reeleezee')}}" class="btn btn-primary"><i class="fa fa-download"></i> {{__('text.Export customers to Reeleezee')}}</a>
                                                    </li>
                                                    <li>
                                                        <a style="background-color: #337ab7;" href="{{route('import-customers')}}" class="btn btn-primary"><i class="fa fa-download"></i> {{__('text.Import Customers')}}</a>
                                                    </li>
                                                    <li>
                                                        <button type="button" href="#myModal" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export Customers')}}</button>
                                                    </li>
                                                @endif
                                                
                                                @if(auth()->user()->can('delete-customer'))
                                                    <li>
                                                        <button type="button" class="btn btn-danger delete-customers"><i class="fa fa-trash"></i> {{__('text.Delete')}}</button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')

                                        <form id="customers-form" method="POST" action="{{route('customers-manage-post')}}">
                                            <input type="hidden" name="_token" value="{{@csrf_token()}}">

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr role="row">
                                                            <th style="width: 50px;">
                                                                <label class="custom-container mb-3">
                                                                    <input type="checkbox" class="select-all">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Relation Number')}}</th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Name')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Email')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Business Name')}}</th>
    {{--                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 95px;" aria-label="City: activate to sort column ascending">{{__('text.Address')}}</th>--}}
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 95px;" aria-label="City: activate to sort column ascending">{{__('text.Contact Number')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 240px;" aria-label="Actions: activate to sort column ascending">{{__('text.Actions')}}</th>
                                                        </tr>
                                                        </thead>
    
                                                        <tbody>
                                                        <?php $x = 1; ?>
    
                                                        @foreach($customers as $c => $user)

                                                            <tr role="row" class="odd">

                                                                <td>
                                                                    <div style="display: flex;align-items: center;justify-content: center;" class="custom-control custom-checkbox mb-3">
                                                                        <input type="checkbox" style="margin: 0;" class="custom-control-input">
                                                                        <input type="hidden" name="customers[]" class="customer_id" value="{{$user->id}}">
                                                                        <input type="hidden" class="delete_customers" name="delete_customers[]">
                                                                    </div>
                                                                </td>

                                                                <td>{{$user->external_relation_number}}</td>
    
                                                                <td>{{$user->name}} {{$user->family_name}}</td>
    
                                                                <td>{{!$user->fake_email ? $user->email : null}}</td>

                                                                <!-- <td>{{$user->email_address}}</td> -->
    
                                                                <td>{{$user->business_name}}</td>
    
    {{--                                                            <td>{{$user->address}}</td>--}}
    
                                                                <td>{{$user->phone}}</td>
    
                                                                <td>
                                                                    <div class="dropdown">
    
                                                                        <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                            <span class="caret"></span>
                                                                        </button>
    
                                                                        <ul class="dropdown-menu">
    
                                                                            @if(auth()->user()->can('edit-customer'))
    
                                                                                <li><a href="{{route('edit-customer',$user->id)}}">{{__('text.Edit')}}</a></li>
    
                                                                            @endif
    
                                                                            @if(auth()->user()->can('delete-customer'))
    
                                                                                <li><a href="{{route('delete-customer',$user->id)}}">{{__('text.Remove')}}</a></li>
    
                                                                            @endif
    
                                                                        </ul>
    
                                                                    </div>
                                                                </td>
                                                            </tr>
    
                                                            <?php $x++; ?>
    
                                                        @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
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

            <form id="customers-export-form" method="POST" action="{{route('export-customers')}}">
                <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                <!-- Modal content-->
			    <div class="modal-content">
				    <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal">&times;</button>
					    <h4 class="modal-title">{{__('text.Export by date')}}</h4>
				    </div>
				    <div style="padding: 30px 20px;display: flex;justify-content: center;" class="modal-body">

                        <div class="wrapper-options1">

                            <input checked type="radio" value="1" class="select-form" name="export_by" id="option1-1">

                            <label for="option1-1" class="option1 option1-1">
                                <div class="dot"></div>
                                <span>{{__('text.Created Date')}}</span>
                            </label>

                            <input type="radio" value="2" class="select-form" name="export_by" id="option1-2">

                            <label for="option1-2" class="option1 option1-2">
                                <div class="dot"></div>
                                <span>{{__('text.Updated Date')}}</span>
                            </label>

                            <input type="radio" value="3" class="select-form" name="export_by" id="option1-3">

                            <label for="option1-3" class="option1 option1-3">
                                <div class="dot"></div>
                                <span>{{__('text.Last Export Date')}}</span>
                            </label>
                        
                        </div>

				    </div>
				    <div class="modal-footer">
					    <button type="submit" class="btn btn-success">{{__('text.Submit')}}</button>
				    </div>
			    </div>

            </form>

		</div>
	</div>

    <div id="myModal1" class="modal fade" role="dialog">
		<div style="width: 60%;" class="modal-dialog">

            <form id="reeleezee-credentials-form" method="POST" action="{{route('reeleezee-credentials')}}">
                <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                <!-- Modal content-->
			    <div class="modal-content">
				    <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal">&times;</button>
					    <h4 class="modal-title">{{__('text.Reeleezee Credentials')}}</h4>
				    </div>
				    <div style="display: flex;justify-content: center;flex-direction: column;" class="modal-body">

                        <div style="margin: 10px 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__("text.Username")}}</label>
                            <input type="text" value="{{auth()->user()->reeleezee_username}}" class="form-control" name="username" placeholder="{{__('text.Reeleezee Username')}}">
                        </div>

                        <div style="margin: 10px 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__("text.Password")}}</label>
                            <input type="password" value="{{auth()->user()->reeleezee_password}}" class="form-control" name="password" placeholder="{{__('text.Reeleezee Password')}}">
                        </div>

				    </div>
				    <div class="modal-footer">
					    <button type="submit" class="btn btn-success">{{__('text.Submit')}}</button>
				    </div>
			    </div>

            </form>

		</div>
	</div>

    <style type="text/css">

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

        .product-btn
        {
            margin: 5px;
        }

        .checked {
            color: orange !important;
        }

        .table.products > tbody > tr > td
        {
            text-align: left;
        }

        .table>caption+thead>tr:first-child>td, .table>caption+thead>tr:first-child>th, .table>colgroup+thead>tr:first-child>td, .table>colgroup+thead>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th
        {
            text-align: left;
        }

        .table>thead>tr>th
        {
            vertical-align: middle;
        }

        .custom-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;  
        }
        
        /* Hide the browser's default checkbox */
        .custom-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 5px;
        }

        /* On mouse-over, add a grey background color */
        .custom-container:hover input ~ .checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .custom-container input:checked ~ .checkmark {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            display: none;
        }

        /* Show the checkmark when checked */
        .custom-container input:checked ~ .checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .custom-container .checkmark:after {
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .custom-dropdown
        {
            left: auto;
            right: 0;
            min-width: 250px;
        }

        .custom-dropdown li
        {
            text-align: center;
        }

        .custom-dropdown a, .custom-dropdown button
        {
            color: white !important;
            margin: 7px auto;
            display: inline-block !important;
            white-space: break-spaces !important;
            width: 200px;
        }

    </style>

@endsection

@section('scripts')

    <script type="text/javascript">
    
        $(".delete-customers").click(function(){
            
            $('#customers-form').submit();
        
        });
        
        $(".select-all").click(function(){
            
            var check = $('.custom-control-input:checked').length > 0 ? true : false;
            $(".select-all").prop('checked', !check);
            $('.custom-control-input').prop('checked', !check);
            $('.delete_customers').val(check ? 0 : 1);

        });
        
        $(".custom-control-input").change(function(){
            
            var check = $(this).is(":checked");
            $(this).parent().find('.delete_customers').val(check ? 1 : 0);
        
        });

        $('#example').DataTable({
            order: [[2, 'desc']],
            stateSave: true,
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
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
