<script>

    $("#task_date").datetimepicker({
        format: 'DD-MM-YYYY',
        locale:'du',
        ignoreReadonly: true
    });

    $("#task_client").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Customer")}}',
        allowClear: true,
        dropdownParent: $('#taskModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $("#task_supplier").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Supplier")}}',
        allowClear: true,
        dropdownParent: $('#taskModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $("#task_employee").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Employee")}}',
        allowClear: true,
        dropdownParent: $('#taskModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $(document).on('click', '.add-task', function (e) {

        $("#task_id").val("");
        $("#task_title").val("");
        $("#task_details").val("");
        $("#task_date").val("");
        $("#task_client").val("");
        $("#task_finished").val(0);
        $("#task_client").trigger('change.select2');
        $("#task_supplier").val("");
        $("#task_supplier").trigger('change.select2');
        $("#task_employee").val("");
        $("#task_employee").trigger('change.select2');
        $("#taskModalLabel").text("{{__('text.Add task')}}");
        $(".delete-task").hide();
        $('#taskModal').modal('toggle');

    });

    $(document).on('click', '.edit-task', function (e) {

        var parent = $(this).parents(".todo-item");
        var task_id = parent.data("id");
        var title = parent.find(".todo-title").text();
        var details = parent.data("details");
        var date = parent.data("date");
        var client_id = parent.data("client");
        var supplier_id = parent.data("supplier");
        var employee_id = parent.data("employee");
        var finished = parent.data("finished");

        $("#task_id").val(task_id);
        $("#task_title").val(title);
        $("#task_details").val(details);
        $("#task_date").val(date);
        $("#task_finished").val(finished);
        $("#task_client").val(client_id);
        $("#task_client").trigger('change.select2');
        $("#task_supplier").val(supplier_id);
        $("#task_supplier").trigger('change.select2');
        $("#task_employee").val(employee_id);
        $("#task_employee").trigger('change.select2');
        $("#taskModalLabel").text("{{__('text.Edit task')}}");
        $(".delete-task").show();
        $('#taskModal').modal('toggle');

    });

    $(document).on('click', '.delete-task', function (e) {

        var task_id = $(this).parents("#taskModal").find("#task_id").val();
        var token = $("[name='_token']").val();
        
        var jsonObject = {
            type: 3,
            task_id: task_id,
            _token: token
        };

        var req = JSON.stringify(jsonObject);
        task_request(req);
        
        $.ajax({
            type: "POST",
            data: "note_id=" + note_id + "&_token=" + token,
            url: '{{route("delete-note")}}',

            success: function (data) {

                $(".note-container").find('.note[data-id="'+note_id+'"]').remove();
                $(".filter-notes").find('.filter-note-item[data-select="'+note_id+'"]').remove();
            
            },
            error: function (data) {}

        });

    });

    function filter_tasks()
    {
        var status = $(".filter-tasks .filter-task-item[selected]").data("select");
        var search = $(".search-tasks").val().toLowerCase();

        $('.todo-container .card').each(function() {
            
            var allHidden = true;
            var child_length = $(this).find(".todo-item").length;

            $(this).find(".todo-item").each(function() {
            
                var search_flag = 0;
                var filter_flag = 0;
                var status_id = $(this).data("finished");

                if($(this).text().toLowerCase().indexOf(""+search+"") != -1){
                    search_flag = 1;
                }

                if(status == "all-tasks" || status == status_id)
                {
                    filter_flag = 1;
                }

                if(!search_flag || !filter_flag)
                {
                    $(this).hide();
                }
                else
                {
                    $(this).show();
                    allHidden = false;
                }

            });

            if(child_length == 0)
            {
                $(this).prev(".todo-date-title").remove();
                $(this).remove();
            }
            else
            {
                if (allHidden) {
                    $(this).prev(".todo-date-title").hide();
                    $(this).hide();
                }
                else
                {
                    $(this).prev(".todo-date-title").show();
                    $(this).show();
                }
            }

        });
    }

    $(document).on('click', '.filter-task-item', function (e) {

        $(this).attr("selected",true);
        $(".filter-tasks .filter-task-item").not(this).attr("selected",false);

        filter_tasks();
        $(".filter-toggle").text($(this).text());
        
    });

    // jQuery.expr[':'].contains = function(a, i, m) {
    //     return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    // };

    $(document).on('input', '.search-tasks', function (e) {

        filter_tasks();
        
    });

    function task_request(req)
    {
        var request = JSON.parse(req);

        $.ajax({
            type: "POST",
            data: request,
            url: '{{route("store-new-task")}}',

            success: function (data) {

                var tasks_container = $(".todo-container");
                var date = data.modified_date;
                var task_group = tasks_container.find('.card[data-date="'+date+'"]');
                var group_count = task_group.length;

                if(request["type"] == 1 || request["type"] == 3)
                {
                    $('#taskModal').modal('toggle');

                    if(request["task_id"])
                    {
                        tasks_container.find('.todo-item[data-id="'+request["task_id"]+'"]').remove();
                    }
                }
                else
                {
                    tasks_container.find('.todo-item[data-id="'+request["task_id"]+'"]').data("finished",request["finished"]);
                }


                var item_class = data.finished ? 'todo-task-done' : '';
                var checked = data.finished ? 'checked' : '';

                var list = '<li class="todo-item '+item_class+'" data-id="'+data.id+'" data-details="'+data.details+'" data-date="'+data.modified_date1+'" data-finished="'+data.finished+'" data-client="'+data.customer_id+'" data-supplier="'+data.supplier_id+'" data-employee="'+data.employee_id+'">\n' +
                                '<div class="custom-control custom-checkbox">\n' +
                                    '<input type="checkbox" '+checked+' class="custom-control-input task-check" id="customCheck'+data.id+'">\n' +
                                    '<label class="custom-control-label" for="customCheck'+data.id+'">&nbsp;</label>\n' +
                                '</div>\n' +
                                '<h6 class="todo-title edit-task">'+data.title+'</h6>\n' +
                            '</li>';

                if(group_count)
                {
                    if(request["type"] == 1)
                    {
                        task_group.find(".todo-list").prepend(list);
                    }
                    
                    task_group.prev(".todo-date-title").find(".group-pending").text(data.remaining_tasks);
                }
                else
                {
                    var $task = $('<div class="todo-date-title">\n' +
                    '<h6 class="mb-0 group-date">'+data.modified_date2+'</h6>\n' +
                    '<p class="text-muted group-pending">'+data.remaining_tasks+'</p>\n' +
                    '</div>\n' +
                    '<div data-date="'+data.modified_date+'" class="card"><div class="card-body">\n' +
                        '<ul class="todo-list">\n' + list + '</ul>\n' +
                    '</div></div>');

                    var flag1 = 0;
                    var $children = tasks_container.find(".card");
    
                    $($children.get().reverse()).each(function() {

                        var group_date = $(this).data("date");

                        if(date < group_date)
                        {
                            $task.insertAfter($(this));
                            flag1 = 1;
                        }

                    });

                    if(!flag1)
                    {
                        tasks_container.prepend($task);
                    }
                }

                var $current = tasks_container.find('.card[data-date="'+date+'"] .todo-list');
                var $items = $current.children('.todo-item'); // Get all child elements with class 'todo-item'
    
                // Sort the items based on the data-id attribute
                $items.sort(function(a, b) {
                    var dataIdA = $(a).data('id');
                    var dataIdB = $(b).data('id');
                    return dataIdB - dataIdA;
                });

                // Append the sorted items back to the container
                $current.empty().append($items);

                filter_tasks();
            },
            error: function (data) {}
        });
    }

    $(document).on('change', '.task-check', function (e) {

        var item = $(this).parents(".todo-item");
        var task_id = item.data("id");
        var finished = $(this).prop('checked');
        finished = finished ? 1 : 0;
        var token = $("[name='_token']").val();
        var jsonObject = {
            type: 2,
            task_id: task_id,
            finished: finished,
            _token: token
        };

        var req = JSON.stringify(jsonObject);
        task_request(req);

    });

    $(document).on('click', '.save-task', function (e) {

        var task_id = $("#task_id").val();
        var customer_id = $("#task_client").val();
        customer_id = customer_id != undefined ? customer_id : "";
        var supplier_id = $("#task_supplier").val();
        supplier_id = supplier_id != undefined ? supplier_id : "";
        var employee_id = $("#task_employee").val();
        employee_id = employee_id != undefined ? employee_id : "";
        var title = $("#task_title").val();
        var details = $("#task_details").val();
        var date = $("#task_date").val();
        var finished = $("#task_finished").val();
        var token = $("[name='_token']").val();
        var flag = 0;

        if(!title)
        {
            $("#task_title").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#task_title").css('border', '');
        }

        if(!details)
        {
            $("#task_details").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#task_details").css('border', '');
        }

        if(!flag)
        {
            var jsonObject = {
                type: 1,
                task_id: task_id,
                customer: customer_id,
                supplier: supplier_id,
                employee: employee_id,
                title: title,
                details: details,
                date: date,
                finished: finished,
                _token: token
            };

            var req = JSON.stringify(jsonObject);
            task_request(req);
        }

    });

</script>