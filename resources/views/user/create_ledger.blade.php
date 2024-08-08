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
                                        <h2>{{isset($ledger) ? __('text.Edit Ledger') : __('text.Create Ledger')}}</h2>
                                        <a href="{{route('general-ledgers')}}" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> {{__('text.Back')}}</a>
                                    </div>
                                    <hr>
                                    <form class="form-horizontal" action="{{route('general-ledgers')}}" method="POST" enctype="multipart/form-data">
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                        {{csrf_field()}}

                                        <input type="hidden" name="ledger_id" value="{{isset($ledger->id) ? $ledger->id : null}}">

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Title')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($ledger->title) ? $ledger->title : old('title')}}" name="title" placeholder="" required="" type="text">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="">{{__('text.Ledger Number')}}*</label>
                                            <div class="col-sm-6">
                                                <input class="form-control" value="{{isset($ledger->number) ? $ledger->number : old('number')}}" name="number" placeholder="" required="" type="text">
                                            </div>
                                        </div>

                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" id="submit" type="submit" class="btn add-product_btn">{{isset($ledger) ? __('text.Edit Ledger') : __('text.Create Ledger')}}</button>
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

@endsection
