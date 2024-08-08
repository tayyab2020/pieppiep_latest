@extends('layouts.handyman')

@section('styles')

    <link href="{{asset('assets/admin/css/jquery-ui.css')}}" rel="stylesheet" type="text/css">

    <style type="text/css">
        .colorpicker-alpha {display:none !important;}
        .colorpicker{ min-width:128px !important;}
        .colorpicker-color {display:none !important;}
    </style>

@endsection

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
                                        <h2>{{ isset($status) ? __('text.Edit Status') : __('text.Add New Status') }}</h2>
                                        <a href="{{route('planning-statuses')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                    </div>
                                    <hr>
                                    <form class="form-horizontal" action="{{route('store-planning-status')}}" method="POST" enctype="multipart/form-data">
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        {{csrf_field()}}

                                        <input type="hidden" name="status_id" value="{{isset($status) ? $status->id : null}}">

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Title')}}*</label>
                                            <div class="col-sm-6">
                                                <input value="{{isset($status) ? $status->title : null}}" class="form-control" name="title" id="blood_group_display_name" placeholder="{{__('text.Planning Status')}}" required="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Agenda Background Color')}}</label>
                                            <div class="col-sm-6">
                                                <span id="cp1" class="cp1 input-group colorpicker-component">
                                                    <input class="cp1 form-control" type="text" name="bg_color" value="{{isset($status) ? $status->bg_color : ''}}" />
                                                    <span class="input-group-addon"><i></i></span>
                                                </span>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" type="submit" class="btn add-product_btn">{{isset($status) ? __('text.Edit Status') : __('text.Add New Status')}}</button>
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

    <style type="text/css">

        .select2-container .select2-selection--single
        {
            height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered
        {
            line-height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow
        {
            height: 38px;
        }

        .swal2-show
        {
            padding: 40px;
            width: 30%;

        }

        .swal2-header
        {
            font-size: 23px;
        }

        .swal2-content
        {
            font-size: 18px;
        }

        .swal2-actions
        {
            font-size: 16px;
        }

    </style>

@section('scripts')

    <script>
        $('.cp1').colorpicker();
    </script>

@endsection
