@extends('layouts.handyman')

@section('content')

    <div class="right-side">

        <div class="container-fluid">
            <div class="row">

                <div style="margin: 0;" class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <!-- Starting of Dashboard data-table area -->
                        <div class="section-padding add-product-1" style="padding: 0;">

                            <div style="margin: 0;" class="row">
                                <div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div style="box-shadow: none;" class="add-product-box">
                                        <div class="add-product-header products">

                                            <h2>{{__('text.Quotation Details')}}</h2>

                                        </div>
                                        <hr style="margin-bottom: 0;">
                                        <div>
                                            
                                            @include('includes.form-success')

                                            <div style="padding: 0;" class="form-horizontal">

                                                @include('user.checklist')

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

    </div>

@endsection

@section('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/moment.min.js"></script>
    <script src="{{asset('assets/admin/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.21.0/locale/nl.js"></script>
    @include('user.checklist_js')

@endsection
