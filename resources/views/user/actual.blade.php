@extends('layouts.handyman')

@section('content')

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div class="section-padding add-product-1" style="padding: 0;">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header products" style="display: flex;justify-content: space-between;align-items: center;">

                                        <h2 style="display: inline-block;">{{__('text.Actual')}}</h2>
                                        <div style="display: flex;align-items: center;">
                                            <button type="button" href="#myModal5" role="button" data-toggle="modal" style="margin-right: 10px;background-color: #4dd5b2 !important;border-color: #ffffff00 !important;border-radius: 30px;" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export to Reeleezee')}}</button>
                                            <button type="button" class="tooltip1 add-pc" style="cursor: pointer;font-size: 20px;width: 30px;height: 30px;line-height: 28px;border: 2px solid #8b8b8b;border-radius: 25px;background-color: transparent;padding: 0;">
											    <i id="next-row-icon" class="fa fa-fw fa-plus" style="width: 100%;"></i>
                                            </button>
                                        </div>

                                    </div>
                                    <hr>
                                    <div>

                                        @include('includes.form-success')

                                        <div class="row">
                                            <div class="col-sm-12">

                                                <input type="hidden" name="filter_text" id="filter_text">

                                                <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" cellspacing="0">
                                                    <thead>

                                                        <tr role="row">

                                                            <th class="sorting a_col" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Date')}}</th>

                                                            <th class="sorting b_col" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Description')}}</th>

                                                            <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Paid By')}}</th>

                                                            <th class="sorting a_col" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Amount')}}</th>

                                                            <th class="sorting a_col" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Action')}}</th>

                                                        </tr>

                                                    </thead>

                                                    <tbody>

                                                        <?php $min_date = strtotime(date("d-m-Y")); ?>

                                                        @foreach($data as $i => $key)

                                                            @foreach($key->payment_calculations as $payment)

                                                                @if($key->getTable() != 'new_quotations' || ($key->getTable() == 'new_quotations' && !$key->invoice))

                                                                    @if($payment->paid_by != "Pending")

                                                                        <?php if(strtotime($payment->date) < $min_date){ $min_date = strtotime($payment->date); } ?>

                                                                        <tr data-row="{{$payment->id.'A'}}" role="row" class="odd">
                                                                    
                                                                            <?php $date = strtotime($payment->date) + $payment->id; $date1 = date('d-m-Y',$date); ?>

                                                                            <td data-sort="{{$date}}">{{$date1}}</td>

                                                                            <td>{{$key->name ? $key->name . ($key->family_name ? " " . $key->family_name : "") . ", " : ""}}{{$key->getTable() == 'new_quotations' ? "QUO# ".$key->quotation_invoice_number : "INV# ".$key->invoice_number}}</td>

                                                                            <td>{{$payment->paid_by}}</td>

                                                                            <td>€ {{number_format((float)$payment->amount, 2, ',', '.')}}</td>

                                                                            <td style="text-align: center;">
                                                                                <div class="dropdown">
                                                                                    <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                                        <span class="caret"></span>
                                                                                    </button>
                                                                                
                                                                                    <ul class="dropdown-menu">
                                                                                        <li><a class="edit-payment" data-id="{{$key->getTable() == 'new_quotations' ? $key->id.'Q' : $key->id.'I'}}" data-payment_id="{{$payment->id}}" data-type="1" data-date="{{$date1}}" data-amount="{{number_format((float)$payment->amount, 2, ',', '')}}" data-paid_by="{{$payment->paid_by}}" data-description="{{$payment->description}}" data-description1="{{$payment->description1}}" data-ledger="{{$payment->general_ledger}}" href="javascript:void(0)">{{__('text.Edit')}}</a></li>
                                                                                        <li><a class="delete-payment" data-payment_id="{{$payment->id}}" data-type="1" data-quotation="{{$key->getTable() == 'new_quotations' ? 1 : 0}}" href="javascript:void(0)">{{__('text.Delete')}}</a></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </td>

                                                                        </tr>

                                                                    @endif

                                                                @endif

                                                            @endforeach

                                                        @endforeach

                                                        @foreach($other_payments as $i => $key)

                                                            <tr data-row="{{$key->id.'B'}}" role="row" class="odd">
                                                                    
                                                                <?php $date = strtotime($key->date) + $key->id; $date1 = date('d-m-Y',$date); ?>

                                                                <td data-sort="{{$date}}">{{$date1}}</td>

                                                                <td>{{$key->description1}}{{$key->title ? ($key->description1 ? ", ".$key->title : $key->title) : ""}}</td>

                                                                <td>{{$key->paid_by}}</td>

                                                                <td>€ {{number_format((float)$key->amount, 2, ',', '.')}}</td>

                                                                <td style="text-align: center;">
                                                                    <div class="dropdown">
                                                                        <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                            <span class="caret"></span>
                                                                        </button>
                                                                        
                                                                        <ul class="dropdown-menu">
                                                                            <li><a class="edit-payment" data-payment_id="{{$key->id}}" data-type="{{$key->invoice_type}}" data-date="{{$date1}}" data-amount="{{number_format((float)$key->amount, 2, ',', '')}}" data-paid_by="{{$key->paid_by}}" data-description1="{{$key->description1}}" data-ledger="{{$key->general_ledger}}" href="javascript:void(0)">{{__('text.Edit')}}</a></li>
                                                                            <li><a class="delete-payment" data-payment_id="{{$key->id}}" data-type="{{$key->invoice_type}}" href="javascript:void(0)">{{__('text.Delete')}}</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                    </tbody>

                                                    <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>

                                                </table>

                                            </div>
                                        </div>

                                        <div style="display: flex;margin: 20px 0;" class="row filters_row">
                                                        
                                            <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                <div style="margin: 0;" class="form-group">
                                                    <select style="min-width: 125px;" class="form-control filter_year">
                                                        <option value="">{{__("text.All Years")}}</option>
                                                        @for($y = date("Y",$min_date); $y <= date("Y"); $y++)
                                                                        
                                                            <option value="{{$y}}">{{$y}}</option>
    
                                                        @endfor
                                                    </select>
                                                </div>

                                            </div>
    
                                            <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                <div style="margin: 0;" class="form-group">
                                                    <select style="min-width: 125px;" class="form-control filter_month">
                                                        <option value="">{{__("text.All Months")}}</option>
                                                        @for ($m=1; $m <= 12; $m++)
                                                                        
                                                            <option value="{{sprintf('%02d', $m)}}">{{date('F', mktime(0,0,0,$m, 1, date('Y')))}}</option>
    
                                                        @endfor
                                                    </select>
                                                </div>

                                            </div>

                                            <div style="display: flex;justify-content: center;padding: 0 10px;">
                                                        
                                                <div style="margin: 0;" class="form-group">
                                                    <select style="min-width: 125px;" class="form-control filter_paid_by">

                                                        <option value="">{{__("text.Paid By")}}</option>
                                                        <option value="Mollie">{{__('text.Mollie')}}</option>
                                                        <option value="Betaallink">{{__('text.Pin device')}}</option>
                                                        <option value="Bank">{{__('text.Bank')}}</option>
                                                        <option value="Cash">{{__('text.Cash')}}</option>
                                                        <option value="Settled">{{__('text.Settled')}}</option>
                                                            
                                                    </select>
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
        </div>
    </div>

    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">

            <form method="POST" action="" id="payment_form">
                
                @csrf
                <input type="hidden" id="payment_id" name="payment_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 style="margin: 0;" id="myModalLabel">{{__("text.Create New Payment")}}</h3>
                    </div>
    
                    <div class="modal-body payment-modal" id="myWizard" style="display: flex;flex-wrap: wrap;">
    
                        <div class="form-group col-sm-6">
                            <label style="font-size: 14px;">{{__("text.Date")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input id="pc_date" name="pc_date" style="background: transparent;" class="form-control validation" readonly type="text">
                            </div>
                        </div>
    
                        <div class="form-group col-sm-6 invoice_type_box">
                            <label style="font-size: 14px;">{{__("text.Type of invoice")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="invoice_type" name="invoice_type">
                                    <option value="1">{{__('text.Sales Invoice')}}</option>
                                    <option value="2">{{__('text.Purchase Invoice')}}</option>
                                    <option value="3">{{__('text.Other')}}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group col-sm-6 q_i_box">
                            <label style="font-size: 14px;">{{__("text.Select Quotation/Invoice")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="quote-invoice-select" name="quotation_invoice_id">
                                    <option value=""></option>
                                    @foreach($data as $key)
                                        @if($key->getTable() == 'new_quotations')
                                            <option value="{{$key->id}}Q">{{"QUO# ".$key->quotation_invoice_number}}</option>
                                        @else
                                            <option value="{{$key->id}}I">{{"INV# ".$key->invoice_number}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="form-group col-sm-6">
                            <label style="font-size: 14px;">{{__('text.Amount')}} (€)</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-money"></i>
                                </div>
                                <input autocomplete="off" type="text" maskedformat="9,1" class="form-control pc_amount" name="pc_amount" required>
                            </div>
                        </div>
    
                        <div class="form-group col-sm-6">
                            <label style="font-size: 14px;">{{__("text.Paid by")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="form-control pc_paid_by validation" name="pc_paid_by">
                                    <option value="Pending">{{__('text.Pending payments')}}</option>
                                    <option value="Mollie">{{__('text.Mollie')}}</option>
                                    <option value="Betaallink">{{__('text.Pin device')}}</option>
                                    <option value="Bank">{{__('text.Bank')}}</option>
                                    <option value="Cash">{{__('text.Cash')}}</option>
                                    <option value="Settled">{{__('text.Settled')}}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="form-group col-sm-6 description_box">
                            <label style="font-size: 14px;">{{__("text.Description")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="form-control pc_description" name="pc_description">
                                    <option value="By accepting">{{__('text.By accepting')}}</option>
                                    <option value="By delivery goods">{{__('text.By delivery goods')}}</option>
                                    <option value="By finishing work">{{__('text.By finishing work')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group col-sm-6 description1_box">
                            <label style="font-size: 14px;">{{__("text.Description")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <input type="text" class="form-control pc_description1" name="pc_description1">
                            </div>
                        </div>

                        <div class="form-group col-sm-6">
                            <label style="font-size: 14px;">{{__("text.General Ledgers")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="form-control general_ledger" name="general_ledger">
                                    <option value="">{{__("text.Select General Ledger")}}</option>
                                    @foreach($general_ledgers as $ledger)
                                        <option value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                    </div>
    
                    <div class="modal-footer">
                        <button type="button" style="border: 0;outline: none;background-color: #5cb85c !important;" class="btn btn-primary submit-payment">{{__("text.Create")}}</button>
                    </div>
    
                </div>     
            </form>

		</div>
	</div>

    <div id="myModal5" class="modal fade" role="dialog">
		<div class="modal-dialog">

            <form id="reeleezee-export-form" method="POST" action="{{route('export-payments-reeleezee')}}">
                <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                <!-- Modal content-->
			    <div class="modal-content">
				    <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal">&times;</button>
					    <h4 class="modal-title">{{__('text.Export by date')}}</h4>
				    </div>
				    <div style="padding: 30px 20px;display: inline-block;" class="modal-body">

                        <div class="wrapper-options1 panel-group" id="accordion">

                            <div class="date-select-box panel panel-default">

                                <input type="radio" value="1" class="select-form" name="export_by" id="option1-1">

                                <label for="option1-1" class="option1 option1-1">
                                    <div class="dot"></div>
                                    <span>{{__('text.Payment Date')}}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1"></a>
                                </label>

                                <div id="collapse1" class="panel-collapse collapse">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="created_start_date" id="created_start_date">
                                        <input type="text" placeholder="{{__('text.End Date')}}" readonly class="export_dates" name="created_end_date" id="created_end_date">
                                    </div>
                                </div>

                            </div>

                            <div class="date-select-box panel panel-default">

                                <input type="radio" value="2" class="select-form" name="export_by" id="option1-2">
                            
                                <label for="option1-2" class="option1 option1-2">
                                    <div class="dot"></div>
                                    <span>{{__('text.Updated Date')}}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2"></a>
                                </label>

                                <div id="collapse2" class="panel-collapse collapse">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="updated_start_date" id="updated_start_date">
                                        <input type="text" placeholder="{{__('text.End Date')}}" readonly class="export_dates" name="updated_end_date" id="updated_end_date">
                                    </div>
                                </div>

                            </div>

                            <div class="date-select-box panel panel-default">

                                <input checked type="radio" value="3" class="select-form" name="export_by" id="option1-3">
                                
                                <label for="option1-3" class="option1 option1-3">
                                    <div class="dot"></div>
                                    <span>{{__('text.Last Export Date')}}</span>
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3"></a>
                                </label>

                                <div id="collapse3" class="panel-collapse collapse in">
                                    <div style="display: flex;margin: 10px;">
                                        <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="last_start_date" id="last_start_date">
                                    </div>
                                </div>

                            </div>
                        
                        </div>

                        <div style="padding: 0;" class="form-group col-sm-12">
                            <label style="font-size: 14px;">{{__("text.Paid by")}}</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-file"></i>
                                </div>
                                <select class="form-control" name="paid_by_filter">
                                    <option value="">{{__('text.All')}}</option>    
                                    <option value="Mollie">{{__('text.Mollie')}}</option>
                                    <option value="Betaallink">{{__('text.Pin device')}}</option>
                                    <option value="Bank">{{__('text.Bank')}}</option>
                                    <option value="Cash">{{__('text.Cash')}}</option>
                                    <option value="Settled">{{__('text.Settled')}}</option>
                                </select>
                            </div>
                        </div>

				    </div>
				    <div class="modal-footer">
					    <button type="submit" class="btn btn-success">{{__('text.Submit')}}</button>
				    </div>
			    </div>

            </form>

		</div>
	</div>

    <style type="text/css">

        .date-select-box
        {
            width: 100%;
            margin: 5px 0;
        }

        .export_dates
        {
            width: 100%;
            height: 35px;
            border: 1px solid #b3b3b3;
            border-radius: 5px;
            padding: 5px;
            outline: none !important;
            margin: 0 5px;
        }

        .wrapper-options1{
            display: inline-flex;
            flex-direction: column;
            width: 100%;
            align-items: center;
            justify-content: space-evenly;
            border-radius: 5px;
            padding: 0;
        }
        .wrapper-options1 .option1{
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
        .wrapper-options1 .option1 .dot{
            height: 20px;
            width: 20px;
            background: #d9d9d9;
            border-radius: 50%;
            position: relative;
        }
        .wrapper-options1 .option1 .dot::before{
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
        .wrapper-options1 input[type="radio"]{
            display: none;
        }
        .wrapper-options1 input[type="radio"]:checked + label{
            border-color: #0069d9;
            background: #0069d9;
        }
        .wrapper-options1 input[type="radio"]:checked + label .dot{
            background: #fff;
        }
        .wrapper-options1 input[type="radio"]:checked + label .dot::before{
            opacity: 1;
            transform: scale(1);
        }
        .wrapper-options1 .option1 span{
            font-size: 20px;
            color: #808080;
            margin-left: 10px;
            margin-top: -1px;
        }
        .wrapper-options1 input[type="radio"]:checked + label span{
            color: #fff;
        }
        .wrapper-options1 .option1 a::before
        {
            display: none;
        }
    
        .select2-selection__rendered
        {
            white-space: normal !important;
        }

        .totals_cols
        {
            padding: 0 !important;
            border-top: 1px solid black !important;
        }

        .totals_cons
        {
            display: flex;
            align-items: center;
        }

        .totals_spans
        {
            padding: 5px 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
        }

        .a_col
        {
            width: 20%;
        }

        .b_col
        {
            width: 40%;
        }

        .cc_row:not(:first-child)
        {
            margin-top: 20px;
        }

        .mt-10
        {
            margin-top: 10px;
        }

        td .btn
        {
            white-space: normal;
            font-size: 13px;
            padding: 1px 5px;
        }
    
        table.dataTable .dtrg-group
        {
            font-size: 15px;
            font-weight: 600;
            background-color: #FDFEFE !important;
        }

        table.dataTable .dtrg-group th
        {
            padding-left: 5px !important;
            padding: 15px;
        }

        .dataTables_scrollHead table
        {
            margin-top: 40px !important;
        }
    
        table.dataTable
        {
            table-layout: fixed;
        }
    
        .table.products > thead > tr > th
        {
            border-right: 1px solid black;
        }

        table.dataTable > thead > tr > th:first-child
        {
            border-left: 1px solid black;
        }

        .dataTables_scrollBody .table.products > thead > tr > th
        {
            border: none !important;
        }
    
        .hovertext
        {
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

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.child
        {
            position: relative;
        }

        select {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            text-indent: 1px !important;
            text-overflow: '' !important;
        }

        .text-left a
        {
            color: #337ab7;
        }

        .text-left b
        {
            font-size: 13px;
        }

        .swal2-popup{

            width: 25% !important;

        }

        .swal2-icon.swal2-warning{

            width: 20% !important;
            height: 82px !important;

        }

        .swal2-title{

            font-size: 27px !important;

        }

        .swal2-content{

            font-size: 18px !important;

        }

        .swal2-actions{

            font-size: 13px !important;

        }

        table.dataTable thead>tr>th.sorting_asc, table.dataTable thead>tr>th.sorting_desc, table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>td.sorting_asc, table.dataTable thead>tr>td.sorting_desc, table.dataTable thead>tr>td.sorting{
            padding-right: 0;
            padding-left: 12px;
            text-align: left;
            border-top: 1px solid black !important;
            border-bottom: 1px solid black !important;
        }

        #img{

            width: 100% !important;
            display: block !important;
        }

        #photo{
            width: 250px !important;
        }

        #client{
            width: 230px !important;
        }

        #handyman{
            width: 230px !important;
        }

        #serv{
            width: 170px !important;
        }

        #rate{
            width: 175px !important;
        }

        #service{
            width: 151px !important;
        }

        #date{
            width: 158px !important;
        }

        #amount{
            width: 160px !important;
        }

        #status{
            width: 77px !important;
        }

        .table.products > tbody > tr > td
        {

            text-align: left;
            padding-left: 12px;

        }

        .select2-container--default .select2-selection--single {
			border: 1px solid #ccc;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
		}

        .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__rendered, .select2-container--default .select2-selection--single .select2-selection__arrow, .select2-container--default .select2-selection--single .select2-selection__rendered
		{
			line-height: 35px;
			height: 35px;
		}

        .dataTables_wrapper .dataTables_length select.form-control
        {
            line-height: 1;
        }

        /* td span.btn
        {
            width: 100%;
        } */

    </style>

@endsection

@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
	<script src="{{asset('assets/admin/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/locale/nl.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js"></script>

    <script>

        $(document).ready( function () {

            $('.export_dates').datetimepicker({
                format: 'YYYY-MM-DD',
                locale:'du',
                ignoreReadonly: true
            });

            $('#option1-1').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

            $('#option1-2').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

            $('#option1-3').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

            $('.invoice_type').on('change', function () {
                var value = $(this).val();
                if(value == 1)
                {
                    $(".q_i_box").show();
                    $(".description_box").show();
                }
                else
                {
                    $(".q_i_box").hide();
                    $(".description_box").hide();
                    $(".pc_paid_by").val("Cash");
                }
            });

            var now = new Date();

			$('#pc_date').datetimepicker({
				format: 'DD-MM-YYYY',
				// minDate: now,
				defaultDate: now,
				ignoreReadonly: true,
				locale:'du',
			});

            $(".invoice_type").select2({
				width: '100%',
				placeholder: "{{__('text.Select Invoice Type')}}",
				allowClear: false,
                dropdownParent: $('.payment-modal'),
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

            $(".quote-invoice-select").select2({
				width: '100%',
				placeholder: "{{__('text.Select Quotation/Invoice')}}",
				allowClear: true,
                dropdownParent: $('.payment-modal'),
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

            $(".pc_description").select2({
				width: '100%',
				allowClear: false,
                dropdownParent: $('.payment-modal'),
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

            // $(".pc_description1").select2({
			// 	width: '100%',
			// 	allowClear: false,
            //     tags: true,
            //     dropdownParent: $('.payment-modal'),
			// 	"language": {
			// 		"noResults": function () {
			// 			return '{{__('text.No results found')}}';
			// 		}
			// 	},
			// }).on("select2:select", function (e) {
            //     var tag_item = $(this).find(":selected").data("select2-tag");
            //     if(tag_item)
            //     {
            //         $(this).find(":selected").removeAttr('data-select2-tag');
            //     }
            // });

            $(".general_ledger").select2({
				width: '100%',
				placeholder: "{{__('text.Select General Ledger')}}",
				allowClear: true,
                dropdownParent: $('.payment-modal'),
				"language": {
					"noResults": function () {
						return '{{__('text.No results found')}}';
					}
				},
			});

            $(document).on('keypress', ".pc_amount", function (e) {
                
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

            $(document).on('focusout', ".pc_amount", function (e) {

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

            var tableId = 'example';
            var dateColumn = 0;
            var descColumn = 1;
            var paidByColumn = 2;
            var amountColumn = 3;
                
            var table = $('#' + tableId).DataTable({
                order: [[dateColumn, 'desc']],
                columnDefs: [{ 'visible': false, 'targets': [paidByColumn] }],
                autoWidth: false,
                // responsive: false,
                // scrollX: true,
                pageLength: 10,
                aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'ALL']],
                language: {
                    decimal: ',',
                    thousands: '.',
                    "sLengthMenu": "<?php echo __('text.Show') . ' _MENU_ ' . __('text.records'); ?>",
                    "sSearch": "<?php echo __('text.Search') . ':' ?>",
                    "sInfo": "<?php echo __('text.Showing') . ' _START_ ' . __('text.to') . ' _END_ ' . __('text.of') . ' _TOTAL_ ' . __('text.items'); ?>",
                    "sInfoEmpty": "<?php echo __('text.No data available in table'); ?>",
                    "sZeroRecords": "<?php echo __('text.No data available in table'); ?>",
                    "sInfoFiltered": "<?php echo '- ' . __('text.filtered from') . ' _MAX_ ' . __('text.records'); ?>",
                    "oPaginate": {
                        "sPrevious": "<?php echo __('text.Previous'); ?>",
                        "sNext": "<?php echo __('text.Next'); ?>"
                    },
                    "sEmptyTable": '<?php echo __('text.No data available in table'); ?>'
                },
                // stateSave: true,
                headerCallback: function( thead, data, start, end, display ) {

                    var filter_paid_by = $(".filter_paid_by").val();

                    var intVal = function (i) {
                        if(typeof i == 'string')
                        {
                            i = i.replace(/[\€.]/g, '');
                            i = i.replace(/\,/g, '.') * 1;
                        }
                        else if(typeof i != 'number')
                        {
                            i = 0;
                        }

                        i = parseFloat(i.toFixed(2));
                        return i;
                    }

                    var api = this.api();
                    
                    var positives = 0;
                    api
                    .column(amountColumn, { search: 'applied' })
                    .data()
                    .reduce(function (a, b) {
                        if(intVal(b) >= 0)
                        {
                            positives = positives + intVal(b);
                        }
                    }, 0);

                    positives = positives.toFixed(2);
                    positives = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(positives);

                    var negatives = 0;
                    api
                    .column(amountColumn, { search: 'applied' })
                    .data()
                    .reduce(function (a, b) {
                        if(intVal(b) <= 0)
                        {
                            negatives = negatives + intVal(b);
                        }
                    }, 0);

                    negatives = negatives.toFixed(2);
                    negatives = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(negatives);

                    var total = 0;

                    $.each(data, function (i, x) {
                        if(filter_paid_by == "" || filter_paid_by == x[paidByColumn])
                        {
                            total = total + intVal(x[amountColumn]);
                        }
                    });

                    // var total = 0;
                    // api
                    // .column(amountColumn, { search: 'applied' })
                    // .data()
                    // .reduce(function (a, b) {
                    //     total = total + intVal(b);
                    // }, 0);

                    total = total.toFixed(2);
                    total = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(total);

                    var parent_thead = $(thead).parents("thead");
                    parent_thead.find(".totals_row").remove();
                    parent_thead.prepend("<tr class='totals_row'>"+
                        "<th class='a_col totals_cols'></th>"+
                        "<th class='b_col totals_cols'>"+
                            "<div class='totals_cons'>"+
                                "<span class='totals_spans' style='width: 60%;background-color: #a1eda1;'>Total amount + <br> € "+positives+"</span>"+
                                "<span class='totals_spans' style='width: 40%;background-color: #e6e8ad;'>Total amount - <br> € "+negatives+"</span>"+
                            "</div>"+
                        "</th>"+
                        "<th colspan='2' class='b_col totals_cols'>"+
                            "<div class='totals_cons'>"+
                                "<span class='totals_spans'>Total amount <br> € "+total+"</span>"+
                            "</div>"+
                        "</th>"+
                    "</tr>");
                },
                footerCallback: function (row, data, start, end, display) {
                    
                    var api = this.api();

                    // Remove the formatting to get integer data for summation
                    var intVal = function (i) {
                        if(typeof i == 'string')
                        {
                            i = i.replace(/[\€.]/g, '');
                            i = i.replace(/\,/g, '.') * 1;
                        }
                        else if(typeof i != 'number')
                        {
                            i = 0;
                        }

                        i = parseFloat(i.toFixed(2));
                        return i;
                    };

                    // Total over all pages
                    // var total = 0;
                    // api
                    // .column(amountColumn)
                    // .data()
                    // .reduce(function (a, b) {
                    //     total = total + intVal(b);
                    // }, 0);

                    // Total over this page
                    var pageTotal = 0;
                    api
                    .column(amountColumn, { page: 'current' })
                    .data()
                    .reduce(function (a, b) {
                        pageTotal = pageTotal + intVal(b);
                    }, 0);

                    pageTotal = pageTotal.toFixed(2);
                    pageTotal = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(pageTotal);
                
                    // Update footer
                    $(api.column(amountColumn).footer()).html('€ ' + pageTotal);
                }
            });

            function refresh()
            {
                $("#payment_id").val("");
                $(".invoice_type").attr("disabled",false);
                $(".invoice_type").val(1);
                $(".invoice_type").trigger('change.select2');
                $(".quote-invoice-select").attr("disabled",false);
                $(".quote-invoice-select").val("");
                $(".quote-invoice-select").trigger('change.select2');
                $(".pc_amount").val("");
                $(".pc_paid_by").val("Pending");
                $(".pc_description").val("By accepting");
                $(".pc_description").trigger('change.select2');
                $(".pc_description1").val("");
                $(".general_ledger").val("");
                $(".general_ledger").trigger('change.select2');
                $(".q_i_box").show();
                $(".description_box").show();
            }

            $(document).on('click', ".add-pc", function (e) {

                refresh();
                $("#myModal").find("#myModalLabel").text("{{__('text.Create New Payment')}}");
                $("#myModal").find(".submit-payment").text("{{__('text.Create')}}");
                $("#myModal").modal("toggle");

            });

            $(document).on('click', ".edit-payment", function (e) {

                refresh();
                var payment_id = $(this).data("payment_id");
                var type = $(this).data("type");
                var date = $(this).data("date");
                var amount = $(this).data("amount");
                var paid_by = $(this).data("paid_by");
                var description1 = $(this).data("description1");
                var ledger = $(this).data("ledger");

                $("#payment_id").val(payment_id);
                $("#pc_date").val(date);
                $(".invoice_type").attr("disabled",true);
                $(".invoice_type").val(type);
                $(".invoice_type").trigger('change.select2');
                $(".pc_amount").val(amount);
                $(".pc_paid_by").val(paid_by);
                $(".pc_description1").val(description1);
                $(".general_ledger").val(ledger);
                $(".general_ledger").trigger('change.select2');

                if(type == 1)
                {
                    var id = $(this).data("id");
                    var description = $(this).data("description");

                    $(".quote-invoice-select").attr("disabled",true);
                    $(".quote-invoice-select").val(id);
                    $(".quote-invoice-select").trigger('change.select2');
                    $(".pc_description").val(description);
                    $(".pc_description").trigger('change.select2');

                    $(".q_i_box").show();
                    $(".description_box").show();
                }
                else
                {
                    $(".quote-invoice-select").attr("disabled",false);
                    $(".quote-invoice-select").val("");
                    $(".quote-invoice-select").trigger('change.select2');
                    $(".pc_description").val("By accepting");
                    $(".pc_description").trigger('change.select2');

                    $(".q_i_box").hide();
                    $(".description_box").hide();
                }
                
                $("#myModal").find("#myModalLabel").text("{{__('text.Edit Payment')}}");
                $("#myModal").find(".submit-payment").text("{{__('text.Update')}}");
                $("#myModal").modal("toggle");

            });

            $(document).on('click', ".submit-payment", function (e) {

                var payment_id = $("#payment_id").val();
                var data = $("#payment_form").serialize();
                var type = $(".invoice_type").val();
                var date = $("#pc_date").val();
                var amount = $(".pc_amount").val();
                var paid_by = $(".pc_paid_by").val();
                var pc_description = $(".pc_description").val();
                var pc_description1 = $(".pc_description1").val();
                var general_ledger = $(".general_ledger").val();
                var flag = 0;

                if(type == 1)
                {
                    var quotation_invoice = $(".quote-invoice-select").val();

                    if(!quotation_invoice)
                    {
                        flag = 1;
                        $(".q_i_box").find(".select2-selection").css("border-color","red");
                    }
                    else
                    {
                        $(".q_i_box").find(".select2-selection").css("border-color","");
                    }
                }

                if(!date)
                {
                    flag = 1;
                    $("#pc_date").css("border-color","red");
                }
                else
                {
                    $("#pc_date").css("border-color","");
                }

                if(!amount)
                {
                    flag = 1;
                    $(".pc_amount").css("border-color","red");
                }
                else
                {
                    $(".pc_amount").css("border-color","");
                }

                if(!flag)
                {
                    if(payment_id)
                    {
                        data = data + "&invoice_type=" + type + "&quotation_invoice_id=" + quotation_invoice;
                        var row_id = type == 1 ? payment_id + "A" : payment_id + "B";
                    }

                    $.ajax({
                        url: "{{route('submit-payment')}}",
                        type: 'POST',
                        data: data,
                        dataType: "json",
                        success: function(response){
                            if(type == 1)
                            {
                                var edit_btn = "<li><a class='edit-payment' data-id='"+quotation_invoice+"' data-payment_id='"+response["payment_id"]+"' data-type='"+type+"' data-date='"+date+"' data-amount='"+amount+"' data-paid_by='"+paid_by+"' data-description='"+pc_description+"' data-description1='"+pc_description1+"' data-ledger='"+general_ledger+"' href='javascript:void(0)'>{{__('text.Edit')}}</a></li>";
                                
                                if(quotation_invoice.indexOf('Q') != -1)
                                {
                                    var delete_btn = "<li><a class='delete-payment' data-payment_id='"+response["payment_id"]+"' data-type='"+type+"' data-quotation='1' href='javascript:void(0)'>{{__('text.Delete')}}</a></li>";
                                }
                                else
                                {
                                    var delete_btn = "<li><a class='delete-payment' data-payment_id='"+response["payment_id"]+"' data-type='"+type+"' data-quotation='0' href='javascript:void(0)'>{{__('text.Delete')}}</a></li>";
                                }
                                
                                var action = "<div class='dropdown'><button style='outline: none;' class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>{{__('text.Action')}} <span class='caret'></span></button><ul class='dropdown-menu'>"+edit_btn+delete_btn+"</ul></div>";
                            }
                            else
                            {
                                var action = "<div class='dropdown'><button style='outline: none;' class='btn btn-primary dropdown-toggle' type='button' data-toggle='dropdown'>{{__('text.Action')}} <span class='caret'></span></button><ul class='dropdown-menu'><li><a class='edit-payment' data-payment_id='"+response["payment_id"]+"' data-type='"+type+"' data-date='"+date+"' data-amount='"+amount+"' data-paid_by='"+paid_by+"' data-description1='"+pc_description1+"' data-ledger='"+general_ledger+"' href='javascript:void(0)'>{{__('text.Edit')}}</a></li><li><a class='delete-payment' data-payment_id='"+payment_id+"' data-type='"+type+"' href='javascript:void(0)'>{{__('text.Delete')}}</a></li></ul></div>";
                            }

                            if(payment_id)
                            {
                                var row_data = [
                                    {"@data-sort":response["date_sort"],"display":date},
                                    response["description"],
                                    paid_by,
                                    response["amount"],
                                    action
                                ];

                                table.row($('#' + tableId + " tbody").find('tr[data-row="'+row_id+'"]')).data(row_data).order([dateColumn,'desc']).draw(false);
                            }
                            else
                            {
                                table.row.add($('<tr data-row="'+response["payment_id"]+response["payment_type"]+'">\n' +
                                '<td data-sort="'+response["date_sort"]+'">'+date+'</td>\n' +
                                '<td>'+response["description"]+'</td>\n' +
                                '<td>'+paid_by+'</td>\n' +
                                '<td>'+response["amount"]+'</td>\n' +
                                '<td style="text-align: center;">'+action+'</td>' +
                                '</tr>')).order([dateColumn,'desc']).draw(false);
                            }

                            $("#myModal").modal("toggle");
                        },
                        error: function(){}
                    });
                }

            });

            $(document).on('click', ".delete-payment", function (e) {

                var row = $(this).parents('tr');
                var payment_id = $(this).data("payment_id");
                var quotation = $(this).data("quotation");
                var type = $(this).data("type");
                var token = $('[name="_token"]').val();

                $.ajax({
                    url: "{{route('delete-payment')}}",
                    type: 'POST',
                    data: { payment_id: payment_id,quotation: quotation,type: type,_token:token },
                    dataType: "json",
                    success: function(response){
                        table.row(row).remove().order([dateColumn,'desc']).draw(false);
                    },
                    error: function(){}
                });

            });
               
            var filters_row = $(".filters_row").html();
            $(".dataTables_wrapper").find('.row').first().after('<div style="display: flex;margin: 20px 0;" class="row filters_row1">'+filters_row+'</div>');
            $(".filters_row").remove();

            function filter()
            {
                // Custom filtering function which will search data in column five between two values
                $.fn.dataTable.ext.search.push(

                    function( settings, data, dataIndex ) {

                        var filter_paid_by = $(".filter_paid_by").val();
                        var filter_month = $(".filter_month").val();
                        var filter_year = $(".filter_year").val();
                        var dateAr = /(\d+)\-(\d+)\-(\d+)/.exec(data[dateColumn]);
                        var format_start = dateAr[3] + '-' + dateAr[2] + '-' + dateAr[1];
                        var date = new Date(format_start);
                        var day = date.getDate();
                        var month = date.getMonth() + 1;
                        var year = date.getFullYear().toString();
                        var paid_by = data[paidByColumn];
                    
                        month = month > 9 ? "" + month : "0" + month;

                        if (((filter_year == "" && filter_month == "") || ( (filter_year && filter_month) && (filter_year == year && filter_month == month) ) || ( ((filter_year && filter_month == "") && (filter_year == year)) || ((filter_month && filter_year == "") && (filter_month == month)) )) && ((filter_paid_by == "") || (filter_paid_by == paid_by)))
                        {
                            return true;
                        }
                        else{
                            return false;
                        }
                        
                    }
            
                );

                var filter_text = $("#filter_text").val();
                table.search(filter_text).draw();
            }

            $('.dataTables_filter input').on('input', function () {
                var value = $(this).val();
                $("#filter_text").val(value);
                filter();
            });

            $('.filter_month, .filter_year, .filter_paid_by').on('change', function () {
                filter();
            });

        });

    </script>

@endsection
