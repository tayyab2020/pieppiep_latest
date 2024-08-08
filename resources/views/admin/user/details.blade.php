@extends('layouts.admin')



@section('content')
<div class="right-side" style="margin-top: 73px;">
                <div class="container-fluid" style="width: 80%;">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                            <div class="login-form" style="border: 1px solid {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors}}">
                                <div class="login-icon" style="background-color: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors}}"><i class="fa fa-user"></i></div>

                                <div class="section-borders">
                                    <span style="background-color: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors}}"></span>
                                    <span class="black-border"></span>
                                    <span style="background-color: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors}}"></span>
                                </div>

                                <div class="login-title text-center" style="background-color: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors}}">@if(Route::currentRouteName() == 'admin-user-details') Retailer Details @else Supplier Details @endif</div>

                                @if(Route::currentRouteName() != 'admin-supplier-organization-details')

                                    <div class="form-group" style="margin-top: 50px;display: inline-block;width: 100%;">

                                        <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Name</label>
                                        <div class="col-sm-7">
    
                                            <p class="form-control" style="padding: 10px;text-align: center;">{{$user->name}} {{$user->family_name}}</p>
    
                                        </div>
                                    </div>

                                @endif

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Email</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{$user->email}}</p>

                                    </div>
                                </div>

                                @if(Route::currentRouteName() != 'admin-supplier-organization-details')

                                    <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                        <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Experience Years</label>
                                        <div class="col-sm-7">
    
                                            <p class="form-control" style="padding: 10px;text-align: center;">
    
                                                @if($user->experience_years) {{$user->experience_years}} @if($user->experience_years > 1) Years @else Year @endif @else N/A @endif
    
                                            </p>
    
                                        </div>
                                    </div>
    
                                    <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">
    
                                        <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Rating</label>
                                        <div class="col-sm-7">
    
                                            <p class="form-control" style="padding: 10px;text-align: center;">{{$user->rating}} <span class="fa fa-star checked" style="margin-left: 5px;"></span></p>
    
                                        </div>
                                    </div>

                                @endif

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Registration Number</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{$user->registration_number}}</p>

                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Company Name</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{$user->company_name}}</p>

                                    </div>
                                </div>


                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Address</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{Route::currentRouteName() != 'admin-supplier-organization-details' ? $user->employee->address : $user->address}}</p>

                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Phone Number</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{Route::currentRouteName() != 'admin-supplier-organization-details' ? $user->employee->phone : $user->phone}}</p>

                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Tax Number</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{Route::currentRouteName() != 'admin-supplier-organization-details' ? $user->employee->tax_number : $user->tax_number}}</p>

                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;">

                                    <label class="control-label col-sm-3" for="contact_form_success_text" style="text-align: right;padding-top: 7px;">Bank Account</label>
                                    <div class="col-sm-7">

                                        <p class="form-control" style="padding: 10px;text-align: center;">{{Route::currentRouteName() != 'admin-supplier-organization-details' ? $user->employee->bank_account : $user->bank_account}}</p>

                                    </div>
                                </div>

                                @if(Route::currentRouteName() == 'admin-supplier-organization-details' && count($retailers) > 0)

                                    <div class="form-group" style="margin-top: 30px;display: inline-block;width: 100%;border: 1px solid #c1c1c1;border-radius: 10px;padding: 10px;">

                                        <h4 style="text-align: center;">Select retailers for which this supplier account will be enabled</h4>

                                        <form action="{{route('admin-supplier-organization-update')}}" method="POST">

                                            {{ csrf_field() }}

                                            <input type="hidden" name="supplier_organization_id" value="{{$user->id}}">

                                            <div style="margin: 0;" class="row">

                                                @foreach($retailers as $x => $key)
                                                
                                                    <?php $filteredRequests = $key->retailerRequests->filter(function ($request) use ($user) {
                                                        return $request->supplier_organization == $user->id && $request->status == 1 && $request->active == 1;
                                                        });
                                                    ?>

                                                    <div class="form-check">
                                                        <input {{$filteredRequests->isNotEmpty() ? 'checked' : null}} class="form-check-input" type="checkbox" name="retailers_organizations[]" value="{{$key->id}}" id="flexCheckChecked{{$x}}"/>
                                                        <label class="form-check-label" for="flexCheckChecked{{$x}}">{{$key->company_name}}</label>
                                                    </div>

                                                @endforeach

                                            </div>

                                            <div style="margin: 0;margin-top: 10px;text-align: center;" class="row">

                                                <button type="submit" class="btn btn-success">Save</button>

                                            </div>

                                        </form>

                                    </div>

                                @endif

                            </div>

                        </div>
                </div>
            </div>
        </div>

        <style type="text/css">

            .checked {
                color: orange !important;
            }

            .form-check
            {
                display: flex;
                align-items: center;
                float: left;
                margin: 10px;
            }

            .form-check input[type=checkbox]
            {
                margin: 0;
                width: 15px;
                height: 15px;
            }

            .form-check label
            {
                margin: 0;
                margin-left: 5px;
                font-size: 16px;
                color: #6e6e6e;
                font-family: monospace;
            }

        </style>

@endsection
