<div style="overflow-y: auto;" id="addAppointmentModal" role="dialog" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" data-dismiss="modal" class="close">×</button>
                <h4 class="modal-title">{{__('text.Add Appointment')}}</h4>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-xs-12 col-sm-12 required appointment_title_box">
                        <label>{{__('text.Title')}}</label>
                        <select class="appointment_title">

                            <option value="">{{__('text.Select Event Title')}}</option>
                            <option data-text="{{__('text.Delivery Date')}}" value="Delivery Date">{{__('text.Delivery Date')}}</option>
                            <option data-text="{{__('text.Installation Date')}}" value="Installation Date">{{__('text.Installation Date')}}</option>

                            @foreach($event_titles as $title)

                                <option data-text="{{$title->title}}" value="{{$title->title}}">{{$title->title}}</option>

                            @endforeach

                        </select>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 appointment_status_box">
                        <label>{{__('text.Status')}}</label>
                        <select class="appointment_status">

                            <option value="">{{__('text.Select Event Status')}}</option>

                            @foreach($event_statuses as $status)

                                <option data-id="{{$status->id}}" data-bgColor="{{$status->bg_color}}" value="{{$status->title}}">{{$status->title}}</option>

                            @endforeach

                        </select>
                    </div>

                    <div class="form-group responsible_box col-xs-12 col-sm-12">
                        <label>{{__('text.Responsible Person')}}</label>
                        <select class="appointment_responsible">

                            <option value="">{{__('text.Select Responsible Person')}}</option>

                            @foreach($responsible_persons as $key)

                                <option data-color="{{$key->agenda_font_color}}" data-fname="{{$key->name}}" data-lname="{{$key->family_name}}" value="{{$key->id}}">{{$key->name . ' ' . $key->family_name}}</option>

                            @endforeach

                        </select>
                    </div>

                    <div class="form-group col-xs-12 col-sm-4 required">
                        <label>{{__('text.Start')}}</label>
                        <input type="text" class="form-control appointment_start validation_required" readonly="readonly">
                    </div>

                    <div class="form-group col-xs-12 col-sm-4 required">
                        <label>{{__('text.End')}}</label>
                        <input type="text" class="form-control appointment_end validation_required" readonly="readonly">
                    </div>

                    <div class="form-group col-xs-12 col-sm-4 appointment_type_box required">
                        <label>{{__('text.Select Type')}}</label>
                        <select class="appointment_type">

                            <option value="1">{{__('text.For Quotation')}}</option>
                            <option value="2">{{__('text.For Client')}}</option>
                            <option value="3">{{__('text.For Supplier')}}</option>
                            <option value="4">{{__('text.For Employee')}}</option>

                        </select>
                    </div>

                    @if(Route::currentRouteName() != "plannings")

                        <?php if(isset($invoice)){ $client = $clients->where('id',$invoice[0]->customer_details)->first(); } ?>

                        <div class="form-group col-xs-12 col-sm-4 appointment_quotation_number_box required">
							<label>{{__('text.Quotation Number')}}</label>
							<select class="appointment_quotation_number">

								<option value="">{{__('text.Select Quotation')}}</option>
								<option @if(isset($invoice) && $client) data-fname="{{$client->name}}" data-lname="{{$client->family_name}}" @endif value="0">{{__('text.Current Quotation')}}</option>

								@foreach($quotation_ids as $key)

									<option data-fname="{{$key->name}}" data-lname="{{$key->family_name}}" value="{{$key->id}}">{{$key->quotation_invoice_number}}</option>

								@endforeach

							</select>
						</div>

                    @else

                        <div class="form-group col-xs-12 appointment_quotation_number_box col-sm-4 required">
                            <label>{{__('text.Quotation Number')}}</label>
                            <select class="appointment_quotation_number">

                                <option value="">{{__('text.Select Quotation')}}</option>

                                @foreach($quotation_ids as $key)

                                    <option data-fname="{{$key->name}}" data-lname="{{$key->family_name}}" value="{{$key->id}}">{{$key->quotation_invoice_number}}</option>

                                @endforeach

                            </select>
                        </div>

                    @endif

                    <div style="display: none;" class="form-group appointment_customer_box col-xs-12 col-sm-6 required">
                        <label>{{__('text.Customer')}}</label>
                        <div class="c_box" style="width: 100%;display: flex;">
                            <select class="appointment_client">

                                <option value="">{{__('text.Select Customer')}}</option>

                                @foreach($clients as $key)

                                    <option data-fname="{{$key->name}}" data-lname="{{$key->family_name}}" data-businessname="{{$key->business_name}}" data-address="{{$key->address}}" data-streetname="{{$key->street_name}}" data-streetnumber="{{$key->street_number}}" data-postcode="{{$key->postcode}}" data-city="{{$key->city}}" data-phone="{{$key->phone}}" data-email="{{$key->fake_email == 0 ? $key->email : NULL}}" value="{{$key->id}}">{{$key->name . ' ' . $key->family_name}}</option>

                                @endforeach

                            </select>
                            <button type="button" href="#createCustomerModal" role="button" data-toggle="modal" style="outline: none;margin-left: 10px;" class="btn btn-primary add-customer">{{__('text.Add New Customer')}}</button>
                        </div>
                    </div>

                    <div style="display: none;" class="form-group appointment_supplier_box col-xs-12 col-sm-4 required">
                        <label>{{__('text.Supplier')}}</label>
                        <select class="appointment_supplier">

                            <option value="">{{__('text.Select Supplier')}}</option>

                            @foreach($planning_suppliers as $key)

                                <option value="{{$key->id}}">{{$key->company_name}}</option>

                            @endforeach

                        </select>
                    </div>

                    <div style="display: none;" class="form-group appointment_employee_box col-xs-12 col-sm-4 required">
                        <label>{{__('text.Employee')}}</label>
                        <select class="appointment_employee">

                            <option value="">{{__('text.Select Employee')}}</option>

                            @foreach($employees as $key)

                                <option data-fname="{{$key->name}}" data-lname="{{$key->family_name}}" value="{{$key->id}}">{{$key->name . ' ' . $key->family_name}}</option>

                            @endforeach

                        </select>
                    </div>

                    <div style="display: none;" class="form-check continuous_toggle col-xs-12 col-sm-8">
                        <input type="checkbox" class="form-check-input" id="continuous_event">
                        <label class="form-check-label" for="continuous_event">{{__('text.Continuous Event')}}</label>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12">
                        <label>{{__('text.Description')}}</label>
                        <textarea rows="4" class="form-control appointment_description"></textarea>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 required">
                        <label>{{__('text.Tags')}}</label>
                        <input type="text" data-role="tagsinput" class="form-control appointment_tags" />
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <input type="hidden" id="event_id">
                <button type="button" class="btn btn-success pull-left submit_appointmentForm">{{__('text.Save')}}</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">{{__('text.Close')}}</button>
            </div>
        </div>
    </div>
</div>