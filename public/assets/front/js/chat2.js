const WS_URL = $('meta[name=ws_url]').attr("content");
const User = window.User;
const user_id = User.id;
const user_avatar = User.avatar;
var socket = io(WS_URL, { query: "id= "+user_id });

socket.on("connect_error", (err) => {
    console.log(`connect_error due to ${err.message}`);
});

var app = new Vue({
    el: '#chatApp',
    data: {
        user_id: user_id,
        chatLists: [],
        bg_array: ["bg-info","bg-primary","bg-secondary","bg-success","bg-danger","bg-dark"],
        chatBox: [],
        socketConnected : {
            status: false,
            msg: 'Connecting Please Wait...'
        },
        bArr: {}
    },
    methods: {
        bg_string: function(id) {
            // const idx = Math.floor(Math.random() * this.bg_array.length);
            return this.bg_array[id % this.bg_array.length];
        },
        getImg: function (image) {

            if(image)
            {
                var http = new XMLHttpRequest();
                http.open('HEAD', '/assets/images/' + image, false);
                http.send();
                return http.status == 200 ? true : false;
            }
            else
            {
                return false;
            }

        },
        chat: function(chat,event){

            this.chatBox.forEach(function(item) {
                app.chatBoxClose(item);
            });

            // $("#chatContactTab>.contacts-item.active").removeClass("active");
            // $(event.currentTarget).removeClass("unread");
            // $(event.currentTarget).addClass("active");
            chat.msgCount = 0;
            chat.unseen_messages_count = 0;
            chat.recentMsg = null;
            chat.active = true;
            const chatboxObj = Vue.extend(chatbox);
            let b = new chatboxObj({
                propsData: {
                    socket: socket,
                    user_id: user_id,
                    user_avatar: user_avatar,
                    cChat: chat,
                    chatBoxClose: this.chatBoxClose,
                    chatBoxMinimize: this.chatBoxMinimize
                }
            }).$mount();
            $('.chats').empty();
            $('.chats').append(b.$el);
            this.bArr[chat.id] = b;
            this.chatBox.unshift(chat.id);
            $('#msginput-'+chat.id).focus();
            // this.calcChatBoxStyle();
            $('.chat-content').magnificPopup({
                delegate: 'a.popup-media',
                type: 'image',
                gallery: {
                  enabled: true,
                  navigateByImgClick: true,
                  preload: [0,1] // Will preload 0 - before current, and 1 after the current image
                }
            });

            $(".message-input").keydown(function(e){
                if (e.keyCode == 13 && !e.shiftKey)
                {
                    e.preventDefault();
                    return false;
                }
            });
        },
        chatBoxClose: function(eleId){
            this.chatLists.find(x => x.id === eleId).active = false;
            $('#chatbox-'+eleId).remove();
            this.bArr[eleId].$destroy();
            var index = this.chatBox.indexOf(eleId);
            this.chatBox.splice(index, 1);
            this.calcChatBoxStyle();
        },
        chatBoxMinimize: function(eleId){
        	$("#chatbox-"+eleId+" .box-body, #chatbox-"+eleId+" .box-footer").slideToggle('slow');
        },
        calcChatBoxStyle(){
            var i = 270; // start position
            var j = 260;  //next position
            this.chatBox.filter(function (value, key) {
                if(key < 4){
                    $('#chatbox-'+value).css("right",i).show();
                    i = i + j;
                }else {
                    $('#chatbox-'+value).hide();
                }

            });
        }
    }
});
socket.on('connect', function(data){
    app.socketConnected.status = true;
    socket.emit('chatList',app.user_id);
});
socket.on('connect_error', function(){
    app.socketConnected.status = false;
    app.socketConnected.msg = 'Could not connect to server';
    app.chatLists = [];
});
socket.on('chatListRes', function(data){
    console.log(data);
    if (data.userDisconnected) {
        app.chatLists.findIndex(function(el) {
            if(el.socket_id == data.socket_id){
                el.online = 'N';
                el.socket_id = '';
            }
        });
    }else if (data.userConnected) {
        app.chatLists.findIndex(function(el) {
            if(el.id == data.userId){
                el.online = 'Y';
                el.socket_id = data.socket_id;
            }
        });
    }else {
        data.chatList.findIndex(function(el) {
            el.msgCount = 0;
            el.active = false;
        });
        app.chatLists = data.chatList;
    }
});
// user chat box not open, count incomming  messages
socket.on('addMessageResponse', function(data){
    if(!app.chatBox.includes(data.fromUserId)){
        app.chatLists.findIndex(function(el) {
            if(el.id == data.fromUserId){
                el.recentMsg = data.message;
                el.msgCount += 1;
            }
        });
    }
});

