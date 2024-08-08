@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard area -->
                    <div style="padding: 0;" class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="add-product-header">
                                        <h2>{{__('text.Agenda Background Color')}}</h2>
                                        <a href="{{route('planning-responsible-persons')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                    </div>
                                    <hr>
                                    <form class="form-horizontal" action="{{route('store-planning-responsible-person')}}" method="POST" enctype="multipart/form-data">
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        {{csrf_field()}}

                                        <input type="hidden" name="person_id" value="{{$person->id}}">

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Font Color')}}</label>
                                            <div class="col-sm-6">
                                                <span id="cp1" class="cp1 input-group colorpicker-component">
                                                    <input class="cp1 form-control" type="text" name="color" value="{{$person->agenda_font_color}}" />
                                                    <span class="input-group-addon"><i></i></span>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" id="submit" type="submit" class="btn add-product_btn">{{__('text.Submit')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ending of Dashboard area -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script type="text/javascript">

        $('.cp1').colorpicker();

        $("#address").on('input',function(e){
            $(this).next('input').val(0);
        });

        $("#address").focusout(function(){

            var check = $(this).next('input').val();

            if(check == 0)
            {
                $(this).val('');
                $('#postcode').val('');
                $("#city").val('');
            }
        });

    </script>


@endsection
