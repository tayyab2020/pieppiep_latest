@extends('layouts.handyman')

@section('content')

	<script>
		var auto_save_route = {
			store_new_quotation: '{{route("store-new-quotation")}}',
			send_quotation: '{{__("text.Send Quotation")}}',
			send_quotation_url: '{{ url("/aanbieder/send-quotation-admin/") }}',
			document_date: '{{__("text.Document date")}}',
			document_number: '{{__("text.Document Number")}}',
    	};
	</script>
	<script src="{{asset('assets/admin/js/main1.js?v=1.1')}}"></script>
	<script src="{{asset('assets/admin/js/bootstrap-tagsinput.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
	<script src="{{asset('assets/admin/js/bootstrap-datetimepicker.min.js')}}"></script>
	<script src="{{asset('assets/front/js/autoSave.js?v=1.5')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/locale/nl.js"></script>

	<div class="right-side">

		<div class="container-fluid">
			<div class="row">

				<form id="form-quote" style="padding: 0;" class="form-horizontal" action="{{route('store-new-quotation')}}" method="POST" enctype="multipart/form-data">
					{{csrf_field()}}

					<input type="hidden" class="appointment_data" name="appointment_data">
					<?php $random_token = bin2hex(random_bytes(20));
					if(isset($invoice) && !isset($invoice->create_negative_invoice) && $invoice[0]->document_date){
						$document_date = date('d-m-Y',strtotime($invoice[0]->document_date));
					}else{
						$document_date = date('d-m-Y');
					}
					if(isset($invoice) && !isset($invoice->create_negative_invoice)){
						$dc = explode('-', $invoice[0]->document_number); $dc = end($dc);
					}else{
						$dc = '';
					} ?>

					<input type="hidden" name="user_id" value="{{Auth::guard('user')->user()->id}}">
					<input type="hidden" name="draft_token" value="{{$random_token}}">
					<input type="hidden" name="form_type" value="1">
					<input type="hidden" id="quote_request_id" name="quote_request_id" value="{{isset($request_id) ? $request_id : (isset($invoice) ? $invoice[0]->quote_request_id : null)}}">
					<input type="hidden" name="quotation_id" value="{{isset($invoice) ? $invoice[0]->invoice_id : null}}">
					<input type="hidden" name="is_invoice" value="{{isset($invoice) ? (Route::currentRouteName() == 'view-new-quotation' ? 0 : 1) : 0}}">
					{{--				<input type="hidden" name="direct_invoice" value="{{Route::currentRouteName() == 'create-direct-invoice' ? 1 : 0}}">--}}
					<input type="hidden" name="negative_invoice" value="{{Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice' ? 1 : 0}}">
					<input type="hidden" name="negative_invoice_id" value="{{isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice' ? ($invoice[0]->negative_invoice != 0 ? $invoice[0]->invoice_id : null) : null) : null}}">
					<input type="hidden" value="{{isset($request_id) && $request_id ? 1 : 0}}" id="request_quotation">
					<input type="hidden" id="document_date" name="document_date" value="{{$document_date}}">
					@if(Route::currentRouteName() == 'create-new-quotation' || Route::currentRouteName() == 'create-custom-quotation' || Route::currentRouteName() == 'create-direct-invoice' || Route::currentRouteName() == 'view-new-quotation')
						<?php $dt = strtotime($document_date); $expire_date = (isset($invoice) && !isset($invoice->create_negative_invoice) && $invoice[0]->expire_date) ? date('d-m-Y',strtotime($invoice[0]->expire_date)) : date('d-m-Y',strtotime( '+1 month',$dt )); ?>
                        <input type="hidden" id="expire_date" name="expire_date" value="{{$expire_date}}">
					@endif
					<input type="hidden" id="document_number" name="document_number" value="{{$dc}}">

					<div style="margin: 0;" class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<!-- Starting of Dashboard data-table area -->
							<div class="section-padding add-product-1" style="padding: 0;">

								<div style="margin: 0;" class="row">
									<div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<div style="box-shadow: none;" class="add-product-box">
											<div style="align-items: center;" class="add-product-header products">

												<h2 style="margin-top: 0;">{{isset($invoice) ? (Route::currentRouteName() == 'view-new-quotation' ? __('text.View Quotation') : (Route::currentRouteName() == 'create-new-negative-invoice' ? __('text.Create Negative Invoice') : (Route::currentRouteName() == 'view-negative-invoice' ? __('text.View Negative Invoice') : __('text.View Invoice')) )) : (Route::currentRouteName() == 'create-direct-invoice' ? __('text.Create Invoice') : __('text.Create Quotation'))}}</h2>

												<div class="con-panel" style="display: flex;">

													@if(Route::currentRouteName() == 'view-new-quotation')
													
														@if($invoice[0]->quote_request_id)
													
															@if(!$invoice[0]->admin_quotation_sent)

																<a style="margin-right: 10px;border-radius: 10px;line-height: 22px;" class="btn btn-success send-quote-btn" href="{{ url('/aanbieder/send-quotation-admin/'.$invoice[0]->invoice_id) }}">{{__('text.Send Quotation')}}</a>

															@endif

														@else

															<a style="margin-right: 10px;border-radius: 10px;line-height: 22px;background-color: #5cb85c;border-color: #4cae4c;color: white;" class="btn send-new-quotation send-quote-btn" data-id="{{$invoice[0]->invoice_id}}" href="javascript:void(0)">{{__('text.Send Quotation')}}</a>
													
														@endif

													@endif

													<div class="actions-sec" style="background-color: black;border-radius: 10px;padding: 0 10px;">

														<div class="action-btns">
															<span class="tooltip1 show-pdf" style="cursor: pointer;font-size: 20px;margin-right: 10px;color: white;">
																<i class="fa fa-fw fa-eye"></i>
																<span class="tooltiptext">{{__('text.View')}}</span>
															</span>
	
															@if(Route::currentRouteName() == 'view-new-invoice' || Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice')
	
																<span class="tooltip1 save-data" style="cursor: pointer;font-size: 20px;margin-right: 10px;color: white;">
																	<i class="fa fa-fw fa-save"></i>
																	<span class="tooltiptext">{{__('text.Save')}}</span>
																</span>
	
															@else
	
																@if((isset($invoice) && ($invoice[0]->status == 0 || $invoice[0]->status == 1 || $invoice[0]->status == 2 || $invoice[0]->ask_customization)) || !isset($invoice))
	
																	<span class="tooltip1 save-data" style="cursor: pointer;font-size: 20px;margin-right: 10px;color: white;">
																		<i class="fa fa-fw fa-save"></i>
																		<span class="tooltiptext">{{__('text.Save')}}</span>
																	</span>
	
																@endif
	
															@endif
	
															<span class="tooltip1 close-form" style="cursor: pointer;font-size: 20px;color: white;">
																<i class="fa fa-fw fa-close"></i>
																<span class="tooltiptext">{{__('text.Close')}}</span>
															</span>
														</div>

														<div class="icon-container1 hide"><i class="loader"></i></div>

													</div>

												</div>

											</div>

											<hr>

											<div style="margin: 0;display: flex;justify-content: flex-end;" class="row">

												@if((isset($invoice) && !$invoice[0]->quote_request_id) || (isset($request_id) && !$request_id))

													<div class="col-md-5 customer_box">
														<div class="form-group" style="margin: 0;">

															<label>{{__('text.Customer')}}</label>

															<div id="cus-box" style="display: flex;">

																<select class="customer-select form-control" name="customer" required>

																	<option value="">{{__('text.Select Customer')}}</option>

																	@foreach($customers as $key)

																		<option {{isset($invoice) ? ($invoice[0]->user_id == $key->user_id ? 'selected' : null) : null}} data-name="{{$key->name}}" data-familyname="{{$key->family_name}}" data-businessname="{{$key->business_name}}" data-address="{{$key->address}}" data-streetname="{{$key->street_name}}" data-streetnumber="{{$key->street_number}}" data-postcode="{{$key->postcode}}" data-city="{{$key->city}}" data-phone="{{$key->phone}}" data-email="{{$key->fake_email == 0 ? $key->email : NULL}}" value="{{$key->id}}">{{$key->name}} {{$key->family_name}}</option>

																	@endforeach

																</select>

																@if((Route::currentRouteName() == 'view-new-invoice' || Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice') || ((isset($invoice) && ($invoice[0]->status == 0 || $invoice[0]->status == 1 || $invoice[0]->ask_customization)) || !isset($invoice)))

																	<button type="button" href="#createCustomerModal" role="button" data-toggle="modal" style="outline: none;margin-left: 10px;" class="btn btn-primary add-customer">{{__('text.Add New Customer')}}</button>

																@endif

																<button style="margin-left: 10px;" type="button" class="btn btn-success edit-customer">
																	<i class="fa fa-fw fa-edit"></i>
																</button>

															</div>
														</div>
													</div>

												@endif

												<div style="display: flex;justify-content: flex-end;align-items: flex-end;" class="col-md-7 intro-box">
													<div href="#infoModal" role="button" data-toggle="modal" class="document-info">
														<span class="intro-text q_i_number">{{(isset($invoice) && !isset($invoice->create_negative_invoice)) ? __("text.Document Number") . ": " . $invoice[0]->document_number : ""}}</span>
														<span class="intro-text document_date_text">{{(isset($invoice) && !isset($invoice->create_negative_invoice)) ? __("text.Document date") . ": " . date('d-m-Y',strtotime($invoice[0]->document_date)) : ""}}</span>
													</div>
												</div>

											</div>

											<div style="display: inline-block;width: 100%;">

												@include('includes.form-success')

												<div class="regards-div" style="display: flex;justify-content: space-between;align-items: flex-end;padding: 0 15px;margin-top: 30px;">

													<textarea maxlength="200" placeholder="{{__('text.Enter regards here...')}}" style="width: 50%;outline: none;border-color: #c8c8c8;border-radius: 5px;padding: 10px;" rows="2" id="regards" name="regards">{{isset($invoice) ? $invoice[0]->regards : ''}}</textarea>
													
												</div>

												<select style="display: none;" class="form-control all-products" id="blood_grp">

													@foreach($products as $key)

														@foreach($key->models as $key1)

															@foreach($key->colors as $key2)

																<option data-title="{{$key->title.', '.$key1->model.', '.$key2->title.', ('.$key->company_name.')'}}" data-model="{{$key1->model}}" data-model-id="{{$key1->id}}" data-color="{{$key2->title}}" data-color-id="{{$key2->id}}" data-supplier-id="{{$key->organization_id}}" value="{{$key->id}}">{{$key->title.', '.$key1->model.', '.$key2->title.', ('.$key->company_name.')' . ' € ' . number_format((float)$key1->estimated_price, 2, ',', '') . ' per m&#178;, pakinhoud ' . number_format((float)$key1->estimated_price_quantity, 2, ',', '') . ' m&#178;'}}</option>

															@endforeach

														@endforeach

													@endforeach

													@foreach($services as $service)
														<option data-title="{{$service->title}}" data-type="Service" value="{{$service->id}}S">{{$service->title}}</option>
													@endforeach

													@foreach($items as $item1)
													<option data-title="{{$item1->cat_name}}" data-type="Item" value="{{$item1->id}}I">{{$item1->cat_name}}</option>
													@endforeach

												</select>

												<div style="padding-bottom: 0;" class="form-horizontal">

													<div style="margin: 0;border-top: 1px solid #eee;" class="row">

														<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 second-row" style="padding-bottom: 15px;">

															<section id="products_table" style="width: 100%;">

																<div class="header-div">
																	<div class="headings" style="width: 6%;"></div>
																	<div class="headings" style="width: 30%;">{{__('text.Product')}}</div>
																	<div class="headings" style="width: 17%;">{{__('text.Qty')}}</div>
																	<div class="headings" style="width: 17%;">{{__('text.€ Art.')}}</div>
																	<div class="headings" style="width: 10%;">{{__('text.Discount')}}</div>
																	<div class="headings" style="width: 7%;">{{__('text.€ Total')}}</div>
																	<div class="headings" style="width: 13%;"></div>
																</div>

																@if(isset($invoice))

																	@foreach($invoice as $i => $item)

																		<div @if($i==0) class="content-div active" @else class="content-div" @endif data-id="{{$i+1}}">

																			<div class="content full-res item1" style="width: 6%;">
																				<label class="content-label">Sr. No</label>
																				<img draggable="false" style="width: 20px;margin: 0 10px;" src="{{asset('assets/images/drag.png')}}">
																				<div style="padding: 0 5px;" class="sr-res">{{$i+1}}</div>
																			</div>

																			<input type="hidden" value="{{$item->order_number}}" id="order_number" name="order_number[]">
																			<input type="hidden" value="{{$item->basic_price}}" id="basic_price" name="basic_price[]">
																			<input type="hidden" value="{{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? $item->rate * -1 : $item->rate}}" id="rate" name="rate[]">
																			<input type="hidden" value="{{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? $item->amount * -1 : $item->amount}}" id="row_total" name="total[]">
																			<input type="hidden" value="{{$i+1}}" id="row_id" name="row_id[]">
																			<input type="hidden" value="{{$item->childsafe ? 1 : 0}}" id="childsafe" name="childsafe[]">
																			<input type="hidden" value="{{$item->ladderband ? 1 : 0}}" id="ladderband" name="ladderband[]">
																			<input type="hidden" value="{{$item->ladderband_value ? $item->ladderband_value : 0}}" id="ladderband_value" name="ladderband_value[]">
																			<input type="hidden" value="{{$item->ladderband_price_impact ? $item->ladderband_price_impact : 0}}" id="ladderband_price_impact" name="ladderband_price_impact[]">
																			<input type="hidden" value="{{$item->ladderband_impact_type ? $item->ladderband_impact_type : 0}}" id="ladderband_impact_type" name="ladderband_impact_type[]">
																			<input type="hidden" value="0" id="area_conflict" name="area_conflict[]">
																			<input type="hidden" value="{{$item->delivery_days}}" id="delivery_days" name="delivery_days[]">
																			<input type="hidden" value="{{$item->base_price}}" id="base_price" name="base_price[]">
																			<input type="hidden" value="{{$item->supplier_margin}}" id="supplier_margin" name="supplier_margin[]">
																			<input type="hidden" value="{{$item->retailer_margin}}" id="retailer_margin" name="retailer_margin[]">
																			<input type="hidden" value="{{$item->box_quantity}}" id="estimated_price_quantity" name="estimated_price_quantity[]">
																			<input type="hidden" value="{{$item->measure}}" id="measure" name="measure[]">
																			<input type="hidden" value="{{$item->max_width}}" id="max_width" name="max_width[]">

																			<div style="width: 30%;" class="products content item3 full-res">

																				<label class="content-label">{{__('text.Product')}}</label>

																				<div class="autocomplete" style="width:100%;">
																					<textarea @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif style="background: transparent;resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product_descriptions[]" placeholder="{{__('text.Select Product')}}">{{$item->product_description ? $item->product_description : ($item->item_id != 0 ? $item_titles[$i]->cat_name : ($item->service_id != 0 ? $service_titles[$i] : ($item->product_id ? $product_titles[$i].', '.$model_titles[$i].', '.$color_titles[$i].', ('.$product_suppliers[$i]->company_name.')' . ' € ' . number_format((float)($item->price_before_labor/$item->box_quantity), 2, ',', '') . ' per m², pakinhoud ' . number_format((float)$item->box_quantity, 2, ',', '') . ' m²' : "")))}}</textarea>
																					<span @if(!$item->product_id && !$item->item_id && !$item->service_id) class="pis_title hide" @else class="pis_title" @endif>
																						{{$item->item_id != 0 ? optional($item_titles[$i])->cat_name : ($item->service_id != 0 ? $service_titles[$i] : ($item->product_id ? $item->secondary_title : ""))}}
																					</span>
																				</div>

																				<input type="hidden" value="{{$item->item_id != 0 ? $item->item_id.'I' : ($item->service_id != 0 ? $item->service_id.'S' : $item->product_id)}}" name="products[]" id="product_id">
																				<input type="hidden" value="{{$item->supplier_id}}" name="suppliers[]" id="supplier_id">
																				<input type="hidden" value="{{$item->color}}" name="colors[]" id="color_id">
																				<input type="hidden" value="{{$item->model_id}}" name="models[]" id="model_id">
																				<input type="hidden" value="{{$item->secondary_title}}" name="secondary_titles[]" id="secondary_title">

																			</div>

																			<div class="content item6" style="width: 17%;">

																				<label class="content-label">{{__('text.Qty')}}</label>

																				<div style="display: flex;align-items: center;height: 100%;position: relative;">
																					<input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif type="text" value="{{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($item->qty * -1), 2, ',', '') : number_format((float)$item->qty, 2, ',', '')}}" maskedformat="9,1" name="qty[]" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">
																					<div class="icon-container hide"><i class="loader"></i></div>
																				</div>
																			</div>

																			<div class="content item6" style="width: 17%;">

																				<label class="content-label">{{__('text.€ Art.')}}</label>

																				<div style="display: flex;align-items: center;height: 100%;position: relative;">
																					<span>€</span>
																					<input type="text" maskedformat="9,1" value="{{number_format((float)$item->price_before_labor, 2, ',', '')}}" name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">
																					<input type="hidden" value="{{$item->price_before_labor}}" name="price_before_labor_old[]" class="price_before_labor_old">
																					<div class="icon-container hide"><i class="loader"></i></div>
																				</div>
																			</div>

																			<div class="content item8" style="width: 10%;">

																				<label class="content-label">{{__('text.Discount')}}</label>

																				<span>€</span>
																				<input type="text" value="{{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($item->total_discount * -1), 2, ',', '') : number_format((float)$item->total_discount, 2, ',', '')}}" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;height: 30px;" class="form-control total_discount res-white">
																				<input type="hidden" value="{{$item->qty != 0 ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? ($item->total_discount * -1)/abs($item->qty) : $item->total_discount/abs($item->qty)) : 0}}" class="total_discount_old">
																			</div>

																			<div style="width: 7%;" class="content item9">

																				<label class="content-label">{{__('text.€ Total')}}</label>
																				<div class="price res-white">€ {{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($item->rate * -1), 2, ',', '.') : number_format((float)$item->rate, 2, ',', '.')}}</div>

																			</div>

																			<div class="content item10 last-content" id="next-row-td" style="padding: 0;width: 13%;">

																				@if((Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice' || Route::currentRouteName() == 'view-new-invoice') || (isset($invoice) && ($invoice[0]->status == 0 || $invoice[0]->status == 1 || $invoice[0]->ask_customization)) || !isset($invoice))

																					<div class="res-white" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;">

																						<div style="display: none;" class="green-circle tooltip1">
																							<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.ALL features selected!')}}</span>
																						</div>

																						<div style="visibility: hidden;" class="yellow-circle tooltip1">
																							<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.Select all features!')}}</span>
																						</div>

																						<span id="next-row-span" class="tooltip1 add-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
																							<i id="next-row-icon" class="fa fa-fw fa-plus"></i>
																							<span class="tooltiptext">{{__('text.Add')}}</span>
																						</span>

																						<span id="next-row-span" class="tooltip1 remove-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
																							<i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
																							<span class="tooltiptext">{{__('text.Remove')}}</span>
																						</span>

																						<span id="next-row-span" class="tooltip1 copy-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">
																							<i id="next-row-icon" class="fa fa-fw fa-copy"></i>
																							<span class="tooltiptext">{{__('text.Copy')}}</span>
																						</span>

																						<!--<span id="next-row-span" class="tooltip1 next-row" style="cursor: pointer;font-size: 20px;">
                                                                                            <i id="next-row-icon" style="color: #868686;" class="fa fa-fw fa-chevron-right"></i>
                                                                                            <span style="top: 45px;left: -20px;" class="tooltiptext">Next</span>
                                                                                        </span>-->

																					</div>

																				@endif

																			</div>

																			<div class="item11" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;margin-top: 10px;">
																				<button style="outline: none;" type="button" class="btn btn-info res-collapse collapsed" data-toggle="collapse" data-target="#demo{{$i+1}}"></button>
																			</div>

																			<div style="width: 100%;" id="demo{{$i+1}}" class="item17 collapse">

																				<div style="width: 25%;margin-left: 10px;" class="ledger-box item14">

																					<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            							<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.General Ledger')}}</label>
																					
																					</div>

																					<select name="general_ledgers[]" class="general_ledger">
																						<option value="">{{__("text.Select General Ledger")}}</option>
																						@foreach($general_ledgers as $key)
																							<option {{$item->ledger_id == $key->id ? "selected" : ""}} value="{{$key->id}}">{{$key->number . " - " . $key->title}}</option>
																						@endforeach
																					</select>

																				</div>

																				<div style="width: 25%;margin-left: 10px;" class="vat-box item15">

																					<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            							<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.VAT')}}</label>
																					
																					</div>

																					<select name="vats[]" class="vat">
																						<option value="">{{__("text.Select VAT")}}</option>
																						@foreach($vats as $key)
																							<option data-percentage="{{$key->vat_percentage}}" {{$item->vat_id == $key->id ? "selected" : ""}} value="{{$key->id}}">{{$key->vat_percentage . "% - " . $key->rule}}</option>
																						@endforeach
																					</select>

																				</div>

																				<div style="width: 25%;margin-left: 10px;" class="discount-box item16">

																					<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            							<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.Discount')}} <span class="discount-sign">{{$item->discount_option ? '€' : '%'}}</span></label>

																						<div style="display: flex;align-items: center;">

																							<span style="font-size: 15px;padding-right: 10px;font-weight: 600;">%</span>
                                                            							
																							<label style="margin: 0;" class="switch">
                                                                								<input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) disabled @endif {{$item->discount_option ? 'checked' : null}} class="discount_option" type="checkbox">
                                                                								<span @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) style="cursor: not-allowed;" @endif class="slider round"></span>
                                                            								</label>
                                                            						
																							<span style="font-size: 15px;padding-left: 10px;">€</span>

																						</div>
																					
																					</div>

																					<input value="{{$item->discount_option}}" class="discount_option_values" name="discount_option_values[]" type="hidden">
																					<input maskedformat="9,1" @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif style="height: 35px;border-radius: 4px;" placeholder="{{__('text.Enter discount in percentage')}}" type="text" class="form-control discount_values" value="{{Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($item->discount * -1), 2, ',', '') : number_format((float)$item->discount, 2, ',', '')}}" name="discount[]">

																				</div>

																			</div>

																		</div>

																	@endforeach

																@else

																	<div class="content-div active" data-id="1">

																		<div class="content full-res item1" style="width: 6%;">
																			<label class="content-label">Sr. No</label>
																			<img draggable="false" style="width: 20px;margin: 0 10px;" src="{{asset('assets/images/drag.png')}}">
																			<div style="padding: 0 5px;" class="sr-res">1</div>
																		</div>

																		<input type="hidden" id="order_number" name="order_number[]">
																		<input type="hidden" id="basic_price" name="basic_price[]">
																		<input type="hidden" id="rate" value="0.00" name="rate[]">
																		<input type="hidden" id="row_total" value="0.00" name="total[]">
																		<input type="hidden" value="1" id="row_id" name="row_id[]">
																		<input type="hidden" value="0" id="childsafe" name="childsafe[]">
																		<input type="hidden" value="0" id="ladderband" name="ladderband[]">
																		<input type="hidden" value="0" id="ladderband_value" name="ladderband_value[]">
																		<input type="hidden" value="0" id="ladderband_price_impact" name="ladderband_price_impact[]">
																		<input type="hidden" value="0" id="ladderband_impact_type" name="ladderband_impact_type[]">
																		<input type="hidden" value="0" id="area_conflict" name="area_conflict[]">
																		<input type="hidden" id="delivery_days" name="delivery_days[]">
																		<input type="hidden" id="base_price" name="base_price[]">
																		<input type="hidden" id="supplier_margin" name="supplier_margin[]">
																		<input type="hidden" id="retailer_margin" name="retailer_margin[]">
																		<input type="hidden" id="estimated_price_quantity" name="estimated_price_quantity[]">
																		<input type="hidden" id="measure" name="measure[]">
																		<input type="hidden" id="max_width" name="max_width[]">

																		<div style="width: 30%;" class="products content item3 full-res">

																			<label class="content-label">{{__('text.Product')}}</label>

																			<div class="autocomplete" style="width:100%;">
																				<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product_descriptions[]" placeholder="{{__('text.Select Product')}}">{{ isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->title.', '.($product_request->model ? $product_request->model.', ' : null).$product_request->color.', ('.$product_request->company_name.')' . ' € ' . number_format((float)$product_request->estimated_price, 2, ',', '') . ' per m², pakinhoud ' . number_format((float)$product_request->estimated_price_quantity, 2, ',', '') . ' m²' : $product_request->title) : null }}</textarea>
																				<span @if(isset($request_id) && $request_id) class="pis_title" @else class="pis_title hide" @endif>{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->title.', '.($product_request->model ? $product_request->model.', ' : null).$product_request->color.', ('.$product_request->company_name.')' : $product_request->title) : null}}</span>
																			</div>

																			<input type="hidden" value="{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->id : $product_request->id.'S') : null}}" name="products[]" id="product_id">
																			<input type="hidden" value="{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->supplier_id : null) : null}}" name="suppliers[]" id="supplier_id">
																			<input type="hidden" value="{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->color_id : null) : null}}" name="colors[]" id="color_id">
																			<input type="hidden" value="{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->model_id : null) : null}}" name="models[]" id="model_id">
																			<input type="hidden" value="{{isset($request_id) && $request_id ? ($quote->quote_service ? $product_request->title.', '.($product_request->model ? $product_request->model.', ' : null).$product_request->color.', ('.$product_request->company_name.')' : $product_request->title) : null}}" name="secondary_titles[]" id="secondary_title">

																		</div>

																		<div class="content item6" style="width: 17%;">

																			<label class="content-label">{{__('text.Qty')}}</label>

																			<div style="display: flex;align-items: center;height: 100%;position: relative;">
																				<input type="text" value="{{isset($request_id) && $request_id ? number_format((float)$quote_qty, 2, ',', '') : '1,00'}}" name="qty[]" maskedformat="9,1" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">
																				<div class="icon-container hide"><i class="loader"></i></div>
																			</div>
																		</div>

																		<div class="content item6" style="width: 17%;">

																			<label class="content-label">{{__('text.€ Art.')}}</label>

																			<div style="display: flex;align-items: center;height: 100%;position: relative;">
																				<span>€</span>
																				<input type="text" maskedformat="9,1" value="0" name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">
																				<input type="hidden" value="0" name="price_before_labor_old[]" class="price_before_labor_old">
																				<div class="icon-container hide"><i class="loader"></i></div>
																			</div>
																		</div>

																		<div class="content item8" style="width: 10%;">

																			<label class="content-label">{{__('text.Discount')}}</label>

																			<span>€</span>
																			<input type="text" value="0" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;height: 30px;" class="form-control total_discount res-white">
																			<input type="hidden" value="0" class="total_discount_old">
																		</div>

																		<div style="width: 7%;" class="content item9">

																			<label class="content-label">{{__('text.€ Total')}}</label>
																			<div class="price res-white"></div>

																		</div>

																		<div class="content item10 last-content" id="next-row-td" style="padding: 0;width: 13%;">
																			<div class="res-white" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;">

																				<div style="display: none;" class="green-circle tooltip1">
																					<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.ALL features selected!')}}</span>
																				</div>

																				<div style="visibility: hidden;" class="yellow-circle tooltip1">
																					<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.Select all features!')}}</span>
																				</div>

																				<span id="next-row-span" class="tooltip1 add-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
																				<i id="next-row-icon" class="fa fa-fw fa-plus"></i>
																				<span class="tooltiptext">{{__('text.Add')}}</span>
																			</span>

																				<span id="next-row-span" class="tooltip1 remove-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
																				<i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
																				<span class="tooltiptext">{{__('text.Remove')}}</span>
																			</span>

																				<span id="next-row-span" class="tooltip1 copy-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">
																				<i id="next-row-icon" class="fa fa-fw fa-copy"></i>
																				<span class="tooltiptext">{{__('text.Copy')}}</span>
																			</span>

																				<!--<span id="next-row-span" class="tooltip1 next-row" style="cursor: pointer;font-size: 20px;">
                                                                                <i id="next-row-icon" style="color: #868686;" class="fa fa-fw fa-chevron-right"></i>
                                                                                <span style="top: 45px;left: -20px;" class="tooltiptext">Next</span>
                                                                                </span>-->
																			</div>
																		</div>

																		<div class="item11" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;margin-top: 10px;">
																			<button style="outline: none;" type="button" class="btn btn-info res-collapse collapsed" data-toggle="collapse" data-target="#demo"></button>
																		</div>

																		<div style="width: 100%;" id="demo" class="item17 collapse">

																			<div style="width: 25%;margin-left: 10px;" class="ledger-box item14">

																				<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            						<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.General Ledger')}}</label>
																					
																				</div>

																				<select name="general_ledgers[]" class="general_ledger">
																					<option value="">{{__("text.Select General Ledger")}}</option>
																					@foreach($general_ledgers as $key)
																						<option value="{{$key->id}}">{{$key->number . " - " . $key->title}}</option>
																					@endforeach
																				</select>

																			</div>

																			<div style="width: 25%;margin-left: 10px;" class="vat-box item15">

																				<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            						<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.VAT')}}</label>
																					
																				</div>

																				<select name="vats[]" class="vat">
																					<option value="">{{__("text.Select VAT")}}</option>
																					@foreach($vats as $key)
																						<option {{$key->vat_percentage == 21 ? 'selected' : null}} data-percentage="{{$key->vat_percentage}}" value="{{$key->id}}">{{$key->vat_percentage . "$ - " . $key->rule}}</option>
																					@endforeach
																				</select>

																			</div>

																			<div style="width: 25%;margin-left: 10px;" class="discount-box item16">

																				<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">

                                                            						<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.Discount')}} <span class="discount-sign">%</span></label>

																					<div style="display: flex;align-items: center;">

																						<span style="font-size: 15px;padding-right: 10px;font-weight: 600;">%</span>
                                                            							
																						<label style="margin: 0;" class="switch">
                                                                							<input class="discount_option" name="discount_option[]" type="checkbox">
                                                                							<span class="slider round"></span>
                                                            							</label>
                                                            						
																						<span style="font-size: 15px;padding-left: 10px;">€</span>

																					</div>
																					
																				</div>

																				<input value="0" class="discount_option_values" name="discount_option_values[]" type="hidden">
																				<input maskedformat="9,1" style="height: 35px;border-radius: 4px;" placeholder="{{__('text.Enter discount in percentage')}}" type="text" class="form-control discount_values" value="0" name="discount[]">

																			</div>

																		</div>

																	</div>

																@endif

															</section>

															<div style="width: 100%;margin-top: 10px;">

																<div style="display: flex;justify-content: center;">

																	<div class="headings1" style="width: 70%;display: flex;flex-direction: column;align-items: flex-start;">

																		<button href="#myModal3" role="button" data-toggle="modal" style="font-size: 16px;" type="button" class="btn btn-success"><i class="fa fa-calendar-check-o" style="margin-right: 5px;"></i> {{__('text.Appointments')}}</button>

																		<!-- @if((isset($invoice) && !$invoice[0]->quote_request_id) || (isset($request_id) && !$request_id))

																		@endif -->

																	</div>
																	<div class="headings1" style="/*visibility: hidden;width: 23%;*/display: none;justify-content: flex-end;align-items: center;padding-right: 15px;"><span style="font-size: 14px;font-weight: bold;font-family: monospace;">{{__('text.Total')}}</span></div>
																	<div class="headings1" style="/*visibility: hidden;width: 7%;*/display: none;align-items: center;">
																		<div style="display: flex;align-items: center;justify-content: center;">
																			<span style="font-size: 14px;font-weight: 500;margin-right: 5px;">€</span>
																			<input name="price_before_labor_total"
																				   id="price_before_labor_total"
																				   style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;"
																				   type="text" readonly
																				   value="{{isset($invoice) ? number_format((float)$invoice[0]->price_before_labor_total, 2, ',', '.') : 0}}">
																		</div>
																	</div>

																	<div class="headings2" style="width: 30%;display: flex;align-items: center;">
																		<div style="display: flex;align-items: center;justify-content: flex-end;width: 100%;">
																			<span style="font-size: 14px;font-weight: 500;font-family: monospace;">Te betalen: €</span>
																			<input name="total_amount" id="total_amount"
																				   style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;text-align: right;"
																				   type="text" readonly
																				   value="{{isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($invoice[0]->grand_total * -1), 2, ',', '.') : number_format((float)$invoice[0]->grand_total, 2, ',', '.')) : 0}}">
																		</div>
																	</div>

																</div>

																<?php if(Route::currentRouteName() == 'create-new-negative-invoice' && isset($invoice) && !$invoice[0]->negative_invoice && $invoice[0]->taxes_json){
																	$tax_array = json_decode($invoice[0]->taxes_json,true);
																	foreach($tax_array as &$js){ $js["tax"] = $js["tax"] * -1; }
																	$invoice[0]->taxes_json = json_encode($tax_array);
																} ?>

																<div style="display: flex;justify-content: flex-end;margin-top: 20px;">

																	<div class="headings1" style="width: 70%;display: flex;flex-direction: column;align-items: flex-start;"></div>
																	<div class="headings1" style="/*width: 16%;*/display: none;align-items: center;"></div>
																	<div class="headings1" style="/*width: 7%;*/display: none;align-items: center;"></div>
																	<div class="headings1" style="/*width: 7%;*/display: none;align-items: center;"></div>
																	<div class="headings2" style="width: 30%;display: flex;align-items: center;">
																		<div style="display: flex;align-items: center;justify-content: flex-end;width: 100%;">
																			<span style="font-size: 14px;font-weight: 500;font-family: monospace;">Nettobedrag: €</span>
																			<input name="net_amount" id="net_amount"
																				   style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;text-align: right;"
																				   type="text" readonly
																				   value="{{isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($invoice[0]->net_amount * -1), 2, ',', '.') : number_format((float)$invoice[0]->net_amount, 2, ',', '.')) : 0}}">
																			<input value="{{isset($invoice) ? $invoice[0]->taxes_json : NULL}}" name="taxes_json" id="taxes_json" type="hidden">
																		</div>
																	</div>

																</div>

																<div id="tax_box" style="display: flex;justify-content: flex-end;margin-top: 20px;">

																	<div class="headings1" style="width: 70%;"></div>
																	<div class="headings2" style="width: 30%;">
																		<div style="display: flex;align-items: center;justify-content: flex-end;width: 100%;">
																			<span style="font-size: 14px;font-weight: 500;font-family: monospace;">BTW: €</span>
																			<input name="tax_amount" id="tax_amount"
																				   style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;text-align: right;"
																				   type="text" readonly
																				   value="{{isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? number_format((float)($invoice[0]->tax_amount * -1), 2, ',', '.') : number_format((float)$invoice[0]->tax_amount, 2, ',', '.')) : 0}}">
																		</div>
																	</div>

																</div>

																@if(isset($invoice) && $invoice[0]->taxes_json)

																	@foreach(json_decode($invoice[0]->taxes_json,true) as $js)

																		<?php if(isset($js['rows_total'])){ $ex_vat = ($js['percentage'] == 0 ? "verlegd over " : "over ") . number_format((float)($js['rows_total'] - $js['tax']), 2, ',', '.');  }else{ $ex_vat = ''; } ?>
																	
																		<div class="dynamic_tax_boxes" style="display: flex;justify-content: flex-end;margin-top: 20px;">
																			<div class="headings1" style="width: 70%;"></div>
																			<div class="headings2" style="width: 30%;">
																				<div style="display: flex;align-items: center;justify-content: flex-end;width: 100%;">
																					<span style="font-size: 14px;font-weight: 500;font-family: monospace;">BTW {{$js['percentage'] != 0 ? '('.$js['percentage'].'%) ' : ''}} {{$ex_vat}}:  €</span>
																					<input style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;text-align: right;" type="text" readonly="" value="{{number_format((float)$js['tax'], 2, ',', '')}}">
																				</div>
																			</div>
																		</div>

																	@endforeach

																@endif

																<div style="width: 90%;margin: auto;margin-top: 20px;">
																	
																	<input type="hidden" name="description" value="{{isset($invoice) ? $invoice[0]->description : ''}}">
                                            						<div class="quote_description">{!! isset($invoice) ? $invoice[0]->description : '' !!}</div>

																</div>

															</div>

														</div>

														<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: white;padding: 15px 0 0 0;">

															<ul style="border: 0;" class="nav nav-tabs feature-tab">

																<li style="margin-bottom: 0;" class="active"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu2" aria-expanded="false">{{__('text.Calculator')}}</a></li>

																<li style="margin-bottom: 0;display: none;"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu1" aria-expanded="false">{{__('text.Features')}}</a></li>

																<li style="margin-bottom: 0;"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu3" aria-expanded="false">{{__('text.General Terms')}}</a></li>

																<li style="margin-bottom: 0;"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu4" aria-expanded="false">{{__('text.Payment Calculator')}}</a></li>

															</ul>

															<div style="padding: 30px 15px 20px 15px;border: 0;border-top: 1px solid #24232329;" class="tab-content">

																<div id="menu1" class="tab-pane">

																	@if(isset($invoice))

																		<?php $f = 0; $s = 0; ?>

																		@foreach($invoice as $x => $key1)

																			<div data-id="{{$x + 1}}" @if($x==0) style="margin: 0;" @else style="margin: 0;display: none;" @endif class="form-group">

																				@if($key1->childsafe)

																					<div class="row childsafe-content-box" style="margin: 0;display: flex;align-items: center;">
																						<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																							<label style="margin-right: 10px;margin-bottom: 0;">Montagehoogte</label>
																							<input value="{{$key1->childsafe_x}}" style="border: none;border-bottom: 1px solid lightgrey;" type="number" class="form-control childsafe_values" id="childsafe_x" name="childsafe_x{{$x+1}}">
																						</div>
																					</div>

																					<div class="row childsafe-content-box1" style="margin: 0;display: flex;align-items: center;">
																						<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
																							<label style="margin-right: 10px;margin-bottom: 0;">Kettinglengte</label>
																							<input value="{{$key1->childsafe_y}}" style="border: none;border-bottom: 1px solid lightgrey;" type="number" class="form-control childsafe_values" id="childsafe_y" name="childsafe_y{{$x+1}}">
																						</div>
																					</div>

																					<div class="row childsafe-question-box" style="margin: 0;display: flex;align-items: center;">

																						<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">

																							<label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Childsafe')}}</label>

																							<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-select" name="childsafe_option{{$x+1}}">

																								<option value="">{{__('text.Select any option')}}</option>

																								@if($key1->childsafe_diff <= 150)

																									<option {{$key1->childsafe_question == 1 ? 'selected' : null}} value="1">{{__('text.Please note not childsafe')}}</option>
																									<option {{$key1->childsafe_question == 2 ? 'selected' : null}} value="2">{{__('text.Add childsafety clip')}}</option>

																								@else

																									<option {{$key1->childsafe_question == 2 ? 'selected' : null}} value="2">{{__('text.Add childsafety clip')}}</option>
																									<option {{$key1->childsafe_question == 3 ? 'selected' : null}} value="3">{{__('text.Yes childsafe')}}</option>

																								@endif

																							</select>

																							<input value="{{$key1->childsafe_diff}}" name="childsafe_diff{{$x + 1}}" class="childsafe_diff" type="hidden">

																						</div>

																					</div>

																					<div class="row childsafe-answer-box" style="margin: 0;display: flex;align-items: center;">

																						<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">

																							<label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Childsafe Answer')}}</label>
																							<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-answer" name="childsafe_answer{{$x+1}}">

																								@if($key1->childsafe_question == 1)

																									<option {{$key1->childsafe_answer == 1 ? 'selected' : null}} value="1">{{__('text.Make it childsafe')}}</option>
																									<option {{$key1->childsafe_answer == 2 ? 'selected' : null}} value="2">{{__('text.Yes i agree')}}</option>

																								@else

																									<option selected value="3">{{__('text.Is childsafe')}}</option>

																								@endif

																							</select>
																						</div>

																					</div>

																				@endif

																				@foreach($key1->features as $feature)

																					@if($feature->feature_id == 0 && $feature->feature_sub_id == 0)

																						<div class="row" style="margin: 0;display: flex;align-items: center;">

																							<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">

																								<label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Ladderband')}}</label>

																								<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features{{$x+1}}[]">

																									<option {{$feature->ladderband == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
																									<option {{$feature->ladderband == 1 ? 'selected' : null}} value="1">{{__('text.Yes')}}</option>

																								</select>

																								<input value="{{$feature->price}}" name="f_price{{$x + 1}}[]" class="f_price" type="hidden">
																								<input value="0" name="f_id{{$x + 1}}[]" class="f_id" type="hidden">
																								<input value="0" name="f_area{{$x + 1}}[]" class="f_area" type="hidden">
																								<input value="0" name="sub_feature{{$x + 1}}[]" class="sub_feature" type="hidden">

																							</div>


																							@if($feature->ladderband)

																								<a data-id="{{$x + 1}}" class="info ladderband-btn">{{__('text.Info')}}</a>

																							@endif

																						</div>

																					@else

																						<div class="row" style="margin: 0;display: flex;align-items: center;">

																							<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">

																								<label style="margin-right: 10px;margin-bottom: 0;">{{$feature->title}}</label>

																								<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features{{$x+1}}[]">

																									<option value="0">{{__('text.Select Feature')}}</option>

																									@foreach($features[$f] as $temp)

																										<option {{$temp->id == $feature->feature_sub_id ? 'selected' : null}} value="{{$temp->id}}">{{$temp->title}}</option>

																									@endforeach

																								</select>

																								<input value="{{$feature->price}}" name="f_price{{$x + 1}}[]" class="f_price" type="hidden">
																								<input value="{{$feature->feature_id}}" name="f_id{{$x + 1}}[]" class="f_id" type="hidden">
																								<input value="0" name="f_area{{$x + 1}}[]" class="f_area" type="hidden">
																								<input value="0" name="sub_feature{{$x + 1}}[]" class="sub_feature" type="hidden">

																							</div>

																							@if($feature->comment_box)

																								<a data-feature="{{$feature->feature_id}}" class="info comment-btn">{{__('text.Info')}}</a>

																							@endif

																						</div>

																						@foreach($key1->sub_features as $sub_feature)

																							@if($sub_feature->feature_id == $feature->feature_sub_id)

																								<div class="row sub-features" style="margin: 0;display: flex;align-items: center;">

																									<div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">

																										<label style="margin-right: 10px;margin-bottom: 0;">{{$sub_feature->title}}</label>

																										<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features{{$x+1}}[]">

																											<option value="0">{{__('text.Select Feature')}}</option>

																											@foreach($sub_features[$s] as $temp)

																												<option {{$temp->id == $sub_feature->feature_sub_id ? 'selected' : null}} value="{{$temp->id}}">{{$temp->title}}</option>

																											@endforeach

																										</select>

																										<input value="{{$sub_feature->price}}" name="f_price{{$x + 1}}[]" class="f_price" type="hidden">
																										<input value="{{$sub_feature->feature_id}}" name="f_id{{$x + 1}}[]" class="f_id" type="hidden">
																										<input value="0" name="f_area{{$x + 1}}[]" class="f_area" type="hidden">
																										<input value="1" name="sub_feature{{$x + 1}}[]" class="sub_feature" type="hidden">

																									</div>

																								</div>

																								<?php $s = $s + 1; ?>

																							@endif

																						@endforeach

																					@endif

																					<?php $f = $f + 1; ?>

																				@endforeach

																			</div>

																		@endforeach

																	@endif

																</div>

																<div id="menu2" class="tab-pane fade active in">

																	@include('user.calculator')

																</div>

																@if(Route::currentRouteName() == 'create-custom-quotation')

																	<div id="menu3" class="tab-pane">
																		<div class="form-group">
																			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
																				<input type="hidden" name="general_terms" value="{{$general_terms ? $general_terms->description : ''}}">
                                            									<div class="general_terms">{!! $general_terms ? $general_terms->description : '' !!}</div>
																			</div>
                                        								</div>
																	</div>

																@else

																	<div id="menu3" class="tab-pane">
																		<div class="form-group">
																			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
																				<input type="hidden" name="general_terms" value="{{$invoice[0]->general_terms}}">
                                            									<div class="general_terms">{!! $invoice[0]->general_terms !!}</div>
																			</div>
                                        								</div>
																	</div>

																@endif

																<div id="menu4" class="tab-pane">

																	@include("user.payment_calculations_section")

																</div>

															</div>

														</div>

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

					<div id="myModal" class="modal fade" role="dialog">
						<div class="modal-dialog">

							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">{{__('text.Sub Products Sizes')}}</h4>
								</div>
								<div class="modal-body">

									@if(isset($invoice))

										@foreach($invoice as $x => $key1)

											@if(isset($sub_products[$x]))

												<div class="sub-tables" data-id="{{$x+1}}">
													<table style="width: 100%;">
														<thead>
														<tr>
															<th>ID</th>
															<th>{{__('text.Title')}}</th>
															<th>{{__('text.Size 38mm')}}</th>
															<th>{{__('text.Size 25mm')}}</th>
														</tr>
														</thead>
														<tbody>

														@foreach($sub_products[$x] as $sub_product)

															<tr>
																<td><input type="hidden" class="sub_product_id"
																		   name="sub_product_id{{$x+1}}[]"
																		   value="{{$sub_product->sub_product_id}}">{{$sub_product->code}}
																</td>
																<td>{{$sub_product->title}}</td>
																<td>
																	@if($sub_product->size1_value == 'x')

																		X<input class="sizeA" name="sizeA{{$x+1}}[]" type="hidden"
																				value="x">

																	@else

																		<input {{$sub_product->size1_value ? 'checked' : null}}
																			   data-id="{{$x + 1}}" class="cus_radio" name="cus_radio{{$x+1}}[]"
																			   type="radio">
																		<input class="cus_value sizeA" type="hidden"
																			   value="{{$sub_product->size1_value ? 1 : 0}}"
																			   name="sizeA{{$x+1}}[]">

																	@endif
																</td>
																<td>
																	@if($sub_product->size2_value == 'x')

																		X<input class="sizeB" name="sizeB{{$x+1}}[]" type="hidden"
																				value="x">

																	@else

																		<input {{$sub_product->size2_value ? 'checked' : null}}
																			   data-id="{{$x + 1}}" class="cus_radio" name="cus_radio{{$x+1}}[]"
																			   type="radio">
																		<input class="cus_value sizeB" type="hidden"
																			   value="{{$sub_product->size2_value ? 1 : 0}}"
																			   name="sizeB{{$x+1}}[]">

																	@endif
																</td>
															</tr>

														@endforeach

														</tbody>
													</table>
												</div>

											@endif

										@endforeach

									@endif
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">{{__('text.Close')}}</button>
								</div>
							</div>

						</div>
					</div>

					<div id="myModal2" class="modal fade" role="dialog">
						<div class="modal-dialog">

							<!-- Modal content-->
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title">{{__('text.Feature Comment')}}</h4>
								</div>
								<div class="modal-body">

									@if(isset($invoice))

										@foreach($invoice as $x => $key1)

											@foreach($key1->features as $feature)

												@if($feature->comment)

													<div class="comment-boxes" data-id="{{$x + 1}}">
														<textarea style="resize: vertical;width: 100%;border: 1px solid #c9c9c9;border-radius: 5px;outline: none;" data-id="{{$feature->feature_id}}" rows="5" name="comment-{{$x + 1}}-{{$feature->feature_id}}">{{$feature->comment}}</textarea>
													</div>

												@endif

											@endforeach

										@endforeach

									@endif

								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">{{__('text.Close')}}</button>
								</div>
							</div>

						</div>
					</div>

				</form>

			</div>

		</div>

	</div>

	@include('user.send_quotation_modal')

	<div id="cover">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
	</div>

	<div style="overflow-y: auto;" id="myModal3" class="modal fade" role="dialog">
		<div style="width: 80%;" class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">{{__('text.Appointments')}}</h4>
				</div>
				<div style="padding: 30px 0;" class="modal-body">

					<ul class="nav nav-pills agenda-pills">
                        <li class="active">
                            <a href="#1a" data-toggle="tab">{{__("text.Agenda")}}</a>
                        </li>
                        <li><a href="#2a" data-toggle="tab">{{__("text.Checklist")}}</a></li>
                    </ul>

					<div style="border-top: 1px solid #cecece;" class="tab-content clearfix">
                        <div style="margin: 30px 0;" class="tab-pane active" id="1a">
							@include("user.plannings_widget")
                        </div>
                        <div class="tab-pane" id="2a">
                            @include('user.checklist')
                        </div>
                    </div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('text.Close')}}</button>
				</div>
			</div>

		</div>
	</div>

	@include("user.plannings_manager")
	@include("user.select_checklist_quotation")
	@include("user.document_info_modal")

	<div id="myModal4" class="modal fade" role="dialog">
		<div style="width: 100%;margin: 0;display: table;" class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button style="font-size: 45px;opacity: 0.7;" type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div style="text-align: center;" class="modal-body">

					<embed id="pdf-viewer" src="" width="90%" height="1200px" />

				</div>
				<!-- <div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('text.Close')}}</button>
				</div> -->
			</div>

		</div>
	</div>

	<style>

		#myModalLabel
		{
			margin: 0;
		}

		.document-info
		{
			display: flex;
			flex-direction: column;
			cursor: pointer;
		}

		.icon-container, .icon-container1 {
			position: absolute;
			top: 0;
			width: 100%;
			height: 100%;
			text-align: center;
			display: flex;
			align-items: center;
			background-color: white;
		}

		.icon-container1
		{
			position: relative;
			background-color: transparent;
			padding: 8px 0;
		}

		.loader
		{
			position: relative;
			width: 20px;
			height: 20px;
			display: inline-block;
			animation: around 5.4s infinite;
		}

		@keyframes around {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}

		.loader::after, .loader::before
		{
			content: "";
			background: white;
			position: absolute;
			display: inline-block;
			width: 100%;
			height: 100%;
			border-width: 2px;
			border-color: #333 #333 transparent transparent;
			border-style: solid;
			border-radius: 20px;
			box-sizing: border-box;
			top: 0;
			left: 0;
			animation: around 0.7s ease-in-out infinite;
		}

		.icon-container1 .loader::after, .loader::before
		{
			background: transparent;
			border-color: #5087cc #fff #ec1919 #60cb53;
		}

		.loader::after
		{
			animation: around 0.7s ease-in-out 0.1s infinite;
			background: transparent;
		}

		.agenda-pills li
        {
            border: 1px solid #cecece;
            border-bottom: 0;
            margin-right: -3px;
        }

        .agenda-pills>li.active
        {
            background-color: #404040;
        }

        .agenda-pills>li>a
        {
            border-radius: 0;
            padding: 5px 30px;
        }

        .agenda-pills>li.active>a, .agenda-pills>li.active>a:focus, .agenda-pills>li.active>a:hover
        {
            background-color: transparent;
        }

        .agenda-pills a::before
        {
            display: none;
        }

		.intro-text {
			font-size: 15px;
			font-weight: 600;
		}
	
		.pis_title {
			color: #8585d3;
			display: block;
			min-width: 40%;
			/* border: 1px solid; */
			border-radius: 10px;
			padding: 0 10px;
			margin: 10px 0;
			cursor: pointer;
			font-size: 12px;
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

		#myModal4
		{
			padding: 0 !important;
		}

		.switch
		{
			width: 60px;
			height: 25px;
		}

		.slider:before
		{
			height: 16px;
			width: 16px;
		}

		input:checked + .slider:before
		{
			-webkit-transform: translateX(33px);
			-ms-transform: translateX(33px);
			transform: translateX(33px);
		}

		.bootstrap-tagsinput
		{
			width: 100%;
		}

		#calendar {
			width: 90%;
			margin: 0 auto;
		}

		.fc-event:hover .fc-buttons
		{
			display: block;
		}

		.fc-event .fc-buttons
		{
			padding: 10px;
			text-align: center;
			display: none;
			position: absolute;
			background-color: #ffffff;
			border: 1px solid #d7d7d7;
			bottom: 100%;
			z-index: 99999;
			min-width: 80px;
		}

		.fc-event .fc-buttons:after,
		.fc-event .fc-buttons:before {
			top: 100%;
			left: 8px;
			border: solid transparent;
			content: " ";
			height: 0;
			width: 0;
			position: absolute;
			pointer-events: none;
		}

		.fc-event .fc-buttons:before {
			border-color: rgba(119, 119, 119, 0);
			border-top-color: #d7d7d7;
			border-width: 6px;
			margin-left: -6px;
		}

		.fc-event .fc-buttons:after {
			border-color: rgba(255, 255, 255, 0);
			border-top-color: #ffffff;
			border-width: 5px;
			margin-left: -5px;
		}

		.fc table
		{
			margin: 0 !important;
		}

		.fc .fc-scrollgrid-section-liquid > td, .fc .fc-scrollgrid-section > td, .fc-theme-standard td, .fc-theme-standard th
		{
			padding: 0 !important;
		}

		.fc .fc-scrollgrid-section-liquid > td:first-child
		{
			border-right: 1px solid var(--fc-border-color, #ddd);
		}

		.fc-col-header-cell, .fc-day, .fc-day-sun
		{
			border-bottom: 0 !important;
		}

		.fc-scrollgrid-section > th, .fc-scrollgrid-section-header > th, .fc-scrollgrid, .fc-scrollgrid-liquid
		{
			border: 0 !important;
		}

		.autocomplete ::-webkit-input-placeholder {
			text-align: center;
		}

		.autocomplete :-moz-placeholder { /* Firefox 18- */
			text-align: center;
		}

		.autocomplete ::-moz-placeholder {  /* Firefox 19+ */
			text-align: center;
		}

		.autocomplete :-ms-input-placeholder {
			text-align: center;
		}

		.autocomplete {
			position: relative;
			display: inline-block;
		}

		.quote-product {
			border: 0;
			padding: 15px;
			width: 100%;
		}

		.autocomplete-items {
			position: absolute;
			border: 1px solid #d4d4d4;
			/* border-bottom: none;
            border-top: none; */
			z-index: 99;
			/*position the autocomplete items to be the same width as the container:*/
			top: 100%;
			left: 0;
			right: 0;
			max-height: 230px;
			overflow-x: hidden;
			overflow-y: auto;
		}

		.autocomplete-items div {
			padding: 10px;
			cursor: pointer;
			background-color: #fff;
			border-bottom: 1px solid #d4d4d4;
		}

		.autocomplete-items div:last-child
		{
			border-bottom: 0;
		}

		/*when hovering an item:*/
		.autocomplete-items div:hover {
			background-color: #e9e9e9;
		}

		/*when navigating through the items using the arrow keys:*/
		.autocomplete-active {
			background-color: DodgerBlue !important;
			color: #ffffff;
		}

		.res-collapse
		{
			box-shadow: none !important;
			border: 0;
			background: white !important;
			color: black !important;
			padding: 0;
		}

		button.btn.collapsed:before
		{
			content: 'Toon alle velden' ;
			display: block;
		}

		button.res-collapse:before
		{
			content: 'Toon minder velden' ;
			display: block;
		}

		.item1 { grid-area: item1; }
		.item2 { grid-area: item2; }
		.item3 { grid-area: item3; }
		.item4 { grid-area: item4; }
		.item5 { grid-area: item5; }
		.item6 { grid-area: item6; }
		.item7 { grid-area: item7; }
		.item8 { grid-area: item8; }
		.item9 { grid-area: item9; }
		.item10 { grid-area: item10; }
		.item11 { grid-area: item11; }
		.item12 { grid-area: item12; }
		.item13 { grid-area: item13; }
		.item14 { grid-area: item14; }
		.item15 { grid-area: item15; }
		.item16 { grid-area: item16; }
		.item17 { grid-area: item17; }

		.content-label
		{
			display: none;
		}

		.m-input,
		.labor_impact {
			border-radius: 5px !important;
			width: 70%;
			border: 0;
			padding: 0 5px;
			text-align: left;
			height: 30px !important;
		}

		.m-input:focus,
		.labor_impact:focus {
			background: #f6f6f6;
		}

		.measure-unit {
			width: 50%;
		}

		.add-product-box hr
		{
			margin-bottom: 20px;
		}

		@media (max-width: 992px)
		{

			.headings1
			{
				width: 25% !important;
			}

			.headings1 input
			{
				width: 40% !important;
			}

			.headings2
			{
				width: 100% !important;
			}

			.headings2 div
			{
				width: 100% !important;
			}

			.headings2 input
			{
				width: 28% !important;
			}

			.add-product-box hr
			{
				margin-top: 0;
			}

			.header-div
			{
				display: none !important;
			}

			.price
			{
				padding: 0 5px;
				display: flex;
				align-items: center;
			}

			.content-div
			{
				display: grid !important;
				grid-template-areas:'item1 item1 item1 item1 item1 item1'
				'item2 item2 item2 item2 item2 item2'
				'item3 item3 item3 item3 item3 item3'
				'item17 item17 item17 item17 item17 item17'
				'item12 item12 item12 item12 item12 item12'
				'item13 item13 item13 item13 item13 item13'
				'item14 item14 item14 item14 item14 item14'
				'item15 item15 item15 item15 item15 item15'
				'item16 item16 item16 item16 item16 item16'
				'item4 item4 item4 item5 item5 item5'
				'item6 item6 item6 item6 item6 item6'
				'item7 item7 item7 item7 item7 item7'
				'item8 item8 item8 item8 item8 item8'
				'item9 item9 item9 item9 item9 item9'
				'item10 item10 item10 item10 item10 item10'
				'item11 item11 item11 item11 item11 item11';
				grid-column-gap: 10px;
				/*grid-gap: 10px;*/
				padding: 20px !important;
				border: 1px solid #d0d0d0 !important;
				border-radius: 5px;
			}

			.vat-box .select2-container--default .select2-selection--single, .ledger-box .select2-container--default .select2-selection--single, .color .select2-container--default .select2-selection--single, .model .select2-container--default .select2-selection--single
			{
				border: 1px solid #d6d6d6 !important;
			}

			.m-box
			{
				border: 1px solid #d6d6d6;
				border-radius: 4px;
				padding: 0 10px;
				background: white;
			}

			.content-div .collapse, .content-div .collapsing, .content-div .collapse.in
			{
				display: grid !important;
				grid-template-areas: 'item12 item12 item12 item12 item12 item12'
				'item13 item13 item13 item13 item13 item13'
				'item14 item14 item14 item14 item14 item14'
				'item15 item15 item15 item15 item15 item15';
				margin-top: 0 !important;
			}

			.color, .model, .discount-box, .labor-discount-box
			{
				width: auto !important;
				margin-left: 0 !important;
				margin-top: 15px;
			}

			.content-div.active
			{
				background: #c6daef;
				border: 0 !important;
			}

			.second-row
			{
				padding: 0 !important;
			}

			.content-div .content
			{
				border: 0 !important;
				display: block !important;
				height: auto !important;
				width: auto !important;
			}

			.content-div .content:not(:first-child)
			{
				margin-top: 15px;
			}

			.res-white
			{
				background: white !important;
				height: 35px !important;
				width: 100% !important;
				border-radius: 4px !important;
				border: 1px solid #d6d6d6 !important;
			}

			.item11
			{
				display: none !important;
			}

			.m-input
			{
				border-radius: 0 !important;
				width: 75%;
			}

			.measure-unit
			{
				height: 30px;
				width: 25%;
				padding-bottom: 3px;
				border-radius: 0;
			}

			.full-res .select2-container .select2-selection--single, .full-res .select2-container--default .select2-selection--single .select2-selection__rendered, .full-res .select2-container--default .select2-selection--single .select2-selection__arrow
			{
				height: 35px;
				line-height: 35px;
				font-size: 10px;
			}

			:is(.color, .model) > .select2-container--default .select2-selection--single, :is(.color, .model) > .select2-container--default .select2-selection--single .select2-selection__rendered, :is(.color, .model) > .select2-container--default .select2-selection--single .select2-selection__arrow, :is(.color, .model) > .select2-container--default .select2-selection--single .select2-selection__rendered
			{
				font-size: 10px;
			}

			.sr-res
			{
				background: white;
				height: 35px;
				display: flex;
				align-items: center;
				border-radius: 4px;
				border: 1px solid #d6d6d6;
			}

			:not(.checklist_quotations_box, .vat-box, .ledger-box, .color, .model, .appointment_title_box, .appointment_status_box, .appointment_quotation_number_box, .appointment_type_box, .appointment_customer_box .c_box, .appointment_supplier_box, .appointment_employee_box, .responsible_box, .filter_responsible_box) > .select2-container--default .select2-selection--single
			{
				border: 1px solid #d6d6d6 !important;
			}

			.content-label
			{
				display: inline-block;
			}
		}

		.content-div .collapsing, .content-div .collapse.in
		{
			display: flex;
		}

		#menu2 .attributes_table
		{
			display: none;
		}

		#menu2 .attributes_table.active
		{
			display: block;
		}

		.header-div, .content-div, .attribute-content-div, .pc-content-div
		{
			display: flex;
			flex-direction: row;
			align-items: stretch;
		}

		.header-div .headings
		{
			font-family: system-ui;
			font-weight: 500;
			border-bottom: 1px solid #ebebeb;
			padding-bottom: 15px;
			color: gray;
			height: 40px;
		}

		.content-div, .attribute-content-div, .pc-content-div
		{
			margin-top: 15px;
			flex-flow: wrap;
			border-bottom: 1px solid #d0d0d0;
			padding-bottom: 10px;
		}

		.content-div .content {
			font-family: system-ui;
			font-weight: 500;
			padding: 0;
			color: #3c3c3c;
			height: auto;
			display: flex;
			align-items: center;
		}

		.content-div.active .content {
			border-top: 2px solid #cecece;
			border-bottom: 2px solid #cecece;
		}

		.content-div.active .content:first-child {
			border-left: 2px solid #cecece;
			border-bottom-left-radius: 4px;
			border-top-left-radius: 4px;
		}

		.content-div.active .last-content {
			border-right: 2px solid #cecece;
			border-bottom-right-radius: 4px;
			border-top-right-radius: 4px;
		}

		.yellow-circle
		{
			background: #fae91a;width: 20px;height: 20px;border-radius: 50%;animation: yellow-glow 2s ease infinite;
		}

		@keyframes yellow-glow {
			0% {
				box-shadow: 0 0 #fae91a;
			}

			100% {
				box-shadow: 0 0 10px 8px transparent;
			}
		}

		.green-circle
		{
			background: #62e660;width: 20px;height: 20px;border-radius: 50%;animation: green-glow 2s ease infinite;
		}

		@keyframes green-glow {
			0% {
				box-shadow: 0 0 #62e660;
			}

			100% {
				box-shadow: 0 0 10px 8px transparent;
			}
		}

		/*.yellow-circle
        {
            background: #fae91a;width: 20px;height: 20px;border-radius: 50%;animation: anim-glow 2s linear infinite;
        }

        @keyframes anim-glow {
            0% {
                box-shadow: 0 0 9px 0px #ffec00;
            }
            25% {
                box-shadow: 0 0 5px 0px #ffec00;
            }
            50% {
                box-shadow: 0 0 0px 0px #ffec00;
            }
            75% {
                box-shadow: 0 0 5px 0px #ffec00;
            }
            100% {
                box-shadow: 0 0 9px 0px #ffec00;
            }
        }*/

		.note-editor
		{
			width: 100%;
		}

		.note-toolbar
		{
			line-height: 1;
		}

		#menu1 .form-group {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
		}

		#menu1 .form-group .row {
			padding: 0 20px;
			justify-content: flex-start;
			border-right: 1px solid #dddddd;
			height: 40px;
			width: 33%;
			margin: 15px 0 !important;
		}

		#menu1 .form-group .row:nth-child(3n + 1) {
			padding-left: 0;
		}

		#menu1 .form-group .row:nth-child(3n) {
			border-right: 0;
			padding-right: 0;
		}

		@media (max-width: 992px) {
			#menu1 .form-group .row {
				width: 50%;
			}

			#menu1 .form-group .row:nth-child(3n + 1) {
				padding-left: 20px;
			}

			#menu1 .form-group .row:nth-child(3n) {
				border-right: 1px solid #dddddd;
				padding-right: 20px;
			}

			#menu1 .form-group .row:nth-child(2n + 1) {
				padding-left: 0;
			}

			#menu1 .form-group .row:nth-child(2n) {
				border-right: 0;
				padding-right: 0;
			}

		}

		@media (max-width: 670px) {
			#menu1 .form-group .row {
				width: 100%;
			}

			#menu1 .form-group .row {
				border-right: 0 !important;
				padding-left: 20px !important;
				padding-right: 20px !important;
			}

		}

		@media (max-width: 550px) {

			.add-product-header .col-md-5 {
				padding: 0;
				margin-top: 20px;
				width: 100%;
			}
		}

		.swal2-html-container {
			line-height: 2;
		}

		a.info {
			vertical-align: bottom;
			position: relative;
			/* Anything but static */
			width: 1.5em;
			height: 1.5em;
			text-indent: -9999em;
			display: inline-block;
			color: white;
			font-weight: bold;
			font-size: 1em;
			line-height: 1em;
			background-color: #628cb6;
			cursor: pointer;
			margin-top: 7px;
			-webkit-border-radius: .75em;
			-moz-border-radius: .75em;
			border-radius: .75em;
		}

		a.info:before {
			content: "i";
			position: absolute;
			top: .25em;
			left: 0;
			text-indent: 0;
			display: block;
			width: 1.5em;
			text-align: center;
			font-family: monospace;
		}

		.ladderband-btn {
			background-color: #494949 !important;
		}

		.childsafe-btn {
			background-color: #56a63c !important;
		}

		/*.select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 25px;
        }*/

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

		.checklist_quotations_box .select2-container--default .select2-selection--single .select2-selection__rendered, #cus-box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_title_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_status_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_quotation_number_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_type_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_customer_box .c_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_supplier_box .select2-container--default .select2-selection--single .select2-selection__rendered, .appointment_employee_box .select2-container--default .select2-selection--single .select2-selection__rendered, .responsible_box .select2-container--default .select2-selection--single .select2-selection__rendered, .filter_responsible_box .select2-container--default .select2-selection--single .select2-selection__rendered {
			line-height: 28px;
		}

		.checklist_quotations_box .select2-container--default .select2-selection--single, .vat-box .select2-container--default .select2-selection--single, .ledger-box .select2-container--default .select2-selection--single, #cus-box .select2-container--default .select2-selection--single, .appointment_title_box .select2-container--default .select2-selection--single, .appointment_status_box .select2-container--default .select2-selection--single, .appointment_quotation_number_box .select2-container--default .select2-selection--single, .appointment_type_box .select2-container--default .select2-selection--single, .appointment_customer_box .c_box .select2-container--default .select2-selection--single, .appointment_supplier_box .select2-container--default .select2-selection--single, .appointment_employee_box .select2-container--default .select2-selection--single, .responsible_box .select2-container--default .select2-selection--single, .filter_responsible_box .select2-container--default .select2-selection--single {
			border: 1px solid #cacaca;
		}

		.checklist_quotations_box .select2-selection, #cus-box .select2-selection, .appointment_title_box .select2-selection, .appointment_status_box .select2-selection, .responsible_box .select2-selection, .filter_responsible_box .select2-selection {
			height: 40px !important;
			padding-top: 5px !important;
			outline: none;
		}

		.appointment_quotation_number_box .select2-selection, .appointment_type_box .select2-selection, .appointment_customer_box .c_box .select2-selection, .appointment_supplier_box .select2-selection, .appointment_employee_box .select2-selection
		{
			height: 35px !important;
			padding-top: 0 !important;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.checklist_quotations_box .select2-selection__arrow, #cus-box .select2-selection__arrow, .appointment_title_box .select2-selection__arrow, .appointment_status_box .select2-selection__arrow, .responsible_box .select2-selection__arrow, .filter_responsible_box .select2-selection__arrow {
			top: 7.5px !important;
		}

		.appointment_quotation_number_box .select2-selection__arrow, .appointment_type_box .select2-selection__arrow, .appointment_customer_box .c_box .select2-selection__arrow, .appointment_supplier_box .select2-selection__arrow, .appointment_employee_box .select2-selection__arrow
		{
			top: 0 !important;
			position: relative;
			height: 100% !important;
		}

		.appointment_start, .appointment_end
		{
			background-color: white !important;
		}

		/* #cus-box .select2-selection__clear, .appointment_title_box .select2-selection__clear, .appointment_status_box .select2-selection__clear, .appointment_quotation_number_box .select2-selection__clear, .appointment_type_box .select2-selection__clear, .appointment_customer_box .c_box .select2-selection__clear, .appointment_supplier_box .select2-selection__clear, .appointment_employee_box .select2-selection__clear {
			display: none;
		} */

		.feature-tab li a[aria-expanded="false"]::before,
		a[aria-expanded="true"]::before {
			display: none;
		}

		.feature-tab .active > a
		{
			border-bottom: 3px solid rgb(151, 140, 135) !important;
		}

		.m-box {
			display: flex;
			align-items: center;
			height: 100%;
		}

		:not(.checklist_quotations_box, .vat-box, .ledger-box, .color, .model, .appointment_title_box, .appointment_status_box, .appointment_quotation_number_box, .appointment_type_box, .appointment_customer_box .c_box, .appointment_supplier_box, .appointment_employee_box, .responsible_box, .filter_responsible_box) > .select2-container--default .select2-selection--single {
			border: 0;
		}

		:is(.vat-box, .ledger-box, .color, .model) > .select2-container--default .select2-selection--single, :is(.vat-box, .ledger-box, .color, .model) > .select2-container--default .select2-selection--single .select2-selection__rendered, :is(.vat-box, .ledger-box, .color, .model) > .select2-container--default .select2-selection--single .select2-selection__arrow, :is(.vat-box, .ledger-box, .color, .model) > .select2-container--default .select2-selection--single .select2-selection__rendered
		{
			line-height: 35px;
			height: 35px;
		}

		.tooltip1 {
			position: relative;
			display: inline-block;
			cursor: pointer;
			font-size: 20px;
		}

		/* Tooltip text */
		.tooltip1 .tooltiptext {
			visibility: hidden;
			width: auto;
			min-width: 60px;
			background-color: #7e7e7e;
			color: #fff;
			text-align: center;
			padding: 10px;
			border-radius: 6px;
			position: absolute;
			z-index: 1;
			left: 0;
			top: 55px;
			font-size: 12px;
			white-space: nowrap;
		}

		/* Show the tooltip text when you mouse over the tooltip container */
		.tooltip1:hover .tooltiptext {
			visibility: visible;
		}

		.first-row {
			flex-direction: row;
			box-sizing: border-box;
			display: flex;
			background-color: rgb(151, 140, 135);
			height: 50px;
			color: white;
			font-size: 13px;
			align-items: center;
			white-space: nowrap;
			justify-content: space-between;
		}

		.second-row {
			padding: 25px;
			display: flex;
			flex-direction: column;
			background: #fff;
			/*overflow-y: hidden;
            overflow-x: auto;*/
		}

		table tr th:not(#myModal3 #checklist_table tr th, #addAppointmentModal table tr th, .pc-content-div table tr th, #infoModal table tr th) {
			font-family: system-ui;
			font-weight: 500;
			border-bottom: 1px solid #ebebeb;
			padding-bottom: 15px;
			color: gray;
		}

		table tbody tr td:not(#myModal3 #checklist_table tbody tr td, #addAppointmentModal table tbody tr td, .pc-content-div table tbody tr td, #infoModal table tbody tr td) {
			font-family: system-ui;
			font-weight: 500;
			padding: 0 10px;
			color: #3c3c3c;
		}

		table tbody tr.active td:not(#myModal3 #checklist_table tbody tr.active td, #addAppointmentModal table tbody tr.active td, .pc-content-div table tbody tr.active td, #infoModal table tbody tr.active td) {
			border-top: 2px solid #cecece;
			border-bottom: 2px solid #cecece;
		}

		table tbody tr.active td:first-child:not(#myModal3 #checklist_table tbody tr.active td:first-child, #addAppointmentModal table tbody tr.active td:first-child, .pc-content-div table tbody tr.active td:first-child, #infoModal table tbody tr.active td:first-child) {
			border-left: 2px solid #cecece;
			border-bottom-left-radius: 4px;
			border-top-left-radius: 4px;
		}

		table tbody tr.active td:last-child:not(#myModal3 #checklist_table tbody tr.active td:last-child, #addAppointmentModal table tbody tr.active td:last-child, .pc-content-div table tbody tr.active td:last-child, #infoModal table tbody tr.active td:last-child) {
			border-right: 2px solid #cecece;
			border-bottom-right-radius: 4px;
			border-top-right-radius: 4px;
		}

		table:not(#myModal3 #checklist_table, #myModal3 .bootstrap-datetimepicker-widget table, #addAppointmentModal table, .pc-content-div table, #infoModal table) {
			border-collapse: separate;
			border-spacing: 0 1em;
		}

		.modal-body table tr th:not(#myModal3 .modal-body #checklist_table tr th, #addAppointmentModal .modal-body table tr th, .pc-content-div .modal-body table tr th, #infoModal .modal-body table tr th) {
			border: 1px solid #ebebeb;
			padding-bottom: 15px;
			color: gray;
		}

		.modal-body table tbody tr td:not(#myModal3 .modal-body #checklist_table tbody tr td, #addAppointmentModal .modal-body table tbody tr td, .pc-content-div .modal-body table tbody tr td, #infoModal .modal-body table tbody tr td) {
			border-left: 1px solid #ebebeb;
			border-right: 1px solid #ebebeb;
			border-bottom: 1px solid #ebebeb;
		}

		.modal-body table tbody tr td:first-child:not(#myModal3 .modal-body #checklist_table tbody tr td:first-child, #addAppointmentModal .modal-body table tbody tr td:first-child, .pc-content-div .modal-body table tbody tr td:first-child, #infoModal .modal-body table tbody tr td:first-child) {
			border-right: 0;
		}

		.modal-body table tbody tr td:last-child:not(#myModal3 .modal-body #checklist_table tbody tr td:last-child, #addAppointmentModal .modal-body table tbody tr td:last-child, .pc-content-div .modal-body table tbody tr td:last-child, #infoModal .modal-body table tbody tr td:last-child) {
			border-left: 0;
		}

		.modal-body table:not(#myModal3 .modal-body #checklist_table, #myModal3 .bootstrap-datetimepicker-widget table, #addAppointmentModal .modal-body table, .pc-content-div .modal-body table, #infoModal .modal-body table) {
			border-collapse: separate;
			border-spacing: 0;
			margin: 20px 0;
		}

		.modal-body table tbody tr td:not(#myModal3 .modal-body #checklist_table tbody tr td, #addAppointmentModal .modal-body table tbody tr td, .pc-content-div .modal-body table tbody tr td, #infoModal .modal-body table tbody tr td),
		.modal-body table thead tr th:not(#myModal3 .modal-body #checklist_table thead tr th, #addAppointmentModal .modal-body table thead tr th, .pc-content-div .modal-body table thead tr th, #infoModal .modal-body table thead tr th) {
			padding: 5px 10px;
		}

		.bootstrap-datetimepicker-widget .row:first-child
		{
			display: flex;
			align-items: center;
		}

	</style>

	@include("user.modals_css")

@endsection

@section('scripts')

	@include("user.modals_js")
	@include('user.checklist_js')

	<script src="{{asset('assets/front/js/plannings.js?v=2.0')}}"></script>

	<script type="text/javascript">

		function ShowPDF()
		{
			// if(auto_save_flag === 0)
			// {
			// 	$('#cover').css('display', 'flex');
			// 	window.setTimeout(ShowPDF, 100); /* this checks the flag every 100 milliseconds*/
			// }
			// else
			// {
			// 	$('#cover').css('display', 'none');
			// }

			var quotation_id = $('input[name="quotation_id"]').val();
			var is_invoice = $('input[name="is_invoice"]').val();
			var is_negative_invoice = $('input[name="negative_invoice"]').val();

			if(is_negative_invoice == 1)
			{
				quotation_id = $('input[name="negative_invoice_id"]').val();
			}

			if(quotation_id)
			{
				var url = is_invoice == 1 ? (is_negative_invoice == 1 ? "<?php echo url('/aanbieder/show-negative-invoice-pdf') ?>" + "/" + quotation_id : "<?php echo url('/aanbieder/show-invoice-pdf') ?>" + "/" + quotation_id) : "<?php echo url('/aanbieder/show-quotation-pdf') ?>" + "/" + quotation_id;

				$.ajax({
					type: "GET",
					url: url,
					beforeSend: function(){
						$('#cover').css('display', 'flex');
					},
					success: function (data) {
					
						if(data)
						{
							document.getElementById("pdf-viewer").src = data;
							$('#myModal4').modal('toggle');
						}
				
					},
					complete: function(){
						
					}
				});
			}
		}

		$(document).ajaxStop(function () {
			if(!calendar_loading)
			{
				$('#cover').css('display', 'none');
			}
		});

		$(".show-pdf").click(function () {

			ShowPDF();

		});

		$('#myModal3').on('shown.bs.modal', function () {

			$('.agenda-pills a[href="#1a"]').tab('show');
			// $('body').addClass('modal-open');
			calendar.refetchEvents();

		});

		var debounceTimeout;
		var calendar_loading = false;

		$(document).ready(function () {

			$(window).on("load", function () {
				AutoSave();
			});

			$('#form-quote').on('change', 'input, select, textarea', function(){
				// auto_save_flag = 0;
				// setTimeout(function(){AutoSave()},1000);
				clearTimeout(debounceTimeout);
    			debounceTimeout = setTimeout(function() {
					AutoSave();
				}, 2000); // Autosave after 2 second of inactivity
			}).on('focus', 'input, select, textarea', function(e) {
				$(this).closest('.content-div').attr("draggable", false);
			}).on('blur', 'input, select, textarea', function(e) {
				$(this).closest('.content-div').attr("draggable", true);
			});

			$(document).on('focus', "input[name='qty[]'], input[name='price_before_labor[]'], .total_boxes, .discount_values, .pc_percentage, .pc_amount, .width, .height", function (e) {
				$(this).select();
			});

			$('.save-data').on('click', function(){
				clearTimeout(debounceTimeout);
    			debounceTimeout = setTimeout(function() {
					AutoSave();
				}, 2000); // Autosave after 2 second of inactivity
			});

			function drag_rows_add_events(item)
			{
				$(item).prop('draggable', true);
				item.addEventListener('dragstart', dragStart);
				item.addEventListener('drop', dropped);
				item.addEventListener('dragenter', cancelDefault);
				item.addEventListener('dragover', cancelDefault);
			}

			let items = document.querySelectorAll('#products_table > .content-div');
			
			items.forEach(item => {
				drag_rows_add_events(item);
			});
			
			function dragStart (e) {
				var index = $(e.target).index();
				e.dataTransfer.setData('text/plain', index);
			}
			
			function dropped (e) {
				cancelDefault(e);
  
				// get new and old index
  				let oldIndex = e.dataTransfer.getData('text/plain');
				let target = $(e.target).parents(".content-div");
				let newIndex = target.index();

				if (newIndex >= 0 && (newIndex != oldIndex))
				{
					// remove dropped items at old place
					let dropped = $(this).parent().children().eq(oldIndex).remove();

					// insert the dropped items at new place
					if (newIndex < oldIndex) {
						target.before(dropped);
					} else {
						target.after(dropped);
					}

					numbering();
					clearTimeout(debounceTimeout);
    				debounceTimeout = setTimeout(function() {
						AutoSave();
					}, 2000); // Autosave after 2 second of inactivity
				}
			}

			function cancelDefault (e) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			}

			$('.quote_description').summernote({
				placeholder: '{{__('text.Description...')}}',
				dialogsInBody: true,
            	toolbar: [
                	// ['style', ['style']],
                	// ['style', ['bold', 'italic', 'underline', 'clear']],
                	// ['fontsize', ['fontsize']],
                	// /*['color', ['color']],*/
                	// ['fontname', ['fontname']],
                	// ['forecolor', ['forecolor']],
                	// ['backcolor', ['backcolor']],
                	// ['para', ['ul', 'ol', 'paragraph']],
                	// ['table', ['table']],
                	// ['view', ['fullscreen', 'codeview']],
                	['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen']],
            	],
            	height: 200,   //set editable area's height
            	codemirror: { // codemirror options
                	theme: 'monokai'
            	},
            	callbacks: {
                	onChange: function(contents, $editable) {
                    	$(this).prev('input').val(contents);
						clearTimeout(debounceTimeout);
						debounceTimeout = setTimeout(function() {
							AutoSave();
						}, 2000); // Autosave after 2 second of inactivity
                	}
            	}
        	});

			$('.general_terms').summernote({
				dialogsInBody: true,
            	toolbar: [
                	// ['style', ['style']],
                	// ['style', ['bold', 'italic', 'underline', 'clear']],
                	// ['fontsize', ['fontsize']],
                	// /*['color', ['color']],*/
                	// ['fontname', ['fontname']],
                	// ['forecolor', ['forecolor']],
                	// ['backcolor', ['backcolor']],
                	// ['para', ['ul', 'ol', 'paragraph']],
                	// ['table', ['table']],
                	// ['view', ['fullscreen', 'codeview']],
					['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen']],
            	],
            	height: 300,   //set editable area's height
            	codemirror: { // codemirror options
                	theme: 'monokai'
            	},
            	callbacks: {
                	onChange: function(contents, $editable) {
                    	$(this).prev('input').val(contents);
						clearTimeout(debounceTimeout);
						debounceTimeout = setTimeout(function() {
							AutoSave();
						}, 2000); // Autosave after 2 second of inactivity
                	}
            	}
        	});

			if($('#request_quotation').val() == 1)
			{
				var current = $(".quote-product");
				var product_id = current.parents(".content-div").find('#product_id').val();
				var model_id = current.parents(".content-div").find('#model_id').val();
				var color_id = current.parents(".content-div").find('#color_id').val();
				var supplier_id = current.parents(".content-div").find('#supplier_id').val();
				var secondary_title = current.parents(".content-div").find('#secondary_title').val();
				var row_id = current.parents(".content-div").data('id');
				var measure = '';
				var box_quantity = '';
				var max_width = '';
				var quote_qty = current.parents(".content-div").find('.qty').val();

				select_product(current,product_id,model_id,color_id,supplier_id,row_id,measure,box_quantity,max_width,quote_qty,secondary_title);
			}

			$(".customer-select").select2({
				width: '100%',
				height: '200px',
				placeholder: "{{__('text.Select Customer')}}",
				allowClear: true,
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

			$(".general_ledger").select2({
				width: '100%',
				height: '200px',
				placeholder: "{{__('text.Select General Ledger')}}",
				allowClear: true,
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

			$(".vat").select2({
				width: '100%',
				height: '200px',
				placeholder: "{{__('text.Select VAT')}}",
				allowClear: true,
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

			$(document).on('change', ".discount_option", function (e) {

				if($(this).is(":checked"))
				{
					$(this).parents('.discount-box').find('.discount-sign').text('€');
					$(this).parents('.discount-box').find('.discount_option_values').val(1);
				}
				else
				{
					$(this).parents('.discount-box').find('.discount-sign').text('%');
					$(this).parents('.discount-box').find('.discount_option_values').val(0);
				}

				calculate_total();

			});

			function focus_row(last_row) {

				var windowsize = $(window).width();

				if (windowsize > 992) {

					$('#products_table .content-div').not(last_row).find('.collapse[aria-expanded]').collapse("hide");

				}

				$('#products_table .content-div.active').removeClass('active');
				last_row.addClass('active');

				var id = last_row.data('id');

				$('#menu1').children().not(`[data-id='${id}']`).hide();
				$('#menu1').find(`[data-id='${id}']`).show();
				$('#menu2').children().not(`.attributes_table[data-id='${id}']`).removeClass('active');
				$('#menu2').find(`.attributes_table[data-id='${id}']`).addClass('active');

			}

			function numbering() {
				$('#products_table .content-div').each(function (index, tr) { $(this).find('.content:eq(0)').find('.sr-res').text(index + 1); });
			}

			function add_row(copy = false, rate = null, basic_price = null, price = null, product = null, product_text = null, supplier = null, color = null, model = null, price_text = null, features = null, features_selects = null, childsafe_question = null, childsafe_answer = null, qty = null, childsafe = 0, ladderband = 0, ladderband_value = 0, ladderband_price_impact = 0, ladderband_impact_type = 0, area_conflict = 0, subs = null, childsafe_x = null, childsafe_y = null, delivery_days = null, base_price = null, supplier_margin = null, retailer_margin = null, price_before_labor = null, price_before_labor_old = null, discount = null, discount_option = null, discount_option_val, total_discount = null, total_discount_old = null, last_column = null, menu2 = null, estimated_price_quantity = null, turns = null, measure = null, max_width = null, pis_title = null, secondary_title = null, general_ledger = null, vat = null) {

				var rowCount = 1;
				$('#products_table .content-div').each(function(){
					var rc = $(this).data('id');
					if(rc > rowCount) rowCount = rc;
				});
				rowCount = rowCount + 1;

				var r_id = 1;
				$('#products_table .content-div').find('.content:eq(0)').find('.sr-res').each(function(){
					var r = parseInt($(this).text());
					if(r > r_id) r_id = r;
				});
				r_id = parseInt(r_id) + 1;

				// var rowCount = $('#products_table .content-div:last').data('id');
				// rowCount = rowCount + 1;

				// var r_id = $('#products_table .content-div:last').find('.content:eq(0)').find('.sr-res').text();
				// r_id = parseInt(r_id) + 1;

				if (!copy) {

					$("#products_table").append('<div class="content-div" data-id="' + rowCount + '">\n' +
							'                                                            <div class="content full-res item1" style="width: 6%;">\n' +
							'                       									 	<label class="content-label">Sr. No</label>\n' +
							'                       									 	<img draggable="false" style="width: 20px;margin: 0 10px;" src="{{asset('assets/images/drag.png')}}">\n' +
							'                       									 	<div style="padding: 0 5px;" class="sr-res">' + r_id + '</div>\n' +
							'                       									 </div>\n' +
							'\n' +
							'                                                            <input type="hidden" id="order_number" name="order_number[]">\n' +
							'                                                            <input type="hidden" id="basic_price" name="basic_price[]">\n' +
							'                                                            <input type="hidden" value="0.00" id="rate" name="rate[]">\n' +
							'                                                            <input type="hidden" value="0.00" id="row_total" name="total[]">\n' +
							'                                                            <input type="hidden" value="' + rowCount + '" id="row_id" name="row_id[]">\n' +
							'                                                            <input type="hidden" value="0" id="childsafe" name="childsafe[]">\n' +
							'                                                            <input type="hidden" value="0" id="ladderband" name="ladderband[]">\n' +
							'                                                            <input type="hidden" value="0" id="ladderband_value" name="ladderband_value[]">\n' +
							'                                                            <input type="hidden" value="0" id="ladderband_price_impact" name="ladderband_price_impact[]">\n' +
							'                                                            <input type="hidden" value="0" id="ladderband_impact_type" name="ladderband_impact_type[]">\n' +
							'                                                            <input type="hidden" value="0" id="area_conflict" name="area_conflict[]">\n' +
							'                                                            <input type="hidden" value="1" id="delivery_days" name="delivery_days[]">\n' +
							'                                                            <input type="hidden" id="base_price" name="base_price[]">\n' +
							'                                                            <input type="hidden" id="supplier_margin" name="supplier_margin[]">\n' +
							'                                                            <input type="hidden" id="retailer_margin" name="retailer_margin[]">\n' +
							'                                                            <input type="hidden" id="estimated_price_quantity" name="estimated_price_quantity[]">\n' +
							'                                                            <input type="hidden" id="measure" name="measure[]">\n' +
							'                                                            <input type="hidden" id="max_width" name="max_width[]">\n' +
							'\n' +
							'                                                            <div style="width: 30%;" class="products content item3 full-res">\n' +
							'\n' +
							'                       									 	<label class="content-label">Product</label>\n' +
							'\n' +
							'                                                                <div class="autocomplete" style="width:100%;">\n' +
							'\n' +
							'																	<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product_descriptions[]" placeholder="{{__('text.Select Product')}}"></textarea>\n' +
							'																	<span class="pis_title hide"></span>\n' +
							'\n' +
							'                                                                </div>\n' +
							'\n' +
							'																<input type="hidden" name="products[]" id="product_id">\n' +
							'																<input type="hidden" name="suppliers[]" id="supplier_id">\n' +
							'																<input type="hidden" name="colors[]" id="color_id">\n' +
							'																<input type="hidden" name="models[]" id="model_id">\n' +
							'																<input type="hidden" name="secondary_titles[]" id="secondary_title">\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item6" style="width: 17%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">Qty</label>\n' +
							'\n' +
							'																 <div style="display: flex;align-items: center;height: 100%;position: relative;">\n' +
							'																 	<input type="text" name="qty[]" maskedFormat="9,1" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">\n' +
							'																	<div class="icon-container hide"><i class="loader"></i></div>\n' +
							'																 </div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item7" style="width: 17%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">€ Art.</label>\n' +
							'\n' +
							'																 <div style="display: flex;align-items: center;height: 100%;position: relative;">\n' +
							'																	<span>€</span>\n' +
							'																 	<input type="text" maskedformat="9,1" name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">\n' +
							'																	<input type="hidden" name="price_before_labor_old[]" class="price_before_labor_old">\n' +
							'																	<div class="icon-container hide"><i class="loader"></i></div>\n' +
							'																 </div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item8" style="width: 10%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">Discount</label>\n' +
							'\n' +
							'																<span>€</span>\n' +
							'																<input type="text" value="0" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;" class="form-control total_discount res-white">\n' +
							'																<input type="hidden" value="0" class="total_discount_old">\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div style="width: 7%;" class="content item9">\n' +
							'\n' +
							'                       									 	<label class="content-label">€ Total</label>\n' +
							'\n' +
							'																<div class="price res-white"></div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item10 last-content" id="next-row-td" style="padding: 0;width: 13%;">\n' +
							'\n' +
							'                       									 	<div class="res-white" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;">\n' +
							'\n' +
							'																<div style="display: none;" class="green-circle tooltip1">\n' +
							'																	<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.ALL features selected!')}}</span>\n' +
							'																</div>\n' +
							'\n' +
							'																<div style="visibility: hidden;" class="yellow-circle tooltip1">\n' +
							'																	<span style="top: 45px;left: -40px;" class="tooltiptext">{{__('text.Select all features!')}}</span>\n' +
							'																</div>\n' +
							'\n' +
							'																<span id="next-row-span" class="tooltip1 add-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
							'\n' +
							'																	<i id="next-row-icon" class="fa fa-fw fa-plus"></i>\n' +
							'\n' +
							'																	<span class="tooltiptext">{{__('text.Add')}}</span>\n' +
							'\n' +
							'																</span>\n' +
							'\n' +
							'																<span id="next-row-span" class="tooltip1 remove-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
							'\n' +
							'																	<i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>\n' +
							'\n' +
							'																	<span class="tooltiptext">{{__('text.Remove')}}</span>\n' +
							'\n' +
							'																</span>\n' +
							'\n' +
							'																<span id="next-row-span" class="tooltip1 copy-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
							'\n' +
							'																	<i id="next-row-icon" class="fa fa-fw fa-copy"></i>\n' +
							'\n' +
							'																	<span class="tooltiptext">{{__('text.Copy')}}</span>\n' +
							'\n' +
							'																</span>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="item11" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;margin-top: 10px;">\n' +
							'\n' +
							'                       									 	<button style="outline: none;" type="button" class="btn btn-info res-collapse collapsed" data-toggle="collapse" data-target="#demo' + rowCount + '"></button>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div style="width: 100%;" id="demo' + rowCount + '" class="item17 collapse">\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="ledger-box item14">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.General Ledger')}}</label>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<select name="general_ledgers[]" class="general_ledger">\n' +
							'                                    									<option value="">{{__("text.Select General Ledger")}}</option>\n' +
																									@foreach($general_ledgers as $key)
							'                                    										<option value="{{$key->id}}">{{$key->number . " - " . $key->title}}</option>\n' +
																									@endforeach
							'																	</select>\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="vat-box item15">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.VAT')}}</label>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<select name="vats[]" class="vat">\n' +
							'                                    									<option value="">{{__("text.Select VAT")}}</option>\n' +
																									@foreach($vats as $key)
							'                                    										<option data-percentage="{{$key->vat_percentage}}" value="{{$key->id}}">{{$key->vat_percentage . "% - " . $key->rule}}</option>\n' +
																									@endforeach
							'																	</select>\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="discount-box item16">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.Discount')}} <span class="discount-sign">%</span></label>\n' +
							'\n' +
							'																		<div style="display: flex;align-items: center;">\n' +
							'\n' +
							'																			<span style="font-size: 15px;padding-right: 10px;font-weight: 600;">%</span>\n' +
							'\n' +                  							
							'																			<label style="margin: 0;" class="switch">\n' +
                            '                                    											<input class="discount_option" name="discount_option[]" type="checkbox">\n' +
                            '                                    											<span class="slider round"></span>\n' +
                            '                                											</label>\n' +
							'\n' +                                                            						
							'																			<span style="font-size: 15px;padding-left: 10px;">€</span>\n' +
							'\n' +
							'																		</div>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<input value="0" class="discount_option_values" name="discount_option_values[]" type="hidden">\n' +
							'																	<input maskedformat="9,1" style="height: 35px;border-radius: 4px;" placeholder="Enter discount in percentage" type="text" class="form-control discount_values" value="0" name="discount[]">\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                        </div>');


					$('#menu2').append('<section class="attributes_table active" data-id="'+rowCount+'" style="width: 100%;">\n' +
							'                                                            <div class="header-div">\n' +
							'                       									 	<div class="headings" style="width: 22%;">{{__('text.Description')}}</div>\n' +
							'                       									 	<div class="headings" style="width: 10%;">{{__('text.Width')}}</div>\n' +
							'																<div class="headings" style="width: 10%;">{{__('text.Height')}}</div>\n' +
							'																<div class="headings m2_box" style="width: 20%;">{{__('text.Total')}}</div>\n' +
							'																<div class="headings" style="width: 10%;">{{__('text.Cutting lose')}}</div>\n' +
							'																<div class="headings m2_box" style="width: 20%;">{{__('text.Total')}}*</div>\n' +
							'																<div class="headings m1_box" style="width: 10%;display: none;">{{__('text.Turn')}}</div>\n' +
							'																<div class="headings m1_box" style="width: 10%;display: none;">{{__('text.Max Width')}}</div>\n' +
							'																<div class="headings m1_box" style="width: 20%;display: none;">{{__('text.Total')}}</div>\n' +
							'																<div class="headings" style="width: 8%;"></div>\n' +
							'                       									 </div>\n'
					);

					add_attribute_row(false, rowCount);

					var last_row = $('#products_table .content-div:last');

					last_row.find(".general_ledger").select2({
						width: '100%',
						height: '200px',
						placeholder: "{{__('text.Select General Ledger')}}",
						allowClear: true,
						"language": {
							"noResults": function () {
								return '{{__('text.No results found')}}';
							}
						},
					});

					last_row.find(".vat").select2({
						width: '100%',
						height: '200px',
						placeholder: "{{__('text.Select VAT')}}",
						allowClear: true,
						"language": {
							"noResults": function () {
								return '{{__('text.No results found')}}';
							}
						},
					});

					last_row.find('.vat').val(last_row.find('.vat').find(`[data-percentage='21']`).attr("value"));
					last_row.find('.vat').trigger('change.select2');

					drag_rows_add_events(last_row[0]);

					focus_row(last_row);

					autocomplete(last_row.find("#productInput")[0], product_titles, product_ids, model_ids, color_ids, supplier_ids, secondary_titles);

				}
				else {

					$("#products_table").append('<div class="content-div" data-id="' + rowCount + '">\n' +
							'                                                            <div class="content full-res item1" style="width: 6%;">\n' +
							'                       									 	<label class="content-label">Sr. No</label>\n' +
							'                       									 	<img draggable="false" style="width: 20px;margin: 0 10px;" src="{{asset('assets/images/drag.png')}}">\n' +
							'                       									 	<div style="padding: 0 5px;" class="sr-res">' + r_id + '</div>\n' +
							'                       									 </div>\n' +
							'\n' +
							'                                                            <input type="hidden" id="order_number" name="order_number[]">\n' +
							'                                                            <input value="' + basic_price + '" type="hidden" id="basic_price" name="basic_price[]">\n' +
							'                                                            <input value="' + rate + '" type="hidden" id="rate" name="rate[]">\n' +
							'                                                            <input value="' + price + '" type="hidden" id="row_total" name="total[]">\n' +
							'                                                            <input type="hidden" value="' + rowCount + '" id="row_id" name="row_id[]">\n' +
							'                                                            <input type="hidden" value="' + childsafe + '" id="childsafe" name="childsafe[]">\n' +
							'                                                            <input type="hidden" value="' + ladderband + '" id="ladderband" name="ladderband[]">\n' +
							'                                                            <input type="hidden" value="' + ladderband_value + '" id="ladderband_value" name="ladderband_value[]">\n' +
							'                                                            <input type="hidden" value="' + ladderband_price_impact + '" id="ladderband_price_impact" name="ladderband_price_impact[]">\n' +
							'                                                            <input type="hidden" value="' + ladderband_impact_type + '" id="ladderband_impact_type" name="ladderband_impact_type[]">\n' +
							'                                                            <input type="hidden" value="' + area_conflict + '" id="area_conflict" name="area_conflict[]">\n' +
							'                                                            <input type="hidden" value="' + delivery_days + '" id="delivery_days" name="delivery_days[]">\n' +
							'                                                            <input type="hidden" value="' + base_price + '" id="base_price" name="base_price[]">\n' +
							'                                                            <input type="hidden" value="' + supplier_margin + '" id="supplier_margin" name="supplier_margin[]">\n' +
							'                                                            <input type="hidden" value="' + retailer_margin + '" id="retailer_margin" name="retailer_margin[]">\n' +
							'                                                            <input type="hidden" value="' + estimated_price_quantity + '" id="estimated_price_quantity" name="estimated_price_quantity[]">\n' +
							'                                                            <input type="hidden" value="' + measure + '" id="measure" name="measure[]">\n' +
							'                                                            <input type="hidden" value="' + max_width + '" id="max_width" name="max_width[]">\n' +
							'\n' +
							'                                                            <div style="width: 30%;" class="products content item3 full-res">\n' +
							'\n' +
							'                       									 	<label class="content-label">Product</label>\n' +
							'\n' +
							'                                                                <div class="autocomplete" style="width:100%;">\n' +
							'\n' +
							'																	<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product_descriptions[]" placeholder="{{__('text.Select Product')}}">'+product_text+'</textarea>\n' +
							pis_title +
							'\n' +
							'                                                                </div>\n' +
							'\n' +
							'																<input type="hidden" name="products[]" id="product_id" value="'+product+'">\n' +
							'																<input type="hidden" name="suppliers[]" id="supplier_id" value="'+supplier+'">\n' +
							'																<input type="hidden" name="colors[]" id="color_id" value="'+color+'">\n' +
							'																<input type="hidden" name="models[]" id="model_id" value="'+model+'">\n' +
							'																<input type="hidden" name="secondary_titles[]" id="secondary_title" value="'+secondary_title+'">\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item6" style="width: 17%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">Qty</label>\n' +
							'\n' +
							'																 <div style="display: flex;align-items: center;height: 100%;position: relative;">\n' +
							'																 	<input value="' + qty + '" type="text" name="qty[]" maskedFormat="9,1" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">\n' +
							'																	<div class="icon-container hide"><i class="loader"></i></div>\n' +
							'																 </div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item7" style="width: 17%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">€ Art.</label>\n' +
							'\n' +
							'																 <div style="display: flex;align-items: center;height: 100%;position: relative;">\n' +
							'																	<span>€</span>\n' +
							'																 	<input value="' + price_before_labor + '" type="text" maskedformat="9,1" name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">\n' +
							'																	<input value="' + price_before_labor_old + '" type="hidden" name="price_before_labor_old[]" class="price_before_labor_old">\n' +
							'																	<div class="icon-container hide"><i class="loader"></i></div>\n' +
							'																 </div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item8" style="width: 10%;">\n' +
							'\n' +
							'                       									 	<label class="content-label">Discount</label>\n' +
							'\n' +
							'																<span>€</span>\n' +
							'																<input type="text" value="' + total_discount + '" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;" class="form-control total_discount res-white">\n' +
							'																<input type="hidden" value="' + total_discount_old + '" class="total_discount_old">\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div style="width: 7%;" class="content item9">\n' +
							'\n' +
							'                       									 	<label class="content-label">€ Total</label>\n' +
							'\n' +
							'																<div class="price res-white">' + price_text + '</div>\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="content item10 last-content" id="next-row-td" style="padding: 0;width: 13%;">\n' +
							'\n' +
							last_column +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div class="item11" style="display: flex;justify-content: flex-end;align-items: center;width: 100%;margin-top: 10px;">\n' +
							'\n' +
							'																<button style="outline: none;" type="button" class="btn btn-info res-collapse collapsed" aria-expanded="true" data-toggle="collapse" data-target="#demo' + rowCount + '"></button>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                            <div style="width: 100%;" id="demo' + rowCount + '" class="item17 collapse">\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="ledger-box item14">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.General Ledger')}}</label>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<select name="general_ledgers[]" class="general_ledger">\n' +
							'                                    									<option value="">{{__("text.Select General Ledger")}}</option>\n' +
																									@foreach($general_ledgers as $key)
							'                                    										<option value="{{$key->id}}">{{$key->number . " - " . $key->title}}</option>\n' +
																									@endforeach
							'																	</select>\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="vat-box item15">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.VAT')}}</label>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<select name="vats[]" class="vat">\n' +
							'                                    									<option value="">{{__("text.Select VAT")}}</option>\n' +
																									@foreach($vats as $key)
							'                                    										<option data-percentage="{{$key->vat_percentage}}" value="{{$key->id}}">{{$key->vat_percentage . "% - " . $key->rule}}</option>\n' +
																									@endforeach
							'																	</select>\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'																<div style="width: 25%;margin-left: 10px;" class="discount-box item16">\n' +
							'\n' +
							'																	<div style="margin: 10px 0;display: flex;align-items: center;" class="form-group">\n' +
							'\n' +
                            '                                										<label style="padding: 0;width: 100%;text-align: left;" class="control-label">{{__('text.Discount')}} <span class="discount-sign">%</span></label>\n' +
							'\n' +
							'																		<div style="display: flex;align-items: center;">\n' +
							'\n' +
							'																			<span style="font-size: 15px;padding-right: 10px;font-weight: 600;">%</span>\n' +
							'\n' +                  							
							'																			<label style="margin: 0;" class="switch">\n' +
                            '                                    											<input ' + discount_option + ' class="discount_option" name="discount_option[]" type="checkbox">\n' +
                            '                                    											<span class="slider round"></span>\n' +
                            '                                											</label>\n' +
							'\n' +                                                            						
							'																			<span style="font-size: 15px;padding-left: 10px;">€</span>\n' +
							'\n' +
							'																		</div>\n' +
							'\n' +																					
							'																	</div>\n' +
							'\n' +
							'                                    								<input value="' + discount_option_val + '" class="discount_option_values" name="discount_option_values[]" type="hidden">\n' +
							'																	<input maskedformat="9,1" value="' + discount + '" style="height: 35px;border-radius: 4px;" placeholder="Enter discount in percentage" type="text" class="form-control discount_values" name="discount[]">\n' +
							'\n' +
							'																</div>\n' +
							'\n' +
							'                                                            </div>\n' +
							'\n' +
							'                                                        </div>');

					var last_row = $('#products_table .content-div:last');

					last_row.find(".general_ledger").val(general_ledger);
					last_row.find(".vat").val(vat);

					last_row.find(".general_ledger").select2({
						width: '100%',
						height: '200px',
						placeholder: "{{__('text.Select General Ledger')}}",
						allowClear: true,
						"language": {
							"noResults": function () {
								return '{{__('text.No results found')}}';
							}
						},
					});

					last_row.find(".vat").select2({
						width: '100%',
						height: '200px',
						placeholder: "{{__('text.Select VAT')}}",
						allowClear: true,
						"language": {
							"noResults": function () {
								return '{{__('text.No results found')}}';
							}
						},
					});

					drag_rows_add_events(last_row[0]);

					autocomplete(last_row.find("#productInput")[0], product_titles, product_ids, model_ids, color_ids, supplier_ids, secondary_titles);

					if (features) {

						$('#menu1').append('<div data-id="' + rowCount + '" style="margin: 0;" class="form-group">\n' + features + '</div>');

						if (childsafe == 1) {
							$('#menu1').find(`[data-id='${rowCount}']`).find('.childsafe-select').attr('name', 'childsafe_option' + rowCount);
							$('#menu1').find(`[data-id='${rowCount}']`).find('.childsafe_diff').attr('name', 'childsafe_diff' + rowCount);
							$('#menu1').find(`[data-id='${rowCount}']`).find('.childsafe-answer').attr('name', 'childsafe_answer' + rowCount);
							$('#menu1').find(`[data-id='${rowCount}']`).find('#childsafe_x').val(childsafe_x);
							$('#menu1').find(`[data-id='${rowCount}']`).find('#childsafe_y').val(childsafe_y);
							$('#menu1').find(`[data-id='${rowCount}']`).find('.childsafe-select').val(childsafe_question);
							$('#menu1').find(`[data-id='${rowCount}']`).find('.childsafe-answer').val(childsafe_answer);
						}

						features_selects.each(function (index, select) {

							$('#menu1').find(`[data-id='${rowCount}']`).find('.feature-select').eq(index).val($(this).val());

							if ($(this).parent().find('.f_id').val() == 0) {
								$('#myModal').find('.modal-body').append('<div class="sub-tables" data-id="' + rowCount + '">\n' + subs + '</div>');
							}

						});

						$('#menu1').find(`[data-id='${rowCount}']`).each(function (i, obj) {

							$(obj).find('.ladderband-btn').attr('data-id', rowCount);
							$(obj).find('.feature-select').attr('name', 'features' + rowCount + '[]');
							$(obj).find('.f_price').attr('name', 'f_price' + rowCount + '[]');
							$(obj).find('.f_id').attr('name', 'f_id' + rowCount + '[]');
							$(obj).find('.f_area').attr('name', 'f_area' + rowCount + '[]');
							$(obj).find('.sub_feature').attr('name', 'sub_feature' + rowCount + '[]');
							$(obj).find('#childsafe_x').attr('name', 'childsafe_x' + rowCount);
							$(obj).find('#childsafe_y').attr('name', 'childsafe_y' + rowCount);

						});

						$('#myModal').find('.modal-body').find(`[data-id='${rowCount}']`).each(function (i, obj) {

							$(obj).find('.sizeA').each(function (b, obj1) {

								if ($(this).val() == 1) {
									$(this).prev('input').prop("checked", true);
								}

							});

							$(obj).find('.sizeB').each(function (c, obj2) {

								if ($(this).val() == 1) {
									$(this).prev('input').prop("checked", true);
								}

							});

							$(obj).find('.sub_product_id').attr('name', 'sub_product_id' + rowCount + '[]');
							$(obj).find('.sizeA').attr('name', 'sizeA' + rowCount + '[]');
							$(obj).find('.sizeB').attr('name', 'sizeB' + rowCount + '[]');
							$(obj).find('.cus_radio').attr('name', 'cus_radio' + rowCount + '[]');
							$(obj).find('.cus_radio').attr('data-id', rowCount);

						});

					}

					if(menu2)
					{
						$('#menu2').append('<section class="attributes_table" data-id="'+rowCount+'" style="width: 100%;"></section>\n');
						menu2.appendTo(`#menu2 .attributes_table[data-id='${rowCount}']`);

						$('#menu2').find(`.attributes_table[data-id='${rowCount}']`).each(function (i, obj) {

							$(obj).find('.calculator_row').attr('name', 'calculator_row' + rowCount + '[]');
							$(obj).find('.attribute_description').attr('name', 'attribute_description' + rowCount + '[]');
							$(obj).find('.width-box').find('.width').attr('name', 'width' + rowCount + '[]');
							$(obj).find('.width-box').find('.measure-unit').attr('name', 'width_unit' + rowCount + '[]');
							$(obj).find('.height-box').find('.height').attr('name', 'height' + rowCount + '[]');
							$(obj).find('.height-box').find('.measure-unit').attr('name', 'height_unit' + rowCount + '[]');
							$(obj).find('.cutting_lose_percentage').attr('name', 'cutting_lose_percentage' + rowCount + '[]');
							$(obj).find('.total_boxes').attr('name', 'total_boxes' + rowCount + '[]');
							$(obj).find('.total_inc_cuttinglose').attr('name', 'total_inc_cuttinglose' + rowCount + '[]');
							$(obj).find('.box_quantity_supplier').attr('name', 'box_quantity_supplier' + rowCount + '[]');
							$(obj).find('.box_quantity').attr('name', 'box_quantity' + rowCount + '[]');
							$(obj).find('.max_width').attr('name', 'max_width' + rowCount + '[]');
							$(obj).find('.turn').attr('name', 'turn' + rowCount + '[]');

						});

						turns.each(function (index, select) {

							$('#menu2').find(`.attributes_table[data-id='${rowCount}']`).find('.turn').eq(index).val($(this).val());

						});
					}

					focus_row(last_row);
				}

				calculate_total();
			}

			$(document).on('click', '#products_table .content-div', function (e) {

				if (e.target.id !== "next-row-td" && e.target.id !== "next-row-span" && e.target.id !== "next-row-icon") {
					focus_row($(this));
				}

			});

			$(document).on('click', '.next-row', function () {

				if ($(this).parents(".content-div").next('.content-div').length == 0) {
					add_row();
				}
				else {
					var next_row = $(this).parents(".content-div").next('.content-div');
					focus_row(next_row);
				}
			});

			$(document).on('click', '.add-row', function () {

				add_row();

			});

			$(document).on('click', '.add-pc-row', function () {

				add_pc_row();

			});

			$(document).on('click', '.remove-row', function () {

				var rowCount = $('#products_table .content-div').length;

				var current = $(this).parents('.content-div');

				var id = current.data('id');

				if (rowCount != 1) {

					$('#menu1').find(`[data-id='${id}']`).remove();
					$('#menu2').find(`.attributes_table[data-id='${id}']`).remove();
					$('#myModal').find('.modal-body').find(`[data-id='${id}']`).remove();
					$('#myModal2').find('.modal-body').find(`[data-id='${id}']`).remove();

					var next = current.next('.content-div');

					if (next.length < 1) {
						var next = current.prev('.content-div');
					}

					focus_row(next);

					current.remove();

					numbering();
					calculate_total();
				}

			});

			$(document).on('click', '.remove-pc-row', function () {

				var rowCount = $('.pc_table .pc-content-div').length;

				var current = $(this).parents('.pc-content-div');

				var id = current.data('id');

				if (rowCount != 1) {

					current.remove();
					// calculate_percentage_amounts();
					payment_calculations(2,null,1);
				}

			});

			$(document).on('click', '.close-form', function () {

				var quote_request_id = $('#quote_request_id').val();
				var flag = 0;

				if(quote_request_id == '')
				{
					var customer = $('.customer-select').val();
					if (!customer) {
						flag = 1;
						$('#cus-box .select2-container--default .select2-selection--single').css('border-color', 'red');
					}
					else {
						$('#cus-box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
					}
				}

				var total_amount = $("#total_amount").val();
				total_amount = total_amount.replace(/\./g, '');
				total_amount = total_amount.replace(/\,/g, '.');

				var total_percentage = $(".pc_percentages_total").val();
				total_percentage = total_percentage.replace(/\./g, '');
				total_percentage = parseFloat(total_percentage.replace(/\,/g, '.'));

				var total_payment_amount = $(".pc_amounts_total").val();
				total_payment_amount = total_payment_amount.replace(/\./g, '');
				total_payment_amount = parseFloat(total_payment_amount.replace(/\,/g, '.'));

				// if(total_percentage != 100)
				// {
				// 	flag = 1;

				// 	Swal.fire({
				// 		icon: 'error',
				// 		title: '{{__('text.Oops...')}}',
				// 		text: '{{__("text.Total payment percentage should not be higher or lower than 100.")}}',
				// 	});
				// }
				// else if(total_payment_amount != total_amount)
				// {
				// 	flag = 1;

				// 	Swal.fire({
				// 		icon: 'error',
				// 		title: '{{__('text.Oops...')}}',
				// 		text: '{{__("text.Total payment amount should not be higher or lower than total quote amount.")}}',
				// 	});
				// }

				// $("[name='products[]']").each(function (i, obj) {

				// 	if (!obj.value) {
				// 		flag = 1;
				// 		$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				// 	}
				// 	else {
				// 		$(obj).parents('.products').find('#productInput').css('border', '0');
				// 	}

				// });

				// $("[name='colors[]']").each(function (i, obj) {

				// 	var product_id = $("[name='products[]']").eq(i).val();

				// 	if(!product_id.includes('S') && !product_id.includes('I'))
				// 	{
				// 		if (!obj.value) {
				// 			flag = 1;
				// 			$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				// 		}
				// 		else {
				// 			$(obj).parents('.products').find('#productInput').css('border', '0');
				// 		}
				// 	}

				// });

				// $("[name='models[]']").each(function (i, obj) {

				// 	var product_id = $("[name='products[]']").eq(i).val();

				// 	if(!product_id.includes('S') && !product_id.includes('I'))
				// 	{
				// 		if (!obj.value) {
				// 			flag = 1;
				// 			$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				// 		}
				// 		else {
				// 			$(obj).parents('.products').find('#productInput').css('border', '0');
				// 		}
				// 	}

				// });

				var conflict_feature = 0;

				$("[name='row_id[]']").each(function () {

					var id = $(this).val();

					var childsafe = $("[name='childsafe_option" + id + "']").val();

					if (!childsafe && childsafe != undefined) {
						flag = 1;
						conflict_feature = 1;
						$("[name='childsafe_option" + id + "']").css('border-bottom', '1px solid red');
					}
					else {
						$("[name='childsafe_option" + id + "']").css('border-bottom', '1px solid lightgrey');
					}

					$("[name='features" + id + "[]']").each(function (i, obj) {

						var selected_feature = $(this).val();
						var feature_id = $(this).parent().find('.f_id').val();

						if (feature_id != 0) {
							if (selected_feature == 0) {
								flag = 1;
								conflict_feature = 1;
								$(this).css('border-bottom', '1px solid red');
							}
							else {
								$(this).css('border-bottom', '1px solid lightgrey');
							}
						}

					});

					// $("[name='width" + id + "[]']").each(function (i, obj) {

					// 	var value = $(this).val();

					// 	if (value == "") {
					// 		flag = 1;
					// 		$(this).css('border', '1px solid red');
					// 	}
					// 	else
					// 	{
					// 		$(this).css('border', '1px solid #ccc');
					// 	}

					// });

					// $("[name='height" + id + "[]']").each(function (i, obj) {

					// 	var value = $(this).val();

					// 	if (value == "") {
					// 		flag = 1;
					// 		$(this).css('border', '1px solid red');
					// 	}
					// 	else
					// 	{
					// 		$(this).css('border', '1px solid #ccc');
					// 	}

					// });

					// $("[name='cutting_lose_percentage" + id + "[]']").each(function (i, obj) {

					// 	var value = $(this).val();

					// 	if (value == "") {
					// 		flag = 1;
					// 		$(this).css('border', '1px solid red');
					// 	}
					// 	else
					// 	{
					// 		$(this).css('border', '1px solid #ccc');
					// 	}

					// });

				});

				if (conflict_feature) {

					Swal.fire({
						icon: 'error',
						title: '{{__('text.Oops...')}}',
						text: '{{__('text.Feature should not be empty!')}}',
					});

				}

				if (!flag) {
					$('#form-quote').submit();
				}

			});

			$(document).on('click', '.copy-row', function () {

				var current = $(this).parents('.content-div');
				var id = current.data('id');
				var childsafe = current.find('#childsafe').val();
				var ladderband = current.find('#ladderband').val();
				var ladderband_value = current.find('#ladderband_value').val();
				var ladderband_price_impact = current.find('#ladderband_price_impact').val();
				var ladderband_impact_type = current.find('#ladderband_impact_type').val();
				var area_conflict = current.find('#area_conflict').val();
				var delivery_days = current.find('#delivery_days').val();
				var rate = current.find('#rate').val();
				var basic_price = current.find('#basic_price').val();
				var price = current.find('#row_total').val();
				var product = current.find('#product_id').val();
				var product_text = current.find('#productInput').val();
				var supplier = current.find('#supplier_id').val();
				var color = current.find('#color_id').val();
				var model = current.find('#model_id').val();
				var secondary_title = current.find('#secondary_title').val();
				var price_text = current.find('.price').text();
				var features = $('#menu1').find(`[data-id='${id}']`).html();
				var childsafe_question = $('#menu1').find(`[data-id='${id}']`).find('.childsafe-select').val();
				var childsafe_answer = $('#menu1').find(`[data-id='${id}']`).find('.childsafe-answer').val();
				var features_selects = $('#menu1').find(`[data-id='${id}']`).find('.feature-select');
				var qty = current.find('.qty').val();
				var subs = $('#myModal').find('.modal-body').find(`[data-id='${id}']`).html();
				var childsafe_x = $('#menu1').find(`[data-id='${id}']`).find('#childsafe_x').val();
				var childsafe_y = $('#menu1').find(`[data-id='${id}']`).find('#childsafe_y').val();
				var base_price = current.find('#base_price').val();
				var supplier_margin = current.find('#supplier_margin').val();
				var retailer_margin = current.find('#retailer_margin').val();
				var price_before_labor = current.find('.price_before_labor').val();
				var price_before_labor_old = current.find('.price_before_labor_old').val();
				var discount = current.find('.discount-box').find('.discount_values').val();
				var discount_option = current.find('.discount-box').find('.discount_option').is(":checked") ? 'checked' : null;
				var discount_option_val = current.find('.discount-box').find('.discount_option_values').val();
				// var labor_discount = current.find('.labor-discount-box').find('.labor_discount_values').val();
				var total_discount = current.find('.total_discount').val();
				var total_discount_old = current.find('.total_discount_old').val();
				var last_column = current.find('#next-row-td').html();
				var menu2 = $('#menu2').find(`.attributes_table[data-id='${id}']`).children().clone();
				var turns = $('#menu2').find(`.attributes_table[data-id='${id}']`).find('.turn');
				var estimated_price_quantity = current.find('#estimated_price_quantity').val();
				var measure = current.find('#measure').val();
				var max_width = current.find('#max_width').val();
				var pis_title = current.find(".pis_title")[0].outerHTML;
				var general_ledger = current.find(".general_ledger").val();
				var vat = current.find(".vat").val();

				add_row(true, rate, basic_price, price, product, product_text, supplier, color, model, price_text, features, features_selects, childsafe_question, childsafe_answer, qty, childsafe, ladderband, ladderband_value, ladderband_price_impact, ladderband_impact_type, area_conflict, subs, childsafe_x, childsafe_y, delivery_days, base_price, supplier_margin, retailer_margin, price_before_labor, price_before_labor_old, discount, discount_option, discount_option_val, total_discount, total_discount_old, last_column, menu2, estimated_price_quantity, turns, measure, max_width, pis_title, secondary_title, general_ledger, vat);

			});

			$(document).on('keypress', "input[name='qty[]'], .pc_percentage, .pc_amount, .discount_values", function (e) {

				var startPos = $(this)[0].selectionStart;
				var endPos = $(this)[0].selectionEnd;
				e = e || window.event;
				var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
				var val = String.fromCharCode(charCode);

				if(val == '.')
				{
					val = ',';
				}

				if (!val.match(/^-?[0-9]*\,?[0-9]*$/))  // For characters validation
				{
					e.preventDefault();
					return false;
				}

				if (e.which == 44 || e.which == 46) {
					if (this.value.indexOf(',') > -1) {
						e.preventDefault();
						return false;
					}
				}

				if (e.which == 45) {

					if (this.value.indexOf('-') > -1) {
						e.preventDefault();
						return false;
					}
					else
					{
						this.value = String.fromCharCode(45) + this.value;
						$(this).trigger("input");
						e.preventDefault();
						return false;
					}

				}
				else
				{
					if(startPos == endPos) //when no selection
					{
						var num = $(this).attr("maskedFormat").toString().split(',');
						var regex = new RegExp("^-?\\d{0," + num[0] + "}(\\,\\d{0," + num[1] + "})?$");
					
						if (!regex.test(this.value)) {
							this.value = this.value.substring(0, this.value.length - 1);
						}
						else{

							if(e.which == 46)
							{
								this.value = this.value + String.fromCharCode(44);
								$(this).trigger("input");
								e.preventDefault();
								return false;
							}

						}
					}
					else
					{
						if(e.which == 46)
						{
							this.value = String.fromCharCode(44);
							$(this).trigger("input");
							e.preventDefault();
							return false;
						}
					}
				}

			});

			$(document).on('keypress', "input[name='price_before_labor[]'], .total_boxes, .width, .height", function (e) {

				var startPos = $(this)[0].selectionStart;
				var endPos = $(this)[0].selectionEnd;
				e = e || window.event;
				var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
				var val = String.fromCharCode(charCode);

				if(val == '.')
				{
					val = ',';
				}

				if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
				{
					e.preventDefault();
					return false;
				}

				if (e.which == 44 || e.which == 46) {
					if (this.value.indexOf(',') > -1) {
						e.preventDefault();
						return false;
					}
				}

				if(startPos == endPos) //when no selection
				{
					var num = $(this).attr("maskedFormat").toString().split(',');
					var regex = new RegExp("^\\d{0," + num[0] + "}(\\,\\d{0," + num[1] + "})?$");

					if (!regex.test(this.value)) {
						this.value = this.value.substring(0, this.value.length - 1);
					}
					else{

						if(e.which == 46)
						{
							this.value = this.value + String.fromCharCode(44);
							$(this).trigger("input");
							e.preventDefault();
							return false;
						}

					}
				}
				else
				{
					if(e.which == 46)
					{
						this.value = String.fromCharCode(44);
						$(this).trigger("input");
						e.preventDefault();
						return false;
					}
				}

			});

			$(document).on('keypress', ".childsafe_values", function (e) {

				e = e || window.event;
				var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
				var val = String.fromCharCode(charCode);

				if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
				{
					e.preventDefault();
					return false;
				}

				if (e.which == 44) {
					e.preventDefault();
					return false;
				}

			});

			$(document).on('input', ".discount_values", function (e) {

				calculate_total();

			});

			$(document).on('input', "input[name='price_before_labor[]']", function (e) {

				var value = $(this).val();

				if ($(this).val().slice($(this).val().length - 1) == ',') {
					var val = $(this).val();
					val = val + '00';
					value = val;
				}

				$(this).next(".price_before_labor_old").val(value.replace(/\,/g, '.'));
				$(this).parents(".content-div").find("#row_total").val(parseFloat(value.replace(/\,/g, '.')));
				$(this).parents(".content-div").find(".total_discount_old").val(-0.00);

				calculate_total();

			});

			$(document).on('change', ".vat", function (e) {
				
				// var vat_box = $(this).parents(".vat-box");
				// calculate_vat(vat_box);
				calculate_total();
			
			});

			$(document).on('input', "input[name='qty[]']", function (e) {

				calculate_total();

			});

			$(document).on('input', ".pc_percentage", function (e) {

				var current = $(this).parents(".pc-content-div");
				// calculate_percentage_amounts(null,current);
				payment_calculations(0,current,1);

			});

			$(document).on('input', ".pc_amount", function (e) {

				var current = $(this).parents(".pc-content-div");
				payment_calculations(1,current,1);

			});

			$(document).on('focusout', "input[name='qty[]'], input[name='price_before_labor[]'], .total_boxes, .discount_values, .pc_percentage, .pc_amount", function (e) {

				if (!$(this).val()) {
					$(this).val(0);
				}

				if ($(this).val().slice($(this).val().length - 1) == ',') {
					var val = $(this).val();
					val = val + '00';
					$(this).val(val);
				}

				if (!$(this).val().includes(',')) {
					var val = $(this).val();
					val = val + ',00';
					$(this).val(val);
				}

			});

			function calculate_vat(vat_box)
			{
				var vat_percentage = vat_box.find(".vat option:selected").data('percentage');
				var vat = vat_percentage == undefined ? 0 : vat_percentage/100;
				var total = vat_box.parents(".content-div").find("#rate").val();
				var net_amount = total/(1 + vat);
				var vat = total - net_amount;
				net_amount = parseFloat(net_amount.toFixed(4));
				vat = parseFloat(vat.toFixed(4));
			}

			// $(document).on('input', '.labor_impact', function () {

			// 	var value = $(this).val();
			// 	value = value.replace(/\,/g, '.');
			// 	var row_id = $(this).parents(".content-div").data('id');
			// 	var price_before_labor = $('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val();
			// 	price_before_labor = price_before_labor.replace(/\,/g, '.');
			// 	var qty = $('#menu1').find(`[data-id='${row_id}']`).find('input[name="qty[]"]').val();
			// 	var total_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val();
			// 	total_discount = total_discount.replace(/\,/g, '.');

			// 	if (!value) {
			// 		value = 0;
			// 	}

			// 	var total = parseFloat(price_before_labor) + parseFloat(value);
			// 	total = total + parseFloat(total_discount);
			// 	total = parseFloat(total);
			// 	total = total.toFixed(2);
			// 	var price = total;
			// 	total = total / qty;
			// 	total = parseFloat(total).toFixed(2);
			// 	//total = Math.round(total);

			// 	var new_old_value = value / qty;
			// 	new_old_value = parseFloat(new_old_value).toFixed(2);

			// 	$('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact_old').val(new_old_value);
			// 	$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + total.replace(/\./g, ','));
			// 	$('#products_table').find(`[data-id='${row_id}']`).find('#rate').val(price);
			// 	$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(total);

			// 	calculate_total(0,1);

			// });

			$(document).on('input', '#childsafe_x, #childsafe_y', function () {

				var id = $(this).attr('id');
				var row_id = $(this).parent().parent().parent().data('id');

				if (id == 'childsafe_x') {
					var x = $(this).val();
					var y = $('#menu1').find(`[data-id='${row_id}']`).find('#childsafe_y').val();
				}
				else {
					var x = $('#menu1').find(`[data-id='${row_id}']`).find('#childsafe_x').val();
					var y = $(this).val();
				}

				var diff = x - y;
				diff = Math.abs(diff);

				if (x && y) {

					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-answer-box').remove();
					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').find('option').not(':first').remove();

					if (diff <= 150) {

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').append('<option value="1" selected>Please note not childsafe</option><option value="2">Add childsafety clip</option>');

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').after('<div class="row childsafe-answer-box" style="margin: 0;display: flex;align-items: center;">\n' +
								'\n' +
								'                                                                                        <div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
								'                                                                                            <label style="margin-right: 10px;margin-bottom: 0">{{__('text.Childsafe Answer')}}</label>\n' +
								'                                                                                            <select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-answer" name="childsafe_answer' + row_id + '">\n' +
								'                                                                                                    <option value="1">{{__('text.Make it childsafe')}}</option>\n' +
								'                                                                                                    <option value="2">{{__('text.Yes i agree')}}</option>\n' +
								'                                                                                            </select>\n' +
								'                                                                                        </div>\n' +
								'\n' +
								'                                                                                    </div>');

					}
					else {

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').append('<option value="2">Add childsafety clip</option><option value="3" selected>Yes childsafe</option>');

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').after('<div class="row childsafe-answer-box" style="margin: 0;display: flex;align-items: center;">\n' +
								'\n' +
								'                                                                                        <div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
								'                                                                                            <label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Childsafe Answer')}}</label>\n' +
								'                                                                                            <select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-answer" name="childsafe_answer' + row_id + '">\n' +
								'                                                                                                    <option value="3">{{__('text.Is childsafe')}}</option>\n' +
								'                                                                                            </select>\n' +
								'                                                                                        </div>\n' +
								'\n' +
								'                                                                                    </div>');

					}

					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe_diff').val(diff);

					var flag = 0;

					var childsafe = $('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-select').val();

					if (!childsafe && childsafe != undefined) {
						flag = 1;
					}

					$("[name='features" + row_id + "[]']").each(function (i, obj) {

						var selected_feature = $(this).val();
						var feature_id = $(this).parent().find('.f_id').val();

						if (feature_id != 0) {
							if (selected_feature == 0) {
								flag = 1;
							}
						}

					});

					if(flag == 1)
					{
						$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
						$('#products_table').find(`[data-id='${row_id}']`).find('.yellow-circle').css('visibility','visible');
						$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();
					}
					else
					{
						$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').hide();
						$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').css('visibility','visible');
						$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').show();
					}
				}
				else {

					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
					$('#products_table').find(`[data-id='${row_id}']`).find('.yellow-circle').css('visibility','visible');
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();

					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-answer-box').remove();
					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').find('option').not(':first').remove();
					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').append('<option value="2">{{__('text.Add childsafety clip')}}</option>');
				}

			});

			$(document).on('change', '.childsafe-select', function () {
				var current = $(this);
				var row_id = current.parent().parent().parent().data('id');
				var value = current.val();
				var value_x = $('#menu1').find(`[data-id='${row_id}']`).find('#childsafe_x').val();
				var value_y = $('#menu1').find(`[data-id='${row_id}']`).find('#childsafe_y').val();

				if (value_x && value_y) {
					if (!value) {
						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-answer-box').remove();
					}
					else if (value == 2 || value == 3) {
						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-answer-box').remove();

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').after('<div class="row childsafe-answer-box" style="margin: 0;display: flex;align-items: center;">\n' +
								'\n' +
								'                                                                                        <div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
								'                                                                                            <label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Childsafe Answer')}}</label>\n' +
								'                                                                                            <select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-answer" name="childsafe_answer' + row_id + '">\n' +
								'                                                                                                    <option value="3">{{__('text.Is childsafe')}}</option>\n' +
								'                                                                                            </select>\n' +
								'                                                                                        </div>\n' +
								'\n' +
								'                                                                                    </div>');
					}
					else {
						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-answer-box').remove();

						$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').after('<div class="row childsafe-answer-box" style="margin: 0;display: flex;align-items: center;">\n' +
								'\n' +
								'                                                                                        <div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
								'                                                                                            <label style="margin-right: 10px;margin-bottom: 0">{{__('text.Childsafe Answer')}}</label>\n' +
								'                                                                                            <select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-answer" name="childsafe_answer' + row_id + '">\n' +
								'                                                                                                    <option value="1">{{__('text.Make it childsafe')}}</option>\n' +
								'                                                                                                    <option value="2">{{__('text.Yes i agree')}}</option>\n' +
								'                                                                                            </select>\n' +
								'                                                                                        </div>\n' +
								'\n' +
								'                                                                                    </div>');
					}
				}
				else {
					current.val('');

					Swal.fire({
						icon: 'error',
						title: '{{__('text.Oops...')}}',
						text: '{{__('text.Kindly fill both childsafe values first.')}}',
					});
				}

			});

			$(document).on('change', '.feature-select', function () {

				var current = $(this);
				var row_id = current.parent().parent().parent().data('id');
				var feature_select = current.val();
				var id = current.parent().find('.f_id').val();
				var product_id = $('#products_table').find(`[data-id='${row_id}']`).find('.products').find('select').val();
				var ladderband_value = $('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_value').val();
				var ladderband_price_impact = $('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_price_impact').val();
				var ladderband_impact_type = $('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_impact_type').val();

				var impact_value = current.next('input').val();
				var total = $('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val();
				var basic_price = $('#products_table').find(`[data-id='${row_id}']`).find('#basic_price').val();
				var margin = 1;
				var supplier_margin = $('#products_table').find(`[data-id='${row_id}']`).find('#supplier_margin').val();
				var retailer_margin = $('#products_table').find(`[data-id='${row_id}']`).find('#retailer_margin').val();

				total = total - impact_value;
				var price_before_labor = $('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val();
				price_before_labor = price_before_labor - impact_value;

				if (id == 0) {

					if (feature_select == 1) {

						if (ladderband_price_impact == 1) {
							if (ladderband_impact_type == 0) {
								impact_value = ladderband_value;
								impact_value = parseFloat(impact_value).toFixed(2);
								total = parseFloat(total) + parseFloat(impact_value);
								total = total.toFixed(2);
							}
							else {
								impact_value = ladderband_value;
								var per = (impact_value) / 100;
								impact_value = basic_price * per;
								impact_value = parseFloat(impact_value).toFixed(2);
								total = parseFloat(total) + parseFloat(impact_value);
								total = total.toFixed(2);
							}
						}
						else {
							impact_value = 0;
							total = parseFloat(total) + parseFloat(impact_value);
							total = total.toFixed(2);
						}

						//total = Math.round(total);
						price_before_labor = parseFloat(price_before_labor) + parseFloat(impact_value);
						price_before_labor = parseFloat(price_before_labor).toFixed(2);
						//price_before_labor = Math.round(price_before_labor);

						current.next('input').val(impact_value);

						$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(price_before_labor);
						$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + total.replace(/\./g, ','));
						$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(total);

						calculate_total();

						$.ajax({
							type: "GET",
							data: "product_id=" + product_id,
							url: "<?php echo url('/aanbieder/get-sub-products-sizes') ?>",
							success: function (data) {

								$('#myModal').find('.modal-body').find('.sub-tables').hide();

								if ($('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).length > 0) {
									$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).remove();
								}


								$('#myModal').find('.modal-body').append(
										'<div class="sub-tables" data-id="' + row_id + '">\n' +
										'<table style="width: 100%;">\n' +
										'<thead>\n' +
										'<tr>\n' +
										'<th>ID</th>\n' +
										'<th>{{__('text.Title')}}</th>\n' +
										'<th>{{__('text.Size 38mm')}}</th>\n' +
										'<th>{{__('text.Size 25mm')}}</th>\n' +
										'</tr>\n' +
										'</thead>\n' +
										'<tbody>\n' +
										'</tbody>\n' +
										'</table>\n' +
										'</div>'
								);

								$.each(data, function (index, value) {

									var size1 = value.size1_value;
									var size2 = value.size2_value;

									if (size1 == 1) {
										size1 = '<input data-id="' + row_id + '" class="cus_radio" name="cus_radio' + row_id + '[]" type="radio"><input class="cus_value sizeA" type="hidden" value="0" name="sizeA' + row_id + '[]">';
									}
									else {
										size1 = 'X' + '<input class="sizeA" name="sizeA' + row_id + '[]" type="hidden" value="x">';
									}

									if (size2 == 1) {
										size2 = '<input data-id="' + row_id + '" class="cus_radio" name="cus_radio' + row_id + '[]" type="radio"><input class="cus_value sizeB" type="hidden" value="0" name="sizeB' + row_id + '[]">';
									}
									else {
										size2 = 'X' + '<input class="sizeB" name="sizeB' + row_id + '[]" type="hidden" value="x">';
									}

									$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).find('table').append(
											'<tr>\n' +
											'<td><input class="sub_product_id" type="hidden" name="sub_product_id' + row_id + '[]" value="' + value.id + '">' + value.code + '</td>\n' +
											'<td>' + value.title + '</td>\n' +
											'<td>' + size1 + '</td>\n' +
											'<td>' + size2 + '</td>\n' +
											'</tr>\n'
									);

								});

								$('#menu1').find(`[data-id='${row_id}']`).find('.ladderband-btn').removeClass('hide');
								/*$('.top-bar').css('z-index','1');*/
								$('#myModal').modal('toggle');
								$('.modal-backdrop').hide();
							}
						});
					}
					else {

						$('#menu1').find(`[data-id='${row_id}']`).find('.ladderband-btn').addClass('hide');
						$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).remove();

						impact_value = 0;
						total = parseFloat(total) + parseFloat(impact_value);
						total = total.toFixed(2);
						//total = Math.round(total);
						price_before_labor = parseFloat(price_before_labor) + parseFloat(impact_value);
						price_before_labor = parseFloat(price_before_labor).toFixed(2);
						//price_before_labor = Math.round(price_before_labor);

						current.next('input').val(impact_value);

						$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(price_before_labor);
						$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + total.replace(/\./g, ','));
						$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(total);

						calculate_total();
					}
				}
				else {
					var heading = current.find("option:selected").text();
					var heading_id = current.val();

					$.ajax({
						type: "GET",
						data: "id=" + feature_select,
						url: "<?php echo url('/aanbieder/get-feature-price') ?>",
						success: function (data) {

							if (current.parent().parent().next('.sub-features').length > 0) {
								var sub_impact_value = current.parent().parent().next('.sub-features').find('.f_price').val();
								total = total - sub_impact_value;
								price_before_labor = price_before_labor - sub_impact_value;
								current.parent().parent().next('.sub-features').remove();
							}

							if (data[1].length > 0) {
								var opt = '<option value="0">Select Feature</option>';

								$.each(data[1], function (index, value) {

									opt = opt + '<option value="' + value.id + '">' + value.title + '</option>';

								});

								current.parent().parent().after('<div class="row sub-features" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
										'<label style="margin-right: 10px;margin-bottom: 0;">' + heading + '</label>' +
										'<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features' + row_id + '[]">' + opt + '</select>\n' +
										'<input value="0" name="f_price' + row_id + '[]" class="f_price" type="hidden">' +
										'<input value="' + heading_id + '" name="f_id' + row_id + '[]" class="f_id" type="hidden">' +
										'<input value="0" name="f_area' + row_id + '[]" class="f_area" type="hidden">' +
										'<input value="1" name="sub_feature' + row_id + '[]" class="sub_feature" type="hidden">' +
										'</div></div>');
							}

							current.parent().find('.f_area').val(0);

							impact_value = 0;
							total = parseFloat(total) + parseFloat(impact_value);
							total = total.toFixed(2);

							//total = Math.round(total);
							price_before_labor = parseFloat(price_before_labor) + parseFloat(impact_value);
							price_before_labor = parseFloat(price_before_labor).toFixed(2);
							//price_before_labor = Math.round(price_before_labor);

							current.next('input').val(impact_value);

							$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(price_before_labor);
							$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + total.replace(/\./g, ','));
							$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(total);

							calculate_total();

							var flag = 0;

							var childsafe = $('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-select').val();

							if (!childsafe && childsafe != undefined) {
								flag = 1;
							}

							$("[name='features" + row_id + "[]']").each(function (i, obj) {

								var selected_feature = $(this).val();
								var feature_id = $(this).parent().find('.f_id').val();

								if (feature_id != 0) {
									if (selected_feature == 0) {
										flag = 1;
									}
								}

							});

							if(flag == 1)
							{
								$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
								$('#products_table').find(`[data-id='${row_id}']`).find('.yellow-circle').css('visibility','visible');
								$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();
							}
							else
							{
								$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').hide();
								$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').css('visibility','visible');
								$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').show();
							}
						}
					});
				}

			});

			$(document).on('change', '.childsafe-select', function () {

				var current = $(this);
				var row_id = current.parent().parent().parent().data('id');
				var feature_select = current.val();

				if(!feature_select)
				{
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
					$('#products_table').find(`[data-id='${row_id}']`).find('.yellow-circle').css('visibility','visible');
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();
				}
				else
				{
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').hide();
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').css('visibility','visible');
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').show();
				}

			});

			/*$('#myModal, #myModal2').on('hidden.bs.modal', function () {
                $('.top-bar').css('z-index','1000');
            });*/

			$(document).on('click', '.comment-btn', function () {

				var current = $(this);
				var row_id = current.parent().parent().data('id');
				var feature_id = current.data('feature');

				$('#myModal2').find('.modal-body').find('.comment-boxes').hide();

				if ($('#myModal2').find('.modal-body').find(`[data-id='${row_id}']`).find(`[data-id='${feature_id}']`).length > 0) {
					var box = $('#myModal2').find('.modal-body').find(`[data-id='${row_id}']`).find(`[data-id='${feature_id}']`);
					box.parent().show();
				}
				else {
					$('#myModal2').find('.modal-body').append(
							'<div class="comment-boxes" data-id="' + row_id + '">\n' +
							'<textarea style="resize: vertical;width: 100%;border: 1px solid #c9c9c9;border-radius: 5px;outline: none;" data-id="' + feature_id + '" rows="5" name="comment-' + row_id + '-' + feature_id + '"></textarea>\n' +
							'</div>'
					);
				}

				/*$('.top-bar').css('z-index','1');*/
				$('#myModal2').modal('toggle');
				$('.modal-backdrop').hide();

			});

			$(document).on('click', '.ladderband-btn', function () {

				var current = $(this);
				var row_id = current.data('id');

				$('#myModal').find('.modal-body').find('.sub-tables').hide();
				$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).show();

				/*$('.top-bar').css('z-index','1');*/
				$('#myModal').modal('toggle');
				$('.modal-backdrop').hide();

			});

			$(document).on('change', '.cus_radio', function () {

				var row_id = $(this).data('id');

				$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).find('.cus_radio').next('input').val(0);
				$(this).next('input').val(1);

			});

			function select_product(current,product_id,model_id,color_id,supplier_id,row_id,measure,box_quantity,max_width,quote_qty,secondary_title)
			{
				current.parents(".autocomplete").find(".pis_title").text(secondary_title);
				current.parents(".autocomplete").find(".pis_title").removeClass("hide");
				$('#products_table').find(`[data-id='${row_id}']`).find('#area_conflict').val(0);

				if(product_id.includes('S'))
				{
					var org_product_id = product_id;
					product_id = product_id.replace('S', '');
					var type = 'service';
				}
				else if(product_id.includes('I'))
				{
					var org_product_id = product_id;
					product_id = product_id.replace('I', '');
					var type = 'item';
				}
				else
				{
					var org_product_id = product_id;
					var type = 'product';
				}

				$('#products_table').find(`[data-id='${row_id}']`).find('.icon-container').removeClass("hide");

				$.ajax({
					type: "GET",
					data: "id=" + product_id + "&model=" + model_id + "&type=" + type,
					url: "<?php echo url('/aanbieder/get-colors') ?>",
					success: function (data) {

						current.parents('.products').find('#product_id').val(org_product_id);
						current.parents('.products').find('#supplier_id').val(supplier_id);
						current.parents('.products').find('#color_id').val(color_id);
						current.parents('.products').find('#model_id').val(model_id);
						current.parents('.products').find('#secondary_title').val(secondary_title);

						$('#menu1').find(`[data-id='${row_id}']`).remove();

						if (data != '') {

							if(type == 'product')
							{
								if(data.measure == 'M2' || data.measure == 'Per Piece')
								{
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m1_box').hide();
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m2_box').show();
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m2_totals').show();
								}
								else
								{
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m2_box').hide();
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m2_totals').hide();
									$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.m1_box').show();
								}

								if(data.estimated_price_per_box)
								{
									var estimated_price_per_box = data.estimated_price_per_box;
									// estimated_price_per_box = estimated_price_per_box.replace(/\./g, ',');
									var estimated_price_per_box_old = data.estimated_price_per_box;
								}
								else
								{
									var estimated_price_per_box = 0;
									var estimated_price_per_box_old = 0;
								}

								if(data.max_width == null)
								{
									data.max_width = 0;
								}

								estimated_price_per_box = parseFloat(estimated_price_per_box).toFixed(2);
								estimated_price_per_box_old = parseFloat(estimated_price_per_box_old).toFixed(2);

								$('#products_table').find(`[data-id='${row_id}']`).find('#max_width').val(data.max_width);
								$('#products_table').find(`[data-id='${row_id}']`).find('#measure').val(data.measure);
								$('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val(0);
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor-discount-box').find('.labor_discount_values').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val(estimated_price_per_box.replace(/\./g, ','));
								$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(estimated_price_per_box_old);
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact').val('');
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact_old').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#delivery_days').val(data.delivery_days);
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband').val(data.ladderband);
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_value').val(data.ladderband_value);
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_price_impact').val(data.ladderband_price_impact);
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_impact_type').val(data.ladderband_impact_type);
								$('#products_table').find(`[data-id='${row_id}']`).find('#base_price').val(data.base_price);
								$('#products_table').find(`[data-id='${row_id}']`).find('#estimated_price_quantity').val(data.estimated_price_quantity);
								$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.box_quantity_supplier').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
								$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.max_width').val(data.max_width);
								$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.box_qty_total').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
								$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#rate').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#basic_price').val('');
								$('#myModal2').find(`.comment-boxes[data-id='${row_id}']`).remove();
								$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + estimated_price_per_box);
								$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(estimated_price_per_box_old);
								$('#products_table').find(`[data-id='${row_id}']`).find('#rate').val(estimated_price_per_box_old);
								$('#products_table').find(`[data-id='${row_id}']`).find('.general_ledger').val(data.ledger);
								$('#products_table').find(`[data-id='${row_id}']`).find('.general_ledger').trigger('change.select2');

								var model = model_id;
								var color = color_id;
								var base_price = $('#products_table').find(`[data-id='${row_id}']`).find('#base_price').val();

								var product = product_id;
								var ladderband = $('#products_table').find(`[data-id='${row_id}']`).find('#ladderband').val();
								$('#products_table').find(`[data-id='${row_id}']`).find('#area_conflict').val(0);

								if (color && model && product) {

									var margin = 1;

									$('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val(0);
									// $('#products_table').find(`[data-id='${row_id}']`).find('.labor-discount-box').find('.labor_discount_values').val(0);
									$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val(0);
									$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val(0);

									$.ajax({
										type: "GET",
										data: "product=" + product + "&color=" + color + "&model=" + model + "&margin=" + margin + "&type=floors",
										url: "<?php echo url('/aanbieder/get-price') ?>",
										success: function (data) {

											$('#myModal2').find(`.comment-boxes[data-id='${row_id}']`).remove();

											$('#products_table').find(`[data-id='${row_id}']`).find('#childsafe').val(data[3].childsafe);
											var childsafe = data[3].childsafe;

											var features = '';
											var count_features = 0;
											var f_value = 0;
											var m1_impact = data[3].m1_impact;
											var m2_impact = data[3].m2_impact;
											var m1_impact_value = 0;
											var m2_impact_value = 0;

											$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).remove();

											if (childsafe == 1) {

												count_features = count_features + 1;

												var content = '<div class="row childsafe-content-box" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
														'<label style="margin-right: 10px;margin-bottom: 0;">Montagehoogte</label>' +
														'<input style="border: none;border-bottom: 1px solid lightgrey;" type="number" class="form-control childsafe_values" id="childsafe_x" name="childsafe_x' + row_id + '">\n' +
														'</div></div>\n' +
														'<div class="row childsafe-content-box1" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
														'<label style="margin-right: 10px;margin-bottom: 0;">Kettinglengte</label>' +
														'<input style="border: none;border-bottom: 1px solid lightgrey;" type="number" class="form-control childsafe_values" id="childsafe_y" name="childsafe_y' + row_id + '">\n' +
														'</div></div>\n' +
														'<div class="row childsafe-question-box" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
														'<label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Childsafe')}}</label>' +
														'<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control childsafe-select" name="childsafe_option' + row_id + '">\n' +
														'<option value="">{{__('text.Select any option')}}</option>\n' +
														'<option value="2">{{__('text.Add childsafety clip')}}</option>\n' +
														'</select>\n' +
														'<input value="0" name="childsafe_diff' + row_id + '" class="childsafe_diff" type="hidden">' +
														'</div></div>\n';

												features = features + content;

											}

											if (ladderband == 1) {

												var content = '<div class="row" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
														'<label style="margin-right: 10px;margin-bottom: 0;">{{__('text.Ladderband')}}</label>' +
														'<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features' + row_id + '[]">\n' +
														'<option value="0">{{__('text.No')}}</option>\n' +
														'<option value="1">{{__('text.Yes')}}</option>\n' +
														'</select>\n' +
														'<input value="0" name="f_price' + row_id + '[]" class="f_price" type="hidden">' +
														'<input value="0" name="f_id' + row_id + '[]" class="f_id" type="hidden">' +
														'<input value="0" name="f_area' + row_id + '[]" class="f_area" type="hidden">' +
														'<input value="0" name="sub_feature' + row_id + '[]" class="sub_feature" type="hidden">' +
														'</div><a data-id="' + row_id + '" class="info ladderband-btn hide">{{__('text.Info')}}</a></div>\n';

												features = features + content;

											}

											$.each(data[1], function (index, value) {

												count_features = count_features + 1;

												var opt = '<option value="0">{{__('text.Select Feature')}}</option>';

												$.each(value.features, function (index1, value1) {

													opt = opt + '<option value="' + value1.id + '">' + value1.title + '</option>';

												});

												if (value.comment_box == 1) {
													var icon = '<a data-feature="' + value.id + '" class="info comment-btn">{{__('text.Info')}}</a>';
												}
												else {
													var icon = '';
												}

												var content = '<div class="row" style="margin: 0;display: flex;align-items: center;"><div style="display: flex;align-items: center;font-family: Dlp-Brown,Helvetica Neue,sans-serif;font-size: 12px;" class="col-lg-11 col-md-11 col-sm-11 col-xs-11">\n' +
														'<label style="margin-right: 10px;margin-bottom: 0;">' + value.title + '</label>' +
														'<select style="border: none;border-bottom: 1px solid lightgrey;height: 30px;padding: 0;" class="form-control feature-select" name="features' + row_id + '[]">' + opt + '</select>\n' +
														'<input value="' + f_value + '" name="f_price' + row_id + '[]" class="f_price" type="hidden">' +
														'<input value="' + value.id + '" name="f_id' + row_id + '[]" class="f_id" type="hidden">' +
														'<input value="0" name="f_area' + row_id + '[]" class="f_area" type="hidden">' +
														'<input value="0" name="sub_feature' + row_id + '[]" class="sub_feature" type="hidden">' +
														'</div>' + icon + '</div>\n';

												features = features + content;

											});

											if(count_features > 0)
											{
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').css('visibility','visible');
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();
											}
											else
											{
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').hide();
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').css('visibility','visible');
												$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').show();
											}

											if ($('#menu1').find(`[data-id='${row_id}']`).length > 0) {
												$('#menu1').find(`[data-id='${row_id}']`).remove();
											}

											$('#menu1').append('<div data-id="' + row_id + '" style="margin: 0;" class="form-group">' + features + '</div>');

											current.parents('.content-div').find('.f_area').val(0);
										}
									});
								}
								else
								{
									$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
									$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').css('visibility','visible');
									$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();
								}

								if($('#request_quotation').val() == 0)
								{
									if(data.measure != measure)
									{
										$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.attribute-content-div').remove();
										add_attribute_row(false, row_id);
										$('#products_table').find(`[data-id='${row_id}']`).find('.qty').val(quote_qty);
									}
									else
									{
										if(measure == 'M1')
										{
											if(max_width != data.max_width)
											{
												$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find(`.attribute-content-div[data-main-id='0']`).each(function (i, obj) {

													$(this).find('.max_width').val(data.max_width);
													var row = $(this).data('id');
													calculator(row_id,row);

												});
											}
										}
										else if(measure == 'M2')
										{
											if(box_quantity != data.estimated_price_quantity)
											{
												$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.box_qty_total').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
												$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find(`.attribute-content-div[data-main-id='0']`).each(function (i, obj) {

													$(this).find('.box_quantity_supplier').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
													var row = $(this).data('id');
													calculator(row_id,row);

												});
											}
										}
									}
								}
								else
								{
									$('#products_table').find(`[data-id='${row_id}']`).find('.qty').val(quote_qty);
								}
							}
							else
							{
								if(data.sell_rate)
								{
									var estimated_price_per_box = parseFloat(data.sell_rate).toFixed(2);
									// estimated_price_per_box = estimated_price_per_box.replace(/\./g, ',');
									var estimated_price_per_box_old = data.sell_rate;
								}
								else
								{
									var estimated_price_per_box = 0;
									var estimated_price_per_box_old = 0;
								}

								estimated_price_per_box = parseFloat(estimated_price_per_box).toFixed(2);
								estimated_price_per_box_old = parseFloat(estimated_price_per_box_old).toFixed(2);

								$('#products_table').find(`[data-id='${row_id}']`).find('.qty').val(quote_qty);
								$('#products_table').find(`[data-id='${row_id}']`).find('#max_width').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#measure').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val(0);
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor-discount-box').find('.labor_discount_values').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val(estimated_price_per_box.replace(/\./g, ','));
								$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(estimated_price_per_box_old);
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact').val('');
								// $('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact_old').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#delivery_days').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_value').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_price_impact').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_impact_type').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#estimated_price_quantity').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('#basic_price').val('');
								$('#myModal2').find(`.comment-boxes[data-id='${row_id}']`).remove();
								$('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + estimated_price_per_box);
								$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(estimated_price_per_box_old);
								$('#products_table').find(`[data-id='${row_id}']`).find('#rate').val(estimated_price_per_box_old);
								$('#products_table').find(`[data-id='${row_id}']`).find('#area_conflict').val(0);
								$('#products_table').find(`[data-id='${row_id}']`).find('#childsafe').val('');
								$('#products_table').find(`[data-id='${row_id}']`).find('.general_ledger').val(data.ledger);
								$('#products_table').find(`[data-id='${row_id}']`).find('.general_ledger').trigger('change.select2');
								$('#myModal').find('.modal-body').find(`[data-id='${row_id}']`).remove();
								$('#menu1').find(`[data-id='${row_id}']`).remove();
								$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.attribute-content-div').remove();

							}

							calculate_total();
						}

						var windowsize = $(window).width();

						if (windowsize > 992) {

							$('#products_table').find(`[data-id='${row_id}']`).find('.collapse').collapse('show');

						}

						$('#products_table').find(`[data-id='${row_id}']`).find('.icon-container').addClass("hide");
					}
				});

				if(type != 'product')
				{
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').hide();
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').css('visibility','visible');
					$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').show();
				}
			}

			function autocomplete(inp, arr, values, model_ids, color_ids, supplier_ids, secondary_titles) {
				/*the autocomplete function takes two arguments,
                the text field element and an array of possible autocompleted values:*/
				var currentFocus;
				/*execute a function when someone writes in the text field:*/
				inp.addEventListener("input", function(e) {

					if($(inp).val() == "")
					{
						$(inp).parents(".autocomplete").find(".pis_title").val("");
						$(inp).parents(".autocomplete").find(".pis_title").addClass("hide");
						$(inp).parents(".products").find("#product_id").val("");
						$(inp).parents(".products").find("#supplier_id").val("");
						$(inp).parents(".products").find("#color_id").val("");
						$(inp).parents(".products").find("#model_id").val("");
						$(inp).parents(".products").find("#secondary_title").val("");
						$(inp).parents(".content-div").find("#childsafe").val(0);
						$(inp).parents(".content-div").find("#ladderband").val(0);
						$(inp).parents(".content-div").find("#ladderband_value").val(0);
						$(inp).parents(".content-div").find("#ladderband_price_impact").val(0);
						$(inp).parents(".content-div").find("#ladderband_impact_type").val(0);
						$(inp).parents(".content-div").find("#area_conflict").val(0);
						$(inp).parents(".content-div").find("#delivery_days").val("");
						$(inp).parents(".content-div").find("#supplier_margin").val("");
						$(inp).parents(".content-div").find("#retailer_margin").val("");
						$(inp).parents(".content-div").find("#measure").val("");
						$(inp).parents(".content-div").find("#max_width").val("");

						var row_id = $(inp).parents(".content-div").data('id');
						$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.attribute-content-div').remove();

					}

					var current = $(this);
					var a, b, i, val = this.value;
					/*close any already open lists of autocompleted values*/
					closeAllLists();
					if (!val) { return false;}
					currentFocus = -1;
					/*create a DIV element that will contain the items (values):*/
					a = document.createElement("DIV");
					a.setAttribute("id", this.id + "autocomplete-list");
					a.setAttribute("class", "autocomplete-items");
					/*append the DIV element as a child of the autocomplete container:*/
					this.parentNode.appendChild(a);

					var border_flag = 0;
					var found_flag = 0;

					if(arr.length == 0)
					{
						border_flag = 1;
					}

					/*for each item in the array...*/
					for (i = 0; i < arr.length; i++) {

						var string = arr[i];
						string = string.toLowerCase();
						val = val.toLowerCase();
						var res = string.includes(val);

						if (res) {

							found_flag = 1;

							/*create a DIV element for each matching element:*/
							b = document.createElement("DIV");
							/*make the matching letters bold:*/
							/*b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                            b.innerHTML += arr[i].substr(val.length);*/
							b.innerHTML = arr[i];
							/*insert a input field that will hold the current array item's value:*/
							b.innerHTML += "<input type='hidden' value='" + arr[i] + "'><input type='hidden' value='" + values[i] + "'><input type='hidden' value='" + model_ids[i] + "'><input type='hidden' value='" + color_ids[i] + "'><input type='hidden' value='" + supplier_ids[i] + "'><input type='hidden' value='" + secondary_titles[i] + "'>";
							/*execute a function when someone clicks on the item value (DIV element):*/

							b.addEventListener("click", function(e) {

								/*insert the value for the autocomplete text field:*/
								inp.value = this.getElementsByTagName("input")[0].value;
								var product_id = this.getElementsByTagName("input")[1].value;
								var model_id = this.getElementsByTagName("input")[2].value;
								var color_id = this.getElementsByTagName("input")[3].value;
								var supplier_id = this.getElementsByTagName("input")[4].value;
								var secondary_title = this.getElementsByTagName("input")[5].value;
								var row_id = current.parents(".content-div").data('id');
								var measure = current.parents(".content-div").find('#measure').val();
								var box_quantity = current.parents(".content-div").find('#estimated_price_quantity').val();
								var max_width = current.parents(".content-div").find('#max_width').val();

								select_product(current,product_id,model_id,color_id,supplier_id,row_id,measure,box_quantity,max_width,1,secondary_title);

								/*close the list of autocompleted values,
                                (or any other open lists of autocompleted values:*/
								closeAllLists();
							});
							a.appendChild(b);
						}
					}

					if(border_flag || !found_flag)
					{
						a.style.border = "0";
					}
				});
				/*execute a function presses a key on the keyboard:*/
				inp.addEventListener("keydown", function(e) {
					var x = document.getElementById(this.id + "autocomplete-list");
					if (x) x = x.getElementsByTagName("div");

					if (e.keyCode == 13 && e.shiftKey) {

						var el = this;
						var val1 = el.value;
						var selStart = el.selectionStart;
						el.value = val1.slice(0, selStart) + "\n" + val1.slice(el.selectionEnd);
						el.selectionEnd = el.selectionStart = selStart + "\n".length;

					}

					if (e.keyCode == 40) {
						/*If the arrow DOWN key is pressed,
                        increase the currentFocus variable:*/
						currentFocus++;
						/*and and make the current item more visible:*/
						addActive(x);
					} else if (e.keyCode == 38) { //up
						/*If the arrow UP key is pressed,
                        decrease the currentFocus variable:*/
						currentFocus--;
						/*and and make the current item more visible:*/
						addActive(x);
					} else if (e.keyCode == 13) {
						/*If the ENTER key is pressed, prevent the form from being submitted,*/
						e.preventDefault();
						if (currentFocus > -1) {
							/*and simulate a click on the "active" item:*/
							if (x)
							{
								if(x[currentFocus] != undefined)
								{
									x[currentFocus].click();
								}
							}
						}
					}
				});
				function addActive(x) {
					/*a function to classify an item as "active":*/
					if (!x) return false;
					/*start by removing the "active" class on all items:*/
					removeActive(x);
					if (currentFocus >= x.length) currentFocus = 0;
					if (currentFocus < 0) currentFocus = (x.length - 1);
					/*add class "autocomplete-active":*/
					if(x[currentFocus] != undefined)
					{
						x[currentFocus].classList.add("autocomplete-active");
					}
				}
				function removeActive(x) {
					/*a function to remove the "active" class from all autocomplete items:*/
					for (var i = 0; i < x.length; i++) {
						x[i].classList.remove("autocomplete-active");
					}
				}
				function closeAllLists(elmnt) {
					/*close all autocomplete lists in the document,
                    except the one passed as an argument:*/
					var x = document.getElementsByClassName("autocomplete-items");
					for (var i = 0; i < x.length; i++) {
						if (elmnt != x[i] && elmnt != inp) {
							x[i].parentNode.removeChild(x[i]);
						}
					}
				}
				/*execute a function when someone clicks in the document:*/
				document.addEventListener("click", function (e) {
					closeAllLists(e.target);
				});
			}

			product_ids = [];
			product_titles = [];
			model_ids = [];
			color_ids = [];
			supplier_ids = [];
			secondary_titles = [];

			var sel = $(".all-products");
			var length = sel.children('option').length;

			$(".all-products:first > option").each(function() {
				if (this.value) product_ids.push(this.value); product_titles.push(this.text); model_ids.push(this.getAttribute('data-model-id')); color_ids.push(this.getAttribute('data-color-id')); supplier_ids.push(this.getAttribute('data-supplier-id')); secondary_titles.push(this.getAttribute('data-title'));
			});

			var cls = document.getElementsByClassName("quote-product");
			for (n=0, length = cls.length; n < length; n++) {
				autocomplete(cls[n], product_titles, product_ids, model_ids, color_ids, supplier_ids, secondary_titles);
			}

		});
	</script>

	<link href="{{asset('assets/admin/css/main.css')}}" rel="stylesheet">
	<link href="{{asset('assets/admin/css/bootstrap-tagsinput.css')}}" rel="stylesheet">
	<link href="{{asset('assets/front/css/plannings.css')}}" rel="stylesheet">

@endsection
