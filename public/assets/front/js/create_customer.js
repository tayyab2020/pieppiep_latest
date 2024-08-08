$(document).on('click', ".add-customer", function (e) {

    $('#c_modal_customer_id').val("");
    $('#c_modal_name').val("");
    $('#c_modal_family_name').val("");
    $('#c_modal_business_name').val("");
    $('#c_modal_address').val("");
    $('#c_modal_street_name').val("");
    $('#c_modal_street_number').val("");
    $('#c_modal_check_address').val(0);
    $('#c_modal_postcode').val("");
    $('#c_modal_city').val("");
    $('#c_modal_phone').val("");
    $('#c_modal_email').val("");
    // $("#addAppointmentModal").modal("hide");

});

$(document).on('click', ".edit-customer", function (e) {

    if($(".customer-select").val())
    {
        var customer_id = $(".customer-select").val();
        var name = $(".customer-select").find(':selected').data('name');
        var family_name = $(".customer-select").find(':selected').data('familyname');
        var business_name = $(".customer-select").find(':selected').data('businessname');
        var address = $(".customer-select").find(':selected').data('address');
        var street_name = $(".customer-select").find(':selected').data('streetname');
        var street_number = $(".customer-select").find(':selected').data('streetnumber');
        var postcode = $(".customer-select").find(':selected').data('postcode');
        var city = $(".customer-select").find(':selected').data('city');
        var phone = $(".customer-select").find(':selected').data('phone');
        var email = $(".customer-select").find(':selected').data('email');
        
        $('#c_modal_customer_id').val(customer_id);
        $('#c_modal_name').val(name);
        $('#c_modal_family_name').val(family_name);
        $('#c_modal_business_name').val(business_name);
        $('#c_modal_address').val(address);
        $('#c_modal_street_name').val(street_name);
        $('#c_modal_street_number').val(street_number);
        $('#c_modal_check_address').val(1);
        $('#c_modal_postcode').val(postcode);
        $('#c_modal_city').val(city);
        $('#c_modal_phone').val(phone);
        $('#c_modal_email').val(email);

        $('#createCustomerModal').modal('toggle');
    }

});

