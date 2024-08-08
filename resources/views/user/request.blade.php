@extends('layouts.handyman')
@section('content')

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard header items area -->
                    <div class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header" style="display: block;">
                                        <!-- Starting of Dashboard header items area -->
                                        <div class="panel panel-default admin">
                                            <div class="panel-heading admin-title">{{__("text.Requested Data")}}</div>
                                        </div>
                                        <!-- Ending of Dashboard header items area -->

                                        <!-- Starting of Dashboard Top reference + Most Used OS area -->
                                        <div class="reference-OS-area">


                                            <div class="profile-fillup-wrap wow fadeInUp"
                                                 style="visibility: visible; animation-name: fadeInUp;">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <form class="form-horizontal" id="form"
                                                                  action="{{route('profile-update-request-post')}}"
                                                                  method="POST" enctype="multipart/form-data">
                                                                @include('includes.form-success')
                                                                @include('includes.form-error')
                                                                {{csrf_field()}}

                                                                <input type="hidden" name="req_id" value="{{$request->id}}">
                                                                <input type="hidden" name="user_id" value="{{$request->user_id}}">
                                                                <input type="hidden" name="company_profile" value="{{$request->company_profile}}">
                                                                <input type="hidden" name="profile_type" value="{{$request->profile_type}}">

                                                                <input type="hidden" name="latitude" value="{{$request->latitude}}">
                                                                <input type="hidden" name="longitude" value="{{$request->longitude}}">

                                                                @if($request->profile_type)

                                                                    <div class="form-group">
                                                                        <label for="" class="col-sm-3 control-label">{{__("text.Profile Type")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                type="text" value="{{$request->profile_type == 1 ? 'Employee' : 'Freelancer'}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if(!$request->company_profile)

                                                                    <div class="form-group">
                                                                        <label for="first_name" class="col-sm-3 control-label">{{__("text.Name")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="name"
                                                                                name="name" placeholder='{{__("text.Name")}}'
                                                                                type="text" value="{{$request->name}}"
                                                                                required="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="family_name" class="col-sm-3 control-label">{{__("text.Family Name")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="family_name"
                                                                                name="family_name"
                                                                                placeholder='{{__("text.Family Name")}}' type="text"
                                                                                value="{{$request->family_name}}"
                                                                                required="" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                <div class="form-group">
                                                                    <label for="current_photo" class="col-sm-3 control-label">{{__("text.Current Photo")}}*</label>
                                                                    <div class="col-sm-8">

                                                                        <img width="130px" height="90px" id="adminimg"
                                                                             src="{{ $request->photo ? asset('assets/images/'.$request->photo) : 'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG' }}"
                                                                             alt="" id="adminimg">

                                                                        <input type="hidden" name="photo" value="{{$request->photo}}">
                                                                        <input type="hidden" name="compressed_photo" value="{{$request->compressed_photo}}">

                                                                    </div>
                                                                </div>

                                                                @if($request->company_profile)

                                                                    <div class="form-group">
                                                                        <label for="registration_number" class="col-sm-3 control-label">{{__("text.Registration Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="registration_number"
                                                                                name="registration_number"
                                                                                placeholder='{{__("text.Registration Number")}}'
                                                                                type="text"
                                                                                value="{{$request->registration_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="company_name" class="col-sm-3 control-label">{{__("text.Company Name")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="company_name"
                                                                                name="company_name"
                                                                                placeholder='{{__("text.Company Name")}}' type="text"
                                                                                value="{{$request->company_name}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                @else

                                                                    <div class="form-group">
                                                                        <label for="contract_number" class="col-sm-3 control-label">{{__("text.Contract Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="contract_number"
                                                                                name="contract_number"
                                                                                placeholder='{{__("text.Contract Number")}}' type="text"
                                                                                value="{{$request->contract_number}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($request->profile_type == 1)

                                                                    <!-- <div class="form-group">
                                                                        <label for="employee_number" class="col-sm-3 control-label">{{__("text.Employee Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="employee_number"
                                                                                name="employee_number"
                                                                                placeholder='{{__("text.Employee Number")}}'
                                                                                type="text"
                                                                                value="{{$request->employee_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div> -->

                                                                    <div class="form-group">
                                                                        <label for="personal_number" class="col-sm-3 control-label">{{__("text.Personal Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="personal_number"
                                                                                name="personal_number"
                                                                                placeholder='{{__("text.Personal Number")}}'
                                                                                type="text"
                                                                                value="{{$request->personal_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($request->profile_type == 2)

                                                                    <div class="form-group">
                                                                        <label for="freelancer_registration_number" class="col-sm-3 control-label">{{__("text.Registration Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="freelancer_registration_number"
                                                                                name="freelancer_registration_number"
                                                                                placeholder='{{__("text.Registration Number")}}'
                                                                                type="text"
                                                                                value="{{$request->freelancer_registration_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                    <!-- <div class="form-group">
                                                                        <label for="freelancer_number" class="col-sm-3 control-label">{{__("text.Freelancer Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="freelancer_number"
                                                                                name="freelancer_number"
                                                                                placeholder='{{__("text.Freelancer Number")}}'
                                                                                type="text"
                                                                                value="{{$request->freelancer_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div> -->

                                                                @endif

                                                                @if($request->company_profile || $request->profile_type == 2)

                                                                    <div class="form-group">
                                                                        <label for="business_name" class="col-sm-3 control-label">{{__("text.Business Name")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="business_name"
                                                                                   name="business_name"
                                                                                   placeholder='{{__("text.Business Name")}}' type="text"
                                                                                   value="{{$request->business_name}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="tax_number" class="col-sm-3 control-label">{{__("text.Tax Number")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="tax_number"
                                                                                   name="tax_number"
                                                                                   placeholder='{{__("text.Tax Number")}}' type="text"
                                                                                   value="{{$request->tax_number}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="bank_account" class="col-sm-3 control-label">{{__("text.Bank Account")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="bank_account"
                                                                                   name="bank_account"
                                                                                   placeholder='{{__("text.Bank Account")}}' type="text"
                                                                                   value="{{$request->bank_account}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                <div class="form-group">
                                                                    <label for="email" class="col-sm-3 control-label">{{__("text.Email")}}</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" id="email"
                                                                                   name="email"
                                                                                   placeholder='{{__("text.Email")}}' type="text"
                                                                                   value="{{$request->email}}" readonly>
                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="profile_description" class="col-sm-3 control-label">{{__("text.Profile Description")}}</label>
                                                                    <div class="col-sm-8">
                                                                        <textarea class="form-control"
                                                                                  name="description"
                                                                                  id="profile_description" rows="5"
                                                                                  style="resize: vertical;"
                                                                                  readonly>{{$request->description}}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="special" class="col-sm-3 control-label">{{__("text.Specialties")}}</label>
                                                                    <div class="col-sm-8">

                                                                        <input class="form-control" id="special"
                                                                               name="special" placeholder='{{__("text.Specialties")}}'
                                                                               type="text" value="{{$request->special}}"
                                                                               readonly>

                                                                    </div>
                                                                </div>
                                                                <div class="profile-filup-description-box margin-bottom-30">

                                                                    <div class="form-group">
                                                                        <label for="edu" class="col-sm-3 control-label">{{__("text.Education")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="education"
                                                                                   id="edu" placeholder='{{__("text.Education")}}'
                                                                                   type="text"
                                                                                   value="{{$request->education}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="lang" class="col-sm-3 control-label">{{__("text.Language")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="language"
                                                                                   id="lang" placeholder='{{__("text.Language")}}'
                                                                                   type="text"
                                                                                   value="{{$request->language}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="prof" class="col-sm-3 control-label">{{__("text.Profession")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                   name="profession" id="prof"
                                                                                   placeholder='{{__("text.Profession")}}' type="text"
                                                                                   value="{{$request->profession}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="city" class="col-sm-3 control-label">{{__("text.City")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="city"
                                                                                   id="city" placeholder='{{__("text.City")}}'
                                                                                   type="text" value="{{$request->city}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="address" class="col-sm-3 control-label">{{__("text.Address")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="address"
                                                                                   id="address" placeholder='{{__("text.Address")}}'
                                                                                   type="text"
                                                                                   value="{{$request->address}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="phone" class="col-sm-3 control-label">{{__("text.Phone")}}*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="phone"
                                                                                   id="phone" placeholder='{{__("text.Phone")}}'
                                                                                   type="number"
                                                                                   value="{{$request->phone}}" required=""
                                                                                   readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="web" class="col-sm-3 control-label">{{__("text.Website")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="web"
                                                                                   id="web" placeholder='{{__("text.Website")}}'
                                                                                   type="text" value="{{$request->web}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="web" class="col-sm-3 control-label">{{__("text.Postal Code")}}</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="postcode"
                                                                                   id="postcode"
                                                                                   placeholder='{{__("text.Postal Code")}}' type="text"
                                                                                   value="{{$request->postcode}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="submit-area margin-bottom-30"
                                                                     style="margin-top: 30px;">
                                                                    <div class="row">
                                                                        <div class="col-md-8 col-md-offset-2">
                                                                            <div class="add-product-footer">
                                                                                <button type="submit"
                                                                                        class="btn add-product_btn">
                                                                                    {{__("text.Approve")}}
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
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
            </div>
        </div>
    </div>




@endsection
