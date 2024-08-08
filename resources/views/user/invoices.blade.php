@extends('layouts.handyman')

@section('content')

    <script src="{{asset('assets/front/js/spartan-multi-image-picker.js')}}"></script>

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
                                        
                                        <h2 style="display: inline-block;">{{__('text.Quotation Invoices')}}</h2>

                                        @if(Auth::guard('user')->user()->role_id == 2)
                                            
                                            <div>
                                                <button style="background-color: #92b105;border-color: #92b105;" type="button" href="#myModal6" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export Invoices XML')}}</button>
                                                <button style="background-color: #4dd5b2 !important;border-color: #ffffff00 !important;" type="button" href="#myModal5" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export to Reeleezee')}}</button>
                                                <button type="button" href="#myModal7" role="button" data-toggle="modal" class="btn btn-primary"><i class="fa fa-upload"></i> {{__('text.Export Invoices')}}</button>
                                                <label class="btn btn-success select-all" for="selectCheck">{{__('text.Select all')}}</label>
                                                <button type="button" class="btn btn-danger delete-invoices"><i class="fa fa-trash"></i> {{__('text.Delete')}}</button>
                                                <input style="display: none;" type="checkbox" id="selectCheck">
                                            </div>

                                        @endif

                                            <!-- @if(auth()->user()->can('create-direct-invoice'))

                                                <a style="float: right;margin-right: 10px;" href="{{route('create-direct-invoice')}}" class="btn add-newProduct-btn"><i class="fa fa-plus"></i> {{__('text.Create New Invoice')}}</a>

                                            @endif -->

                                    </div>
                                    <hr>
                                    <div>

                                        @include('includes.form-success')
                                        
                                        <div class="row">
                                            <div class="col-sm-12">

                                                @if(Auth::guard('user')->user()->role_id == 2)

                                                    <input value="{{Auth::guard('user')->user()->filter_text_invoice}}" type="hidden" name="filter_text" id="filter_text">

                                                    <div style="display: flex;margin: 10px 0 20px 0;" class="row filters_row">
                                                        
                                                        <div style="display: flex;justify-content: center;padding: 0 15px;">
                                                        
                                                            <div style="margin: 0;" class="form-group">
                                                                <select style="min-width: 125px;" class="form-control filter_year">
                                                                    <option value="">{{__("text.All Years")}}</option>
                                                                    @for($y = date('Y',strtotime($invoices->min('invoice_date'))); $y <= date("Y"); $y++)
                                                                        
                                                                        <option {{Auth::guard('user')->user()->filter_year_invoice == $y ? "selected" : null}} value="{{$y}}">{{$y}}</option>
    
                                                                    @endfor
                                                                </select>
                                                            </div>

                                                        </div>
    
                                                        <div style="display: flex;justify-content: center;">
                                                        
                                                            <div style="margin: 0;" class="form-group">
                                                                <select style="min-width: 125px;" class="form-control filter_month">
                                                                    <option value="">{{__("text.All Months")}}</option>
                                                                    @for ($m=1; $m <= 12; $m++)
                                                                        
                                                                        <option {{Auth::guard('user')->user()->filter_month_invoice == $m ? "selected" : null}} value="{{sprintf('%02d', $m)}}">{{date('F', mktime(0,0,0,$m, 1, date('Y')))}}</option>
    
                                                                    @endfor
                                                                </select>
                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                                <form id="invoices-delete-form" method="POST" action="{{route('invoices-delete-post')}}">
                                                    <input type="hidden" id="token" name="_token" value="{{@csrf_token()}}">
                                                
                                                    <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" cellspacing="0">
                                                        <thead>
    
                                                            <tr role="row">
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Quotation Number')}}</th>
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Invoice Number')}}</th>
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Donor's Name: activate to sort column ascending">{{__('text.Customer Name')}}</th>
    
                                                                @if(Auth::guard('user')->user()->role_id == 2)
    
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Grand Total')}}</th>
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Paid')}}</th>
                                                                    <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Pending')}}</th>
    
                                                                @endif
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Current Stage')}}</th>
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Date')}}</th>
    
                                                                <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Action')}}</th>
    
                                                            </tr>
    
                                                        </thead>
    
                                                        <tbody>
    
                                                        @foreach($invoices as $i => $key)
    
                                                            @if($key->getTable() == 'custom_quotations')
    
                                                                <tr role="row" class="odd">
    
                                                                    <td><a href="{{ url('/aanbieder/bekijk-eigen-offerte/'.$key->invoice_id) }}">OF# {{$key->quotation_invoice_number}}</a></td>
    
                                                                    <td><a href="{{ url('/aanbieder/bekijk-eigen-offerte/'.$key->invoice_id) }}">FA# {{$key->quotation_invoice_number}}</a></td>
    
                                                                    <td>{{$key->name}} {{$key->family_name}}</td>
    
                                                                    @if(Auth::guard('user')->user()->role_id == 2)
                                                                    
                                                                        <td>{{number_format((float)$key->grand_total, 2, ',', '.')}}</td>
                                                                        <td></td>
                                                                        <td></td>
    
                                                                    @endif
    
                                                                    <td>
    
                                                                        @if(Route::currentRouteName() == 'quotations' || Route::currentRouteName() == 'new-quotations' || Route::currentRouteName() == 'customer-quotations' || Route::currentRouteName() == 'customer-invoices')
    
                                                                            @if($key->status == 3)
    
                                                                                @if($key->received)
    
                                                                                    <span class="btn btn-success">{{__('text.Goods Received')}}</span>
    
                                                                                @elseif($key->delivered)
    
                                                                                    <span class="btn btn-success">{{__('text.Goods Delivered')}}</span>
                                                                                
                                                                                @else
    
                                                                                    <span class="btn btn-success">{{__('text.Invoice Generated')}}</span>
    
                                                                                @endif
    
                                                                            @elseif($key->status == 2)
    
                                                                                @if(Route::currentRouteName() == 'new-quotations')
    
                                                                                    @if($key->accepted)
    
                                                                                        @if($key->processing)
    
                                                                                            <span class="btn btn-success">{{__('text.Order Processing')}}</span>
    
                                                                                        @elseif($key->finished)
    
                                                                                            @if(Auth::guard('user')->user()->role_id == 2)
    
                                                                                                @if($key->retailer_delivered)
    
                                                                                                    <span class="btn btn-success">{{__('text.Delivered')}}</span>
    
                                                                                                @else
    
                                                                                                    <?php $data = $key->data->unique('supplier_id'); $filteredData = $data->reject(function ($value, $key) {
                                                                                                            return $value['approved'] !== 1;
                                                                                                    }); ?>
    
                                                                                                    @if($filteredData->count() === $data->count())
    
                                                                                                        @if($data->contains('delivered',1))
    
                                                                                                            <?php $filteredData2 = $data->reject(function ($value, $key) {
                                                                                                                return $value['delivered'] !== 1;
                                                                                                            }); ?>
    
                                                                                                            @if($filteredData2->count() === $data->count())
    
                                                                                                                <span class="btn btn-success">{{__('text.Delivered by supplier(s)')}}</span>
    
                                                                                                            @elseif($filteredData2->count() == 0)
    
                                                                                                                <span class="btn btn-success">{{__('text.Confirmed by supplier(s)')}}</span>
    
                                                                                                            @else
    
                                                                                                                <span class="btn btn-success">{{$filteredData2->count()}}/{{$data->count()}} {{__('text.Delivered Order')}}</span>
    
                                                                                                            @endif
    
                                                                                                        @else
    
                                                                                                            <span class="btn btn-success">{{__('text.Confirmed by supplier(s)')}}</span>
    
                                                                                                        @endif
    
                                                                                                    @elseif($filteredData->count() == 0)
    
                                                                                                        <span class="btn btn-warning">{{__('text.Confirmation Pending')}}</span>
    
                                                                                                    @else
    
                                                                                                        <span class="btn btn-success">{{$filteredData->count()}}/{{$data->count()}} {{__('text.Confirmed')}}</span>
    
                                                                                                    @endif
    
                                                                                                @endif
    
                                                                                            @else
    
                                                                                                @if($key->data_processing)
    
                                                                                                    <span class="btn btn-warning">{{__('text.Processing')}}</span>
    
                                                                                                @elseif($key->data_delivered)
    
                                                                                                    <span class="btn btn-success">{{__('text.Order Delivered')}}</span>
    
                                                                                                @elseif($key->data_approved)
    
                                                                                                    <span class="btn btn-success">{{__('text.Order Confirmed')}}</span>
    
                                                                                                @else
    
                                                                                                    <span class="btn btn-warning">{{__('text.Confirmation Pending')}}</span>
    
                                                                                                @endif
    
                                                                                            @endif
    
                                                                                        @else
    
                                                                                            <span class="btn btn-primary1">{{__('text.Quotation Accepted')}}</span>
    
                                                                                        @endif
    
                                                                                    @else
    
                                                                                        <span class="btn btn-success">{{__('text.Closed')}}</span>
    
                                                                                    @endif
    
                                                                                @else
    
                                                                                    @if($key->accepted)
    
                                                                                        <span class="btn btn-primary1">{{__('text.Quotation Accepted')}}</span>
    
                                                                                    @else
    
                                                                                        <span class="btn btn-success">{{__('text.Closed')}}</span>
    
                                                                                    @endif
    
                                                                                @endif
    
                                                                            @else
    
                                                                                @if($key->ask_customization)
    
                                                                                    <span class="btn btn-info">{{__('text.Asking for Review')}}</span>
    
                                                                                @elseif($key->approved)
    
                                                                                    @if(Route::currentRouteName() == 'new-quotations')
    
                                                                                        <span class="btn btn-success">{{__('text.Quotation Sent')}}</span>
    
                                                                                    @else
    
                                                                                        <span class="btn btn-success">{{__('text.Quotation Approved')}}</span>
    
                                                                                    @endif
    
                                                                                @else
    
                                                                                    <span class="btn btn-warning">{{__('text.Pending')}}</span>
    
                                                                                @endif
    
                                                                            @endif
    
                                                                        @else
    
                                                                            @if($key->received)
    
                                                                                <span class="btn btn-success">{{__('text.Goods Received')}}</span>
    
                                                                            @elseif($key->delivered)
    
                                                                                <span class="btn btn-success">{{__('text.Goods Delivered')}}</span>
    
                                                                            @else
    
                                                                                <span class="btn btn-success">{{__('text.Invoice Generated')}}</span>
    
                                                                            @endif
    
                                                                        @endif
    
                                                                    </td>
    
    
                                                                        <?php $date = strtotime($key->invoice_date);
    
                                                                        $date1 = date('d-m-Y',$date); ?>
    
                                                                    <td data-sort="{{strtotime($key->invoice_date)}}">{{$date1}}</td>
    
                                                                    <td>
    
                                                                        <div class="dropdown dropdown1">
                                                                            
                                                                            <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                                <span class="caret"></span>
                                                                            </button>
                                                                            
                                                                            <ul class="dropdown-menu">
    
                                                                                @if(auth()->user()->can('view-custom-quotation'))
    
                                                                                    <li><a href="{{ url('/aanbieder/bekijk-eigen-offerte/'.$key->invoice_id) }}">{{__('text.View')}}</a></li>
    
                                                                                @endif
    
    
                                                                                @if(auth()->user()->can('download-custom-quotation'))
    
                                                                                    <li><a href="{{ url('/aanbieder/download-custom-quotation/'.$key->invoice_id) }}">{{__('text.Download PDF')}}</a></li>
    
                                                                                @endif
    
    
                                                                                @if(!$key->approved)
    
                                                                                    @if(auth()->user()->can('send-custom-quotation'))
    
                                                                                        <li><a href="{{ url('/aanbieder/versturen-eigen-offerte/'.$key->invoice_id) }}">{{__('text.Send Quotation')}}</a></li>
    
                                                                                    @endif
    
                                                                                @endif
    
    
                                                                                @if($key->status == 2 && $key->accepted)
    
                                                                                    @if(auth()->user()->can('create-custom-invoice'))
    
                                                                                        <li><a href="{{ url('/aanbieder/opstellen-eigen-factuur/'.$key->invoice_id) }}">{{__('text.Create Invoice')}}</a></li>
    
                                                                                    @endif
    
                                                                                @endif
    
    
                                                                                @if($key->status != 2 && $key->status != 3)
    
                                                                                    @if($key->ask_customization)
    
                                                                                        <li><a onclick="ask(this)" data-text="{{$key->review_text}}" href="javascript:void(0)">{{__('text.Review Reason')}}</a></li>
    
                                                                                    @endif
    
    
                                                                                    @if(auth()->user()->can('edit-custom-quotation'))
    
                                                                                        <li><a href="{{ url('/aanbieder/bewerk-eigen-offerte/'.$key->invoice_id) }}">{{__('text.Edit Quotation')}}</a></li>
    
                                                                                    @endif
    
                                                                                @endif
    
    
                                                                                @if($key->status == 3 && $key->delivered == 0)
    
                                                                                    @if(auth()->user()->can('custom-mark-delivered'))
    
                                                                                        <li><a href="{{ url('/aanbieder/custom-mark-delivered/'.$key->invoice_id) }}">{{__('text.Mark as delivered')}}</a></li>
    
                                                                                    @endif
    
                                                                                @endif
    
                                                                            </ul>
                                                                            
                                                                        </div>
                                                                        
                                                                    </td>
                                                                        
                                                                </tr>
                                                            
                                                            @else
    
                                                            <tr role="row" class="odd">
    
                                                                <td>
                                                                    @if(Auth::guard('user')->user()->role_id == 2)
    
                                                                        <div style="display: flex;align-items: center;" class="custom-control custom-checkbox mb-3">
                                                                            <input type="checkbox" style="margin: 0;" class="custom-control-input" id="customCheck{{$i}}">
                                                                            <input type="hidden" name="invoice_ids[]" class="invoice_ids" value="{{$key->invoice_id}}">
                                                                            <input type="hidden" class="delete_invoices_options" name="delete_invoices_options[]">
                                                                            <label style="margin: 0 0 0 5px;font-weight: 500;" class="custom-control-label" for="customCheck{{$i}}">OF# {{$key->quotation_invoice_number}}</label>
                                                                        </div>
    
                                                                    @else
                                                                        OF# {{$key->quotation_invoice_number}}
                                                                    @endif
                                                                </td>
    
                                                                <td>FA# {{$key->invoice_number}}</td>
    
                                                                <td>{{$key->quote_request_id ? $key->quote_name . ' ' . $key->quote_familyname : $key->name . ' ' . $key->family_name}}</td>
    
                                                                @if(Auth::guard('user')->user()->role_id == 2)
    
                                                                    <?php
                                                                        $payment_calculations = $key->payment_calculations;
                                                                        $paid = 0;
                                                                        $pending = 0;
                                                                        foreach($payment_calculations as $p)
                                                                        {
                                                                            if($p->paid_by != "Pending")
                                                                            {
                                                                                $paid = $paid + $p->amount;
                                                                            }
                                                                            if($p->paid_by == "Pending")
                                                                            {
                                                                                $pending = $pending + $p->amount;
                                                                            }
                                                                        }

                                                                        if($paid == 0 && $pending == 0)
                                                                        {
                                                                            $pending = $key->grand_total;
                                                                        }
                                                                    ?>
                                                                        
                                                                    <td>{{number_format((float)$key->grand_total, 2, ',', '.')}}</td>
                                                                    <td>{{number_format((float)$paid, 2, ',', '.')}}</td>
                                                                    <td>{{number_format((float)$pending, 2, ',', '.')}}</td>
    
                                                                @endif
    
                                                                <td><span class="btn btn-success">{{__('text.Invoice Generated')}}</span></td>
    
                                                                <?php $date = strtotime($key->document_date);
    
                                                                $date1 = date('d-m-Y',$date); ?>
    
                                                                <td data-sort="{{$date}}">{{$date1}}</td>
    
                                                                <td>
                                                                    
                                                                    <div class="dropdown dropdown1">
                                                                        
                                                                        <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                            <span class="caret"></span>
                                                                        </button>
                                                                            
                                                                            <ul class="dropdown-menu">
    
                                                                                @if(!$key->negative_invoice)
    
                                                                                    <!-- <li><a href="{{ url('/aanbieder/copy-new-invoice/'.$key->quotation_id) }}">{{__('text.Copy Invoice')}}</a></li> -->
                                                                                    <li><a style="cursor: pointer;" class="delete-btn" data-href="{{ url('/aanbieder/delete-new-invoice/'.$key->quotation_id) }}">{{__('text.Delete Invoice')}}</a></li>
                                                                                    <li><a style="cursor: pointer;" href="{{ url('/aanbieder/view-new-invoice/'.$key->quotation_id) }}">{{__('text.View Invoice')}}</a></li>
    
                                                                                    @if(!$key->has_negative_invoice)
                                                                                        <li><a style="cursor: pointer;" class="create-neginv-btn" data-href="{{ url('/aanbieder/create-new-negative-invoice/'.$key->quotation_id) }}">{{__('text.Create Negative Invoice')}}</a></li>
                                                                                    @endif
    
                                                                                    <li><a style="cursor: pointer;" href="{{ url('/aanbieder/download-invoice-pdf/'.$key->invoice_id) }}">{{__('text.Download Invoice PDF')}}</a></li>
    
                                                                                    <li><a style="cursor: pointer;" class="send-new-invoice" data-type="{{$key->quote_request_id ? 0 : 1}}" data-negative="0" data-id="{{$key->quotation_id}}" href="javascript:void(0)">{{__('text.Send Invoice')}}</a></li>
    
                                                                                @else
    
                                                                                    <!-- <li><a href="{{ url('/aanbieder/copy-new-negative-invoice/'.$key->quotation_id) }}">{{__('text.Copy Negative Invoice')}}</a></li> -->
                                                                                    <li><a style="cursor: pointer;" class="delete-btn" data-href="{{ url('/aanbieder/delete-new-negative-invoice/'.$key->quotation_id) }}">{{__('text.Delete Negative Invoice')}}</a></li>
                                                                                    <li><a style="cursor: pointer;" href="{{ url('/aanbieder/view-negative-invoice/'.$key->quotation_id) }}">{{__('text.View Negative Invoice')}}</a></li>
                                                                                    <li><a style="cursor: pointer;" href="{{ url('/aanbieder/download-negative-invoice-pdf/'.$key->quotation_id) }}">{{__('text.Download Negative Invoice PDF')}}</a></li>
    
                                                                                    <li><a style="cursor: pointer;" class="send-negative-invoice" data-type="{{$key->quote_request_id ? 0 : 1}}" data-negative="1" data-id="{{$key->quotation_id}}" href="javascript:void(0)">{{__('text.Send Negative Invoice')}}</a></li>
    
                                                                                @endif
    
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                </tr>
    
                                                            @endif
    
                                                        @endforeach
    
                                                        </tbody>
    
                                                        @if(Auth::guard('user')->user()->role_id == 2)
    
                                                            <tfoot>
                                                                <tr>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th style="text-align: left;"></th>
                                                                    <th style="text-align: left;"></th>
                                                                    <th style="text-align: left;"></th>
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
                <!-- Ending of Dashboard data-table area -->
            </div>
        </div>
    </div>

    @if(Auth::guard('user')->user()->role_id == 2)

        <div id="myModal5" class="modal fade" role="dialog">
		    <div class="modal-dialog">

                <form id="reeleezee-export-form" method="POST" action="{{route('export-invoices-reeleezee')}}">
                    <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                    <!-- Modal content-->
			        <div class="modal-content">
				        <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">{{__('text.Export by date')}}</h4>
				        </div>
				        <div style="padding: 30px 20px;display: flex;justify-content: center;" class="modal-body">

                            <div class="wrapper-options2 panel-group" id="accordion">

                                <div class="date-select-box panel panel-default">

                                    <input type="radio" value="1" class="select-form" name="export_by" id="option1-1">

                                    <label for="option1-1" class="option1 option1-1">
                                        <div class="dot"></div>
                                        <span>{{__('text.Document date')}}</span>
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1"></a>
                                    </label>

                                    <div id="collapse1" class="panel-collapse collapse">
                                        <div class="export-dates">
                                            <div class="e-date">
                                                <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="created_start_date" id="created_start_date">
                                            </div>
                                            <div class="e-date">
                                                <input type="text" placeholder="{{__('text.End Date')}}" readonly class="export_dates" name="created_end_date" id="created_end_date">
                                            </div>
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
                                        <div class="export-dates">
                                            <div class="e-date">
                                                <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="updated_start_date" id="updated_start_date">
                                            </div>
                                            <div class="e-date">
                                                <input type="text" placeholder="{{__('text.End Date')}}" readonly class="export_dates" name="updated_end_date" id="updated_end_date">
                                            </div>
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
                                        <div class="export-dates">
                                            <div class="e-date">
                                                <input type="text" placeholder="{{__('text.Start Date')}}" readonly class="export_dates" name="last_start_date" id="last_start_date">
                                            </div>
                                        </div>
                                    </div>

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

        <div id="myModal6" class="modal fade" role="dialog">
		    <div style="width: 60%;" class="modal-dialog">

                <form id="customers-export-form" method="GET" action="{{route('export-invoices-xml')}}">
                    <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                    <!-- Modal content-->
			        <div class="modal-content">
				        <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">{{__('text.Export by date')}}</h4>
				        </div>
				        <div style="padding: 30px 20px;display: flex;justify-content: center;flex-direction: column;" class="modal-body">

                            <div class="wrapper-options1">

                                <input checked type="radio" value="1" class="select-form" name="export_xml_by" id="option1-1">

                                <label for="option1-1" class="option1 option1-1">
                                    <div class="dot"></div>
                                    <span>{{__('text.Date Range')}}</span>
                                </label>

                                <input type="radio" value="2" class="select-form" name="export_xml_by" id="option1-2">

                                <label for="option1-2" class="option1 option1-2">
                                    <div class="dot"></div>
                                    <span>{{__('text.Recent Date')}}</span>
                                </label>
                        
                            </div>

                            <div class="dates-container">
                                <label>{{__("text.Select Range")}}</label>
                                <div class="dates">
                                    <div class="dc start_date_container">
                                        <input autocomplete="off" placeholder="{{__('text.Start Date')}}" type="text" class="form-control export_from_date" name="export_from_date">
                                    </div>
                                    <div class="dc end_date_container">
                                        <input autocomplete="off" placeholder="{{__('text.End Date')}}" type="text" class="form-control export_to_date" name="export_to_date">
                                    </div>
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

        <div id="myModal7" class="modal fade" role="dialog">
		    <div style="width: 60%;" class="modal-dialog">

                <form id="customers-export-form" method="GET" action="{{route('export-invoices')}}">
                    <input type="hidden" name="_token" value="{{@csrf_token()}}">
                
                    <!-- Modal content-->
			        <div class="modal-content">
				        <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal">&times;</button>
					        <h4 class="modal-title">{{__('text.Export by date')}}</h4>
				        </div>
				        <div style="padding: 30px 20px;display: flex;justify-content: center;flex-direction: column;" class="modal-body">

                            <div class="wrapper-options1">

                                <input checked type="radio" value="1" class="select-form" name="export_by" id="option2-1">

                                <label for="option2-1" class="option1 option1-1">
                                    <div class="dot"></div>
                                    <span>{{__('text.Date Range')}}</span>
                                </label>

                                <input type="radio" value="2" class="select-form" name="export_by" id="option2-2">

                                <label for="option2-2" class="option1 option1-2">
                                    <div class="dot"></div>
                                    <span>{{__('text.Recent Date')}}</span>
                                </label>
                        
                            </div>

                            <div class="dates-container">
                                <label>{{__("text.Select Range")}}</label>
                                <div class="dates">
                                    <div class="dc start_date_container1">
                                        <input autocomplete="off" placeholder="{{__('text.Start Date')}}" type="text" class="form-control export_from_date1" name="export_from_date">
                                    </div>
                                    <div class="dc end_date_container1">
                                        <input autocomplete="off" placeholder="{{__('text.End Date')}}" type="text" class="form-control export_to_date1" name="export_to_date">
                                    </div>
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

        <script src="https://unpkg.com/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script>
        <script>

            $(".select-form").on("click",function(){
                if($(this).val() == 1)
                {
                    $(this).parents(".modal-body").find(".dates-container").show();
                }
                else
                {
                    $(this).parents(".modal-body").find(".dates-container").hide();
                }
            });

            $('.export_from_date').datepicker({

				format: 'yyyy-mm-dd',
                container: $(".start_date_container"),

			});

            $('.export_to_date').datepicker({

				format: 'yyyy-mm-dd',
                container: $(".end_date_container"),

			});

            $('.export_from_date1').datepicker({

				format: 'yyyy-mm-dd',
                container: $(".start_date_container1"),

			});

            $('.export_to_date1').datepicker({

				format: 'yyyy-mm-dd',
                container: $(".end_date_container1"),

			});

            $('.export_dates').each(function(i, obj) {

                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    language: 'du',
                    ignoreReadonly: true,
                    container: $(this).parent(),
                });
                
            });

            $('.date-select-box #option1-1').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

            $('.date-select-box #option1-2').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

            $('.date-select-box #option1-3').on('click', function(){
                $(this).parent().find('a').trigger('click');
            });

        </script>

        <link href="{{ asset('assets/front/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <style type="text/css">

            @import url('https://fonts.googleapis.com/css?family=Lato:400,500,600,700&display=swap');

            .export-dates
            {
                display: flex;
                margin: 10px;
            }

            .e-date
            {
                width: 100%;
                position: relative;
                margin: 0 5px;
            }

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
            }

            .wrapper-options2{
                display: inline-flex;
                flex-direction: column;
                width: 100%;
                align-items: center;
                justify-content: space-evenly;
                border-radius: 5px;
                padding: 0;
            }
            .wrapper-options2 .option1{
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
            .wrapper-options2 .option1 .dot{
                height: 20px;
                width: 20px;
                background: #d9d9d9;
                border-radius: 50%;
                position: relative;
            }
            .wrapper-options2 .option1 .dot::before{
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
            .wrapper-options2 input[type="radio"]{
                display: none;
            }
            .wrapper-options2 input[type="radio"]:checked + label{
                border-color: #0069d9;
                background: #0069d9;
            }
            .wrapper-options2 input[type="radio"]:checked + label .dot{
                background: #fff;
            }
            .wrapper-options2 input[type="radio"]:checked + label .dot::before{
                opacity: 1;
                transform: scale(1);
            }
            .wrapper-options2 .option1 span{
                font-size: 20px;
                color: #808080;
                margin-left: 10px;
                margin-top: -1px;
                text-transform: capitalize;
            }
            .wrapper-options2 input[type="radio"]:checked + label span{
                color: #fff;
            }
            .wrapper-options2 .option1 a::before
            {
                display: none;
            }

            .dc
            {
                width: 48%;
                position: relative;
            }

            .datepicker table
            {
                width: 100%;
            }

            .datepicker-dropdown
            {
                position: absolute;
                width: 100% !important;
                overflow: hidden !important;
            }

            .dates-container
            {
                margin-top: 20px;
                padding: 20px 10px;
                border: 1px solid #d1d1d1;
                border-radius: 10px;
            }

            .dates
            {
                display: flex;
                justify-content: space-between;
            }
                            
            .wrapper-options1{
                display: inline-flex;
                width: 100%;
                align-items: center;
                justify-content: space-evenly;
                border-radius: 5px;
                padding: 0;
            }
            .wrapper-options1 .option1{
                background: #fff;
                height: 100%;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                margin: 0 10px;
                border-radius: 5px;
                cursor: pointer;
                padding: 0 10px;
                border: 2px solid lightgrey;
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

        </style>

    @endif

    <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            
            <div class="modal-content">

                <div class="modal-header">
                    <button style="font-size: 32px;background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 style="margin: 10px 0;" id="myModalLabel">{{__('text.Review Reason')}}</h3>
                </div>

                <div class="modal-body" id="myWizard">
                    <textarea rows="5" style="resize: vertical;" type="text" name="review_text" id="review_text" class="form-control" readonly autocomplete="off"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" aria-label="Close" style="border: 0;outline: none;background-color: #e5e5e5 !important;color: black !important;" class="btn back">{{__('text.Close')}}</button>
                </div>

            </div>

        </div>
    </div>

    @include('user.send_invoice_modal')

    <style type="text/css">

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
            border-right: 1px solid #acacac;
        }

        .dataTables_scrollBody .table.products > thead > tr > th
        {
            border: none !important;
        }

        .note-editor
        {
            width: 100%;
        }

        .note-toolbar
        {
            line-height: 1;
        }

        .btn-primary1
        {
            background-color: darkcyan;
            border-color: darkcyan;
            color: white !important;
        }

        @media (min-width: 768px)
        {
            .dropdown1.open>.dropdown-menu
            {
                display: grid;
            }

            .dropdown1 .dropdown-menu
            {
                /* width: 215px; */
                overflow: auto;
            }

        }

        .dropdown1 .dropdown-menu
        {
            position: relative;
            left: -65px;
            float: right;
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
            margin: 10px; /* demo only */
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
            -webkit-appearance: button; /* hide default arrow in chrome OSX */
            outline: none;
        }

        .custom-dropdown::before,
        .custom-dropdown::after {
            content: "";
            position: absolute;
            pointer-events: none;
        }

        .custom-dropdown::after { /*  Custom dropdown arrow */
            content: "\25BC";
            height: 1em;
            font-size: .625em;
            line-height: 1;
            right: 1.2em;
            top: 50%;
            margin-top: -.5em;
        }

        .custom-dropdown::before { /*  Custom dropdown arrow cover */
            width: 2em;
            right: 0;
            top: 0;
            bottom: 0;
            border-radius: 0 3px 3px 0;
        }

        .custom-dropdown select[disabled] {
            color: rgba(0,0,0,.3);
        }

        .custom-dropdown select[disabled]::after {
            color: rgba(0,0,0,.1);
        }

        .custom-dropdown::before {
            background-color: rgba(0,0,0,.15);
        }

        .custom-dropdown::after {
            color: rgba(0,0,0,.4);
        }

        .text-left{

            font-size: 18px !important;
            text-align: left !important;

        }

        .swal2-popup{

            width: 25% !important;
            height: 330px !important;
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

        .dataTables_wrapper .dataTables_length select.form-control
        {
            line-height: 1;
        }

    </style>

    @include("user.modals_css")

@endsection

@section('scripts')

    @include("user.modals_js")

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.datatables.net/rowgroup/1.3.0/js/dataTables.rowGroup.min.js"></script>

    <script type="text/javascript">

        function delete_confirmation(href,type)
        {
            Swal.fire({
				title: '{{__("text.Are you sure?")}}',
				text: '{{__("text.Are you sure you want to delete this invoice")}}',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '{{__("text.Yes")}}!',
				cancelButtonText: '{{__("text.Cancel")}}',
			}).then((result) => {
				if (result.value) {
                    if(type)
                    {
                        $('#invoices-delete-form').submit();
                    }
                    else
                    {
                        window.location.href = href;
                    }
				}
			});
        }

        function create_confirmation(href)
        {
            Swal.fire({
				title: '{{__("text.Are you sure?")}}',
				text: '{{__("text.Are you sure you want to create a negative invoice")}}',
				icon: 'question',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: '{{__("text.Yes")}}!',
				cancelButtonText: '{{__("text.Cancel")}}',
			}).then((result) => {
				if (result.value) {
                    window.location.href = href;
				}
			});
        }

        $(".delete-btn").click(function(){

            var href = $(this).data("href");
            delete_confirmation(href,0);
        
        });

        $(".create-neginv-btn").click(function(){

            var href = $(this).data("href");
            create_confirmation(href);
        
        });
    
        $(".delete-invoices").click(function(){

            delete_confirmation(null,1);
        
        });
        
        $(".select-all").click(function(){
            
            var check = $('.custom-control-input:checked').length > 0 ? true : false;
            $('.custom-control-input').prop('checked', !check);
            $('.delete_invoices_options').val(check ? 0 : 1);

        });
        
        $(".custom-control-input").change(function(){
            
            var check = $(this).is(":checked");
            $(this).parent().find('.delete_invoices_options').val(check ? 1 : 0);
        
        });

        function ask(e)
        {
            var text = $(e).data('text');

            $('#review_text').val(text);

            $('#myModal1').modal('toggle');
        }

    </script>

    @if(Auth::guard('user')->user()->role_id == 2)

        <script>

            $(document).ready( function () {

                var screen_width = $(window).width();
                var userColumnDefs = [];
                var table_width = "";

                var tableId = 'example';
                var dateColumn = 7;
                var amountColumn = 3;
                var paidColumn = 4;
                var pendingColumn = 5;
                
                var table = $('#' + tableId).DataTable({
                    order: [[dateColumn, 'desc']],
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
                    rowGroup: {
                        endRender: function ( rows, group ) {

                            var sum = rows
                            .data()
                            .pluck(amountColumn)
                            .reduce( function (a, b) {
                                b = b.replace(/[\€.]/g, '');
                                b = b.replace(/\,/g, '.') * 1;
                                return a + b;
                            }, 0);

                            sum = sum.toFixed(2);
                            sum = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(sum);

                            var sum1 = rows
                            .data()
                            .pluck(paidColumn)
                            .reduce( function (a, b) {
                                b = b.replace(/[\€.]/g, '');
                                b = b.replace(/\,/g, '.') * 1;
                                return a + b;
                            }, 0);

                            sum1 = sum1.toFixed(2);
                            sum1 = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(sum1);

                            var sum2 = rows
                            .data()
                            .pluck(pendingColumn)
                            .reduce( function (a, b) {
                                b = b.replace(/[\€.]/g, '');
                                b = b.replace(/\,/g, '.') * 1;
                                return a + b;
                            }, 0);

                            sum2 = sum2.toFixed(2);
                            sum2 = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(sum2);

                            return $('<tr class="group-total" />').append( '<td colspan="3"></td><td style="color: #0097bd;">€ '+ sum +'</td><td style="color: #0097bd;">€ '+ sum1 +'</td><td style="color: #0097bd;" colspan="4">€ '+ sum2 +'</td>' );

                        },
                        dataSrc: function(row) {
                            return row[dateColumn].display;
                        }
                    },
                    autoWidth: false,
                    responsive: false,
                    scrollX: true,
                    pageLength: 100,
                    aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'ALL']],
                    stateSave: true,
                    // stateSaveCallback: function (settings, data) {
                    //     data.type = 4;
                    //     // Send an Ajax request to the server with the state object
                    //     $.ajax({
                    //         type: "GET",
                    //         url: "<?php echo route('user-update-filter'); ?>",
                    //         "data": data,
                    //         "dataType": "json",
                    //         "success": function () {}
                    //     });
                    // },
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

                        var pageTotal1 = 0;
                        api
                        .column(paidColumn, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            pageTotal1 = pageTotal1 + intVal(b);
                        }, 0);
 
                        pageTotal1 = pageTotal1.toFixed(2);
                        pageTotal1 = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(pageTotal1);

                        var pageTotal2 = 0;
                        api
                        .column(pendingColumn, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            pageTotal2 = pageTotal2 + intVal(b);
                        }, 0);
 
                        pageTotal2 = pageTotal2.toFixed(2);
                        pageTotal2 = new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(pageTotal2);
                    
                        // Update footer
                        $(api.column(amountColumn).footer()).html('€ ' + pageTotal);
                        $(api.column(paidColumn).footer()).html('€ ' + pageTotal1);
                        $(api.column(pendingColumn).footer()).html('€ ' + pageTotal2);
                    },
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
                        data: "screen_width=" + screen_width + '&table_id=invoices',
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
					// var userColumnDefs = JSON.parse(localStorage.getItem("invoices_table")) || [];

					if (userColumnDefs.length === 0) return;

                    // var table_width = localStorage.getItem('invoices_table_width');

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
                        data: "table_width=" + table_width + "&column_defs=" + JSON.stringify(userColumnDefs) + "&screen_width=" + screen_width + '&table_id=invoices' + "&_token=" + token,
                        url: "<?php echo route('update-table-widths'); ?>",

                        success: function (data) {

                        },
                        error: function (data) {

                        }

                    });

					// Save (or update) the settings in localStorage
					localStorage.setItem('invoices_table_width', table_width);
                    localStorage.setItem("invoices_table", JSON.stringify(userColumnDefs));
				}

                var filters_row = $(".filters_row").html();
                $(".dataTables_wrapper").find('.row').first().after('<div style="display: flex;margin: 20px 0 0 0;" class="row filters_row1">'+filters_row+'</div>');
                $(".filters_row").remove();

                function filter()
                {
                    // Custom filtering function which will search data in column six between two values
                    $.fn.dataTable.ext.search.push(
                
                        function( settings, data, dataIndex ) {

                            var filter_month = $(".filter_month").val();
                            var filter_year = $(".filter_year").val();
                            var dateAr = /(\d+)\-(\d+)\-(\d+)/.exec(data[dateColumn]);
                            var format_start = dateAr[3] + '-' + dateAr[2] + '-' + dateAr[1];
                            var date = new Date(format_start);
                            var day = date.getDate();
                            var month = date.getMonth() + 1;
                            var year = date.getFullYear().toString();
                    
                            month = month > 9 ? "" + month : "0" + month;

                            if ((filter_year == "" && filter_month == "") || ( (filter_year && filter_month) && (filter_year == year && filter_month == month) ) || ( ((filter_year && filter_month == "") && (filter_year == year)) || ((filter_month && filter_year == "") && (filter_month == month)) ))
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

                    var filter_month = $(".filter_month").val();
                    var filter_year = $(".filter_year").val();
                
                    $.ajax({

                        type: "GET",
                        data: "filter_text=" + filter_text + "&filter_month=" + filter_month + '&filter_year=' + filter_year + '&type=2',
                        url: "<?php echo route('user-update-filter'); ?>",

                        success: function (data) {

                        },
                        error: function (data) {

                        }

                    });
                }

                table.on('draw', function () {
                    getTableWidth();
                });

                $('.dataTables_filter input').on('input', function () {
                    var value = $(this).val();
                    $("#filter_text").val(value);
                    filter();
                });

                $('.filter_month, .filter_year').on('change', function () {
                    filter();
                });

                $(window).on("load", function () {
                    filter();
                });

            });

        </script>

    @else

        <script>

            $('#example').DataTable({
                order: [[5, 'desc']],
                pageLength: 100,
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
                }
            });

        </script>

    @endif

@endsection
