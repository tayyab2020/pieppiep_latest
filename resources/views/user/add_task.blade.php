<!-- Modal 6 :: Add Task -->
<div class="modal modal-lg-fullscreen fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog-zoom">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">{{__("text.Add task")}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
    
                    <input type="hidden" name="task_id" id="task_id">
                    <input type="hidden" name="task_finished" id="task_finished">
                    <div class="form-group">
                        <label for="task_title" class="col-form-label">{{__("text.Task title")}}:</label>
                        <input type="text" class="form-control" id="task_title" placeholder="{{__('text.Add task name here')}}">
                    </div>
    
                    <div class="form-group">
                        <label for="task_details" class="col-form-label">{{__("text.Task details")}}:</label>
                        <textarea class="form-control hide-scrollbar" id="task_details" rows="4" placeholder="{{__('text.Add task descriptions')}}"></textarea>
                    </div>
    
                    <div class="form-group">
                        <label for="task_date" class="col-form-label">{{__("text.Task date")}}:</label>
                        <input type="text" style="background-color: transparent;" readonly class="form-control" id="task_date" placeholder="{{__('text.Add task date here')}}">
                    </div>
    
                    @if(Auth::guard('user')->user()->role_id == 2)
                    
                        <div class="form-group">
                            <label class="col-form-label">{{__('text.Customer')}}:</label>
                            <div style="display: flex;">
                                <div style="padding: 0;flex: auto;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 notes_client_box">
                                    <select id="task_client" class="custom-select font-size-sm shadow-none">
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
                                    <select id="task_supplier" class="custom-select font-size-sm shadow-none">
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
                                    <select id="task_employee" class="custom-select font-size-sm shadow-none">
                                        <option value="">{{__("text.Select Employee")}}</option>
                                        @foreach($allData[4] as $key)
                                            <option value="{{$key->id}}">{{$key->name}} {{$key->family_name ? ' '.$key->family_name : ''}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
    
                    @endif
    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">{{__("text.Close")}}</button>
                <button type="button" class="btn btn-danger delete-task">{{__("text.Delete")}}</button>
                <button type="button" class="btn btn-primary save-task">{{__("text.Save")}}</button>
            </div>
        </div>
    </div>
</div>