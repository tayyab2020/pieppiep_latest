@extends('layouts.handyman')

@section('content')

    <div class="right-side">
        <div class="container-fluid">

            @include('includes.form-success')

            <div style="margin: 0;" class="row">
                <div class="col-lg-12 col-ml-12 padding-bottom-30">
                    <div style="margin: 0;" class="row">

                        <div class="col-12 mt-5">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">{{Route::currentRouteName() == 'sent-emails' ? __('text.Sent Mails') : (Route::currentRouteName() == 'customer-messages' ? __('text.Customer Messages') : __('text.Review Reasons'))}}</h4>

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                        @if(Route::currentRouteName() == 'sent-emails')

                                            <div class="panel-group" id="accordion">
                                                <div class="panel panel-default">
                                                  <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                                      Quotations</a>
                                                    </h4>
                                                  </div>
                                                  <div id="collapse1" class="panel-collapse collapse in">
                                                    <div class="panel-body">
    
                                                        @foreach($quotations_sent_mails as $key)
    
                                                            <div class="r-row">
    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Quotation Number')}}</label>
                                                                    <p class="row-content">{{$key->quotation_invoice_number}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Mail To')}}</label>
                                                                    <p class="row-content">{{$key->mail_to}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.CC')}}</label>
                                                                    <p class="row-content">{!! implode("<br>",explode(",",$key->ccs)) !!}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Subject')}}</label>
                                                                    <p class="row-content">{{$key->subject}}</p>
                                                                </div>
                    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Body')}}</label>
                                                                    <div class="row-content">{!! $key->body !!}</div>
                                                                </div>
                                                                
                                                            </div>
                
                                                        @endforeach
    
                                                    </div>
                                                  </div>
                                                </div>
    
                                                <div class="panel panel-default">
                                                  <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                                      Orders</a>
                                                    </h4>
                                                  </div>
                                                  <div id="collapse2" class="panel-collapse collapse">
                                                    <div class="panel-body">
    
                                                        @foreach($orders_sent_mails as $key)
    
                                                            <div class="r-row">
    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Order Number')}}</label>
                                                                    <p class="row-content">{{$key->order_number}}</p>
                                                                </div>
    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Quotation Number')}}</label>
                                                                    <p class="row-content">{{$key->quotation_invoice_number}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Mail To')}}</label>
                                                                    <p class="row-content">{{$key->mail_to}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.CC')}}</label>
                                                                    <p class="row-content">{!! implode("<br>",explode(",",$key->ccs)) !!}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Subject')}}</label>
                                                                    <p class="row-content">{{$key->subject}}</p>
                                                                </div>
                    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Body')}}</label>
                                                                    <div class="row-content">{!! $key->body !!}</div>
                                                                </div>
                                                                
                                                            </div>
                
                                                        @endforeach
    
                                                    </div>
                                                  </div>
                                                </div>
    
                                                <div class="panel panel-default">
                                                  <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                                      Invoices</a>
                                                    </h4>
                                                  </div>
                                                  <div id="collapse3" class="panel-collapse collapse">
                                                    <div class="panel-body">
    
                                                        @foreach($invoices_sent_mails as $key)
    
                                                            <div class="r-row">
    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Invoice Number')}}</label>
                                                                    <p class="row-content">{{$key->invoice_number}}</p>
                                                                </div>
    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Quotation Number')}}</label>
                                                                    <p class="row-content">{{$key->quotation_invoice_number}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Mail To')}}</label>
                                                                    <p class="row-content">{{$key->mail_to}}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.CC')}}</label>
                                                                    <p class="row-content">{!! implode("<br>",explode(",",$key->ccs)) !!}</p>
                                                                </div>
                
                                                                <div class="form-group">
                                                                    <label>{{__('text.Subject')}}</label>
                                                                    <p class="row-content">{{$key->subject}}</p>
                                                                </div>
                    
                                                                <div class="form-group">
                                                                    <label>{{__('text.Body')}}</label>
                                                                    <div class="row-content">{!! $key->body !!}</div>
                                                                </div>
                                                                
                                                            </div>
                
                                                        @endforeach
    
                                                    </div>
                                                  </div>
                                                </div>
                                            </div>

                                        @elseif(Route::currentRouteName() == 'customer-messages')
                                        
                                            <div class="panel-group" id="accordion">

                                                @foreach($customer_messages as $x => $key)

                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                          <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$x}}">
                                                                {{__('text.Quotation Number').": ".$key->quotation_invoice_number}}
                                                            </a>
                                                          </h4>
                                                        </div>
                                                        <div id="collapse{{$x}}" @if($x == 0) class="panel-collapse collapse in" @else class="panel-collapse collapse" @endif>
                                                            <div class="panel-body">

                                                                <div class="r-row">
                                                                    <div class="form-group">
                                                                        {!! nl2br($key->text) !!}
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
    
                                                @endforeach

                                            </div>

                                        @else

                                            <div class="panel-group" id="accordion">

                                                @foreach($review_reasons as $x => $key)
    
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                          <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$x}}">
                                                                {{__('text.Quotation Number').": ".$key->quotation_invoice_number}}
                                                            </a>
                                                          </h4>
                                                        </div>
                                                        <div id="collapse{{$x}}" @if($x == 0) class="panel-collapse collapse in" @else class="panel-collapse collapse" @endif>
                                                            <div class="panel-body">
    
                                                                <div class="r-row">
                                                                    <div class="form-group">
                                                                        {!! nl2br($key->review_text) !!}
                                                                    </div>
                                                                </div>
    
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                @endforeach
    
                                            </div>

                                        @endif

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>

        .panel
        {
            border: 1px solid #cacaca;
            margin: 20px 0 !important;
        }

        .panel, .panel-default>.panel-heading
        {
            border-radius: 10px !important;
        }

        .panel-default>.panel-heading
        {
            background-color: #fafafa;
        }

        .panel-body
        {
            padding: 0;
        }

        .row-content
        {
            background: #f5f5f5;
            padding: 10px;
            color: #707070;
        }

        .panel-title
        {
            font-size: 18px;
        }

        .panel-title a
        {
            display: block;
            font-family: 'Montserrat';
            padding: 15px;
        }

        .panel-title a::before
        {
            font-family: 'Glyphicons Halflings';
            font-size: 0.6em;
            content: '\e260';
            position: absolute;
            right: 20px;
        }

        .panel-title a[aria-expanded="false"]::before
        {
            content: '\e259';
        }

        .panel-title a[aria-expanded="true"]::before
        {
            content: '\e260';
        }

        .panel-heading
        {
            padding: 0;
        }

        .r-row
        {
            /* box-shadow: 1px 1px 2px 2px #e2e2e2cc;
            border-radius: 5px;
            margin: 20px 0; */
            padding: 20px 0;
        }

        .r-row:not(:last-child)
        {
            border-bottom: 1px solid #bababa;
        }

        .form-group
        {
            margin: 10px 0;
            padding: 0 20px;
        }

        .note-toolbar
        {
            line-height: 1.5;
        }

        .right-side
        {
            background: white;
        }

        .padding-bottom-30
        {
            padding-bottom: 30px;
        }

        .mt-5, .my-5
        {
            margin-top: 3rem!important;
        }

        .card
        {
            background-color: #f5f5f5;
            border: none;
            border-radius: 4px;
            position: relative;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-clip: border-box;
            box-shadow: 0 0 6px -1px #00000047;
        }

        .card-body
        {
            padding: 3.6rem 2rem;
            -webkit-box-flex: 1;
            flex: 1 1 auto;
        }

        label, .form-text
        {
            color: black;
        }

        .header-title
        {
            color: black;
            font-family: 'Lato', sans-serif;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0;
            text-transform: capitalize;
            margin-bottom: 30px;
        }

        .note-editor
        {
            margin-bottom: 10px;
        }

    </style>
@endsection
