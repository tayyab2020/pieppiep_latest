@extends('layouts.admin')



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
                                            <div class="panel-heading admin-title">Requested Data</div>

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
                                                                  action="{{route('request-profile-update')}}"
                                                                  method="POST" enctype="multipart/form-data">
                                                                @include('includes.form-success')
                                                                @include('includes.form-error')
                                                                {{csrf_field()}}

                                                                <input type="hidden" name="req_id" value="{{$user->id}}">
                                                                <input type="hidden" name="user_id" value="{{$user->user_id}}">
                                                                <input type="hidden" name="company_profile" value="{{$user->company_profile}}">
                                                                <input type="hidden" name="profile_type" value="{{$user->profile_type}}">

                                                                <input type="hidden" name="latitude" value="{{$user->latitude}}">
                                                                <input type="hidden" name="longitude" value="{{$user->longitude}}">

                                                                @if($user->profile_type)

                                                                    <div class="form-group">
                                                                        <label for=""
                                                                            class="col-sm-3 control-label">Profile Type*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                placeholder="Profile Type"
                                                                                type="text" value="{{$user->profile_type == 1 ? 'Employee' : 'Freelancer'}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                <div class="form-group">
                                                                    <label for="email" class="col-sm-3 control-label">Email</label>
                                                                    <div class="col-sm-8">
                                                                        <input class="form-control" id="email"
                                                                                   name="email"
                                                                                   placeholder='Email' type="text"
                                                                                   value="{{$user->email}}" readonly>
                                                                    </div>
                                                                </div>

                                                                @if(!$user->company_profile)

                                                                    <div class="form-group">
                                                                        <label for="first_name"
                                                                               class="col-sm-3 control-label">First
                                                                            Name*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="name"
                                                                                   name="name" placeholder="First Name"
                                                                                   type="text" value="{{$user->name}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>
    
                                                                    <div class="form-group">
                                                                        <label for="family_name"
                                                                               class="col-sm-3 control-label">Family
                                                                            Name*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="family_name"
                                                                                   name="family_name"
                                                                                   placeholder="Family Name" type="text"
                                                                                   value="{{$user->family_name}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($user->company_profile)

                                                                    <div class="form-group">
                                                                        <label for="registration_number"
                                                                               class="col-sm-3 control-label">Registration
                                                                            Number</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                   id="registration_number"
                                                                                   name="registration_number"
                                                                                   placeholder="Registration Number"
                                                                                   type="text"
                                                                                   value="{{$user->registration_number}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="company_name"
                                                                               class="col-sm-3 control-label">Company
                                                                            Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="company_name"
                                                                                   name="company_name"
                                                                                   placeholder="Company Name" type="text"
                                                                                   value="{{$user->company_name}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($user->profile_type == 1 || $user->profile_type == 2)

                                                                    <div class="form-group">
                                                                        <label for="contract_number" class="col-sm-3 control-label">Contract Number</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="contract_number"
                                                                                name="contract_number"
                                                                                placeholder='Contract Number'
                                                                                type="text"
                                                                                value="{{$user->contract_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="personal_number" class="col-sm-3 control-label">Personal Number</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="personal_number"
                                                                                name="personal_number"
                                                                                placeholder='Personal Number'
                                                                                type="text"
                                                                                value="{{$user->personal_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($user->profile_type == 2)

                                                                    <div class="form-group">
                                                                        <label for="freelancer_registration_number" class="col-sm-3 control-label">Registration Number</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                id="freelancer_registration_number"
                                                                                name="freelancer_registration_number"
                                                                                placeholder='Registration Number'
                                                                                type="text"
                                                                                value="{{$user->freelancer_registration_number}}"
                                                                                readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                @if($user->company_profile || $user->profile_type == 2)

                                                                    <div class="form-group">
                                                                        <label for="business_name" class="col-sm-3 control-label">Business Name</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="business_name"
                                                                                   name="business_name"
                                                                                   placeholder='Business Name' type="text"
                                                                                   value="{{$user->business_name}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="tax_number" class="col-sm-3 control-label">Tax Number</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="tax_number"
                                                                                   name="tax_number"
                                                                                   placeholder='Tax Number' type="text"
                                                                                   value="{{$user->tax_number}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="bank_account" class="col-sm-3 control-label">Bank Account</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" id="bank_account"
                                                                                   name="bank_account"
                                                                                   placeholder='Bank Account' type="text"
                                                                                   value="{{$user->bank_account}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                @endif

                                                                <div class="form-group">
                                                                    <label for="current_photo"
                                                                           class="col-sm-3 control-label">Current
                                                                        Photo*</label>
                                                                    <div class="col-sm-8">

                                                                        <img width="130px" height="90px" id="adminimg"
                                                                             src="{{ $user->photo ? asset('assets/images/'.$user->photo):'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'}}"
                                                                             alt="" id="adminimg">

                                                                        <input type="hidden" name="photo"
                                                                               value="{{$user->photo}}">
                                                                               <input type="hidden" name="compressed_photo"
                                                                               value="{{$user->compressed_photo}}">

                                                                    </div>
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="profile_description"
                                                                           class="col-sm-3 control-label">Profile
                                                                        Description</label>
                                                                    <div class="col-sm-8">
                                                                        <textarea class="form-control"
                                                                                  name="description"
                                                                                  id="profile_description" rows="5"
                                                                                  style="resize: vertical;"
                                                                                  readonly>{{$user->description}}</textarea>
                                                                    </div>
                                                                </div>


                                                                <div class="form-group">
                                                                    <label for="special" class="col-sm-3 control-label">Specialties</label>
                                                                    <div class="col-sm-8">

                                                                        <input class="form-control" id="special"
                                                                               name="special" placeholder="Specialties"
                                                                               type="text" value="{{$user->special}}"
                                                                               readonly>

                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="profile-filup-description-box margin-bottom-30">

                                                                    <div class="form-group">
                                                                        <label for="edu" class="col-sm-3 control-label">Education</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="education"
                                                                                   id="edu" placeholder="Education"
                                                                                   type="text"
                                                                                   value="{{$user->education}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="lang"
                                                                               class="col-sm-3 control-label">Language</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="language"
                                                                                   id="lang" placeholder="Language"
                                                                                   type="text"
                                                                                   value="{{$user->language}}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="prof"
                                                                               class="col-sm-3 control-label">Profession</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control"
                                                                                   name="profession" id="prof"
                                                                                   placeholder="Profession" type="text"
                                                                                   value="{{$user->profession}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>


                                                                    <div class="form-group">
                                                                        <label for="city"
                                                                               class="col-sm-3 control-label">City*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="city"
                                                                                   id="city" placeholder="City"
                                                                                   type="text" value="{{$user->city}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="address"
                                                                               class="col-sm-3 control-label">Address*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="address"
                                                                                   id="address" placeholder="Address"
                                                                                   type="text"
                                                                                   value="{{$user->address}}"
                                                                                   required="" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="phone"
                                                                               class="col-sm-3 control-label">Phone*</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="phone"
                                                                                   id="phone" placeholder="Phone"
                                                                                   type="number"
                                                                                   value="{{$user->phone}}" required=""
                                                                                   readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="web" class="col-sm-3 control-label">Website</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="web"
                                                                                   id="web" placeholder="Website"
                                                                                   type="text" value="{{$user->web}}"
                                                                                   readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label for="web" class="col-sm-3 control-label">Postal
                                                                            Code</label>
                                                                        <div class="col-sm-8">
                                                                            <input class="form-control" name="postcode"
                                                                                   id="postcode"
                                                                                   placeholder="Postal Code" type="text"
                                                                                   value="{{$user->postcode}}" readonly>
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
                                                                                    Approve
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
