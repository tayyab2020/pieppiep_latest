@extends('layouts.handyman')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div style="margin: 0;" class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard data-table area -->
                    <h2 style="text-align: center;margin: 10px 0 0 0;">{{__("text.Notes")}}</h2>
                    <div class="action-navbar">
                        <!-- Sidebar Header Dropdown Start -->
                        <div class="dropdown mr-2">
                            <!-- Dropdown Button Start -->
                            <button class="btn btn-outline-default dropdown-toggle filter-toggle" type="button" data-notes-filter-list="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__("text.All Notes")}}</button>
                            <!-- Dropdown Button End -->
                
                            <!-- Dropdown Menu Start -->
                            <div class="dropdown-menu filter-notes">
                                <a class="dropdown-item filter-note-item" selected data-notes-filter="" data-select="all-notes" href="javascript:void(0)">{{__("text.All Notes")}}</a>
                                @foreach($allData[0] as $note)
                                    <a class="dropdown-item filter-note-item" data-notes-filter="" data-select="{{$note->id}}" href="javascript:void(0)">{{$note->title}}</a>
                                @endforeach
                            </div>
                            <!-- Dropdown Menu End -->
                        </div>
                        <!-- Sidebar Header Dropdown End -->
                
                        <!-- Sidebar Search Start -->
                        <form class="form-inline">
                            <div class="input-group">
                                <input type="text" class="form-control search-notes border-right-0 transparent-bg pr-0" placeholder="{{__('text.Search notes')}}">
                                <div class="input-group-append">
                                    <div class="input-group-text transparent-bg border-left-0" role="button">
                                        <!-- Default :: Inline SVG -->
                                        <svg class="text-muted hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                
                                        <!-- Alternate :: External File link -->
                                        <!-- <img class="injectable hw-20" src="./../assets/media/heroicons/outline/search.svg" alt=""> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Sidebar Search End -->
                        <button style="width: auto;margin-left: 5px;" class="btn btn-primary btn-block add-note">{{__('text.Add new note')}}</button>
                    </div>
                    <div class="note-container">
                        @foreach($allData[0] as $note)
                            @include("user.note")
                        @endforeach
                    </div>
                </div>
                <!-- Ending of Dashboard data-table area -->
            </div>
        </div>
    </div>

    @include("user.notes_css")

    <style>

        .action-navbar
        {
            display: flex;
            align-items: center;
            flex-grow: 1;
            padding: 15px;
            background-color: #fff;
            border: 1px solid #e5e9f2;
            margin-top: 30px;
        }

        .text-muted {
            color: #adb5bd!important;
        }

        .mr-2,.mx-2 {
            margin-right: .75rem!important;
        }

        .btn-outline-default {
            border: 1px solid #e5e9f2;
            background: transparent;
        }

        .btn-outline-default:hover {
            background: #f8f9fa;
        }

        .action-navbar .dropdown-toggle
        {
            white-space: nowrap;
        }

        .btn-outline-default:after {
            display: inline-block;
            margin-left: .255em;
            vertical-align: .255em;
            content: "";
            border-top: .3em solid;
            border-right: .3em solid transparent;
            border-bottom: 0;
            border-left: .3em solid transparent;
        }

        .action-navbar .form-inline {
            display: flex;
            flex-flow: row wrap;
            align-items: center;
        }

        .action-navbar .input-group
        {
            display: flex !important;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .input-group-append {
            margin-left: -1px;
        }

        .input-group-append,.input-group-prepend {
            display: flex;
        }

        .input-group>.custom-select,.input-group>.form-control,.input-group>.form-control-plaintext {
            position: relative;
            flex: 1 1 auto;
            min-width: 0;
            margin-bottom: 0;
            border: 1px solid #ced4da;
        }

        .action-navbar .input-group>.form-control
        {
            width: 1%;
        }

        .input-group .form-control:focus
        {
            -webkit-box-shadow: none;
            box-shadow: none;
            border-color: #ccc;
        }

        .input-group-text {
            display: flex;
            align-items: center;
            padding: .375rem .75rem;
            margin-bottom: 0;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            text-align: center;
            white-space: nowrap;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .transparent-bg {
            background-color: transparent;
        }

        .pr-0,.px-0 {
            padding-right: 0!important;
        }

        .border-right-0 {
            border-right: 0!important;
        }

        .border-left-0 {
            border-left: 0!important;
        }

        .note-title
        {
            font-size: 24px;
        }

        .note-container
        {
            padding: 30px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .note-container .note
        {
            border: 1px solid #e5e9f2;
            border-radius: 0.25rem;
            background-color: #fff;
            width: 32%;
            margin: 0.5%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
        }

        .note-container .note .note-body
        {
            padding: 20px;
            width: 100%;
        }

        .note-upper
        {
            display: flex;
            justify-content: space-between;
        }

        .note-container .note .note-added-on
        {
            margin-bottom: 5px;
            font-size: 12px;
            color: #adb5bd;
            font-weight: 500;
        }

        .note-container .note .note-description
        {
            color: #adb5bd;
            margin-bottom: 0;
        }

        .note-container .note .note-footer
        {
            border-top: 1px solid #e5e9f2;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 1rem 2rem;
            width: 100%;
        }

        .note-container .note .note-footer .note-tools
        {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 90%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .dropdown, .dropleft, .dropright, .dropup
        {
            position: relative;
        }

        .btn-icon {
            height: 3rem;
            width: 3rem;
            padding: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-minimal,.btn-outline-default:focus {
            box-shadow: none;
        }

        .btn-minimal {
            color: rgba(33,37,41,.35);
            background-color: transparent!important;
            border: 0;
            transition: all .2s ease;
        }

        [type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled) {
            cursor: pointer;
        }

        .btn-group-sm>.btn-icon.btn,.btn-icon.btn-sm {
            width: calc(1.5rem + 2px);
            line-height: 1.5rem;
        }

        .btn:not(:disabled):not(.disabled) {
            cursor: pointer;
        }

        .btn.focus,.btn:focus {
            outline: 0;
        }

        .btn-secondary.focus,.btn-secondary:focus,.btn-secondary:hover {
            color: #fff;
            background-color: #5a6268;
            border-color: #545b62;
        }

        .btn-secondary.focus,.btn-secondary:focus {
            box-shadow: 0 0 0 .2rem rgba(130,138,145,.5);
        }

        .btn-group-sm>.btn-icon.btn,.btn-icon.btn-sm {
            width: calc(1.5rem + 2px);
            line-height: 1.5rem;
        }

        .btn-minimal:hover {
            color: inherit!important;
        }

        .btn-minimal:focus {
            box-shadow: none!important;
            color: rgba(33,37,41,.35);
        }

        .btn:not(:disabled):not(.disabled) {
            cursor: pointer;
        }

        .btn-secondary:not(:disabled):not(.disabled).active,.btn-secondary:not(:disabled):not(.disabled):active,.show>.btn-secondary.dropdown-toggle {
            color: #fff;
            background-color: #545b62;
            border-color: #4e555b;
        }

        .btn-secondary:not(:disabled):not(.disabled):active {
            color: inherit;
        }

        .btn-secondary:not(:disabled):not(.disabled).active:focus,.btn-secondary:not(:disabled):not(.disabled):active:focus,.show>.btn-secondary.dropdown-toggle:focus {
            box-shadow: 0 0 0 .2rem rgba(130,138,145,.5);
        }

        .hw-20 {
            height: 2.25rem;
            width: 2.25rem;
        }

        .dropdown-menu, .collapse
        {
            display: none;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: .5rem 0;
            margin: .125rem 0 0;
            font-size: 1rem;
            color: #495057;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: .25rem;
        }

        .dropdown-menu-right {
            right: 0;
            left: auto;
        }

        .dropdown .dropdown-menu {
            z-index: 1025;
            font-size: 1.5rem;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: .25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: rgba(73,80,87,.8);
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }

        .text-success {
            color: #44a675!important;
        }

        .text-danger {
            color: #ff337c!important;
        }

        .dropdown .dropdown-menu .dropdown-item {
            padding: .5rem 1.5rem;
            display: flex;
            align-items: center;
        }

        .dropdown-item:focus,.dropdown-item:hover {
            color: #3d4349;
            text-decoration: none;
            background-color: #f8f9fa;
        }

        .form-control
        {
            -webkit-box-shadow: none;
            box-shadow: none;
        }

    </style>

@endsection
