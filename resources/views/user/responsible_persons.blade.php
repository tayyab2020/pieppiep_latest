@extends('layouts.handyman')

@section('content')

    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div style="padding: 0;" class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header products">
                                        <h2>{{__('text.Responsible Persons')}}</h2>
                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">ID</th>
                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Name')}}</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Blood Group: activate to sort column ascending">{{__('text.Font Color')}}</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" aria-label="Actions: activate to sort column ascending">{{__('text.Actions')}}</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>

                                                    @foreach($responsible_persons as $user)
                                                        
                                                        <tr role="row" class="odd">

                                                            <td>{{$user->id}}</td>

                                                            <td>{{$user->name . " " . $user->family_name}}</td>

                                                            <td>
                                                                @if($user->agenda_font_color)
                                                                    <span style="width: 10%;display: inline-block;height: 35px;border: 0;background-color: {{$user->agenda_font_color}};" class="input-group-addon"></span>
                                                                @endif
                                                            </td>

                                                            <td>
                                                                <a href="{{route('edit-planning-responsible-person',$user->id)}}" class="btn btn-primary product-btn"><i class="fa fa-edit"></i> {{__('text.Edit')}}</a>
                                                            </td>
                                                        </tr>

                                                    @endforeach
                                                    </tbody>
                                                </table>
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

    <style type="text/css">

        .product-btn
        {
            margin: 5px;
        }

        .checked {
            color: orange !important;
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

        $('#example').DataTable({
            order: [[0, 'desc']],
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
