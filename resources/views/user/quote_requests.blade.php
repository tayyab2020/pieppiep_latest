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
                                    <div class="add-product-header products">
                                        <h2>{{__('text.Quotation Requests')}}</h2>

                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" cellspacing="0">
                                                    <thead>

                                                    <tr role="row">

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Request No.')}}</th>

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Category')}}</th>

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Brand')}}</th>

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Model')}}</th>

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Service')}}</th>

                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Current Stage')}}</th>

                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Created At')}}</th>

                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Action')}}</th>

                                                    </thead>

                                                    <tbody>
                                                    <?php $i=0;  ?>

                                                    @foreach($requests as $i => $key)

                                                        <tr role="row" class="odd">

                                                            <?php $requested_quote_number = $key->quote_number; ?>

                                                                <td><a href="{{ url('/aanbieder/aanbieder-offertes/'.$key->id) }}">{{$requested_quote_number}}</a></td>

                                                                <td>{{$key->cat_name}}</td>

                                                                <td>{{$key->brand_name}}</td>

                                                                <td>{{$key->model_name}}</td>

                                                                <td>{{$key->title}}</td>

                                                            <td>

                                                                @if($key->status == 3)

                                                                    @if($invoices[$i] && $invoices[$i]->received)

                                                                        <span class="btn btn-success">{{__('text.Goods Received')}}</span>

                                                                    @elseif($invoices[$i] && $invoices[$i]->delivered)

                                                                        <span class="btn btn-success">{{__('text.Goods Delivered')}}</span>

                                                                    @elseif($invoices[$i] && $invoices[$i]->invoice)

                                                                        <span class="btn btn-success">{{__('text.Invoice Generated')}}</span>

                                                                    @else

                                                                        <span class="btn btn-success">{{__('text.Closed')}}</span>

                                                                    @endif

                                                                @elseif($key->status == 2)

                                                                    @if($invoices[$i] && $invoices[$i]->accepted)

                                                                        <span class="btn btn-success">{{__('text.Quotation Accepted')}}</span>

                                                                    @else

                                                                        <span class="btn btn-success">{{__('text.Closed')}}</span>

                                                                    @endif

                                                                @else

                                                                    @if($invoices[$i])

                                                                        @if($invoices[$i]->ask_customization)

                                                                            <span class="btn btn-info">{{__('text.Asking for Review')}}</span>

                                                                        @elseif($invoices[$i]->accepted)

                                                                            <span class="btn btn-primary1">{{__('text.Quotation Accepted')}}</span>

                                                                        @elseif($invoices[$i]->approved)

                                                                            <span class="btn btn-success">{{__('text.Quotation Sent')}}</span>

                                                                        @elseif($invoices[$i]->admin_quotation_sent)

                                                                            <span class="btn btn-info">{{__('text.Waiting For Approval')}}</span>

                                                                        @else

                                                                            <span class="btn btn-info">{{__('text.Quotation Created')}}</span>

                                                                        @endif

                                                                    @else

                                                                        <span class="btn btn-warning">{{__('text.Pending')}}</span>

                                                                    @endif

                                                                @endif
                                                            </td>

                                                            <?php $date = strtotime($key->created_at);

                                                            $date = date('d-m-Y',$date);  ?>

                                                            <td data-sort="{{strtotime($key->created_at)}}">{{$date}}</td>

                                                            <td>
                                                                <div class="dropdown">
                                                                    <button style="outline: none;" class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">{{__('text.Action')}}
                                                                        <span class="caret"></span></button>
                                                                    <ul class="dropdown-menu">

                                                                        @if(auth()->user()->can('handyman-quote-request'))

                                                                            <li><a href="{{ url('/aanbieder/bekijk-offerteaanvraag-aanbieder/'.$key->id) }}">{{__('text.View')}}</a></li>

                                                                        @endif

                                                                            @if(auth()->user()->can('view-handyman-quotation'))

                                                                                @if($invoices[$i])
                                                                                    <li><a href="{{ route('view-new-quotation', ['id' => $invoices[$i]->id]) }}">{{__('text.View Quotation')}}</a></li>
                                                                                @endif

                                                                            @endif


                                                                            @if(auth()->user()->can('download-handyman-quote-request'))

                                                                                <li><a href="{{ url('/aanbieder/download-handyman-quote-request/'.$key->id) }}">{{__('text.Download PDF')}}</a></li>

                                                                            @endif


                                                                            @if($key->status == 2 && $invoices[$i] && $invoices[$i]->accepted)

                                                                                @if(auth()->user()->can('create-handyman-invoice'))

                                                                                    <!-- <li><a href="{{ url('/aanbieder/opstellen-factuur/'.$invoices[$i]->id) }}">{{__('text.Create Invoice')}}</a></li> -->

                                                                                @endif

                                                                            @elseif($key->status != 2 && $key->status != 3)

                                                                                @if($invoices[$i])

                                                                                    @if($invoices[$i]->ask_customization)

                                                                                        <li><a onclick="ask(this)" data-text="{{$invoices[$i]->review_text}}" href="javascript:void(0)">{{__('text.Review Reason')}}</a></li>

                                                                                    @endif

                                                                                @else

                                                                                    @if(auth()->user()->can('create-quotation'))

                                                                                        <li><a href="{{ route('create-custom-quotation', ['id' => Crypt::encrypt($key->id) ]) }}">{{__('text.Create Quotation')}}</a></li>

                                                                                    @endif

                                                                                @endif

                                                                            @endif

                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    @endforeach
                                                    </tbody>
                                                </table></div></div>
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


    <style type="text/css">
    
        td .btn
        {
            white-space: normal;
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

        .btn-primary1
        {
            background-color: darkcyan;
            border-color: darkcyan;
            color: white !important;
        }

        .dropdown-menu
        {
            position: relative;
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

    </style>


    <style type="text/css">

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
            width: 168px !important;
        }

        #client{
            width: 185px !important;
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


    </style>

@endsection

@section('scripts')

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript">

        $(document).ready( function () {

            var tableId = 'example';

            var table = $('#' + tableId).DataTable({
                order: [[6, 'desc']],
                autoWidth: false,
                responsive: false,
                scrollX: true,
                "oLanguage": {
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
                initComplete: function (settings) {

                    setUserColumnsDefWidths();
                    
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
                        
                            var index = $(this).index();
                            $(this).width(ui.size.width);
                            $('#' + tableId + '_wrapper .dataTables_scrollBody thead th').eq(index).width(ui.size.width);
                            saveColumnSettings();

                        }
                    });

                },
            });

            function preventOrdering(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
            }

            function setUserColumnsDefWidths() {

				var userColumnDef;
                // localStorage.clear();

				// Get the settings for this table from localStorage
				var userColumnDefs = JSON.parse(localStorage.getItem("requests_table")) || [];

				if (userColumnDefs.length === 0) return;

                var table_width = localStorage.getItem('requests_table_width');

				$('#' + tableId + '_wrapper .dataTables_scrollHead table thead tr').find("th").each( function ( target ) {

                    // Check if there is a setting for this column in localStorage
					existingSetting = userColumnDefs.findIndex( function(column) { return column.targets === target; });
                        
                    if ( existingSetting !== -1 ) {

                        var index = $(this).index();
                        // Update the width
                        $(this).width(userColumnDefs[existingSetting].width);
                        $('#' + tableId + '_wrapper .dataTables_scrollBody thead th').eq(index).width(userColumnDefs[existingSetting].width);

					}

				});

                $('#' + tableId + '_wrapper table').width(table_width);
			}


			function saveColumnSettings() {
	
				var userColumnDefs = JSON.parse(localStorage.getItem("requests_table")) || [];
	
				var width, header, table_width, existingSetting;

				$('#' + tableId + '_wrapper .dataTables_scrollHead table thead tr').find("th").each( function ( targets ) {
	
					// Check if there is a setting for this column in localStorage
					existingSetting = userColumnDefs.findIndex(function(column) { return column.targets === targets; });
							
                    table_width =  $('#' + tableId + '_wrapper .dataTables_scrollHead table').width();

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

				// Save (or update) the settings in localStorage
				localStorage.setItem('requests_table_width', table_width);
                localStorage.setItem("requests_table", JSON.stringify(userColumnDefs));
			}

            table.on('draw', function () {
                setUserColumnsDefWidths();
            });

        });

        function ask(e)
        {
            var text = $(e).data('text');

            $('#review_text').val(text);

            $('#myModal1').modal('toggle');
        }

    </script>

@endsection
