@extends('layouts.handyman')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>

<div class="right-side">

	<div class="container-fluid">
		<div class="row">

			<form id="form-quote" style="padding: 0;" class="form-horizontal" action="{{route('store-new-order')}}" method="POST" enctype="multipart/form-data">
				{{csrf_field()}}

                @if(Route::currentRouteName() == 'edit-order')

                    <input type="hidden" name="supplier_id" value="{{$invoice[0]->supplier_id}}">

                @endif

				<input type="hidden" id="order_page" value="1">
                <input type="hidden" id="quotation_id" name="quotation_id" value="{{$check->quotation_id}}">
                <input type="hidden" name="customer" value="{{$check->customer_details}}">
                <input type="hidden" name="quotation_invoice_number" value="{{$check->quotation_invoice_number}}">
                <input type="hidden" name="created_at" value="{{$check->created_at}}">
                <input type="hidden" id="form_type" name="form_type" value="{{Route::currentRouteName() == 'view-order' ? 1 : 2}}">
                <input type="hidden" id="category" name="category" value="{{$check->form_type}}">
				<input type="hidden" id="retailer_delivery_date" name="retailer_delivery_date" value="{{$check->delivery_date}}">

				<div style="margin: 0;" class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<!-- Starting of Dashboard data-table area -->
						<div class="section-padding add-product-1" style="padding: 0;">

							<div style="margin: 0;" class="row">
								<div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div style="box-shadow: none;" class="add-product-box">
										<div style="align-items: center;" class="add-product-header products">

											@if(Route::currentRouteName() == 'edit-order')

												<h2 style="margin-top: 0;">{{__('text.Edit Order')}}</h2>

											@else

												<h2 style="margin-top: 0;">{{__('text.View Order')}}</h2>

											@endif

												@if(Auth::guard('user')->user()->role_id == 2)

													<div style="display: flex;">

														@if(Route::currentRouteName() == 'view-order' && (!$check->quote_request_id || $check->paid) && $check->accepted && !$check->processing && !$check->finished)

															<button type="button" class="btn btn-success send-new-order" data-id="{{$check->quotation_id}}" data-date="{{$check->delivery_date ? date('d-m-Y',strtotime($check->delivery_date)) : null}}" style="margin-right: 10px;">{{__('text.Send Order')}}</button>

														@endif

														<div style="background-color: black;border-radius: 10px;padding: 0 10px;">

															@if(!$check->processing && !$check->finished)

																<span class="tooltip1 save-data" style="cursor: pointer;font-size: 20px;margin-right: 10px;color: white;">
																	<i class="fa fa-fw fa-save"></i>
																	<span class="tooltiptext">{{__('text.Save')}}</span>
																</span>

															@endif

															<a href="{{route('customer-quotations')}}" class="tooltip1" style="cursor: pointer;font-size: 20px;color: white;">
																<i class="fa fa-fw fa-close"></i>
																<span class="tooltiptext">{{__('text.Close')}}</span>
															</a>

														</div>

													</div>

												@endif

										</div>

										<div style="display: inline-block;width: 100%;">

											@include('includes.form-success')

											<select style="display: none;" class="form-control all-products" id="blood_grp">

												@foreach($products as $key)

													@foreach($key->models as $key1)

														@foreach($key->colors as $key2)

															<option data-model="{{$key1->model}}" data-model-id="{{$key1->id}}" data-color="{{$key2->title}}" data-color-id="{{$key2->id}}" data-supplier-id="{{$key->user_id}}" value="{{$key->id}}">{{$key->title.', '.$key1->model.', '.$key2->title.', ('.$key->company_name.')'}}</option>

														@endforeach

													@endforeach

												@endforeach

											</select>

											<div style="padding-bottom: 0;" class="form-horizontal">

												<div style="margin: 0;border-top: 1px solid #eee;" class="row">

													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 second-row" style="padding-bottom: 15px;">

                                                        <section id="products_table" style="width: 100%;">

                                                            <div class="header-div">
                                                                <div class="headings" style="width: 3%;"></div>
																<div class="headings" style="width: 35%;">{{__('text.Product')}}</div>
																<div class="headings" style="width: 18%;">{{__('text.Qty')}}</div>
																<div class="headings" style="width: 18%;">{{__('text.€ Art.')}}</div>

																@if(Auth::guard('user')->user()->role_id == 2)

																	<div class="headings" style="width: 13%;">{{__('text.Discount')}}</div>

																@endif

																<div class="headings" @if(Auth::guard('user')->user()->role_id == 2) style="width: 13%;" @else style="width: 26%;" @endif></div>
                                                            </div>

															@foreach($invoice as $i => $item)

																<div @if($i==0) class="content-div active" @else class="content-div" @endif data-id="{{$i+1}}">

																	<div class="content full-res item1" style="width: 3%;">
																		<label class="content-label">Sr. No</label>
																		<div style="padding: 0 5px;" class="sr-res">{{$i+1}}</div>
																	</div>

																	<input type="hidden" value="{{$item->order_number}}" id="order_number" name="order_number[]">
																	<input type="hidden" value="{{$i+1}}" id="row_id" name="row_id[]">
																	<input type="hidden" value="{{$item->childsafe ? 1 : 0}}" id="childsafe" name="childsafe[]">
																	<input type="hidden" value="{{$item->ladderband ? 1 : 0}}" id="ladderband" name="ladderband[]">
																	<input type="hidden" value="{{$item->ladderband_value ? $item->ladderband_value : 0}}" id="ladderband_value" name="ladderband_value[]">
																	<input type="hidden" value="{{$item->ladderband_price_impact ? $item->ladderband_price_impact : 0}}" id="ladderband_price_impact" name="ladderband_price_impact[]">
																	<input type="hidden" value="{{$item->ladderband_impact_type ? $item->ladderband_impact_type : 0}}" id="ladderband_impact_type" name="ladderband_impact_type[]">
																	<input type="hidden" value="0" id="area_conflict" name="area_conflict[]">
																	<input type="hidden" value="{{$item->delivery_days}}" id="delivery_days" name="delivery_days[]">
                                                                    <input type="hidden" value="{{$item->box_quantity}}" id="estimated_price_quantity" name="estimated_price_quantity[]">
																	<input type="hidden" value="{{$item->measure}}" id="measure" name="measure[]">
																	<input type="hidden" value="{{$item->max_width}}" id="max_width" name="max_width[]">

																	<div style="width: 35%;" class="products content item3 full-res">

																		<label class="content-label">Product</label>

																		<div class="autocomplete" style="width:100%;">
																			<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product" placeholder="{{__('text.Select Product')}}">{{$product_titles[$i].', '.$model_titles[$i].', '.$color_titles[$i].', ('.$product_suppliers[$i]->company_name.')'}}</textarea>
																		</div>

																		<input type="hidden" value="{{$item->product_id}}" name="products[]" id="product_id">
																		<input type="hidden" value="{{$item->supplier_id}}" name="suppliers[]" id="supplier_id">
																		<input type="hidden" value="{{$item->color}}" name="colors[]" id="color_id">
																		<input type="hidden" value="{{$item->model_id}}" name="models[]" id="model_id">

																	</div>

																	<div class="content item6" style="width: 18%;">

																		<label class="content-label">Qty</label>

																		<div style="display: flex;align-items: center;height: 100%;">
																			<input type="text" value="{{str_replace('.', ',',floatval($item->qty))}}" maskedformat="9,1" name="qty[]" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">
																		</div>
																	</div>

																	<div class="content item6" style="width: 18%;">

																		<label class="content-label">€ Art.</label>

																		<div style="display: flex;align-items: center;">
																			<input type="text" value="{{number_format((float)$item->price_before_labor, 2, ',', '.')}}" readonly name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">
																			<input type="hidden" value="{{$item->price_before_labor}}" class="price_before_labor_old">
																		</div>
																	</div>

																	@if(Auth::guard('user')->user()->role_id == 2)

																		<div class="content item8" style="width: 13%;">

																			<label class="content-label">Discount</label>

																			<input type="text" value="{{$item->total_discount}}" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;height: 30px;" class="form-control total_discount res-white">
																			<input type="hidden" value="{{$item->qty != 0 ? $item->total_discount/$item->qty : 0}}" class="total_discount_old">
																		</div>

																	@endif

																	<div class="content item10 last-content" id="next-row-td" @if(Auth::guard('user')->user()->role_id == 2) style="padding: 0;width: 13%;" @else style="padding: 0;width: 26%;" @endif>

																		@if(Auth::guard('user')->user()->role_id == 2 && !$check->processing && !$check->finished)

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

																</div>

															@endforeach

                                                        </section>

													</div>

													<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: white;padding: 15px 0 0 0;">

														<ul style="border: 0;" class="nav nav-tabs feature-tab">
															
															<li style="margin-bottom: 0;" class="active"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu2" aria-expanded="false">{{__('text.Calculator')}}</a></li>

															<li style="margin-bottom: 0;"><a style="border: 0;padding: 10px 30px;" data-toggle="tab" href="#menu1" aria-expanded="false">{{__('text.Features')}}</a></li>
														
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
									<textarea
										style="resize: vertical;width: 100%;border: 1px solid #c9c9c9;border-radius: 5px;outline: none;"
										data-id="{{$feature->feature_id}}" rows="5"
										name="comment-{{$x + 1}}-{{$feature->feature_id}}">{{$feature->comment}}</textarea>
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

