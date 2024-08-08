<div class="appnavbar-body">
    <div class="appnavbar-body-title">
        <!-- Sidebar Header Dropdown Start -->
        <div class="dropdown mr-2">
            <!-- Dropdown Button Start -->
            <button class="btn btn-outline-default dropdown-toggle filter-toggle" type="button" data-tasks-filter-list="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{__("text.All Tasks")}}</button>
            <!-- Dropdown Button End -->

            <!-- Dropdown Menu Start -->
            <div class="dropdown-menu filter-tasks">
                <a class="dropdown-item filter-task-item" selected data-task-filter="" data-select="all-tasks" href="javascript:void(0)">{{__("text.All Tasks")}}</a>
                <a class="dropdown-item filter-task-item" data-task-filter="" data-select="0" href="javascript:void(0)">{{__("text.Active")}}</a>
                <a class="dropdown-item filter-task-item" data-task-filter="" data-select="1" href="javascript:void(0)">{{__("text.Finished")}}</a>
            </div>
            <!-- Dropdown Menu End -->
        </div>
        <!-- Sidebar Header Dropdown End -->

        <!-- Sidebar Search Start -->
        <form class="form-inline">
            <div class="input-group">
                <input type="text" class="form-control search-tasks border-right-0 transparent-bg pr-0" placeholder="{{__('text.Search tasks')}}">
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
    </div>

    <div class="todo-container">

        @foreach($allData[5] as $date => $task_group)

            <?php $pending = $task_group->filter(function($task){ return !$task->finished; })->count(); ?>

            <div class="todo-date-title">
                <h6 class="mb-0 group-date">{{date("d/m/Y",strtotime($date))}}</h6>
                <p class="text-muted group-pending">{{trans_choice('text.Task remaining',$pending)}}</p>
            </div>

            <div data-date="{{strtotime($date)}}" class="card">
                <div class="card-body">
                    <ul class="todo-list">
                        @foreach($task_group as $t => $task)

                            <li class="todo-item {{$task->finished ? 'todo-task-done' : ''}}" data-id="{{$task->id}}" data-details="{{$task->details}}" data-date="{{date('d-m-Y',strtotime($task->date))}}" data-finished="{{$task->finished}}" data-client="{{$task->customer_id}}" data-supplier="{{$task->supplier_id}}" data-employee="{{$task->employee_id}}">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input task-check" id="customCheck{{$task->id}}" {{$task->finished ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="customCheck{{$task->id}}">&nbsp;</label>
                                </div>
                                <h6 class="todo-title edit-task">{{$task->title}}</h6>
                            </li>

                        @endforeach
                    </ul>
                </div>
            </div>

        @endforeach
        
    </div>
</div>

<div class="appnavbar-footer">
    <div class="btn btn-primary btn-block add-task">Add new task</div>
</div>

<style>

    #taskModal .bootstrap-datetimepicker-widget table td.day
    {
        height: 30px;
        line-height: 30px;
    }

    #taskModal .bootstrap-datetimepicker-widget table tr:not(:first-child) th
    {
        padding-top: 10px;
    }

    #taskModal .datepicker
    {
        margin: 5px 0;
    }

    .todo-container .todo-date-title:first-child {
        margin-top: 10px;
    }

    .todo-container .todo-date-title {
        margin-top: 30px;
    }

    .todo-date-title p
    {
        margin-bottom: 10px;
    }

</style>