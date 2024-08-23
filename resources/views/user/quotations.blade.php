@extends('layouts.handyman')
@section('content')
    <script src="{{ asset('assets/front/js/spartan-multi-image-picker.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div class="section-padding add-product-1" style="padding: 0;">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header products" style="display: flex;justify-content: space-between;">
                                        <h2 style="display: inline-block;">
                                            @if (Auth::guard('user')->user()->role_id == 2)
                                                {{ __('text.Quotations') }}
                                            @else
                                                {{ __('text.Orders') }}
                                            @endif
                                        </h2>
                                        @if (Auth::guard('user')->user()->role_id == 2)
                                            <div>
                                                <button style="background-color: #4dd5b2 !important;border-color: #ffffff00 !important;border-radius: 30px;" type="button" href="#myModal5" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{ __('text.Export to Reeleezee') }}</button>
                                                <a href="{{ route('create-custom-quotation') }}" class="btn add-newProduct-btn"><i class="fa fa-plus"></i> {{ __('text.Create Quotation') }}</a>
                                                <label style="border-radius: 30px;" class="btn btn-success select-all" for="selectCheck">{{ __('text.Select all') }}</label>
                                                <button style="border-radius: 30px;" type="button" class="btn btn-danger delete-quotations"><i class="fa fa-trash"></i> {{ __('text.Delete') }}</button>
                                                <input style="display: none;" type="checkbox" id="selectCheck">
                                            </div>
                                        @endif
                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                @if (Auth::guard('user')->user()->role_id == 2)
                                                    <div style="display: flex;margin: 10px 0 20px 0;" class="row filters_row">
                                                        <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                            <div style="margin: 0;" class="form-group">
                                                                <select style="min-width: 125px;" class="form-control filter_year">
                                                                    <option value="">{{ __('text.All Years') }}</option>

                                                                    @for ($y = $earliestYear; $y <= date('Y'); $y++)
                                                                        <option {{ Auth::guard('user')->user()->filter_year == $y ? 'selected' : null }} value="{{ $y }}">{{ $y }}</option>
                                                                    @endfor

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                            <div style="margin: 0;" class="form-group">
                                                                <select style="min-width: 125px;" class="form-control filter_month">
                                                                    <option value="">{{ __('text.All Months') }}</option>
                                                                    @for ($m = 1; $m <= 12; $m++)
                                                                        <option {{ Auth::guard('user')->user()->filter_month == $m ? 'selected' : null }} value="{{ sprintf('%02d', $m) }}">{{ date('F', mktime(0, 0, 0, $m, 1, date('Y'))) }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                            <div style="margin: 0;" class="form-group">
                                                                <select style="min-width: 125px;" class="form-control filter_status">
                                                                    <option value="">{{ __('text.All Statuses') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Pending' ? 'selected' : null }} value="Pending">{{ __('text.Pending') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Draft' ? 'selected' : null }} value="Draft">{{ __('text.Draft') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Waiting For Approval' ? 'selected' : null }} value="Waiting For Approval">{{ __('text.Waiting For Approval') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Quotation Sent' ? 'selected' : null }} value="Quotation Sent">{{ __('text.Quotation Sent') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Asking for Review' ? 'selected' : null }} value="Asking for Review">{{ __('text.Asking for Review') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Quotation Accepted' ? 'selected' : null }} value="Quotation Accepted">{{ __('text.Quotation Accepted') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Payment Pending' ? 'selected' : null }} value="Payment Pending">{{ __('text.Payment Pending') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Paid' ? 'selected' : null }} value="Paid">{{ __('text.Paid') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Invoice Generated' ? 'selected' : null }} value="Invoice Generated">{{ __('text.Invoice Generated') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Goods Delivered' ? 'selected' : null }} value="Goods Delivered">{{ __('text.Goods Delivered') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Goods Received' ? 'selected' : null }} value="Goods Received">{{ __('text.Goods Received') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Closed' ? 'selected' : null }} value="Closed">{{ __('text.Closed') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Order Processing' ? 'selected' : null }} value="Order Processing">{{ __('text.Order Processing') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Processing' ? 'selected' : null }} value="Processing">{{ __('text.Processing') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Order Delivered' ? 'selected' : null }} value="Order Delivered">{{ __('text.Order Delivered') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Order Confirmed' ? 'selected' : null }} value="Order Confirmed">{{ __('text.Order Confirmed') }}</option>
                                                                    <option {{ Auth::guard('user')->user()->filter_status == 'Confirmation Pending' ? 'selected' : null }} value="Confirmation Pending">{{ __('text.Confirmation Pending') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <form id="quotations-delete-form" method="POST" action="{{ route('quotations-delete-post') }}">
                                                    <input type="hidden" id="token" name="_token" value="{{ @csrf_token() }}">
                                                    <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" cellspacing="0">
                                                        <thead>
                                                            <tr role="row">
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{ __('text.Quotation Number') }}</th>
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{ Auth::guard('user')->user()->role_id == 2 ? __('text.Customer Name') : __('text.Retailer') }}</th>
                                                                @if (Auth::guard('user')->user()->role_id == 2)
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Grand Total Overview') }}</th>
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Paid') }}</th>
                                                                @endif
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Current Stage') }}</th>
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Date') }}</th>
                                                                @if (Auth::guard('user')->user()->role_id == 2)
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Regards') }}</th>
                                                                @endif
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{ __('text.Action') }}</th>
                                                            </tr>
                                                        </thead>

                                                        @if (Auth::guard('user')->user()->role_id == 2)
                                                            <tfoot>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th style="text-align: left;"></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        @endif
                                                    </table>
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

    <div id="myModal5" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form id="reeleezee-export-form" method="POST" action="{{ route('export-quotations-reeleezee') }}">
                <input type="hidden" name="_token" value="{{ @csrf_token() }}">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{{ __('text.Export by date') }}</h4>
                    </div>
                    <div style="padding: 30px 20px;display: flex;justify-content: center;" class="modal-body">
                        <div class="wrapper-options1 panel-group" id="accordion">
                            <div class="date-select-box panel panel-default">
                                <input type="radio" value="1" class="select-form" name="export_by" id="option1-1">
                                <label for="option1-1" class="option1 option1-1">
                                    <div class="dot"></div>
                                    <span>{{ __('text.Document date') }}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1"></a>
                                </label>
                                <div id="collapse1" class="panel-collapse collapse">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{ __('text.Start Date') }}" readonly class="export_dates" name="created_start_date" id="created_start_date">
                                        <input type="text" placeholder="{{ __('text.End Date') }}" readonly class="export_dates" name="created_end_date" id="created_end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="date-select-box panel panel-default">
                                <input type="radio" value="2" class="select-form" name="export_by" id="option1-2">
                                <label for="option1-2" class="option1 option1-2">
                                    <div class="dot"></div>
                                    <span>{{ __('text.Updated Date') }}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"></a>
                                </label>
                                <div id="collapse2" class="panel-collapse collapse">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{ __('text.Start Date') }}" readonly class="export_dates" name="updated_start_date" id="updated_start_date">
                                        <input type="text" placeholder="{{ __('text.End Date') }}" readonly class="export_dates" name="updated_end_date" id="updated_end_date">
                                    </div>
                                </div>
                            </div>
                            <div class="date-select-box panel panel-default">
                                <input checked type="radio" value="3" class="select-form" name="export_by" id="option1-3">
                                <label for="option1-3" class="option1 option1-3">
                                    <div class="dot"></div>
                                    <span>{{ __('text.Last Export Date') }}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"></a>
                                </label>
                                <div id="collapse3" class="panel-collapse collapse in">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{ __('text.Start Date') }}" readonly class="export_dates" name="last_start_date" id="last_start_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('text.Submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button style="font-size: 32px;background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 style="margin: 10px 0;" id="myModalLabel">{{ __('text.Review Reason') }}</h3>
                </div>
                <div class="modal-body" id="myWizard">
                    <textarea rows="5" style="resize: vertical;" type="text" name="review_text" id="review_text" class="form-control" readonly autocomplete="off"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" aria-label="Close" style="border: 0;outline: none;background-color: #e5e5e5 !important;color: black !important;" class="btn back">{{ __('text.Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @include('user.send_quotation_modal')
    @include('user.send_order_modal')
    <style type="text/css">
        .date-select-box {
            width: 100%;
            margin: 5px 0;
        }

        .export_dates {
            width: 100%;
            height: 35px;
            border: 1px solid #b3b3b3;
            border-radius: 5px;
            padding: 5px;
            outline: none !important;
            margin: 0 5px;
        }

        .wrapper-options1 {
            display: inline-flex;
            flex-direction: column;
            width: 100%;
            align-items: center;
            justify-content: space-evenly;
            border-radius: 5px;
            padding: 0;
        }

        .wrapper-options1 .option1 {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin: 0;
            border-radius: 5px;
            cursor: pointer;
            padding: 0 10px;
            transition: all 0.3s ease;
        }

        .wrapper-options1 .option1 .dot {
            height: 20px;
            width: 20px;
            background: #d9d9d9;
            border-radius: 50%;
            position: relative;
        }

        .wrapper-options1 .option1 .dot::before {
            position: absolute;
            content: "";
            top: 4px;
            left: 4px;
            width: 12px;
            height: 12px;
            background: #0069d9;
            border-radius: 50%;
            opacity: 0;
            transform: scale(1.5);
            transition: all 0.3s ease;
        }

        .wrapper-options1 input[type="radio"] {
            display: none;
        }

        .wrapper-options1 input[type="radio"]:checked+label {
            border-color: #0069d9;
            background: #0069d9;
        }

        .wrapper-options1 input[type="radio"]:checked+label .dot {
            background: #fff;
        }

        .wrapper-options1 input[type="radio"]:checked+label .dot::before {
            opacity: 1;
            transform: scale(1);
        }

        .wrapper-options1 .option1 span {
            font-size: 20px;
            color: #808080;
            margin-left: 10px;
            margin-top: -1px;
            text-transform: capitalize;
        }

        .wrapper-options1 input[type="radio"]:checked+label span {
            color: #fff;
        }

        .wrapper-options1 .option1 a::before {
            display: none;
        }

        .mt-10 {
            margin-top: 10px;
        }

        td .btn {
            white-space: normal;
            font-size: 13px;
            padding: 1px 5px;
        }

        table.dataTable .dtrg-group {
            font-size: 15px;
            font-weight: 600;
            background-color: #FDFEFE !important;
        }

        table.dataTable .dtrg-group th {
            padding-left: 5px !important;
            padding: 15px;
        }

        .dataTables_scrollHead table {
            margin-top: 40px !important;
        }

        table.dataTable {
            table-layout: fixed;
        }

        .table.products>thead>tr>th {
            border-right: 1px solid #acacac;
        }

        .dataTables_scrollBody .table.products>thead>tr>th {
            border: none !important;
        }

        .hovertext {
            position: relative;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin: 0;
            cursor: pointer;
        }

        .hovertext:hover {
            -webkit-line-clamp: unset;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.child {
            position: relative;
        }

        .note-editor {
            width: 100%;
        }

        .note-toolbar {
            line-height: 1;
        }

        .btn-primary1 {
            background-color: darkcyan;
            border-color: darkcyan;
            color: white !important;
        }

        @media (min-width: 768px) {
            .dropdown1.open>.dropdown-menu {
                display: grid;
            }

            .dropdown1 .dropdown-menu {
                /* width: 215px; */
                overflow: auto;
            }
        }

        .dropdown1 .dropdown-menu {
            position: relative !important;
            left: -65px;
            float: none;
        }

        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            text-indent: 1px !important;
            text-overflow: '' !important;
        }

        /* Custom dropdown */
        .custom-dropdown {
            position: relative;
            display: inline-block;
            vertical-align: middle;
            margin: 10px;
            /* demo only */
        }

        .custom-dropdown select {
            background-color: #1abc9c;
            color: #fff;
            font-size: inherit;
            padding: .5em;
            padding-right: 2.5em;
            border: 0;
            margin: 0;
            border-radius: 3px;
            text-indent: 0.01px;
            text-overflow: '';
            -webkit-appearance: button;
            /* hide default arrow in chrome OSX */
            outline: none;
        }

        .custom-dropdown::before,
        .custom-dropdown::after {
            content: "";
            position: absolute;
            pointer-events: none;
        }

        .custom-dropdown::after {
            /*  Custom dropdown arrow */
            content: "\25BC";
            height: 1em;
            font-size: .625em;
            line-height: 1;
            right: 1.2em;
            top: 50%;
            margin-top: -.5em;
        }

        .custom-dropdown::before {
            /*  Custom dropdown arrow cover */
            width: 2em;
            right: 0;
            top: 0;
            bottom: 0;
            border-radius: 0 3px 3px 0;
        }

        .custom-dropdown select[disabled] {
            color: rgba(0, 0, 0, .3);
        }

        .custom-dropdown select[disabled]::after {
            color: rgba(0, 0, 0, .1);
        }

        .custom-dropdown::before {
            background-color: rgba(0, 0, 0, .15);
        }

        .custom-dropdown::after {
            color: rgba(0, 0, 0, .4);
        }

        .text-left {
            font-size: 18px !important;
            text-align: center !important;
        }

        .swal2-popup {
            width: 25% !important;
        }

        .swal2-icon.swal2-warning {
            width: 20% !important;
            height: 82px !important;
        }

        .swal2-title {
            font-size: 27px !important;
        }

        .swal2-content {
            font-size: 18px !important;
        }

        .swal2-actions {
            font-size: 13px !important;
        }
    </style>
    <style type="text/css">
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

        .table-condensed {
            width: 100%;
        }

        .datepicker td,
        .datepicker th {
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

        .datepicker>div {
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

        table.dataTable thead>tr>th.sorting_asc,
        table.dataTable thead>tr>th.sorting_desc,
        table.dataTable thead>tr>th.sorting,
        table.dataTable thead>tr>td.sorting_asc,
        table.dataTable thead>tr>td.sorting_desc,
        table.dataTable thead>tr>td.sorting {
            padding-right: 0;
            padding-left: 12px;
            text-align: left;
            border-top: 1px solid black !important;
            border-bottom: 1px solid black !important;
        }

        #img {
            width: 100% !important;
            display: block !important;
        }

        #photo {
            width: 250px !important;
        }

        #client {
            width: 230px !important;
        }

        #handyman {
            width: 230px !important;
        }

        #serv {
            width: 170px !important;
        }

        #rate {
            width: 175px !important;
        }

        #service {
            width: 151px !important;
        }

        #date {
            width: 158px !important;
        }

        #amount {
            width: 160px !important;
        }

        #status {
            width: 77px !important;
        }

        .table.products>tbody>tr>td {
            text-align: left;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ccc;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            height: 40px;
        }

        .dataTables_wrapper .dataTables_length select.form-control {
            line-height: 1;
        }

        table.dataTable thead .sorting.sorting_disabled:after {
            content: "";
        }

        /* td span.btn
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            width: 100%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        } */
    </style>
    <style>
        @media (min-width: 768px) {
            .dropdown1 .dropdown-menu {
                width: auto !important;
                float: inline-start !important;
            }
        }
    </style>
    @include('user.modals_css')
