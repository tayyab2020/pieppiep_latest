@extends('layouts.handyman')

@section('content')

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <form action="" method="POST">
                        {{csrf_field()}}
                        <div class="section-padding add-product-1">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="add-product-box">
                                        <div style="justify-content: space-between;" class="add-product-header products">
                                            <h2>{{__('text.Payment Accounts')}}</h2>
                                            <button type="submit" class="btn btn-success">{{__("text.Save")}}</button>
                                        </div>
                                        <hr>
                                        <div>
                                            @include('includes.form-success')
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                        <thead>
                                                        <tr role="row">
    
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Pieppiep Title')}}</th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Reeleezee Title')}}</th>
                                                            <!-- <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 171px;" aria-label="Donor's Name: activate to sort column ascending">{{__('text.General Ledger')}}</th> -->
    
                                                        </tr>
                                                        </thead>
    
                                                        <tbody>

                                                            <tr role="row">
                                                                <?php $selected = $payment_accounts->filter(function($item) { return $item->title == "Bank"; })->first(); ?>
                                                                <td>{{__('text.Bank')}}</td>
                                                                <td>
                                                                    <input name="payment_accounts[]" type="hidden" value="Bank">
                                                                    <input required class="form-control" name="reeleezee_payment_accounts[]" type="text" value="{{$selected ? $selected->reeleezee_title : ''}}">
                                                                </td>
                                                                <!-- <td>
                                                                    <select class="form-control general_ledger" name="general_ledgers[]">
                                                                        <option value="">{{__("text.Select General Ledger")}}</option>
                                                                        @foreach($general_ledgers as $ledger)
                                                                            <option {{$selected ? ($selected->ledger_id == $ledger->id ? "selected" : "") : ""}} value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td> -->
                                                            </tr>

                                                            <tr role="row">
                                                                <?php $selected = $payment_accounts->filter(function($item) { return $item->title == "Cash"; })->first(); ?>
                                                                <td>{{__('text.Cash')}}</td>
                                                                <td>
                                                                    <input name="payment_accounts[]" type="hidden" value="Cash">
                                                                    <input required class="form-control" name="reeleezee_payment_accounts[]" type="text" value="{{$selected ? $selected->reeleezee_title : ''}}">
                                                                </td>
                                                                <!-- <td>
                                                                    <select class="form-control general_ledger" name="general_ledgers[]">
                                                                        <option value="">{{__("text.Select General Ledger")}}</option>
                                                                        @foreach($general_ledgers as $ledger)
                                                                            <option {{$selected ? ($selected->ledger_id == $ledger->id ? "selected" : "") : ""}} value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td> -->
                                                            </tr>

                                                            <tr role="row">
                                                                <?php $selected = $payment_accounts->filter(function($item) { return $item->title == "Betaallink"; })->first(); ?>
                                                                <td>{{__('text.Pin device')}}</td>
                                                                <td>
                                                                    <input name="payment_accounts[]" type="hidden" value="Betaallink">
                                                                    <input required class="form-control" name="reeleezee_payment_accounts[]" type="text" value="{{$selected ? $selected->reeleezee_title : ''}}">
                                                                </td>
                                                                <!-- <td>
                                                                    <select class="form-control general_ledger" name="general_ledgers[]">
                                                                        <option value="">{{__("text.Select General Ledger")}}</option>
                                                                        @foreach($general_ledgers as $ledger)
                                                                            <option {{$selected ? ($selected->ledger_id == $ledger->id ? "selected" : "") : ""}} value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td> -->
                                                            </tr>

                                                            <tr role="row">
                                                                <?php $selected = $payment_accounts->filter(function($item) { return $item->title == "Mollie"; })->first(); ?>
                                                                <td>{{__('text.Mollie')}}</td>
                                                                <td>
                                                                    <input name="payment_accounts[]" type="hidden" value="Mollie">
                                                                    <input required class="form-control" name="reeleezee_payment_accounts[]" type="text" value="{{$selected ? $selected->reeleezee_title : ''}}">
                                                                </td>
                                                                <!-- <td>
                                                                    <select class="form-control general_ledger" name="general_ledgers[]">
                                                                        <option value="">{{__("text.Select General Ledger")}}</option>
                                                                        @foreach($general_ledgers as $ledger)
                                                                            <option {{$selected ? ($selected->ledger_id == $ledger->id ? "selected" : "") : ""}} value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td> -->
                                                            </tr>

                                                            <tr role="row">
                                                                <?php $selected = $payment_accounts->filter(function($item) { return $item->title == "Settled"; })->first(); ?>
                                                                <td>{{__('text.Settled')}}</td>
                                                                <td>
                                                                    <input name="payment_accounts[]" type="hidden" value="Settled">
                                                                    <input required class="form-control" name="reeleezee_payment_accounts[]" type="text" value="{{$selected ? $selected->reeleezee_title : ''}}">
                                                                </td>
                                                                <!-- <td>
                                                                    <select class="form-control general_ledger" name="general_ledgers[]">
                                                                        <option value="">{{__("text.Select General Ledger")}}</option>
                                                                        @foreach($general_ledgers as $ledger)
                                                                            <option {{$selected ? ($selected->ledger_id == $ledger->id ? "selected" : "") : ""}} value="{{$ledger->id}}">{{$ledger->number . " - ". $ledger->title}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td> -->
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Ending of Dashboard data-table area -->
            </div>
        </div>
    </div>

    <style type="text/css">

        .select2-selection__rendered
        {
            white-space: normal !important;
        }

        .table.products > tbody > tr > td
        {
            text-align: center;
        }

        .table>caption+thead>tr:first-child>td, .table>caption+thead>tr:first-child>th, .table>colgroup+thead>tr:first-child>td, .table>colgroup+thead>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th
        {
            text-align: center;
        }

    </style>

@endsection

@section('scripts')

    <script type="text/javascript">

        $(document).ready(function() {

            var $selects = $('.general_ledger').change(function() {

                var id = this.value;
                var selector = this;

                if ($selects.find('option[value=' + id + ']:selected').length > 1) {

                    Swal.fire({
                        title: 'Oops...',
                        text: '{{__("text.Ledger already selected!")}}',
                    });

                    $(selector).val('').trigger('change.select2');

                }

            });


        });

        $(".general_ledger").select2({
			width: '100%',
			placeholder: "{{__('text.Select General Ledger')}}",
			allowClear: true,
			"language": {
				"noResults": function () {
					return '{{__('text.No results found')}}';
				}
			},
		});

        $('#example').DataTable({
            order: [],
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
            }
        });
    </script>

@endsection
