<!DOCTYPE html>
<html style="position: relative;height: 100%;" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="{{$seo->meta_keys}}">
    <meta name="author" content="GeniusOcean">

    @yield('chat')
    
    <title>{{$gs->title}}</title>

    @if(Route::currentRouteName() != 'chat')
        <link href="{{asset('assets/admin/css/bootstrap.min.css')}}" rel="stylesheet">
    @endif
    <link href="{{asset('assets/admin/css/font-awesome.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/perfect-scrollbar.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/bootstrap-colorpicker.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/responsive.css')}}" rel="stylesheet">
    <link href="{{asset('assets/admin/css/c3.css')}}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{asset('assets/images/'.$gs->favicon)}}">

    <link href="{{ asset('assets/front/css/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/slicknav.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/responsive.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{asset('assets/images/'.$gs->favicon)}}">
    <link href="{{ asset('assets/front/select2/select2.min.css') }}" rel="stylesheet">
    <script src="{{asset('assets/admin/js/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/front/select2/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@9/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.2/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css">
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{ asset('assets/admin/js/d3-5.8.2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/c3.min.js') }}"></script>
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>

@include('styles.admin-design')

@yield('styles')

<!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-165295462-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-165295462-1');
        var alertTimeout;
    </script>

</head>

<body @if(Route::currentRouteName() == 'chat') class="chats-tab-open" @endif style="overflow: hidden;height: 100%;">

<div style="padding: 20px 0;border-bottom: 2px solid #0090e3c9;position: fixed;width: 100%;z-index: 1000;" class="container-fluid top-bar">

    <div style="display: flex;flex-direction: row;align-items: center;">

        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">

            <button style="outline: none !important;background: #5bc0de !important;border-color: #46b8da !important;" type="button" id="sidebarCollapse1" class="btn btn-info">
                <i class="fa fa-align-left"></i>
            </button>

        </div>

        <div style="display: flex;justify-content: space-between;" class="col-lg-10 col-md-10 col-sm-10 col-xs-10">

            <div class="wrapper-options">

                @if(Route::currentRouteName() == 'create-custom-quotation' || Route::currentRouteName() == 'create-new-quotation')

                    <input {{Route::currentRouteName() == 'create-custom-quotation' ? 'checked' : null}} type="radio" value="{{route('create-custom-quotation')}}" class="select-form" name="select" id="option-1">

                    <label for="option-1" class="option option-1">
                        <div class="dot"></div>
                        <span>Vloeren</span>
                    </label>

                    <input {{Route::currentRouteName() == 'create-new-quotation' ? 'checked' : null}} type="radio" value="{{route('create-new-quotation')}}" class="select-form" name="select" id="option-2">

                    <label for="option-2" class="option option-2">
                        <div class="dot"></div>
                        <span>Binnen zonwering</span>
                    </label>

                @elseif(Route::currentRouteName() == 'admin-product-create' || Route::currentRouteName() == 'admin-product-edit')

                    @if($supplier_global_categories[0])

                        <input {{$categories[0]->cat_name == $supplier_global_categories[0]->cat_name ? 'checked' : null}} type="radio" value="{{route('admin-product-create',['cat' => $supplier_global_categories[0]->cat_name])}}" class="select-form" name="select" id="option-1">

                        <label for="option-1" class="option option-1">
                            <div class="dot"></div>
                            <span>Vloeren</span>
                        </label>

                    @endif

                    @if($supplier_global_categories[1])

                        <input {{$categories[0]->cat_name == $supplier_global_categories[1]->cat_name ? 'checked' : null}} type="radio" value="{{route('admin-product-create',['cat' => $supplier_global_categories[1]->cat_name])}}" class="select-form" name="select" id="option-2">

                        <label for="option-2" class="option option-2">
                            <div class="dot"></div>
                            <span>Binnen zonwering</span>
                        </label>

                    @endif

                @endif

                <style>

                    @import url('https://fonts.googleapis.com/css?family=Lato:400,500,600,700&display=swap');
                        
                    .wrapper-options{
                        display: inline-flex;
                        width: 600px;
                        align-items: center;
                        justify-content: space-evenly;
                        border-radius: 5px;
                        padding: 0;
                    }
                            
                    .wrapper-options .option{
                        background: #fff;
                        height: 100%;
                        width: 100%;
                        display: flex;
                        align-items: center;
                        justify-content: flex-start;
                        margin: 0 10px;
                        border-radius: 5px;
                        cursor: pointer;
                        padding: 0 10px;
                        border: 2px solid lightgrey;
                        transition: all 0.3s ease;
                    }
                            
                    .wrapper-options .option .dot{
                        height: 20px;
                        width: 20px;
                        background: #d9d9d9;
                        border-radius: 50%;
                        position: relative;
                    }
                            
                    .wrapper-options .option .dot::before{
                        position: absolute;
                        content: "";
                        top: 4px;
                        left: 4px;
                        width: 12px;
                        height: 12px;
                        background: #0069d9;
                        border-radius: 50%;
                        opacity: 0;
                        transform: scale(1.5);
                        transition: all 0.3s ease;
                    }
                            
                    .wrapper-options input[type="radio"]{
                        display: none;
                    }
                            
                    .wrapper-options input[type="radio"]:checked + label{
                        border-color: #0069d9;
                        background: #0069d9;
                    }
                            
                    .wrapper-options input[type="radio"]:checked + label .dot{
                        background: #fff;
                    }
                            
                    .wrapper-options input[type="radio"]:checked + label .dot::before{
                        opacity: 1;
                        transform: scale(1);
                    }
                            
                    .wrapper-options .option span{
                        font-size: 20px;
                        color: #808080;
                        margin-left: 10px;
                        margin-top: -1px;
                    }
                            
                    .wrapper-options input[type="radio"]:checked + label span{
                        color: #fff;
                    }

                    #homeSubmenu
                    {
                        top: 25px;
                    }

                </style>

                <script>

                    $('.select-form').click(function () {

                        window.location.href = $(this).val();

                    });

                </script>

            </div>

            <div style="display: flex;align-items: center;">

                @if(Route::currentRouteName() != 'chat')

                    <button style="padding: 0 5px;margin-right: 10px;line-height: 2.5;background-color: white;border-color: #c1c1c1;" type="button" class="btn btn-primary add-note">
                        <svg style="color: #484848;" xmlns="http://www.w3.org/2000/svg" width="30" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                            <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"></path>
                        </svg>
                    </button>

                @endif
                
                <form style="margin-right: 10px;margin-bottom: 0;" method="post" action="{{route('lang.handymanchange')}}" id="lang-form">
                    {{csrf_field()}}
    
                    <input type="hidden" class="lang_select" value="{{$lang->lang}}" name="lang_select">
    
                    <div class="btn-group bootstrap-select fit-width">
    
                        @if($lang->lang == 'eng')
    
                            <button type="button" class="btn dropdown-toggle selectpicker btn-default" data-toggle="dropdown" title="English" style="color: black !important;outline: none !important;">
                                <span class="filter-option pull-left"><span class="flag-icon flag-icon-gb"></span> English</span>&nbsp;<span class="caret"></span>
                            </button>
    
                            <div class="dropdown-menu open">
    
                                <ul class="dropdown-menu inner selectpicker" role="menu">
    
                                    <li rel="0" class="selected">
                                        <a href="#" tabindex="0" class="" onclick="formSubmit(this)" data-value="eng" style="color: black !important;"><span class="flag-icon flag-icon-gb"></span> English<i class="glyphicon glyphicon-ok icon-ok check-mark"></i></a>
                                    </li>
    
                                    <li rel="1">
                                        <a href="#" tabindex="0" class="" style="color: black !important;" onclick="formSubmit(this)" data-value="du"><span class="flag-icon flag-icon-nl"></span> Nederlands<i class="glyphicon glyphicon-ok icon-ok check-mark"></i></a>
                                    </li>
    
                                </ul>
    
                            </div>
    
                        @elseif($lang->lang == 'du')
    
                            <button type="button" class="btn dropdown-toggle selectpicker btn-default" data-toggle="dropdown" title="Nederlands" style="color: black !important;outline: none !important;">
                                <span class="filter-option pull-left"><span class="flag-icon flag-icon-nl"></span> Nederlands</span>&nbsp;<span class="caret"></span>
                            </button>
    
                            <div class="dropdown-menu open">
    
                                <ul class="dropdown-menu inner selectpicker" role="menu">
    
                                    <li rel="0">
                                        <a href="#" tabindex="0" class="" onclick="formSubmit(this)" data-value="eng" style="color: black !important;"><span class="flag-icon flag-icon-gb"></span> English<i class="glyphicon glyphicon-ok icon-ok check-mark"></i></a>
                                    </li>
    
                                    <li rel="1" class="selected">
                                        <a href="#" tabindex="0" class="" onclick="formSubmit(this)" data-value="du" style="color: black !important;"><span class="flag-icon flag-icon-nl"></span> Nederlands<i class="glyphicon glyphicon-ok icon-ok check-mark"></i></a>
                                    </li>
    
                                </ul>
    
                            </div>
    
                        @endif
    
                    </div>
    
                </form>
    
                <a style="display: flex;" class="dropdown-toggle" href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">
    
                    <img style="width: 45px;height: 45px;border-radius: 100%;border: 1px solid #dddddd;margin-right: 10px;"
                         src="{{ Auth::guard('user')->user()->getRawOriginal('photo') ? asset('assets/images/'.Auth::guard('user')->user()->getRawOriginal('photo')):"https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG"}}"
                         alt="">
    
                    <span class="user-info">
                        {{ Auth::guard('user')->user()->name}} {{Auth::guard('user')->user()->family_name}}
                        {{--<span>{{$lang->hmt}}</span>--}}
                    </span>
    
                </a>
            </div>

            <ul style="position: absolute;right: 20px;border: 1px solid rgb(190, 190, 190);text-align: left;margin: 0;z-index: 1000;background: white;border-radius: 5px;" class="collapse list-unstyled profile-submenu" id="homeSubmenu">

                <li style="padding: 15px;"><a href=" {{ route('user-reset') }} "><i
                            class="fa fa-fw fa-cog"></i> {{$lang->chnp}}</a></li>
                <li style="padding: 0 15px 15px 15px;"><a href="{{ route('user-logout') }}"><i
                            class="fa fa-fw fa-power-off"></i> {{$lang->logout}}</a></li>
            </ul>

        </div>

    </div>

