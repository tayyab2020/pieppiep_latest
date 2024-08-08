@extends('layouts.handyman')

@section('chat')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="ws_url" content="http://localhost:3000/">
    <meta name="user_id" content="{{ Auth::id() }}">
    <link href="{{ asset('assets/front/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/front/css/AdminLTE.min.css') }}" rel="stylesheet">

@endsection

@section('styles')
    <style media="screen">
    .online{
        color: #32CD32;
    }
    .ffside {
        height: 100%;
        position: fixed;
        z-index: 1;
        top: 85px;
        right: 0;
        width: 18em;
        overflow-x: hidden;
        padding-top: 0;
    }
    .chat_box{
        width:260px;
        padding: 5px;
        position: fixed;
        bottom: 0px;
    }
    </style>
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2" id="chatApp">
                <div class="panel panel-default ffside">
                    <div class="panel-heading">Users</div>
                    <div class="panel-body" style="padding:0px;">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="chatList in chatLists" style="cursor: pointer;" @click="chat(chatList)">@{{ chatList.name }}  <i class="fa fa-circle pull-right" v-bind:class="{'online': (chatList.online=='Y')}"></i>  <span class="badge" v-if="chatList.msgCount !=0">@{{ chatList.msgCount }}</span></li>
                            <li class="list-group-item" v-if="socketConnected.status == false">@{{ socketConnected.msg }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    
    <script src="{{ asset('assets/front/js/vue.js') }}"></script>
    <script src="{{ asset('assets/front/js/socket.io.js') }}"></script>
    <script src="{{ asset('assets/front/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/front/js/chat.js') }}" charset="utf-8"></script>

@endsection
