<div data-id="{{$note->id}}" data-client="{{$note->customer_id}}" data-supplier="{{$note->supplier_id}}" data-employee="{{$note->employee_id}}" class="note">
    <div class="note-body">
        <div class="note-upper">
            <div class="note-added-on">{{date("l, d/m/Y H:i",strtotime($note->created_at))}}</div>
            <div class="note-employee">{{$note->employee_fname . ($note->employee_lname ? " " . $note->employee_lname : "")}}</div>
        </div>
        <h5 class="note-title">{{$note->title}}</h5>
        <p class="note-description">{{$note->details}}</p>
    </div>
    <div class="note-footer">
        <div class="note-tools">
            <span data-tag_id="{{$note->tag_id}}" style="color: white;background: {{$note->background}};" class="badge tag">{{$note->tag_title}}</span>
        </div>
        <div class="note-customer-supplier">{{$note->customer_fname ? $note->customer_fname . ($note->customer_lname ? " " . $note->customer_lname : "") : ($note->supplier_fname ? $note->supplier_fname . ($note->supplier_lname ? " " . $note->supplier_lname : "") : "")}}</div>
        <div class="note-tools">
            <div class="dropdown">
                <button class="btn btn-secondary btn-icon btn-minimal btn-sm text-muted" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- <img class="injectable hw-20" src="./../assets/media/heroicons/outline/dots-vertical.svg" alt=""> -->
                    <svg class="hw-20" xmlns="http://www.w3.org/2000/svg" height="24" width="24"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item text-success edit-note" href="javascript:void(0)">{{__("text.Edit")}}</a>
                    <a class="dropdown-item text-danger delete-note" href="javascript:void(0)">{{__("text.Delete")}}</a>
                </div>
            </div>
        </div>
    </div>
</div>