</div>

<style type="text/css">

    .panel-default.admin>.panel-heading
    {
        border: 0;
    }

    .modal-open .top-bar
    {
        z-index: 1 !important;
    }

    #sidebar ul
    {
        list-style-type:none;
    }


    #sidebar ul li a
    {
        text-decoration:none;
        text-align: center !important;
    }

    #sidebar ul li a .icon
    {
        width: 48px;
        height: 48px;
        display: block;
        margin: auto;
    }

    .dashboard-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Dashboard.svg')}});
    }

    .active1 .dashboard-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Dashboard-Active.svg')}});
    }

    .customer-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Customers.svg')}});
    }

    .active .customer-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Customers-Active.svg')}});
    }

    .catalog-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Catalog.svg')}});
    }

    .active .catalog-icon, .active1 .catalog-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Catalog-Active.svg')}});
    }

    .sales-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Sales.svg')}});
    }

    .active1 .sales-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Sales-Active.svg')}});
    }

    .configuration-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Configure.svg')}});
    }

    .active .configuration-icon
    {
        background-image: url({{asset('assets/admin/img/Icon-Configure-Active.svg')}});
    }

    #sidebar ul li a::before
    {
        display: none;
    }

    #sidebar.active .sub-show {

        /*-webkit-transform: translateX(118px) !important;

        transform: translateX(118px) !important;*/

        -webkit-transform: translateX(-250px) !important;

        transform: translateX(-250px) !important;

        -webkit-transition: transform 0.5s ease-in-out !important;

        -moz-transition: transform 0.5s ease-in-out !important;

        -ms-transition: transform 0.5s ease-in-out !important;

        transition: transform 0.5s ease-in-out !important;

    }

    #sidebar .sub-show {

        -webkit-transform: translateX(118px) !important;

        transform: translateX(118px) !important;

        -webkit-transition: transform 1s ease-in !important;

        -moz-transition: transform 1s ease-in !important;

        -ms-transition: transform 1s ease-in !important;

        transition: transform 1s ease-in !important;

    }

    #sidebar ul li > ul {

        position: absolute;

        background-color: #fff;

        top: 87px;

        width: 250px;

        z-index: 1000;

        height: 100%;

        -webkit-transform: translateX(-250px);

        transform: translateX(-250px);

        -webkit-transition: transform 0.8s ease-in;

        -moz-transition: transform 0.8s ease-in;

        -ms-transition: transform 0.8s ease-in;

        transition: transform 0.8s ease-in;

        padding: 0;

        border-left: 1px solid #3f99e6;

        border-right: 1px solid hsla(0,0%,63.5%,.2);

        padding-bottom: 87px;

    }

    #sidebar:hover > #sidebar::-webkit-scrollbar
    {
        display: block;
    }

    #sidebar::-webkit-scrollbar-thumb
    {
        background-color: #1c97dd;
        border-radius: 10px;
    }

    #sidebar::-webkit-scrollbar
    {
        background-color: transparent;
        width: 5px;
    }

    /*
    DEMO STYLE
*/
    .section-padding
    {
        padding: 0;
    }

    @import "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700";

    body {
        font-family: 'Poppins', sans-serif;
        background: #fafafa;
    }

    p {
        font-family: 'Poppins', sans-serif;
        font-size: 1.1em;
        font-weight: 300;
        line-height: 1.7em;
        color: #999;
    }

    a,
    a:hover,
    a:focus {
        color: inherit;
        text-decoration: none;
        transition: all 0.3s;
    }

    .navbar {
        padding: 15px 10px;
        background: #fff;
        border: none;
        border-radius: 0;
        margin-bottom: 40px;
        box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    }

    .navbar-btn {
        box-shadow: none;
        outline: none !important;
        border: none;
    }

    .line {
        width: 100%;
        height: 1px;
        border-bottom: 1px dashed #ddd;
        margin: 40px 0;
    }


    /*i,
    span {
        display: inline-block;
    }*/

    /* ---------------------------------------------------
        SIDEBAR STYLE
    ----------------------------------------------------- */

    .wrapper {
        display: flex;
        align-items: stretch;
        height: 100%;
        position: absolute;
        width: 100%;
        padding-top: 87px;
    }

    #sidebar {
        position: static;
        z-index: 1000;
        height: 100%;
        /*min-width: 250px;
        max-width: 250px;*/
        min-width: 120px;
        max-width: 120px;
        /*background-color: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors.'c9'}};*/
        background-color: #fff;
        color: #fff;
        transition: all 1s;
        overflow-x: hidden;
        overflow-y: auto;
        padding-bottom: 100px;
        border-right: 1px solid hsla(0,0%,63.5%,.2);
    }

    #sidebar.active {
        /*min-width: 120px;
        max-width: 120px;*/
        min-width: 0;
        max-width: 0;
        text-align: center;
        /*margin-left: -250px;*/
    }

    .transform-it
    {
        -webkit-transform: translateX(120px);
        transform: translateX(120px);
        width: 92.2% !important;
    }

    .transform-it2
    {
        -webkit-transform: translateX(250px);
        transform: translateX(250px);
    }

    #sidebar:not(.active) h3
    {
        display: none;
    }

    #sidebar.active .sidebar-header .sidebar-header-logo,
    #sidebar.active .CTAs {
        display: none;
    }

    #sidebar.active .sidebar-header strong {
        display: block;
    }

    #sidebar.active .profile .r-na
    {
        width: 100%;
        padding: 0;
    }

    #sidebar ul li a {
        text-align: center;
        font-family: Montserrat,sans-serif;
        color: #a2a2a2;
    }

    #sidebar ul li a.active
    {
        /*color: #fff;
        background: {{$gs->colors == null ? 'rgba(207, 55, 58, 0.70)':$gs->colors.'c2'}};*/
        color: #0041ff;
    }

    #sidebar ul li a.active1
    {
        background-color: #fff;
        /*color: {{$gs->colors == null ? 'rgba(207, 55, 58, 0.70)':$gs->colors.'c2'}};*/
        color: #0041ff;
    }

    #sidebar.active ul li a {
        padding: 20px 10px;
        text-align: center;
        font-size: 0.85em;
    }

    #sidebar.active ul li a span
    {
        display: none;
    }

    #sidebar.active ul li a i {
        margin: auto auto 5px auto;
        display: block;
        font-size: 1.3em;
    }

    #sidebar.active ul li ul li a i {
        margin: auto 10px 5px auto;
        display: inline-block;
    }

    #sidebar.active ul ul a {
        padding: 19px !important;
    }

    #sidebar ul ul a {
        padding: 1em !important;
        font-size: 14px !important;
        display: block !important;
        text-align: left !important;
    }

    #sidebar.active .dropdown-toggle::before {
        top: auto !important;
        bottom: 5px !important;
        right: 50% !important;
        -webkit-transform: translateX(50%) !important;
        -ms-transform: translateX(50%) !important;
        transform: translateX(50%) !important;
    }

    #sidebar .sidebar-header {
        padding: 20px;
    }

    #sidebar .sidebar-header strong {
        display: none;
        font-size: 0.8em;
    }

    #sidebar ul.components {
        /*padding: 0 0 100px 0;*/
        /*overflow-y: auto;*/
        height: 100%;
        /* visibility: hidden; */
        /*-webkit-overflow-scrolling: touch;*/
        display: inline-block;
        width: 100%;
    }

    #sidebar ul.components li, #sidebar ul.components:hover,
    #sidebar ul.components:focus
    {
        visibility: visible !important;
    }

    #sidebar ul li a {
        padding: 10px;
        font-size: 0.8em;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    #sidebar .profile .profile-submenu li a:hover {
        color: #7386D5;
        background: #fff;
    }

    .sub-show li a:hover
    {
        background-color: #0041ff !important;
        color: white !important;
    }

    /*#sidebar ul li a i {
        margin-right: 10px;
    }*/

    #sidebar .components li.active>a {
        color: #fff;
        background: {{$gs->colors == null ? 'rgba(204, 37, 42, 0.79)':$gs->colors.'c9'}};
    }

    a[data-toggle="collapse"] {
        position: relative;
    }

    #sidebar .dropdown-toggle::before
    {
        display: block !important;
        position: absolute !important;
        top: 50% !important;
        right: 20px !important;
        transform: translateY(-50%) !important;
    }

    ul ul a {
        font-size: 0.9em !important;
        padding-left: 30px !important;
    }

    ul.CTAs {
        padding: 20px;
    }

    ul.CTAs a {
        text-align: center;
        font-size: 0.9em !important;
        display: block;
        border-radius: 5px;
        margin-bottom: 5px;
    }

    a.download {
        background: #fff;
        color: #7386D5;
    }

    /* ---------------------------------------------------
        CONTENT STYLE
    ----------------------------------------------------- */

    #content {
        width: 100%;
        transition: all 1s;
        overflow: hidden;
    }

    /* ---------------------------------------------------
        MEDIAQUERIES
    ----------------------------------------------------- */

    @media (max-width: 768px) {

        #sidebar ul li > ul
        {
            /*top: 0 !important;*/
            position: fixed;
        }

        .user-info, a[aria-expanded="false"]::before, a[aria-expanded="true"]::before
        {
            display: none;
        }

        /*#sidebar {
            min-width: 80px;
            max-width: 80px;
            text-align: center;
            margin-left: -80px !important;
        }*/
        /*#sidebar .dropdown-toggle::before {
            top: auto !important;
            bottom: 10px !important;
            right: 50% !important;
            -webkit-transform: translateX(50%) !important;
            -ms-transform: translateX(50%) !important;
            transform: translateX(50%) !important;
        }*/

        .transform-it
        {
            -webkit-transform: translateX(0px);
            transform: translateX(0px);
            width: 100% !important;
        }

        .transform-it2
        {
            -webkit-transform: translateX(250px);
            transform: translateX(250px);
        }

        #sidebar
        {
            position: absolute;
            min-width: 250px;
            max-width: 250px;
            display: flex;
        }

        #sidebar.active .sub-show {

            -webkit-transform: translateX(-250px) !important;

            transform: translateX(-250px) !important;

            -webkit-transition: transform 0.5s ease-in-out !important;

            -moz-transition: transform 0.5s ease-in-out !important;

            -ms-transition: transform 0.5s ease-in-out !important;

            transition: transform 0.5s ease-in-out !important;

        }

        #sidebar .sub-show {

            -webkit-transform: translateX(0px) !important;

            transform: translateX(0px) !important;

            -webkit-transition: transform 1s ease-in !important;

            -moz-transition: transform 1s ease-in !important;

            -ms-transition: transform 1s ease-in !important;

            transition: transform 1s ease-in !important;

        }

        #sidebar.active {
            margin-left: -120px !important;
        }

        #sidebar .sidebar-header .sidebar-header-logo,
        #sidebar .CTAs {
            display: none;
        }
        #sidebar .sidebar-header strong {
            display: block;
        }
        #sidebar ul li a {
            padding: 20px 10px;
            flex-direction: row;
            justify-content: flex-start;
        }

        #sidebar ul li a .icon
        {
            width: 25%;
            height: 40px;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            margin: 0;
        }

        #sidebar ul li a span {
            font-size: 1.65em;
            max-width: 70%;
            text-align: left !important;
        }
        /*#sidebar ul li a i {
            margin-right: 0;
            display: block;
        }*/
        #sidebar ul ul a {
            padding: 10px !important;
        }
        #sidebar ul li a i {
            font-size: 1.3em;
        }
        /*#sidebar {
            margin-left: 0;
        }*/
        #sidebarCollapse span {
            display: none;
        }
    }

    #sidebar-menu
    {
        width: 100%;
    }

    button {
        outline: none !important;
    }

    .bootstrap-select {
        margin-bottom: 0 !important;
    }

    #lang-form .bootstrap-select .selectpicker {

        background-color: white !important;
        color: inherit !important;
        margin: 0;
        text-transform: inherit;
        white-space: nowrap;
        border: 1px solid transparent;
        box-shadow: none;
        border-color: #ccc !important;
        font-size: 14px;
        padding: 6px 12px;
        padding-right: 25px;
        border-radius: 4px;

    }

    .bootstrap-select .dropdown-menu {
        padding: 0 !important;
    }

    .selected {
        background-color: #ececec;

    }

    .language-select {

        width: 100% !important;
        text-align: center;
        margin-top: 25px !important;
    }

    .right-side {
        width: 100% !important;
        margin: 0 !important;
        height: 100% !important;
        background: transparent !important;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .right-side .container-fluid
    {
        padding: 0;
    }

    .add-product-1
    {
        margin: 0;
    }

    @media (min-width: 769px)
    {
        #sidebarCollapse1
        {
            display: none;
        }
    }

    @media only screen and (min-width: 1200px) and (min-width: 768px) {

        ul.profile li.active img {

            margin-left: 0;
        }

    }

    /* .bootstrap-select.fit-width {
        width: 70% !important;
    } */

    #sidebar-menu ul.components ul li a {
        padding-left: 15px;
    }


    iframe {
        width: 100%;
    }


    /* .bootstrap-select .dropdown-menu {
        position: relative;
    } */


    .add-back-btn, .add-newProduct-btn {
        background-color: <?php if($gs->btn_bg == null) { if($gs->colors == null){ echo '#f3bd02 !important;'; } else {   echo $gs->colors.' !important;'; }} else { echo $gs->btn_bg. ' !important;'; } ?>

         border-color:
        <?php if($gs->btn_brd != null) { echo $gs->btn_brd. ' !important;'; } else { echo '#ffffff00 !important;'; } ?>
color: <?php if($gs->btn_col != null) { echo $gs->btn_col. ' !important;'; } else { echo '#fff !important;'; } ?>

    }

    .featured-btn {
        background-color: <?php if($gs->btn_bg == null) { if($gs->colors == null){ echo '#f3bd02 !important;'; } else {   echo $gs->colors.' !important;'; }} else { echo $gs->btn_bg. ' !important;'; } ?>

         border-color:
        <?php if($gs->btn_brd != null) { echo $gs->btn_brd. ' !important;'; } else { echo '#ffffff00 !important;'; } ?>

color: <?php if($gs->btn_col != null) { echo $gs->btn_col. ' !important;'; } else { echo '#fff !important;'; } ?>

    }

    .add-product_btn {
        background-color: <?php if($gs->btn_bg == null) { if($gs->colors == null){ echo '#f3bd02 !important;'; } else {   echo $gs->colors.' !important;'; }} else { echo $gs->btn_bg. ' !important;'; } ?>

         border-color:
        <?php if($gs->btn_brd != null) { echo $gs->btn_brd. ' !important;'; } else { echo '#ffffff00 !important;'; } ?>

color: <?php if($gs->btn_col != null) { echo $gs->btn_col. ' !important;'; } else { echo '#fff !important;'; } ?>

    }

    .boxed-btn.blog {
        background-color: <?php if($gs->btn_bg == null) { if($gs->colors == null){ echo '#f3bd02 !important;'; } else {   echo $gs->colors.' !important;'; }} else { echo $gs->btn_bg. ' !important;'; } ?>

         border-color:
        <?php if($gs->btn_brd != null) { echo $gs->btn_brd. ' !important;'; } else { echo '#ffffff00 !important;'; } ?>

color: <?php if($gs->btn_col != null) { echo $gs->btn_col. ' !important;'; } else { echo '#fff !important;'; } ?>

    }

    .nicEdit-button {
        background-image: url("<?php echo asset('assets/images/nicEditIcons-latest.gif'); ?>") !important;
    }

    .sub-show .toggle-aside-nav
    {
        display: block;
    }

    .toggle-aside-nav
    {
        display: none;
        position: absolute;
        top: 25px;
        right: -12px;
    }

    .accordian-left-icon
    {
        background-image: url({{asset('assets/admin/img/chevron-left.svg')}});
        width: 24px;
        height: 24px;
    }

    .icon
    {
        display: inline-block;
        background-size: cover;
    }

    .note-editable p
    {
        color: black;
    }

