<script>

    $("#note_client").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Customer")}}',
        allowClear: true,
        dropdownParent: $('#addNoteModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $("#note_supplier").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Supplier")}}',
        allowClear: true,
        dropdownParent: $('#addNoteModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $("#note_employee").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Employee")}}',
        allowClear: true,
        dropdownParent: $('#addNoteModal'),
        "language": {
            "noResults": function () {
                return '{{__('text.No results found')}}';
            }
        },
    });

    $('.tag_cp').colorpicker();

    $('#addTagModal').on('hidden.bs.modal', function () {
        $('#addNoteModal').modal('toggle');
    });

    $(document).on('click', '.add-tag', function (e) {

        $("#tag_id").val("");
        $("#addTagName").val("");
        $("#bg_color").val("");
        $("#addTagModalLabel").text("{{__('text.Add new tag')}}");
        $('#addNoteModal').modal('toggle');
        $('#addTagModal').modal('toggle');

    });

    $(document).on('click', '.edit-tag', function (e) {

        var tag = $("#note_tag").val();

        if(tag)
        {
            var title = $("#note_tag option:selected").text();
            var background = $("#note_tag option:selected").data("background");
            $("#tag_id").val(tag);
            $("#addTagName").val(title);
            $('#bg_color').val(background);
            $("#bg_color").trigger('change');
            $("#addTagModalLabel").text("{{__('text.Edit tag')}}");
            $('#addNoteModal').modal('toggle');
            $('#addTagModal').modal('toggle');
        }

    });

    $(document).on('click', '.submit-tag', function (e) {

        var tag_id = $("#tag_id").val();
        var title = $("#addTagName").val();
        var background = $("#bg_color").val();
        var token = $("[name='_token']").val();
        var flag = 0;

        if(!title)
        {
            $("#addTagName").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#addTagName").css('border', '');
        }

        if(!background)
        {
            $("#bg_color").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#bg_color").css('border', '');
        }

        if(!flag)
        {
            $.ajax({
                type: "POST",
                data: "tag_id=" + tag_id + "&title=" + title + "&background=" + background + "&_token=" + token,
                url: '{{route("store-new-tag")}}',

                success: function (data) {

                    var newStateVal = data.id;
                    var newName = data.title;
                    var background_color = data.background;
                    $('#addTagModal').modal('toggle');

                    if(tag_id)
                    {
                        $("#note_tag option[value='"+newStateVal+"']").data("background",background_color);
                        $("#note_tag option[value='"+newStateVal+"']").text(newName);
                        $(".note-container").find('.note .tag[data-tag_id="'+newStateVal+'"]').each(function (i, obj) {
                            $(this).text(newName);
                            $(this).css("background",background_color);
                        });
                    }
                    else
                    {
                        // Create the DOM option that is pre-selected by default
                        var newState = new Option(newName, newStateVal, true, true);
                        // Append it to the select
                        $("#note_tag").append(newState);
                        $("#note_tag option[value='"+newStateVal+"']").attr("data-background",background_color);
                    }

                },
                error: function (data) {}

            });
        }

    });

    $(document).on('click', '.add-note', function (e) {

        $("#note_id").val("");
        $("#addNoteName").val("");
        $("#addNoteDetails").val("");
        $("#note_tag").val("");
        $("#note_client").val("");
        $("#note_client").trigger('change.select2');
        $("#note_supplier").val("");
        $("#note_supplier").trigger('change.select2');
        $("#note_employee").val("");
        $("#note_employee").trigger('change.select2');
        $("#addNoteModalLabel").text("{{__('text.Add new note')}}");
        $('#addNoteModal').modal('toggle');

    });

    $(document).on('click', '.edit-note', function (e) {

        var note_id = $(this).parents(".note").data("id");
        var title = $(this).parents(".note").find(".note-title").text();
        var details = $(this).parents(".note").find(".note-description").text();
        var tag_id = $(this).parents(".note").find(".tag").data("tag_id");
        var client_id = $(this).parents(".note").data("client");
        var supplier_id = $(this).parents(".note").data("supplier");
        var employee_id = $(this).parents(".note").data("employee");

        $("#note_id").val(note_id);
        $("#addNoteName").val(title);
        $("#addNoteDetails").val(details);
        $("#note_tag").val(tag_id);
        $("#note_client").val(client_id);
        $("#note_client").trigger('change.select2');
        $("#note_supplier").val(supplier_id);
        $("#note_supplier").trigger('change.select2');
        $("#note_employee").val(employee_id);
        $("#note_employee").trigger('change.select2');
        $("#addNoteModalLabel").text("{{__('text.Edit note')}}");
        $('#addNoteModal').modal('toggle');

    });

    $(document).on('click', '.delete-note', function (e) {

        var note_id = $(this).parents(".note").data("id");
        var token = $("[name='_token']").val();
        
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

    function filter_notes()
    {
        var note_id = $(".filter-notes .filter-note-item[selected]").data("select");
        var note_title = $(".filter-notes .filter-note-item[selected]").text();
        var search = $(".search-notes").val().toLowerCase();

        $('.note-container .note').each(function() {
            
            var search_flag = 0;
            var filter_flag = 0;
            var data_id = $(this).data("id");

            if($(this).text().toLowerCase().indexOf(""+search+"") != -1){
                search_flag = 1;
            }

            if(note_id == "all-notes" || note_id == data_id)
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
            }

        });
    }

    $(document).on('click', '.filter-note-item', function (e) {

        $(this).attr("selected",true);
        $(".filter-notes .filter-note-item").not(this).attr("selected",false);

        filter_notes();
        $(".filter-toggle").text($(this).text());
        
    });

    // jQuery.expr[':'].contains = function(a, i, m) {
    //     return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    // };

    $(document).on('input', '.search-notes', function (e) {

        filter_notes();
        
    });

    $(document).on('click', '.submit-note', function (e) {

        var note_id = $("#note_id").val();
        var customer_id = $("#note_client").val();
        customer_id = customer_id != undefined ? customer_id : "";
        var supplier_id = $("#note_supplier").val();
        supplier_id = supplier_id != undefined ? supplier_id : "";
        var employee_id = $("#note_employee").val();
        employee_id = employee_id != undefined ? employee_id : "";
        var title = $("#addNoteName").val();
        var details = $("#addNoteDetails").val();
        var tag = $("#note_tag").val();
        var token = $("[name='_token']").val();
        var flag = 0;

        if(!title)
        {
            $("#addNoteName").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#addNoteName").css('border', '');
        }

        if(!details)
        {
            $("#addNoteDetails").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#addNoteDetails").css('border', '');
        }

        if(!tag)
        {
            $("#note_tag").css('border', '1px solid red');
            flag = 1;
        }
        else
        {
            $("#note_tag").css('border', '');
        }

        if(!flag)
        {
            $.ajax({
                type: "POST",
                data: "note_id=" + note_id + "&customer=" + customer_id + "&supplier=" + supplier_id + "&employee=" + employee_id + "&title=" + title + "&details=" + details + "&tag=" + tag + "&_token=" + token,
                url: '{{route("store-new-note")}}',

                success: function (data) {

                    $('#addNoteModal').modal('toggle');

                    var note_container = $(".note-container");

                    if(note_id)
                    {
                        note_container.find('.note[data-id="'+note_id+'"]').find(".note-employee").text(data.employee);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".note-customer-supplier").text(data.cs);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".note-title").text(data.title);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".note-description").text(data.details);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".tag").text(data.tag_title);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".tag").css("background",data.background);
                        note_container.find('.note[data-id="'+note_id+'"]').find(".tag").data("tag_id",data.tag_id);
                        note_container.find('.note[data-id="'+note_id+'"]').data("client",data.customer_id);
                        note_container.find('.note[data-id="'+note_id+'"]').data("supplier",data.supplier_id);
                        note_container.find('.note[data-id="'+note_id+'"]').data("employee",data.employee_id);

                        $(".filter-notes").find('.filter-note-item[data-select="'+data.id+'"]').text(data.title);
                    }
                    else
                    {
                        note_container.prepend('<div data-id='+data.id+' data-client='+data.customer_id+' data-supplier='+data.supplier_id+' data-employee='+data.employee_id+' class="note">\n' +
                        '<div class="note-body">\n' +
                        '   <div class="note-upper">\n' +
                        '       <div class="note-added-on">'+data.modified_created_at+'</div>\n' +
                        '       <div class="note-employee">'+data.employee+'</div>\n' +
                        '   </div>\n' +
                        '   <h5 class="note-title">'+data.title+'</h5>\n' +
                        '   <p class="note-description">'+data.details+'</p>\n' +
                        '</div>\n' +
                        '<div class="note-footer">\n' +
                        '   <div class="note-tools">\n' +
                        '       <span data-tag_id="'+data.tag_id+'" style="color: white;background: '+data.background+'" class="badge tag">'+data.tag_title+'</span>\n' +
                        '   </div>\n' +
                        '   <div class="note-customer-supplier">'+data.cs+'</div>\n' +
                        '   <div class="note-tools">\n' +
                        '       <div class="dropdown">\n' +
                        '           <button class="btn btn-secondary btn-icon btn-minimal btn-sm text-muted" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\n' +
                        '               <svg class="hw-20" xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n' +
                        '                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>\n' +
                        '               </svg>\n' +                                
                        '           </button>\n' +
                        '           <div class="dropdown-menu dropdown-menu-right">\n' +
                        '               <a class="dropdown-item text-success edit-note" href="javascript:void(0)">{{__("text.Edit")}}</a>\n' +
                        '               <a class="dropdown-item text-danger delete-note" href="javascript:void(0)">{{__("text.Delete")}}</a>\n' +
                        '           </div>\n' +
                        '       </div>\n' +
                        '   </div>\n' +
                        '</div></div>');

                        $(".filter-notes").find('.filter-note-item[data-select="all-notes"]').after('<a class="dropdown-item filter-note-item" data-notes-filter="" data-select="'+data.id+'" href="javascript:void(0)">'+data.title+'</a>');
                    }

                    if($(".search-notes").length)
                    {
                        filter_notes();
                    }
                },
                error: function (data) {}

            });
        }

    });

</script>