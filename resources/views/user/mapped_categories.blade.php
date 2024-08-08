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
                                            <h2>{{__('text.Categories Mapping')}}</h2>
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
    
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Pieppiep Category Title')}}</th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 239px;" aria-sort="ascending" aria-label="Donor's Photo: activate to sort column descending">{{__('text.Prestashop Category Title')}}</th>
    
                                                        </tr>
                                                        </thead>
    
                                                        <tbody>

                                                            @foreach($categories as $key)

                                                                <tr role="row">
                                                                    <?php $mapped = $mapped_categories->filter(function ($item) use ($key) { return $item->cat_id == $key->id; })->first(); ?>
                                                                    <td>{{$key->cat_name}}</td>
                                                                    <td>
                                                                        <input name="category_ids[]" type="hidden" value="{{$key->id}}">
                                                                        <input class="form-control" name="mapped_category_titles[]" type="text" value="{{$mapped ? $mapped->title : ''}}">
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
                    </form>
                </div>
                <!-- Ending of Dashboard data-table area -->
            </div>
        </div>
    </div>

    <style type="text/css">

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
