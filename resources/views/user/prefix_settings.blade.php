@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard area -->
                    <div class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">

                                    <div class="add-product-header">
                                        <h2>{{__('text.Prefix Settings')}}</h2>
                                    </div>
                                    
                                    <hr>

                                    <form class="form-horizontal" action="{{route('save-prefix-settings')}}" method="POST" enctype="multipart/form-data">

                                        @include('includes.form-error')
                                        @include('includes.form-success')

                                        {{csrf_field()}}

                                        @if($user->role_id == 2)

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Client ID in quotation number?')}}</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control" name="quotation_client_id">
                                                        <option {{$user->quotation_client_id == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                        <option {{$user->quotation_client_id == 1 ? 'selected' : null}} value="1">{{__('text.Yes')}}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Quotation Prefix')}}</label>
                                                <div class="col-sm-6">
                                                    <input value="{{$user->quotation_prefix}}" class="form-control" name="quotation_prefix" placeholder="{{__('text.Enter Quotation Prefix')}}" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Client ID in Invoice number?')}}</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control" name="invoice_client_id">
                                                        <option {{$user->invoice_client_id == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                        <option {{$user->invoice_client_id == 1 ? 'selected' : null}} value="1">{{__('text.Yes')}}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Invoice Prefix')}}</label>
                                                <div class="col-sm-6">
                                                    <input value="{{$user->invoice_prefix}}" class="form-control" name="invoice_prefix" placeholder="{{__('text.Enter Invoice Prefix')}}" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name"></label>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Next Number')}}</label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Max Number Used')}}</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Customer Number Counter')}}</label>
                                                <div class="col-sm-3">
                                                    <input value="{{sprintf('%06d', $user->counter_customer_number)}}" id="my_number" class="form-control" name="customer_number_counter" required placeholder="" type="text">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input value="{{$max_customer_number}}" class="form-control" readonly placeholder="" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name"></label>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Next Number')}}</label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Last Number Used')}}</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Quotation Counter')}}</label>
                                                <div class="col-sm-3">
                                                    <input value="{{sprintf('%06d', $user->counter)}}" id="my_number" class="form-control" name="quotation_counter" required placeholder="{{__('text.Enter Quotation Counter')}}" type="text">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input value="{{$last_quotation_number}}" class="form-control" readonly placeholder="" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Invoice Counter')}}</label>
                                                <div class="col-sm-3">
                                                    <input value="{{sprintf('%06d', $user->counter_invoice)}}" id="my_number" class="form-control" name="invoice_counter" required placeholder="{{__('text.Enter Invoice Counter')}}" type="text">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input value="{{$last_invoice_number}}" class="form-control" readonly placeholder="" type="text">
                                                </div>
                                            </div>

                                        @else

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Client ID in Order number?')}}</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control" name="order_client_id">
                                                        <option {{$user->order_client_id == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                        <option {{$user->order_client_id == 1 ? 'selected' : null}} value="1">{{__('text.Yes')}}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Order Prefix')}}</label>
                                                <div class="col-sm-6">
                                                    <input value="{{$user->order_prefix}}" class="form-control" name="order_prefix" placeholder="{{__('text.Enter Order Prefix')}}" type="text">
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name"></label>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Next Number')}}</label>
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>{{__('text.Last Number Used')}}</label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Order Counter')}}</label>
                                                <div class="col-sm-3">
                                                    <input value="{{sprintf('%06d', $user->counter_order)}}" id="my_number" class="form-control" name="order_counter" required placeholder="{{__('text.Enter Order Counter')}}" type="text">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input value="{{$last_order_number}}" class="form-control" readonly placeholder="" type="text">
                                                </div>
                                            </div>

                                        @endif

                                        <hr>

                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" type="submit" class="btn add-product_btn">{{__('text.Save')}}</button>
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

    <script>

        $('body').on('keypress', "#my_number", function (e) {

			var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
			var val = String.fromCharCode(charCode);

			if (!val.match(/^[0-9]+$/))  // For characters validation
			{
				e.preventDefault();
				return false;
			}

        });

        $('body').on('input', '#my_number' ,function(){

            var value = $(this).val();
            value = value.replace(/^0+/, '');

            while (value.length < 6) value = "0" + value;

            $(this).val(value);

        });

    </script>

@endsection

    <style type="text/css">

        .swal2-show {
            padding: 40px;
            width: 30%;

        }

        .swal2-header {
            font-size: 23px;
        }

        .swal2-content {
            font-size: 18px;
        }

        .swal2-actions {
            font-size: 16px;
        }

    </style>
