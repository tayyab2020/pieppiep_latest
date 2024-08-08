<div id="orderModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <form id="send-order-form" action="{{route('send-new-order')}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}

            <input type="hidden" name="quotation_id1" id="quotation_id1">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{__('text.Order Mail Body')}}</h4>
                </div>
                <div class="modal-body">

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deliver_to_box">
                            <label>{{__('text.Deliver To')}}</label>
                            <select name="deliver_to" id="deliver_to">
                                <option value="1">{{__('text.Retailer')}}</option>
                                <option value="2">{{__('text.Customer')}}</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Delivery Date')}}</label>
                            <input style="height: 45px;background: white;" type="text" name="delivery_date" id="delivery_date_picker" class="form-control" placeholder="{{__('text.Select Delivery Date')}}" readonly autocomplete="off">
                        </div>
                    </div>

                    <div style="margin: 20px 0;" class="row cc_container">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cc_row">
                            <label>{{__('text.CC')}}:</label>
                            <div style="width: 100%;display: flex;">
                                <input type="text" name="mail_cc[]" class="form-control">
                                <div style="display: flex;justify-content: flex-end;padding: 0;">
                                    <span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="add-cc"><i style="font-size: 18px;" class="fa fa-fw fa-plus"></i></span>
                                    <span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="remove-cc"><i style="font-size: 18px;" class="fa fa-fw fa-trash-o"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin: 20px 0;" class="row">
                    
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Documents')}}:</label>    
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="custom-file-upload">
                                <input type="file" id="file" name="myfiles[]" multiple />
                            </div>
                        </div>

                    </div>

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Subject')}}:</label>
                            <input type="text" name="mail_subject1" class="form-control">
                        </div>
                    </div>

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Text')}}:</label>
                            <input type="hidden" name="mail_body1">
                            <div class="summernote"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button style="border: 0;outline: none;background-color: #81ccda !important;" type="button" class="btn btn-primary save-draft">{{__('text.Save as draft')}}</button>
                    <button style="border: 0;outline: none;background-color: #5cb85c !important;" type="button" class="btn btn-primary submit-form1">{{__('text.Submit')}}</button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>

    $(document).on('click', ".send-new-order", function (e) {

        var id = $(this).data('id');
        var date = $(this).data('date');

        $.ajax({

            type: "GET",
            data: "id=" + id + '&type=order',
            url: "<?php echo url('/aanbieder/get-customer-email')?>",

            success: function (data) {

                var parent = $('#orderModal').find(".cc_container");
                var mail_ccs = data[6] ? data[6].split(",") : [];
                parent.find(".cc_row").remove();

                if(mail_ccs && mail_ccs.length > 0)
                {
                    add_cc_row(parent,mail_ccs);
                }
                else
                {
                    add_cc_row(parent);
                }

                $('#quotation_id1').val(id);
                data[4] ? $('#deliver_to').val(data[4]) : $('#deliver_to').val(1);
                $('#deliver_to').trigger('change.select2');
                data[5] ? $('#delivery_date_picker').val(data[5]) : $('#delivery_date_picker').val(date);
                $("[name='mail_subject1']").val(data[1]);
                $("[name='mail_body1']").val(data[2]);
                $('#orderModal').find(".custom-file-upload-hidden").val("");
                $('#orderModal').find(".file-upload-input").val("");
                $('#orderModal').find(".file-upload-input").attr("title","");
                $('#orderModal').find(".note-editable").html(data[2]);
                $('#orderModal').modal('toggle');
                $('.modal-backdrop').hide();

            },
            error: function (data) {}

        });

    });

    $(document).on('click', '.submit-form1', function () {

        var flag = 0;
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        $("#orderModal").find("[name='mail_cc[]']").each( function ( target ) {

            if(!$(this).val() || regex.test($(this).val()))
            {
                $(this).css('border','');
            }
            else{
                $(this).css('border','1px solid red');
                flag = 1;
            }

        });

        if(!$("[name='delivery_date']").val())
        {
            $("[name='delivery_date']").css('border','1px solid red');
            flag = 1;
        }
        else
        {
            $("[name='delivery_date']").css('border','');
        }

        if(!$("[name='mail_subject1']").val())
        {
            $("[name='mail_subject1']").css('border','1px solid red');
            flag = 1;
        }
        else{
            $("[name='mail_subject1']").css('border','');
        }

        if(!$("[name='mail_body1']").val())
        {
            $('#orderModal').find(".note-editable").css('border','1px solid red');
            flag = 1;
        }
        else{
            $('#orderModal').find(".note-editable").css('border','');
        }

        if(!flag)
        {
            $('#send-order-form').submit();
        }

    });
    
</script>