$(".c_modal_submit_customer").click(function () {

    var customer_id = $("#c_modal_customer_id").val();
    var name = $('#c_modal_name').val();
    var family_name = $('#c_modal_family_name').val();
    var business_name = $('#c_modal_business_name').val();
    var postcode = $('#c_modal_postcode').val();
    var address = $('#c_modal_address').val();
    var street_name = $('#c_modal_street_name').val();
    var street_number = $('#c_modal_street_number').val();
    var city = $('#c_modal_city').val();
    var phone = $('#c_modal_phone').val();
    var email = $('#c_modal_email').val();
    var token = $('#token').val();

    var validation = $('.modal-body').find('.validation');

    var flag = 0;
    var email_flag = 0;

    $(validation).each(function () {

        if (!$(this).val()) {
            $(this).css('border', '1px solid red');
            flag = 1;
        }
        else {
            $(this).css('border', '');
        }

    });

    if (!flag) {

        if(email)
        {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

            if(!regex.test(email))
            {
                email_flag = 1;
            }
        }

        if (email_flag) {
            $('.alert-box').prepend('<div class="alert alert-simple alert-danger alert-dismissible text-left font__family-montserrat font__size-16 font__weight-light brk-library-rendered rendered">\n' +
            '<button type="button" class="close font__size-18" data-dismiss="alert">\n' +
            '  <span aria-hidden="true"><i class="fa fa-times cross"></i></span>\n' +
            '  <span class="sr-only">Close</span>\n' +
            '</button>\n' +
            '<i class="start-icon fa fa-times-circle faa-pulse animated"></i>\n' +
            '<span class="alt-msg">'+config1.email_invalid+'</span>\n' +
            '</div>');

            clearTimeout(alertTimeout);
            $('.alert-box .alert .cross').removeClass('fa-times');
            void $('.alert-box .alert .cross')[0].offsetWidth; // Trigger reflow
            $('.alert-box .alert .cross').addClass('fa-times');
            alertTimeout = setTimeout(function() {
                $('.alert-box .alert-simple').fadeOut(400, function () {
                    // Remove the old alert after it fades out
                    $(this).remove();
                });
			}, 15000);

            $('#createCustomerModal').modal('toggle');
            $('.right-side').animate({ scrollTop: 0, behavior: 'smooth' });
        }
        else {
            $('#cover').css('display', 'flex');

            $.ajax({

                type: "POST",
                data: "customer_id=" + customer_id + "&name=" + name + "&family_name=" + family_name + "&business_name=" + business_name + "&postcode=" + postcode + "&address=" + address + "&street_name=" + street_name + "&street_number=" + street_number + "&city=" + city + "&phone=" + phone + "&email=" + email + "&_token=" + token,
                url: config1.create_customer_url,

                success: function (data) {

                    $('#cover').css('display', 'none');

                    var newStateVal = data.data.id;

                    if(data.data.family_name)
                    {
                        var newName = data.data.name + " " + data.data.family_name;
                    }
                    else
                    {
                        var newName = data.data.name;
                    }

                    // Set the value, creating a new option if necessary
                    if ($(".customer-select").find("option[value=" + newStateVal + "]").length) {
                        $(".customer-select option[value='"+newStateVal+"']").data("name",data.data.name);
                        $(".customer-select option[value='"+newStateVal+"']").data("familyname",data.data.family_name ? data.data.family_name : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("businessname",data.data.business_name ? data.data.business_name : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("address",data.data.address ? data.data.address : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("streetname",data.data.street_name ? data.data.street_name : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("streetnumber",data.data.street_number ? data.data.street_number : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("postcode",data.data.postcode ? data.data.postcode : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("city",data.data.city ? data.data.city : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("phone",data.data.phone ? data.data.phone : "");
                        $(".customer-select option[value='"+newStateVal+"']").data("email",data.data.email ? data.data.email : "");
                        $(".customer-select option[value='"+newStateVal+"']").text(data.data.name + (data.data.family_name ? " " + data.data.family_name : ""));
                        $(".customer-select").select2().trigger('change');
                    } else {
                        // Create the DOM option that is pre-selected by default
                        var newState = new Option(newName, newStateVal, true, true);
                        // Append it to the select
                        $(".customer-select").append(newState).trigger('change');
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-name",data.data.name);
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-familyname",data.data.family_name);
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-businessname",data.data.business_name ? data.data.business_name : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-address",data.data.address ? data.data.address : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-streetname",data.data.street_name ? data.data.street_name : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-streetnumber",data.data.street_number ? data.data.street_number : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-postcode",data.data.postcode ? data.data.postcode : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-city",data.data.city ? data.data.city : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-phone",data.data.phone ? data.data.phone : "");
                        $(".customer-select option[value='"+newStateVal+"']").attr("data-email",data.data.email ? data.data.email : "");
                    }

                    if ($(".appointment_client").find("option[value=" + newStateVal + "]").length) {
                        $(".appointment_client option[value='"+newStateVal+"']").data("fname",data.data.name);
                        $(".appointment_client option[value='"+newStateVal+"']").data("lname",data.data.family_name ? data.data.family_name : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("businessname",data.data.business_name ? data.data.business_name : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("address",data.data.address ? data.data.address : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("streetname",data.data.street_name ? data.data.street_name : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("streetnumber",data.data.street_number ? data.data.street_number : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("postcode",data.data.postcode ? data.data.postcode : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("city",data.data.city ? data.data.city : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("phone",data.data.phone ? data.data.phone : "");
                        $(".appointment_client option[value='"+newStateVal+"']").data("email",data.data.email ? data.data.email : "");
                        $(".appointment_client option[value='"+newStateVal+"']").text(data.data.name + (data.data.family_name ? " " + data.data.family_name : ""));
                        $(".appointment_client").select2().trigger('change');
                    } else {
                        var newState1 = new Option(newName, newStateVal, true, true);
                        $(".appointment_client").append(newState1).trigger('change');
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-fname",data.data.name);
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-lname",data.data.family_name);
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-businessname",data.data.business_name ? data.data.business_name : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-address",data.data.address ? data.data.address : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-streetname",data.data.street_name ? data.data.street_name : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-streetnumber",data.data.street_number ? data.data.street_number : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-postcode",data.data.postcode ? data.data.postcode : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-city",data.data.city ? data.data.city : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-phone",data.data.phone ? data.data.phone : "");
                        $(".appointment_client option[value='"+newStateVal+"']").attr("data-email",data.data.email ? data.data.email : "");
                    }

                    if ($("#note_client").find("option[value=" + newStateVal + "]").length) {
                        $("#note_client option[value='"+newStateVal+"']").text(data.data.name + (data.data.family_name ? " " + data.data.family_name : ""));
                        $("#note_client").select2().trigger('change');
                    } else {
                        var newState2 = new Option(newName, newStateVal, true, true);
                        $("#note_client").append(newState2).trigger('change');
                    }

                    $('.alert-box').prepend('<div class="alert alert-simple alert-success alert-dismissible text-left font__family-montserrat font__size-16 font__weight-light brk-library-rendered rendered">\n' +
                    '<button type="button" class="close font__size-18" data-dismiss="alert">\n' +
                    '  <span aria-hidden="true"><i class="fa fa-times cross"></i></span>\n' +
                    '  <span class="sr-only">Close</span>\n' +
                    '</button>\n' +
                    '<i class="start-icon fa fa-check-circle faa-tada animated"></i>\n' +
                    '<span class="alt-msg">' + data.message + '</span>\n' +
                    '</div>');
    
                    clearTimeout(alertTimeout);
                    $('.alert-box .alert .cross').removeClass('fa-times');
                    void $('.alert-box .alert .cross')[0].offsetWidth; // Trigger reflow
                    $('.alert-box .alert .cross').addClass('fa-times');
                    alertTimeout = setTimeout(function() {
                        $('.alert-box .alert-simple').fadeOut(400, function () {
                            // Remove the old alert after it fades out
                            $(this).remove();
                        });
                    }, 15000);

                    $('#createCustomerModal').modal('toggle');
                    $('.right-side').animate({ scrollTop: 0, behavior: 'smooth' });
                },
                error: function (data) {

                    $('#cover').css('display', 'none');

                    /*if (data.status == 422) {
                        $.each(data.responseJSON.errors, function (i, error) {
                            $('.alert-box').html('<div class="alert alert-danger">\n' +
                                '                                            <button type="button" class="close cl-btn" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>\n' +
                                '                                            <p class="text-left">'+error[0]+'</p>\n' +
                                '                                        </div>');
                        });
                        $('.alert-box').show();
                        $('.alert-box').delay(5000).fadeOut(400);
                    }*/

                    $('.alert-box').prepend('<div class="alert alert-simple alert-danger alert-dismissible text-left font__family-montserrat font__size-16 font__weight-light brk-library-rendered rendered">\n' +
                    '<button type="button" class="close font__size-18" data-dismiss="alert">\n' +
                    '  <span aria-hidden="true"><i class="fa fa-times cross"></i></span>\n' +
                    '  <span class="sr-only">Close</span>\n' +
                    '</button>\n' +
                    '<i class="start-icon fa fa-times-circle faa-pulse animated"></i>\n' +
                    '<span class="alt-msg">'+config1.something_went_wrong+'</span>\n' +
                    '</div>');
    
                    clearTimeout(alertTimeout);
                    $('.alert-box .alert .cross').removeClass('fa-times');
                    void $('.alert-box .alert .cross')[0].offsetWidth; // Trigger reflow
                    $('.alert-box .alert .cross').addClass('fa-times');
                    alertTimeout = setTimeout(function() {
                        $('.alert-box .alert-simple').fadeOut(400, function () {
                            // Remove the old alert after it fades out
                            $(this).remove();
                        });
                    }, 15000);

                    $('#createCustomerModal').modal('toggle');
                    $('.right-side').animate({ scrollTop: 0, behavior: 'smooth' });
                }

            });
        }
    }

});