</style>

<div class="wrapper">

    <!-- Sidebar  -->
    <nav id="sidebar" @if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"])) class="active" @endif>

        {{--<div class="sidebar-header">

            <a href="{{route('front.index')}}">
                <h3 style="color: white;">{{$gs->title[0]}}</h3>

                <img src="{{asset('assets/images/'.$gs->logo)}}" alt="Sidebar header logo" class="sidebar-header-logo" style="height: 55px;width: 100%;">
            </a>

        </div>--}}

        {{--<ul class="list-unstyled profile">
            <li style="padding-bottom: 0;" class="active">
                <div class="row" style="margin-left: 0px;margin-right: 0px;">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                        <img
                            src="{{ Auth::guard('user')->user()->photo ? asset('assets/images/'.Auth::guard('user')->user()->photo):"https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG"}}"
                            alt="">
                    </div>
                    <div class="r-na col-lg-9 col-md-9 col-sm-9 col-xs-9">
                        <a class="dropdown-toggle" href="#homeSubmenu" data-toggle="collapse"
                           aria-expanded="false">{{ Auth::guard('user')->user()->name}} {{Auth::guard('user')->user()->family_name}}
                            <span>{{$lang->hmt}}</span></a>
                    </div>
                </div>
                <ul class="collapse list-unstyled profile-submenu" id="homeSubmenu">

                    <li><a href=" {{ route('user-reset') }} "><i
                                class="fa fa-fw fa-cog"></i> {{$lang->chnp}}</a></li>
                    <li><a href="{{ route('user-logout') }}"><i
                                class="fa fa-fw fa-power-off"></i> {{$lang->logout}}</a></li>
                </ul>
            </li>
        </ul>--}}

        <ul class="parent-menu list-unstyled components">

            <li><a @if(Route::currentRouteName() == 'user-dashboard' || Route::currentRouteName() == 'user-profile' || Route::currentRouteName() == 'radius-management' || Route::currentRouteName() == 'user-complete-profile') class="active1" @endif href="javascript:void(0)"><span class="icon dashboard-icon"></span> <span>{{$lang->dashboard}}</span></a>

                <ul class="hide">

                    <span class="toggle-aside-nav">
                        <i class="icon accordian-left-icon"></i>
                    </span>

                    <div style="overflow-y: auto;height: 100%;">

                        @if(auth()->guard("user")->user()->can('show-dashboard'))

                            <li><a href="{{route('user-dashboard')}}"><i class="fa fa-angle-right"></i> {{__('text.Dashboard')}}</a></li>

                        @endif


                        @if(auth()->guard("user")->user()->can('edit-profile'))

                            <li><a href="{{route('user-profile')}}"><i class="fa fa-angle-right"></i> {{$lang->edit}}</a></li>

                        @endif


                        @if(auth()->guard("user")->user()->can('radius-management'))

                            <li><a href="{{route('radius-management')}}"><i class="fa fa-angle-right"></i> {{$lang->rm}}</a></li>

                        @endif


                        @if(auth()->guard("user")->user()->can('user-complete-profile'))

                            <li><a href="{{route('user-complete-profile')}}"><i class="fa fa-angle-right"></i> {{$lang->cmpt}}</a></li>

                        @endif

                    </div>

                </ul>

            </li>

            {{--<li>
                <a href="{{route('handyman-bookings')}}"><i class="fa fa-fw fa-book"></i> <span>{{$lang->mbt}}</span></a>
            </li>--}}

            @if(auth()->guard("user")->user()->role_id == 2)

                @if(auth()->guard("user")->user()->hasAnyPermission(['retailer-suppliers', 'customers', 'employees']))

                    <li><a @if(Route::currentRouteName() == 'suppliers' || Route::currentRouteName() == 'customers' || Route::currentRouteName() == 'employees' || Route::currentRouteName() == 'profile-update-requests') class="active1" @endif href="javascript:void(0)"><span class="icon dashboard-icon"></span> <span>{{__('text.Relations')}}</span></a>

                        <ul class="hide">

                            <span class="toggle-aside-nav">
                                <i class="icon accordian-left-icon"></i>
                            </span>

                            <div style="overflow-y: auto;height: 100%;">

                                @if(auth()->guard("user")->user()->can('retailer-suppliers'))

                                    <li><a href="{{route('suppliers')}}"><i class="fa fa-angle-right"></i> {{__('text.Suppliers')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('customers'))

                                    <li><a href="{{route('customers')}}"><i class="fa fa-angle-right"></i> {{__('text.Customers')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('employees'))

                                    <li><a href="{{route('employees')}}"><i class="fa fa-angle-right"></i> {{__('text.Employees')}}</a></li>

                                @endif

                                <!-- <li><a href="{{route('profile-update-requests')}}"><i class="fa fa-angle-right"></i> {{__('text.Update Requests')}}</a></li> -->

                            </div>

                        </ul>

                    </li>

                @endif

                <li><a @if(Route::currentRouteName() == 'plannings' || Route::currentRouteName() == 'planning-titles' || Route::currentRouteName() == 'planning-statuses' || Route::currentRouteName() == 'planning-responsible-persons' || Route::currentRouteName() == 'notes') class="active1" @endif href="javascript:void(0)"><span class="icon dashboard-icon"></span> <span>{{__('text.Plannings')}}</span></a>

                    <ul class="hide">

                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            <li><a href="{{route('plannings')}}"><i class="fa fa-angle-right"></i> {{__('text.Plannings')}}</a></li>
                            <li><a href="{{route('planning-titles')}}"><i class="fa fa-angle-right"></i> {{__('text.Planning Titles')}}</a></li>
                            <li><a href="{{route('planning-statuses')}}"><i class="fa fa-angle-right"></i> {{__('text.Planning Statuses')}}</a></li>
                            <li><a href="{{route('planning-responsible-persons')}}"><i class="fa fa-angle-right"></i> {{__('text.Responsible Persons')}}</a></li>
                            <li><a href="{{route('notes')}}"><i class="fa fa-angle-right"></i> {{__('text.Notes')}}</a></li>

                        </div>

                    </ul>

                </li>

            @endif

            @if(auth()->guard("user")->user()->role_id == 4)

                @if(auth()->guard("user")->user()->hasAnyPermission(['supplier-retailers', 'employees']))

                    <li><a @if(Route::currentRouteName() == 'suppliers' || Route::currentRouteName() == 'employees') class="active1" @endif href="javascript:void(0)"><span class="icon dashboard-icon"></span> <span>{{__('text.Relations')}}</span></a>

                        <ul class="hide">

                            <span class="toggle-aside-nav">
                                <i class="icon accordian-left-icon"></i>
                            </span>

                            <div style="overflow-y: auto;height: 100%;">

                                @if(auth()->guard("user")->user()->can('retailer-suppliers'))

                                    <li>
                                        <a href="{{route('retailers')}}"><i class="fa fa-angle-right"></i> {{__('text.Retailers')}}
                                            <main rel="main">
                                                <div class="notification">
                                                    <svg viewbox="-10 0 35 35">
                                                        <path class="notification--bell" d="M14 12v1H0v-1l0.73-0.58c0.77-0.77 0.81-3.55 1.19-4.42 0.77-3.77 4.08-5 4.08-5 0-0.55 0.45-1 1-1s1 0.45 1 1c0 0 3.39 1.23 4.16 5 0.38 1.88 0.42 3.66 1.19 4.42l0.66 0.58z"></path>
                                                        <path class="notification--bellClapper" d="M7 15.7c1.11 0 2-0.89 2-2H5c0 1.11 0.89 2 2 2z"></path>
                                                    </svg>
                                                    <span class="notification--num">{{count($no_retailers)}}</span>
                                                </div>
                                            </main>
                                        </a>
                                    </li>

                                @endif

                                <!-- @if(auth()->guard("user")->user()->can('customers'))

                                    <li><a href="{{route('customers')}}"><i class="fa fa-angle-right"></i> {{__('text.Customers')}}</a></li>

                                @endif -->

                                @if(auth()->guard("user")->user()->can('employees'))

                                    <li><a href="{{route('employees')}}"><i class="fa fa-angle-right"></i> {{__('text.Employees')}}</a></li>

                                @endif

                            </div>

                        </ul>

                    </li>

                @endif

                <li><a @if(Route::currentRouteName() == 'notes') class="active1" @endif href="javascript:void(0)"><span class="icon dashboard-icon"></span> <span>{{__('text.Plannings')}}</span></a>

                    <ul class="hide">

                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            <li><a href="{{route('notes')}}"><i class="fa fa-angle-right"></i> {{__('text.Notes')}}</a></li>

                        </div>

                    </ul>

                </li>

            @endif


            @if(auth()->guard("user")->user()->role_id == 2)

                <li><a @if(Route::currentRouteName() == 'company-info' || Route::currentRouteName() == 'email-templates' || Route::currentRouteName() == 'prefix-settings' || Route::currentRouteName() == 'general-ledgers' || Route::currentRouteName() == 'email-settings' || Route::currentRouteName() == 'generate-dkim') class="active1" @endif href="javascript:void(0)"><span class="icon catalog-icon"></span> <span>{{__('text.Configurations')}}</span></a>

                    <ul class="hide">

                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            <li><a href="{{route('company-info')}}"><i class="fa fa-angle-right"></i> {{__('text.Company Info')}}</a></li>

                            <li><a href="{{route('email-templates')}}"><i class="fa fa-angle-right"></i> {{__('text.Email Templates')}}</a></li>
                            
                            <li><a href="{{route('prefix-settings')}}"><i class="fa fa-angle-right"></i> {{__('text.Prefix Settings')}}</a></li>

                            <li><a href="{{route('general-ledgers')}}"><i class="fa fa-angle-right"></i> {{__('text.General Ledgers')}}</a></li>

                            <li><a href="{{route('general-categories')}}"><i class="fa fa-angle-right"></i> {{__('text.Ledgers by categories')}}</a></li>

                            <li><a href="{{route('email-settings')}}"><i class="fa fa-angle-right"></i> {{__('text.Email Settings')}}</a></li>

                            <li><a href="{{route('generate-dkim')}}"><i class="fa fa-angle-right"></i> {{__('text.Generate DKIM')}}</a></li>

                            <li><a href="{{route('shop.create')}}"><i class="fa fa-angle-right"></i> {{__('text.Webshop')}}</a></li>

                        </div>

                    </ul>

                </li>

            @elseif(auth()->guard("user")->user()->role_id == 4)

                <li><a @if(Route::currentRouteName() == 'company-info' || Route::currentRouteName() == 'prefix-settings' || Route::currentRouteName() == 'email-settings' || Route::currentRouteName() == 'generate-dkim') class="active1" @endif href="javascript:void(0)"><span class="icon catalog-icon"></span> <span>{{__('text.Configurations')}}</span></a>

                    <ul class="hide">

                         <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            <li><a href="{{route('company-info')}}"><i class="fa fa-angle-right"></i> {{__('text.Company Info')}}</a></li>
                            
                            <li><a href="{{route('prefix-settings')}}"><i class="fa fa-angle-right"></i> {{__('text.Prefix Settings')}}</a></li>

                            <li><a href="{{route('email-settings')}}"><i class="fa fa-angle-right"></i> {{__('text.Email Settings')}}</a></li>

                            <li><a href="{{route('generate-dkim')}}"><i class="fa fa-angle-right"></i> {{__('text.Generate DKIM')}}</a></li>

                        </div>

                    </ul>

                </li>

            @endif


            {{--@if(auth()->guard("user")->user()->can('create-new-quotation'))

                <!--<li>
                    <a href="{{route('new-quotations')}}"><span class="icon catalog-icon"></span> <span>New Quotations</span></a>
                </li>-->

                @if(auth()->guard("user")->user()->role_id == 2)

                    <li>
                        <a href="{{route('select-quotation-form')}}"><span class="icon catalog-icon"></span> <span>Create Quotation (New)</span></a>
                    </li>

                @endif

            @endif--}}

            <!--@if(auth()->guard("user")->user()->role_id == 2)

                <li>
                    <a href="{{route('new-orders')}}"><span class="icon catalog-icon"></span> <span>New Orders</span></a>
                </li>

                <li>
                    <a href="{{route('new-invoices')}}"><span class="icon catalog-icon"></span> <span>New Invoices</span></a>
                </li>

            @endif-->

            {{--@if(auth()->guard("user")->user()->can('quotations'))

                <li>
                    <a href="{{route('quotations')}}"><span class="icon catalog-icon"></span> <span>{{__('text.Quotations')}}</span></a>
                </li>

            @endif


            @if(auth()->guard("user")->user()->can('quotations-invoices'))

                <li>
                    <a href="{{route('quotations-invoices')}}"><span class="icon catalog-icon"></span> <span>{{__('text.Quotation Invoices')}}</span></a>
                </li>

            @endif


            @if(auth()->guard("user")->user()->can('commission-invoices'))

                <li>
                    <a href="{{route('commission-invoices')}}"><span class="icon catalog-icon"></span> <span>{{__('text.Commission Invoices')}}</span></a>
                </li>

            @endif--}}


            @if(auth()->guard("user")->user()->hasAnyPermission(['create-new-quotation', 'customer-quotations', 'customer-invoices', 'handyman-quotation-requests', 'retailer-general-terms']))

                <li><a @if(Route::currentRouteName() == 'customer-quotations' || Route::currentRouteName() == 'new-orders' || Route::currentRouteName() == 'customer-invoices' || Route::currentRouteName() == 'handyman-quotation-requests' || Route::currentRouteName() == 'retailer-general-terms') class="active1" @endif href="javascript:void(0)"><span class="icon sales-icon"></span> <span>{{__('text.Sales')}}</span></a>

                    <ul class="hide">

                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            @if(auth()->guard("user")->user()->role_id == 2 && auth()->guard("user")->user()->can('create-new-quotation'))

                                <li><a href="{{route('customer-quotations')}}"><i class="fa fa-angle-right"></i> {{__('text.New Quotations')}}</a></li>

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 4 && auth()->guard("user")->user()->can('customer-quotations'))

                                <li><a href="{{route('customer-quotations')}}"><i class="fa fa-angle-right"></i> {{__('text.New Orders')}}</a></li>

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 2 && auth()->guard("user")->user()->can('handyman-quotation-requests'))

                                <li>
                                    <a href="{{route('handyman-quotation-requests')}}"><i class="fa fa-angle-right"></i> {{__('text.Quotation Requests')}}
                                        <main rel="main">
                                            <div class="notification">
                                                <svg viewbox="-10 0 35 35">
                                                    <path class="notification--bell" d="M14 12v1H0v-1l0.73-0.58c0.77-0.77 0.81-3.55 1.19-4.42 0.77-3.77 4.08-5 4.08-5 0-0.55 0.45-1 1-1s1 0.45 1 1c0 0 3.39 1.23 4.16 5 0.38 1.88 0.42 3.66 1.19 4.42l0.66 0.58z"></path>
                                                    <path class="notification--bellClapper" d="M7 15.7c1.11 0 2-0.89 2-2H5c0 1.11 0.89 2 2 2z"></path>
                                                </svg>
                                                <span class="notification--num">{{count($no_requests)}}</span>
                                            </div>
                                        </main>
                                    </a>
                                </li>

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 2)

                                <li><a href="{{route('new-orders')}}"><i class="fa fa-angle-right"></i> {{__('text.New Orders')}}</a></li>

                                <li><a href="{{route('customer-invoices')}}"><i class="fa fa-angle-right"></i> {{__('text.New Invoices')}}</a></li>

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 2 && auth()->guard("user")->user()->can('retailer-general-terms'))

                                <li><a href="{{route('retailer-general-terms')}}"><i class="fa fa-angle-right"></i> {{__('text.General Terms')}}</a></li>

                            @endif

                            <!--@if(auth()->guard("user")->user()->can('customer-quotations'))

                                <li><a href="{{route('customer-quotations')}}"><i class="fa fa-angle-right"></i> {{__('text.Quotations')}}</a></li>

                            @endif

                            @if(auth()->guard("user")->user()->can('customer-invoices'))

                                <li><a href="{{route('customer-invoices')}}"><i class="fa fa-angle-right"></i> {{__('text.Invoices')}}</a></li>

                            @endif-->

                        </div>

                    </ul>

                </li>

            @endif

            @if(auth()->guard("user")->user()->role_id == 2)

                <li><a @if(Route::currentRouteName() == 'actual' || Route::currentRouteName() == 'forecast' || Route::currentRouteName() == 'payment-accounts') class="active1" @endif href="javascript:void(0)"><span class="icon sales-icon"></span> <span>{{__('text.Money')}}</span></a>

                    <ul class="hide">
    
                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>
    
                        <div style="overflow-y: auto;height: 100%;">
    
                            <li><a href="{{route('actual')}}"><i class="fa fa-angle-right"></i> {{__('text.Actual')}}</a></li>

                            <li><a href="{{route('forecast')}}"><i class="fa fa-angle-right"></i> {{__('text.Forecast')}}</a></li>

                            <li><a href="{{route('payment-accounts')}}"><i class="fa fa-angle-right"></i> {{__('text.Payment Accounts')}}</a></li>
    
                        </div>
    
                    </ul>
    
                </li>

            @endif

            <li>
                <a href="{{ route('chat') }}"><span class="icon catalog-icon"></span> <span>{{__("text.Chat")}}</span></a>
            </li>

            @if(auth()->guard("user")->user()->role_id == 2 || auth()->guard("user")->user()->role_id == 4)

                <li><a @if(Route::currentRouteName() == 'review-reasons' || Route::currentRouteName() == 'customer-messages' || Route::currentRouteName() == 'sent-emails' || Route::currentRouteName() == 'send-email') class="active1" @endif href="javascript:void(0)"><span class="icon sales-icon"></span> <span>{{__('text.Messages')}}</span></a>

                    <ul class="hide">
    
                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>
    
                        <div style="overflow-y: auto;height: 100%;">

                            @if(auth()->guard("user")->user()->role_id == 2)

                                <li><a href="{{route('review-reasons')}}"><i class="fa fa-angle-right"></i> {{__('text.Review Reasons')}}</a></li>

                                <li><a href="{{route('customer-messages')}}"><i class="fa fa-angle-right"></i> {{__('text.Customer Messages')}}</a></li>

                                <li><a href="{{route('sent-emails')}}"><i class="fa fa-angle-right"></i> {{__('text.Sent Mails')}}</a></li>

                            @endif
                            
                            <li><a href="{{route('send-email')}}"><i class="fa fa-angle-right"></i> {{__('text.Send Mail')}}</a></li>
    
                        </div>
    
                    </ul>
    
                </li>

            @endif

            {{--@if(auth()->guard("user")->user()->hasAnyPermission(['user-products', 'product-create', 'user-items']))

                <li><a @if(Route::currentRouteName() == 'user-products' || Route::currentRouteName() == 'product-create' || Route::currentRouteName() == 'user-items') class="active1" @endif href="javascript:void(0)"><i class="fa fa-fw fa-file-code-o"></i> <span>{{__('text.My Products')}}</span></a>

                <ul class="hide">

                    @if(auth()->guard("user")->user()->can('user-products'))

                        <li><a href="{{route('user-products')}}"><i class="fa fa-angle-right"></i> {{__('text.Products Overview')}}</a></li>

                    @endif

                    @if(auth()->guard("user")->user()->can('product-create'))

                        <li><a href="{{route('product-create')}}"><i class="fa fa-angle-right"></i> {{__('text.Add Products')}}</a></li>

                    @endif

                    @if(auth()->guard("user")->user()->can('user-items'))

                        <li><a href="{{route('user-items')}}"><i class="fa fa-angle-right"></i> {{__('text.My Items')}}</a></li>

                    @endif

                </ul>

            </li>

            @endif--}}

            @if(auth()->guard("user")->user()->hasAnyPermission(['user-products', 'user-colors', 'user-price-tables', 'my-services', 'user-categories', 'user-brands', 'user-models', 'user-items', 'user-features']))

                <li><a @if(Route::currentRouteName() == 'admin-product-index' || Route::currentRouteName() == 'admin-brand-index' || Route::currentRouteName() == 'admin-model-index' || Route::currentRouteName() == 'admin-item-index' || Route::currentRouteName() == 'user-items' || Route::currentRouteName() == 'my-services' || Route::currentRouteName() == 'admin-feature-index' || Route::currentRouteName() == 'supplier-products' || Route::currentRouteName() == 'supplier-categories' || Route::currentRouteName() == 'admin-color-index' || Route::currentRouteName() == 'admin-price-tables' || Route::currentRouteName() == 'predefined-model-index' || Route::currentRouteName() == 'features-update-requests' || Route::currentRouteName() == 'models-update-requests') class="active1" @endif href="javascript:void(0)"><span class="icon catalog-icon"></span> <span>{{__('text.Products')}}</span></a>

                    <ul class="hide">

                        <span class="toggle-aside-nav">
                            <i class="icon accordian-left-icon"></i>
                        </span>

                        <div style="overflow-y: auto;height: 100%;">

                            @if(auth()->guard("user")->user()->can('user-products'))

                                <li><a href="{{route('admin-product-index')}}"><i class="fa fa-angle-right"></i> @if(auth()->guard("user")->user()->role_id == 4) {{__('text.My Products')}} @else {{__('text.Suppliers Products')}} @endif</a></li>

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 2)

                                @if(auth()->guard("user")->user()->can('user-items'))

                                    <li><a href="{{route('user-items')}}"><i class="fa fa-angle-right"></i> {{__('text.My Items')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('my-services'))

                                    <li><a href="{{route('my-services')}}"><i class="fa fa-angle-right"></i> {{__('text.Services')}}</a></li>

                                @endif

                            @endif

                            @if(auth()->guard("user")->user()->role_id == 4)

                                @if(auth()->guard("user")->user()->can('user-colors'))

                                    <li><a href="{{route('admin-color-index')}}"><i class="fa fa-angle-right"></i> {{__('text.Colors')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('user-price-tables'))

                                    <li><a href="{{route('admin-price-tables')}}"><i class="fa fa-angle-right"></i> {{__('text.Price Tables')}}</a></li>

                                @endif

                                <!-- @if(auth()->guard("user")->user()->can('my-services'))

                                    <li><a href="{{route('admin-service-index')}}"><i class="fa fa-angle-right"></i> Services</a></li>

                                @endif -->

                                @if(auth()->guard("user")->user()->can('user-categories'))

                                    <li><a href="{{route('supplier-categories')}}"><i class="fa fa-angle-right"></i> {{__('text.Categories')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('user-brands'))

                                    <li><a href="{{route('admin-brand-index')}}"><i class="fa fa-angle-right"></i> {{__('text.Brands')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('user-models'))

                                    <li><a href="{{route('predefined-model-index')}}"><i class="fa fa-angle-right"></i> {{__('text.Models')}}</a></li>

                                @endif

                                @if(auth()->guard("user")->user()->can('user-models'))

                                    <li><a href="{{route('admin-model-index')}}"><i class="fa fa-angle-right"></i> {{__('text.Types')}}</a></li>

                                @endif

                                <!-- @if(auth()->guard("user")->user()->can('user-items'))

                                    <li><a href="{{route('admin-item-index')}}"><i class="fa fa-angle-right"></i> Items</a></li>

                                @endif -->

                                @if(auth()->guard("user")->user()->can('user-features'))

                                    <li><a href="{{route('admin-feature-index')}}"><i class="fa fa-angle-right"></i> {{__('text.Features')}}</a></li>

                                @endif

                                <li><a href="{{route('features-update-requests')}}"><i class="fa fa-angle-right"></i> {{__('text.Features Update Requests')}}</a></li>
                                <li><a href="{{route('models-update-requests')}}"><i class="fa fa-angle-right"></i> {{__('text.Models Update Requests')}}</a></li>

                            @endif

                        </div>

                    </ul>

                </li>

            @endif


            {{--@if(auth()->guard("user")->user()->hasAnyPermission(['my-services', 'service-create']))

                <li><a @if(Route::currentRouteName() == 'my-services' || Route::currentRouteName() == 'service-create') class="active1" @endif href="javascript:void(0)"><i class="fa fa-fw fa-file-code-o"></i> <span>My Services</span></a>

                <ul class="hide">

                    @if(auth()->guard("user")->user()->can('my-services'))

                        <li><a href="{{route('my-services')}}"><i class="fa fa-angle-right"></i>{{__('text.Services Overview')}} </a></li>

                    @endif

                    @if(auth()->guard("user")->user()->can('service-create'))

                        <li><a href="{{route('service-create')}}"><i class="fa fa-angle-right"></i>{{__('text.Add Services')}}</a></li>

                    @endif

                </ul>

            </li>

            @endif--}}


            {{--<li>
                <a href="{{ route('user-subservices') }}" id="sub-services"><i
                        class="fa fa-fw fa-hospital-o"></i> <span>{{$lang->msst}}</span></a>
            </li>

            <li>
                <a href="{{ route('user-availability') }}" id="availability"><i
                        class="fa fa-fw fa-hospital-o"></i> <span>{{$lang->avmt}}</span></a>
            </li>--}}

            {{--<li>
                <a href="{{route('purchased-bookings')}}"><i class="fa fa-fw fa-book"></i> <span>{{$lang->pbt}}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('experience-years') }}" id="experience"><i
                        class="fa fa-fw fa-hospital-o"></i> <span>{{$lang->eyt}}</span></a>
            </li>

            <li>
                <a href="{{ route('insurance') }}" id="insurance"><i
                        class="fa fa-fw fa-book"></i> <span>{{$lang->ist}}</span></a>
            </li>--}}


            @if(auth()->guard("user")->user()->can('ratings'))

                <li>
                    <a href="{{ route('ratings') }}" id="rating"><span class="icon catalog-icon"></span> <span>{{$lang->hpmrt}}</span></a>
                </li>

            @endif


            @if(auth()->guard("user")->user()->can('instruction-manual'))

                <li>
                    <a href="{{ route('instruction-manual') }}" id="instruction"><span class="icon configuration-icon"></span> <span>{{__('text.Instruction Manual')}}</span></a>
                </li>

            @endif

        </ul>

    </nav>

    <!-- Page Content  -->
    <div id="content">

        @include('includes.notification-box')
        @yield('content')

        @if(Route::currentRouteName() != 'chat')

            @include("user.add_note")

        @endif

        @if(auth()->guard("user")->user()->role_id == 2)

            @include("user.create_user_modal")

        @endif

    </div>
</div>

<style>

    main {
        display: flex;
        justify-content: center;
        align-items: center;
        /* height: 100%; */
        position: relative;
        float: right;
    }
    main .notification {
        position: relative;
        width: 4em;
        height: 4em;
    }
    main .notification svg {
        width: 100%;
        height: 100%;
    }
    main .notification svg > path {
        fill: black;
    }
    main .notification--bell {
        animation: bell 2.2s linear infinite;
        transform-origin: 50% 0%;
    }
    main .notification--bellClapper {
        animation: bellClapper 2.2s 0.1s linear infinite;
    }
    main .notification--num {
        position: absolute;
        top: -18%;
        left: 50%;
        font-size: 13px;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        background-color: #ff4c13;
        border: 4px solid #ff4c13;
        color: #fff;
        text-align: center;
        animation: notification 2.2s linear;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
    }
    @keyframes bell {
        0%, 25%, 75%, 100% {
            transform: rotate(0deg);
        }
        40% {
            transform: rotate(10deg);
        }
        45% {
            transform: rotate(-10deg);
        }
        55% {
            transform: rotate(8deg);
        }
        60% {
            transform: rotate(-8deg);
        }
    }
    @keyframes bellClapper {
        0%, 25%, 75%, 100% {
            transform: translateX(0);
        }
        40% {
            transform: translateX(-0.15em);
        }
        45% {
            transform: translateX(0.15em);
        }
        55% {
            transform: translateX(-0.1em);
        }
        60% {
            transform: translateX(0.1em);
        }
    }
    @keyframes notification {
        0%, 25%, 75%, 100% {
            opacity: 1;
        }
        30%, 70% {
            opacity: 0;
        }
    }

</style>

<script type="text/javascript">

    function formSubmit(e) {
        var value = $(e).data('value');

        $('.lang_select').val(value);

        $('#lang-form').submit();

    }

    $(document).ready(function() {

        $('#sidebar ul li ul').removeClass('hide');

        /*$('#sidebar').hover(function () {

            $('#sidebar').removeClass('active');

        }, function(){
            $('#sidebar').addClass('active');
        });*/

        $('.toggle-aside-nav').on('click', function () {

            $(this).parent().removeClass('sub-show');

            if($(window).innerWidth() <= 768)
            {
                $('#sidebar').css('overflow-y','');
            }

        });

        $('#sidebarCollapse1').on('click', function () {

            $('#sidebar').css('overflow-y','');

            if($(window).innerWidth() <= 768)
            {
                $('#sidebar ul li ul').removeClass('sub-show');
            }

            $('#sidebar').toggleClass('active');

            /*if($('#sidebar').hasClass('active'))
            {
                $('#content').removeClass('transform-it2');
                $('#content').addClass('transform-it');
            }
            else
            {
                $('#content').removeClass('transform-it');
                $('#content').addClass('transform-it2');
            }*/
        });

        $('#sidebar ul li a').on('click', function () {

            $('#sidebar ul li ul').not($(this).next('ul')).removeClass('sub-show');
            $(this).next('ul').toggleClass('sub-show');

            if($(window).innerWidth() <= 768)
            {
                if($(this).next('ul').hasClass('sub-show'))
                {
                    $('#sidebar').css('overflow-y','hidden');
                }
                else
                {
                    $('#sidebar').css('overflow-y','');
                }
            }

        });

    });

</script>

<script src="{{asset('assets/admin/js/bootstrap-colorpicker.js')}}"></script>
@if(Route::currentRouteName() != 'chat')
    <script src="{{asset('assets/admin/js/bootstrap.min.js')}}"></script>
    @include("user.notes_js")
@endif
<script src="{{asset('assets/admin/js/perfect-scrollbar.jquery.min.js')}}"></script>
<script src="{{asset('assets/admin/js/jquery.canvasjs.min.js')}}"></script>
<script src="{{asset('assets/admin/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/admin/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/admin/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/admin/js/main.js')}}"></script>
<script src="{{asset('assets/admin/js/admin-main.js')}}"></script>

@yield('scripts')

</body>
</html>
