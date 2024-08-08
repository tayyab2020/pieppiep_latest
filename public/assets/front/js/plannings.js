async function AutoSavePlannings()
{
    var appointments_data = $('.appointment_data').val();
    console.log(appointments_data);
    var token = $('input[name="_token"]').val();
    // var data = "appointment_data=" + appointments_data + "&_token=" + token + "&runtime=1";

    return $.ajax({
        url: config.objects.store_plannings,
        data: {
            "_token": $('[name="_token"]').val(),
            "appointment_data": appointments_data,
            "runtime": 1
        },
        type: 'POST',
        // async: false,
        success: function(data){
        },
        complete: function(){
        }
    });
}

$('.appointment_start').on('dp.change', function(e) {

    var start_date = $(this).val();
    var end_date = $(".appointment_end").val();

    if(!end_date || (Date.parse(end_date) < Date.parse(start_date)))
    {
        $('.appointment_end').val(start_date);
    }

});

$('.appointment_end').on('dp.change', function(e) {

    var start_date = $(".appointment_start").val();

    if(start_date)
    {
        var end_date = $(this).val();
        var datetimeAr = start_date.split(' ');
        var timeAr = datetimeAr[1];
        var st = new Date('00','00',timeAr.split(':')[0],timeAr.split(':')[1]);
    
        var datetimeAr1 = end_date.split(' ');
        var timeAr1 = datetimeAr1[1];
        var et = new Date('00','00',timeAr1.split(':')[0],timeAr1.split(':')[1]);
    
        var timeDifference = et.getTime() - st.getTime();
    
        if(timeDifference < 0)
        {
            $('.appointment_end').css('border','1px solid red');
        }
        else
        {
            $('.appointment_end').css("border","");
        }
    }

});

$(".appointment_type").change(function () {

    var type = $(this).val();

    if(type == 1)
    {
        $('.appointment_customer_box').hide();
        $('.appointment_supplier_box').hide();
        $('.appointment_employee_box').hide();
        $('.appointment_quotation_number_box').show();

        $('.appointment_client').val('');
        $('.appointment_supplier').val('');
        $('.appointment_employee').val('');
        $('.appointment_client').trigger('change.select2');
        $('.appointment_supplier').trigger('change.select2');
        $('.appointment_employee').trigger('change.select2');
    }
    else if(type == 2)
    {
        $('.appointment_quotation_number_box').hide();
        $('.appointment_supplier_box').hide();
        $('.appointment_employee_box').hide();
        $('.appointment_customer_box').show();

        $('.appointment_quotation_number').val('');
        $('.appointment_supplier').val('');
        $('.appointment_employee').val('');
        $('.appointment_quotation_number').trigger('change.select2');
        $('.appointment_supplier').trigger('change.select2');
        $('.appointment_employee').trigger('change.select2');
    }
    else if(type == 3)
    {
        $('.appointment_quotation_number_box').hide();
        $('.appointment_customer_box').hide();
        $('.appointment_employee_box').hide();
        $('.appointment_supplier_box').show();

        $('.appointment_quotation_number').val('');
        $('.appointment_client').val('');
        $('.appointment_employee').val('');
        $('.appointment_quotation_number').trigger('change.select2');
        $('.appointment_client').trigger('change.select2');
        $('.appointment_employee').trigger('change.select2');
    }
    else
    {
        $('.appointment_quotation_number_box').hide();
        $('.appointment_customer_box').hide();
        $('.appointment_supplier_box').hide();
        $('.appointment_employee_box').show();

        $('.appointment_quotation_number').val('');
        $('.appointment_client').val('');
        $('.appointment_supplier').val('');
        $('.appointment_quotation_number').trigger('change.select2');
        $('.appointment_client').trigger('change.select2');
        $('.appointment_supplier').trigger('change.select2');
    }

});

