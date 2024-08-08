<div id="createCustomerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div style="display: block;" class="modal-header">
                <button style="background-color: white !important;color: black !important;" type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{{__('text.Create Customer')}}</h3>
            </div>

            <div class="modal-body" id="myWizard" style="display: inline-block;">

                <input type="hidden" id="token" name="token" value="{{csrf_token()}}">
                <input type="hidden" id="c_modal_customer_id" value="">

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_name" class="form-control validation" placeholder="{{$lang->suf}}"
                               type="text">
                    </div>
                </div>

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_family_name" class="form-control"
                               placeholder="{{$lang->fn}}" type="text">
                    </div>
                </div>

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_business_name" class="form-control" placeholder="{{$lang->bn}}"
                               type="text">
                    </div>
                </div>

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_address" class="form-control" placeholder="{{$lang->ad}}" type="text">
                        <input type="hidden" id="c_modal_check_address" value="0">
                        <input type="hidden" id="c_modal_street_name">
                        <input type="hidden" id="c_modal_street_number">
                    </div>
                </div>


                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_postcode" class="form-control" placeholder="{{$lang->pc}}" type="text">
                    </div>
                </div>


                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_city" class="form-control" placeholder="{{$lang->ct}}" type="text">
                    </div>
                </div>

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </div>
                        <input id="c_modal_phone" class="form-control" placeholder="{{$lang->pn}}" type="text">
                    </div>
                </div>

                <div class="form-group col-sm-6 fl">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <input id="c_modal_email" class="form-control" placeholder="{{$lang->sue}}" type="email">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" style="border: 0;outline: none;background-color: #5cb85c !important;"
                        class="btn btn-primary c_modal_submit_customer">{{__('text.Create')}}</button>
            </div>

        </div>

    </div>
</div>

<style>
    .pac-container {
		z-index: 1000000;
	}

    .fl
    {
        float: left;
    }

    #createCustomerModal .input-group
    {
        display: table;
    }

    #createCustomerModal .form-control
    {
        width: 100%;
    }

    #createCustomerModal .modal-dialog
    {
        max-width: 600px;
    }

    .input-group-addon
    {
        display: table-cell;
        width: 1%;
        white-space: nowrap;
        vertical-align: middle;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1;
        color: #555;
        text-align: center;
        background-color: #eee;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .input-group .form-control:first-child,.input-group-addon:first-child,.input-group-btn:first-child>.btn,.input-group-btn:first-child>.btn-group>.btn,.input-group-btn:first-child>.dropdown-toggle,.input-group-btn:last-child>.btn-group:not(:last-child)>.btn,.input-group-btn:last-child>.btn:not(:last-child):not(.dropdown-toggle) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0
    }

    .input-group-addon:first-child
    {
        border-right: 0;
    }
</style>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDnNrRbo2J8d60OLHlolqpP_jZm7WVxpA8&libraries=places&callback=initCustomerMap" defer></script>
<script>
    // global app configuration object
    var config1 = {
        email_invalid: "{{__('text.Email address is not valid...')}}",
        create_customer_url: "{{url('/aanbieder/create-customer')}}",
        something_went_wrong: "{{__('text.Something went wrong!')}}",
    };

    function initCustomerMap() {

        if (typeof initMap === 'function') {
            // The function is defined
            initMap();
        }

		var input = document.getElementById('c_modal_address');

		var options = {
			componentRestrictions: { country: "nl" }
		};

		var autocomplete = new google.maps.places.Autocomplete(input, options);

		// Set the data fields to return when the user selects a place.
		autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

		autocomplete.addListener('place_changed', function () {

			var flag = 0;

			var place = autocomplete.getPlace();

			if (!place.geometry) {

				// User entered the name of a Place that was not suggested and
				// pressed the Enter key, or the Place Details request failed.
				window.alert("{{__('text.No details available for input: ')}}" + place.name);
				return;
			}
			else {
				var string = $('#c_modal_address').val().substring(0, $('#c_modal_address').val().indexOf(',')); //first string before comma

				if (string) {
					var is_number = $('#c_modal_address').val().match(/\d+/);

					if (is_number === null) {
						flag = 1;
					}
				}
			}

			var city = '';
			var postal_code = '';
			var street_name = '';
			var street_number = '';

			for (var i = 0; i < place.address_components.length; i++)
            {
				if (place.address_components[i].types[0] == 'postal_code' || place.address_components[i].types[0] == 'postal_code_prefix')
                {
					postal_code = place.address_components[i].long_name;
				}

				if (place.address_components[i].types[0] == 'locality')
                {
					city = place.address_components[i].long_name;
				}

				if(place.address_components[i].types[0] == 'route')
                {
                    street_name = place.address_components[i].long_name;
                }

                if(place.address_components[i].types[0] == 'street_number')
                {
                    street_number = place.address_components[i].long_name;
                }
			}

			if (city == '')
            {
				for (var i = 0; i < place.address_components.length; i++)
                {
					if (place.address_components[i].types[0] == 'administrative_area_level_2')
                    {
						city = place.address_components[i].long_name;
					}
				}
			}

			if (postal_code == '' || city == '')
            {
				flag = 1;
			}

			if (!flag)
            {
				$('#c_modal_check_address').val(1);
				$("#c_modal_address_error").remove();
				$('#c_modal_postcode').val(postal_code);
				$("#c_modal_city").val(city);
				$("#c_modal_street_name").val(street_name);
                $("#c_modal_street_number").val(street_number);
			}
			else
            {
				$('#c_modal_address').val('');
				$('#c_modal_postcode').val('');
				$("#c_modal_city").val('');
				$("#c_modal_street_name").val('');
                $("#c_modal_street_number").val('');

				$("#c_modal_address_error").remove();
				$('#c_modal_address').parent().parent().append('<small id="c_modal_address_error" style="color: red;display: block;margin-top: 10px;">{{__('text.Kindly write your full address with house / building number so system can detect postal code and city from it!')}}</small>');
			}

		});

	}

	$("#c_modal_address").on('input', function (e) {
		$(this).next('input').val(0);
	});

	$("#c_modal_address").focusout(function () {
		var check = $(this).next('input').val();

		if (check == 0) {
			$(this).val('');
			$('#c_modal_postcode').val('');
			$("#c_modal_city").val('');
		}
	});
</script>
<script src="{{asset('assets/front/js/create_customer.js?v=1')}}"></script>