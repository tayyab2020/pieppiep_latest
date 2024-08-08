@extends('layouts.handyman')

@section('content')

    <script src="{{asset('assets/admin/js/main1.js?v=1.1')}}"></script>
    <script src="{{asset('assets/admin/js/bootstrap-tagsinput.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
    <script src="{{asset('assets/admin/js/bootstrap-datetimepicker.min.js')}}"></script>

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    @include('includes.form-success')

                    <ul class="nav nav-pills agenda-pills">
                        <li class="active">
                            <a href="#1a" data-toggle="tab">{{__("text.Agenda")}}</a>
                        </li>
                        <li><a href="#2a" data-toggle="tab">{{__("text.Checklist")}}</a></li>
                    </ul>
                    <div style="border: 1px solid #cecece;" class="tab-content clearfix">
                        <div style="margin: 30px 0;" class="tab-pane active" id="1a">
                            <form method="POST" action="{{route('store-plannings')}}" enctype="multipart/form-data">
                                {{csrf_field()}}
        
                                <input type="hidden" class="appointment_data" name="appointment_data">
                                @include("user.plannings_widget")
        
                            </form>
                        </div>
                        <div class="tab-pane" id="2a">
                            @include('user.checklist')
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    @include("user.plannings_manager")
    @include("user.select_checklist_quotation")

    <div id="cover">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
	</div>

    <style>

        .agenda-pills li
        {
            border: 1px solid #cecece;
            border-bottom: 0;
            margin-right: -3px;
        }

        .agenda-pills>li.active
        {
            background-color: #404040;
        }

        .agenda-pills>li>a
        {
            border-radius: 0;
            padding: 5px 30px;
        }

        .agenda-pills>li.active>a, .agenda-pills>li.active>a:focus, .agenda-pills>li.active>a:hover
        {
            background-color: transparent;
        }

        .agenda-pills a::before
        {
            display: none;
        }

        #cover {
			position: fixed;
			z-index: 100000;
			height: 100%;
			width: 100%;
			margin: 0;
			top: 0;
			left: 0;
			bottom: 0;
			right: 0;
			background-color: #ffffff99;
			display: none;
			justify-content: center;
			align-items: center;
		}

        .lds-ripple {
  			display: inline-block;
			position: relative;
			width: 80px;
			height: 80px;
		}
		
		.lds-ripple div {
			position: absolute;
			border: 4px solid #000;
			opacity: 1;
			border-radius: 50%;
			animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
		}

		.lds-ripple div:nth-child(2) {
			animation-delay: -0.5s;
		}

		@keyframes lds-ripple {
			
			0% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 0;
			}
			4.9% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 0;
			}
			5% {
				top: 36px;
				left: 36px;
				width: 0;
				height: 0;
				opacity: 1;
			}
			100% {
				top: 0px;
				left: 0px;
				width: 72px;
				height: 72px;
				opacity: 0;
			}
		
		}

        .bootstrap-tagsinput
        {
            width: 100%;
        }

        .fc-event:hover .fc-buttons
        {
            display: block;
        }

        .fc-event .fc-buttons
        {
            padding: 10px;
            text-align: center;
            display: none;
            position: absolute;
            background-color: #ffffff;
            border: 1px solid #d7d7d7;
            bottom: 100%;
            z-index: 99999;
            min-width: 80px;
        }

        .fc-event .fc-buttons:after,
        .fc-event .fc-buttons:before {
            top: 100%;
            left: 8px;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .fc-event .fc-buttons:before {
            border-color: rgba(119, 119, 119, 0);
            border-top-color: #d7d7d7;
            border-width: 6px;
            margin-left: -6px;
        }

        .fc-event .fc-buttons:after {
            border-color: rgba(255, 255, 255, 0);
            border-top-color: #ffffff;
            border-width: 5px;
            margin-left: -5px;
        }

        .fc table
        {
            margin: 0 !important;
        }

        .fc .fc-scrollgrid-section-liquid > td, .fc .fc-scrollgrid-section > td, .fc-theme-standard td, .fc-theme-standard th
        {
            padding: 0 !important;
        }

        .fc .fc-scrollgrid-section-liquid > td:first-child
        {
            border-right: 1px solid var(--fc-border-color, #ddd);
        }

        #calendar {
            width: 90%;
            margin: 0 auto;
        }

        .alert-danger
        {
            margin: 30px 10px !important;
        }

        .swal2-html-container {
            line-height: 2;
        }

        a.info {
            vertical-align: bottom;
            position: relative;
            /* Anything but static */
            width: 1.5em;
            height: 1.5em;
            text-indent: -9999em;
            display: inline-block;
            color: white;
            font-weight: bold;
            font-size: 1em;
            line-height: 1em;
            background-color: #628cb6;
            cursor: pointer;
            margin-top: 7px;
            -webkit-border-radius: .75em;
            -moz-border-radius: .75em;
            border-radius: .75em;
        }

        a.info:before {
            content: "i";
            position: absolute;
            top: .25em;
            left: 0;
            text-indent: 0;
            display: block;
            width: 1.5em;
            text-align: center;
            font-family: monospace;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            width: 100%;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #cacaca;
        }

        .select2-selection {
            height: 40px !important;
            padding-top: 5px !important;
            outline: none;
        }

        .select2-selection
        {
            height: 35px !important;
            padding-top: 0 !important;
            display: flex !important;
            align-items: center;
            justify-content: space-between;
        }

        .select2-selection__arrow {
            top: 7.5px !important;
        }

        .select2-selection__arrow
        {
            top: 0 !important;
            position: relative;
            height: 100% !important;
        }

        .appointment_start, .appointment_end
        {
            background-color: white !important;
        }

        .bootstrap-datetimepicker-widget .row:first-child
        {
            display: flex;
            align-items: center;
        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/locale/nl.js"></script>
    <script src="{{asset('assets/front/js/plannings.js?v=2.0')}}"></script>

    <link href="{{asset('assets/admin/css/main.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/bootstrap-tagsinput.css')}}" rel="stylesheet">
    <link href="{{asset('assets/front/css/plannings.css')}}" rel="stylesheet">
    @include('user.checklist_js')

@endsection
