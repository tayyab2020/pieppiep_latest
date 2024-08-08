@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard area -->
                    
                    <!-- Added by Nordin -->
                                        <!-- <div class="product-configuration" style="width: 85%;margin: auto;">

                                            <ul style="border: 0;" class="nav nav-tabs">
                                                <li style="margin-bottom: 0;" class="active"><a data-toggle="tab" href="#menu1">{{__('text.Contactgegevens')}}</a></li>
                                                {{--<li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu2">{{__('text.General Options')}}</a></li>--}}
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu3">{{__('text.Contactpersonen')}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu5">{{__('text.Financieel')}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu7">{{__('text.Projecten')}}</a></li>
                                            </ul>
                                        </div> -->
                    <!-- Added by Nordin -->

                    <div style="padding: 0;" class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header">
                                        <h2>{{isset($customer) ? __('text.Edit Customer') : __('text.Create Customer')}}</h2>
                                        <a href="{{route('customers')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                    </div>
                                    <hr>
                                    <form class="form-horizontal" action="{{route('post-create-customer')}}" method="POST" enctype="multipart/form-data">
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        {{csrf_field()}}

                                        <input type="hidden" name="is_form" value="1">
                                        <input type="hidden" id="org_id" name="org_id" value="{{isset($customer->id) ? $customer->id : null}}">
                                        <input type="hidden" name="reeleezee_id" value="{{isset($customer) ? $customer->reeleezee_guid : null}}">
                                        
                                        <div style="width: 100%;" id="exTab1" class="container">	
                                            <ul class="nav nav-pills">
                                                <li class="active">
                                                    <a href="#1a" data-toggle="tab">{{__("text.Overview")}}</a>
                                                </li>
                                                <li><a href="#2a" data-toggle="tab">{{__("text.Contacts")}}</a></li>
                                                <li><a href="#3a" data-toggle="tab">{{__("text.Projects")}}</a></li>
                                            </ul>
                                            <div class="tab-content clearfix">
                                                <div class="tab-pane active" id="1a">
                                                    
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4">{{__('text.Entity Type')}}</label>
                                                        <div class="col-sm-6">
                                                            <select class="form-control" name="entity_type">
                                                                <option {{isset($customer) ? ($customer->entity_type == 1 ? "selected" : null) : null}} value="1">{{__("text.Natural Person")}}</option>
                                                                <option {{isset($customer) ? ($customer->entity_type == 2 ? "selected" : null) : null}} value="2">{{__("text.Company")}}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="customer_number">{{__('text.Customer Number')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer) ? $customer->customer_number : $counter_customer_number}}" name="customer_number" id="customer_number" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="external_relation_number">{{__('text.External relation number')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->external_relation_number) ? $customer->external_relation_number : null}}" name="external_relation_number" id="external_relation_number" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="">{{__('text.Name')}}*</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->name) ? $customer->name : null}}" name="name" id="name" placeholder="" required="" type="text">
                                                        </div>
                                                    </div>
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="">{{__('text.Family Name')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->family_name) ? $customer->family_name : null}}" name="family_name" id="family_name" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="">{{__('text.Business Name')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->business_name) ? $customer->business_name : null}}" name="business_name" id="business_name" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="address">{{__('text.Address')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->address) ? $customer->address : null}}" name="address" id="address" placeholder="" type="text">
                                                            <input type="hidden" id="check_address" value="0">
                                                            <input type="hidden" name="street_name" id="street_name" value="{{isset($customer->street_name) ? $customer->street_name : null}}">
                                                            <input type="hidden" name="street_number" id="street_number" value="{{isset($customer->street_number) ? $customer->street_number : null}}">
                                                        </div>
                                                    </div>
            
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="address">{{__('text.Postcode')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->postcode) ? $customer->postcode : null}}" name="postcode" id="postcode" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="city">{{__('text.City')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->city) ? $customer->city : null}}" name="city" id="city" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="phone">{{__('text.Contact Number')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->phone) ? $customer->phone : null}}" name="phone" id="phone" placeholder="" type="text">
                                                        </div>
                                                    </div>
            
            
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-4" for="email">{{__('text.Email')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->email) ? (!$customer->fake_email ? $customer->email : null) : null}}" name="email" id="email" placeholder="" type="email">
                                                        </div>
                                                    </div>
            
                                                    <!-- <div class="form-group">
                                                        <label class="control-label col-sm-4" for="email">{{__('text.Email')}}</label>
                                                        <div class="col-sm-6">
                                                            <input class="form-control" value="{{isset($customer->email_address) ? $customer->email_address : null}}" name="email" id="email" placeholder="" type="email">
                                                        </div>
                                                    </div> -->
                                                </div>
                                                <div class="tab-pane" id="2a">
                                                    
                                                    <span class="create-cp" style="float: right;cursor: pointer;font-size: 40px;width: 40px;height: 40px;line-height: 40px;border: 1px solid black;text-align: center;border-radius: 100%;">
                                                        +
                                                    </span>
                                                    
                                                    <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr role="row">
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Name')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 171px;" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Contact Number')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Email')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Actions')}}</th>
                                                        </tr>
                                                        </thead>
    
                                                        <tbody>
                                                        <?php $contact_persons = isset($customer->contact_persons) ? json_decode($customer->contact_persons) : ""; ?>
    
                                                        @if($contact_persons)
                                                        
                                                            @foreach($contact_persons as $c => $cp)

                                                                <tr data-row="{{$c}}" role="row" class="odd">
    
                                                                    <td>
                                                                        <span class="text">{{$cp->name}}</span>
                                                                        <input value="{{$cp->name}}" type="hidden" name="contact_person_names[]" class="contact_person_names">
                                                                    </td>
    
                                                                    <td>
                                                                        <span class="text">{{$cp->phone}}</span>
                                                                        <input value="{{$cp->phone}}" type="hidden" name="contact_person_phone_numbers[]" class="contact_person_phone_numbers">
                                                                    </td>

                                                                    <td>
                                                                        <span class="text">{{$cp->email}}</span>
                                                                        <input value="{{$cp->email}}" type="hidden" name="contact_person_emails[]" class="contact_person_emails">
                                                                    </td>

                                                                    <td>
                                                                        <div style="display: flex;">
                                                                            <span data-type="1" class="edit-cp" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">
                                                                                <i style="width: 100%;" class="fa fa-fw fa-edit"></i>
                                                                            </span>
    
                                                                            <span data-type="2" class="delete-cp" style="cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;">
                                                                                <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                            </span>
                                                                        </div>
                                                                    </td>

                                                                </tr>
    
                                                            @endforeach

                                                        @endif

                                                        </tbody>
                                                    </table>

                                                    <fieldset id="contact-person-details" style="display: none;padding: 20px;border: 1px solid #d7e1ee;border-radius: 5px;margin-top: 20px;">
                                                        <legend style="width: auto;margin: 0;border: 0;">{{__("text.Contact Person Details")}}</legend>
                                                        <input type="hidden" id="edit_id">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Name")}}</label>
                                                                <input style="border-radius: 5px;" class="form-control" type="text" id="contact_person_name" name="contact_person_name">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Contact Number")}}</label>
                                                                <input style="border-radius: 5px;" class="form-control" type="text" id="contact_person_phone_number" name="contact_person_phone_number">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Email")}}</label>
                                                                <input style="border-radius: 5px;" class="form-control" type="text" id="contact_person_email" name="contact_person_email">
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <button class="btn btn-success btn-cp-submit" type="button">
                                                                    {{__("text.Submit")}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <div class="tab-pane" id="3a">
                                                    
                                                    <span class="create-p" style="float: right;cursor: pointer;font-size: 40px;width: 40px;height: 40px;line-height: 40px;border: 1px solid black;text-align: center;border-radius: 100%;">
                                                        +
                                                    </span>
                                                    
                                                    <table id="example1" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr role="row">
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Name')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 171px;" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Active')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Start Date')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.End Date')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Description')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Notes')}}</th>
                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 134px;" aria-label="Blood Group: activate to sort column ascending">{{__('text.Actions')}}</th>
                                                        </tr>
                                                        </thead>
    
                                                        <tbody>
                                                        <?php $projects = isset($customer->projects) ? json_decode($customer->projects) : ""; ?>
    
                                                        @if($projects)
                                                        
                                                            @foreach($projects as $p => $project)

                                                                <tr data-row="{{$p}}" role="row" class="odd">
    
                                                                    <td>
                                                                        <span class="text">{{$project->name}}</span>
                                                                        <input value="{{$project->name}}" type="hidden" name="project_names[]" class="project_names">
                                                                        <input value="0" type="hidden" name="project_totals[]" class="project_totals">
                                                                    </td>
    
                                                                    <td>
                                                                        <span class="text">{{$project->is_active ? __("text.Yes") : __("text.No")}}</span>
                                                                        <input value="{{$project->is_active ? 1 : 0}}" type="hidden" name="project_is_active[]" class="project_is_active">
                                                                    </td>

                                                                    <td>
                                                                        <span class="text">{{$project->start_date}}</span>
                                                                        <input value="{{$project->start_date}}" type="hidden" name="project_start_dates[]" class="project_start_dates">
                                                                    </td>

                                                                    <td>
                                                                        <span class="text">{{$project->end_date}}</span>
                                                                        <input value="{{$project->end_date}}" type="hidden" name="project_end_dates[]" class="project_end_dates">
                                                                    </td>

                                                                    <td>
                                                                        <span class="text">{{$project->description}}</span>
                                                                        <input value="{{$project->description}}" type="hidden" name="project_descriptions[]" class="project_descriptions">
                                                                    </td>

                                                                    <td>
                                                                        <span class="text">{{$project->comment}}</span>
                                                                        <input value="{{$project->comment}}" type="hidden" name="project_comments[]" class="project_comments">
                                                                    </td>

                                                                    <td>
                                                                        <div style="display: flex;">
                                                                            <span data-type="1" class="edit-p" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">
                                                                                <i style="width: 100%;" class="fa fa-fw fa-edit"></i>
                                                                            </span>
    
                                                                            <span data-type="2" class="delete-p" style="cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;">
                                                                                <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                            </span>
                                                                        </div>
                                                                    </td>

                                                                </tr>
    
                                                            @endforeach

                                                        @endif

                                                        </tbody>
                                                    </table>

                                                    <fieldset id="project-details" style="display: none;padding: 20px;border: 1px solid #d7e1ee;border-radius: 5px;margin-top: 20px;">
                                                        <legend style="width: auto;margin: 0;border: 0;">{{__("text.Project Details")}}</legend>
                                                        <input type="hidden" id="edit_project_id">
                                                        <div class="row">
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Name")}}</label>
                                                                <input style="border-radius: 5px;" class="form-control" type="text" id="project_name" name="project_name">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Active")}}</label>
                                                                <select style="border-radius: 5px;" class="form-control" type="text" id="project_active" name="project_active">
                                                                    <option value="1">{{__("text.Yes")}}</option>
                                                                    <option value="0">{{__("text.No")}}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Start Date")}}</label>
                                                                <input readonly style="border-radius: 5px;background: transparent;" class="form-control" type="text" id="project_start_date" name="project_start_date">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.End Date")}}</label>
                                                                <input readonly style="border-radius: 5px;background: transparent;" class="form-control" type="text" id="project_end_date" name="project_end_date">
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Description")}}</label>
                                                                <textarea rows="5" style="border-radius: 5px;" class="form-control" id="project_description" name="project_description"></textarea>
                                                            </div>
                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 25px;">
                                                                <label>{{__("text.Notes")}}</label>
                                                                <textarea rows="5" style="border-radius: 5px;" class="form-control" id="project_notes" name="project_notes"></textarea>
                                                            </div>
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <button class="btn btn-success btn-p-submit" type="button">
                                                                    {{__("text.Submit")}}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" id="submit" type="submit" class="btn add-product_btn">{{isset($customer) ? __('text.Edit Customer') : __('text.Create Customer')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ending of Dashboard area -->
                </div>
            </div>
        </div>
    </div>

    <div id="cover">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
	</div>

    <style>

        .table.products > tbody > tr > td
        {
            padding: 5px 10px;
        }
    
        #exTab1
        {
            margin-bottom: 20px;
        }

        #exTab1 .nav li{
            border: 1px solid #d7e1ee;
            border-bottom: 0;
            margin-right: -3px;
        }
    
        #exTab1 .tab-content {
            padding: 30px 15px;
            border: 1px solid #d7e1ee;
        }

        #exTab2 h3 {
            color: white;
            background-color: #428bca;
            padding: 5px 15px;
        }

        /* remove border radius for the tab */
        #exTab1 .nav-pills > li > a {
            border-radius: 0;
            padding: 10px 35px;
        }

        #exTab1 .nav-pills > li > a[aria-expanded="false"]::before, #exTab1 .nav-pills > li > a[aria-expanded="true"]::before
        {
            display: none;
        }

        /* change border radius for the tab , apply corners on top*/
        #exTab3 .nav-pills > li > a {
            border-radius: 4px 4px 0 0 ;
        }

        #exTab3 .tab-content {
            color: white;
            background-color: #428bca;
            padding: 5px 15px;
        }

        #cover {
			position: fixed;
			z-index: 100000;
			height: 100%;
			width: 100%;
			margin: 0;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			background-color: #ffffff99;
			display: none;
			justify-content: center;
			align-items: center;
		}

        .lds-ripple {
  			display: inline-block;
			position: relative;
			width: 80px;
			height: 80px;
		}
		
		.lds-ripple div {
			position: absolute;
			border: 4px solid #000;
			opacity: 1;
			border-radius: 50%;
			animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
		}

		.lds-ripple div:nth-child(2) {
			animation-delay: -0.5s;
		}

		@keyframes lds-ripple {
			
			0% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 0;
			}
			4.9% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 0;
			}
			5% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 1;
			}
			100% {
				top: 0px;
				left: 0px;
				width: 72px;
				height: 72px;
				opacity: 0;
			}
		
		}

    </style>

