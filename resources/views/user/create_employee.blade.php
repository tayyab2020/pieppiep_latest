@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard area -->
                    <div style="padding: 0;" class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header">
                                        <h2>{{isset($employee) ? __('text.Edit Employee') : __('text.Create Employee')}}</h2>
                                        <a href="{{route('employees')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                    </div>
                                    <hr>
                                    <form class="form-horizontal" action="{{route('post-create-employee')}}" method="POST" enctype="multipart/form-data">
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        {{csrf_field()}}

                                        <input type="hidden" name="category_id" value="20">
                                        <input type="hidden" name="emp_id" value="{{isset($employee->id) ? $employee->id : null}}">

                                        <div class="form-group">
                                            <label for="first_name" class="col-sm-4 control-label">{{__("text.Profile Type")}}*</label>
                                            <div class="col-sm-6">
                                                <select class="form-control" id="profile_type" name="profile_type">
                                                    <option {{isset($employee) && $employee->employee->profile_type == 1 ? "selected" : ""}} value="1">{{__("text.Employee")}}</option>
                                                    <option {{isset($employee) && $employee->employee->profile_type == 2 ? "selected" : ""}} value="2">{{__("text.Freelancer")}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contract_number" class="col-sm-4 control-label">{{__("text.Contract Number")}}</label>
                                            <div class="col-sm-6">
                                              <input class="form-control" id="contract_number" name="contract_number" placeholder="{{__('text.Contract Number')}}" type="text" value="{{isset($employee->employee) ? $employee->employee->contract_number : ''}}">
                                            </div>
                                        </div>

                                        <div class="form-group pf1" @if(!isset($employee) || $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label for="freelancer_registration_number" class="col-sm-4 control-label">{{__("text.Registration Number")}}</label>
                                            <div class="col-sm-6">
                                              <input class="form-control" id="freelancer_registration_number" name="freelancer_registration_number" placeholder="{{__('text.Registration Number')}}" type="text" value="{{isset($employee->employee) ? $employee->employee->freelancer_registration_number : ''}}">
                                            </div>
                                        </div>

                                        <!-- <div class="form-group pf2" @if(isset($employee) && $employee->employee->profile_type == 2) style="display: none;" @endif>
                                            <label for="employee_number" class="col-sm-4 control-label">{{__("text.Employee Number")}}</label>
                                            <div class="col-sm-6">
                                              <input step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="employee_number" name="employee_number" placeholder="{{__('text.Employee Number')}}" type="number" value="{{isset($employee) && $employee->employee->employee_number ? $employee->employee->employee_number : $ep_counter}}">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group pf1" @if(!isset($employee) || $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label for="freelancer_number" class="col-sm-4 control-label">{{__("text.Freelancer Number")}}</label>
                                            <div class="col-sm-6">
                                              <input step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="freelancer_number" name="freelancer_number" placeholder="{{__('text.Freelancer Number')}}" type="number" value="{{isset($employee) && $employee->employee->freelancer_number ? $employee->employee->freelancer_number : $fr_counter}}">
                                            </div>
                                        </div>

                                        <div class="form-group pf2" @if(isset($employee) && $employee->employee->profile_type == 2) style="display: none;" @endif>
                                            <label for="personal_number" class="col-sm-4 control-label">{{__("text.Personal Number")}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" id="personal_number" name="personal_number" placeholder="" type="text" value="{{isset($employee) ? $employee->employee->personal_number : old('personal_number')}}">
                                            </div>
                                        </div> -->

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Name')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->name) ? $employee->name : old('name')}}" name="name" id="name" placeholder="" required="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Family Name')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->family_name) ? $employee->family_name : old('family_name')}}" name="family_name" id="family_name" placeholder="" required="" type="text">
                                            </div>
                                        </div>

                                        <!-- <div class="form-group pf1" @if(!isset($employee) || isset($employee) && $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label class="control-label col-sm-4" for="">{{__('text.Registration Number')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->registration_number) ? $employee->registration_number : old('registration_number')}}" name="registration_number" id="registration_number" placeholder="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group pf1" @if(!isset($employee) || isset($employee) && $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label for="company_name" class="col-sm-4 control-label">{{__('text.Company Name')}}</label>
                                            <div class="col-sm-6">
                                              <input class="form-control" id="company_name" name="company_name" placeholder="" type="text" value="{{isset($employee) && $employee->company_name ? $employee->company_name : (isset($employee->organization) ? $employee->organization->Name : old('company_name'))}}">
                                            </div>
                                        </div> -->

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="address">{{__('text.Address')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->address : old('address')}}" name="address" id="address" placeholder="" required type="text">
                                                <input type="hidden" id="check_address" value="0">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="address">{{__('text.Postcode')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->postcode : old('postcode')}}" name="postcode" id="postcode" placeholder="" required type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="city">{{__('text.City')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->city : old('city')}}" name="city" id="city" placeholder="" required type="text">
                                            </div>
                                        </div>

                                        <div class="form-group pf1" @if(!isset($employee) || $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label class="control-label col-sm-4" for="business_name">{{__('text.Business Name')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->business_name : old('business_name')}}" name="business_name" id="business_name" placeholder="" type="text">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group pf1" @if(!isset($employee) || $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label class="control-label col-sm-4" for="tax_number">{{__('text.Tax Number')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->tax_number : old('tax_number')}}" name="tax_number" id="tax_number" placeholder="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group pf1" @if(!isset($employee) || $employee->employee->profile_type == 1) style="display: none;" @endif>
                                            <label class="control-label col-sm-4" for="bank_account">{{__('text.Bank Account')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->bank_account : old('bank_account')}}" name="bank_account" id="bank_account" placeholder="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="phone">{{__('text.Contact Number')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->phone : old('phone')}}" name="phone" id="phone" placeholder="" required="" type="text">
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="email">{{__('text.Email')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($employee->employee) ? $employee->employee->email : old('email')}}" name="email" id="email" placeholder="" required="" type="email">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="email">{{__('text.Password')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" name="password" id="password" placeholder="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="email">{{__('text.Confirm Password')}}</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" type="text">
                                            </div>
                                        </div>

                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" id="submit" type="submit" class="btn add-product_btn">{{isset($employee) ? __('text.Edit Employee') : __('text.Create Employee')}}</button>
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
@endsection

@section('scripts')

    <script type="text/javascript">

        $("#profile_type").on('change',function(e){
            var type = $(this).val();
            if(type == 1)
            {
              $(".pf1").hide();
              $(".pf2").show();
            }
            else
            {
              $(".pf2").hide();
              $(".pf1").show();
            }
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
                }
                else
                {
                    $('#address').val('');
                    $('#postcode').val('');
                    $("#city").val('');

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