async function create_nonContinuous_event(data,data_array)
{
    var flag = 0;

    if(data["timeAr"][1] <= data["timeAr1"][1])
    {
        for(var i = 0; i <= data["diff"]; i++)
        {
            var newDate = new Date(data["format_start"]);
            newDate.setDate(newDate.getDate() + i);
            var new_start_date = newDate.getFullYear() + '-' + String(newDate.getMonth() + 1).padStart(2, '0') + '-' + String(newDate.getDate()).padStart(2, '0') + " " + data["timeAr"][1];
            var new_end_date = newDate.getFullYear() + '-' + String(newDate.getMonth() + 1).padStart(2, '0') + '-' + String(newDate.getDate()).padStart(2, '0') + " " + data["timeAr1"][1];

            var obj = {};
            obj['group_id'] = null;
            obj['main_id'] = null;
            obj['non_continuous_start'] = i == 0 ? data["format_start"] : null;
            obj['non_continuous_end'] = i == 0 ? data["format_end"] : null;
            obj['quotation_id'] = data["appointment_quotation_id"];
            obj['title'] = data["title"];
            // obj['status'] = data["status"];
            obj['status_id'] = data["status_id"];
            obj['start'] = new_start_date;
            obj['end'] = new_end_date;
            obj['description'] = i == 0 ? data["appointment_desc"] : null;
            obj['tags'] = data["appointment_tags"];
            obj['new'] = 1;
            obj['event_type'] = data["event_type"];
            obj['retailer_client_id'] = data["customer_id"];
            obj['supplier_id'] = data["supplier_id"];
            obj['employee_id'] = data["employee_id"];
            obj['responsible_id'] = data["responsible_id"];
            data_array.push(obj);
            $('.appointment_data').val(JSON.stringify(data_array));

            var id = await AutoSavePlannings();
            id = parseInt(id);
            data_array[data_array.length-1]["id"] = id;

            if(i == 0)
            {
                var main_id = id;
            }
            else
            {
                data_array[data_array.length-1]["main_id"] = main_id;
            }

            data_array[data_array.length-1]["group_id"] = main_id;
            $('.appointment_data').val(JSON.stringify(data_array));

            // calendar.addEvent({
            //     id: id,
            //     group_id: main_id,
            //     main_id: i != 0 ? main_id : null,
            //     non_continuous_start: i == 0 ? data["format_start"] : null,
            //     non_continuous_end: i == 0 ? data["format_end"] : null,
            //     quotation_id: data["appointment_quotation_id"],
            //     title: data["event_title"],
            //     org_title: data["title"],
            //     status: data["status"],
            //     status_id: data["status_id"],
            //     start: new_start_date,
            //     end: new_end_date + ':01',
            //     description: data["appointment_desc"],
            //     tags: data["appointment_tags"],
            //     event_type: data["event_type"],
            //     retailer_client_id: data["customer_id"],
            //     supplier_id: data["supplier_id"],
            //     employee_id: data["employee_id"],
            //     responsible_id: data["responsible_id"],
            //     color: data["color"] ? data["color"] : "",
            //     fontColor: data["font_color"] ? data["font_color"] : "",
            //     client_quotation_fname: data["client_quotation_fname"],
            //     client_quotation_lname: data["client_quotation_lname"],
            //     client_fname: data["client_fname"],
            //     client_lname: data["client_lname"],
            //     company_name: data["company_name"],
            //     employee_fname: data["employee_fname"],
            //     employee_lname: data["employee_lname"],
            // });
        }

        await eventChanged();
    }
    else
    {
        flag = 1;
        alert(config.objects.alert1);
    }

    if(flag)
    {
        return 0;
    }
    else
    {
        return 1;
    }
}

function updateEvent(event,data,non_continuous = 0)
{
    var events = [];
    events.push(event);

    if(non_continuous)
    {
        var group_id = event._def.extendedProps.group_id;
        var main_id = event._def.extendedProps.main_id;
        var all_events = calendar.getEvents();
                    
        $.each(all_events, function (i, obj) {
            if((obj._def.extendedProps.group_id == group_id) && (obj.id != data["id"]))
            {
                events.push(obj);
            }
        });
    }

    $.each(events, function (i, event) {
        event.setExtendedProp('quotation_id', data["appointment_quotation_id"]);
        event.setExtendedProp('event_type', data["event_type"]);
        event.setExtendedProp('retailer_client_id', data["customer_id"]);
        event.setExtendedProp('supplier_id', data["supplier_id"]);
        event.setExtendedProp('employee_id', data["employee_id"]);
        event.setExtendedProp('responsible_id', data["responsible_id"]);
        event.setProp('title', data["event_title"]);
        event.setProp('color', data["color"] ? data["color"] : "");
        event.setProp('fontColor', data["font_color"] ? data["font_color"] : "");
        event.setExtendedProp('org_title', data["title"]);
        // event.setExtendedProp('status', data["status"]);
        event.setExtendedProp('status_id', data["status_id"]);
        event.setExtendedProp('description', data["appointment_desc"]);
        event.setExtendedProp('tags', data["appointment_tags"]);
        event.setExtendedProp('client_quotation_fname', data["client_quotation_fname"]);
        event.setExtendedProp('client_quotation_lname', data["client_quotation_lname"]);
        event.setExtendedProp('client_fname', data["client_fname"]);
        event.setExtendedProp('client_lname', data["client_lname"]);
        event.setExtendedProp('company_name', data["company_name"]);
        event.setExtendedProp('employee_fname', data["employee_fname"]);
        event.setExtendedProp('employee_lname', data["employee_lname"]);

        if(i == 0)
        {
            event.setDates(data["format_start"],data["format_end"] + ':01'); 
        }

        if(!event._def.extendedProps.main_id)
        {
            event.setExtendedProp('non_continuous_start', data["format_start"]);
            event.setExtendedProp('non_continuous_end', data["format_end"]);
        }

        $('.fc-daygrid-event').each(function (i, obj) {
            
            // if(data["appointment_quotation_id"])
            // {
            //     if(data["client_quotation_fname"] || data["client_quotation_lname"])
            //     {
            //         var extended_title = data["client_quotation_fname"] + ' ' + data["client_quotation_lname"] + (data["status"] ? "<br/>" + data["status"] : "");
            //     }
            //     else
            //     {
            //         var extended_title = (data["status"] ? data["status"] : "");
            //     }
            // }
            // else if(data["customer_id"])
            // {
            //     var extended_title = data["client_fname"] + ' ' + data["client_lname"] + (data["status"] ? "<br/>" + data["status"] : "");
            // }
            // else if(data["supplier_id"])
            // {
            //     var extended_title = data["company_name"] + (data["status"] ? "<br/>" + data["status"] : "");
            // }
            // else
            // {
            //     var extended_title = data["employee_fname"] + ' ' + data["employee_lname"] + (data["status"] ? "<br/>" + data["status"] : "");
            // }
            
            // $(this).find(`.extended_title[data-id='${event.id}']`).html(extended_title);
        });
    });
}