@endsection

@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
    <script src="{{asset('assets/admin/js/bootstrap-datetimepicker.min.js')}}"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            $('#project_start_date').datetimepicker({
			    format: 'YYYY-MM-DDTHH:mm:00',
			    defaultDate: '',
			    ignoreReadonly: true,
			    sideBySide: true,
			    locale:'du',
		    });

            $('#project_end_date').datetimepicker({
			    format: 'YYYY-MM-DDTHH:mm:00',
			    defaultDate: '',
			    ignoreReadonly: true,
			    sideBySide: true,
			    locale:'du',
		    });

        });

        var table1 = $('#example1').DataTable({
            order: [[0, 'asc']],
            retrieve: true,
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

        function project_section(data = null)
        {
            if(data)
            {
                $("#edit_project_id").val(data["row_id"]);
                $("#project_name").val(data["project_name"]);
                $("#project_active").val(data["project_is_active"]);
                $("#project_start_date").val(data["project_start_date"]);
                $("#project_end_date").val(data["project_end_date"]);
                $("#project_description").val(data["project_description"]);
                $("#project_notes").val(data["project_comment"]);
            }
            else
            {
                var now = new Date();
                var curr_day = String(now.getDate()).padStart(2, '0');
                var curr_month = String(now.getMonth() + 1).padStart(2, '0');
                var curr_year = now.getFullYear();
                var start_date = curr_year+"-"+curr_month+"-"+curr_day+"T00:00:00";

                var next = new Date();
                next.setFullYear(next.getFullYear() + 1);
                var next_day = String(next.getDate()).padStart(2, '0');
                var next_month = String(next.getMonth() + 1).padStart(2, '0');
                var next_year = next.getFullYear();
                var end_date = next_year+"-"+next_month+"-"+next_day+"T00:00:00";

                $("#edit_project_id").val("");
                $("#project_name").val("");
                $("#project_active").val(1);
                $("#project_start_date").val(start_date);
                $("#project_end_date").val(end_date);
                $("#project_description").val("");
                $("#project_notes").val("");
            }
            
            $("#project-details").show();
        }

        $(".create-p").on('click',function(e){
            project_section();
        });

        function projects_manage(row_id = null)
        {
            var flag = 0;
            var name = $("#project_name").val();
            var active = $("#project_active").val();
            
            if(active == 1)
            {
                var active_text = "<?php echo __("text.Yes"); ?>";
            }
            else
            {
                var active_text = "<?php echo __("text.No"); ?>";
            }
            
            var start_date = $("#project_start_date").val();
            var end_date = $("#project_end_date").val();
            var description = $("#project_description").val();
            var notes = $("#project_notes").val();

            // if(!name)
            // {
            //     alert("Project name is required");
            //     flag = 1;
            // }

            // if(!start_date)
            // {
            //     alert("Project start date is required");
            //     flag = 1;
            // }

            // if(!end_date)
            // {
            //     alert("Project end date is required");
            //     flag = 1;
            // }

            // if(flag == 1)
            // {
            //     return false;
            // }

            if(row_id)
            {
                table1.row($("#example1 tbody").find('tr[data-row="'+row_id+'"]')).data([
                    "<td><span class='text'>"+name+"</span><input value='"+name+"' type='hidden' name='project_names[]' class='project_names'><input value='0' type='hidden' name='project_totals[]' class='project_totals'></td>",
                    "<td><span class='text'>"+active_text+"</span><input value='"+active+"' type='hidden' name='project_is_active[]' class='project_is_active'></td>",
                    "<td><span class='text'>"+start_date+"</span><input value='"+start_date+"' type='hidden' name='project_start_dates[]' class='project_start_dates'></td>",
                    "<td><span class='text'>"+end_date+"</span><input value='"+end_date+"' type='hidden' name='project_end_dates[]' class='project_end_dates'></td>",
                    "<td><span class='text'>"+description+"</span><input value='"+description+"' type='hidden' name='project_descriptions[]' class='project_descriptions'></td>",
                    "<td><span class='text'>"+notes+"</span><input value='"+notes+"' type='hidden' name='project_comments[]' class='project_comments'></td>",
                    "<td><div style='display: flex;'><span data-type='1' class='edit-p' style='cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-edit'></i></span><span data-type='2' class='delete-p' style='cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-trash-o'></i></span></div></td>"
                ]).order([]).draw(false);
            }
            else
            {
                var last_row = $('#example1 tbody tr:last').data('row');
                last_row = last_row + 1;
                table1.row.add($("<tr data-row="+last_row+"><td><span class='text'>"+name+"</span><input value='"+name+"' type='hidden' name='project_names[]' class='project_names'><input value='0' type='hidden' name='project_totals[]' class='project_totals'></td><td><span class='text'>"+active_text+"</span><input value='"+active+"' type='hidden' name='project_is_active[]' class='project_is_active'></td><td><span class='text'>"+start_date+"</span><input value='"+start_date+"' type='hidden' name='project_start_dates[]' class='project_start_dates'></td><td><span class='text'>"+end_date+"</span><input value='"+end_date+"' type='hidden' name='project_end_dates[]' class='project_end_dates'></td><td><span class='text'>"+description+"</span><input value='"+description+"' type='hidden' name='project_descriptions[]' class='project_descriptions'></td><td><span class='text'>"+notes+"</span><input value='"+notes+"' type='hidden' name='project_comments[]' class='project_comments'></td><td><div style='display: flex;'><span data-type='1' class='edit-p' style='cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-edit'></i></span><span data-type='2' class='delete-p' style='cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-trash-o'></i></span></div></td></tr>")).order([]).draw(false);
            }

            return true;
        }

        $('body').on('click','.btn-p-submit, .delete-p',function(e){

            var flag = 1;
            var customer_id = $("#org_id").val();
            var token = $('[name="_token"]').val();

            if($(this).data("type") == 2)
            {
                table1.row( $(this).parents('tr') ).remove().order([]).draw(false);
            }
            else
            {
                var edit_project_id = $("#edit_project_id").val();

                if(edit_project_id)
                {
                    flag = projects_manage(edit_project_id);
                }
                else
                {
                    flag = projects_manage();
                }
            }

            if(flag)
            {
                $('#cover').css('display', 'flex');

                if(customer_id)
                {
                    var project_names = $('[name="project_names[]"]').map(function(){return $(this).val();}).get();
                    var project_active = $('[name="project_is_active[]"]').map(function(){return $(this).val();}).get();
                    var project_start_dates = $('[name="project_start_dates[]"]').map(function(){return $(this).val();}).get();
                    var project_end_dates = $('[name="project_end_dates[]"]').map(function(){return $(this).val();}).get();
                    var project_descriptions = $('[name="project_descriptions[]"]').map(function(){return $(this).val();}).get();
                    var project_comments = $('[name="project_comments[]"]').map(function(){return $(this).val();}).get();
                    var project_totals = $('[name="project_totals[]"]').map(function(){return $(this).val();}).get();

                    $.ajax({
				        url: '{{route("project-post")}}',
				        type: 'POST',
                        dataType: "json",
				        data: { customer_id: customer_id,project_names: project_names,project_active: project_active,project_start_dates: project_start_dates,project_end_dates: project_end_dates,project_descriptions: project_descriptions,project_comments: project_comments,project_totals: project_totals,_token:token },
				        success: function(data){
				        },
				        complete: function(){
                            $('#cover').css('display', 'none');
                            $("#edit_project_id").val("");
                            $("#project_name").val("");
                            $("#project_active").val(1);
                            $("#project_start_date").val("");
                            $("#project_end_date").val("");
                            $("#project_description").val("");
                            $("#project_notes").val("");
                            $("#project-details").hide();
				        }
			        });
                }
                else
                {
                    $('#cover').css('display', 'none');
                }
            }

        });

        $('body').on('click','.edit-p',function(e){

            var row_id = $(this).parents("tr").data("row");
            var project_name = $(this).parents("tr").find(".project_names").val();
            var project_is_active = $(this).parents("tr").find(".project_is_active").val();
            var project_start_date = $(this).parents("tr").find(".project_start_dates").val();
            var project_end_date = $(this).parents("tr").find(".project_end_dates").val();
            var project_description = $(this).parents("tr").find(".project_descriptions").val();
            var project_comment = $(this).parents("tr").find(".project_comments").val();

            $data = {"row_id":row_id,"project_name":project_name,"project_is_active":project_is_active,"project_start_date":project_start_date,"project_end_date":project_end_date,"project_description":project_description,"project_comment":project_comment};
            project_section($data);

        });

        var table = $('#example').DataTable({
            order: [[0, 'asc']],
            retrieve: true,
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

        function contact_person_section(data = null)
        {
            if(data)
            {
                $("#edit_id").val(data["row_id"]);
                $("#contact_person_name").val(data["name"]);
                $("#contact_person_phone_number").val(data["phone"]);
                $("#contact_person_email").val(data["email"]);
            }
            else
            {
                $("#edit_id").val("");
                $("#contact_person_name").val("");
                $("#contact_person_phone_number").val("");
                $("#contact_person_email").val("");
            }
            
            $("#contact-person-details").show();
        }

        $(".create-cp").on('click',function(e){
            contact_person_section();
        });

        function contact_details_manage(row_id = null)
        {
            var name = $("#contact_person_name").val();
            var phone = $("#contact_person_phone_number").val();
            var email = $("#contact_person_email").val();

            if(row_id)
            {
                table.row($("#example tbody").find('tr[data-row="'+row_id+'"]')).data([
                    "<td><span class='text'>"+name+"</span><input value='"+name+"' type='hidden' name='contact_person_names[]' class='contact_person_names'></td>",
                    "<td><span class='text'>"+phone+"</span><input value='"+phone+"' type='hidden' name='contact_person_phone_numbers[]' class='contact_person_phone_numbers'></td>",
                    "<td><span class='text'>"+email+"</span><input value='"+email+"' type='hidden' name='contact_person_emails[]' class='contact_person_emails'></td>",
                    "<td><div style='display: flex;'><span data-type='1' class='edit-cp' style='cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-edit'></i></span><span data-type='2' class='delete-cp' style='cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-trash-o'></i></span></div></td>"
                ]).order([]).draw(false);
            }
            else
            {
                var last_row = $('#example tbody tr:last').data('row');
                last_row = last_row + 1;
                table.row.add($("<tr data-row="+last_row+"><td><span class='text'>"+name+"</span><input value='"+name+"' type='hidden' name='contact_person_names[]' class='contact_person_names'></td><td><span class='text'>"+phone+"</span><input value='"+phone+"' type='hidden' name='contact_person_phone_numbers[]' class='contact_person_phone_numbers'></td><td><span class='text'>"+email+"</span><input value='"+email+"' type='hidden' name='contact_person_emails[]' class='contact_person_emails'></td><td><div style='display: flex;'><span data-type='1' class='edit-cp' style='cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-edit'></i></span><span data-type='2' class='delete-cp' style='cursor: pointer;font-size: 20px;margin-left: 5px;width: 20px;height: 20px;line-height: 20px;'><i style='width: 100%;' class='fa fa-fw fa-trash-o'></i></span></div></td></tr>")).order([]).draw(false);
            }
        }

        $('body').on('click','.btn-cp-submit, .delete-cp',function(e){

            // if(!$("#contact_person_name").val())
            // {
            //     alert("Contact person name is required.");
            // }
            var customer_id = $("#org_id").val();
            var token = $('[name="_token"]').val();

            $('#cover').css('display', 'flex');
                
            if($(this).data("type") == 2)
            {
                table.row( $(this).parents('tr') ).remove().order([]).draw(false);
            }
            else
            {
                var edit_id = $("#edit_id").val();

                if(edit_id)
                {
                    contact_details_manage(edit_id);
                }
                else
                {
                    contact_details_manage();
                }
            }

            if(customer_id)
            {
                var names = $('[name="contact_person_names[]"]').map(function(){return $(this).val();}).get();
                var phone_numbers = $('[name="contact_person_phone_numbers[]"]').map(function(){return $(this).val();}).get();
                var emails = $('[name="contact_person_emails[]"]').map(function(){return $(this).val();}).get();

                $.ajax({
				    url: '{{route("contact-person-post")}}',
				    type: 'POST',
                    dataType: "json",
				    data: { customer_id: customer_id,contact_person_names: names,contact_person_phone_numbers: phone_numbers,contact_person_emails: emails,_token:token },
				    success: function(data){
				    },
				    complete: function(){
                        $('#cover').css('display', 'none');
                        $("#edit_id").val("");
                        $("#contact_person_name").val("");
                        $("#contact_person_phone_number").val("");
                        $("#contact_person_email").val("");
                        $("#contact-person-details").hide();
				    }   
			    });
            }
            else
            {
                $('#cover').css('display', 'none');
            }

        });

        $('body').on('click','.edit-cp',function(e){

            var row_id = $(this).parents("tr").data("row");
            var name = $(this).parents("tr").find(".contact_person_names").val();
            var phone = $(this).parents("tr").find(".contact_person_phone_numbers").val();
            var email = $(this).parents("tr").find(".contact_person_emails").val();

            $data = {"row_id":row_id,"name":name,"phone":phone,"email":email};
            contact_person_section($data);

        });

        function initMap() {

            var input = document.getElementById('address');

            var options = {
                componentRestrictions: {country: "nl"}
            };

            var autocomplete = new google.maps.places.Autocomplete(input,options);

            // Set the data fields to return when the user selects a place.
            autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

            autocomplete.addListener('place_changed', function() {

                var flag = 0;

                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("{{__('text.No details available for input: ')}}" + place.name);
                    return;
                }
                else
                {
                    var string = $('#address').val().substring(0, $('#address').val().indexOf(',')); //first string before comma

                    if(string)
                    {
                        var is_number = $('#address').val().match(/\d+/);

                        if(is_number === null)
                        {
                            flag = 1;
                        }
                    }
                }

                var city = '';
                var postal_code = '';
                var street_name = '';
                var street_number = '';

                for(var i=0; i < place.address_components.length; i++)
                {
                    if(place.address_components[i].types[0] == 'postal_code' || place.address_components[i].types[0] == 'postal_code_prefix')
                    {
                        postal_code = place.address_components[i].long_name;
                    }

                    if(place.address_components[i].types[0] == 'locality')
                    {
                        city = place.address_components[i].long_name;
                    }

                    if(place.address_components[i].types[0] == 'route')
                    {
                        street_name = place.address_components[i].long_name;
                    }

                    if(place.address_components[i].types[0] == 'street_number')
                    {
                        street_number = place.address_components[i].long_name;
                    }
                }

                if(city == '')
                {
                    for(var i=0; i < place.address_components.length; i++)
                    {
                        if(place.address_components[i].types[0] == 'administrative_area_level_2')
                        {
                            city = place.address_components[i].long_name;
                        }
                    }
                }

                if(postal_code == '' || city == '')
                {
                    flag = 1;
                }

                if(!flag)
                {
                    $('#check_address').val(1);
                    $("#address-error").remove();
                    $('#postcode').val(postal_code);
                    $("#city").val(city);
                    $("#street_name").val(street_name);
                    $("#street_number").val(street_number);
                }
                else
                {
                    $('#address').val('');
                    $('#postcode').val('');
                    $("#city").val('');
                    $("#street_name").val('');
                    $("#street_number").val('');

                    $("#address-error").remove();
                    $('#address').parent().append('<small id="address-error" style="color: red;display: block;margin-top: 10px;">{{__('text.Kindly write your full address with house / building number so system can detect postal code and city from it!')}}</small>');
                }

            });
        }

        $("#address").on('input',function(e){
            $(this).next('input').val(0);
        });

        $("#address").focusout(function(){

            var check = $(this).next('input').val();

            if(check == 0)
            {
                $(this).val('');
                $('#postcode').val('');
                $("#city").val('');
            }
        });

    </script>


@endsection
