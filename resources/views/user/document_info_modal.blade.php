<div id="infoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <button style="background-color: white !important;color: black !important;" type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{{__('text.Document Info')}}</h3>
            </div>

            <div class="modal-body" id="myWizard" style="display: inline-block;">

                <div class="form-group col-sm-6">
                    <label>{{__('text.Document date')}}</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input readonly style="background: transparent;" value="{{$document_date}}" type="text" placeholder="{{__('text.Document date')}}" class="form-control document_date">
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    <label>{{__('text.Document Number')}}</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-file"></i>
                        </div>
                        <input value="{{$dc}}" class="form-control document_number" placeholder="{{__('text.Document Number')}}" type="text">
                    </div>
                </div>

                @if(Route::currentRouteName() == 'create-new-quotation' || Route::currentRouteName() == 'create-custom-quotation' || Route::currentRouteName() == 'create-direct-invoice' || Route::currentRouteName() == 'view-new-quotation')

                    <div class="form-group col-sm-6">
                        <label>{{__('text.Expire date')}}</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input readonly style="background: transparent;" value="{{$expire_date}}" type="text" placeholder="{{__('text.Expire date')}}" class="form-control expire_date">
                        </div>
                    </div>

                @endif

            </div>

            <div class="modal-footer">
                <button type="button" style="border: 0;outline: none;background-color: #5cb85c !important;"
                        class="btn btn-primary submit-document-info">{{__('text.Submit')}}</button>
            </div>

        </div>

    </div>
</div>

<script>

    $(document).ready(function () {

		$('.document_date').datetimepicker({
        	format: 'DD-MM-YYYY',
        	// minDate: now,
        	ignoreReadonly: true,
        	locale:'du',
    	});

		$('.expire_date').datetimepicker({
        	format: 'DD-MM-YYYY',
        	// minDate: now,
        	ignoreReadonly: true,
        	locale:'du',
    	});

        $('body').on('keypress', ".document_number", function (e) {

		    var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
		    var val = String.fromCharCode(charCode);

		    if (!val.match(/^[0-9]+$/))  // For characters validation
		    {
			    e.preventDefault();
			    return false;
		    }

        });

        $('body').on('input', '.document_number' ,function(){

            var value = $(this).val();
            value = value.replace(/^0+/, '');

            while (value.length < 6) value = "0" + value;

            $(this).val(value);

        });

        $('body').on('click', '.submit-document-info' ,function(){

            var document_date = $(".document_date").val();
            var expire_date = $(".expire_date").val();
            var document_id = $('input[name="quotation_id"]').val();
            var is_negative_invoice = $('input[name="negative_invoice"]').val();

			if(is_negative_invoice == 1)
			{
				document_id = $('input[name="negative_invoice_id"]').val();
			}

            var document_number = $(".document_number").val();
            var is_invoice = $('input[name="is_invoice"]').val();
            var token = $('[name="_token"]').val();

            $("#document_date").val(document_date);
            $("#expire_date").val(expire_date);

            if(is_invoice == 0)
            {
                var data = "quotation_counter=" + document_number;
            }
            else
            {
                var data = "invoice_counter=" + document_number;
            }

            data = data + "&document_id=" + document_id + "&api=1" + "&_token=" + token;

            $.ajax({
                url: '{{route("save-prefix-settings")}}',
				type: 'POST',
				data: data,
				success: function(data){
                    if(data != 0)
                    {
                        $(".document_number").css("border-color","");
                        $("#document_number").val(data);
                        $('#infoModal').modal('toggle');
                        clearTimeout(debounceTimeout);
    			        debounceTimeout = setTimeout(function() {
					        AutoSave();
				        }, 2000); // Autosave after 5 second of inactivity
                    }
                    else
                    {
                        $(".document_number").css("border-color","red");
                    }
                },
				complete: function(){}
			});

        });

    });

</script>