@endsection
@section('scripts')
    @include('user.modals_js')
    <script src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript">
        (function($) {
            if ($.fn.datepicker) {
                $('.export_dates').datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'du',
                    ignoreReadonly: true
                });
            } else {
                console.error('Datepicker plugin is not loaded. 1');
            }
            // Simplified event binding
            ['#option1-1', '#option1-2', '#option1-3'].forEach(function(selector) {
                $(selector).on('click', function() {
                    $(this).parent().find('a').trigger('click');
                });
            });

        })(jQuery);

        function delete_confirmation(href, type) {
            Swal.fire({
                title: '{{ __('text.Are you sure?') }}',
                text: '{{ __('text.Are you sure you want to delete this quotation') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ __('text.Yes') }}!',
                cancelButtonText: '{{ __('text.Cancel') }}',
            }).then((result) => {
                if (result.value) {
                    if (type) {
                        $('#quotations-delete-form').submit();
                    } else {
                        window.location.href = href;
                    }
                }
            });
        }

        $(".delete-btn").click(function() {
            var href = $(this).data("href");
            delete_confirmation(href, 0);
        });

        $(".delete-quotations").click(function() {
            delete_confirmation(null, 1);
        });

        $(".select-all").click(function() {
            var isChecked = $('.custom-control-input:checked').length > 0;
            var newCheckState = !isChecked;
            $('.custom-control-input').prop('checked', newCheckState);
            $('.delete_quotations_options').val(newCheckState ? 1 : 0);
        });

        $(".custom-control-input").change(function() {
            var isChecked = $(this).is(":checked");
            $(this).parent().find('.delete_quotations_options').val(isChecked ? 1 : 0);
        });

        $('#filter_date').datepicker({
            format: 'mm-yyyy',
            language: 'du',
            startView: "months",
            minViewMode: "months",
        });

        if ($.fn.datepicker && $.fn.datepicker.dates) {
            $.fn.datepicker.dates['du'] = {
                days: ["zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag"],
                daysShort: ["zo", "ma", "di", "wo", "do", "vr", "za"],
                daysMin: ["zo", "ma", "di", "wo", "do", "vr", "za"],
                months: ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"],
                monthsShort: ["jan", "feb", "mrt", "apr", "mei", "jun", "jul", "aug", "sep", "okt", "nov", "dec"],
            };
        } else {
            console.error('Datepicker plugin is not loaded.');
        }

        function ask(e) {
            var text = $(e).data('text');
            $('#review_text').val(text);
            $('#myModal1').modal('toggle');
        }
    </script>
    @if (Auth::guard('user')->user()->role_id == 2)
        <script>
            $(document).ready(function() {
                $(document).on('click', '.accept-btn', function() {
                    var href = $(this).data("href");
                    Swal.fire({
                        title: '{{ __('text.Are you sure?') }}',
                        text: '{{ __('text.Are you sure you want to accept this quote') }}',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '{{ __('text.Yes') }}!',
                        cancelButtonText: '{{ __('text.Cancel') }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = href;
                        }
                    });
                });
            });

            $(document).on('click', '.create-invoice-btn', function() {
                var href = $(this).data("href");
                Swal.fire({
                    title: '{{ __('text.Are you sure?') }}',
                    text: '{{ __('text.Are you sure you want to create an invoice') }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: '{{ __('text.Yes') }}!',
                    cancelButtonText: '{{ __('text.Cancel') }}',
                }).then((result) => {
                    if (result.value) {
                        window.location.href = href;
                    }
                });
            });
        </script>
    @endif

    @if (Auth::check())
        <script>
            $(document).ready(function() {
                var screen_width = $(window).width();
                var userColumnDefs = [];
                var table_width = "";

                var table;
                var tableId = 'example';
                @if (Auth::guard('user')->user()->role_id == 2)
                    var dateColumn = 5;
                @else
                    var dateColumn = 3;
                @endif
                var statusColumn = 4;
                var orderStatusColumn = 6;
                var amountColumn = 2;
                var paidColumn = 3;

                table = $('#' + tableId).DataTable({
                    processing: true,
                    responsive: false,
                    autoWidth: false,
                    scrollX: true,
                    rowReorder: {
                        selector: 'td:nth-child(2)'
                    },
                    pageLength: 100,
                    serverSide: true,
                    stateSave: true,
                    ajax: function(data, callback, settings) {
                        // Collect filter values
                        var filters = {
                            year: $('.filter_year').val(),
                            month: $('.filter_month').val(),
                            status: $('.filter_status').val()
                        };

                        $.ajax({
                            url: "{{ route('customer-quotations-ajax') }}",
                            dataType: "json",
                            data: $.extend(data, filters),
                            success: function(response) {
                                console.log('Server response:', response);
                                callback({
                                    draw: data.draw,
                                    recordsTotal: response.recordsTotal,
                                    recordsFiltered: response.recordsFiltered,
                                    data: response.data
                                });
                            }
                        });
                    },
                    columns: [{
                            data: 'document_number',
                            name: 'quotation_invoice_number',
                            searchable: false,
                            orderable: true,
                        },
                        @if (Auth::guard('user')->user()->role_id == 4)
                            {
                                data: 'company_name',
                                name: 'company_name',
                                searchable: false,
                                orderable: true,
                            },
                        @else
                            {
                                data: 'customer_name',
                                name: 'quote_name',
                                searchable: false,
                                orderable: true,
                            },
                        @endif

                        @if (Auth::guard('user')->user()->role_id == 2)
                            {
                                data: 'grand_total',
                                name: 'grand_total',
                                searchable: false,
                                orderable: true,
                            }, {
                                data: 'paid',
                                name: 'paid',
                                searchable: false,
                                orderable: true,
                            },
                        @endif {
                            data: 'all_status_elements',
                            name: 'status',
                            searchable: false,
                            orderable: true,
                        },
                        {
                            data: 'date1',
                            name: 'invoice_date',
                            searchable: false,
                            orderable: true,
                        },
                        @if (Auth::guard('user')->user()->role_id == 2)
                            {
                                data: 'regards',
                                name: 'regards',
                                orderable: true,
                                searchable: false
                            },
                        @endif {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    search: {
                        "regex": true
                    },
                    order: [
                        [dateColumn, 'desc']
                    ],
                    @if (Auth::guard('user')->user()->role_id == 2)

                        initComplete: function (settings) {
                    
                            $('#' + tableId + '_wrapper thead th').resizable({
                                handles: 'e',
                                alsoResize: '#' + tableId + '_wrapper .dataTables_scrollHead table', //Not essential but makes the resizing smoother
                                start: function (e) {
                            
                                    document.querySelectorAll('th').forEach(target => {
                                        target.addEventListener("click", preventOrdering, true);
                                    });

                                    $('#' + tableId + '_wrapper .dataTables_scrollHead table').on('mouseup', preventOrdering);

                                },
                                stop: function(e, ui) {

                                    document.querySelectorAll('th').forEach(target => {
                                        target.removeEventListener("click", preventOrdering, true);
                                    });

                                    $('#' + tableId + '_wrapper .dataTables_scrollHead table').off('mouseup', preventOrdering);
                        
                                    var table_width = $('#' + tableId + '_wrapper .dataTables_scrollHead table').width();
                                    $('#' + tableId + '_wrapper .dataTables_scrollBody table').width(table_width);
                                    $('#' + tableId + '_wrapper .dataTables_scrollFoot table').width(table_width);
                        
                                    var index = $(this).index();
                                    $(this).width(ui.size.width);
                                    $('#' + tableId + '_wrapper .dataTables_scrollBody thead th').eq(index).width(ui.size.width);
                                    $('#' + tableId + '_wrapper .dataTables_scrollFoot tfoot th').eq(index).width(ui.size.width);
                                    saveColumnSettings();

                                }
                            });

                        },

                        rowGroup: {
                            dataSrc: ['date1'],
                            startRender: function(rows, group) {
                                return $('<tr/>')
                                    .append('<td colspan="7">' + group + '</td>')
                                    .append('<td/>');

                            },
                            endRender: function(rows, group) {
                                var grand_total_t = rows
                                    .data()
                                    .pluck('grand_total')
                                    .reduce(function(a, b) {
                                        if (b) {
                                            b = b.replace(/[\€]/g, ''); // Remove currency symbol if present
                                            //b = b.replace(/\./g, ''); // Remove thousands separators
                                            b = b.replace(/\,/g, ''); // Replace decimal comma with dot
                                            return a + parseFloat(b) || 0; // Convert to float and accumulate
                                        }
                                        return a;
                                    }, 0);
                                grand_total_t = grand_total_t.toFixed(2);

                                var paid_total = rows
                                    .data()
                                    .pluck('paid')
                                    .reduce(function(a, b) {
                                        if (b) {
                                            b = b.replace(/[\€]/g, ''); // Remove currency symbol if present
                                            //b = b.replace(/\./g, ''); // Remove thousands separators
                                            b = b.replace(/\,/g, ''); // Replace decimal comma with dot
                                            return a + parseFloat(b) || 0; // Convert to float and accumulate
                                        }
                                        return a;
                                    }, 0);
                                paid_total = paid_total.toFixed(2);

                                return $('<tr/>')
                                    .append('<td colspan="2" style="color: #0097bd;">Group Totals</td>')
                                    .append('<td style="color: #0097bd;">€ ' + grand_total_t + '</td>')
                                    .append('<td colspan="4" style="color: #0097bd;">€ ' + paid_total + '</td>')
                                    .append('<td/>');

                            },

                        },
                        footerCallback: function(row, data, start, end, display) {
                            var api = this.api();

                            // Function to parse and format values
                            var intVal = function(i) {
                                if (typeof i === 'string') {
                                    // Remove currency symbols and replace comma with dot
                                    i = i.replace(/[\€]/g, ''); // Remove currency symbol
                                    //i = i.replace(/\./g, ''); // Remove thousands separators (if periods are used)
                                    i = i.replace(/\,/g, ''); // Replace decimal comma with dot
                                }
                                return parseFloat(i) || 0; // Convert to float and handle non-numeric values
                            };

                            // Check the column indexes
                            var grandTotalIndex = api.column(2).index();
                            var paidIndex = api.column(3).index();

                            // Debug column indexes
                            console.log('Grand Total Column Index:', grandTotalIndex);
                            console.log('Paid Column Index:', paidIndex);

                            // Calculate page totals for 'grand_total'
                            var pageTotal = api
                                .column(grandTotalIndex, {
                                    page: 'current'
                                })
                                .data()
                                .reduce(function(a, b) {
                                    return a + intVal(b);
                                }, 0).toFixed(2);;

                            // Calculate page totals for 'paid'
                            var pageTotal1 = api
                                .column(paidIndex, {
                                    page: 'current'
                                })
                                .data()
                                .reduce(function(a, b) {
                                    return a + intVal(b);
                                }, 0).toFixed(2);

                            // Update footer
                            $(api.column(grandTotalIndex).footer()).html('€ ' + pageTotal);
                            $(api.column(paidIndex).footer()).html('€ ' + pageTotal1);
                        },
                        
                    @endif
                });

                function preventOrdering(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }

                function getTableWidth()
                {
                    $.ajax({

                        type: "GET",
                        data: "screen_width=" + screen_width + '&table_id=quotations',
                        url: "<?php echo route('get-table-widths'); ?>",

                        success: function (data) {

                            if(data)
                            {
                                userColumnDefs = JSON.parse(data.column_defs);
                                table_width = data.table_width;
                            }

                            setUserColumnsDefWidths();

                        },
                        error: function (data) {

                        }

                    });
                }

                function setUserColumnsDefWidths() {

                    // localStorage.clear();

					// Get the settings for this table from localStorage
					// var userColumnDefs = JSON.parse(localStorage.getItem("quotations_table")) || [];

					if (userColumnDefs.length === 0) return;

                    // var table_width = localStorage.getItem('quotations_table_width');

					$('#' + tableId + '_wrapper .dataTables_scrollHead table thead tr').find("th").each( function ( target ) {

                        // Check if there is a setting for this column in localStorage
						existingSetting = userColumnDefs.findIndex( function(column) { return column.targets === target; } );
                        
                        if ( existingSetting !== -1 ) {

                            var index = $(this).index();
                            // Update the width
                            $(this).width(userColumnDefs[existingSetting].width);
                            $('#' + tableId + '_wrapper .dataTables_scrollBody thead th').eq(index).width(userColumnDefs[existingSetting].width);
                            $('#' + tableId + '_wrapper .dataTables_scrollFoot tfoot th').eq(index).width(userColumnDefs[existingSetting].width);
	
						}

					});

                    $('#' + tableId + '_wrapper table').width(table_width);

				}

				function saveColumnSettings() {
	
					// var userColumnDefs = JSON.parse(localStorage.getItem("quotations_table")) || [];
	
					var width, header, existingSetting;

					$('#' + tableId + '_wrapper .dataTables_scrollHead table thead tr').find("th").each( function ( targets ) {
	
						// Check if there is a setting for this column in localStorage
						existingSetting = userColumnDefs.findIndex(function(column) { return column.targets === targets; });
							
                        table_width = $('#' + tableId + '_wrapper .dataTables_scrollHead table').width();

						// Get the width of this column
						width = $(this).width();
	
						if ( existingSetting !== -1 ) {
	
							// Update the width
							userColumnDefs[existingSetting].width = width;
	
						} else {
	
							// Add the width for this column
							userColumnDefs.push({
								targets: targets,
								width:  width,
							});
	
						}
	
					});

                    var token = $("#token").val();

                    // Save (or update) the settings in database
                    $.ajax({

                        type: "POST",
                        data: "table_width=" + table_width + "&column_defs=" + JSON.stringify(userColumnDefs) + "&screen_width=" + screen_width + '&table_id=quotations' + "&_token=" + token,
                        url: "<?php echo route('update-table-widths'); ?>",

                        success: function (data) {

                        },
                        error: function (data) {

                        }

                    });

					// Save (or update) the settings in localStorage
					localStorage.setItem('quotations_table_width', table_width);
                    localStorage.setItem("quotations_table", JSON.stringify(userColumnDefs));
				}

                function filter(page_load = 0) {
                    // Custom filtering function which will search data in column five between two values
                    // $.fn.dataTable.ext.search.push(
                    //     function(settings, data, dataIndex) {
                    //         var filter_status = $(".filter_status").val();
                    //         var filter_month = $(".filter_month").val();
                    //         var filter_year = $(".filter_year").val();
                    //         var dateAr = /(\d+)\-(\d+)\-(\d+)/.exec(data[dateColumn]);
                    //         var format_start = dateAr[3] + '-' + dateAr[2] + '-' + dateAr[1];
                    //         var date = new Date(format_start);
                    //         var day = date.getDate();
                    //         var month = date.getMonth() + 1;
                    //         var year = date.getFullYear().toString();
                    //         var status = data[statusColumn];
                    //         var order_status = data[orderStatusColumn];

                    //         month = month > 9 ? "" + month : "0" + month;

                    //         if (((filter_year == "" && filter_month == "") || ((filter_year && filter_month) && (filter_year == year && filter_month == month)) || (((filter_year && filter_month == "") && (filter_year == year)) || ((filter_month && filter_year == "") && (filter_month == month)))) && ((filter_status == "") || ((filter_status == status) || (filter_status == order_status)))) {
                    //             return true;
                    //         } else {
                    //             return false;
                    //         }

                    //     }

                    // );

                    // var filter_text = $("#filter_text").val();
                    // table.search(filter_text).draw();

                    if (!page_load) {
                        var filter_month = $(".filter_month").val();
                        var filter_year = $(".filter_year").val();
                        var filter_status = $(".filter_status").val();

                        $.ajax({

                            type: "GET",
                            data: "filter_month=" + filter_month + '&filter_year=' + filter_year + '&filter_status=' + filter_status + '&type=1',
                            url: "<?php echo route('user-update-filter'); ?>",
                            success: function(data) {},
                            error: function(data) {}
                        });

                        table.ajax.reload();
                    }
                }

                table.on('draw', function () {
                    getTableWidth();
                });

                $('.dataTables_filter input').on('input', function() {
                    filter();
                });

                $('.filter_month, .filter_year, .filter_status').on('change', function() {
                    filter();
                });

                $(window).on("load", function() {
                    filter(1);
                });
            });
        </script>
    @endif
@endsection
