<!-- Modal 5 :: Add Note -->
<div class="modal modal-lg-fullscreen fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNoteModalLabel">{{__('text.Add new note')}}</h5>
                <div style="display: flex;">
                    <a href="{{route('notes')}}" style="padding: 0 5px;background-color: black;width: 35px;margin-right: 10px;" class="btn">
                        <svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="eye"><g data-name="Layer 2"><g data-name="eye"><rect width="24" height="24" opacity="0"></rect><path d="M21.87 11.5c-.64-1.11-4.16-6.68-10.14-6.5-5.53.14-8.73 5-9.6 6.5a1 1 0 0 0 0 1c.63 1.09 4 6.5 9.89 6.5h.25c5.53-.14 8.74-5 9.6-6.5a1 1 0 0 0 0-1zM12.22 17c-4.31.1-7.12-3.59-8-5 1-1.61 3.61-4.9 7.61-5 4.29-.11 7.11 3.59 8 5-1.03 1.61-3.61 4.9-7.61 5z"></path><path d="M12 8.5a3.5 3.5 0 1 0 3.5 3.5A3.5 3.5 0 0 0 12 8.5zm0 5a1.5 1.5 0 1 1 1.5-1.5 1.5 1.5 0 0 1-1.5 1.5z"></path></g></g></svg>
                    </a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <form>

                    <input type="hidden" name="note_id" id="note_id">
                    <div class="form-group">
                        <label for="addNoteName" class="col-form-label">{{__('text.Note title')}}:</label>
                        <input type="text" class="form-control" id="addNoteName" value="" placeholder="{{__('text.Add note title here')}}">
                    </div>
                    <div class="form-group">
                        <label for="addNoteDetails" class="col-form-label">{{__('text.Note details')}}:</label>
                        <textarea class="form-control hide-scrollbar" id="addNoteDetails" rows="4" placeholder="{{__('text.Add note descriptions')}}"></textarea>
                    </div>

                    @if(Auth::guard('user')->user()->role_id == 2)

                        <div class="form-group">
                            <label class="col-form-label">{{__('text.Customer')}}:</label>
                            <div style="display: flex;">
                                <div style="padding: 0;flex: auto;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 notes_client_box">
                                    <select id="note_client" class="custom-select font-size-sm shadow-none">
                                        <option value="">{{__("text.Select Customer")}}</option>
                                        @foreach($allData[2] as $key)
                                            <option value="{{$key->id}}">{{$key->name}} {{$key->family_name ? ' '.$key->family_name : ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div style="display: flex;padding-left: 10px;">
                                    <button type="button" href="#createCustomerModal" role="button" data-toggle="modal" style="padding: 0 5px;" class="btn btn-success add-customer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="25" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">{{__('text.Supplier')}}:</label>
                            <div style="display: flex;">
                                <div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 notes_supplier_box">
                                    <select id="note_supplier" class="custom-select font-size-sm shadow-none">
                                        <option value="">{{__("text.Select Supplier")}}</option>
                                        @foreach($allData[3] as $key)
                                            <option value="{{$key->id}}">{{$key->name}} {{$key->family_name ? ' '.$key->family_name : ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    @endif

                    @if(Auth::guard('user')->user()->role_id == 2 || Auth::guard('user')->user()->role_id == 4)

                        <div class="form-group">
                            <label class="col-form-label">{{__('text.Employee')}}:</label>
                            <div style="display: flex;">
                                <div style="padding: 0;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 notes_employee_box">
                                    <select id="note_employee" class="custom-select font-size-sm shadow-none">
                                        <option value="">{{__("text.Select Employee")}}</option>
                                        @foreach($allData[4] as $key)
                                            <option value="{{$key->id}}">{{$key->name}} {{$key->family_name ? ' '.$key->family_name : ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                    @endif
                    
                    <div class="form-group">
                        <label class="col-form-label">{{__('text.Note tag')}}:</label>
                        <div style="display: flex;">
                            <div style="padding: 0;" class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                                <select id="note_tag" class="custom-select font-size-sm shadow-none">
                                    <option value="">{{__("text.Select tag")}}</option>
                                    @foreach($allData[1] as $tag)
                                        <option value="{{$tag->id}}" data-background="{{$tag->background}}">{{$tag->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="display: flex;justify-content: space-between;padding: 0;padding-left: 10px;" class="col-lg-3 col-md-3 col-sm-3 col-xs-3 tag-btns">
                                <button style="padding: 0 5px;" type="button" class="btn btn-primary edit-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"></path>
                                    </svg>
                                </button>
                                <button style="padding: 0 5px;" type="button" class="btn btn-success add-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="25" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">{{__('text.Close')}}</button>
                <button type="button" class="btn btn-primary submit-note">{{__('text.Submit')}}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-lg-fullscreen fade" id="addTagModal" tabindex="-1" role="dialog" aria-labelledby="addTagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTagModalLabel">{{__('text.Add new tag')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" name="tag_id" id="tag_id">
                    <div class="form-group">
                        <label for="addTageName" class="col-form-label">{{__('text.Tag title')}}:</label>
                        <input type="text" class="form-control" id="addTagName" value="" placeholder="{{__('text.Add tag title here')}}">
                    </div>
                    <div class="form-group">
                        <label for="addTagBackground" class="col-form-label">{{__('text.Tag background')}}:</label>
                        <span id="tag_cp" class="tag_cp input-group colorpicker-component">
                            <input class="tag_cp form-control" type="text" id="bg_color" />
                            <span class="input-group-addon"><i></i></span>
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">{{__("text.Close")}}</button>
                <button type="button" class="btn btn-primary submit-tag">{{__('text.Submit')}}</button>
            </div>
        </div>
    </div>
</div>

<style>

    .colorpicker.dropdown-menu
    {
        z-index: 1050;
    }

    #addTagModal .add-product-box .form-horizontal .form-control {
        height: 40px;
        border-radius: 0;
        box-shadow: none;
    }

    #addTagModal .input-group .form-control, #addTagModal .input-group-addon, #addTagModal .input-group-btn
    {
        display: table-cell;
    }

    #addTagModal .input-group .form-control {
        position: relative;
        z-index: 2;
        float: left;
        width: 100%;
        margin-bottom: 0
    }

    #addTagModal .input-group {
        position: relative;
        display: table;
        border-collapse: separate
    }

    #addTagModal .input-group-addon {
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px
    }

    #addTagModal .input-group-addon, #addTagModal .input-group-btn {
        width: 1%;
        white-space: nowrap;
        vertical-align: middle
    }

    #addTagModal .input-group-addon:last-child
    {
        border-left: 0;
    }

    #addTagModal .input-group .form-control:last-child, #addTagModal .input-group-addon:last-child, #addTagModal .input-group-btn:first-child>.btn-group:not(:first-child)>.btn, #addTagModal .input-group-btn:first-child>.btn:not(:first-child), #addTagModal .input-group-btn:last-child>.btn, #addTagModal .input-group-btn:last-child>.btn-group>.btn, #addTagModal .input-group-btn:last-child>.dropdown-toggle {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    #addTagModal .colorpicker-element .input-group-addon i, #addTagModal .colorpicker-element .add-on i {
        display: inline-block;
        cursor: pointer;
        height: 16px;
        vertical-align: text-top;
        width: 16px;
    }

</style>

@if(Route::currentRouteName() != 'chat')

    <style>

        #addNoteModal .select2-container--default .select2-selection--single
        {
            border: 1px solid #aaa;
        }

        #addNoteModal .form-group
        {
            margin-bottom: 25px;
        }

        #addNoteModal .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        @media (min-width: 576px)
        {
            #addNoteModal .modal-dialog {
                max-width:500px;
                margin: 1.75rem auto;
            }

            #addNoteModal .modal-dialog-scrollable {
                max-height: calc(100% - 3.5rem);
            }

            #addNoteModal .modal-dialog-centered {
                min-height: calc(100% - 3.5rem);
            }
        }

        #addNoteModal .modal-dialog-centered.modal-dialog-scrollable {
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        #addNoteModal .modal.fade .modal-dialog {
            transition: transform .3s ease-out;
            transform: translateY(-50px);
        }

        #addNoteModal .modal.in .modal-dialog.modal-dialog-zoom {
            transform: translate(0) scale(1);
        }

        #addNoteModal .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: .3rem;
            outline: 0;
        }

        #addNoteModal .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 1rem);
            overflow: hidden;
        }

        #addNoteModal .modal-dialog-centered.modal-dialog-scrollable .modal-content {
            max-height: none;
        }

        #addNoteModal .modal-dialog-scrollable .modal-footer, #addNoteModal .modal-dialog-scrollable .modal-header
        {
            flex-shrink: 0;
        }

        #addNoteModal .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 2rem;
        }

        #addNoteModal .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }

        #addNoteModal .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: .75rem;
            border-top: 1px solid #e5e9f2;
            border-bottom-right-radius: calc(.3rem - 1px);
            border-bottom-left-radius: calc(.3rem - 1px);
        }

        #addNoteModal .select2-container--default .select2-selection--single .select2-selection__rendered
        {
            border: none;
            line-height: 35px;
        }

        #addNoteModal .select2-container .select2-selection--single, #addNoteModal .select2-container--default .select2-selection--single .select2-selection__arrow
        {
            height: 35px;
        }

        #addNoteModal .select2-search__field
        {
            outline: none;
        }

        svg
        {
            overflow: hidden;
            vertical-align: middle;
        }

        #addNoteModal .modal-header, #addTagModal .modal-header{
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid #e5e9f2;
            border-top-left-radius: calc(.3rem - 1px);
            border-top-right-radius: calc(.3rem - 1px);
        }

        #addNoteModal .modal-title, #addTagModal .modal-title
        {
            font-size: 18px;
        }

        #addNoteModal .close, #addTagModal .close
        {
            font-size: 27px;
        }

        #addNoteModal .form-control
        {
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        #addNoteModal .custom-select {
            display: inline-block;
            width: 100%;
            height: 100%;
            outline: none !important;
            padding: .375rem 1.75rem .375rem .75rem;
            font-size: 1.5rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            vertical-align: middle;
            background: #fff url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='5'%3E%3Cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E") no-repeat right .75rem center/8px 10px;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        #addNoteModal .tag-btns
        {
            justify-content: flex-end !important;
        }

        #addNoteModal .edit-tag, #addNoteModal .add-tag
        {
            padding: 7px 5px !important;
        }

        #addNoteModal .add-tag
        {
            margin-left: 5px;
        }

        #addNoteModal .modal-footer:after, #addTagModal .modal-footer:after, #addNoteModal .modal-footer:before, #addTagModal .modal-footer:before, #addNoteModal .modal-header:after, #addTagModal .modal-header:after, #addNoteModal .modal-header:before, #addTagModal .modal-header:before
        {
            display: none;
        }

    </style>

@endif