@include('user.send_order_modal')

<div id="cover"></div>

<style>

		.datepicker {
            padding: 4px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            direction: ltr;
        }
        .datepicker-inline {
            width: 220px;
        }
        .datepicker.datepicker-rtl {
            direction: rtl;
        }
        .datepicker.datepicker-rtl table tr td span {
            float: right;
        }
        .datepicker-dropdown {
            top: 0;
            min-width: 19.3% !important;
        }
        .table-condensed{
            width: 100%;
        }
        .datepicker td, .datepicker th
        {
            font-size: 17px;
        }
        .datepicker-dropdown:before {
            content: '';
            display: inline-block;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #999999;
            border-top: 0;
            border-bottom-color: rgba(0, 0, 0, 0.2);
            position: absolute;
        }
        .datepicker-dropdown:after {
            content: '';
            display: inline-block;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #ffffff;
            border-top: 0;
            position: absolute;
        }
        .datepicker-dropdown.datepicker-orient-left:before {
            left: 6px;
        }
        .datepicker-dropdown.datepicker-orient-left:after {
            left: 7px;
        }
        .datepicker-dropdown.datepicker-orient-right:before {
            right: 6px;
        }
        .datepicker-dropdown.datepicker-orient-right:after {
            right: 7px;
        }
        .datepicker-dropdown.datepicker-orient-bottom:before {
            display: none;
            top: -7px;
        }
        .datepicker-dropdown.datepicker-orient-bottom:after {
            display: none;
            top: -6px;
        }
        .datepicker-dropdown.datepicker-orient-top:before {
            display: none;
            bottom: -7px;
            border-bottom: 0;
            border-top: 7px solid #999999;
        }
        .datepicker-dropdown.datepicker-orient-top:after {
            display: none;
            bottom: -6px;
            border-bottom: 0;
            border-top: 6px solid #ffffff;
        }
        .datepicker > div {
            display: none;
        }
        .datepicker table {
            margin: 0;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
			border-spacing: 0;
        }
        .datepicker td,
        .datepicker th {
            text-align: center;
            width: 20px;
            height: 20px;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            border: none;
        }
        .table-striped .datepicker table tr td,
        .table-striped .datepicker table tr th {
            background-color: transparent;
        }
        .datepicker table tr td.day:hover,
        .datepicker table tr td.day.focused {
            background: #eeeeee;
            cursor: pointer;
        }
        .datepicker table tr td.old,
        .datepicker table tr td.new {
            color: #999999;
        }
        .datepicker table tr td.disabled,
        .datepicker table tr td.disabled:hover {
            background: none;
            color: #999999;
            cursor: default;
        }
        .datepicker table tr td.highlighted {
            background: #d9edf7;
            border-radius: 0;
        }
        .datepicker table tr td.today,
        .datepicker table tr td.today:hover,
        .datepicker table tr td.today.disabled,
        .datepicker table tr td.today.disabled:hover {
            background-color: #fde19a;
            background-image: -moz-linear-gradient(to bottom, #fdd49a, #fdf59a);
            background-image: -ms-linear-gradient(to bottom, #fdd49a, #fdf59a);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fdd49a), to(#fdf59a));
            background-image: -webkit-linear-gradient(to bottom, #fdd49a, #fdf59a);
            background-image: -o-linear-gradient(to bottom, #fdd49a, #fdf59a);
            background-image: linear-gradient(to bottom, #fdd49a, #fdf59a);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fdd49a', endColorstr='#fdf59a', GradientType=0);
            border-color: #fdf59a #fdf59a #fbed50;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            color: #000;
        }
        .datepicker table tr td.today:hover,
        .datepicker table tr td.today:hover:hover,
        .datepicker table tr td.today.disabled:hover,
        .datepicker table tr td.today.disabled:hover:hover,
        .datepicker table tr td.today:active,
        .datepicker table tr td.today:hover:active,
        .datepicker table tr td.today.disabled:active,
        .datepicker table tr td.today.disabled:hover:active,
        .datepicker table tr td.today.active,
        .datepicker table tr td.today:hover.active,
        .datepicker table tr td.today.disabled.active,
        .datepicker table tr td.today.disabled:hover.active,
        .datepicker table tr td.today.disabled,
        .datepicker table tr td.today:hover.disabled,
        .datepicker table tr td.today.disabled.disabled,
        .datepicker table tr td.today.disabled:hover.disabled,
        .datepicker table tr td.today[disabled],
        .datepicker table tr td.today:hover[disabled],
        .datepicker table tr td.today.disabled[disabled],
        .datepicker table tr td.today.disabled:hover[disabled] {
            background-color: #fdf59a;
        }
        .datepicker table tr td.today:active,
        .datepicker table tr td.today:hover:active,
        .datepicker table tr td.today.disabled:active,
        .datepicker table tr td.today.disabled:hover:active,
        .datepicker table tr td.today.active,
        .datepicker table tr td.today:hover.active,
        .datepicker table tr td.today.disabled.active,
        .datepicker table tr td.today.disabled:hover.active {
            background-color: #fbf069 \9;
        }
        .datepicker table tr td.today:hover:hover {
            color: #000;
        }
        .datepicker table tr td.today.active:hover {
            color: #fff;
        }
        .datepicker table tr td.range,
        .datepicker table tr td.range:hover,
        .datepicker table tr td.range.disabled,
        .datepicker table tr td.range.disabled:hover {
            background: #eeeeee;
            -webkit-border-radius: 0;
            -moz-border-radius: 0;
            border-radius: 0;
        }
        .datepicker table tr td.range.today,
        .datepicker table tr td.range.today:hover,
        .datepicker table tr td.range.today.disabled,
        .datepicker table tr td.range.today.disabled:hover {
            background-color: #f3d17a;
            background-image: -moz-linear-gradient(to bottom, #f3c17a, #f3e97a);
            background-image: -ms-linear-gradient(to bottom, #f3c17a, #f3e97a);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f3c17a), to(#f3e97a));
            background-image: -webkit-linear-gradient(to bottom, #f3c17a, #f3e97a);
            background-image: -o-linear-gradient(to bottom, #f3c17a, #f3e97a);
            background-image: linear-gradient(to bottom, #f3c17a, #f3e97a);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#f3c17a', endColorstr='#f3e97a', GradientType=0);
            border-color: #f3e97a #f3e97a #edde34;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            -webkit-border-radius: 0;
            -moz-border-radius: 0;
            border-radius: 0;
        }
        .datepicker table tr td.range.today:hover,
        .datepicker table tr td.range.today:hover:hover,
        .datepicker table tr td.range.today.disabled:hover,
        .datepicker table tr td.range.today.disabled:hover:hover,
        .datepicker table tr td.range.today:active,
        .datepicker table tr td.range.today:hover:active,
        .datepicker table tr td.range.today.disabled:active,
        .datepicker table tr td.range.today.disabled:hover:active,
        .datepicker table tr td.range.today.active,
        .datepicker table tr td.range.today:hover.active,
        .datepicker table tr td.range.today.disabled.active,
        .datepicker table tr td.range.today.disabled:hover.active,
        .datepicker table tr td.range.today.disabled,
        .datepicker table tr td.range.today:hover.disabled,
        .datepicker table tr td.range.today.disabled.disabled,
        .datepicker table tr td.range.today.disabled:hover.disabled,
        .datepicker table tr td.range.today[disabled],
        .datepicker table tr td.range.today:hover[disabled],
        .datepicker table tr td.range.today.disabled[disabled],
        .datepicker table tr td.range.today.disabled:hover[disabled] {
            background-color: #f3e97a;
        }
        .datepicker table tr td.range.today:active,
        .datepicker table tr td.range.today:hover:active,
        .datepicker table tr td.range.today.disabled:active,
        .datepicker table tr td.range.today.disabled:hover:active,
        .datepicker table tr td.range.today.active,
        .datepicker table tr td.range.today:hover.active,
        .datepicker table tr td.range.today.disabled.active,
        .datepicker table tr td.range.today.disabled:hover.active {
            background-color: #efe24b \9;
        }
        .datepicker table tr td.selected,
        .datepicker table tr td.selected:hover,
        .datepicker table tr td.selected.disabled,
        .datepicker table tr td.selected.disabled:hover {
            background-color: #9e9e9e;
            background-image: -moz-linear-gradient(to bottom, #b3b3b3, #808080);
            background-image: -ms-linear-gradient(to bottom, #b3b3b3, #808080);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#b3b3b3), to(#808080));
            background-image: -webkit-linear-gradient(to bottom, #b3b3b3, #808080);
            background-image: -o-linear-gradient(to bottom, #b3b3b3, #808080);
            background-image: linear-gradient(to bottom, #b3b3b3, #808080);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#b3b3b3', endColorstr='#808080', GradientType=0);
            border-color: #808080 #808080 #595959;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            color: #fff;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }
        .datepicker table tr td.selected:hover,
        .datepicker table tr td.selected:hover:hover,
        .datepicker table tr td.selected.disabled:hover,
        .datepicker table tr td.selected.disabled:hover:hover,
        .datepicker table tr td.selected:active,
        .datepicker table tr td.selected:hover:active,
        .datepicker table tr td.selected.disabled:active,
        .datepicker table tr td.selected.disabled:hover:active,
        .datepicker table tr td.selected.active,
        .datepicker table tr td.selected:hover.active,
        .datepicker table tr td.selected.disabled.active,
        .datepicker table tr td.selected.disabled:hover.active,
        .datepicker table tr td.selected.disabled,
        .datepicker table tr td.selected:hover.disabled,
        .datepicker table tr td.selected.disabled.disabled,
        .datepicker table tr td.selected.disabled:hover.disabled,
        .datepicker table tr td.selected[disabled],
        .datepicker table tr td.selected:hover[disabled],
        .datepicker table tr td.selected.disabled[disabled],
        .datepicker table tr td.selected.disabled:hover[disabled] {
            background-color: #808080;
        }
        .datepicker table tr td.selected:active,
        .datepicker table tr td.selected:hover:active,
        .datepicker table tr td.selected.disabled:active,
        .datepicker table tr td.selected.disabled:hover:active,
        .datepicker table tr td.selected.active,
        .datepicker table tr td.selected:hover.active,
        .datepicker table tr td.selected.disabled.active,
        .datepicker table tr td.selected.disabled:hover.active {
            background-color: #666666 \9;
        }
        .datepicker table tr td.active,
        .datepicker table tr td.active:hover,
        .datepicker table tr td.active.disabled,
        .datepicker table tr td.active.disabled:hover {
            background-color: #006dcc;
            background-image: -moz-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -ms-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
            background-image: -webkit-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -o-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: linear-gradient(to bottom, #0088cc, #0044cc);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0044cc', GradientType=0);
            border-color: #0044cc #0044cc #002a80;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            color: #fff;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }
        .datepicker table tr td.active:hover,
        .datepicker table tr td.active:hover:hover,
        .datepicker table tr td.active.disabled:hover,
        .datepicker table tr td.active.disabled:hover:hover,
        .datepicker table tr td.active:active,
        .datepicker table tr td.active:hover:active,
        .datepicker table tr td.active.disabled:active,
        .datepicker table tr td.active.disabled:hover:active,
        .datepicker table tr td.active.active,
        .datepicker table tr td.active:hover.active,
        .datepicker table tr td.active.disabled.active,
        .datepicker table tr td.active.disabled:hover.active,
        .datepicker table tr td.active.disabled,
        .datepicker table tr td.active:hover.disabled,
        .datepicker table tr td.active.disabled.disabled,
        .datepicker table tr td.active.disabled:hover.disabled,
        .datepicker table tr td.active[disabled],
        .datepicker table tr td.active:hover[disabled],
        .datepicker table tr td.active.disabled[disabled],
        .datepicker table tr td.active.disabled:hover[disabled] {
            background-color: #0044cc;
        }
        .datepicker table tr td.active:active,
        .datepicker table tr td.active:hover:active,
        .datepicker table tr td.active.disabled:active,
        .datepicker table tr td.active.disabled:hover:active,
        .datepicker table tr td.active.active,
        .datepicker table tr td.active:hover.active,
        .datepicker table tr td.active.disabled.active,
        .datepicker table tr td.active.disabled:hover.active {
            background-color: #003399 \9;
        }
        .datepicker table tr td span {
            display: block;
            width: 23%;
            height: 54px;
            line-height: 54px;
            float: left;
            margin: 1%;
            cursor: pointer;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }
        .datepicker table tr td span:hover {
            background: #eeeeee;
        }
        .datepicker table tr td span.disabled,
        .datepicker table tr td span.disabled:hover {
            background: none;
            color: #999999;
            cursor: default;
        }
        .datepicker table tr td span.active,
        .datepicker table tr td span.active:hover,
        .datepicker table tr td span.active.disabled,
        .datepicker table tr td span.active.disabled:hover {
            background-color: #006dcc;
            background-image: -moz-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -ms-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
            background-image: -webkit-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: -o-linear-gradient(to bottom, #0088cc, #0044cc);
            background-image: linear-gradient(to bottom, #0088cc, #0044cc);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0044cc', GradientType=0);
            border-color: #0044cc #0044cc #002a80;
            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            color: #fff;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        }
        .datepicker table tr td span.active:hover,
        .datepicker table tr td span.active:hover:hover,
        .datepicker table tr td span.active.disabled:hover,
        .datepicker table tr td span.active.disabled:hover:hover,
        .datepicker table tr td span.active:active,
        .datepicker table tr td span.active:hover:active,
        .datepicker table tr td span.active.disabled:active,
        .datepicker table tr td span.active.disabled:hover:active,
        .datepicker table tr td span.active.active,
        .datepicker table tr td span.active:hover.active,
        .datepicker table tr td span.active.disabled.active,
        .datepicker table tr td span.active.disabled:hover.active,
        .datepicker table tr td span.active.disabled,
        .datepicker table tr td span.active:hover.disabled,
        .datepicker table tr td span.active.disabled.disabled,
        .datepicker table tr td span.active.disabled:hover.disabled,
        .datepicker table tr td span.active[disabled],
        .datepicker table tr td span.active:hover[disabled],
        .datepicker table tr td span.active.disabled[disabled],
        .datepicker table tr td span.active.disabled:hover[disabled] {
            background-color: #0044cc;
        }
        .datepicker table tr td span.active:active,
        .datepicker table tr td span.active:hover:active,
        .datepicker table tr td span.active.disabled:active,
        .datepicker table tr td span.active.disabled:hover:active,
        .datepicker table tr td span.active.active,
        .datepicker table tr td span.active:hover.active,
        .datepicker table tr td span.active.disabled.active,
        .datepicker table tr td span.active.disabled:hover.active {
            background-color: #003399 \9;
        }
        .datepicker table tr td span.old,
        .datepicker table tr td span.new {
            color: #999999;
        }
        .datepicker .datepicker-switch {
            width: 145px;
        }
        .datepicker .datepicker-switch,
        .datepicker .prev,
        .datepicker .next,
        .datepicker tfoot tr th {
            cursor: pointer;
        }
        .datepicker .datepicker-switch:hover,
        .datepicker .prev:hover,
        .datepicker .next:hover,
        .datepicker tfoot tr th:hover {
            background: #eeeeee;
        }
        .datepicker .cw {
            font-size: 10px;
            width: 12px;
            padding: 0 2px 0 5px;
            vertical-align: middle;
        }
        .input-append.date .add-on,
        .input-prepend.date .add-on {
            cursor: pointer;
        }
        .input-append.date .add-on i,
        .input-prepend.date .add-on i {
            margin-top: 3px;
        }
        .input-daterange input {
            text-align: center;
        }
        .input-daterange input:first-child {
            -webkit-border-radius: 3px 0 0 3px;
            -moz-border-radius: 3px 0 0 3px;
            border-radius: 3px 0 0 3px;
        }
        .input-daterange input:last-child {
            -webkit-border-radius: 0 3px 3px 0;
            -moz-border-radius: 0 3px 3px 0;
            border-radius: 0 3px 3px 0;
        }
        .input-daterange .add-on {
            display: inline-block;
            width: auto;
            min-width: 16px;
            height: 18px;
            padding: 4px 5px;
            font-weight: normal;
            line-height: 18px;
            text-align: center;
            text-shadow: 0 1px 0 #ffffff;
            vertical-align: middle;
            background-color: #eeeeee;
            border: 1px solid #ccc;
            margin-left: -5px;
            margin-right: -5px;
        }

        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting{

            padding-right: 0;
            padding-left: 0;
            text-align: center;
            border-top: 1px solid black !important;
            border-bottom: 1px solid black !important;
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
			display:none !important;
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
			'item16 item16 item16 item16 item16 item16'
			'item12 item12 item12 item12 item12 item12'
			'item13 item13 item13 item13 item13 item13'
			'item14 item14 item14 item14 item14 item14'
			'item15 item15 item15 item15 item15 item15'
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

		.color .select2-container--default .select2-selection--single, .model .select2-container--default .select2-selection--single, .deliver_to_box .select2-container--default .select2-selection--single
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

		:is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__rendered, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__arrow, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__rendered
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

		:not(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single
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

    .header-div, .content-div, .attribute-content-div
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

    .content-div, .attribute-content-div
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
		background: url(<?php echo asset('assets/images/page-loader.gif');
		?>) no-repeat scroll center center #ffffff78;
		position: fixed;
		z-index: 100000;
		height: 100%;
		width: 100%;
		margin: auto;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		background-size: 8%;
		display: none;
	}

	.pac-container {
		z-index: 1000000;
	}

	#cus-box .select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 28px;
	}

	#cus-box .select2-container--default .select2-selection--single, .deliver_to_box .select2-container--default .select2-selection--single {
		border: 1px solid #cacaca;
	}

	#cus-box .select2-selection {
		height: 40px !important;
		padding-top: 5px !important;
		outline: none;
	}

	#cus-box .select2-selection__arrow {
		top: 7.5px !important;
	}

	#cus-box .select2-selection__clear {
		display: none;
	}

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

	:not(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single {
		border: 0;
	}

    :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__rendered, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__arrow, :is(.color, .model, .deliver_to_box) > .select2-container--default .select2-selection--single .select2-selection__rendered
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

	table tr th {
		font-family: system-ui;
		font-weight: 500;
		border-bottom: 1px solid #ebebeb;
		padding-bottom: 15px;
		color: gray;
	}

	table tbody tr td {
		font-family: system-ui;
		font-weight: 500;
		padding: 0 10px;
		color: #3c3c3c;
	}

	table tbody tr.active td {
		border-top: 2px solid #cecece;
		border-bottom: 2px solid #cecece;
	}

	table tbody tr.active td:first-child {
		border-left: 2px solid #cecece;
		border-bottom-left-radius: 4px;
		border-top-left-radius: 4px;
	}

	table tbody tr.active td:last-child {
		border-right: 2px solid #cecece;
		border-bottom-right-radius: 4px;
		border-top-right-radius: 4px;
	}

	table {
		border-collapse: separate;
		border-spacing: 0 1em;
	}


	.modal-body table tr th {
		border: 1px solid #ebebeb;
		padding-bottom: 15px;
		color: gray;
	}

	.modal-body table tbody tr td {
		border-left: 1px solid #ebebeb;
		border-right: 1px solid #ebebeb;
		border-bottom: 1px solid #ebebeb;
	}

	.modal-body table tbody tr td:first-child {
		border-right: 0;
	}

	.modal-body table tbody tr td:last-child {
		border-left: 0;
	}

	.modal-body table {
		border-collapse: separate;
		border-spacing: 0;
		margin: 20px 0;
	}

	.modal-body table tbody tr td,
	.modal-body table thead tr th {
		padding: 5px 10px;
	}

</style>
@include("user.modals_css")

@endsection

@section('scripts')

@include("user.modals_js")

<script type="text/javascript">
	
	$(document).ready(function () {

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

		function add_row(copy = false, product = null, product_text = null, supplier = null, color = null, model = null, features = null, features_selects = null, childsafe_question = null, childsafe_answer = null, qty = null, childsafe = 0, ladderband = 0, ladderband_value = 0, ladderband_price_impact = 0, ladderband_impact_type = 0, area_conflict = 0, subs = null, childsafe_x = null, childsafe_y = null, delivery_days = null, price_before_labor = null, price_before_labor_old = null, total_discount = null, total_discount_old= null, last_column = null, menu2 = null, estimated_price_quantity = null, turns = null, measure = null, max_width = null) {

			var rowCount = $('#products_table .content-div:last').data('id');
			rowCount = rowCount + 1;

			var r_id = $('#products_table .content-div:last').find('.content:eq(0)').find('.sr-res').text();
			r_id = parseInt(r_id) + 1;

			if (!copy) {

				$("#products_table").append('<div class="content-div" data-id="' + rowCount + '">\n' +
					'                                                            <div class="content full-res item1" style="width: 3%;">\n' +
                    '                       									 	<label class="content-label">Sr. No</label>\n' +
					'                       									 	<div style="padding: 0 5px;" class="sr-res">' + r_id + '</div>\n' +
					'                       									 </div>\n' +
					'\n' +
					'                                                            <input type="hidden" id="order_number" name="order_number[]">\n' +
					'                                                            <input type="hidden" value="' + rowCount + '" id="row_id" name="row_id[]">\n' +
					'                                                            <input type="hidden" value="0" id="childsafe" name="childsafe[]">\n' +
					'                                                            <input type="hidden" value="0" id="ladderband" name="ladderband[]">\n' +
					'                                                            <input type="hidden" value="0" id="ladderband_value" name="ladderband_value[]">\n' +
					'                                                            <input type="hidden" value="0" id="ladderband_price_impact" name="ladderband_price_impact[]">\n' +
					'                                                            <input type="hidden" value="0" id="ladderband_impact_type" name="ladderband_impact_type[]">\n' +
					'                                                            <input type="hidden" value="0" id="area_conflict" name="area_conflict[]">\n' +
					'                                                            <input type="hidden" value="1" id="delivery_days" name="delivery_days[]">\n' +
					'                                                            <input type="hidden" id="estimated_price_quantity" name="estimated_price_quantity[]">\n' +
					'                                                            <input type="hidden" id="measure" name="measure[]">\n' +
					'                                                            <input type="hidden" id="max_width" name="max_width[]">\n' +
					'\n' +
					'                                                            <div style="width: 35%;" class="products content item3 full-res">\n' +
					'\n' +
					'                       									 	<label class="content-label">Product</label>\n' +
					'\n' +
					'                                                                <div class="autocomplete" style="width:100%;">\n' +
					'\n' +
					'																	<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product" placeholder="{{__('text.Select Product')}}"></textarea>\n' +
					'\n' +
					'                                                                </div>\n' +
					'\n' +
					'																<input type="hidden" name="products[]" id="product_id">\n' +
					'																<input type="hidden" name="suppliers[]" id="supplier_id">\n' +
					'																<input type="hidden" name="colors[]" id="color_id">\n' +
					'																<input type="hidden" name="models[]" id="model_id">\n' +
					'\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item6" style="width: 18%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">Qty</label>\n' +
					'\n' +
					'																 <div style="display: flex;align-items: center;height: 100%;">\n' +
					'																 	<input type="text" name="qty[]" maskedFormat="9,1" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">\n' +
					'																 </div>\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item7" style="width: 18%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">€ Art.</label>\n' +
					'\n' +
					'																 <div style="display: flex;align-items: center;">\n' +
					'																 	<input type="text" readonly name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">\n' +
					'																	<input type="hidden" class="price_before_labor_old">\n' +
					'																 </div>\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item8" style="width: 13%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">Discount</label>\n' +
					'\n' +
					'																<input type="text" value="0" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;" class="form-control total_discount res-white">\n' +
					'																<input type="hidden" value="0" class="total_discount_old">\n' +
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

				focus_row(last_row);

				autocomplete(last_row.find("#productInput")[0], product_titles, product_ids, model_ids, color_ids, supplier_ids);

			}
			else {

				$("#products_table").append('<div class="content-div" data-id="' + rowCount + '">\n' +
					'                                                            <div class="content full-res item1" style="width: 3%;">\n' +
                    '                       									 	<label class="content-label">Sr. No</label>\n' +
					'                       									 	<div style="padding: 0 5px;" class="sr-res">' + r_id + '</div>\n' +
					'                       									 </div>\n' +
					'\n' +
					'                                                            <input type="hidden" id="order_number" name="order_number[]">\n' +
					'                                                            <input type="hidden" value="' + rowCount + '" id="row_id" name="row_id[]">\n' +
					'                                                            <input type="hidden" value="' + childsafe + '" id="childsafe" name="childsafe[]">\n' +
					'                                                            <input type="hidden" value="' + ladderband + '" id="ladderband" name="ladderband[]">\n' +
					'                                                            <input type="hidden" value="' + ladderband_value + '" id="ladderband_value" name="ladderband_value[]">\n' +
					'                                                            <input type="hidden" value="' + ladderband_price_impact + '" id="ladderband_price_impact" name="ladderband_price_impact[]">\n' +
					'                                                            <input type="hidden" value="' + ladderband_impact_type + '" id="ladderband_impact_type" name="ladderband_impact_type[]">\n' +
					'                                                            <input type="hidden" value="' + area_conflict + '" id="area_conflict" name="area_conflict[]">\n' +
					'                                                            <input type="hidden" value="' + delivery_days + '" id="delivery_days" name="delivery_days[]">\n' +
					'                                                            <input type="hidden" value="' + estimated_price_quantity + '" id="estimated_price_quantity" name="estimated_price_quantity[]">\n' +
					'                                                            <input type="hidden" value="' + measure + '" id="measure" name="measure[]">\n' +
					'                                                            <input type="hidden" value="' + max_width + '" id="max_width" name="max_width[]">\n' +
					'\n' +
					'                                                            <div style="width: 35%;" class="products content item3 full-res">\n' +
					'\n' +
					'                       									 	<label class="content-label">Product</label>\n' +
					'\n' +
					'                                                                <div class="autocomplete" style="width:100%;">\n' +
					'\n' +
					'																	<textarea style="resize: vertical;word-break: break-word;" id="productInput" autocomplete="off" class="form-control quote-product" name="product" placeholder="{{__('text.Select Product')}}">'+product_text+'</textarea>\n' +
					'\n' +
					'                                                                </div>\n' +
					'\n' +
					'																<input type="hidden" name="products[]" id="product_id" value="'+product+'">\n' +
					'																<input type="hidden" name="suppliers[]" id="supplier_id" value="'+supplier+'">\n' +
					'																<input type="hidden" name="colors[]" id="color_id" value="'+color+'">\n' +
					'																<input type="hidden" name="models[]" id="model_id" value="'+model+'">\n' +
					'\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item6" style="width: 18%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">Qty</label>\n' +
					'\n' +
					'																 <div style="display: flex;align-items: center;height: 100%;">\n' +
					'																 	<input value="' + qty + '" type="text" name="qty[]" maskedFormat="9,1" style="border: 0;background: transparent;padding: 0 5px;height: 100%;" class="form-control qty res-white">\n' +
					'																 </div>\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item7" style="width: 18%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">€ Art.</label>\n' +
					'\n' +
					'																 <div style="display: flex;align-items: center;">\n' +
					'																 	<input value="' + price_before_labor + '" type="text" readonly name="price_before_labor[]" style="border: 0;background: transparent;padding: 0 5px;" class="form-control price_before_labor res-white">\n' +
					'																	<input value="' + price_before_labor_old + '" type="hidden" class="price_before_labor_old">\n' +
					'																 </div>\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item8" style="width: 13%;">\n' +
					'\n' +
					'                       									 	<label class="content-label">Discount</label>\n' +
					'\n' +
					'																<input type="text" value="' + total_discount + '" name="total_discount[]" readonly style="border: 0;background: transparent;padding: 0 5px;" class="form-control total_discount res-white">\n' +
					'																<input type="hidden" value="' + total_discount_old + '" class="total_discount_old">\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                            <div class="content item10 last-content" id="next-row-td" style="padding: 0;width: 13%;">\n' +
					'\n' +
					last_column +
					'\n' +
					'                                                            </div>\n' +
					'\n' +
					'                                                        </div>');

				var last_row = $('#products_table .content-div:last');

				autocomplete(last_row.find("#productInput")[0], product_titles, product_ids, model_ids, color_ids, supplier_ids);

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
			}

		});

		$(document).on('click', '.save-data', function () {

			var flag = 0;

			$("[name='products[]']").each(function (i, obj) {

				if (!obj.value) {
					flag = 1;
					$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				}
				else {
					$(obj).parents('.products').find('#productInput').css('border', '0');
				}

			});

			$("[name='colors[]']").each(function (i, obj) {

				if (!obj.value) {
					flag = 1;
					$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				}
				else {
					$(obj).parents('.products').find('#productInput').css('border', '0');
				}

			});

			$("[name='models[]']").each(function (i, obj) {

				if (!obj.value) {
					flag = 1;
					$(obj).parents('.products').find('#productInput').css('border', '1px solid red');
				}
				else {
					$(obj).parents('.products').find('#productInput').css('border', '0');
				}

			});

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
			var product = current.find('#product_id').val();
			var product_text = current.find('#productInput').val();
			var supplier = current.find('#supplier_id').val();
			var color = current.find('#color_id').val();
			var model = current.find('#model_id').val();
			var features = $('#menu1').find(`[data-id='${id}']`).html();
			var childsafe_question = $('#menu1').find(`[data-id='${id}']`).find('.childsafe-select').val();
			var childsafe_answer = $('#menu1').find(`[data-id='${id}']`).find('.childsafe-answer').val();
			var features_selects = $('#menu1').find(`[data-id='${id}']`).find('.feature-select');
			var qty = current.find('.qty').val();
			var subs = $('#myModal').find('.modal-body').find(`[data-id='${id}']`).html();
			var childsafe_x = $('#menu1').find(`[data-id='${id}']`).find('#childsafe_x').val();
			var childsafe_y = $('#menu1').find(`[data-id='${id}']`).find('#childsafe_y').val();
			var price_before_labor = current.find('.price_before_labor').val();
			var price_before_labor_old = current.find('.price_before_labor_old').val();
			// var labor_discount = current.find('.labor-discount-box').find('.labor_discount_values').val();
            var total_discount = current.find('.total_discount').val();
            var total_discount_old = current.find('.total_discount_old').val();
			var last_column = current.find('#next-row-td').html();
			var menu2 = $('#menu2').find(`.attributes_table[data-id='${id}']`).children().clone();
			var turns = $('#menu2').find(`.attributes_table[data-id='${id}']`).find('.turn');
			var estimated_price_quantity = current.find('#estimated_price_quantity').val();
			var measure = current.find('#measure').val();
			var max_width = current.find('#max_width').val();

			add_row(true, product, product_text, supplier, color, model, features, features_selects, childsafe_question, childsafe_answer, qty, childsafe, ladderband, ladderband_value, ladderband_price_impact, ladderband_impact_type, area_conflict, subs, childsafe_x, childsafe_y, delivery_days, price_before_labor, price_before_labor_old, total_discount, total_discount_old, last_column, menu2, estimated_price_quantity, turns, measure, max_width);

		});

		$(document).on('keypress', "input[name='qty[]'], .total_boxes", function (e) {

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

			var num = $(this).attr("maskedFormat").toString().split(',');
			var regex = new RegExp("^\\d{0," + num[0] + "}(\\,\\d{0," + num[1] + "})?$");
			if (!regex.test(this.value)) {
				this.value = this.value.substring(0, this.value.length - 1);
			}
			else{
					if(e.which == 46)
					{
						this.value = this.value + String.fromCharCode(44);
						e.preventDefault();
						return false;
					}
			}

		});

		$(document).on('keypress', ".childsafe_values, .discount_values", function (e) {

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

		$(document).on('keypress', ".width, .height", function (e) {
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

		$(document).on('focusout', "input[name='qty[]'], .total_boxes", function (e) {

			if (!$(this).val()) {
				$(this).val(0);
			}

			if ($(this).val().slice($(this).val().length - 1) == ',') {
				var val = $(this).val();
				val = val + '00';
				$(this).val(val);
			}

		});

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

					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').append('<option value="1" selected>{{__('text.Please note not childsafe')}}</option><option value="2">{{__('text.Add childsafety clip')}}</option>');

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

					$('#menu1').find(`[data-id='${row_id}']`).find('.childsafe-question-box').find('.childsafe-select').append('<option value="2">{{__('text.Add childsafety clip')}}</option><option value="3" selected>{{__('text.Yes childsafe')}}</option>');

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
					text: 'Kindly fill both childsafe values first.',
				});
			}

		});

        $(document).on('change', '.feature-select', function () {

            var current = $(this);
            var row_id = current.parent().parent().parent().data('id');
            var feature_select = current.val();
            var id = current.parent().find('.f_id').val();
            var product_id = $('#products_table').find(`[data-id='${row_id}']`).find('.products').find('select').val();

            if (id == 0) {

                if (feature_select == 1) {

                    $.ajax({
                        type: "GET",
                        data: "product_id=" + product_id,
                        url: "<?php echo url('/aanbieder/get-sub-products-sizes')?>",
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

                }
            }
            else {
                var heading = current.find("option:selected").text();
                var heading_id = current.val();

                $.ajax({
                    type: "GET",
                    data: "id=" + feature_select,
                    url: "<?php echo url('/aanbieder/get-feature-price')?>",
                    success: function (data) {

                        if (current.parent().parent().next('.sub-features').length > 0) {

                            current.parent().parent().next('.sub-features').remove();

                        }

                        if (data[1].length > 0) {

                            var opt = '<option value="0">{{__('text.Select Feature')}}</option>';

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


		function autocomplete(inp, arr, values, model_ids, color_ids, supplier_ids) {
			/*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
			var currentFocus;
			/*execute a function when someone writes in the text field:*/
			inp.addEventListener("input", function(e) {

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
						b.innerHTML += "<input type='hidden' value='" + arr[i] + "'><input type='hidden' value='" + values[i] + "'><input type='hidden' value='" + model_ids[i] + "'><input type='hidden' value='" + color_ids[i] + "'><input type='hidden' value='" + supplier_ids[i] + "'>";
						/*execute a function when someone clicks on the item value (DIV element):*/
						b.addEventListener("click", function(e) {

							/*insert the value for the autocomplete text field:*/
							inp.value = this.getElementsByTagName("input")[0].value;
							var product_id = this.getElementsByTagName("input")[1].value;
							var model_id = this.getElementsByTagName("input")[2].value;
							var color_id = this.getElementsByTagName("input")[3].value;
							var supplier_id = this.getElementsByTagName("input")[4].value;
							var row_id = current.parents(".content-div").data('id');
							var measure = current.parents(".content-div").find('#measure').val();
							var box_quantity = current.parents(".content-div").find('#estimated_price_quantity').val();
							var max_width = current.parents(".content-div").find('#max_width').val();
							$('#products_table').find(`[data-id='${row_id}']`).find('#area_conflict').val(0);

							$.ajax({
								type: "GET",
								data: "id=" + product_id + "&model=" + model_id,
								url: "<?php echo url('/aanbieder/get-colors')?>",
								success: function (data) {

									current.parents('.products').find('#product_id').val(product_id);
									current.parents('.products').find('#supplier_id').val(supplier_id);
									current.parents('.products').find('#color_id').val(color_id);
									current.parents('.products').find('#model_id').val(model_id);

									$('#menu1').find(`[data-id='${row_id}']`).remove();

									if (data != '') {

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
										$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(estimated_price_per_box));
										$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val(estimated_price_per_box_old);
										$('#products_table').find(`[data-id='${row_id}']`).find('#delivery_days').val(data.delivery_days);
										$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband').val(data.ladderband);
										$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_value').val(data.ladderband_value);
										$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_price_impact').val(data.ladderband_price_impact);
										$('#products_table').find(`[data-id='${row_id}']`).find('#ladderband_impact_type').val(data.ladderband_impact_type);
										$('#products_table').find(`[data-id='${row_id}']`).find('#estimated_price_quantity').val(data.estimated_price_quantity);
										$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.box_quantity_supplier').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
										$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.max_width').val(data.max_width);
										$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.box_qty_total').val(data.estimated_price_quantity != '' ? data.estimated_price_quantity.replace(/\./g, ',') : '');
										$('#myModal2').find(`.comment-boxes[data-id='${row_id}']`).remove();

										var model = model_id;
										var color = color_id;

										var product = product_id;
										var ladderband = $('#products_table').find(`[data-id='${row_id}']`).find('#ladderband').val();
										$('#products_table').find(`[data-id='${row_id}']`).find('#area_conflict').val(0);

										if (color && model && product) {

											var margin = 1;

											$('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val(0);

											$.ajax({
												type: "GET",
												data: "product=" + product + "&color=" + color + "&model=" + model + "&margin=" + margin + "&type=floors",
												url: "<?php echo url('/aanbieder/get-price')?>",
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

										if(data.measure != measure)
										{
											$('#menu2').find(`.attributes_table[data-id='${row_id}']`).find('.attribute-content-div').remove();
											add_attribute_row(false, row_id);
											$('#products_table').find(`[data-id='${row_id}']`).find('.qty').val(1);
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

														$(this).find('.box_quantity_supplier').val(data.estimated_price_quantity);
														var row = $(this).data('id');
														calculator(row_id,row);
													
													});
												}
											}
										}
									}

									var windowsize = $(window).width();

									if (windowsize > 992) {

										$('#products_table').find(`[data-id='${row_id}']`).find('.collapse').collapse('show');

									}

								}
							});

							$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.green-circle').hide();
							$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').css('visibility','visible');
							$('#products_table').find(`[data-id='${row_id}']`).find('#next-row-td').find('.yellow-circle').show();

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

		var sel = $(".all-products");
		var length = sel.children('option').length;

		$(".all-products:first > option").each(function() {
			if (this.value) product_ids.push(this.value); product_titles.push(this.text); model_ids.push(this.getAttribute('data-model-id')); color_ids.push(this.getAttribute('data-color-id')); supplier_ids.push(this.getAttribute('data-supplier-id'));
		});

		var cls = document.getElementsByClassName("quote-product"); 
        for (n=0, length = cls.length; n < length; n++) {
            autocomplete(cls[n], product_titles, product_ids, model_ids, color_ids, supplier_ids);
        }

	});
</script>

@endsection