$(".submit_appointmentForm").click(async function () {

    var validation = $('#addAppointmentModal').find('.modal-body').find('.validation_required');

    var flag = 0;
    var timeRange_flag = 1;

    var title = $('.appointment_title').val();
    // var status = $('.appointment_status').val();
    // status = status ? status : null;
    var status_id = $(".appointment_status option:selected").data('id');
    status_id = status_id == undefined ? null : status_id;
    var color = $(".appointment_status option:selected").data('bgColor');
    color = color == undefined ? null : color;
    var event_type = $('.appointment_type').val();
    var appointment_quotation_id = $('.appointment_quotation_number').val();
    var customer_id = $('.appointment_client').val();
    var supplier_id = $('.appointment_supplier').val();
    var employee_id = $('.appointment_employee').val();
    var responsible_id = $('.appointment_responsible').val();
    var fontColor = $(".appointment_responsible option:selected").data('color');
    fontColor = fontColor == undefined ? null : fontColor;
    var continuous_event = $('#continuous_event').is(':checked');

    if (!title) {
        flag = 1;
        $('.appointment_title_box .select2-container--default .select2-selection--single').css('border-color', 'red');
    }
    else {
        $('.appointment_title_box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
    }

    if(event_type == 1)
    {
        if(!appointment_quotation_id)
        {
            flag = 1;
            $('.appointment_quotation_number_box .select2-container--default .select2-selection--single').css('border-color', 'red');
        }
        else
        {
            if(appointment_quotation_id == 0)
            {
                appointment_quotation_id = $('input[name="quotation_id"]').val();
            }
            
            $('.appointment_quotation_number_box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
        }

        var client_quotation_fname = $(".appointment_quotation_number option:selected").data('fname') != undefined ? $(".appointment_quotation_number option:selected").data('fname') : '';
        var client_quotation_lname = $(".appointment_quotation_number option:selected").data('lname') != undefined ? $(".appointment_quotation_number option:selected").data('lname') : '';
        var client_fname = '';
        var client_lname = '';
        var company_name = '';
        var employee_fname = '';
        var employee_lname = '';
    }
    else if(event_type == 2)
    {
        if(!customer_id)
        {
            flag = 1;
            $('.appointment_customer_box .select2-container--default .select2-selection--single').css('border-color', 'red');
        }
        else
        {
            $('.appointment_customer_box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
        }

        var client_quotation_fname = '';
        var client_quotation_lname = '';
        var client_fname = $(".appointment_client option:selected").data('fname') != undefined ? $(".appointment_client option:selected").data('fname') : '';
        var client_lname = $(".appointment_client option:selected").data('lname') != undefined ? $(".appointment_client option:selected").data('lname') : '';
        var company_name = '';
        var employee_fname = '';
        var employee_lname = '';
    }
    else if(event_type == 3)
    {
        if(!supplier_id)
        {
            flag = 1;
            $('.appointment_supplier_box .select2-container--default .select2-selection--single').css('border-color', 'red');
        }
        else
        {
            $('.appointment_supplier_box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
        }

        var client_quotation_fname = '';
        var client_quotation_lname = '';
        var client_fname = '';
        var client_lname = '';
        var company_name = $('.appointment_supplier option:selected').text();
        var employee_fname = '';
        var employee_lname = '';
    }
    else
    {
        if(!employee_id)
        {
            flag = 1;
            $('.appointment_employee_box .select2-container--default .select2-selection--single').css('border-color', 'red');
        }
        else
        {
            $('.appointment_employee_box .select2-container--default .select2-selection--single').css('border-color', '#cacaca');
        }

        var client_quotation_fname = '';
        var client_quotation_lname = '';
        var client_fname = '';
        var client_lname = '';
        var company_name = '';
        var employee_fname = $(".appointment_employee option:selected").data('fname') != undefined ? $(".appointment_employee option:selected").data('fname') : '';
        var employee_lname = $(".appointment_employee option:selected").data('lname') != undefined ? $(".appointment_employee option:selected").data('lname') : '';
    }

    $(validation).each(function(){

        if(!$(this).val())
        {
            $(this).css('border','1px solid red');
            flag = 1;
        }
        else
        {
            $(this).css('border','');
        }

    });

    if(!flag)
    {
        var id = $('#event_id').val();
        var appointment_start = $('.appointment_start').val();
        var appointment_end = $('.appointment_end').val();
        var dateAr = /(\d+)\-(\d+)\-(\d+)/.exec(appointment_start);
        var timeAr = appointment_start.split(' ');
        var format_start = dateAr[3] + '-' + dateAr[2] + '-' + dateAr[1] + ' ' + timeAr[1];
        var start = moment(format_start, "YYYY-MM-DD");

        var dateAr = /(\d+)\-(\d+)\-(\d+)/.exec(appointment_end);
        var timeAr1 = appointment_end.split(' ');
        var format_end = dateAr[3] + '-' + dateAr[2] + '-' + dateAr[1] + ' ' + timeAr1[1];
        var end = moment(format_end, "YYYY-MM-DD");

        var diff = moment.duration(end.diff(start)).asDays();

        var appointment_desc = $('.appointment_description').val();
        var appointment_tags = $('.appointment_tags').val();

        if(title == "Delivery Date")
        {
            var event_title = config.objects.delivery_date_text;
        }
        else if(title == "Installation Date")
        {
            var event_title = config.objects.installation_date_text;
        }
        else
        {
            var event_title = title;
        }

        if (format_start <= format_end){

            $('#cover').css('display', 'flex');
            
            var data = {};
            data["diff"] = diff;
            data["format_start"] = format_start;
            data["format_end"] = format_end;
            data["timeAr"] = timeAr;
            data["timeAr1"] = timeAr1;
            data["appointment_quotation_id"] = appointment_quotation_id;
            data["event_title"] = event_title;
            data["title"] = title;
            // data["status"] = status;
            data["status_id"] = status_id;
            data["appointment_desc"] = appointment_desc;
            data["appointment_tags"] = appointment_tags;
            data["event_type"] = event_type;
            data["customer_id"] = customer_id;
            data["supplier_id"] = supplier_id;
            data["employee_id"] = employee_id;
            data["responsible_id"] = responsible_id;
            data["color"] = color;
            data["font_color"] = fontColor;
            data["client_quotation_fname"] = client_quotation_fname;
            data["client_quotation_lname"] = client_quotation_lname;
            data["client_fname"] = client_fname;
            data["client_lname"] = client_lname;
            data["company_name"] = company_name;
            data["employee_fname"] = employee_fname;
            data["employee_lname"] = employee_lname;

            var appointments = $('.appointment_data').val();

            if(appointments)
            {
                var data_array = JSON.parse(appointments);
            }
            else
            {
                var data_array = [];
            }

            if(id)
            {
                data["id"] = id;
                // var event = calendar.getEventById(id);
                await eventChanged(null,1,0,data);
            }
            else
            {
                if(!continuous_event)
                {
                    timeRange_flag = await create_nonContinuous_event(data,data_array);
                }
                else
                {
                    var obj = {};
                    obj['id'] = null;
                    obj['group_id'] = null;
                    obj['main_id'] = null;
                    obj['quotation_id'] = appointment_quotation_id;
                    obj['title'] = title;
                    // obj['status'] = status;
                    obj['status_id'] = status_id;
                    obj['non_continuous_start'] = null;
                    obj['non_continuous_end'] = null;
                    obj['start'] = format_start;
                    obj['end'] = format_end;
                    obj['description'] = appointment_desc;
                    obj['tags'] = appointment_tags;
                    obj['new'] = 1;
                    obj['event_type'] = event_type;
                    obj['retailer_client_id'] = customer_id;
                    obj['supplier_id'] = supplier_id;
                    obj['employee_id'] = employee_id;
                    obj['responsible_id'] = responsible_id;
                    data_array.push(obj);

                    $('.appointment_data').val(JSON.stringify(data_array));

                    var id = await AutoSavePlannings();
                    id = parseInt(id);

                    data_array[data_array.length-1]["id"] = id;
                    $('.appointment_data').val(JSON.stringify(data_array));

                    // calendar.addEvent({
                    //     id: id,
                    //     group_id: null,
                    //     main_id: null,
                    //     non_continuous_start: null,
                    //     non_continuous_end: null,
                    //     quotation_id: appointment_quotation_id,
                    //     title: event_title,
                    //     org_title: title,
                    //     status: status,
                    //     status_id: status_id,
                    //     start: format_start,
                    //     end: format_end + ':01',
                    //     description: appointment_desc,
                    //     tags: appointment_tags,
                    //     event_type: event_type,
                    //     retailer_client_id: customer_id,
                    //     supplier_id: supplier_id,
                    //     employee_id: employee_id,
                    //     responsible_id: responsible_id,
                    //     color: color ? color : "",
                    //     fontColor: fontColor ? fontColor : "",
                    //     client_quotation_fname: client_quotation_fname,
                    //     client_quotation_lname: client_quotation_lname,
                    //     client_fname: client_fname,
                    //     client_lname: client_lname,
                    //     company_name: company_name,
                    //     employee_fname: employee_fname,
                    //     employee_lname: employee_lname,
                    // });

                    await eventChanged();
                }
            }

            if(timeRange_flag)
            {
                $('.appointment_end').css('border','');
                // $('#cover').css('display', 'none');
                $('#addAppointmentModal').modal('toggle');
            
                if($("#widget_type").val() == 2)
                {
                    $('#myModal3').modal('toggle');
                }

                $('#event_id').val('');
                $('.appointment_quotation_number').val('');
                $('.appointment_title').val('');
                $('.appointment_status').val('');
                $('.appointment_start').val('');
                $('.appointment_start').data("DateTimePicker").clear();
                $('.appointment_end').val('');
                $('.appointment_end').data("DateTimePicker").clear();
                $('.appointment_description').val('');
                $('.appointment_client').val('');
                $('.appointment_supplier').val('');
                $('.appointment_employee').val('');
                $('.appointment_responsible').val('');
                $('#continuous_event').prop('checked', false);
                $('.appointment_tags').tagsinput('removeAll');
                $('.appointment_title').trigger('change.select2');
                $('.appointment_status').trigger('change.select2');
                $('.appointment_quotation_number').trigger('change.select2');
                $('.appointment_client').trigger('change.select2');
                $('.appointment_supplier').trigger('change.select2');
                $('.appointment_employee').trigger('change.select2');
                $('.appointment_responsible').trigger('change.select2');

                calendar.refetchEvents();
            }
            else
            {
                $('.appointment_end').css('border','1px solid red');
                $('#cover').css('display', 'none');
            }
        }
        else
        {
            $('.appointment_end').css('border','1px solid red');
        }
    }

    return false;

});

$(".appointment_tags").tagsinput('items');

function addAppointment(start = null,end = null)
{
    var logged_user = $("#logged_user").val();
    $('#event_id').val('');
    $('#continuous_event').prop('checked', false);
    // $(".continuous_toggle").show();
    $('.appointment_quotation_number').val('');
    $('.appointment_title').val('');
    $('.appointment_status').val('');

    if(start)
    {
        $('.appointment_start').val(start);
    }
    else
    {
        $('.appointment_start').val('');
        $('.appointment_start').data("DateTimePicker").clear();
    }

    if(end)
    {
        $('.appointment_end').val(end);
    }
    else
    {
        $('.appointment_end').val('');
        $('.appointment_end').data("DateTimePicker").clear();
    }

    $('.appointment_description').val('');
    $('.appointment_client').val('');
    $('.appointment_supplier').val('');
    $('.appointment_employee').val('');
    $('.appointment_responsible').val(logged_user);
    $('.appointment_tags').tagsinput('removeAll');
    $('.appointment_title').trigger('change.select2');
    $('.appointment_status').trigger('change.select2');
    $('.appointment_quotation_number').trigger('change.select2');
    $('.appointment_type').trigger('change.select2');
    $('.appointment_client').trigger('change.select2');
    $('.appointment_supplier').trigger('change.select2');
    $('.appointment_employee').trigger('change.select2');
    $('.appointment_responsible').trigger('change.select2');

    if(($("#widget_type").val() == 2))
    {
        $('#myModal3').modal('toggle');
    }
    
    $('#addAppointmentModal').modal('toggle');
}

$(".add-appointment").click(function () {

    addAppointment();

});

function edit_appointment(id)
{
    var event = calendar.getEventById(id);
    var current_quotation_id = $('input[name="quotation_id"]').val();
    var quotation_id = event._def.extendedProps.quotation_id;
    var group_id = event._def.extendedProps.group_id;
    var main_id = event._def.extendedProps.main_id;
    var title = event._def.title;
    // var org_title = event._def.extendedProps.org_title;
    // var status = event._def.extendedProps.status;
    var status_id = event._def.extendedProps.status_id;
    var description = event._def.extendedProps.description;
    var tags = event._def.extendedProps.tags;
    var event_type = event._def.extendedProps.event_type;
    var retailer_client_id = event._def.extendedProps.retailer_client_id;
    var supplier_id = event._def.extendedProps.supplier_id;
    var employee_id = event._def.extendedProps.employee_id;
    var responsible_id = event._def.extendedProps.responsible_id;
    var start = moment(event.start).format('DD-MM-YYYY HH:mm');
    var end = event.end ? moment(event.end).format('DD-MM-YYYY HH:mm') : start;

    if($("#widget_type").val() == 2)
    {
        if(quotation_id == current_quotation_id)
		{
			quotation_id = 0;
		}
		else
		{
			quotation_id = quotation_id ? quotation_id : (event_type == 1 ? 0 : '');
		}
    }

    if(group_id)
    {
        $('#continuous_event').prop('checked', false);
    }
    else
    {
        $('#continuous_event').prop('checked', true);
    }

    $(".continuous_toggle").hide();

    $('#event_id').val(id);
    $('.appointment_quotation_number').val(quotation_id);
    // $('.appointment_title').val(title);
    // $('.appointment_status').val(status);
    $(".appointment_status option[data-id='" + status_id + "']").prop("selected", true);
    $(".appointment_title option[data-text='" + title + "']").prop("selected", true);
    $('.appointment_start').val(start);
    $('.appointment_end').val(end);
    $('.appointment_description').val(description);
    $('.appointment_type').val(event_type);
    $('.appointment_client').val(retailer_client_id);
    $('.appointment_supplier').val(supplier_id);
    $('.appointment_employee').val(employee_id);
    $('.appointment_responsible').val(responsible_id);
    $('.appointment_tags').tagsinput('removeAll');
    $('.appointment_tags').tagsinput('add',tags);

    $('.appointment_title').trigger('change.select2');
    $('.appointment_status').trigger('change.select2');
    $('.appointment_type').trigger('change.select2');
    $('.appointment_quotation_number').trigger('change.select2');
    $('.appointment_client').trigger('change.select2');
    $('.appointment_supplier').trigger('change.select2');
    $('.appointment_employee').trigger('change.select2');
    $('.appointment_responsible').trigger('change.select2');
    $('.appointment_type').trigger('change');

    if($("#widget_type").val() == 2)
    {
        $('#myModal3').modal('toggle');
    }
    
    $('#addAppointmentModal').modal('toggle');
}

async function remove_appointment(id,manual = 0)
{
    var event = calendar.getEventById(id);
    var flag = 1;

    if(manual)
    {
        if(event._def.extendedProps.group_id && !event._def.extendedProps.main_id)
        {
            await Swal.fire({
                title: config.objects.are_you_sure,
                text: config.objects.related_events_confirmation,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: config.objects.yes_text,
                cancelButtonText: config.objects.cancel_text,
            }).then((result) => {
                if (result.value == undefined) {
                    flag = 0;
                }
            });
        }
        else
        {
            await Swal.fire({
                title: config.objects.are_you_sure,
                text: '',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: config.objects.yes_text,
                cancelButtonText: config.objects.cancel_text,
            }).then((result) => {
                if (result.value == undefined) {
                    flag = 0;
                }
            });
        }
    }

    if(flag)
    {
        if(event._def.extendedProps.group_id && !event._def.extendedProps.main_id)
        {
            var all_events = calendar.getEvents();
                    
            $.each(all_events, function (i, obj) {
                if(obj._def.extendedProps.main_id == id)
                {
                    remove_appointment(obj.id);
                }
            });
        }
    
        event.remove();

        var appointments = $('.appointment_data').val();

        if(appointments)
        {
            appointments = JSON.parse(appointments);
        }
        else
        {
            appointments = [];
        }

        for(var i = 0; i < appointments.length; i++) {
            if(appointments[i].id == id) {
                appointments.splice(i, 1);
                break;
            }
        }

        if(jQuery.isEmptyObject(appointments))
        {
            $('.appointment_data').val('');
        }
        else
        {
            $('.appointment_data').val(JSON.stringify(appointments));
        }

        var token = $('input[name="_token"]').val();
        var data = "id=" + id + "&_token=" + token;

        $.ajax({
            url: config.objects.remove_plannings,
            type: 'POST',
            data: data,
            success: function(data){
            },
            complete: function(){
            }
        });
    }
}

function toggle_checklist(id)
{
    $.ajax({
        url: config.objects.view_details,
        type: 'GET',
        data: "id=" + id,
        success: function(data){
            $(".first-row1").text(data[1]);
            $("#checklist_table tbody tr").remove();
            $("#checklist_table tbody").append(data[2]);
        },
        complete: function(){
            $('.agenda-pills a[href="#2a"]').tab('show');
        }
    });
}

function checklist(id)
{
    var event = calendar.getEventById(id);
    var quotation_id = event._def.extendedProps.quotation_id;
    var retailer_client_id = event._def.extendedProps.retailer_client_id;

    if(quotation_id)
    {
        toggle_checklist(quotation_id);
    }
    else
    {
        $.ajax({
            url: config.objects.fetch_customer_quotations,
            type: 'GET',
            data: "id=" + retailer_client_id,
            success: function(data){

                $(".checklist_quotations").find('option').not(':first').remove();

                if(data)
                {
                    $.each(data, function (index, value) {
                        var id = value.id;
                        var quotation_number = value.quotation_invoice_number;
                        var newState = new Option(quotation_number,id, true, true);
                        // Append it to the select
                        $(".checklist_quotations").append(newState);
                    });

                    $(".checklist_quotations").val("");
                    $(".checklist_quotations").trigger('change.select2');
                }

            },
            complete: function(){
                $('#checklist_quotationModal').modal('toggle');
            }
        });
    }
}

$(".submit-checklist-quotation").click(function () {

    var quotation = $(".checklist_quotations").val();

    if(!quotation)
    {
        $("#checklist_quotationModal").find(".select2-selection").css("border-color","red");
    }
    else
    {
        $("#checklist_quotationModal").find(".select2-selection").css("border-color","");
        $('#checklist_quotationModal').modal('toggle');
        toggle_checklist(quotation);
    }

});

function updateData(arg,type,dt)
{
    var all_events = calendar.getEvents();

    if(dt)
    {
        var event = calendar.getEventById(dt["id"]);
    }
    else
    {
        if(type)
        {
            var event = arg.event;
        }
        else
        {
            var event = arg;
        }
    }

    var events = [];
    events.push(event);

    if(event._def.extendedProps.group_id)
    {
        var all_related_childs = [];
        var last_child_id = "";

        $.each(all_events, function (i, obj) {
            if((obj._def.extendedProps.group_id == event._def.extendedProps.group_id))
            {
                last_child_id = obj.id;

                if(obj.id != event.id)
                {
                    events.push(obj);
                }

                if(obj._def.extendedProps.main_id)
                {
                    all_related_childs.push(obj);
                }
            }
        });
    }

    $.each(events, function (i, arg) {

        if(arg._def != undefined)
        {
            var data = $('.appointment_data').val();
            var appointments = data ? JSON.parse(data) : '';
            var id = arg.id;
            var group_id = arg._def.extendedProps.group_id;
            var main_id = arg._def.extendedProps.main_id;
            var non_continuous_start = arg._def.extendedProps.non_continuous_start;
            var non_continuous_end = arg._def.extendedProps.non_continuous_end;
            var quotation_id = dt ? dt["appointment_quotation_id"] : arg._def.extendedProps.quotation_id;
            // var title = dt ? dt["event_title"] : arg._def.title;
            var title = dt ? dt["title"] : arg._def.extendedProps.org_title;
            // var status = dt ? dt["status"] : arg._def.extendedProps.status;
            var status_id = dt ? dt["status_id"] : arg._def.extendedProps.status_id;
            var tags = dt ? dt["appointment_tags"] : arg._def.extendedProps.tags;
            var retailer_client_id = dt ? dt["customer_id"] : arg._def.extendedProps.retailer_client_id;
            var supplier_id = dt ? dt["supplier_id"] : arg._def.extendedProps.supplier_id;
            var employee_id = dt ? dt["employee_id"] : arg._def.extendedProps.employee_id;
            var responsible_id = dt ? dt["responsible_id"] : arg._def.extendedProps.responsible_id;
            var event_type = dt ? dt["event_type"] : arg._def.extendedProps.event_type;
            var description = arg._def.extendedProps.description;
            
            if(dt && i == 0)
            {
                var start = dt["format_start"];
                var end = dt["format_end"];
                description = dt["appointment_desc"];
            }
            else
            {
                var start = new Date(arg._instance.range.start.toLocaleString('en-US', { timeZone: 'UTC' }));
                var end = new Date(arg._instance.range.end.toLocaleString('en-US', { timeZone: 'UTC' }));
        
                var start_date = new Date(start);
                var curr_date = (start_date.getDate()<10?'0':'') + start_date.getDate();
                var curr_month = start_date.getMonth() + 1;
                curr_month = (curr_month<10?'0':'') + curr_month;
                var curr_year = start_date.getFullYear();
                var hour = (start_date.getHours()<10?'0':'') + start_date.getHours();
                var minute = (start_date.getMinutes()<10?'0':'') + start_date.getMinutes();
                start = curr_year+"-"+curr_month+"-"+curr_date+" "+hour+":"+minute;

                var end_date = new Date(end);
                var curr_date = (end_date.getDate()<10?'0':'') + end_date.getDate();
                var curr_month = end_date.getMonth() + 1;
                curr_month = (curr_month<10?'0':'') + curr_month;
                var curr_year = end_date.getFullYear();
                var hour = (end_date.getHours()<10?'0':'') + end_date.getHours();
                var minute = (end_date.getMinutes()<10?'0':'') + end_date.getMinutes();
                end = curr_year+"-"+curr_month+"-"+curr_date+" "+hour+":"+minute;
            }

            if(group_id && !main_id)
            {
                non_continuous_start = start;

                if(dt)
                {
                    var parent = appointments.find(x => x.id == last_child_id);
                    var non_continuous_end = parent.end;
                }
                else
                {
                    var last_related_event_end = all_related_childs.length > 0 ? all_related_childs[all_related_childs.length - 1]._instance.range.end.toLocaleString('en-US', { timeZone: 'UTC' }) : end;
                    non_continuous_end = new Date(last_related_event_end);
                    var curr_date = (non_continuous_end.getDate()<10?'0':'') + non_continuous_end.getDate();
                    var curr_month = non_continuous_end.getMonth() + 1;
                    curr_month = (curr_month<10?'0':'') + curr_month;
                    var curr_year = non_continuous_end.getFullYear();
                    var hour = (non_continuous_end.getHours()<10?'0':'') + non_continuous_end.getHours();
                    var minute = (non_continuous_end.getMinutes()<10?'0':'') + non_continuous_end.getMinutes();
                    non_continuous_end = curr_year+"-"+curr_month+"-"+curr_date+" "+hour+":"+minute;
                }
            }

            for(var i = 0; i < appointments.length; i++) {

                if(appointments[i].id == id) {

                    appointments[i]['group_id'] = group_id;
                    appointments[i]['main_id'] = main_id;
                    appointments[i]['title'] = title;
                    // appointments[i]['status'] = status;
                    appointments[i]['status_id'] = status_id;
                    appointments[i]['non_continuous_start'] = non_continuous_start;
                    appointments[i]['non_continuous_end'] = non_continuous_end;
                    appointments[i]['start'] = start;
                    appointments[i]['end'] = end;
                    appointments[i]['description'] = description;
                    appointments[i]['tags'] = tags;
                    appointments[i]['quotation_id'] = quotation_id;
                    appointments[i]['event_type'] = event_type;
                    appointments[i]['retailer_client_id'] = retailer_client_id;
                    appointments[i]['supplier_id'] = supplier_id;
                    appointments[i]['employee_id'] = employee_id;
                    appointments[i]['responsible_id'] = responsible_id;

                    $('.appointment_data').val(JSON.stringify(appointments));
                    break;
                }

            }
        }

    });
}

async function eventChanged(arg = null,save = 1,type = 0,data = null)
{
    if(arg != null || data != null)
    {
        updateData(arg,type,data);
    }

    if(save)
    {
        await AutoSavePlannings();
    }
}

var calendar = '';

document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
        allDayText: config.objects.all_day_text,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        contentHeight:"auto",
        firstDay: 1,
        views: {
            dayGridMonth: {
                dayMaxEvents: false,
            },
        },
        buttonText: {
            today: config.objects.today_text,
            day: config.objects.day_text,
            week: config.objects.week_text,
            month: config.objects.month_text
        },
        initialDate: new Date(),
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        select: function(arg) {
            var start = moment(arg.start).format('DD-MM-YYYY HH:mm');
            var end = moment(arg.end).format('DD-MM-YYYY HH:mm');
            addAppointment(start,end);
            calendar.unselect()
        },
        eventChange: async function(arg) {

            // await eventChanged(arg);

        },
        eventContent: function (arg) {
            
        },
        eventDidMount: function (arg)
        {
            if(!jQuery.isEmptyObject(arg.event._def.extendedProps))
            {
                var actualAppointment = $(arg.el);
                var event = arg.event;
                var id = event._def.publicId;
                var title = event._def.title;
                // var org_title = event._def.extendedProps.org_title;
                var status = event._def.extendedProps.status;

                if(title == "Delivery Date")
                {
                    title = config.objects.delivery_date_text;
                }
                else if(title == "Installation Date")
                {
                    title = config.objects.installation_date_text;
                }

                event.setProp('title', title);

                // if(jQuery.inArray("fc-daygrid-dot-event", actualAppointment[0]["classList"]) !== -1)
                // {
                //     var dot = actualAppointment.find(".fc-daygrid-event-dot");
                //     var time = actualAppointment.find(".fc-event-time");
                //     var row = "<div class='fc-row'>"+dot[0]["outerHTML"]+time[0]["outerHTML"]+"</div>";
                //     actualAppointment.find(".fc-daygrid-event-dot").hide();
                //     actualAppointment.find(".fc-event-time").hide();
                //     actualAppointment.prepend(row);
                // }

                // if(event._def.extendedProps.quotation_id || event._def.extendedProps.retailer_client_id)
                // {
                //     if(event._def.extendedProps.client_quotation_fname || event._def.extendedProps.client_quotation_lname)
                //     {
                //         actualAppointment.find('.fc-event-title').append('<br/><span class="extended_title" data-id="'+id+'" style="font-size: 12px;">'+ event._def.extendedProps.client_quotation_fname + ' ' + event._def.extendedProps.client_quotation_lname + (status ? '<br/>' + status : '') +'</span>');
                //     }
                //     else
                //     {
                //         actualAppointment.find('.fc-event-title').append('<br/><span class="extended_title" data-id="'+id+'" style="font-size: 12px;">'+ (status ? status : '') +'</span>');
                //     }
                // }
                // else if(event._def.extendedProps.retailer_client_id)
                // {
                //     actualAppointment.find('.fc-event-title').append('<br/><span class="extended_title" data-id="'+id+'" style="font-size: 12px;">'+ event._def.extendedProps.client_fname + ' ' + event._def.extendedProps.client_lname + (status ? '<br/>' + status : '') +'</span>');
                // }
                // else if(event._def.extendedProps.supplier_id)
                // {
                //     actualAppointment.find('.fc-event-title').append('<br/><span class="extended_title" data-id="'+id+'" style="font-size: 12px;">'+ event._def.extendedProps.company_name + (status ? '<br/>' + status : '') +'</span>');
                // }
                // else
                // {
                //     actualAppointment.find('.fc-event-title').append('<br/><span class="extended_title" data-id="'+id+'" style="font-size: 12px;">'+ event._def.extendedProps.employee_fname + ' ' + event._def.extendedProps.employee_lname + (status ? '<br/>' + status : '') +'</span>');
                // }

                if(event._def.extendedProps.quotation_id || event._def.extendedProps.retailer_client_id)
                {
                    var checklist_btn = '<button type="button" class="btn btn-default checklist-btn" title="Checklist"><i class="fa fa-list"></i></button>';
                }
                else
                {
                    var checklist_btn = '<button type="button" class="btn btn-default checklist-btn hide" title="Checklist"><i class="fa fa-list"></i></button>';
                }

                var buttonsHtml = '<div class="fc-buttons">' + '<button type="button" class="btn btn-default edit-event" title="Edit"><i class="fa fa-pencil"></i></button>' + '<button type="button" class="btn btn-default remove-event" title="Remove"><i class="fa fa-trash"></i></button>' + checklist_btn + '</div>';

                actualAppointment.append(buttonsHtml);

                actualAppointment.find(".edit-event").on('click', function () {
                    edit_appointment(event.id);
                });

                actualAppointment.find(".remove-event").on('click', function () {
                    remove_appointment(event.id,1);
                });

                actualAppointment.find(".checklist-btn").on('click', function () {
                    checklist(event.id);
                });
            }
        },
        eventDrop: async function(arg) {
            eventChanged(arg,1,1);
        },
        eventResize: async function(arg) {
            eventChanged(arg,1,1);
        },
        eventClick: function(arg) {

        },
        eventTimeFormat: { // like '14:30:00'
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        displayEventEnd: true,
        editable: true,
        dayMaxEvents: true, // allow "more" link when too many events
        // events: {!! $plannings !!},
        events: function (fetchInfo, successCallback, failureCallback) {
            var filter_responsible = $(".filter_responsible").val();
            $.ajax({
                url: config.objects.get_plannings,
                type: 'GET',
                data: "filter_responsible=" + filter_responsible,
                // async: false,
                success: function(data){
                    // var all_events = calendar.getEvents();
                    // $.each(all_events, function (i, obj) {
                    //     obj.remove();
                    // });
                    $(".appointment_data").val(JSON.stringify(data));
                    successCallback(data,calendar.render());
                },
                error: function(){
                }
            });
        },
        loading: function(bool) {
            if(bool)
            {
                calendar_loading = true;
                $('#cover').css('display', 'flex');
            }
            else
            {
                calendar_loading = false;
                $('#cover').css('display', 'none');
            }
        },
    });

    calendar.setOption('locale', 'nl');
    calendar.render();

});

$(".filter_responsible").change(function () {
    calendar.refetchEvents();
});

$(".appointment_title").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_event_title_text,
    allowClear: true,
    dropdownParent: $('.appointment_title_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_status").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_event_status_text,
    allowClear: true,
    dropdownParent: $('.appointment_status_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_type").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_event_type_text,
    allowClear: false,
    dropdownParent: $('.appointment_type_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_quotation_number").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_quotation_text,
    allowClear: false,
    dropdownParent: $('.appointment_quotation_number_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_client").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_customer_text,
    allowClear: false,
    dropdownParent: $('.appointment_customer_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_supplier").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_supplier_text,
    allowClear: false,
    dropdownParent: $('.appointment_supplier_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_employee").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_employee_text,
    allowClear: false,
    dropdownParent: $('.appointment_employee_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".appointment_responsible").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_responsible_text,
    allowClear: true,
    dropdownParent: $('.responsible_box'),
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$(".filter_responsible").select2({
    width: '100%',
    height: '200px',
    placeholder: config.objects.select_responsible_text,
    allowClear: true,
    "language": {
        "noResults": function () {
            return config.objects.no_results_text;
        }
    },
});

$('.appointment_start').datetimepicker({
    format: 'DD-MM-YYYY HH:mm',
    defaultDate: '',
    ignoreReadonly: true,
    sideBySide: true,
    locale:'du'
});

$('.appointment_end').datetimepicker({
    format: 'DD-MM-YYYY HH:mm',
    defaultDate: '',
    ignoreReadonly: true,
    sideBySide: true,
    locale:'du'
});