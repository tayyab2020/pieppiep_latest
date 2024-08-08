@extends((Route::currentRouteName() == "default-models-index" || Route::currentRouteName() == "admin-models-update-requests") ? 'layouts.admin' : 'layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <div class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header products">
                                        <h2>{{Route::currentRouteName() == "default-models-index" ? "Models" : __("text.Models Update Requests")}}</h2>

                                        @if(Route::currentRouteName() == "default-models-index")

                                            <a href="{{route('default-models-create')}}" class="btn add-newProduct-btn"><i class="fa fa-plus"></i> Add New Model</a>

                                        @endif

                                    </div>
                                    <hr>
                                    <div>
                                        @include('includes.form-success')

                                        <div class="row">
                                            <div class="col-sm-12">

                                            <div style="display: flex;justify-content: flex-end;padding-right: 10px;margin-bottom: 10px;">

                                                <select class="form-control" style="border: 1px solid #c5c3c3;border-radius: 5px;padding: 7px;width: auto;" id="dropdown1">
                                                    
                                                    <option value="">Select Category to filter</option>

                                                    @foreach($categories as $key)

                                                        <option value="{{$key->cat_name}}">{{$key->cat_name}}</option>

                                                    @endforeach

                                                </select>

                                            </div>

                                                <table id="example" class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline" role="grid" aria-describedby="product-table_wrapper_info" style="width: 100%;" width="100%" cellspacing="0">
                                                    <thead>
                                                    <tr role="row">
                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 344px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">ID</th>
                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 144px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">Title</th>

                                                        @if(Route::currentRouteName() == "admin-models-update-requests")
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 144px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">Supplier</th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 144px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">New</th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 144px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">Default</th>
                                                        @endif

                                                        <th class="sorting_asc" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 144px;" aria-sort="ascending" aria-label="Blood Group Name: activate to sort column descending">Category(s)</th>
                                                        <th class="sorting" tabindex="0" aria-controls="product-table_wrapper" rowspan="1" colspan="1" style="width: 314px;" aria-label="Actions: activate to sort column ascending">Actions</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($models as $item)
                                                        <tr role="row" class="odd">
                                                            <td>{{$item->id}}</td>
                                                            <td>{{$item->model}}</td>

                                                            @if(Route::currentRouteName() == "admin-models-update-requests")

                                                                <td>{{$item->company_name}}</td>
                                                                <td>{{$item->model_id ? "No" : "Yes"}}</td>
                                                                <td>{{$item->default_feature ? "Yes" : "No"}}</td>

                                                            @endif
                                                            
                                                            <?php $item_categories = ''; foreach($item->getCategoriesAttribute() as $temp){ $item_categories = $item_categories.', '.$temp->cat_name; } ?>
                                                            
                                                            <td>{{ltrim($item_categories,', ')}}</td>
                                                            <td>
                                                                <a href="{{Route::currentRouteName() == 'default-models-index' ? route('default-models-edit',$item->id) : (Route::currentRouteName() == 'admin-models-update-requests' ? route('admin-model-update-request',$item->id) : route('model-update-request',$item->id))}}" class="btn btn-primary product-btn"><i class="fa fa-edit"></i> {{Route::currentRouteName() != 'models-update-requests' ? "Edit" : "View"}}</a>
                                                                <a href="{{Route::currentRouteName() == 'default-models-index' ? route('default-models-delete',$item->id) : (Route::currentRouteName() == 'admin-models-update-requests' ? route('admin-model-update-request-delete',$item->id) : route('model-update-request-delete',$item->id))}}" class="btn btn-danger product-btn"><i class="fa fa-trash"></i> Remove</a>
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

@endsection

@section('scripts')

    <script type="text/javascript">
        var table = $('#example').DataTable();

        $('#dropdown1').on('change', function () {
            table.columns(2).search( this.value ).draw();
        } );
    </script>

@endsection
