@extends($admin == 1 ? 'layouts.admin' : 'layouts.handyman')

@section('styles')

<link href="{{asset('assets/admin/css/jquery-ui.css')}}" rel="stylesheet" type="text/css">

<style type="text/css">
    .colorpicker-alpha {display:none !important;}
    .colorpicker{ min-width:128px !important;}
    .colorpicker-color {display:none !important;}
</style>

@endsection

@section('content')

    <div class="right-side">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <!-- Starting of Dashboard area -->
                        <div class="section-padding add-product-1">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="add-product-box">
                                        <div class="add-product-header">
                                            <h2>{{isset($cats) ? __('text.Edit Product') : __('text.Add Product')}}</h2>
                                            <a href="{{$admin ? route('all-products') : route('admin-product-index')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                        </div>

                                        <hr>

                                        @include('includes.form-error')
                                        @include('includes.form-success')

                                        <select style="display: none;" class="form-control all-models" id="blood_grp">

                                            @foreach($predefined_models as $key)

                                                <option value="{{$key->id}}">{{$key->model}}</option>

                                            @endforeach

                                        </select>

                                        <div class="product-configuration" style="width: 85%;margin: auto;">

                                            <ul style="border: 0;" class="nav nav-tabs">
                                                <li style="margin-bottom: 0;" class="active"><a data-toggle="tab" href="#menu1">{{__("text.General Information")}}</a></li>
                                                {{--<li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu2">General Options</a></li>--}}
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu3">{{__("text.Colors Options")}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu4">{{__("text.Price Tables")}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu5">{{__("text.Features")}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu6">{{__("text.Price Control")}}</a></li>
                                                <li style="margin-bottom: 0;"><a data-toggle="tab" href="#menu7">{{__("text.Models")}}</a></li>
                                            </ul>

                                            <form id="product_form" style="padding: 0;" class="form-horizontal" action="{{$admin ? route('all-product-store') : route('admin-product-store')}}" method="POST" enctype="multipart/form-data">

                                                {{csrf_field()}}

                                                @if($admin)
                                                    <input type="hidden" name="supplier_id" value="{{$user_id}}">
                                                @endif

                                                <input type="hidden" id="submit_check" value="0">
                                                <input type="hidden" name="cat_id" value="{{isset($cats) ? $cats->id : null}}" />
                                                <input type="hidden" name="form_type" id="form_type" value="2" />

                                                <div style="padding: 40px 15px 20px 15px;border: 1px solid #24232329;" class="tab-content">

                                                    <div id="menu1" class="tab-pane fade in active">

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">{{__('text.Category*')}}</label>
                                                            <div class="col-sm-6">
                                                                <select class="js-data-example-ajax8 form-control" style="height: 40px;" name="category_id" id="blood_grp" required>

                                                                    <option value="">Select Category</option>

                                                                    @foreach($categories as $key)
                                                                        <option @if(isset($cats)) @if($cats->category_id == $key->id) selected @endif @endif value="{{$key->id}}">{{$key->cat_name}}</option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">{{__('text.Sub Category')}}</label>
                                                            <div class="col-sm-6">
                                                                <select class="js-data-example-ajax9 form-control" style="height: 40px;" name="sub_category_id" id="blood_grp">

                                                                    <option value="">{{__('text.Select sub category')}}</option>

                                                                    @if(isset($sub_categories))

                                                                        @foreach($sub_categories as $sub_cat)

                                                                            <option @if(isset($cats)) @if($cats->sub_category_id == $sub_cat->id) selected @endif @endif value="{{$sub_cat->id}}">{{$sub_cat->cat_name}}</option>

                                                                        @endforeach

                                                                    @endif

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">{{__('text.Brand*')}}</label>
                                                            <div class="col-sm-6">
                                                                <select class="js-data-example-ajax1 form-control" style="height: 40px;" name="brand_id" id="blood_grp" required>

                                                                    <option value="">Select Brand</option>

                                                                    @foreach($brands as $key)
                                                                        <option @if(isset($cats)) @if($cats->brand_id == $key->id) selected @endif @endif value="{{$key->id}}">{{$key->cat_name}}</option>
                                                                    @endforeach

                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Margin (%)*')}}</label>
                                                            <div class="col-sm-6">
                                                                <input min="100" value="{{isset($cats) ? $cats->margin : null}}" class="form-control" name="margin" id="margin_input" placeholder="{{__('text.Enter Product margin')}}" required step="1" type="number">
                                                            </div>
                                                        </div>


                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_display_name">{{__('text.Title')}}</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->title : null}}" class="form-control" name="title" id="blood_group_display_name" placeholder="{{__('text.Enter Product Title')}}" required="" type="text">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">Slug*</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->slug : null}}" class="form-control" name="slug" id="blood_group_slug" placeholder="{{__('text.Enter Product Slug')}}" required="" type="text">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">{{__('text.Additional info')}}</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->additional_info : null}}" class="form-control" name="additional_info" id="blood_group_slug" placeholder="{{__('text.Enter Additional info')}}" type="text">
                                                            </div>
                                                        </div>
                                                        

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">{{__('text.Delivery Time (In Days)*')}}</label>
                                                            <div class="col-sm-6">
                                                                <select class="js-data-example-ajax6 form-control" style="height: 40px;" name="delivery_days" id="blood_grp" required>

                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 1) selected @endif @endif value="1">1</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 2) selected @endif @endif value="2">2</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 3) selected @endif @endif value="3">3</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 4) selected @endif @endif value="4">4</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 5) selected @endif @endif value="5">5</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 6) selected @endif @endif value="6">6</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 7) selected @endif @endif value="7">7</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 8) selected @endif @endif value="8">8</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 9) selected @endif @endif value="9">9</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 10) selected @endif @endif value="10">10</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 11) selected @endif @endif value="11">11</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 12) selected @endif @endif value="12">12</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 13) selected @endif @endif value="13">13</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 14) selected @endif @endif value="14">14</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 15) selected @endif @endif value="15">15</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 16) selected @endif @endif value="16">16</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 17) selected @endif @endif value="17">17</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 18) selected @endif @endif value="18">18</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 19) selected @endif @endif value="19">19</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 20) selected @endif @endif value="20">20</option>
                                                                    <option @if(isset($cats)) @if($cats->delivery_days == 21) selected @endif @endif value="21">21</option>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{--<div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">Model*</label>
                                                            <div class="col-sm-6">
                                                                <select class="js-data-example-ajax2 form-control" style="height: 40px;" name="model_id" id="blood_grp" required>

                                                                    <option value="">Select Model</option>

                                                                    @if(isset($cats))

                                                                        @foreach($models as $key)

                                                                            <option @if($cats->model_id == $key->id) selected @endif value="{{$key->id}}">{{$key->cat_name}}</option>

                                                                        @endforeach

                                                                    @endif

                                                                </select>
                                                            </div>
                                                        </div>--}}

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="service_description">{{__('text.Product Description')}}</label>
                                                            <div class="col-sm-6">
                                                                <textarea class="form-control" name="description" id="service_description" rows="5" style="resize: vertical;" placeholder="{{__('text.Enter Product Description')}}">{{isset($cats) ? $cats->description : null}}</textarea>
                                                            </div>
                                                        </div>


                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="current_photo">{{__('text.Current photo*')}}</label>
                                                            <div class="col-sm-6">
                                                                <img width="130px" height="90px" id="adminimg" src="{{isset($cats->photo) ? asset('assets/images/'.$cats->photo):'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'}}" alt="">
                                                            </div>
                                                        </div>


                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="profile_photo">{{__('text.Add photo')}}</label>
                                                            <div class="col-sm-6">
                                                                <input type="file" id="uploadFile" class="hidden" name="photo" value="">
                                                                <button type="button" id="uploadTrigger" class="form-control"><i class="fa fa-download"></i> {{__('text.Add Photo')}}</button>
                                                                <p>{{__('text.Prefered Size: (600x600) or Square Sized Image')}}</p>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{--<div id="menu2" class="tab-pane fade">

                                                        <input type="hidden" name="cat_id" value="{{isset($cats) ? $cats->id : null}}" />

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_display_name">Min Height</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->min_height : null}}" class="form-control" name="min_height" id="blood_group_display_name" placeholder="" type="text">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">Max Height</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->max_height : null}}" class="form-control" name="max_height" id="blood_group_slug" placeholder="" type="text">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">Min Width</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->min_width : null}}" class="form-control" name="min_width" id="blood_group_slug" placeholder="" type="text">
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-4" for="blood_group_slug">Max Width</label>
                                                            <div class="col-sm-6">
                                                                <input value="{{isset($cats) ? $cats->max_width : null}}" class="form-control" name="max_width" id="blood_group_slug" placeholder="" type="text">
                                                            </div>
                                                        </div>

                                                    </div>--}}

                                                    <div id="menu3" class="tab-pane fade">

                                                        <div class="wrapper1">
                                                            <div class="file-upload">
                                                                <input id="upload" type="file" name="files[]">
                                                                <i style="font-size: 15px;margin-right: 15px;" class="fa fa-arrow-up"></i> {{__('text.Import Colors')}}
                                                            </div>
                                                        </div>

                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
                                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>

                                                        <script>

                                                            var ExcelToJSON = function() {                                                                

                                                                this.parseExcel = function(file) {

                                                                    var reader = new FileReader();
                                                                    reader.onload = function(e) {

                                                                        var data = e.target.result;
                                                                        var workbook = XLSX.read(data, {
                                                                            type: 'binary'
                                                                        });

                                                                        workbook.SheetNames.forEach(function(sheetName) {
                                                                            // Here is your object
                                                                            var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                                                                            var json_object = JSON.stringify(XL_row_object);
                                                                            var data = JSON.parse(json_object);

                                                                            for (i = 0; i < data.length; ++i)
                                                                            {

                                                                                var color_title = data[i]['Color Title'];

                                                                                if(!color_title)
                                                                                {
                                                                                    color_title = '';
                                                                                }

                                                                                var color_code = data[i]['Color Code'];

                                                                                if(!color_code)
                                                                                {
                                                                                    color_code = '';
                                                                                }

                                                                                var max_height = data[i]['Max Height'];

                                                                                if(!max_height)
                                                                                {
                                                                                    max_height = '';
                                                                                }

                                                                                var price_table = data[i]['Price Table'];

                                                                                if(!price_table)
                                                                                {
                                                                                    price_table = '';
                                                                                }

                                                                                <?php
                                                                                $js_array = json_encode($tables);
                                                                                echo "var tables_array = ". $js_array . ";\n";
                                                                                ?>

                                                                                var options = "";

                                                                                for (x = 0; x < tables_array.length; ++x)
                                                                                {
                                                                                    if(tables_array[x]['title'] == data[i]['Price Table'])
                                                                                    {
                                                                                        options = options + '<option selected value="'+tables_array[x]['id']+'">'+tables_array[x]['title']+'</option>';
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        options = options + '<option value="'+tables_array[x]['id']+'">'+tables_array[x]['title']+'</option>';
                                                                                    }
                                                                                }

                                                                                var color_row = $('.color_box').find('.form-group').last().data('id');
                                                                                color_row = color_row + 1;

                                                                                $(".color_box").append('<div class="form-group" data-id="'+color_row+'">\n' +
                                                                                    '\n' +
                                                                                    '                                                                <div class="col-sm-3">\n' +
                                                                                    '\n' +
                                                                                    '                                                                    <input class="form-control color_title" name="colors[]" value="'+color_title+'" id="blood_group_slug" placeholder="{{__('text.Color Title')}}" type="text">\n' +
                                                                                    '\n' +
                                                                                    '                                                                </div>\n' +
                                                                                    '\n' +
                                                                                    '                                                                <div class="col-sm-3">\n' +
                                                                                    '\n' +
                                                                                    '                                                                    <input class="form-control color_code" name="color_codes[]" value="'+color_code+'" id="blood_group_slug" placeholder="{{__('text.Color Code')}}" type="text">\n' +
                                                                                    '\n' +
                                                                                    '                                                                </div>\n' +
                                                                                    '\n' +
                                                                                    '                                                                <div class="col-sm-2">\n' +
                                                                                    '\n' +
                                                                                    '                                                                    <input class="form-control color_max_height" maskedformat="9,1" name="color_max_height[]" value="'+max_height+'" id="blood_group_slug" placeholder="{{__('text.Max Height')}}" type="text">\n' +
                                                                                    '\n' +
                                                                                    '                                                                </div>\n' +
                                                                                    '\n' +
                                                                                    '                                                                <div class="col-sm-3">\n' +
                                                                                    '                                                                    <select class="form-control validate js-data-example-ajax4" name="price_tables[]">\n' +
                                                                                    '\n' +
                                                                                    '                                                                        <option value="">Select Price Table</option>\n' +
                                                                                    '\n' +
                                                                                                                                                              options +
                                                                                    '\n' +
                                                                                    '                                                                    </select>\n' +
                                                                                    '                                                                </div>\n'+
                                                                                    '\n' +
                                                                                    '                <div class="col-xs-1 col-sm-1">\n' +
                                                                                    '                <span class="ui-close remove-color" data-id="" style="margin:0;right:70%;">X</span>\n' +
                                                                                    '                </div>\n' +
                                                                                    '\n' +
                                                                                    '                </div>');

                                                                                $(".js-data-example-ajax4").select2({
                                                                                    width: '100%',
                                                                                    height: '200px',
                                                                                    placeholder: "Select Price Table",
                                                                                    allowClear: true,
                                                                                });

                                                                            }

                                                                        });

                                                                        $('.js-data-example-ajax4').trigger('change');
                                                                    };

                                                                    reader.onerror = function(ex) {
                                                                        alert(ex);
                                                                    };

                                                                    reader.readAsBinaryString(file);

                                                                };

                                                            };

                                                            function handleFileSelect(evt) {

                                                                if($('#upload').val())
                                                                {
                                                                    var files = evt.target.files; // FileList object
                                                                    var xl2json = new ExcelToJSON();
                                                                    xl2json.parseExcel(files[0]);
                                                                    $('#upload').val(null);
                                                                }

                                                            }

                                                            document.getElementById('upload').addEventListener('change', handleFileSelect, false);

                                                        </script>

                                                        <style>

                                                            .wrapper1 {
                                                                width: 100%;
                                                                height: 100%;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: flex-start;
                                                                margin-bottom: 20px;
                                                            }
                                                            .wrapper1 .file-upload {
                                                                height: 50px;
                                                                width: auto;
                                                                border-radius: 5px;
                                                                position: relative;
                                                                display: flex;
                                                                justify-content: center;
                                                                align-items: center;
                                                                /*border: 4px solid #fff;*/
                                                                overflow: hidden;
                                                                background-image: linear-gradient(to bottom, #2590eb 50%, #fff 50%);
                                                                background-size: 100% 200%;
                                                                transition: all 1s;
                                                                color: #fff;
                                                                font-size: 16px;
                                                                font-weight: 600;
                                                                padding: 20px;
                                                            }
                                                            .wrapper1 .file-upload input[type='file'] {
                                                                height: 50px;
                                                                width: 170px;
                                                                position: absolute;
                                                                top: 0;
                                                                left: 0;
                                                                opacity: 0;
                                                                cursor: pointer;
                                                            }
                                                            .wrapper1 .file-upload:hover {
                                                                background-position: 0 -100%;
                                                                color: #2590eb;
                                                            }

                                                        </style>

                                                        <div class="color_box" style="margin-bottom: 20px;">

                                                            <input type="hidden" name="removed_colors" id="removed_colors">

                                                            @if(isset($colors_data) && count($colors_data) > 0)

                                                                @foreach($colors_data as $i => $key)

                                                                    <div class="form-group" data-id="{{$i+1}}">

                                                                        <div class="col-sm-3">

                                                                            <input value="{{$key->color}}" class="form-control color_title" name="colors[]" id="blood_group_slug" placeholder="{{__('text.Color Title')}}" type="text">

                                                                        </div>

                                                                        <div class="col-sm-3">

                                                                            <input value="{{$key->color_code}}" class="form-control color_code" name="color_codes[]" id="blood_group_slug" placeholder="{{__('text.Color Code')}}" type="text">

                                                                        </div>

                                                                        <div class="col-sm-2">

                                                                            <input class="form-control color_max_height" value="{{str_replace(".",",",$key->max_height)}}" maskedformat="9,1" name="color_max_height[]" id="blood_group_slug" placeholder="{{__('text.Max Height')}}" type="text">

                                                                        </div>

                                                                        <div class="col-sm-3">

                                                                            <select class="form-control validate js-data-example-ajax4" name="price_tables[]">

                                                                                <option value="">Select Price Table</option>

                                                                                @foreach($tables as $table)

                                                                                    <option @if($table->id == $key->table_id) selected @endif value="{{$table->id}}">{{$table->title}}</option>

                                                                                @endforeach

                                                                            </select>

                                                                        </div>

                                                                        <div class="col-xs-1 col-sm-1">
                                                                            <span class="ui-close remove-color" data-id="{{$key->id}}" style="margin:0;right:70%;">X</span>
                                                                        </div>

                                                                    </div>

                                                                @endforeach

                                                            @else

                                                                <div class="form-group" data-id="1">

                                                                    <div class="col-sm-3">

                                                                        <input class="form-control color_title" name="colors[]" id="blood_group_slug" placeholder="{{__('text.Color Title')}}" type="text">

                                                                    </div>

                                                                    <div class="col-sm-3">

                                                                        <input class="form-control color_code" name="color_codes[]" id="blood_group_slug" placeholder="{{__('text.Color Code')}}" type="text">

                                                                    </div>

                                                                    <div class="col-sm-2">

                                                                        <input class="form-control color_max_height" maskedformat="9,1" name="color_max_height[]" id="blood_group_slug" placeholder="{{__('text.Max Height')}}" type="text">

                                                                    </div>

                                                                    <div class="col-sm-3">

                                                                        <select class="form-control validate js-data-example-ajax4" name="price_tables[]">

                                                                            <option value="">Select Price Table</option>

                                                                            @foreach($tables as $table)

                                                                                <option value="{{$table->id}}">{{$table->title}}</option>

                                                                            @endforeach

                                                                        </select>

                                                                    </div>

                                                                    <div class="col-xs-1 col-sm-1">
                                                                        <span class="ui-close remove-color" data-id="" style="margin:0;right:70%;">X</span>
                                                                    </div>

                                                                </div>

                                                            @endif

                                                        </div>

                                                        <div class="form-group add-color">
                                                            <label class="control-label col-sm-3" for=""></label>

                                                            <div class="col-sm-12 text-center">
                                                                <button class="btn btn-default featured-btn" type="button" id="add-color-btn"><i class="fa fa-plus"></i> {{__('text.Add More Colors')}}</button>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div id="menu4" class="tab-pane fade">

                                                        <div class="row">
                                                            <div class="col-sm-12">

                                                                <table id="example1"
                                                                       class="table table-striped table-hover products dt-responsive dataTable no-footer dtr-inline"
                                                                       role="grid" aria-describedby="product-table_wrapper_info"
                                                                       style="width: 100%;display: inline-table;overflow-x: auto;" width="100%" cellspacing="0">
                                                                    <thead>

                                                                    <tr role="row">

                                                                        <th tabindex="0"
                                                                            aria-controls="product-table_wrapper" rowspan="1"
                                                                            colspan="1" style="padding: 0 25px;border: 1px solid #e7e7e7;text-align: center;" aria-sort="ascending"
                                                                            aria-label="Blood Group Name: activate to sort column descending">
                                                                            ID
                                                                        </th>

                                                                        <th tabindex="0"
                                                                            aria-controls="product-table_wrapper" rowspan="1"
                                                                            colspan="1" style="padding: 0 25px;border: 1px solid #e7e7e7;text-align: center;" aria-sort="ascending"
                                                                            aria-label="Blood Group Name: activate to sort column descending">
                                                                            Table
                                                                        </th>

                                                                        <th tabindex="0"
                                                                            aria-controls="product-table_wrapper" rowspan="1"
                                                                            colspan="1" style="padding: 0 25px;border: 1px solid #e7e7e7;text-align: center;" aria-sort="ascending"
                                                                            aria-label="Blood Group Name: activate to sort column descending">
                                                                            Color
                                                                        </th>

                                                                        <th tabindex="0"
                                                                            aria-controls="product-table_wrapper" rowspan="1"
                                                                            colspan="1" style="padding: 0 25px;border: 1px solid #e7e7e7;text-align: center;" aria-sort="ascending"
                                                                            aria-label="Blood Group Name: activate to sort column descending">
                                                                            Code
                                                                        </th>

                                                                        <th tabindex="0"
                                                                            aria-controls="product-table_wrapper" rowspan="1"
                                                                            colspan="1" style="padding: 0 25px;border: 1px solid #e7e7e7;text-align: center;" aria-sort="ascending"
                                                                            aria-label="Blood Group Name: activate to sort column descending">
                                                                            Action
                                                                        </th>

                                                                    </tr>
                                                                    </thead>

                                                                    <tbody>

                                                                    @if(isset($colors_data))

                                                                        @foreach($colors_data as $i => $key)

                                                                            @if($key->table)

                                                                                <tr data-id="{{$i+1}}">
                                                                                    <td>{{$key->table_id}}</td>
                                                                                    <td>{{$key->table}}</td>
                                                                                    <td>{{$key->color}}</td>
                                                                                    <td>{{$key->color_code}}</td>
                                                                                    <td><a href="/aanbieder/price-tables/prices/view/{{$key->table_id}}">View</a></td>
                                                                                </tr>

                                                                            @endif                                                                            

                                                                        @endforeach

                                                                    @endif

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div id="menu5" class="tab-pane fade">

                                                        <div class="row" style="margin: 0;margin-bottom: 35px;">

                                                            <div class="form-group">

                                                                <div class="row" style="margin: 0;">

                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;color: red;" class="control-label">Ladderband:</label>

                                                                        <input type="hidden" name="ladderband" id="ladderband" value="{{isset($cats) ? $cats->ladderband : 0}}">

                                                                        <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                        <label style="margin: 0;" class="switch">
                                                                            <input class="ladderband" type="checkbox" {{isset($cats) ? ($cats->ladderband ? 'checked' : null) : null}}>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                        <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div @if(isset($cats)) @if(!$cats->ladderband) style='display: none;' @endif @else style='display: none;' @endif id="ladderband_box" class="form-group">

                                                                <div class="row" style="margin: 0;">

                                                                    <div style="margin: 15px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;" class="control-label">Ladderband Value:</label>
                                                                        <input style="width: auto;border-radius: 10px;" class="form-control ladderband_value" value="{{isset($cats) ? $cats->ladderband_value : null}}" name="ladderband_value" id="blood_group_slug" placeholder="Ladderband Value" type="text">
                                                                    </div>

                                                                    <div style="margin: 15px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;" class="control-label">Ladderband Price Impact:</label>

                                                                        <input type="hidden" name="ladderband_price_impact" id="ladderband_price_impact" value="{{isset($cats) ? $cats->ladderband_price_impact : 0}}">

                                                                        <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                        <label style="margin: 0;" class="switch">
                                                                            <input class="ladderband_price_impact" type="checkbox" {{isset($cats) ? ($cats->ladderband_price_impact ? 'checked' : null) : null}}>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                        <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                    </div>

                                                                    <div style="margin: 15px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;" class="control-label">Ladderband Impact Type:</label>

                                                                        <input type="hidden" name="ladderband_impact_type" id="ladderband_impact_type" value="{{isset($cats) ? $cats->ladderband_impact_type : 0}}">

                                                                        <span style="font-size: 15px;padding-right: 10px;font-weight: 600;font-family: monospace;">€</span>
                                                                        <label style="margin: 0;" class="switch">
                                                                            <input class="ladderband_impact_type" type="checkbox" {{isset($cats) ? ($cats->ladderband_impact_type ? 'checked' : null) : null}}>
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                        <span style="font-size: 15px;padding-left: 10px;font-weight: 1000;font-family: revert;">%</span>

                                                                    </div>

                                                                </div>

                                                                <div class="form-group" style="margin: 50px 0px 20px 0;display: flex;justify-content: center;">

                                                                    <div style="border: 1px solid #e1e1e1;padding: 25px;" class="col-lg-11 col-md-11 col-sm-12 col-xs-12">

                                                                        <h4 style="text-align: center;margin-bottom: 50px;">Ladderband Sub Product(s)</h4>

                                                                        <div class="row" style="margin: 0;">

                                                                            <div style="font-family: monospace;" class="col-sm-2">
                                                                                <h4>ID</h4>
                                                                            </div>

                                                                            <div style="font-family: monospace;" class="col-sm-3">
                                                                                <h4>Title</h4>
                                                                            </div>

                                                                            <div style="font-family: monospace;text-align: center;" class="col-sm-3">
                                                                                <h4>Size 38mm</h4>
                                                                            </div>

                                                                            <div style="font-family: monospace;text-align: center;" class="col-sm-3">
                                                                                <h4>Size 25mm</h4>
                                                                            </div>

                                                                        </div>

                                                                        <div class="row ladderband_products_box" style="margin: 15px 0;">

                                                                            <input type="hidden" name="removed_ladderband" id="removed_ladderband_rows">

                                                                            @if(isset($ladderband_data) && count($ladderband_data) > 0)

                                                                                @foreach($ladderband_data as $f => $key)

                                                                                    <div class="form-group" style="margin: 0 0 20px 0;display: flex;justify-content: center;">

                                                                                        <div class="col-sm-2">

                                                                                            <input value="{{$key->code}}" class="form-control sub_code" name="sub_codes[]" id="blood_group_slug" placeholder="Sub Product ID" type="text">

                                                                                        </div>

                                                                                        <div class="col-sm-3">

                                                                                            <input value="{{$key->title}}" class="form-control sub_product_title" name="sub_product_titles[]" id="blood_group_slug" placeholder="Sub Product Title" type="text">

                                                                                        </div>

                                                                                        <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">

                                                                                            <input type="hidden" name="size1_value[]" id="size1_value" value="{{$key->size1_value}}">

                                                                                            <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                                            <label style="margin: 0;" class="switch">
                                                                                                <input {{$key->size1_value ? 'checked' : null}} class="size1_value" type="checkbox">
                                                                                                <span class="slider round"></span>
                                                                                            </label>
                                                                                            <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                                        </div>

                                                                                        <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">

                                                                                            <input type="hidden" name="size2_value[]" id="size2_value" value="{{$key->size2_value}}">

                                                                                            <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                                            <label style="margin: 0;" class="switch">
                                                                                                <input {{$key->size2_value ? 'checked' : null}} class="size2_value" type="checkbox">
                                                                                                <span class="slider round"></span>
                                                                                            </label>
                                                                                            <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                                        </div>

                                                                                        <div class="col-xs-1 col-sm-1">
                                                                                            <span class="ui-close remove-ladderband" data-id="{{$key->id}}" style="margin:0;right:70%;">X</span>
                                                                                        </div>

                                                                                    </div>

                                                                                @endforeach

                                                                            @else

                                                                                <div class="form-group" style="margin: 0 0 20px 0;display: flex;justify-content: center;">

                                                                                    <div class="col-sm-2">

                                                                                        <input class="form-control sub_code" name="sub_codes[]" id="blood_group_slug" placeholder="Sub Product ID" type="text">

                                                                                    </div>

                                                                                    <div class="col-sm-3">

                                                                                        <input class="form-control sub_product_title" name="sub_product_titles[]" id="blood_group_slug" placeholder="Sub Product Title" type="text">

                                                                                    </div>

                                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">

                                                                                        <input type="hidden" name="size1_value[]" id="size1_value" value="0">

                                                                                        <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                                        <label style="margin: 0;" class="switch">
                                                                                            <input class="size1_value" type="checkbox">
                                                                                            <span class="slider round"></span>
                                                                                        </label>
                                                                                        <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                                    </div>

                                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">

                                                                                        <input type="hidden" name="size2_value[]" id="size2_value" value="0">

                                                                                        <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>
                                                                                        <label style="margin: 0;" class="switch">
                                                                                            <input class="size2_value" type="checkbox">
                                                                                            <span class="slider round"></span>
                                                                                        </label>
                                                                                        <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>

                                                                                    </div>

                                                                                    <div class="col-xs-1 col-sm-1">
                                                                                        <span class="ui-close remove-ladderband" data-id="" style="margin:0;right:70%;">X</span>
                                                                                    </div>

                                                                                </div>

                                                                            @endif

                                                                        </div>

                                                                        <div class="form-group add-color">
                                                                            <label class="control-label col-sm-3" for=""></label>

                                                                            <div class="col-sm-12 text-center">
                                                                                <button class="btn btn-default featured-btn" type="button" id="add-ladderband-btn"><i class="fa fa-plus"></i> Add Ladderband Sub Products</button>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="form-group" style="margin-bottom: 20px;">

                                                            <div class="row" style="margin: 0;display: flex;justify-content: center;">

                                                                <div style="font-family: monospace;" class="col-sm-3">
                                                                    <h4>{{__('text.Heading')}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-3">
                                                                    <h4>{{__('text.Action')}}</h4>
                                                                </div>

                                                                {{--<div style="font-family: monospace;" class="col-sm-1">
                                                                    <h4>{{__('text.Value')}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-1">
                                                                    <h4>{{__('text.Max Size')}}</h4>
                                                                </div>

                                                                <div style="text-align: center;font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__('text.Price Impact')}}</h4>
                                                                </div>

                                                                <div style="text-align: center;font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__('text.Impact Type')}}</h4>
                                                                </div>--}}

                                                            </div>

                                                            <div class="row feature_box" style="margin: 15px 0;">

                                                                <input type="hidden" name="removed" id="removed_rows">

                                                                @foreach($features_headings as $h => $heading)
    
                                                                    @if((isset($features_data) && count($features_data) > 0) && ($features_data->contains('heading_id', $heading->id)))

                                                                        <div data-id="{{$h+1}}" class="form-group feature-row" style="margin: 0 0 20px 0;display: flex;justify-content: center;">

                                                                            <div class="col-sm-5">

                                                                                <select class="form-control validate js-data-example-ajax5">

                                                                                    <option value="">{{__('text.Select Feature Heading Placeholder')}}</option>

                                                                                    @foreach($features_headings as $key)

                                                                                        <option {{$heading->id == $key->id ? 'selected' : null}} value="{{$key->id}}">{{$key->title}}</option>

                                                                                    @endforeach

                                                                                </select>

                                                                            </div>

                                                                            <div style="display:flex;" class="col-sm-5">

                                                                                <button data-id="{{$h+1}}" style="margin-right: 10px;" class="btn btn-success create-feature-btn" type="button">{{__('text.Create/Edit Features')}}</button>

                                                                                <span class="ui-close remove-feature" data-id="{{$h+1}}" style="margin:0;position: relative;left: 0;right: 0;">X</span>

                                                                            </div>

                                                                        </div>

                                                                    @endif

                                                                @endforeach

                                                            </div>

                                                            <div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                <div style="width: 80%;" class="modal-dialog">

                                                                    <div class="modal-content">

                                                                        <div class="modal-header">
                                                                            <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                            <h3 id="myModalLabel">{{__("text.Features")}}</h3>
                                                                        </div>

                                                                        <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;padding: 30px 10px;">

                                                                            <div id="primary-features">
                                                                                
                                                                                @foreach($features_headings as $h => $heading)
                                                                                
                                                                                    @if((isset($features_data) && count($features_data) > 0) && ($features_data->contains('heading_id', $heading->id)))
                                                                                    
                                                                                        <div data-id="{{$h+1}}" class="feature-table-container">
                                                                                                
                                                                                            <table style="margin: auto;width: 95%;border-collapse: separate;">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th>{{__('text.Feature')}}</th>
                                                                                                    <th style="width: 10%;">{{__('text.Value')}}</th>
                                                                                                    <th style="width: 10%;">{{__('text.Factor')}}</th>
                                                                                                    <th style="width: 10%;">{{__('text.Sub Feature')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Price Impact')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Impact Type')}}</th>
                                                                                                    <th>{{__('text.Remove')}}</th>
                                                                                                </tr>
                                                                                                </thead>
    
                                                                                                <tbody>
    
                                                                                                    @if(isset($features_data) && count($features_data) > 0)
                                                                                                    
                                                                                                        @foreach($features_data as $f1 => $key1)
    
                                                                                                            @if($heading->id == $key1->heading_id)
            
                                                                                                                <tr data-id="{{$f1+1}}">
                                                                                                                    <td>
                                                                                                                        <input type="hidden" name="f_rows[]" class="f_row" value="{{$f1+1}}">
                                                                                                                        <input value="{{$key1->heading_id}}" type="hidden" class="feature_heading" name="feature_headings[]">
                                                                                                                        <select class="feature_title" name="features[]">
                                                                                                                            <option value="">{{__("text.Feature Title")}}</option>
                                                                                                                            @foreach($feature_values[$f1] as $value)
                                                                                                                                <option {{$key1->feature_value_id == $value->id ? "selected" : ""}} value="{{$value->id}}">{{$value->title}}</option>
                                                                                                                            @endforeach
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input value="{{$key1->value}}" class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <input value="{{$key1->factor_value}}" class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <button style="width: 100%;white-space: normal;" data-id="{{$f1+1}}" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select style="padding: 5px;" class="form-control" name="price_impact[]">
            
                                                                                                                            <option {{$key1->price_impact == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                                                                                            <option {{$key1->price_impact == 1 ? 'selected' : null}} value="1">{{__('text.Fixed')}}</option>
                                                                                                                            <option {{$key1->variable == 1 ? 'selected' : null}} value="2">{{__('text.m¹ Impact')}}</option>
                                                                                                                            <option {{$key1->m2_impact == 1 ? 'selected' : null}} value="3">{{__('text.m² Impact')}}</option>
                                                                                                                            <option {{$key1->factor == 1 ? 'selected' : null}} value="4">{{__('text.Factor')}}</option>
            
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <select style="padding: 5px;" class="form-control" name="impact_type[]">
            
                                                                                                                            <option {{$key1->impact_type == 0 ? 'selected' : null}} value="0">€</option>
                                                                                                                            <option {{$key1->impact_type == 1 ? 'selected' : null}} value="1">%</option>
            
                                                                                                                        </select>
                                                                                                                    </td>
                                                                                                                    <td>
                                                                                                                        <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="{{$key1->id}}" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>
                                                                                                                    </td>
                                                                                                                </tr>
            
                                                                                                            @endif
            
                                                                                                        @endforeach

                                                                                                    @endif
    
                                                                                                </tbody>
                                                                                            </table>

                                                                                            <div style="margin-top: 20px;" class="col-sm-12 text-center">
                                                                                                <button data-id="{{$h+1}}" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>
                                                                                            </div>
                                                                                        
                                                                                        </div>

                                                                                    @endif

                                                                                @endforeach

                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                            
                                                            <div id="myModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                <div style="width: 70%;" class="modal-dialog">

                                                                    <div class="modal-content">

                                                                        <div class="modal-header">
                                                                            <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                            <h3 id="myModalLabel">{{__("text.Sub Features")}}</h3>
                                                                        </div>

                                                                        <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;padding: 30px 10px;">

                                                                            <div id="sub-features">

                                                                                <?php $s1 = 1; ?>

                                                                                @if(isset($sub_features_data) && count($features_data) > 0)

                                                                                    @foreach($features_data as $s => $key)

                                                                                        <div data-id="{{$s+1}}" class="sub-feature-table-container">
    
                                                                                            <table style="margin: auto;width: 95%;border-collapse: separate;">
                                                                                                <thead>
                                                                                                <tr>
                                                                                                    <th>{{__('text.Feature')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Value')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Factor')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Price Impact')}}</th>
                                                                                                    <th style="width: 15%;">{{__('text.Impact Type')}}</th>
                                                                                                    <th>{{__('text.Remove')}}</th>
                                                                                                </tr>
                                                                                                </thead>
    
                                                                                                <tbody>
    
                                                                                                @if($sub_features_data->contains('main_id',$key->id))
    
                                                                                                    @foreach($sub_features_data as $key1)
    
                                                                                                        @if($key->id == $key1->main_id)
    
                                                                                                            <tr data-id="{{$s1}}">
                                                                                                                <td>
                                                                                                                    <input type="hidden" name="f_rows{{$s+1}}[]" class="f_row1" value="{{$s1}}">
                                                                                                                    <select class="feature_title1" name="features{{$s+1}}[]">
                                                                                                                        <option value="">{{__("text.Feature Title")}}</option>
                                                                                                                        @foreach($sub_feature_values[$s] as $value)
                                                                                                                            <option {{$key1->feature_value_id == $value->id ? "selected" : ""}} value="{{$value->id}}">{{$value->title}}</option>
                                                                                                                        @endforeach
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input value="{{$key1->value}}" class="form-control feature_value1" name="feature_values{{$s+1}}[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <input value="{{$key1->factor_value}}" class="form-control factor_value1" name="factor_values{{$s+1}}[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select class="form-control" name="price_impact{{$s+1}}[]">
    
                                                                                                                        <option {{$key1->price_impact == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                                                                                        <option {{$key1->price_impact == 1 ? 'selected' : null}} value="1">{{__('text.Fixed')}}</option>
                                                                                                                        <option {{$key1->variable == 1 ? 'selected' : null}} value="2">{{__('text.m¹ Impact')}}</option>
                                                                                                                        <option {{$key1->m2_impact == 1 ? 'selected' : null}} value="3">{{__('text.m² Impact')}}</option>
                                                                                                                        <option {{$key1->factor == 1 ? 'selected' : null}} value="4">{{__('text.Factor')}}</option>
    
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <select class="form-control" name="impact_type{{$s+1}}[]">
    
                                                                                                                        <option {{$key1->impact_type == 0 ? 'selected' : null}} value="0">€</option>
                                                                                                                        <option {{$key1->impact_type == 1 ? 'selected' : null}} value="1">%</option>
    
                                                                                                                    </select>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="{{$key1->id}}" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>
                                                                                                                </td>
                                                                                                            </tr>
    
                                                                                                            <?php $s1 = $s1 + 1; ?>
    
                                                                                                        @endif
    
                                                                                                    @endforeach
    
                                                                                                @else
    
                                                                                                    <tr data-id="{{$s1}}">
                                                                                                        <td>
                                                                                                            <input type="hidden" name="f_rows{{$s+1}}[]" class="f_row1" value="{{$s1}}">
                                                                                                            <select class="feature_title1" name="features{{$s+1}}[]">
                                                                                                                <option value="">{{__("text.Feature Title")}}</option>
                                                                                                                @foreach($sub_feature_values[$s] as $value)
                                                                                                                    <option value="{{$value->id}}">{{$value->title}}</option>
                                                                                                                @endforeach
                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input value="" class="form-control feature_value1" name="feature_values{{$s+1}}[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input value="" class="form-control factor_value1" name="factor_values{{$s+1}}[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <select class="form-control" name="price_impact{{$s+1}}[]">
    
                                                                                                                <option value="0">{{__('text.No')}}</option>
                                                                                                                <option value="1">{{__('text.Fixed')}}</option>
                                                                                                                <option value="2">{{__('text.m¹ Impact')}}</option>
                                                                                                                <option value="3">{{__('text.m² Impact')}}</option>
                                                                                                                <option value="4">{{__('text.Factor')}}</option>
    
                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <select class="form-control" name="impact_type{{$s+1}}[]">
    
                                                                                                                <option value="0">€</option>
                                                                                                                <option value="1">%</option>
    
                                                                                                            </select>
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>
                                                                                                        </td>
                                                                                                    </tr>
    
                                                                                                    <?php $s1 = $s1 + 1; ?>
    
                                                                                                @endif
    
                                                                                                </tbody>
                                                                                            </table>
    
                                                                                            <div style="margin-top: 20px;" class="col-sm-12 text-center">
                                                                                                <button data-id="{{$s+1}}" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>
                                                                                            </div>
                                                                                        </div>
    
                                                                                    @endforeach

                                                                                @endif

                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="form-group add-color">
                                                            <label class="control-label col-sm-3" for=""></label>

                                                            <div class="col-sm-12 text-center">
                                                                <button class="btn btn-default featured-btn" type="button" id="add-feature-btn"><i class="fa fa-plus"></i> Add More Headings</button>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div id="menu6" class="tab-pane fade">

                                                        <div class="row" style="margin: 0;">

                                                            <div class="form-group">

                                                                <div class="row" style="margin: 0;">

                                                                    <div style="margin: 10px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <label class="container1">Price based on price table
                                                                            <input type="radio" name="price_based_option" value="1" {{isset($cats) ? ($cats->price_based_option == 1 ? 'checked' : null) : 'checked'}}>
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                    </div>

                                                                    <div style="margin: 10px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <label class="container1">Price based on width
                                                                            <input type="radio" name="price_based_option" value="2" {{isset($cats) ? ($cats->price_based_option == 2 ? 'checked' : null) : null}}>
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                    </div>

                                                                    <div style="margin: 10px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <label class="container1">Price based on height
                                                                            <input type="radio" name="price_based_option" value="3" {{isset($cats) ? ($cats->price_based_option == 3 ? 'checked' : null) : null}}>
                                                                            <span class="checkmark"></span>
                                                                        </label>
                                                                    </div>

                                                                    <div style="margin: 10px 0;display: flex;align-items: center;justify-content: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;" class="control-label">Base Price:</label>
                                                                        <input style="width: auto;border-radius: 10px;" class="form-control base_price" value="{{isset($cats) ? $cats->base_price : 0}}" name="base_price" id="blood_group_slug" placeholder="Base Price" type="number">

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div id="menu7" class="tab-pane fade">

                                                        <div class="form-group" style="margin-bottom: 20px;">

                                                            <div class="row" style="margin: 0;">

                                                                <div style="font-family: monospace;" class="col-sm-3">
                                                                    <h4>{{__("text.Model")}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__("text.Value")}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__("text.Factor")}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__("text.Price Impact")}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;" class="col-sm-2">
                                                                    <h4>{{__("text.Impact Type")}}</h4>
                                                                </div>

                                                                <div style="font-family: monospace;text-align: center;" class="col-sm-1">
                                                                    <h4>{{__("text.Action")}}</h4>
                                                                </div>

                                                            </div>

                                                            <div class="row model_box" style="margin: 15px 0;">

                                                                <input type="hidden" name="removed1" id="removed_rows1">

                                                                @if(isset($models) && count($models) > 0)

                                                                    @foreach($models as $m => $key)

                                                                        <div data-id="{{$m+1}}" class="form-group model-row" style="margin: 0 0 20px 0;">

                                                                            <div class="col-sm-3 autocomplete">

                                                                                <input type="hidden" value="{{$key->size_id}}" name="size_ids[]" class="form-control size_ids">
                                                                                <input {{$key->size_id ? "readonly" : ""}} type="text" id="modelInput" autocomplete="off" value="{{$key->size_title ? $key->size_title : $key->model}}" placeholder="Model" name="models[]" class="form-control validate models">

                                                                            </div>

                                                                            <div class="col-sm-2">

                                                                                <input value="{{str_replace(".",",",$key->value)}}" maskedformat="9,1" class="form-control model_value" name="model_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">

                                                                            </div>

                                                                            <div class="col-sm-2">

                                                                                <input value="{{str_replace(".",",",$key->factor_value)}}" maskedformat="9,1" class="form-control model_factor_value" name="model_factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">

                                                                            </div>

                                                                            <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">

                                                                                <select class="form-control" id="price_impact" name="model_price_impact[]">
                                                                                    <option {{$key->price_impact == 0 ? 'selected' : null}} value="0">{{__('text.No')}}</option>
                                                                                    <option {{$key->price_impact == 1 ? 'selected' : null}} value="1">{{__('text.Fixed')}}</option>
                                                                                    <option {{$key->m1_impact == 1 ? 'selected' : null}} value="2">{{__('text.m¹ Impact')}}</option>
                                                                                    <option {{$key->m2_impact == 1 ? 'selected' : null}} value="3">{{__('text.m² Impact')}}</option>
                                                                                    <option {{$key->factor == 1 ? 'selected' : null}} value="4">{{__('text.Factor')}}</option>
                                                                                </select>

                                                                            </div>

                                                                            <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">

                                                                                <select class="form-control" id="impact_type" name="model_impact_type[]">
                                                                                    <option {{$key->impact_type == 0 ? 'selected' : null}} value="0">€</option>
                                                                                    <option {{$key->impact_type == 1 ? 'selected' : null}} value="1">%</option>
                                                                                </select>

                                                                            </div>

                                                                            <div style="display: flex;justify-content: center;" class="col-sm-1">

                                                                                {{--<button data-id="{{$m+1}}" style="margin-right: 10px;" class="btn btn-success select-feature-btn" type="button">Select Features</button>--}}
                                                                                <span class="ui-close select-feature-btn" data-id="{{$m+1}}" style="margin: 0;position: relative;right: auto;top: 0;background-color: #5cb85c;font-size: 22px;">+</span>
                                                                                <span class="ui-close remove-model" data-id="{{$key->id}}" style="margin: 0;position: relative;right: -5px;top: 0;">X</span>

                                                                            </div>

                                                                        </div>

                                                                    @endforeach

                                                                @else

                                                                    <div data-id="1" class="form-group model-row" style="margin: 0 0 20px 0;">

                                                                        <div class="col-sm-3 autocomplete">

                                                                            <input type="hidden" name="size_ids[]" class="form-control size_ids">
                                                                            <input type="text" id="modelInput" autocomplete="off" placeholder="Model" name="models[]" class="form-control validate models">

                                                                        </div>

                                                                        <div class="col-sm-2">

                                                                            <input class="form-control model_value" maskedformat="9,1" name="model_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">

                                                                        </div>

                                                                        <div class="col-sm-2">

                                                                            <input class="form-control model_factor_value" maskedformat="9,1" name="model_factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">

                                                                        </div>

                                                                        <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">

                                                                            <select class="form-control" id="price_impact" name="model_price_impact[]">
                                                                                <option value="0">{{__('text.No')}}</option>
                                                                                <option value="1">{{__('text.Fixed')}}</option>
                                                                                <option value="2">{{__('text.m¹ Impact')}}</option>
                                                                                <option value="3">{{__('text.m² Impact')}}</option>
                                                                                <option value="4">{{__('text.Factor')}}</option>
                                                                            </select>

                                                                        </div>

                                                                        <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">

                                                                            <select class="form-control" id="impact_type" name="model_impact_type[]">
                                                                                <option value="0">€</option>
                                                                                <option value="1">%</option>
                                                                            </select>

                                                                        </div>

                                                                        <div style="display: flex;justify-content: center;" class="col-sm-1">

                                                                            {{--<button data-id="1" style="margin-right: 10px;" class="btn btn-success select-feature-btn" type="button">Select Features</button>--}}
                                                                            <span class="ui-close select-feature-btn" data-id="1" style="margin: 0;position: relative;right: auto;top: 0;background-color: #5cb85c;font-size: 22px;">+</span>
                                                                            <span class="ui-close remove-model" data-id="" style="margin: 0;position: relative;right: -5px;top: 0;">X</span>

                                                                        </div>

                                                                    </div>

                                                                @endif

                                                            </div>

                                                            @if(isset($models) && count($models) > 0)

                                                                <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">

                                                                        <div class="modal-content">

                                                                            <div class="modal-header">
                                                                                <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                <h3 id="myModalLabel">{{__('text.Model features')}}</h3>
                                                                            </div>

                                                                            <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;">

                                                                                <div id="models-features-tables">

                                                                                    <div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                            
                                                                                        <div class="card">
                                                                                                
                                                                                            <div class="card-header">
                                                                                                <ul class="nav nav-tabs nav-tabs-neutral justify-content-center" role="tablist" data-background-color="orange">
                                                                                                    <li class="nav-item active">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#general1" role="tab" aria-selected="false">{{__('text.General')}}</a>
                                                                                                    </li>

                                                                                                    <li class="nav-item">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#features1" role="tab" aria-selected="false">{{__('text.Features')}}</a>
                                                                                                    </li>
                                                                                                        
                                                                                                    <li class="nav-item">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#curtains1" role="tab" aria-selected="false">{{__('text.Curtains')}}</a>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                                
                                                                                            <div class="card-body">
                                                                                                <!-- Tab panes -->
                                                                                                <div class="tab-content text-center">
                                                                                                    <div class="tab-pane active" id="general1" role="tabpanel">

                                                                                                        @foreach($models as $s => $mod)
                                                                                                        
                                                                                                            <div style="margin-left: 0;margin-right: 0;" data-id="{{$s+1}}" class="form-group model-childsafe">

                                                                                                                <div class="row" style="margin: auto;width: 70%;">
                                                                                                                    
                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;color: red;width: 40%;" class="control-label">{{__('text.Childsafe')}}:</label>
                                                                                                                        <input type="hidden" name="childsafe[]" id="childsafe" value="{{$mod->childsafe ? 1 : 0}}">
                                                                                                                        
                                                                                                                        <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.No')}}</span>
                                                                                                                        
                                                                                                                        <label style="margin: 0;" class="switch">
                                                                                                                            <input class="childsafe" type="checkbox" {{$mod->childsafe ? 'checked' : null}}>
                                                                                                                            <span class="slider round"></span>
                                                                                                                        </label>
                                                                                                                        
                                                                                                                        <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.Yes')}}</span>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max m²')}}:</label>
                                                                                                                        <input value="{{str_replace(".",",",$mod->max_size)}}" class="form-control model_max_size" name="model_max_size[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max m²')}}" type="text">
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max Width')}}:</label>
                                                                                                                        <input value="{{str_replace(".",",",$mod->max_width)}}" class="form-control model_max_width" name="model_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Width Placeholder')}}" type="text">
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max Height')}}:</label>
                                                                                                                        <input value="{{str_replace(".",",",$mod->max_height)}}" class="form-control model_max_height" name="model_max_height[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Height Placeholder')}}" type="text">
                                                                                                                    </div>
                                                                                                                    
                                                                                                                </div>
                                                                                                                
                                                                                                            </div>

                                                                                                        @endforeach
                                                                                                        
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="tab-pane" id="features1" role="tabpanel">

                                                                                                        @foreach($models as $s => $mod)

                                                                                                            <table data-id="{{$s+1}}" style="margin: auto;width: 70%;border-collapse: separate;">
                                                                                                            
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th></th>
                                                                                                                        <th>{{__('text.Heading')}}</th>
                                                                                                                        <th>{{__('text.Feature')}}</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                
                                                                                                                <tbody>
                                                                                                                    
                                                                                                                    @foreach($mod->features as $x => $feature)
                                                                                                                    
                                                                                                                        <tr data-id="{{$x+1}}">
                                                                                                                            <td>
                                                                                                                                <div style="display: flex;justify-content: center;align-items: center;">
                                                                                                                                    <input type="hidden" name="selected_model_feature{{$x+1}}[]" id="price_impact" value="{{$feature->linked}}">
                                                                                                                                    <label style="margin: 0;" class="switch">
                                                                                                                                        <input class="price_impact" type="checkbox" {{$feature->linked ? 'checked' : null}}>
                                                                                                                                        <span class="slider round"></span>
                                                                                                                                    </label>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                            <td>{{$feature->heading}}</td>
                                                                                                                            <td>{{$feature->feature_title}}</td>
                                                                                                                        </tr>
                                                                                                                        
                                                                                                                    @endforeach
                                                                                                                
                                                                                                                </tbody>
                                                                                                            
                                                                                                            </table>

                                                                                                        @endforeach

                                                                                                    </div>
                                                                                                        
                                                                                                    <div class="tab-pane" id="curtains1" role="tabpanel">
                                                                                                        
                                                                                                        @foreach($models as $s => $mod)
                                                                                                        
                                                                                                            <div style="margin-left: 0;margin-right: 0;" data-id="{{$s+1}}" class="form-group model-curtain">
                                                                                                                <input type="hidden" name="row_curtain_id[]" value="{{$s+1}}">

                                                                                                                <div class="row" style="margin: auto;width: 100%;">

                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Curtain Type')}}:</label>
                                                                                                                        <select style="border-radius: 5px;" class="form-control curtain_type" name="curtain_type[]">
                                                                                                                            <option {{$mod->curtain_type == 1 ? "selected" : ""}} value="1">Kamerhoog</option>
                                                                                                                            <option {{$mod->curtain_type == 2 ? "selected" : ""}} value="2">{{__('text.Max Width')}}</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    
                                                                                                                    <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;margin-bottom: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                        <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Factor Max Width')}}:</label>
                                                                                                                        <input value="{{$mod->factor_max_width ? str_replace(".",",",$mod->factor_max_width) : 0}}" {{$mod->curtain_type == 1 ? "readonly" : ""}} class="form-control model_factor_max_width" name="model_factor_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Factor Max Width Placeholder')}}" type="text">
                                                                                                                    </div>

                                                                                                                    <table style="width: 100%;border-collapse: separate;display: table;">
                                                                                                                
                                                                                                                        <thead>
                                                                                                                            <tr>
                                                                                                                                <th>{{__('text.Option')}}</th>
                                                                                                                                <th>{{__('text.Description')}}</th>
                                                                                                                                <th>{{__('text.Value')}}</th>
                                                                                                                                <th></th>
                                                                                                                            </tr>
                                                                                                                        </thead>
                                                                                                                
                                                                                                                        <tbody>

                                                                                                                            @if(count($mod->curtain_variables) > 0)
                                                                                                                            
                                                                                                                                @foreach($mod->curtain_variables as $cv => $curt)
                                                                                                                                
                                                                                                                                    <tr>
                                                                                                                                        <td>
                                                                                                                                            <div class="curtain_option_box" style="display: flex;align-items: center;">
                                                                                                                                                <input type="hidden" name="curtain_variable_options{{$s+1}}[]" id="curtain_variable_option" value="{{$curt->enabled}}">
                                                                                                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.Off')}}</span>
                                                                                                                                                <label style="margin: 0;" class="switch">
                                                                                                                                                    <input {{$curt->enabled ? "checked" : ""}} class="curtain_variable_option" type="checkbox">
                                                                                                                                                    <span class="slider round"></span>
                                                                                                                                                </label>
                                                                                                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.On')}}</span>
                                                                                                                                            </div>
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            <input value="{{$curt->description}}" type="text" name="curtain_variable_descriptions{{$s+1}}[]" class="form-control">
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            <input value="{{str_replace(".",",",$curt->value)}}" type="text" maskedformat="9,1" name="curtain_variable_values{{$s+1}}[]" class="form-control curtain_variable_values">
                                                                                                                                        </td>
                                                                                                                                        <td>
                                                                                                                                            <div style="display: flex;">
                                                                                                                                                <span class="add-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">
                                                                                                                                                    <i style="width: 100%;" class="fa fa-fw fa-plus"></i>
                                                                                                                                                </span>
                                                                                                                                                <span class="remove-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;margin-left: 5px;">
                                                                                                                                                    <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                                                                                                </span>
                                                                                                                                            </div>
                                                                                                                                        </td>
                                                                                                                                    </tr>

                                                                                                                                @endforeach

                                                                                                                            @else

                                                                                                                                <tr>
                                                                                                                                    <td>
                                                                                                                                        <div class="curtain_option_box" style="display: flex;align-items: center;">
                                                                                                                                            <input type="hidden" name="curtain_variable_options{{$s+1}}[]" id="curtain_variable_option" value="0">
                                                                                                                                            <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.Off')}}</span>
                                                                                                                                            <label style="margin: 0;" class="switch">
                                                                                                                                                <input class="curtain_variable_option" type="checkbox">
                                                                                                                                                <span class="slider round"></span>
                                                                                                                                            </label>
                                                                                                                                            <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.On')}}</span>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <input type="text" name="curtain_variable_descriptions{{$s+1}}[]" class="form-control">
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <input type="text" maskedformat="9,1" name="curtain_variable_values{{$s+1}}[]" class="form-control curtain_variable_values">
                                                                                                                                    </td>
                                                                                                                                    <td>
                                                                                                                                        <div style="display: flex;">
                                                                                                                                            <span class="add-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">
                                                                                                                                                <i style="width: 100%;" class="fa fa-fw fa-plus"></i>
                                                                                                                                            </span>
                                                                                                                                            <span class="remove-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;margin-left: 5px;">
                                                                                                                                                <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                                                                                            </span>
                                                                                                                                        </div>
                                                                                                                                    </td>
                                                                                                                                </tr>

                                                                                                                            @endif
                                                                                                                            
                                                                                                                        </tbody>
                                                                                                            
                                                                                                                    </table>
                                                                                                                    
                                                                                                                </div>
                                                                                                                
                                                                                                            </div>

                                                                                                        @endforeach
                                                                                                        
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- End Tabs on plain Card -->
                                                                                    </div>

                                                                                </div>

                                                                                <div style="text-align: center;margin: 20px 0;display: inline-block;width: 100%;">
                                                                                    <button class="btn btn-success" type="button" data-dismiss="modal" aria-hidden="true" style="padding: 5px 25px;font-size: 16px;">{{__('text.Save')}}</button>
                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            @else

                                                                <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog">

                                                                        <div class="modal-content">

                                                                            <div class="modal-header">
                                                                                <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                                <h3 id="myModalLabel">{{__('text.Model features')}}</h3>
                                                                            </div>

                                                                            <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;">

                                                                                <div id="models-features-tables">

                                                                                    <div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                            
                                                                                        <div class="card">
                                                                                                
                                                                                            <div class="card-header">
                                                                                                <ul class="nav nav-tabs nav-tabs-neutral justify-content-center" role="tablist" data-background-color="orange">
                                                                                                    <li class="nav-item active">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#general1" role="tab" aria-selected="false">{{__('text.General')}}</a>
                                                                                                    </li>

                                                                                                    <li class="nav-item">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#features1" role="tab" aria-selected="false">{{__('text.Features')}}</a>
                                                                                                    </li>
                                                                                                        
                                                                                                    <li class="nav-item">
                                                                                                        <a class="nav-link" data-toggle="tab" href="#curtains1" role="tab" aria-selected="false">{{__('text.Curtains')}}</a>
                                                                                                    </li>
                                                                                                </ul>
                                                                                            </div>
                                                                                                
                                                                                            <div class="card-body">
                                                                                                <!-- Tab panes -->
                                                                                                <div class="tab-content text-center">
                                                                                                    <div class="tab-pane active" id="general1" role="tabpanel">
                                                                                                        <div style="margin-left: 0;margin-right: 0;" data-id="1" class="form-group model-childsafe">

                                                                                                            <div class="row" style="margin: auto;width: 70%;">
                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;color: red;width: 40%;" class="control-label">{{__('text.Childsafe')}}:</label>
                                                                                                                    <input type="hidden" name="childsafe[]" id="childsafe" value="0">
                                                                                                                    <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.No')}}</span>
                                                                                                                    <label style="margin: 0;" class="switch">
                                                                                                                        <input class="childsafe" type="checkbox">
                                                                                                                        <span class="slider round"></span>
                                                                                                                    </label>
                                                                                                                    <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.Yes')}}</span>
                                                                                                                </div>
                                                                                                                    
                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max m²')}}:</label>
                                                                                                                    <input class="form-control model_max_size" name="model_max_size[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max m²')}}" type="text">
                                                                                                                </div>
                                                                                                                    
                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max Width')}}:</label>
                                                                                                                    <input class="form-control model_max_width" name="model_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Width Placeholder')}}" type="text">
                                                                                                                </div>
                                                                                                                    
                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Max Height')}}:</label>
                                                                                                                    <input class="form-control model_max_height" name="model_max_height[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Height Placeholder')}}" type="text">
                                                                                                                </div>
                                                                                                            </div>
                                                                                                                
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="tab-pane" id="features1" role="tabpanel">
                                                                                                        <table data-id="1" style="margin: auto;width: 70%;border-collapse: separate;">
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <th></th>
                                                                                                                    <th>{{__('text.Heading')}}</th>
                                                                                                                    <th>{{__('text.Feature')}}</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                                
                                                                                                            <tbody>
                                                                                                                
                                                                                                                @if(isset($features_data) && count($features_data) > 0)

                                                                                                                    @foreach($features_data as $x => $feature)

                                                                                                                        <tr data-id="{{$x+1}}">
                                                                                                                            <td>
                                                                                                                                <div style="display: flex;justify-content: center;align-items: center;">
                                                                                                                                    <input type="hidden" name="selected_model_feature{{$x+1}}[]" id="price_impact" value="0">
                                                                                                                                    <label style="margin: 0;" class="switch">
                                                                                                                                        <input class="price_impact" type="checkbox">
                                                                                                                                        <span class="slider round"></span>
                                                                                                                                    </label>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                            <td>
                                                                                                                                @foreach($features_headings as $heading)
                                                                                                                                    
                                                                                                                                    @if($heading->id == $feature->heading_id)
                                                                                                                                    
                                                                                                                                        {{$heading->title}}
                                                                                                                                        
                                                                                                                                    @endif
                                                                                                                                    
                                                                                                                                @endforeach
                                                                                                                            </td>
                                                                                                                            <td>{{$feature->title}}</td>
                                                                                                                        </tr>
                                                                                                                        
                                                                                                                    @endforeach

                                                                                                                @endif

                                                                                                            </tbody>
                                                                                                            
                                                                                                        </table>
                                                                                                    </div>
                                                                                                        
                                                                                                    <div class="tab-pane" id="curtains1" role="tabpanel">
                                                                                                        <div style="margin-left: 0;margin-right: 0;" data-id="1" class="form-group model-curtain">
                                                                                                            <input type="hidden" name="row_curtain_id[]" value="1">

                                                                                                            <div class="row" style="margin: auto;width: 100%;">

                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Curtain Type')}}:</label>
                                                                                                                    <select style="border-radius: 5px;" class="form-control curtain_type" name="curtain_type[]">
                                                                                                                        <option value="1">Kamerhoog</option>
                                                                                                                        <option value="2">{{__('text.Max Width')}}</option>
                                                                                                                    </select>
                                                                                                                </div>
                                                                                                                    
                                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;margin-bottom: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Factor Max Width')}}:</label>
                                                                                                                    <input value="0" readonly class="form-control model_factor_max_width" name="model_factor_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Factor Max Width Placeholder')}}" type="text">
                                                                                                                </div>

                                                                                                                <table style="width: 100%;border-collapse: separate;display: table;">
                                                                                                                
                                                                                                                    <thead>
                                                                                                                        <tr>
                                                                                                                            <th>{{__('text.Option')}}</th>
                                                                                                                            <th>{{__('text.Description')}}</th>
                                                                                                                            <th>{{__('text.Value')}}</th>
                                                                                                                            <th></th>
                                                                                                                        </tr>
                                                                                                                    </thead>
                                                                                                                
                                                                                                                    <tbody>
                                                                                                                        <tr>
                                                                                                                            <td>
                                                                                                                                <div class="curtain_option_box" style="display: flex;align-items: center;">
                                                                                                                                    <input type="hidden" name="curtain_variable_options1[]" id="curtain_variable_option" value="0">
                                                                                                                                    <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.Off')}}</span>
                                                                                                                                    <label style="margin: 0;" class="switch">
                                                                                                                                        <input class="curtain_variable_option" type="checkbox">
                                                                                                                                        <span class="slider round"></span>
                                                                                                                                    </label>
                                                                                                                                    <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.On')}}</span>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                            <td>
                                                                                                                                <input type="text" name="curtain_variable_descriptions1[]" class="form-control">
                                                                                                                            </td>
                                                                                                                            <td>
                                                                                                                                <input type="text" maskedformat="9,1" name="curtain_variable_values1[]" class="form-control curtain_variable_values">
                                                                                                                            </td>
                                                                                                                            <td>
                                                                                                                                <div style="display: flex;">
                                                                                                                                    <span class="add-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">
                                                                                                                                        <i style="width: 100%;" class="fa fa-fw fa-plus"></i>
                                                                                                                                    </span>
                                                                                                                                    <span class="remove-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;margin-left: 5px;">
                                                                                                                                        <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>
                                                                                                                                    </span>
                                                                                                                                </div>
                                                                                                                            </td>
                                                                                                                        </tr>
                                                                                                                    </tbody>
                                                                                                            
                                                                                                                </table>
                                                                                                                    
                                                                                                            </div>
                                                                                                                
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- End Tabs on plain Card -->
                                                                                    </div>

                                                                                </div>

                                                                                <div style="text-align: center;margin: 20px 0;display: inline-block;width: 100%;">
                                                                                    <button class="btn btn-success" type="button" data-dismiss="modal" aria-hidden="true" style="padding: 5px 25px;font-size: 16px;">{{__('text.Save')}}</button>
                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            @endif

                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-sm-3" for=""></label>

                                                            <div class="col-sm-12 text-center">
                                                                <button class="btn btn-default featured-btn" type="button" id="add-model-btn"><i class="fa fa-plus"></i> {{__('text.Add More Models')}}</button>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <hr style="margin: 30px 0;">

                                                    <div style="padding: 0;" class="add-product-footer">
                                                        <button name="addProduct_btn" type="button" class="btn add-product_btn">{{isset($cats) ?  __('text.Edit Product') :  __('text.Add Product')}}</button>
                                                    </div>

                                                </div>

                                            </form>
                                        </div>

                                    </div>
                                </div>
                        </div>
                    </div>
                    <!-- Ending of Dashboard area -->
                </div>
            </div>
        </div>
    </div>

    <div id="myModal3" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div style="width: 70%;" class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <button style="background-color: white !important;color: black !important;" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel">{{__("text.Model Sizes")}}</h3>
                </div>

                <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;padding: 30px 10px;">

                    <div id="model-sizes">

                        <table style="margin: auto;width: 95%;border-collapse: separate;">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{__("text.Title")}}</th>
                                <th>{{__("text.Value")}}</th>
                                <th>{{__("text.Factor")}}</th>
                                <th>{{__("text.Price Impact")}}</th>
                                <th>{{__("text.Impact Type")}}</th>
                            </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>

                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-default" type="button" data-dismiss="modal" aria-hidden="true" style="padding: 5px 25px;font-size: 16px;">Close</button>

                </div>

            </div>

        </div>
    </div>

    <style>

        #menu7 .switch
        {
            width: 75px;
        }

        #menu7 input:checked + .slider:before
        {
            -webkit-transform: translateX(50px);
            transform: translateX(50px);
        }

        #myModal .modal-body
        {
            padding: 0;
        }

        .nav-tabs-neutral>li>a
        {
            border: none !important;
        }

        .nav-item .nav-link,
        .nav-tabs .nav-link {
            -webkit-transition: all 300ms ease 0s;
            -moz-transition: all 300ms ease 0s;
            -o-transition: all 300ms ease 0s;
            -ms-transition: all 300ms ease 0s;
            transition: all 300ms ease 0s;
        }

        .card-body
        {
            padding: 20px;
        }
        
        .card a {
            -webkit-transition: all 150ms ease 0s;
            -moz-transition: all 150ms ease 0s;
            -o-transition: all 150ms ease 0s;
            -ms-transition: all 150ms ease 0s;
            transition: all 150ms ease 0s;
        }
        
        .nav-tabs:not(.nav-tabs-neutral)>.nav-item.active>.nav-link {
            box-shadow: 0px 5px 35px 0px rgba(0, 0, 0, 0.3);
        }
        
        .card .nav-tabs {
            padding: 15px 0.7rem;
            border-top-right-radius: 0.1875rem;
            border-top-left-radius: 0.1875rem;
        }
        
        .nav-tabs>.nav-item>.nav-link {
            color: #888888;
            margin: 0;
            margin-right: 5px;
            background-color: transparent;
            border: 1px solid transparent;
            border-radius: 30px;
            font-size: 14px;
            padding: 11px 23px;
            line-height: 1.5;
        }

        .nav-tabs>.nav-item>.nav-link:hover {
            background-color: transparent;
        }

        .nav-tabs>.nav-item.active>.nav-link {
            background-color: #444;
            border-radius: 30px;
            color: #FFFFFF;
        }
        
        .nav-tabs.nav-tabs-neutral>.nav-item>.nav-link {
            color: #FFFFFF;
        }
        
        .nav-tabs.nav-tabs-neutral>.nav-item.active>.nav-link {
            background-color: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }
        
        .card {
            border: 0;
            border-radius: 0.1875rem;
            display: inline-block;
            position: relative;
            width: 100%;
            margin-bottom: 0;
            border-bottom: 1px solid #eeeeee;
            /* box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2); */
        }
        
        .card .card-header {
            background-color: transparent;
            border-bottom: 0;
            background-color: transparent;
            border-radius: 0;
            padding: 0;
        }

        .card[data-background-color="orange"] {
            background-color: #f96332;
        }
        
        .card[data-background-color="red"] {
            background-color: #FF3636;
        }

        .card[data-background-color="yellow"] {
            background-color: #FFB236;
        }

        .card[data-background-color="blue"] {
            background-color: #2CA8FF;
        }

        .card[data-background-color="green"] {
            background-color: #15b60d;
        }
        
        [data-background-color="orange"] {
            background-color: #e95e38;
        }

        [data-background-color="black"] {
            background-color: #2c2c2c;
        }

        [data-background-color]:not([data-background-color="gray"]) {
            color: #FFFFFF;
        }

        [data-background-color]:not([data-background-color="gray"]) p {
            color: #FFFFFF;
        }

        [data-background-color]:not([data-background-color="gray"]) a:not(.btn):not(.dropdown-item) {
            color: #FFFFFF;
        }

        [data-background-color]:not([data-background-color="gray"]) .nav-tabs>.nav-item>.nav-link i.now-ui-icons {
            color: #FFFFFF;
        }

    </style>

@endsection

@section('scripts')

<script type="text/javascript">

    window.onbeforeunload = function (e) {

        if($('#submit_check').val() == 0)
        {
            e = e || window.event;

            // For IE and Firefox prior to version 4
            if (e) {
                e.returnValue = 'Sure?';
            }
            // For Safari
            return 'Sure?';
        }
        else
        {
            // do nothing
        }

    };

    $(document).ready(function() {

        $('body').on('change', '.size-checkbox' ,function(){

            var size_id = $(this).val();
            var title = $(this).parents('tr').find('.size_title').text();
            var value = $(this).parents('tr').find('.size_value').text();
            var factor_value = $(this).parents('tr').find('.size_factor_value').text();
            var price_impact = $(this).parents('tr').find('.size_price_impact').text();

            if(price_impact == 'No')
            {
                price_impact = 0;
            }
            else if(price_impact == 'Fixed')
            {
                price_impact = 1;
            }
            else if(price_impact == 'm¹ Impact')
            {
                price_impact = 2;
            }
            else if(price_impact == 'm² Impact')
            {
                price_impact = 3;
            }
            else
            {
                price_impact = 4;
            }

            var impact_type = $(this).parents('tr').find('.size_impact_type').text();

            if(impact_type == '€')
            {
                impact_type = 0;
            }
            else
            {
                impact_type = 1;
            }

            if($(this).is(":checked"))
            {
                add_model(title,value,factor_value,price_impact,impact_type,size_id);
                var row_id = $(this).parents('tr').data('id');
                var remove_id = '';
                remove_model(remove_id,row_id);
            }
            else
            {
                $($('.model_box .model-row').get().reverse()).each(function(){

                    if($(this).find('.models').val() == title && $(this).find('.model_value').val() == value && $(this).find('.model_factor_value').val() == factor_value && $(this).find('#price_impact').val() == price_impact && $(this).find('#impact_type').val() == impact_type)
                    {
                        var remove_id = '';
                        var row_id = $(this).data('id');
                        remove_model(remove_id,row_id);
                        return false;
                    }

                });
            }

        });

        function autocomplete(inp, arr, values) {
            /*the autocomplete function takes two arguments,
            the text field element and an array of possible autocompleted values:*/
            var currentFocus;
            /*execute a function when someone writes in the text field:*/
            inp.addEventListener("input", function(e) {

                var current = $(this);
                var a, b, i, x, val = this.value;
                /*close any already open lists of autocompleted values*/
                closeAllLists();
                if (!val) { return false;}
                currentFocus = -1;
                /*create a DIV element that will contain the items (values):*/
                x = document.createElement("DIV");
                x.setAttribute("class", "autocomplete-con col-sm-12");
                x.style.padding = "0";
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                x.appendChild(a);
                this.parentNode.appendChild(x);

                var border_flag = 0;
                var found_flag = 0;

                if(arr.length == 0)
                {
                    border_flag = 1;
                }

                /*for each item in the array...*/
                for (i = 0; i < arr.length; i++) {

                    var string = arr[i];
                    string = string.toLowerCase();
                    val = val.toLowerCase();
                    var res = string.includes(val);

                    if (res) {

                        found_flag = 1;

                        /*create a DIV element for each matching element:*/
                        b = document.createElement("DIV");
                        /*make the matching letters bold:*/
                        /*b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                        b.innerHTML += arr[i].substr(val.length);*/
                        b.innerHTML = arr[i];
                        /*insert a input field that will hold the current array item's value:*/
                        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'><input type='hidden' value='" + values[i] + "'>";
                        /*execute a function when someone clicks on the item value (DIV element):*/
                        b.addEventListener("click", function(e) {

                            /*insert the value for the autocomplete text field:*/
                            inp.value = this.getElementsByTagName("input")[0].value;
                            var id = this.getElementsByTagName("input")[1].value;
                            var row_id = current.parents('.model-row').data('id');

                            $.ajax({
                                type:"GET",
                                data: "id=" + id,
                                url: "<?php echo url('/aanbieder/product/get-sizes-by-model')?>",
                                success: function(data) {

                                    $('#model-sizes table tbody tr').remove();

                                    $.each(data, function(index, value) {

                                        if(value.price_impact == 1)
                                        {
                                            var price_impact = "{{__('text.Fixed')}}";
                                        }
                                        else if(value.m1_impact == 1)
                                        {
                                            var price_impact = "{{__('text.m¹ Impact')}}";
                                        }
                                        else if(value.m2_impact == 1)
                                        {
                                            var price_impact = "{{__('text.m² Impact')}}";
                                        }
                                        else if(value.factor == 1)
                                        {
                                            var price_impact = "{{__('text.Factor')}}";
                                        }
                                        else
                                        {
                                            var price_impact = "{{__('text.No')}}";
                                        }

                                        if(value.impact_type == 0)
                                        {
                                            var impact_type = '€';
                                        }
                                        else
                                        {
                                            var impact_type = '%';
                                        }

                                        $('#model-sizes table').append('<tr data-id="'+row_id+'">\n' +
                                            '\n' +
                                            '                                                                            <td><input value="'+value.id+'" type="checkbox" class="size-checkbox"></td>\n' +
                                            '\n' +
                                            '                                                                            <td class="size_title">'+value.model+'</td>\n' +
                                            '\n' +
                                            '                                                                            <td class="size_value">'+value.value+'</td>\n' +
                                            '\n' +
                                            '                                                                            <td class="size_factor_value">'+value.factor_value+'</td>\n' +
                                            '\n' +
                                            '                                                                            <td class="size_price_impact">'+price_impact+'</td>\n' +
                                            '\n' +
                                            '                                                                            <td class="size_impact_type">'+impact_type+'</td>\n' +
                                            '\n' +
                                            '                                                                            </tr>');

                                    });

                                    $('#myModal3').modal('toggle');
                                    $('.modal-backdrop').hide();

                                }
                            });

                            /*close the list of autocompleted values,
                            (or any other open lists of autocompleted values:*/
                            closeAllLists();
                        });
                        a.appendChild(b);
                    }
                }

                if(border_flag || !found_flag)
                {
                    a.style.border = "0";
                }
            });
            /*execute a function presses a key on the keyboard:*/
            inp.addEventListener("keydown", function(e) {
                var x = document.getElementById(this.id + "autocomplete-list");
                if (x) x = x.getElementsByTagName("div");

                if (e.keyCode == 13 && e.shiftKey) {
                    
                    var el = this;
                    var val1 = el.value;
                    var selStart = el.selectionStart;
                    el.value = val1.slice(0, selStart) + "\n" + val1.slice(el.selectionEnd);
                    el.selectionEnd = el.selectionStart = selStart + "\n".length;
                
                }

                if (e.keyCode == 40) {
                    /*If the arrow DOWN key is pressed,
                    increase the currentFocus variable:*/
                    currentFocus++;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 38) { //up
                    /*If the arrow UP key is pressed,
                    decrease the currentFocus variable:*/
                    currentFocus--;
                    /*and and make the current item more visible:*/
                    addActive(x);
                } else if (e.keyCode == 13) {
                    /*If the ENTER key is pressed, prevent the form from being submitted,*/
                    e.preventDefault();
                    if (currentFocus > -1) {
                        /*and simulate a click on the "active" item:*/
                        if (x)
						{
							if(x[currentFocus] != undefined)
							{
								x[currentFocus].click();
							}
						}
                    }
                }
            });
            function addActive(x) {
                /*a function to classify an item as "active":*/
                if (!x) return false;
                /*start by removing the "active" class on all items:*/
                removeActive(x);
                if (currentFocus >= x.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = (x.length - 1);
                /*add class "autocomplete-active":*/
                if(x[currentFocus] != undefined)
				{
					x[currentFocus].classList.add("autocomplete-active");
				}
            }
            function removeActive(x) {
                /*a function to remove the "active" class from all autocomplete items:*/
                for (var i = 0; i < x.length; i++) {
                    x[i].classList.remove("autocomplete-active");
                }
            }
            function closeAllLists(elmnt) {
                /*close all autocomplete lists in the document,
                except the one passed as an argument:*/
                var x = document.getElementsByClassName("autocomplete-con");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            /*execute a function when someone clicks in the document:*/
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }

        values = [];
        model_titles = [];

        var sel = $(".all-models");
        var length = sel.children('option').length;

        $(".all-models:first > option").each(function() {
            if (this.value) values.push(this.value); model_titles.push(this.text);
        });

        var cls = document.getElementsByClassName("models");
        for (n=0, length = cls.length; n < length; n++) {
            autocomplete(cls[n], model_titles, values);
        }

        var rem_arr = [];
        var rem_col_arr = [];
        var rem_lad_arr = [];

        function fetch_features(data)
        {
            $('.feature_box').find(".feature-row").find('.remove-feature').each(function() {

                var id = $(this).data('id');
                var row_id = $(this).parent().parent().data('id');

                $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").find('table tbody tr').each(function (index) {

                    var row = $(this).find('.f_row').val();

                    if($(this).find('.remove-primary-feature').data('id'))
                    {
                        rem_arr.push($(this).find('.remove-primary-feature').data('id'));
                    }

                    $('#models-features-tables #features1 table tbody').find("[data-id='" + row + "']").remove();

                    $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").find('table tbody tr').each(function (index) {

                        if($(this).find('.remove-sub-feature').data('id'))
                        {
                            rem_arr.push($(this).find('.remove-sub-feature').data('id'));
                        }

                    });

                    $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").remove();

                });

                $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").remove();


                if(id)
                {
                    $('#removed_rows').val(rem_arr);
                }

                var parent = this.parentNode.parentNode;

                $(parent).hide();
                $(parent).remove();

            });

            if(data.length == 0)
            {
                $(".feature_box").append('<div data-id="1" class="form-group feature-row" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
                '\n' +
                '                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="1">\n' +
                '\n' +
                '                                                                            <div class="col-sm-5">\n' +
                '\n' +
                '                                                                            <select class="form-control validate js-data-example-ajax5">\n' +
                '\n' +
                '                                                                                <option value="">Select Feature Heading</option>\n' +
                '\n' +
                '                                                                                @foreach($features_headings as $feature)\n' +
                '\n' +
                '                                                                                    <option value="{{$feature->id}}">{{$feature->title}}</option>\n' +
                '\n' +
                '                                                                                @endforeach\n' +
                '\n' +
                '                                                                            </select>\n' +
                '\n' +
                '                                                                        </div>\n'+
                '\n' +
                '                                                                    <div style="display: flex;" class="col-sm-5">\n' +
                '\n' +
                '                                                                        <button data-id="1" style="margin-right: 10px;" class="btn btn-success create-feature-btn" type="button">{{__('text.Create/Edit Features')}}</button>\n' +
                '                                                                        <span class="ui-close remove-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;">X</span>\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                </div>');

                var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                $('#primary-features').append('<div data-id="1" class="feature-table-container">\n' +
                '\n' +
                '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                '                                                                                        <thead>\n' +
                '                                                                                        <tr>\n' +
                '                                                                                            <th>{{__("text.Feature")}}</th>\n' +
                '                                                                                            <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
                '                                                                                            <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
                '                                                                                            <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
                '                                                                                            <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                '                                                                                            <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                '                                                                                            <th>{{__("text.Remove")}}</th>\n' +
                '                                                                                        </tr>\n' +
                '                                                                                        </thead>\n' +
                '\n' +
                '                                                                                        <tbody>' +
                '                                                                                   <tr data-id="1">\n' +
                '\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="1">' +
                '                                                                                            <input type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <button style="width: 100%;white-space: normal;" data-id="1" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr></tbody></table>' +
                '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                '                                                                                        <button data-id="1" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
                '                                                                                    </div></div>');

                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features1[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                $('#sub-features').append('<div data-id="1" class="sub-feature-table-container">\n' +
                '\n' +
                '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                '                                                                                            <thead>\n' +
                '                                                                                            <tr>\n' +
                '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                '                                                                                            </tr>\n' +
                '                                                                                            </thead>\n' +
                '\n' +
                '                                                                                            <tbody>' +
                '                                                                                        <tr data-id="1">\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows1[]" class="f_row1" value="1">' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value1" name="feature_values1[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value1" name="factor_values1[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="price_impact1[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="impact_type1[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr></tbody></table>' +
                '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                '                                                                                            <button data-id="1" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                '                                                                                        </div></div>');

                $(".js-data-example-ajax5").select2({
                    width: '100%',
                    height: '200px',
                    placeholder: "{{__('text.Select Feature Heading Placeholder')}}",
                    allowClear: true,
                });

            }

            $.each(data, function(index, value) {

                if(index == 0)
                {
                    var heading_row = 1;
                }
                else
                {
                    var heading_row = $('.feature_box').find('.feature-row').last().data('id');
                    heading_row = heading_row + 1;
                }

                /*var features_options = '';

                for(var i=0; i<data.length; i++)
                {
                    if(i == index)
                    {
                        features_options = features_options + '<option selected value="'+data[i].id+'">'+data[i].title+'</option>';
                    }
                    else
                    {
                        features_options = features_options + '<option value="'+data[i].id+'">'+data[i].title+'</option>';
                    }
                }*/

                $(".feature_box").append('<div data-id="' + heading_row + '" class="form-group feature-row" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
                    '\n' +
                    '                                                                            <div class="col-sm-5">\n' +
                    '\n' +
                    '                                                                            <select class="form-control validate js-data-example-ajax5">\n' +
                    '\n' +
                    '                                                                                <option value="">Select Feature Heading</option>\n' +
                    '\n' +
                    '                                                                                @foreach($features_headings as $feature)\n' +
                    '\n' +
                    '                                                                                    <option value="{{$feature->id}}">{{$feature->title}}</option>\n' +
                    '\n' +
                    '                                                                                @endforeach\n' +
                    '\n' +
                    '                                                                            </select>\n' +
                    '\n' +
                    '                                                                        </div>\n' +
                    '\n' +
                    '                                                                    <div style="display: flex;" class="col-sm-5">\n' +
                    '\n' +
                    '                                                                        <button data-id="' + heading_row + '" style="margin-right: 10px;" class="btn btn-success create-feature-btn" type="button">{{__('text.Create/Edit Features')}}</button>\n' +
                    '                                                                        <span class="ui-close remove-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;">X</span>\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                </div>');

                var features = '';

                if(value.feature_details.length == 0)
                {
                    var f_row = null;

                    $('#primary-features').find(".feature-table-container").each(function() {

                        $(this).find('table tbody tr').each(function() {

                            var value = parseInt($(this).find('.f_row').val());
                            value = isNaN(value) ? 0 : value;
                            f_row = (value > f_row) ? value : f_row;

                        });
                    });

                    var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                    selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                    f_row = f_row + 1;

                    features = features + '<tr data-id="'+f_row+'">\n' +
                        '\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                        '                                                                                            <input value="'+value.id+'" type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                        selectElement.prop('outerHTML') +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                        '\n' +
                        '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                        '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                        '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                        '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                        '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                        '\n' +
                        '                                                                                            </select>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                        '\n' +
                        '                                                                                                <option value="0">€</option>\n' +
                        '                                                                                                <option value="1">%</option>\n' +
                        '\n' +
                        '                                                                                            </select>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                    </tr>';

                    var sub_features = '';

                    var feature_row1 = null;

                    $('#sub-features').find(".sub-feature-table-container").each(function () {

                        $(this).find('table tbody tr').each(function () {

                            var value = parseInt($(this).find('.f_row1').val());
                            value = isNaN(value) ? 0 : value;
                            feature_row1 = (value > feature_row1) ? value : feature_row1;

                        });
                    });

                    var selectElement = $("<select>").addClass("feature_title1").attr("name", "features"+ f_row +"[]");
                    selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                    feature_row1 = feature_row1 + 1;

                    sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                        selectElement.prop('outerHTML') +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                        '\n' +
                        '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                        '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                        '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                        '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                        '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                        '\n' +
                        '                                                                                            </select>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                        '\n' +
                        '                                                                                                <option value="0">€</option>\n' +
                        '                                                                                                <option value="1">%</option>\n' +
                        '\n' +
                        '                                                                                            </select>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                        <td>\n' +
                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                        '                                                                                        </td>\n' +
                        '                                                                                    </tr>';

                    $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                        '\n' +
                        '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                        '                                                                                            <thead>\n' +
                        '                                                                                            <tr>\n' +
                        '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                        '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                        '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                        '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                        '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                        '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                        '                                                                                            </tr>\n' +
                        '                                                                                            </thead>\n' +
                        '\n' +
                        '                                                                                            <tbody>' +
                        sub_features +
                        '                                                                                            </tbody></table>' +
                        '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                        '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                        '                                                                                        </div></div>');
                }
                else
                {
                    var f_row = null;

                    $('#primary-features').find(".feature-table-container").each(function() {

                        $(this).find('table tbody tr').each(function() {

                            var value = parseInt($(this).find('.f_row').val());
                            value = isNaN(value) ? 0 : value;
                            f_row = (value > f_row) ? value : f_row;

                        });
                    });

                    var feature_row1 = null;

                    $('#sub-features').find(".sub-feature-table-container").each(function () {

                        $(this).find('table tbody tr').each(function () {

                            var value = parseInt($(this).find('.f_row1').val());
                            value = isNaN(value) ? 0 : value;
                            feature_row1 = (value > feature_row1) ? value : feature_row1;

                        });
                    });

                    $.each(value.feature_details, function(index1, value1) {

                        f_row = f_row + 1;
                        label_update(null,heading_row,f_row,value1.title);

                        var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                        selectElement = feature_values(selectElement,value.feature_details,value1.id);

                        var fv = value1.factor_value != null ? value1.factor_value : "";

                        features = features + '<tr data-id="'+f_row+'">\n' +
                            '\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                            '                                                                                            <input value="'+value.id+'" type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                            selectElement.prop('outerHTML') +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <input value="'+value1.value+'" class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <input value="'+fv+'" class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                            '\n' +
                            (value1.price_impact == 0 ? '<option selected value="0">{{__("text.No")}}</option>' : '<option value="0">{{__("text.No")}}</option>') +
                            (value1.price_impact == 1 ? '<option selected value="1">{{__("text.Fixed")}}</option>' : '<option value="1">{{__("text.Fixed")}}</option>') +
                            (value1.price_impact == 2 ? '<option selected value="2">{{__("text.m¹ Impact")}}</option>' : '<option value="2">{{__("text.m¹ Impact")}}</option>') +
                            (value1.price_impact == 3 ? '<option selected value="3">{{__("text.m² Impact")}}</option>' : '<option value="3">{{__("text.m² Impact")}}</option>') +
                            (value1.price_impact == 4 ? '<option selected value="4">{{__("text.Factor")}}</option>' : '<option value="4">{{__("text.Factor")}}</option>') +
                            '\n' +
                            '                                                                                            </select>\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                            '\n' +
                            (value1.impact_type == 0 ? '<option selected value="0">€</option>' : '<option value="0">€</option>') +
                            (value1.impact_type == 1 ? '<option selected value="1">%</option>' : '<option value="1">%</option>') +
                            '\n' +
                            '                                                                                            </select>\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                        <td>\n' +
                            '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                            '                                                                                        </td>\n' +
                            '                                                                                    </tr>';


                        var sub_features = '';

                        if(value.sub_features.length == 0) {

                            var selectElement = $("<select>").addClass("feature_title1").attr("name", "features"+ f_row +"[]");
                            selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                            feature_row1 = feature_row1 + 1;

                            sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                selectElement.prop('outerHTML') +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">€</option>\n' +
                                '                                                                                                <option value="1">%</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                    </tr>';
                        }
                        else
                        {
                            var flag = 0;

                            $.each(value.sub_features, function(index2, value2) {

                                if(value1.id == value2.main_id)
                                {
                                    var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                    selectElement = feature_values(selectElement,value.sub_features,value2.id,value1.id);

                                    feature_row1 = feature_row1 + 1;
                                    var fv = value2.factor_value != null ? value2.factor_value : "";

                                    sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                        selectElement.prop('outerHTML') +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input value="'+value2.value+'" class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input value="'+fv+'" class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                        '\n' +
                                        (value2.price_impact == 0 ? '<option selected value="0">{{__("text.No")}}</option>' : '<option value="0">{{__("text.No")}}</option>') +
                                        (value2.price_impact == 1 ? '<option selected value="1">{{__("text.Fixed")}}</option>' : '<option value="1">{{__("text.Fixed")}}</option>') +
                                        (value2.price_impact == 2 ? '<option selected value="2">{{__("text.m¹ Impact")}}</option>' : '<option value="2">{{__("text.m¹ Impact")}}</option>') +
                                        (value2.price_impact == 3 ? '<option selected value="3">{{__("text.m² Impact")}}</option>' : '<option value="3">{{__("text.m² Impact")}}</option>') +
                                        (value2.price_impact == 4 ? '<option selected value="4">{{__("text.Factor")}}</option>' : '<option value="4">{{__("text.Factor")}}</option>') +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                        '\n' +
                                        (value2.impact_type == 0 ? '<option selected value="0">€</option>' : '<option value="0">€</option>') +
                                        (value2.impact_type == 1 ? '<option selected value="1">%</option>' : '<option value="1">%</option>') +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                    </tr>';

                                    flag = 1;
                                }

                            });

                            if(flag == 0)
                            {
                                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                                feature_row1 = feature_row1 + 1;

                                sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                    selectElement.prop('outerHTML') +
                                    '                                                                                        </td>\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                    '                                                                                        </td>\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                    '                                                                                        </td>\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                    '\n' +
                                    '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                    '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                    '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                    '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                    '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                    '\n' +
                                    '                                                                                            </select>\n' +
                                    '                                                                                        </td>\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                    '\n' +
                                    '                                                                                                <option value="0">€</option>\n' +
                                    '                                                                                                <option value="1">%</option>\n' +
                                    '\n' +
                                    '                                                                                            </select>\n' +
                                    '                                                                                        </td>\n' +
                                    '                                                                                        <td>\n' +
                                    '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                    '                                                                                        </td>\n' +
                                    '                                                                                    </tr>';
                            }

                        }

                        $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                            '\n' +
                            '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                            '                                                                                            <thead>\n' +
                            '                                                                                            <tr>\n' +
                            '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                            '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                            '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                            '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                            '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                            '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                            '                                                                                            </tr>\n' +
                            '                                                                                            </thead>\n' +
                            '\n' +
                            '                                                                                            <tbody>' +
                            sub_features +
                            '                                                                                    </tbody></table>' +
                            '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                            '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                            '                                                                                        </div></div>');

                    });

                }

                $('#primary-features').append('<div data-id="'+heading_row+'" class="feature-table-container">\n' +
                    '\n' +
                    '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                    '                                                                                        <thead>\n' +
                    '                                                                                        <tr>\n' +
                    '                                                                                            <th>{{__("text.Feature")}}</th>\n' +
                    '                                                                                            <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
                    '                                                                                            <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
                    '                                                                                            <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
                    '                                                                                            <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                    '                                                                                            <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                    '                                                                                            <th>{{__("text.Remove")}}</th>\n' +
                    '                                                                                        </tr>\n' +
                    '                                                                                        </thead>\n' +
                    '\n' +
                    '                                                                                        <tbody>' +
                    features +
                    '                                                                                    </tbody></table>' +
                    '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                    '                                                                                        <button data-id="'+heading_row+'" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
                    '                                                                                    </div></div>');

                $(".feature_box").find(".feature-row[data-id='" + heading_row + "']").find('.js-data-example-ajax5').val(value.id).trigger('change.select2');

                $('.feature_title').each(function (index,value) {

                    label_update(this);

                });

            });

            $('.js-data-example-ajax5').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Select Feature Heading Placeholder")}}",
                allowClear: true,
            });

            $('.feature_title').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Feature Title")}}",
                allowClear: true,
            });

            $('.feature_title1').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Feature Title")}}",
                allowClear: true,
            });
        }

        function feature_values(selectElement,details,id = null,main_id = null)
        {
            selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

            $.each(details, function(ind, val) {
                if(!main_id || (main_id == val.main_id))
                {
                    var option = $("<option>").text(val.title);
                    option.attr("value", val.id);

                    if (val.id === id) {
                        option.attr("selected", "selected");
                    }
                            
                    selectElement.append(option);
                }
            });

            return selectElement;
        }

        $('body').on('change', '.js-data-example-ajax8' ,function(){

            var id = $(this).val();
            var options = '';

            $.ajax({
                type:"GET",
                data: "id=" + id + "&type=single",
                url: "<?php echo url('/aanbieder/product/get-sub-categories-by-category')?>",
                success: function(data) {

                    $.each(data, function(index, value) {

                        var opt = '<option value="'+value.id+'" >'+value.cat_name+'</option>';

                        options = options + opt;

                    });

                    $('.js-data-example-ajax9').find('option')
                        .remove()
                        .end()
                        .append('<option value="">{{__('text.Select sub category')}}</option>'+options);

                }
            });

            if($("#supplier_id").val())
            {
                var ajax_data = "id=" + id + "&user_id=" + $("#supplier_id").val();
            }
            else
            {
                var ajax_data = "id=" + id;
            }

            $.ajax({
                type:"GET",
                data: ajax_data,
                url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                success: function(data) {

                    fetch_features(data);

                }
            });

        });

        $('body').on('change', '.js-data-example-ajax9' ,function(){

            var sub_id = $(this).val();
            var cat_id = $('.js-data-example-ajax8').val();

            if($("#supplier_id").val())
            {
                var ajax_data = "id=" + cat_id + "&sub_id=" + sub_id + "&user_id=" + $("#supplier_id").val();
            }
            else
            {
                var ajax_data = "id=" + cat_id + "&sub_id=" + sub_id;
            }

            $.ajax({
                type:"GET",
                data: ajax_data,
                url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                success: function(data) {

                    fetch_features(data);

                }
            });

        });

        $('body').on('click', '.create-feature-btn' ,function(){

            var id = $(this).data('id');
            $('#primary-features').children().not(".feature-table-container[data-id='" + id + "']").hide();
            $('#primary-features').find(".feature-table-container[data-id='" + id + "']").show();

            $('#myModal1').modal('toggle');
            $('.modal-backdrop').hide();

        });

        $('body').on('click', '.create-sub-feature-btn' ,function(){

            var id = $(this).data('id');
            $('#sub-features').children().not(".sub-feature-table-container[data-id='" + id + "']").hide();
            $('#sub-features').find(".sub-feature-table-container[data-id='" + id + "']").show();

            $('#myModal1').modal('toggle');
            $('#myModal2').modal('toggle');
            $('.modal-backdrop').hide();

        });

        $('#myModal1').on('hidden.bs.modal', function () {
            $('body').addClass('modal-open');
        });

        $('body').on('click', '.select-feature-btn' ,function(){

            var id = $(this).data('id');
            $('#models-features-tables #features1').find("table").hide();
            $('#models-features-tables').find(".model-childsafe").hide();
            $('#models-features-tables').find(".model-curtain").hide();
            $('#models-features-tables').find(".model-childsafe[data-id='" + id + "']").show();
            $('#models-features-tables').find(".model-curtain[data-id='" + id + "']").show();
            $('#models-features-tables #features1').find("table[data-id='" + id + "']").show();

            $('#myModal').modal('toggle');
            $('.modal-backdrop').hide();

        });

        function add_curtain_variable(table)
        {
            var row_id1 = table.parents(".model-curtain").data("id");

            table.find("tbody").append('<tr>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <div class="curtain_option_box" style="display: flex;align-items: center;">\n' +
                '                                                                                                                   <input type="hidden" name="curtain_variable_options'+row_id1+'[]" id="curtain_variable_option" value="0">\n' +
                '                                                                                                                    <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.Off')}}</span>\n' +
                '                                                                                                                    <label style="margin: 0;" class="switch">\n' +
                '                                                                                                                        <input class="curtain_variable_option" type="checkbox">\n' +
                '                                                                                                                        <span class="slider round"></span>\n' +
                '                                                                                                                    </label>\n' +
                '                                                                                                                    <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.On')}}</span>\n' +
                '                                                                                                               </div>\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <input type="text" name="curtain_variable_descriptions'+row_id1+'[]" class="form-control">\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <input type="text" maskedformat="9,1" name="curtain_variable_values'+row_id1+'[]" class="form-control curtain_variable_values">\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <div style="display: flex;">\n' +
                '                                                                                                                   <span class="add-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">\n' +
                '                                                                                                                       <i style="width: 100%;" class="fa fa-fw fa-plus"></i>\n' +
                '                                                                                                                   </span>\n' +
                '                                                                                                                   <span class="remove-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;margin-left: 5px;">\n' +
                '                                                                                                                       <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>\n' +
                '                                                                                                                   </span>\n' +
                '                                                                                                               </div>\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                       </tr>');
        }

        function remove_curtain_variable(row,table)
        {
            row.remove();

            if(table.find("tbody tr").length == 0)
            {
                add_curtain_variable(table);
            }
        }

        function add_model_content(model_row,rows)
        {
            $('#models-features-tables #general1').append('<div style="margin-left: 0;margin-right: 0;" data-id="'+model_row+'" class="form-group model-childsafe">\n' +
                '\n' +
                '                                                                                        <div class="row" style="margin: auto;width: 70%;">\n' +
                '\n' +
                '                                                                                            <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                <label style="display: block;text-align: left;padding-top: 0;padding-right: 20px;color: red;width: 40%;" class="control-label">{{__("text.Childsafe")}}:</label>\n' +
                '                                                                                                <input type="hidden" name="childsafe[]" id="childsafe" value="0">\n' +
                '\n' +
                '                                                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__("text.No")}}</span>\n' +
                '                                                                                                <label style="margin: 0;" class="switch">\n' +
                '                                                                                                    <input class="childsafe" type="checkbox">\n' +
                '                                                                                                    <span class="slider round"></span>\n' +
                '                                                                                                </label>\n' +
                '                                                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__("text.Yes")}}</span>\n' +
                '\n' +
                '                                                                                            </div>\n' +
                '\n' +
                '                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__("text.Max m²")}}:</label>\n' +
                '                                                                                                    <input class="form-control model_max_size" name="model_max_size[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max m²')}}" type="text">\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__("text.Max Width")}}:</label>\n' +
                '                                                                                                    <input class="form-control model_max_width" name="model_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Width Placeholder')}}" type="text">\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__("text.Max Height")}}:</label>\n' +
                '                                                                                                    <input class="form-control model_max_height" name="model_max_height[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Max Height Placeholder')}}" type="text">\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                        </div>\n' +
                '\n' +
                '                                                                                    </div>');

                $('#models-features-tables #features1').append('<table data-id="'+model_row+'" style="margin: auto;width: 70%;border-collapse: separate;">\n' +
                '                <thead>\n' +
                '                   <tr>\n' +
                '                       <th></th>\n' +
                '                       <th>{{__('text.Heading')}}</th>\n' +
                '                       <th>{{__('text.Feature')}}</th>\n' +
                '                   </tr>\n' +
                '                </thead>\n' +
                '\n' +
                '                <tbody>\n' +
                '\n' +
                rows +
                '                </tbody>\n' +
                '                </table>');

                $('#models-features-tables #curtains1').append('<div style="margin-left: 0;margin-right: 0;" data-id="'+model_row+'" class="form-group model-curtain">\n' +
                '\n' +
                '                                                                                        <input type="hidden" name="row_curtain_id[]" value="'+model_row+'">\n' +
                '                                                                                        <div class="row" style="margin: auto;width: 100%;">\n' +
                '\n' +
                '                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__('text.Curtain Type')}}:</label>\n' +
                '                                                                                                    <select style="border-radius: 5px;" class="form-control curtain_type" name="curtain_type[]">\n' +
                '                                                                                                       <option value="1">Kamerhoog</option>\n' +
                '                                                                                                       <option value="2">{{__('text.Max Width')}}</option>\n' +
                '                                                                                                    </select>\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                                <div style="display: flex;align-items: center;justify-content: flex-start;padding: 0;margin-top: 20px;margin-bottom: 20px;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">\n' +
                '\n' +
                '                                                                                                    <label style="display: block;text-align: left;padding-top: 0;color: red;white-space: nowrap;margin-right: 10px;width: 65%;" class="control-label">{{__("text.Factor Max Width")}}:</label>\n' +
                '                                                                                                    <input value="0" readonly class="form-control model_factor_max_width" name="model_factor_max_width[]" maskedformat="9,1" id="blood_group_slug" placeholder="{{__('text.Factor Max Width Placeholder')}}" type="text">\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                                <table style="width: 100%;border-collapse: separate;display: table;">\n' +
                '\n' +
                '                                                                                                    <thead>\n' +
                '                                                                                                       <tr>\n' +
                '                                                                                                           <th>{{__('text.Option')}}</th>\n' +
                '                                                                                                           <th>{{__('text.Description')}}</th>\n' +
                '                                                                                                           <th>{{__('text.Value')}}</th>\n' +
                '                                                                                                           <th></th>\n' +
                '                                                                                                       </tr>\n' +
                '                                                                                                    </thead>\n' +
                '\n' +
                '                                                                                                    <tbody>\n' +
                '                                                                                                       <tr>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <div class="curtain_option_box" style="display: flex;align-items: center;">\n' +
                '                                                                                                                   <input type="hidden" name="curtain_variable_options'+model_row+'[]" id="curtain_variable_option" value="0">\n' +
                '                                                                                                                    <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">{{__('text.Off')}}</span>\n' +
                '                                                                                                                    <label style="margin: 0;" class="switch">\n' +
                '                                                                                                                        <input class="curtain_variable_option" type="checkbox">\n' +
                '                                                                                                                        <span class="slider round"></span>\n' +
                '                                                                                                                    </label>\n' +
                '                                                                                                                    <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">{{__('text.On')}}</span>\n' +
                '                                                                                                               </div>\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <input type="text" name="curtain_variable_descriptions'+model_row+'[]" class="form-control">\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <input type="text" maskedformat="9,1" name="curtain_variable_values'+model_row+'[]" class="form-control curtain_variable_values">\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                           <td>\n' +
                '                                                                                                               <div style="display: flex;">\n' +
                '                                                                                                                   <span class="add-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;">\n' +
                '                                                                                                                       <i style="width: 100%;" class="fa fa-fw fa-plus"></i>\n' +
                '                                                                                                                   </span>\n' +
                '                                                                                                                   <span class="remove-curtain-variable" style="cursor: pointer;font-size: 20px;width: 20px;height: 20px;line-height: 20px;margin-left: 5px;">\n' +
                '                                                                                                                       <i style="width: 100%;" class="fa fa-fw fa-trash-o"></i>\n' +
                '                                                                                                                   </span>\n' +
                '                                                                                                               </div>\n' +
                '                                                                                                           </td>\n' +
                '                                                                                                       </tr>\n' +
                '                                                                                                    </tbody>\n' +
                '\n' +
                '                                                                                                </div>\n' +
                '\n' +
                '                                                                                        </div>\n' +
                '\n' +
                '                                                                                    </div>');
        }

        function add_model(title = '',value = '',factor_value = '',price_impact = '',impact_type = '',size_id = '')
        {
            var model_row = $('.model_box').find('.form-group').last().data('id');
            model_row = model_row + 1;

            $(".model_box").append('<div data-id="'+model_row+'" class="form-group model-row" style="margin: 0 0 20px 0;">\n' +
                '\n' +
                '                                                                   <div class="col-sm-3 autocomplete">\n' +
                '\n' +
                '                                                                        <input value="'+size_id+'" type="hidden" name="size_ids[]" class="form-control size_ids">\n' +
                (size_id ? '<input readonly value="'+title+'" type="text" id="modelInput" autocomplete="off" placeholder="Model" name="models[]" class="form-control validate models">\n' : '<input value="'+title+'" type="text" id="modelInput" autocomplete="off" placeholder="Model" name="models[]" class="form-control validate models">\n') +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                                                                    <div class="col-sm-2">\n' +
                '\n' +
                '                                                                        <input value="'+value+'" maskedformat="9,1" class="form-control model_value" name="model_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                                                                    <div class="col-sm-2">\n' +
                '\n' +
                '                                                                        <input value="'+factor_value+'" maskedformat="9,1" class="form-control model_factor_value" name="model_factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">\n' +
                '\n' +
                '                                                                        <select class="form-control" id="price_impact" name="model_price_impact[]">\n' +
                '\n' +
                (price_impact == 0 ? '<option selected value="0">No</option>\n': '<option value="0">{{__("text.No")}}</option>\n') +
                (price_impact == 1 ? '<option selected value="1">{{__("text.Fixed")}}</option>\n': '<option value="1">{{__("text.Fixed")}}</option>\n') +
                (price_impact == 2 ? '<option selected value="2">{{__("text.m¹ Impact")}}</option>\n': '<option value="2">{{__("text.m¹ Impact")}}</option>\n') +
                (price_impact == 3 ? '<option selected value="3">{{__("text.m² Impact")}}</option>\n': '<option value="3">{{__("text.m² Impact")}}</option>\n') +
                (price_impact == 4 ? '<option selected value="4">{{__("text.Factor")}}</option>\n': '<option value="4">{{__("text.Factor")}}</option>\n') +
                '\n' +
                '                                                                        </select>\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">\n' +
                '\n' +
                '                                                                        <select class="form-control" id="impact_type" name="model_impact_type[]">\n' +
                '\n' +
                (impact_type == 0 ? '<option selected value="0">€</option>\n': '<option value="0">€</option>\n') +
                (impact_type == 1 ? '<option selected value="1">%</option>\n': '<option value="1">%</option>\n') +
                '\n' +
                '                                                                        </select>\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '                                                                    <div style="display: flex;justify-content: center;" class="col-sm-1">\n' +
                '\n' +
                /*'                                                                        <button data-id="'+model_row+'" style="margin-right: 10px;" class="btn btn-success select-feature-btn" type="button">Select Features</button>\n' +*/
                '                                                                        <span class="ui-close select-feature-btn" data-id="'+model_row+'" style="margin: 0;position: relative;right: auto;top: 0;background-color: #5cb85c;font-size: 22px;">+</span>\n' +
                '                                                                        <span class="ui-close remove-model" data-id="" style="margin: 0;position: relative;right: -5px;top: 0;">X</span>\n' +
                '\n' +
                '                                                                    </div>' +
                '\n' +
                '        </div>');

            var last_row = $('.model_box .model-row:last');

            autocomplete(last_row.find("#modelInput")[0], model_titles, values);

            var rows = '';

            $('.feature_box').find('.feature-row', this).each(function (index) {

                var id = $(this).data('id');
                var heading = $(this).find('.js-data-example-ajax5 option:selected').text();
                var heading_id = $(this).find('.js-data-example-ajax5').val();

                if(!heading_id)
                {
                    heading = '';
                }

                $('#primary-features').find(".feature-table-container[data-id='" + id + "']").find('table tbody tr').each(function (index) {

                    var title_id = $(this).find('.feature_title').val();
                    var title = $(this).find('.feature_title option:selected').text();
                    var row = $(this).find('.f_row').val();

                    if(title_id && heading_id)
                    {
                        rows += '<tr data-id="'+row+'">' +
                            '                                                                                 <td>\n' +
                            '                                                                                <div style="display: flex;justify-content: center;align-items: center;">\n' +
                            '                                                                                <input type="hidden" name="selected_model_feature'+row+'[]" id="price_impact" value="0">\n' +
                            '                                                                                <label style="margin: 0;" class="switch">\n' +
                            '                                                                                    <input class="price_impact" type="checkbox">\n' +
                            '                                                                                    <span class="slider round"></span>\n' +
                            '                                                                                </label>\n' +
                            '                                                                                </div>\n' +
                            '                                                                                </td>' +
                            '                                                                                <td>'+heading+'</td><td>'+title+'</td></tr>';
                    }

                });

            });

            add_model_content(model_row,rows);
        }

        function remove_model(id,model_row)
        {
            if(id)
            {
                rem_mod.push(id);
                $('#removed_rows1').val(rem_mod);
            }

            $('#models-features-tables').find(".model-childsafe[data-id='" + model_row + "']").remove();
            $('#models-features-tables').find(".model-curtain[data-id='" + model_row + "']").remove();
            $('#models-features-tables #features1').find("table[data-id='" + model_row + "']").remove();
            $('.model_box').find("[data-id='" + model_row + "']").remove();

            if($(".model_box .form-group").length == 0)
            {
                $(".model_box").append('<div data-id="1" class="form-group model-row" style="margin: 0 0 20px 0;">\n' +
                    '\n' +
                    '                                                                   <div class="col-sm-3 autocomplete">\n' +
                    '\n' +
                    '                                                                        <input type="hidden" name="size_ids[]" class="form-control size_ids">\n' +
                    '                                                                        <input type="text" id="modelInput" autocomplete="off" placeholder="Model" name="models[]" class="form-control validate models">\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                                                                    <div class="col-sm-2">\n' +
                    '\n' +
                    '                                                                        <input class="form-control model_value" maskedformat="9,1" name="model_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                                                                    <div class="col-sm-2">\n' +
                    '\n' +
                    '                                                                        <input class="form-control model_factor_value" maskedformat="9,1" name="model_factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">\n' +
                    '\n' +
                    '                                                                        <select class="form-control" id="price_impact" name="model_price_impact[]">\n' +
                    '\n' +
                    '                                                                           <option value="0">{{__("text.No")}}</option>\n' +
                    '                                                                           <option value="1">{{__("text.Fixed")}}</option>\n' +
                    '                                                                           <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                    '                                                                           <option value="3">{{__("text.m² Impact")}}</option>\n' +
                    '                                                                           <option value="4">{{__("text.Factor")}}</option>\n' +
                    '\n' +
                    '                                                                        </select>\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                                                                    <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-2">\n' +
                    '\n' +
                    '                                                                        <select class="form-control" id="impact_type" name="model_impact_type[]">\n' +
                    '\n' +
                    '                                                                           <option value="0">€</option>\n' +
                    '                                                                           <option value="1">%</option>\n' +
                    '\n' +
                    '                                                                        </select>\n' +
                    '\n' +
                    '                                                                    </div>\n' +
                    '\n' +
                    '                                                                    <div style="display: flex;justify-content: center;" class="col-sm-1">\n' +
                    '\n' +
                    /*'                                                                        <button data-id="1" style="margin-right: 10px;" class="btn btn-success select-feature-btn" type="button">Select Features</button>\n' +*/
                    '                                                                        <span class="ui-close select-feature-btn" data-id="1" style="margin: 0;position: relative;right: auto;top: 0;background-color: #5cb85c;font-size: 22px;">+</span>\n' +
                    '                                                                        <span class="ui-close remove-model" data-id="" style="margin: 0;position: relative;right: -5px;top: 0;">X</span>\n' +
                    '\n' +
                    '                                                                    </div>' +
                    '\n' +
                    '        </div>');

                var last_row = $('.model_box .model-row:last');

                autocomplete(last_row.find("#modelInput")[0], model_titles, values);

                var rows = '';

                $('.feature_box').find('.feature-row', this).each(function (index) {

                    var id = $(this).data('id');
                    var heading = $(this).find('.js-data-example-ajax5 option:selected').text();
                    var heading_id = $(this).find('.js-data-example-ajax5').val();

                    if(!heading_id)
                    {
                        heading = '';
                    }

                    $('#primary-features').find(".feature-table-container[data-id='" + id + "']").find('table tbody tr').each(function (index) {

                        var title_id = $(this).find('.feature_title').val();
                        var title = $(this).find('.feature_title option:selected').text();
                        var row = $(this).find('.f_row').val();

                        if(title_id && heading_id)
                        {
                            rows += '<tr data-id="'+row+'">' +
                                '                                                                                 <td>\n' +
                                '                                                                                <div style="display: flex;justify-content: center;align-items: center;">\n' +
                                '                                                                                <input type="hidden" name="selected_model_feature'+row+'[]" id="price_impact" value="0">\n' +
                                '                                                                                <label style="margin: 0;" class="switch">\n' +
                                '                                                                                    <input class="price_impact" type="checkbox">\n' +
                                '                                                                                    <span class="slider round"></span>\n' +
                                '                                                                                </label>\n' +
                                '                                                                                </div>\n' +
                                '                                                                                </td>' +
                                '                                                                                <td>'+heading+'</td><td>'+title+'</td></tr>';
                        }

                    });

                });

                add_model_content(1,rows);
            }
        }


        $('body').on('click', '#add-model-btn' ,function(){

            add_model();

        });

        $('body').on('click', '.add-curtain-variable' ,function(){

            var table = $(this).parents("table");

            add_curtain_variable(table);

        });

        $('body').on('click', '.remove-curtain-variable' ,function(){

            var row = $(this).parents("tr");
            var table = $(this).parents("table");

            remove_curtain_variable(row,table);

        });

        var rem_mod = [];

        $('body').on('click', '.remove-model' ,function()
        {
            var id = $(this).data('id');
            var model_row = $(this).parent().parent().data('id');

            remove_model(id,model_row);

        });

        var $selects = $('body').on('change', '.js-data-example-ajax5', function()
        {
            var feature_category = $('.js-data-example-ajax8').val();
            var feature_sub_category = $('.js-data-example-ajax9').val();
            var id = $(this).parent().parent().attr("data-id");
            var heading = $(this).find("option:selected").text();
            var heading_id = $(this).val();

            var selector = this;

            if ($('.js-data-example-ajax5').find('option[value=' + heading_id + ']:selected').length > 1) {

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This Heading is already selected!',

                });

                $(selector).val('').trigger('change.select2');

            }
            else
            {
                $('.feature_box').find(".feature-row[data-id='" + id + "']").find(".feature-group").remove();

                if(!heading_id)
                {
                    heading = '';
                }

                if(feature_category)
                {
                    if($("#supplier_id").val())
                    {
                        var ajax_data = "id=" + feature_category + "&sub_id=" + feature_sub_category + '&heading_id=' + heading_id + "&user_id=" + $("#supplier_id").val();
                    }
                    else
                    {
                        var ajax_data = "id=" + feature_category + "&sub_id=" + feature_sub_category + '&heading_id=' + heading_id;
                    }

                    $.ajax({
                        type:"GET",
                        data: ajax_data,
                        url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                        success: function(data) {

                            $('.feature_box').find(".feature-row[data-id='" + id + "']").find('.remove-feature').each(function() {

                                var id = $(this).data('id');
                                var row_id = $(this).parent().parent().data('id');

                                $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").find('table tbody tr').each(function (index) {

                                    var row = $(this).find('.f_row').val();

                                    if($(this).find('.remove-primary-feature').data('id'))
                                    {
                                        rem_arr.push($(this).find('.remove-primary-feature').data('id'));
                                    }

                                    $('#models-features-tables #features1 table tbody').find("[data-id='" + row + "']").remove();

                                    $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").find('table tbody tr').each(function (index) {

                                        if($(this).find('.remove-sub-feature').data('id'))
                                        {
                                            rem_arr.push($(this).find('.remove-sub-feature').data('id'));
                                        }

                                    });

                                    $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").remove();

                                });

                                $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").remove();


                                if(id)
                                {
                                    $('#removed_rows').val(rem_arr);
                                }

                            });

                            if(data.length == 0 || !heading_id)
                            {
                                var f_row = null;

                                $('#primary-features').find(".feature-table-container").each(function() {

                                    $(this).find('table tbody tr').each(function() {

                                        var value = parseInt($(this).find('.f_row').val());
                                        value = isNaN(value) ? 0 : value;
                                        f_row = (value > f_row) ? value : f_row;

                                    });
                                });

                                var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                                f_row = f_row + 1;

                                $('#primary-features').append('<div data-id="'+id+'" class="feature-table-container">\n' +
                                '\n' +
                                '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                                '                                                                                        <thead>\n' +
                                '                                                                                        <tr>\n' +
                                '                                                                                            <th>{{__("text.Feature")}}</th>\n' +
                                '                                                                                            <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
                                '                                                                                            <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
                                '                                                                                            <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
                                '                                                                                            <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                                '                                                                                            <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                                '                                                                                            <th>{{__("text.Remove")}}</th>\n' +
                                '                                                                                        </tr>\n' +
                                '                                                                                        </thead>\n' +
                                '\n' +
                                '                                                                                        <tbody>' +
                                '                                                                                   <tr data-id="'+f_row+'">\n' +
                                '\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                                '                                                                                            <input value="'+heading_id+'" type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                                selectElement.prop('outerHTML') +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">€</option>\n' +
                                '                                                                                                <option value="1">%</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                    </tr></tbody></table>' +
                                '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                                '                                                                                        <button data-id="'+id+'" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
                                '                                                                                    </div></div>');

                                var sub_row = null;

                                $('#sub-features').find(".sub-feature-table-container").each(function () {

                                    $(this).find('table tbody tr').each(function () {

                                        var value = parseInt($(this).find('.f_row1').val());
                                        value = isNaN(value) ? 0 : value;
                                        sub_row = (value > sub_row) ? value : sub_row;

                                    });
                                });

                                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                                sub_row = sub_row + 1;

                                $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                                '\n' +
                                '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                                '                                                                                            <thead>\n' +
                                '                                                                                            <tr>\n' +
                                '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                                '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                                '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                                '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                                '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                                '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                                '                                                                                            </tr>\n' +
                                '                                                                                            </thead>\n' +
                                '\n' +
                                '                                                                                            <tbody>' +
                                '                                                                                        <tr data-id="'+sub_row+'">\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input type="hidden" name="f_rows'+f_row+'[]" class="f_row1" value="'+sub_row+'">' +
                                selectElement.prop('outerHTML') +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control feature_value1" name="feature_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <input class="form-control factor_value1" name="factor_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select class="form-control" name="price_impact'+f_row+'[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <select class="form-control" name="impact_type'+f_row+'[]">\n\n' +
                                '\n' +
                                '                                                                                                <option value="0">€</option>\n' +
                                '                                                                                                <option value="1">%</option>\n' +
                                '\n' +
                                '                                                                                            </select>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                        <td>\n' +
                                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                '                                                                                        </td>\n' +
                                '                                                                                    </tr></tbody></table>' +
                                '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                                '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                                '                                                                                        </div></div>');
                            }
                            else
                            {
                                $.each(data, function(index, value) {

                                    var features = '';
    
                                    if(value.feature_details.length == 0)
                                    {
                                        var f_row = null;
    
                                        $('#primary-features').find(".feature-table-container").each(function() {
    
                                            $(this).find('table tbody tr').each(function() {
    
                                                var value = parseInt($(this).find('.f_row').val());
                                                value = isNaN(value) ? 0 : value;
                                                f_row = (value > f_row) ? value : f_row;
    
                                            });
                                        });
    
                                        var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                                        selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
    
                                        f_row = f_row + 1;
    
                                        features = features + '<tr data-id="'+f_row+'">\n' +
                                        '\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                                        '                                                                                            <input value="'+value.id+'" type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                                        selectElement.prop('outerHTML') +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                                        '\n' +
                                        '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                        '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                        '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                        '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                        '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                                        '\n' +
                                        '                                                                                                <option value="0">€</option>\n' +
                                        '                                                                                                <option value="1">%</option>\n' +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                    </tr>';
    
                                        var sub_features = '';
    
                                        var feature_row1 = null;
    
                                        $('#sub-features').find(".sub-feature-table-container").each(function () {
    
                                            $(this).find('table tbody tr').each(function () {
    
                                                var value = parseInt($(this).find('.f_row1').val());
                                                value = isNaN(value) ? 0 : value;
                                                feature_row1 = (value > feature_row1) ? value : feature_row1;
    
                                            });
                                        });
    
                                        var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                        selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
    
                                        feature_row1 = feature_row1 + 1;
    
                                        sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                        selectElement.prop('outerHTML') +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                        '\n' +
                                        '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                        '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                        '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                        '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                        '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                        '\n' +
                                        '                                                                                                <option value="0">€</option>\n' +
                                        '                                                                                                <option value="1">%</option>\n' +
                                        '\n' +
                                        '                                                                                            </select>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                        <td>\n' +
                                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                        '                                                                                        </td>\n' +
                                        '                                                                                    </tr>';
    
                                        $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                                        '\n' +
                                        '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                                        '                                                                                            <thead>\n' +
                                        '                                                                                            <tr>\n' +
                                        '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                                        '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                                        '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                                        '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                                        '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                                        '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                                        '                                                                                            </tr>\n' +
                                        '                                                                                            </thead>\n' +
                                        '\n' +
                                        '                                                                                            <tbody>' +
                                        sub_features +
                                        '                                                                                    </tbody></table>' +
                                        '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                                        '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                                        '                                                                                        </div></div>');
                                    }
                                    else
                                    {
                                        var f_row = null;
    
                                        $('#primary-features').find(".feature-table-container").each(function() {
    
                                            $(this).find('table tbody tr').each(function() {
    
                                                var value = parseInt($(this).find('.f_row').val());
                                                value = isNaN(value) ? 0 : value;
                                                f_row = (value > f_row) ? value : f_row;
    
                                            });
                                        });
    
                                        var feature_row1 = null;
    
                                        $('#sub-features').find(".sub-feature-table-container").each(function () {
    
                                            $(this).find('table tbody tr').each(function () {
    
                                                var value = parseInt($(this).find('.f_row1').val());
                                                value = isNaN(value) ? 0 : value;
                                                feature_row1 = (value > feature_row1) ? value : feature_row1;
    
                                            });
                                        });
    
                                        $.each(value.feature_details, function(index1, value1) {
    
                                            var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                                            selectElement = feature_values(selectElement,value.feature_details,value1.id);
    
                                            f_row = f_row + 1;
                                            var fv = value1.factor_value != null ? value1.factor_value : "";
    
                                            features = features + '<tr data-id="'+f_row+'">\n' +
                                            '\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                                            '                                                                                            <input value="'+value.id+'" type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                                            selectElement.prop('outerHTML') +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <input value="'+value1.value+'" class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <input value="'+fv+'" class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                                            '\n' +
                                            (value1.price_impact == 0 ? '<option selected value="0">{{__("text.No")}}</option>' : '<option value="0">{{__("text.No")}}</option>') +
                                            (value1.price_impact == 1 ? '<option selected value="1">{{__("text.Fixed")}}</option>' : '<option value="1">{{__("text.Fixed")}}</option>') +
                                            (value1.price_impact == 2 ? '<option selected value="2">{{__("text.m¹ Impact")}}</option>' : '<option value="2">{{__("text.m¹ Impact")}}</option>') +
                                            (value1.price_impact == 3 ? '<option selected value="3">{{__("text.m² Impact")}}</option>' : '<option value="3">{{__("text.m² Impact")}}</option>') +
                                            (value1.price_impact == 4 ? '<option selected value="4">{{__("text.Factor")}}</option>' : '<option value="4">{{__("text.Factor")}}</option>') +
                                            '\n' +
                                            '                                                                                            </select>\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                                            '\n' +
                                            (value1.impact_type == 0 ? '<option selected value="0">€</option>' : '<option value="0">€</option>') +
                                            (value1.impact_type == 1 ? '<option selected value="1">%</option>' : '<option value="1">%</option>') +
                                            '\n' +
                                            '                                                                                            </select>\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                        <td>\n' +
                                            '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                            '                                                                                        </td>\n' +
                                            '                                                                                    </tr>';
    
                                            var sub_features = '';
    
                                            if(value.sub_features.length == 0) {
    
                                                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
    
                                                feature_row1 = feature_row1 + 1;
    
                                                sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                                selectElement.prop('outerHTML') +
                                                '                                                                                        </td>\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                                '                                                                                        </td>\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                                '                                                                                        </td>\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                                '\n' +
                                                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                                '\n' +
                                                '                                                                                            </select>\n' +
                                                '                                                                                        </td>\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                                '\n' +
                                                '                                                                                                <option value="0">€</option>\n' +
                                                '                                                                                                <option value="1">%</option>\n' +
                                                '\n' +
                                                '                                                                                            </select>\n' +
                                                '                                                                                        </td>\n' +
                                                '                                                                                        <td>\n' +
                                                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                                '                                                                                        </td>\n' +
                                                '                                                                                    </tr>';
                                            }
                                            else
                                            {
                                                var flag = 0;
    
                                                $.each(value.sub_features, function(index2, value2) {
    
                                                    if(value1.id == value2.main_id)
                                                    {
                                                        var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                                        selectElement = feature_values(selectElement,value.sub_features,value2.id,value1.id);
    
                                                        feature_row1 = feature_row1 + 1;
                                                        var fv = value2.factor_value != null ? value2.factor_value : "";
    
                                                        sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                                        selectElement.prop('outerHTML') +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <input value="'+value2.value+'" class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <input value="'+fv+'" class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                                        '\n' +
                                                        (value2.price_impact == 0 ? '<option selected value="0">{{__("text.No")}}</option>' : '<option value="0">{{__("text.No")}}</option>') +
                                                        (value2.price_impact == 1 ? '<option selected value="1">{{__("text.Fixed")}}</option>' : '<option value="1">{{__("text.Fixed")}}</option>') +
                                                        (value2.price_impact == 2 ? '<option selected value="2">{{__("text.m¹ Impact")}}</option>' : '<option value="2">{{__("text.m¹ Impact")}}</option>') +
                                                        (value2.price_impact == 3 ? '<option selected value="3">{{__("text.m² Impact")}}</option>' : '<option value="3">{{__("text.m² Impact")}}</option>') +
                                                        (value2.price_impact == 4 ? '<option selected value="4">{{__("text.Factor")}}</option>' : '<option value="4">{{__("text.Factor")}}</option>') +
                                                        '\n' +
                                                        '                                                                                            </select>\n' +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                                        '\n' +
                                                        (value2.impact_type == 0 ? '<option selected value="0">€</option>' : '<option value="0">€</option>') +
                                                        (value2.impact_type == 1 ? '<option selected value="1">%</option>' : '<option value="1">%</option>') +
                                                        '\n' +
                                                        '                                                                                            </select>\n' +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                        <td>\n' +
                                                        '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                                        '                                                                                        </td>\n' +
                                                        '                                                                                    </tr>';
    
                                                        flag = 1;
                                                    }
    
                                                });
    
                                                if(flag == 0)
                                                {
                                                    var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                                                    selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
    
                                                    feature_row1 = feature_row1 + 1;
    
                                                    sub_features = sub_features + '<tr data-id="' + feature_row1 + '">\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <input type="hidden" name="f_rows' + f_row + '[]" class="f_row1" value="' + feature_row1 + '">' +
                                                    selectElement.prop('outerHTML') +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <input class="form-control feature_value1" name="feature_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <input class="form-control factor_value1" name="factor_values' + f_row + '[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <select class="form-control" name="price_impact' + f_row + '[]">\n\n' +
                                                    '\n' +
                                                    '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                                                    '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                                                    '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                                                    '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                                                    '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                                                    '\n' +
                                                    '                                                                                            </select>\n' +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <select class="form-control" name="impact_type' + f_row + '[]">\n\n' +
                                                    '\n' +
                                                    '                                                                                                <option value="0">€</option>\n' +
                                                    '                                                                                                <option value="1">%</option>\n' +
                                                    '\n' +
                                                    '                                                                                            </select>\n' +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                        <td>\n' +
                                                    '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                                                    '                                                                                        </td>\n' +
                                                    '                                                                                    </tr>';
                                                }
    
                                            }
    
                                            $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                                            '\n' +
                                            '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                                            '                                                                                            <thead>\n' +
                                            '                                                                                            <tr>\n' +
                                            '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                                            '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                                            '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                                            '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                                            '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                                            '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                                            '                                                                                            </tr>\n' +
                                            '                                                                                            </thead>\n' +
                                            '\n' +
                                            '                                                                                            <tbody>' +
                                            sub_features +
                                            '                                                                                    </tbody></table>' +
                                            '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                                            '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                                            '                                                                                        </div></div>');
    
                                        });
    
                                    }
    
                                    $('#primary-features').append('<div data-id="'+id+'" class="feature-table-container">\n' +
                                    '\n' +
                                    '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                                    '                                                                                        <thead>\n' +
                                    '                                                                                        <tr>\n' +
                                    '                                                                                            <th>{{__("text.Feature")}}</th>\n' +
                                    '                                                                                            <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
                                    '                                                                                            <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
                                    '                                                                                            <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
                                    '                                                                                            <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                                    '                                                                                            <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                                    '                                                                                            <th>{{__("text.Remove")}}</th>\n' +
                                    '                                                                                        </tr>\n' +
                                    '                                                                                        </thead>\n' +
                                    '\n' +
                                    '                                                                                        <tbody>' +
                                    features +
                                    '                                                                                    </tbody></table>' +
                                    '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                                    '                                                                                        <button data-id="'+id+'" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
                                    '                                                                                    </div></div>');
    
                                });
                            }

                            $(".feature_title").select2({
                                width: "100%",
                                height: '200px',
                                placeholder: "{{__("text.Feature Title")}}",
                                allowClear: true,
                            });

                            $(".feature_title1").select2({
                                width: "100%",
                                height: '200px',
                                placeholder: "{{__("text.Feature Title")}}",
                                allowClear: true,
                            });

                            $('.feature_title').each(function (index,value) {

                                label_update(this);

                            });
                        }
                    });
                }
                else
                {
                    $('#primary-features').find(".feature-table-container[data-id='" + id + "']").find('table tbody tr').each(function (index) {

                        $(this).find('.feature_heading').val(heading_id);
                        var title_id = $(this).find('.feature_title').val();
                        var title = $(this).find('.feature_title option:selected').text();
                        var f_row = $(this).find('.f_row').val();

                        $('#models-features-tables #features1').find('table', this).each(function (index) {

                            if($(this).find('tbody').find("[data-id='" + f_row + "']").length > 0)
                            {
                                if(!title || !heading)
                                {
                                    $(this).find('tbody').find("[data-id='" + f_row + "']").hide();
                                }
                                else
                                {
                                    $(this).find('tbody').find("[data-id='" + f_row + "']").find('td', this).each(function (index) {

                                        if(index == 1)
                                        {
                                            $(this).text(heading);
                                        }

                                        if(index == 2)
                                        {
                                            $(this).text(title);
                                        }

                                    });

                                    $(this).find('tbody').find("[data-id='" + f_row + "']").show();
                                }
                            }
                            else
                            {
                                if(title_id && heading_id)
                                {
                                    $(this).find('tbody').append('<tr data-id="'+f_row+'">' +
                                        '                                                                                 <td>\n' +
                                        '                                                                                <div style="display: flex;justify-content: center;align-items: center;">\n' +
                                        '                                                                                <input type="hidden" name="selected_model_feature'+f_row+'[]" id="price_impact" value="0">\n' +
                                        '                                                                                <label style="margin: 0;" class="switch">\n' +
                                        '                                                                                    <input class="price_impact" type="checkbox">\n' +
                                        '                                                                                    <span class="slider round"></span>\n' +
                                        '                                                                                </label>\n' +
                                        '                                                                                </div>\n' +
                                        '                                                                                </td>' +
                                        '                                                                                <td>'+heading+'</td><td>'+title+'</td></tr>');

                                    var $wrapper = $(this).find('tbody');

                                    $wrapper.find('tr').sort(function(a, b) {
                                        return +$(a).data('id') - +$(b).data('id');
                                    }).appendTo($wrapper);
                                }
                            }

                        });

                    });
                }
            }
        });

        function label_update(selector,main = null,id = null,title = null)
        {
            if(selector)
            {
                var id = $(selector).parent().find('.f_row').val();
                var main = $(selector).parents(".feature-table-container").data('id');
                var title_id = $(selector).val();
                var title = title_id ? $(selector).find("option:selected").text() : "";
            }
            
            var heading = $('.feature_box').find(".feature-row[data-id='" + main + "']").find('.js-data-example-ajax5 option:selected').text();
            var heading_id = $('.feature_box').find(".feature-row[data-id='" + main + "']").find('.js-data-example-ajax5').val();

            if(!heading_id)
            {
                heading = '';
            }

            $('#models-features-tables').find('table', this).each(function (index) {

                if($(this).find('tbody').find("[data-id='" + id + "']").length > 0)
                {
                    if(!title || !heading)
                    {
                        $(this).find('tbody').find("[data-id='" + id + "']").hide();
                    }
                    else
                    {
                        $(this).find('tbody').find("[data-id='" + id + "']").find('td', this).each(function (index) {

                            if(index == 1)
                            {
                                $(this).text(heading);
                            }

                            if(index == 2)
                            {
                                $(this).text(title);
                            }

                        });

                        $(this).find('tbody').find("[data-id='" + id + "']").show();
                    }
                }
                else
                {
                    if(title && heading)
                    {
                        $(this).find('tbody').append('<tr data-id="'+id+'">' +
                            '                                                                                 <td>\n' +
                            '                                                                                <div style="display: flex;justify-content: center;align-items: center;">\n' +
                            '                                                                                <input type="hidden" name="selected_model_feature'+id+'[]" id="price_impact" value="0">\n' +
                            '                                                                                <label style="margin: 0;" class="switch">\n' +
                            '                                                                                    <input class="price_impact" type="checkbox">\n' +
                            '                                                                                    <span class="slider round"></span>\n' +
                            '                                                                                </label>\n' +
                            '                                                                                </div>\n' +
                            '                                                                                </td>' +
                            '                                                                                <td>'+heading+'</td><td>'+title+'</td></tr>');

                        var $wrapper = $(this).find('tbody');

                        $wrapper.find('tr').sort(function(a, b) {
                            return +$(a).data('id') - +$(b).data('id');
                        }).appendTo($wrapper);

                    }
                }

            });
        }

        $('body').on('change', '.feature_title1', function() {

            var main = $(this).parents(".sub-feature-table-container").data('id');
            var title_id = $(this).val();

            if (title_id && ($('.sub-feature-table-container[data-id=' + main + ']').find('.feature_title1 option[value=' + title_id + ']:selected').length > 1)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This feature is already selected!',
                });
                
                $(this).val('').trigger('change.select2');
            }
        
        });

        $('body').on('change', '.feature_title', function() {

            var id = $(this).parent().find('.f_row').val();
            var main = $(this).parents(".feature-table-container").data('id');
            var title_id = $(this).val();
            var f_row = $(this).parents("tr").data("id");

            var f_row = $(this).parents("tr").data("id");

            $('#sub-features').find(".sub-feature-table-container[data-id='" + f_row + "']").find('table tbody tr .remove-sub-feature').each(function (index) {
                removeSubFeature(this,1);
            });

            if (title_id && ($('.feature-table-container[data-id=' + main + ']').find('.feature_title option[value=' + title_id + ']:selected').length > 1)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'This feature is already selected!',
                });
                
                $(this).val('').trigger('change.select2');
            }
            else
            {
                if($("#supplier_id").val())
                {
                    var ajax_data = "value_id=" + title_id + "&user_id=" + $("#supplier_id").val();
                }
                else
                {
                    var ajax_data = "value_id=" + title_id;
                }

                $.ajax({
                    type:"GET",
                    data: ajax_data,
                    url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                    success: function(data) {

                        var mySelect = $('#sub-features').find(".sub-feature-table-container[data-id='" + f_row + "']").find('table tbody tr .feature_title1');
                        mySelect.empty();

                        var option = $("<option>").text("{{__('text.Feature Title')}}").val("");
                        mySelect.append(option);

                        $.each(data, function(ind, val) {
                            var option = $("<option>").text(val.title).val(val.id);
                            mySelect.append(option);
                        });

                        mySelect.trigger('change.select2');
                    }
                });
            }

            label_update(this);

        });

        $(".add-product_btn").click(function () {

            var flag = 0;

            if(!$("input[name='margin']").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Margin should not be empty!',
                });
            }
            else if($("input[name='margin']").val() < 100)
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Margin should not be smaller than 100!',
                });
            }
            else if(!$("input[name='title']").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Title should not be empty!',
                });
            }
            else if(!$("input[name='slug']").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Slug should not be empty!',
                });
            }
            else if(!$(".js-data-example-ajax8").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Category should not be empty!',
                });
            }
            else if(!$(".js-data-example-ajax1").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Brand should not be empty!',
                });
            }
            /*else if(!$(".js-data-example-ajax2").val())
            {
                flag = 1;
                Swal.fire({
                    type: 'error',
                    title: 'Oops...',
                    text: 'Model should not be empty!',
                });
            }*/
            else if(!$(".base_price").val())
            {
                flag = 1;
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Base price should not be empty!',
                });
            }

            if(!flag)
            {
                $('#submit_check').val(1);
                $('#product_form').submit();
            }

        });

        $("#add-ladderband-btn").on('click',function() {


            $(".ladderband_products_box").append('<div class="form-group" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
                '\n' +
                '                                                            <div class="col-sm-2">\n' +
                '\n' +
                '                                                                <input class="form-control sub_code" name="sub_codes[]" id="blood_group_slug" placeholder="Sub Product ID" type="text">\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div class="col-sm-3">\n' +
                '\n' +
                '                                                                <input class="form-control sub_product_title" name="sub_product_titles[]" id="blood_group_slug" placeholder="Sub Product Title" type="text">\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                           <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">\n' +
                '\n' +
                '                                                                <input type="hidden" name="size1_value[]" id="size1_value" value="0">\n' +
                '\n' +
                '                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>\n' +
                '                                                                <label style="margin: 0;" class="switch">\n' +
                '                                                                    <input class="size1_value" type="checkbox">\n' +
                '                                                                    <span class="slider round"></span>\n' +
                '                                                                </label>\n' +
                '                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">\n' +
                '\n' +
                '                                                                <input type="hidden" name="size2_value[]" id="size2_value" value="0">\n' +
                '\n' +
                '                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>\n' +
                '                                                                <label style="margin: 0;" class="switch">\n' +
                '                                                                    <input class="size2_value" type="checkbox">\n' +
                '                                                                    <span class="slider round"></span>\n' +
                '                                                                </label>\n' +
                '                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div class="col-xs-1 col-sm-1">\n' +
                '                                                                <span class="ui-close remove-ladderband" data-id="" style="margin:0;right:70%;">X</span>\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                        </div>');


        });

        $('body').on('click', '.remove-ladderband' ,function() {

            var id = $(this).data('id');

            if(id)
            {
                rem_lad_arr.push(id);
                $('#removed_ladderband_rows').val(rem_lad_arr);
            }

            var parent = this.parentNode.parentNode;

            $(parent).hide();
            $(parent).remove();

            if($(".ladderband_products_box .form-group").length == 0)
            {
                $(".ladderband_products_box").append('<div class="form-group" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
                    '\n' +
                    '                                                            <div class="col-sm-2">\n' +
                    '\n' +
                    '                                                                <input class="form-control sub_code" name="sub_codes[]" id="blood_group_slug" placeholder="Sub Product ID" type="text">\n' +
                    '\n' +
                    '                                                            </div>\n' +
                    '\n' +
                    '                                                            <div class="col-sm-3">\n' +
                    '\n' +
                    '                                                                <input class="form-control sub_product_title" name="sub_product_titles[]" id="blood_group_slug" placeholder="Sub Product Title" type="text">\n' +
                    '\n' +
                    '                                                            </div>\n' +
                    '\n' +
                    '                                                           <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">\n' +
                    '\n' +
                    '                                                                <input type="hidden" name="size1_value[]" id="size1_value" value="0">\n' +
                    '\n' +
                    '                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>\n' +
                    '                                                                <label style="margin: 0;" class="switch">\n' +
                    '                                                                    <input class="size1_value" type="checkbox">\n' +
                    '                                                                    <span class="slider round"></span>\n' +
                    '                                                                </label>\n' +
                    '                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>\n' +
                    '\n' +
                    '                                                            </div>\n' +
                    '\n' +
                    '                                                            <div style="display: flex;align-items: center;height: 40px;justify-content: center;" class="col-sm-3">\n' +
                    '\n' +
                    '                                                                <input type="hidden" name="size2_value[]" id="size2_value" value="0">\n' +
                    '\n' +
                    '                                                                <span style="font-size: 13px;padding-right: 10px;font-weight: 600;font-family: monospace;">No</span>\n' +
                    '                                                                <label style="margin: 0;" class="switch">\n' +
                    '                                                                    <input class="size2_value" type="checkbox">\n' +
                    '                                                                    <span class="slider round"></span>\n' +
                    '                                                                </label>\n' +
                    '                                                                <span style="font-size: 13px;padding-left: 10px;font-weight: 600;font-family: monospace;">Yes</span>\n' +
                    '\n' +
                    '                                                            </div>\n' +
                    '\n' +
                    '                                                            <div class="col-xs-1 col-sm-1">\n' +
                    '                                                                <span class="ui-close remove-ladderband" data-id="" style="margin:0;right:70%;">X</span>\n' +
                    '                                                            </div>\n' +
                    '\n' +
                    '                                                        </div>');

            }

        });

        $('body').on('change', '.size1_value', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#size1_value').val(1);
            }
            else
            {
                $(this).parent().parent().find('#size1_value').val(0);
            }

        });

        $('body').on('change', '.size2_value', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#size2_value').val(1);
            }
            else
            {
                $(this).parent().parent().find('#size2_value').val(0);
            }

        });

        $(document).on('keypress', ".model_value, .model_factor_value, .max_size, .model_max_size, .model_max_width, .model_max_height, .model_factor_max_width, .curtain_variable_values", function(e){

            e = e || window.event;
            var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
            var val = String.fromCharCode(charCode);

            if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
            {
                e.preventDefault();
                return false;
            }

            if(e.which == 44)
            {
                if(this.value.indexOf(',') > -1)
                {
                    e.preventDefault();
                    return false;
                }
            }

            var num = $(this).attr("maskedFormat").toString().split(',');
            var regex = new RegExp("^\\d{0," + num[0] + "}(\\,\\d{0," + num[1] + "})?$");
            if (!regex.test(this.value)) {
                this.value = this.value.substring(0, this.value.length - 1);
            }

        });

        $(document).on('focusout', ".model_value, .model_factor_value, .max_size, .model_max_size, .model_max_width, .model_max_height, .model_factor_max_width, .curtain_variable_values", function(e){

            if($(this).val().slice($(this).val().length - 1) == ',')
            {
                var val = $(this).val();
                val = val + '00';
                $(this).val(val);
            }
        });

        $(document).on('keypress', ".color_max_height", function(e){

            e = e || window.event;
            var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
            var val = String.fromCharCode(charCode);

            if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
            {
                e.preventDefault();
                return false;
            }

            if(e.which == 44)
            {
                if(this.value.indexOf(',') > -1)
                {
                    e.preventDefault();
                    return false;
                }
            }

            var num = $(this).attr("maskedFormat").toString().split(',');
            var regex = new RegExp("^\\d{0," + num[0] + "}(\\,\\d{0," + num[1] + "})?$");
            if (!regex.test(this.value)) {
                this.value = this.value.substring(0, this.value.length - 1);
            }

        });

        $(document).on('focusout', ".color_max_height", function(e){

            if($(this).val().slice($(this).val().length - 1) == ',')
            {
                var val = $(this).val();
                val = val + '00';
                $(this).val(val);
            }
        });

        $(document).on('keypress', ".base_price, #margin_input", function(e){

            e = e || window.event;
            var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
            var val = String.fromCharCode(charCode);

            if (!val.match(/^[0-9]*\,?[0-9]*$/))  // For characters validation
            {
                e.preventDefault();
                return false;
            }

            if(e.which == 44)
            {
                e.preventDefault();
                return false;
            }

        });

        $(document).on('focusout', ".base_price", function(e){

            if(!$(this).val())
            {
                $(this).val(0);
            }

        });

        $('body').on('change', '.childsafe', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#childsafe').val(1);
            }
            else
            {
                $(this).parent().parent().find('#childsafe').val(0);
            }

        });

        $('body').on('change', '.curtain_variable_option', function() {

            if($(this).is(":checked"))
            {
                $(this).parents(".curtain_option_box").find('#curtain_variable_option').val(1);
            }
            else
            {
                $(this).parents(".curtain_option_box").find('#curtain_variable_option').val(0);
            }

        });

        $('body').on('change', '.curtain_type', function() {

            if($(this).val() == 1)
            {
                $(this).parents(".model-curtain").find('.model_factor_max_width').val(0);
                $(this).parents(".model-curtain").find('.model_factor_max_width').attr("readonly",true);
            }
            else
            {
                $(this).parents(".model-curtain").find('.model_factor_max_width').attr("readonly",false);
            }

        });

        $('body').on('change', '.ladderband', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#ladderband').val(1);
                $('#ladderband_box').show();
            }
            else
            {
                $(this).parent().parent().find('#ladderband').val(0);
                $('#ladderband_box').hide();
            }

        });

        $('body').on('change', '.ladderband_price_impact', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#ladderband_price_impact').val(1);
            }
            else
            {
                $(this).parent().parent().find('#ladderband_price_impact').val(0);
            }

        });

        $('body').on('change', '.ladderband_impact_type', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#ladderband_impact_type').val(1);
            }
            else
            {
                $(this).parent().parent().find('#ladderband_impact_type').val(0);
            }

        });

        $('body').on('change', '.price_impact', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#price_impact').val(1);
            }
            else
            {
                $(this).parent().parent().find('#price_impact').val(0);
            }

        });

        $('body').on('change', '.impact_type', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#impact_type').val(1);
            }
            else
            {
                $(this).parent().parent().find('#impact_type').val(0);
            }

        });

        $('body').on('change', '.m2_impact', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#m2_impact').val(1);
            }
            else
            {
                $(this).parent().parent().find('#m2_impact').val(0);
            }

        });

        $('body').on('change', '.width_impact', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#width_impact').val(1);
            }
            else
            {
                $(this).parent().parent().find('#width_impact').val(0);
            }

        });

        $('body').on('change', '.variable', function() {

            if($(this).is(":checked"))
            {
                $(this).parent().parent().find('#variable').val(1);
            }
            else
            {
                $(this).parent().parent().find('#variable').val(0);
            }

        });

        $('body').on('input', '.color_title', function() {

            var val = $(this).val();
            var id = $(this).parent().parent().attr("data-id");

            if(id)
            {
                $('#example1 tbody').find("[data-id='" + id + "']").find('.color_col').text(val);
            }

        });

        $('body').on('input', '.color_code', function() {

            var val = $(this).val();
            var id = $(this).parent().parent().attr("data-id");

            if(id)
            {
                $('#example1 tbody').find("[data-id='" + id + "']").find('.code_col').text(val);
            }

        });

        $('body').on('change', '.js-data-example-ajax4', function() {

            var id = this.value;
            var selector = this;
            var code = $(selector).parent().parent().find('.color_code').val();
            var color = $(selector).parent().parent().find('.color_title').val();
            var row_id = $(this).parent().parent().attr("data-id");

            $.ajax({
                type:"GET",
                data: "id=" + id ,
                url: "<?php echo url('/aanbieder/product/get-prices-tables')?>",
                success: function(data) {

                    $.each(data, function(index, value) {

                        if(row_id && $('#example1 tbody').find("[data-id='" + row_id + "']").length > 0)
                        {

                            $('#example1 tbody').find("[data-id='" + row_id + "']").find('td', this).each(function (index) {

                                if(index == 0)
                                {
                                    $(this).text(value.id);
                                }
                                else if(index == 1)
                                {
                                    $(this).text(value.title);
                                }
                                else if(index == 2)
                                {
                                    $(this).text(color);
                                }
                                else if(index == 3)
                                {
                                    $(this).text(code);
                                }
                                else if(index == 4)
                                {
                                    $(this).html('<a href="/aanbieder/price-tables/prices/view/'+value.id+'">View</a>');
                                }

                            })
                        }
                        else
                        {
                            $("#example1").append('<tr data-id="'+row_id+'"><td>'+value.id+'</td><td>'+value.title+'</td><td class="color_col">'+color+'</td><td class="code_col">'+code+'</td><td><a href="/aanbieder/price-tables/prices/view/'+value.id+'">View</a></td></tr>');
                            /*$(selector).parent().parent().attr('data-id',row);
                            row++;*/
                        }

                    });

                }
            });
        });

        $("#add-color-btn").on('click',function() {
            
            var color_row = $('.color_box').find('.form-group').last().data('id');
            color_row = color_row + 1;

            $(".color_box").append('<div class="form-group" data-id="'+color_row+'">\n' +
                '\n' +
                '                                                                <div class="col-sm-3">\n' +
                '\n' +
                '                                                                    <input class="form-control color_title" name="colors[]" id="blood_group_slug" placeholder="{{__('text.Color Title')}}" type="text">\n' +
                '\n' +
                '                                                                </div>\n' +
                '\n' +
                '                                                                <div class="col-sm-3">\n' +
                '\n' +
                '                                                                    <input class="form-control color_code" name="color_codes[]" id="blood_group_slug" placeholder="{{__('text.Color Code')}}" type="text">\n' +
                '\n' +
                '                                                                </div>\n' +
                '\n' +
                '                                                                <div class="col-sm-2">\n' +
                '\n' +
                '                                                                    <input class="form-control color_max_height" maskedformat="9,1" name="color_max_height[]" id="blood_group_slug" placeholder="{{__('text.Max Height')}}" type="text">\n' +
                '\n' +
                '                                                                </div>\n' +
                '\n' +
                '                                                                <div class="col-sm-3">\n' +
                '                                                                    <select class="form-control validate js-data-example-ajax4" name="price_tables[]">\n' +
                '\n' +
                '                                                                        <option value="">Select Price Table</option>\n' +
                '\n' +
                '                                                                        @foreach($tables as $table)\n' +
                '\n' +
                '                                                                            <option value="{{$table->id}}">{{$table->title}}</option>\n' +
                '\n' +
                '                                                                        @endforeach\n' +
                '\n' +
                '                                                                    </select>\n' +
                '                                                                </div>\n'+
                '\n' +
                '                <div class="col-xs-1 col-sm-1">\n' +
                '                <span class="ui-close remove-color" data-id="" style="margin:0;right:70%;">X</span>\n' +
                '                </div>\n' +
                '\n' +
                '                </div>');



            $(".js-data-example-ajax4").select2({
                width: '100%',
                height: '200px',
                placeholder: "Select Price Table",
                allowClear: true,
            });


        });

        $(document).on('click', "#add-primary-feature-btn", function(e){

            var id = $(this).data('id');
            var heading = $('.feature_box').find(".feature-row[data-id='" + id + "']").find('.js-data-example-ajax5').val();
            var feature_row = null;
            var feature_category = $('.js-data-example-ajax8').val();
            var feature_sub_category = $('.js-data-example-ajax9').val();

            $('#primary-features').find(".feature-table-container").each(function() {

                $(this).find('table tbody tr').each(function() {

                    var value = parseInt($(this).find('.f_row').val());
                    feature_row = (value > feature_row) ? value : feature_row;

                });
            });

            if($("#supplier_id").val())
            {
                var ajax_data = "id=" + feature_category + "&sub_id=" + feature_sub_category + '&heading_id=' + heading + "&user_id=" + $("#supplier_id").val();
            }
            else
            {
                var ajax_data = "id=" + feature_category + "&sub_id=" + feature_sub_category + '&heading_id=' + heading;
            }

            $.ajax({
                type:"GET",
                data: ajax_data,
                url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                success: function(data) {

                    var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                    
                    if(data.length)
                    {
                        selectElement = feature_values(selectElement,data[0].feature_details);
                    }
                    else
                    {
                        selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
                    }

                    feature_row = feature_row + 1;

                    $('#primary-features').find(".feature-table-container[data-id='" + id + "']").find('table').append('<tr data-id="'+feature_row+'">\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+feature_row+'">' +
                    '                                                                                            <input type="hidden" class="feature_heading" name="feature_headings[]" value="'+heading+'">\n' +
                    selectElement.prop('outerHTML') +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+feature_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                    '\n' +
                    '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                    '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                    '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                    '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                    '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                    '\n' +
                    '                                                                                            </select>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                    '\n' +
                    '                                                                                                <option value="0">€</option>\n' +
                    '                                                                                                <option value="1">%</option>\n' +
                    '\n' +
                    '                                                                                            </select>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                    </tr>');

                    var feature_row1 = null;
        
                    $('#sub-features').find(".sub-feature-table-container").each(function() {
        
                        $(this).find('table tbody tr').each(function() {
        
                            var value = parseInt($(this).find('.f_row1').val());
                            feature_row1 = (value > feature_row1) ? value : feature_row1;
        
                        });
                    });
        
                    var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + feature_row + "[]");
                    selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
        
                    feature_row1 = feature_row1 + 1;
        
                    $('#sub-features').append('<div data-id="'+feature_row+'" class="sub-feature-table-container">\n' +
                    '\n' +
                    '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                    '                                                                                            <thead>\n' +
                    '                                                                                            <tr>\n' +
                    '                                                                                                <th>{{__("text.Feature")}}</th>\n' +
                    '                                                                                                <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                    '                                                                                                <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                    '                                                                                                <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                    '                                                                                                <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                    '                                                                                                <th>{{__("text.Remove")}}</th>\n' +
                    '                                                                                            </tr>\n' +
                    '                                                                                            </thead>\n' +
                    '\n' +
                    '                                                                                            <tbody>' +
                    '                                                                                        <tr data-id="'+feature_row1+'">\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input type="hidden" name="f_rows'+feature_row+'[]" class="f_row1" value="'+feature_row1+'">' +
                    selectElement.prop('outerHTML') +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input class="form-control feature_value1" name="feature_values'+feature_row+'[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <input class="form-control factor_value1" name="factor_values'+feature_row+'[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <select class="form-control" name="price_impact'+feature_row+'[]">\n\n' +
                    '\n' +
                    '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                    '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                    '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                    '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                    '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                    '\n' +
                    '                                                                                            </select>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <select class="form-control" name="impact_type'+feature_row+'[]">\n\n' +
                    '\n' +
                    '                                                                                                <option value="0">€</option>\n' +
                    '                                                                                                <option value="1">%</option>\n' +
                    '\n' +
                    '                                                                                            </select>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                        <td>\n' +
                    '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                    '                                                                                        </td>\n' +
                    '                                                                                    </tr></tbody></table>' +
                    '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                    '                                                                                            <button data-id="'+feature_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                    '                                                                                        </div></div>');
        
                    $('.feature_title').select2({
                        width: "100%",
                        height: '200px',
                        placeholder: "{{__("text.Feature Title")}}",
                        allowClear: true,
                    });
        
                    $('.feature_title1').select2({
                        width: "100%",
                        height: '200px',
                        placeholder: "{{__("text.Feature Title")}}",
                        allowClear: true,
                    });
                }
            });

        });

        function SubFeatureRow(id,feature_row,selectElement)
        {
            $('#sub-features').find(".sub-feature-table-container[data-id='" + id + "']").find('table').append('<tr data-id="'+feature_row+'">\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input type="hidden" name="f_rows'+id+'[]" class="f_row1" value="'+feature_row+'">' +
            selectElement.prop('outerHTML') +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input class="form-control feature_value1" name="feature_values'+id+'[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}"  type="text">\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select class="form-control" name="price_impact'+id+'[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">No</option>\n' +
            '                                                                                                <option value="1">Fixed</option>\n' +
            '                                                                                                <option value="2">m¹ Impact</option>\n' +
            '                                                                                                <option value="3">m² Impact</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select class="form-control" name="impact_type'+id+'[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">€</option>\n' +
            '                                                                                                <option value="1">%</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
            '                                                                                        </td>\n' +
            '                                                                                    </tr>');

            $('.feature_title1').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Feature Title")}}",
                allowClear: true,
            });
        }

        function addSubFeature(selector,type = 0)
        {
            var id = $(selector).data('id');
            var value_id = $("#primary-features tr[data-id='" + id + "'] .feature_title").val();
            var feature_row = null;

            $('#sub-features').find(".sub-feature-table-container").each(function() {

                $(this).find('table tbody tr').each(function() {

                    var value = parseInt($(this).find('.f_row1').val());
                    feature_row = (value > feature_row) ? value : feature_row;

                });
                
            });

            feature_row = feature_row + 1;
            var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + id + "[]");

            if(type != 1)
            {
                if($("#supplier_id").val())
                {
                    var ajax_data = "value_id=" + value_id + "&user_id=" + $("#supplier_id").val();
                }
                else
                {
                    var ajax_data = "value_id=" + value_id;
                }

                $.ajax({
                    type:"GET",
                    data: ajax_data,
                    url: "<?php echo url('/aanbieder/product/get-features-data') ?>",
                    success: function(data) {
    
                        selectElement = feature_values(selectElement,data);
                        SubFeatureRow(id,feature_row,selectElement);
    
                    }
                });
            }
            else
            {
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));
                SubFeatureRow(id,feature_row,selectElement);
            }
        }

        $(document).on('click', "#add-sub-feature-btn", function(e){

            addSubFeature(this);

        });

        $("#add-feature-btn").on('click',function() {

            var heading_row = $('.feature_box').find('.feature-row').length > 0 ? $('.feature_box').find('.feature-row').last().data('id') : 0;
            heading_row = heading_row + 1;
            var f_row = null;

            $('#primary-features').find(".feature-table-container").each(function() {

                $(this).find('table tbody tr').each(function() {

                    var value = parseInt($(this).find('.f_row').val());
                    f_row = (value > f_row) ? value : f_row;

                });
            });

            f_row = f_row + 1;

            $(".feature_box").append('<div data-id="' + heading_row + '" class="form-group feature-row" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
            '\n' +
            '                                                                            <div class="col-sm-5">\n' +
            '\n' +
            '                                                                            <select class="form-control validate js-data-example-ajax5">\n' +
            '\n' +
            '                                                                                <option value="">Select Feature Heading</option>\n' +
            '\n' +
            '                                                                                @foreach($features_headings as $feature)\n' +
            '\n' +
            '                                                                                    <option value="{{$feature->id}}">{{$feature->title}}</option>\n' +
            '\n' +
            '                                                                                @endforeach\n' +
            '\n' +
            '                                                                            </select>\n' +
            '\n' +
            '                                                                        </div>\n' +
            '\n' +
            '                                                                    <div style="display: flex;" class="col-sm-5">\n' +
            '\n' +
            '                                                                        <button data-id="' + heading_row + '" style="margin-right: 10px;" class="btn btn-success create-feature-btn" type="button">{{__('text.Create/Edit Features')}}</button>\n' +
            '                                                                        <span class="ui-close remove-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;">X</span>\n' +
            '\n' +
            '                                                                    </div>\n' +
            '\n' +
            '\n' +
            '                </div>');

            var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
            selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

            $('#primary-features').append('<div data-id="'+heading_row+'" class="feature-table-container">\n' +
            '\n' +
            '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
            '                                                                                        <thead>\n' +
            '                                                                                        <tr>\n' +
            '                                                                                            <th>{{__("text.Feature")}}</th>\n' +
            '                                                                                            <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
            '                                                                                            <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
            '                                                                                            <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
            '                                                                                            <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
            '                                                                                            <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
            '                                                                                            <th>{{__("text.Remove")}}</th>\n' +
            '                                                                                        </tr>\n' +
            '                                                                                        </thead>\n' +
            '\n' +
            '                                                                                        <tbody>' +
            '                                                                                   <tr data-id="'+f_row+'">\n' +
            '\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
            '                                                                                            <input type="hidden" class="feature_heading" name="feature_headings[]">\n' +
            selectElement.prop('outerHTML') +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
            '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
            '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
            '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
            '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">€</option>\n' +
            '                                                                                                <option value="1">%</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
            '                                                                                        </td>\n' +
            '                                                                                    </tr></tbody></table>' +
            '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
            '                                                                                        <button data-id="'+heading_row+'" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
            '                                                                                    </div></div>');

            var feature_row1 = null;

            $('#sub-features').find(".sub-feature-table-container").each(function() {

                $(this).find('table tbody tr').each(function() {

                    var value = parseInt($(this).find('.f_row1').val());
                    feature_row1 = (value > feature_row1) ? value : feature_row1;

                });
            });

            var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
            selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

            feature_row1 = feature_row1 + 1;

            $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
            '\n' +
            '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
            '                                                                                            <thead>\n' +
            '                                                                                            <tr>\n' +
            '                                                                                               <th>{{__("text.Feature")}}</th>\n' +
            '                                                                                               <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
            '                                                                                               <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
            '                                                                                               <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
            '                                                                                               <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
            '                                                                                               <th>{{__("text.Remove")}}</th>\n' +
            '                                                                                            </tr>\n' +
            '                                                                                            </thead>\n' +
            '\n' +
            '                                                                                            <tbody>' +
            '                                                                                        <tr data-id="'+feature_row1+'">\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input type="hidden" name="f_rows'+f_row+'[]" class="f_row1" value="'+feature_row1+'">' +
            selectElement.prop('outerHTML') +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input class="form-control feature_value1" name="feature_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <input class="form-control factor_value1" name="factor_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select class="form-control" name="price_impact'+f_row+'[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
            '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
            '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
            '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
            '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <select class="form-control" name="impact_type'+f_row+'[]">\n\n' +
            '\n' +
            '                                                                                                <option value="0">€</option>\n' +
            '                                                                                                <option value="1">%</option>\n' +
            '\n' +
            '                                                                                            </select>\n' +
            '                                                                                        </td>\n' +
            '                                                                                        <td>\n' +
            '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
            '                                                                                        </td>\n' +
            '                                                                                    </tr></tbody></table>' +
            '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
            '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
            '                                                                                        </div></div>');

            $(".js-data-example-ajax5").select2({
                width: '100%',
                height: '200px',
                placeholder: "{{__('text.Select Feature Heading Placeholder')}}",
                allowClear: true,
            });

            $('.feature_title').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Feature Title")}}",
                allowClear: true,
            });

            $('.feature_title1').select2({
                width: "100%",
                height: '200px',
                placeholder: "{{__("text.Feature Title")}}",
                allowClear: true,
            });

        });

        $(".js-data-example-ajax8").select2({
            width: '100%',
            height: '200px',
            placeholder: "{{__('text.Select category placeholder')}}",
            allowClear: true,
        });

        $(".js-data-example-ajax9").select2({
            width: '100%',
            height: '200px',
            placeholder: "{{__('text.Select sub category')}}",
            allowClear: true,
        });

        $(".js-data-example-ajax1").select2({
            width: '100%',
            height: '200px',
            placeholder: "{{__('text.Select Brand placeholder')}}",
            allowClear: true,
        });

        $(".js-data-example-ajax3").select2({
            width: '100%',
            height: '200px',
            placeholder: "{{__('text.Select Measure placeholder')}}",
            allowClear: true,
        });

        /*$(".js-data-example-ajax2").select2({
            width: '80%',
            height: '200px',
            placeholder: "Select Model",
            allowClear: true,
        });*/


        $(".js-data-example-ajax4").select2({
            width: '100%',
            height: '200px',
            placeholder: "Select Price Table",
            allowClear: true,
        });

        $(".js-data-example-ajax5").select2({
            width: '100%',
            height: '200px',
            placeholder: "{{__('text.Select Feature Heading Placeholder')}}",
            allowClear: true,
        });

        $(".js-data-example-ajax7").select2({
            width: '100%',
            height: '200px',
            placeholder: "Select Feature",
            allowClear: true,
        });

        $('.feature_title').select2({
            width: "100%",
            height: '200px',
            placeholder: "{{__("text.Feature Title")}}",
            allowClear: true,
        });

        $('.feature_title1').select2({
            width: "100%",
            height: '200px',
            placeholder: "{{__("text.Feature Title")}}",
            allowClear: true,
        });

        /*$('.js-data-example-ajax1').on('change', function() {

            var brand_id = $(this).val();
            var options = '';

            $.ajax({
                type:"GET",
                data: "id=" + brand_id ,
                url: "<?php echo url('/aanbieder/product/products-models-by-brands')?>",
                success: function(data) {

                    $.each(data, function(index, value) {

                        var opt = '<option value="'+value.id+'" >'+value.cat_slug+'</option>';

                        options = options + opt;

                    });

                    $('.js-data-example-ajax2').find('option')
                        .remove()
                        .end()
                        .append('<option value="">Select Model</option>'+options);

                }
            });

        });*/

        $('#uploadTrigger').on('click', function() {

            uploadclick();

        });

        function uploadclick(){
            $("#uploadFile").click();
            $("#uploadFile").change(function(event) {
                readURL(this);
                $("#uploadTrigger").html($("#uploadFile").val());
            });
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#adminimg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('body').on('click', '.remove-color' ,function() {

            var parent = this.parentNode.parentNode;
            var id = $(this).parent().parent().attr("data-id");

            if(id)
            {
                $('#example1 tbody').find("[data-id='" + id + "']").remove();
            }

            var rem_id = $(this).data('id');

            if(rem_id)
            {
                rem_col_arr.push(rem_id);
                $('#removed_colors').val(rem_col_arr);
            }

            $(parent).hide();
            $(parent).remove();

            if($(".color_box .form-group").length == 0)
            {
                $(".color_box").append('<div class="form-group" data-id="1">\n' +
                    '\n' +
                    '                                                                <div class="col-sm-3">\n' +
                    '\n' +
                    '                                                                    <input class="form-control color_title" name="colors[]" id="blood_group_slug" placeholder="{{__('text.Color Title')}}" type="text">\n' +
                    '\n' +
                    '                                                                </div>\n' +
                    '\n' +
                    '                                                                <div class="col-sm-3">\n' +
                    '\n' +
                    '                                                                    <input class="form-control color_code" name="color_codes[]" id="blood_group_slug" placeholder="{{__('text.Color Code')}}" type="text">\n' +
                    '\n' +
                    '                                                                </div>\n' +
                    '\n' +
                    '                                                                <div class="col-sm-2">\n' +
                    '\n' +
                    '                                                                    <input class="form-control color_max_height" maskedformat="9,1" name="color_max_height[]" id="blood_group_slug" placeholder="{{__('text.Max Height')}}" type="text">\n' +
                    '\n' +
                    '                                                                </div>\n' +
                    '\n' +
                    '                                                                <div class="col-sm-3">\n' +
                    '                                                                    <select class="form-control validate js-data-example-ajax4" name="price_tables[]">\n' +
                    '\n' +
                    '                                                                        <option value="">Select Price Table</option>\n' +
                    '\n' +
                    '                                                                        @foreach($tables as $table)\n' +
                    '\n' +
                    '                                                                            <option value="{{$table->id}}">{{$table->title}}</option>\n' +
                    '\n' +
                    '                                                                        @endforeach\n' +
                    '\n' +
                    '                                                                    </select>\n' +
                    '                                                                </div>\n'+
                    '\n' +
                    '                <div class="col-xs-1 col-sm-1">\n' +
                    '                <span class="ui-close remove-color" data-id="" style="margin:0;right:70%;">X</span>\n' +
                    '                </div>\n' +
                    '\n' +
                    '                </div>');


                $(".js-data-example-ajax4").select2({
                    width: '100%',
                    height: '200px',
                    placeholder: "Select Price Table",
                    allowClear: true,
                });


            }

        });

        function removeSubFeature(selector,type = 0)
        {
            var id = $(selector).data('id');
            var row_id = $(selector).parents("tr").data('id');
            var heading_id = $(selector).parents(".sub-feature-table-container").data('id');
            var add_btn = $(selector).parents(".sub-feature-table-container").find("#add-sub-feature-btn");

            if(id)
            {
                rem_arr.push(id);
                $('#removed_rows').val(rem_arr);
            }

            $('#sub-features').find(".sub-feature-table-container[data-id='" + heading_id + "']").find("table tbody tr[data-id='" + row_id + "']").remove();

            if($('#sub-features').find(".sub-feature-table-container[data-id='" + heading_id + "']").find("table tbody tr").length == 0)
            {
                addSubFeature(add_btn,type);
            }
        }

        $('body').on('click', '.remove-sub-feature' ,function() {

            removeSubFeature(this);

        });

        $('body').on('click', '.remove-primary-feature' ,function() {

            var id = $(this).data('id');
            var row_id = $(this).parent().parent().parent().data('id');
            var heading_id = $(this).parent().parent().parent().parent().parent().parent().data('id');
            var heading = $('.feature_box').find(".feature-row[data-id='" + heading_id + "']").find('.js-data-example-ajax5').val();
            var f_row = null;

            $('#primary-features').find(".feature-table-container").each(function() {

                $(this).find('table tbody tr').each(function() {

                    var value = parseInt($(this).find('.f_row').val());
                    f_row = (value > f_row) ? value : f_row;

                });
            });

            f_row = f_row + 1;

            $('#models-features-tables #features1 table tbody').find("[data-id='" + row_id + "']").remove();

            if(id)
            {
                rem_arr.push(id);

                $('#sub-features').find(".sub-feature-table-container[data-id='" + row_id + "']").find('table tbody tr').each(function (index) {

                    if($(this).find('.remove-sub-feature').data('id'))
                    {
                        rem_arr.push($(this).find('.remove-sub-feature').data('id'));
                    }

                });

                $('#removed_rows').val(rem_arr);
            }

            $('#primary-features').find(".feature-table-container[data-id='" + heading_id + "']").find("table tbody tr[data-id='" + row_id + "']").remove();
            $('#sub-features').find(".sub-feature-table-container[data-id='" + row_id + "']").remove();

            if($('#primary-features').find(".feature-table-container[data-id='" + heading_id + "']").find("table tbody tr").length == 0)
            {
                var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                $('#primary-features').find(".feature-table-container[data-id='" + heading_id + "']").find("table").append('<tr data-id="'+f_row+'">\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="'+f_row+'">' +
                '                                                                                            <input type="hidden" class="feature_heading" name="feature_headings[]" value="'+heading+'">\n' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <button style="width: 100%;white-space: normal;" data-id="'+f_row+'" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr>');

                var f_row1 = null;

                $('#sub-features').find(".sub-feature-table-container").each(function() {

                    $(this).find('table tbody tr').each(function() {

                        var value = parseInt($(this).find('.f_row1').val());
                        f_row1 = (value > f_row1) ? value : f_row1;

                    });
                });

                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features" + f_row + "[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                f_row1 = f_row1 + 1;

                $('#sub-features').append('<div data-id="'+f_row+'" class="sub-feature-table-container">\n' +
                '\n' +
                '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                '                                                                                            <thead>\n' +
                '                                                                                            <tr>\n' +
                '                                                                                                <th>Feature</th>\n' +
                '                                                                                                <th>Value</th>\n' +
                '                                                                                                <th>Price Impact</th>\n' +
                '                                                                                                <th>Impact Type</th>\n' +
                '                                                                                                <th>Remove</th>\n' +
                '                                                                                            </tr>\n' +
                '                                                                                            </thead>\n' +
                '\n' +
                '                                                                                            <tbody>' +
                '                                                                                        <tr data-id="'+f_row1+'">\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows'+f_row+'[]" class="f_row1" value="'+f_row1+'">' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value1" name="feature_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value1" name="factor_values'+f_row+'[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="price_impact'+f_row+'[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="impact_type'+f_row+'[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr></tbody></table>' +
                '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                '                                                                                            <button data-id="'+f_row+'" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                '                                                                                        </div></div>');

                $('.feature_title').select2({
                    width: "100%",
                    height: '200px',
                    placeholder: "{{__("text.Feature Title")}}",
                    allowClear: true,
                });

                $('.feature_title1').select2({
                    width: "100%",
                    height: '200px',
                    placeholder: "{{__("text.Feature Title")}}",
                    allowClear: true,
                });

            }

        });

        $('body').on('click', '.remove-feature' ,function() {

            var id = $(this).data('id');
            var row_id = $(this).parent().parent().data('id');

            $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").find('table tbody tr').each(function (index) {

                var row = $(this).find('.f_row').val();

                if($(this).find('.remove-primary-feature').data('id'))
                {
                    rem_arr.push($(this).find('.remove-primary-feature').data('id'));
                }

                $('#models-features-tables #features1 table tbody').find("[data-id='" + row + "']").remove();

                $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").find('table tbody tr').each(function (index) {

                    if($(this).find('.remove-sub-feature').data('id'))
                    {
                        rem_arr.push($(this).find('.remove-sub-feature').data('id'));
                    }

                });

                $('#sub-features').find(".sub-feature-table-container[data-id='" + row + "']").remove();

            });

            $('#primary-features').find(".feature-table-container[data-id='" + row_id + "']").remove();


            if(id)
            {
                $('#removed_rows').val(rem_arr);
            }

            var parent = this.parentNode.parentNode;

            $(parent).hide();
            $(parent).remove();

            if($(".feature_box .form-group").length == 0)
            {
                $(".feature_box").append('<div data-id="1" class="form-group feature-row" style="margin: 0 0 20px 0;display: flex;justify-content: center;">\n' +
                '\n' +
                '                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="1">\n' +
                '\n' +
                '                                                                            <div class="col-sm-5">\n' +
                '\n' +
                '                                                                            <select class="form-control validate js-data-example-ajax5">\n' +
                '\n' +
                '                                                                                <option value="">Select Feature Heading</option>\n' +
                '\n' +
                '                                                                                @foreach($features_headings as $feature)\n' +
                '\n' +
                '                                                                                    <option value="{{$feature->id}}">{{$feature->title}}</option>\n' +
                '\n' +
                '                                                                                @endforeach\n' +
                '\n' +
                '                                                                            </select>\n' +
                '\n' +
                '                                                                        </div>\n'+
                '\n' +
                '                                                                    <div style="display: flex;" class="col-sm-5">\n' +
                '\n' +
                '                                                                        <button data-id="1" style="margin-right: 10px;" class="btn btn-success create-feature-btn" type="button">{{__('text.Create/Edit Features')}}</button>\n' +
                '                                                                        <span class="ui-close remove-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;">X</span>\n' +
                '\n' +
                '                                                                    </div>\n' +
                '\n' +
                '\n' +
                '                </div>');

                var selectElement = $("<select>").addClass("feature_title").attr("name", "features[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                $('#primary-features').append('<div data-id="1" class="feature-table-container">\n' +
                '\n' +
                '                                                                                    <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                '                                                                                        <thead>\n' +
                '                                                                                        <tr>\n' +
                '                                                                                               <th>{{__("text.Feature")}}</th>\n' +
                '                                                                                               <th style="width: 10%;">{{__("text.Value")}}</th>\n' +
                '                                                                                               <th style="width: 10%;">{{__("text.Factor")}}</th>\n' +
                '                                                                                               <th style="width: 10%;">{{__("text.Sub Feature")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                '                                                                                               <th>{{__("text.Remove")}}</th>\n' +
                '                                                                                        </tr>\n' +
                '                                                                                        </thead>\n' +
                '\n' +
                '                                                                                        <tbody>' +
                '                                                                                   <tr data-id="1">\n' +
                '\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows[]" class="f_row" value="1">' +
                '                                                                                            <input type="hidden" class="feature_heading" name="feature_headings[]">\n' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value" name="feature_values[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value" name="factor_values[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <button style="width: 100%;white-space: normal;" data-id="1" class="btn btn-success create-sub-feature-btn" type="button">{{__('text.Create/Edit Sub Features')}}</button>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="price_impact[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select style="padding: 5px;" class="form-control" name="impact_type[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-primary-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr></tbody></table>' +
                '                                                                                    <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                '                                                                                        <button data-id="1" class="btn btn-default featured-btn" type="button" id="add-primary-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add More Features')}}</button>\n' +
                '                                                                                    </div></div>');

                var selectElement = $("<select>").addClass("feature_title1").attr("name", "features1[]");
                selectElement.append($("<option>").text('{{__("text.Feature Title")}}').attr("value", ""));

                $('#sub-features').append('<div data-id="1" class="sub-feature-table-container">\n' +
                '\n' +
                '                                                                                        <table style="margin: auto;width: 95%;border-collapse: separate;">\n' +
                '                                                                                            <thead>\n' +
                '                                                                                            <tr>\n' +
                '                                                                                               <th>{{__("text.Feature")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Value")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Factor")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Price Impact")}}</th>\n' +
                '                                                                                               <th style="width: 15%;">{{__("text.Impact Type")}}</th>\n' +
                '                                                                                               <th>{{__("text.Remove")}}</th>\n' +
                '                                                                                            </tr>\n' +
                '                                                                                            </thead>\n' +
                '\n' +
                '                                                                                            <tbody>' +
                '                                                                                        <tr data-id="1">\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input type="hidden" name="f_rows1[]" class="f_row1" value="1">' +
                selectElement.prop('outerHTML') +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control feature_value1" name="feature_values1[]" id="blood_group_slug" placeholder="{{__('text.Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <input class="form-control factor_value1" name="factor_values1[]" id="blood_group_slug" placeholder="{{__('text.Factor Value Placeholder')}}" type="text">\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="price_impact1[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">{{__("text.No")}}</option>\n' +
                '                                                                                                <option value="1">{{__("text.Fixed")}}</option>\n' +
                '                                                                                                <option value="2">{{__("text.m¹ Impact")}}</option>\n' +
                '                                                                                                <option value="3">{{__("text.m² Impact")}}</option>\n' +
                '                                                                                                <option value="4">{{__("text.Factor")}}</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <select class="form-control" name="impact_type1[]">\n\n' +
                '\n' +
                '                                                                                                <option value="0">€</option>\n' +
                '                                                                                                <option value="1">%</option>\n' +
                '\n' +
                '                                                                                            </select>\n' +
                '                                                                                        </td>\n' +
                '                                                                                        <td>\n' +
                '                                                                                            <div style="display: flex;justify-content: center;"><span class="ui-close remove-sub-feature" data-id="" style="margin:0;position: relative;left: 0;right: 0;top: 0;">X</span></div>\n' +
                '                                                                                        </td>\n' +
                '                                                                                    </tr></tbody></table>' +
                '                                                                                        <div style="margin-top: 20px;" class="col-sm-12 text-center">\n' +
                '                                                                                            <button data-id="1" class="btn btn-default featured-btn" type="button" id="add-sub-feature-btn"><i class="fa fa-plus"></i> {{__('text.Add more sub features')}}</button>\n' +
                '                                                                                        </div></div>');

                $(".js-data-example-ajax5").select2({
                    width: '100%',
                    height: '200px',
                    placeholder: "{{__('text.Select Feature Heading Placeholder')}}",
                    allowClear: true,
                });

                $('.feature_title').select2({
                    width: "100%",
                    height: '200px',
                    placeholder: "{{__("text.Feature Title")}}",
                    allowClear: true,
                });

                $('.feature_title1').select2({
                    width: "100%",
                    height: '200px',
                    placeholder: "{{__("text.Feature Title")}}",
                    allowClear: true,
                });
            }

        });

    });

</script>

<style type="text/css">

    .autocomplete {
        position: relative;
        display: inline-block;
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        z-index: 99;
        max-height: 230px;
        overflow-x: hidden;
        overflow-y: auto;
        width: 100%;
    }

    .autocomplete-items div {
        padding: 10px;
        cursor: pointer;
        background-color: #fff;
        border-bottom: 1px solid #d4d4d4;
    }

    .autocomplete-items div:last-child
    {
        border-bottom: 0;
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9;
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important;
        color: #ffffff;
    }

    th:first-child,td:first-child
    {
        border-left: 1px solid #c5c5c5;
    }

    th
    {
        border-top: 1px solid #c5c5c5;
        border-bottom: 1px solid #c5c5c5;
    }

    td
    {
        border-bottom: 1px solid #c5c5c5;
    }

    th,td
    {
        padding: 10px;
        font-family: monospace;
        color: #4f4f4f;
        text-align: center;
        border-right: 1px solid #c5c5c5;
    }

    .container1 {
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        padding-left: 35px;
        margin-bottom: 0;
        cursor: pointer;
        font-size: 17px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default radio button */
    .container1 input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: #eee;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* On mouse-over, add a grey background color */
    .container1:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .container1 input:checked ~ .checkmark {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: relative;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .container1 input:checked ~ .checkmark:after {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Style the indicator (dot/circle) */
    .container1 .checkmark:after {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: white;
    }

    .table.products > tbody > tr td
    {
        border-right: 1px solid #e3e3e3;
        text-align: center;
    }

    .table.products > tbody > tr td:first-child
    {
        border-left: 1px solid #e3e3e3;
    }

    .table.products > tbody > tr td:last-child
    {
        border-right: 1px solid #e3e3e3;
    }

    .product-configuration a[aria-expanded="false"]::before, a[aria-expanded="true"]::before
    {
        display: none;
    }

    .product-configuration a[aria-expanded="true"]::before
    {
        display: none;
    }

    .select2-selection
    {
        height: 40px !important;
        display: flex !important;
        align-items: center;
        justify-content: space-between;
    }

    .select2-selection__rendered
    {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow
    {
        position: relative !important;
        top: 0 !important;
    }

  .swal2-show
  {
    width: 30%;

  }

  .swal2-header
  {
    font-size: 14px;
  }

  .swal2-content
  {
    font-size: 20px;
  }

  .swal2-actions
  {
    font-size: 12px;
  }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 13px;
        width: 13px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>


@endsection