var chatbox = {
    data: function () {
        return {
            messages: [],
            message: '',
            typing: '',
            timeout: '',
            imgLoaded: 0,
            imagesCount: 0
        }
    },
    props: ['user_id','user_avatar','cChat', 'socket', 'chatBoxClose', 'chatBoxMinimize'],
    mounted: function(){
        socket.emit('getMessages', {fromUserId: this.user_id,toUserId: this.cChat.id});
        socket.on('getMessagesResponse', this.getMessagesResponse);
        socket.on('addMessageResponse', this.addMessageResponse);
        socket.on('typing', this.typingListener);
        socket.on('image-uploaded', this.imageuploaded);
    },
    destroyed: function() {
        socket.removeListener('getMessagesResponse', this.getMessagesResponse);
        socket.removeListener('addMessageResponse', this.addMessageResponse);
        socket.removeListener('typing', this.typingListener);
    },
    methods: {
        handleLoad: function(event){
            this.imgLoaded++;
            if(this.imagesCount == this.imgLoaded)
            {
                this.scrollToBottom();
            }
        },
        sendMessage: function(event = null){
            if(!event || event.keyCode === 13){
                if (this.message.length > 0) {
                    let messagePacket = this.createMsgObj('text', '', this.message);
                    this.socket.emit('addMessage', messagePacket);
                    this.messages.push(messagePacket);
                    this.message = "";
                    this.scrollToBottom();
                }else{
                    alert("Please Enter Your Message.");
                }
            }else{
                if((event.keyCode != 116) && (event.keyCode != 82 && !event.ctrlKey)){
                    this.socket.emit('typing', {typing:'typing...',socket_id:this.cChat.socket_id});
                    clearTimeout(this.timeout);
                    this.timeout = setTimeout(this.timeoutFunction, 500);
                }
            }
        },
        triggerKey: function(event){
            this.sendMessage(event);
        },
        triggerClick: function(){
            this.sendMessage();
        },
        timeoutFunction: function(){
            socket.emit("typing", {typing:false,socket_id:this.cChat.socket_id});
        },
        scrollToBottom: function(){
            $("#messageBody").animate({ scrollTop: $('#messageBody').prop("scrollHeight")}, 1000);
            // let element = document.querySelectorAll("#messageBody .container .message-day .message:last-child")[0];
            // element.scrollIntoView({behavior: 'smooth',block: 'end'});
        },
        createMsgObj : function(type, fileFormat, message){
            return {
                type: type,
                fileFormat: fileFormat,
                filePath: '',
                fromUserId: this.user_id,
                toUserId: this.cChat.id,
                toSocketId: this.cChat.socket_id,
                message: message,
                time: new moment().format("hh:mm A"),
                date: new moment().format("Y-MM-D")
            }
        },
        addMessageResponse: function(data){
            if (data && data.fromUserId == this.cChat.id) {
                this.messages.push(data);
                this.scrollToBottom();
            }
        },
        typingListener: function(data){
            if (data.typing && data.to_socket_id == this.cChat.socket_id) {
                this.typing = "is "+data.typing;
            } else {
                this.typing = "";
            }
        },
        getMessagesResponse: function(data){
            if (data.toUserId == this.cChat.id) {
                this.messages = data.result;
                var count = 0;
                $.each(this.messages, function(i,v) { if (v.fileFormat === "image") count++; });
                this.imagesCount = count;
                // this.$nextTick(function () {
                //     setTimeout(() => {
                //         this.scrollToBottom();
                //     }, 1000);
                // });
            }
        },
        imageuploaded: function(data){
            if (data && data.toUserId == this.cChat.id) {
                $(".overlay").parent().parent().remove();
                this.messages.push(data);
                this.scrollToBottom();
            }
        },
        file: function(event){
            var file = event.target.files[0];
            if (this.validateSize(file)) {
                let fileFormat = file.type.split('/')[0];
                let reader  = new FileReader();
                reader.onload = function (e) {
                    let messagePacket = this.createMsgObj('file', fileFormat, reader.result);
                    messagePacket['fileName'] = file.name;
                    socket.emit('upload-image', messagePacket);
                    // messagePacket.type = "file";
                    // messagePacket.message = file.name;
                    // if(fileFormat != 'image'){
                    //     messagePacket.message = '<span class="info-box-icon bg-primary" style="color: white;background:none;"><i class="fa fa-paperclip"></i></span><div class="overlay"><i style="color:#fff" class="fa fa-refresh fa-spin"></i></div>';
                    // }else {
                    //     let src = URL.createObjectURL(new Blob([reader.result]));
                    //     messagePacket.message = '<img height="100px;" width="100px;" src="'+src+'"><div class="overlay"><i style="color:#fff" class="fa fa-refresh fa-spin"></i></div>';
                    // }
                    // this.messages.push(messagePacket);
                    // this.scrollToBottom();
                }.bind(this);
                reader.readAsArrayBuffer(file);
            }else {
                event.target.value = "";
                alert('File size exceeds 10 MB');
            }
        },
        validateSize: function(file) {
            var fileSize = file.size / 1024 / 1024; // in MB
            if (fileSize > 10) {
                return false;
            }
            return true;
        }
    },
    filters: {
        dateFormat: function(value) {
            return new moment(value).format("D-MMM-YY")
        }
    },
    template: `
        <div class="chat-body">
            <div class="chat-header">
                <button class="btn btn-secondary btn-icon btn-minimal btn-sm text-muted d-xl-none" type="button" data-close="">
                    <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </button>
                <div class="media chat-name align-items-center text-truncate">
                    <div class="avatar d-none d-sm-inline-block mr-3" v-bind:class="{'avatar-online': (cChat.online=='Y')}">
                        <img v-if="cChat.photo" v-bind:src="'/assets/images/' + cChat.photo" alt="">
                        <img v-else v-bind:src="'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'" alt="">
                    </div>
                    <div class="media-body align-self-center">
                        <h6 class="text-truncate mb-0">{{ cChat.name }} <span style="font-size: 12px;">{{ typing }}</span></h6>
                        <small class="text-muted">{{cChat.online=='Y' ? 'Online' : 'Offline'}}</small>
                    </div>
                </div>
                <ul class="nav flex-nowrap">
                    <li class="nav-item list-inline-item d-none d-sm-block mr-1">
                        <a class="nav-link text-muted px-1" data-toggle="collapse" data-target="#searchCollapse" href="#" aria-expanded="false">
                            <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item list-inline-item d-none d-sm-block mr-1">
                        <a class="nav-link text-muted px-1" href="#" title="Add People">
                            <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </a>
                    </li>
                    <li class="nav-item list-inline-item d-none d-sm-block mr-0">
                        <div class="dropdown">
                            <a class="nav-link text-muted px-1" href="#" role="button" title="Details" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item align-items-center d-flex" href="#" data-chat-info-toggle="">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>View Info</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                                    </svg>
                                    <span>Mute Notifications</span> 
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Wallpaper</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    <span>Archive</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Delete</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex text-danger" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    <span>Block</span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item list-inline-item d-sm-none mr-0">
                        <div class="dropdown">
                            <a class="nav-link text-muted px-1" href="#" role="button" title="Details" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>Call</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#" data-toggle="collapse" data-target="#searchCollapse" aria-expanded="false">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <span>Search</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#" data-chat-info-toggle="">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>View Info</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" clip-rule="evenodd"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                                    </svg>
                                    <span>Mute Notifications</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Wallpaper</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    <span>Archive</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Delete</span>
                                </a>
                                <a class="dropdown-item align-items-center d-flex text-danger" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    <span>Block</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="collapse border-bottom px-3" id="searchCollapse">
                <div class="container-xl py-2 px-0 px-md-3">
                    <div class="input-group bg-light ">
                        <input type="text" class="form-control form-control-md border-right-0 transparent-bg pr-0" placeholder="Search">
                        <div class="input-group-append">
                            <span class="input-group-text transparent-bg border-left-0">
                                <svg class="hw-20 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chat-content p-2" id="messageBody">
                <div class="container">
                    <div class="message-day">
                        <div v-for="messagePacket in messages" class="message" v-bind:class="{ 'self' : (messagePacket.fromUserId == user_id) }">
                            <div class="message-wrapper">
                                <div class="message-content">
                                    <span v-if="messagePacket.type == 'text'" v-html=messagePacket.message></span>
                                    <div v-if="messagePacket.type == 'file' && messagePacket.fileFormat == 'image'" class="form-row">
                                        <div class="col">
                                            <a :href="'`+WS_URL+'media/'+`' + messagePacket.filePath" :title="messagePacket.message" class="popup-media">
                                                <img @load="handleLoad($event)"  @error="handleLoad($event)" class="img-fluid rounded" :src="'`+WS_URL+'media/'+`' + messagePacket.filePath" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div v-else-if="messagePacket.type == 'file' && messagePacket.fileFormat != 'image'" class="document">
                                        <div class="btn btn-primary btn-icon rounded-circle text-light mr-2">
                                            <svg class="hw-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="document-body">
                                            <h6>
                                                <a :href="'`+WS_URL+'media/'+`' + messagePacket.filePath" :title="messagePacket.message" class="text-reset">{{messagePacket.message}}</a>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="message-options">
                                <div v-if="messagePacket.fromUserId == user_id" class="avatar avatar-sm">
                                    <img v-if="user_avatar" v-bind:src="'/assets/images/' + user_avatar" alt="">
                                    <img v-else v-bind:src="'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'" alt="">
                                </div>
                                <div v-else class="avatar avatar-sm">
                                    <img v-if="cChat.photo" v-bind:src="'/assets/images/' + cChat.photo" alt="">
                                    <img v-else v-bind:src="'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcSCM_FnlKpZr_N7Pej8GA40qv63zVgNc0MFfejo35drsuxLUcYG'" alt="">
                                </div>
                                <span class="message-date">{{ messagePacket.date | dateFormat }},{{ messagePacket.time }}</span>
                                <div class="dropdown">
                                    <a class="text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="hw-18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                        </svg>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <svg class="hw-18 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Copy</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <svg class="hw-18 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            <span>Replay</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <svg class="hw-18 rotate-y mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            <span>Forward</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center" href="#">
                                            <svg class="hw-18 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                            <span>Favourite</span>
                                        </a>
                                        <a class="dropdown-item d-flex align-items-center text-danger" href="#">
                                            <svg class="hw-18 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Delete</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-finished" id="chat-finished"></div>
                </div>
            </div>
            <div class="chat-footer">
                <div class="attachment">
                    <div class="dropdown">
                            <button class="btn btn-secondary btn-icon btn-minimal btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg class="hw-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <div style="padding: 0;" class="dropdown-item">
                                    <label style="width: 100%;margin: 0;padding: 0.5rem 1.5rem;" for="upload-media">
                                        <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Media</span>
                                    </label>
                                    <input style="display: none;" id="upload-media" name="attachment" type="file" v-on:change="file($event)">
                                </div>
                                <a class="dropdown-item" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                    </svg>
                                    <span>Audio</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Document</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Contact</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Location</span>
                                </a>
                                <a class="dropdown-item" href="#">
                                    <svg class="hw-20 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Poll</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <textarea v-bind:id="'msginput-' + cChat.id" v-model.trim="message" v-on:keydown="triggerKey($event)" class="form-control emojionearea-form-control message-input" rows="1" placeholder="Type your message here..."></textarea>
                    <div @click="triggerClick($event)" class="btn btn-primary btn-icon send-icon rounded-circle text-light mb-1" role="button">
                        <svg class="hw-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>`
};
