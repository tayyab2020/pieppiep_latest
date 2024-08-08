<div id="quotationModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <form id="send-quotation-form" action="{{route('send-new-quotation')}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}

            <input type="hidden" name="quotation_id3" id="quotation_id3">
            <input type="hidden" name="current_route" value="{{Route::currentRouteName()}}">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{__('text.Quotation Mail Body')}}</h4>
                </div>
                <div class="modal-body">

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.To')}}:</label>
                            <input type="text" name="mail_to" class="form-control">
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

                    <!-- <div style="margin: 20px 0;" class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Images')}}:</label>    
                        </div>
                        
                        <div id="image_picker"></div>
                        
                        <script>
                        
                            $("#image_picker").spartanMultiImagePicker({
                                groupClassName: 'col-lg-6 col-md-6 col-sm-6 col-xs-12',
                                fieldName: 'fileUpload[]' // this configuration will send your images named "fileUpload" to the server
                            });
                            
                        </script>
                        
                    </div> -->

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
                            <input type="text" name="mail_subject" class="form-control">
                        </div>
                    </div>

                    <div style="margin: 20px 0;" class="row">
                        <div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label>{{__('text.Text')}}:</label>
                            <input type="hidden" name="mail_body">
                            <div class="summernote"></div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button style="border: 0;outline: none;background-color: #81ccda !important;" type="button" class="btn btn-primary save-draft">{{__('text.Save as draft')}}</button>
                    <button style="border: 0;outline: none;background-color: #5cb85c !important;" type="button" class="btn btn-primary submit-form">{{__('text.Submit')}}</button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>

    $(document).on('click', ".send-new-quotation", function (e) {

        var id = $(this).data('id');

        $.ajax({

            type: "GET",
            data: "id=" + id + '&type=quotation',
            url: "<?php echo url('/aanbieder/get-customer-email')?>",

            success: function (data) {

                var parent = $('#quotationModal').find(".cc_container");
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

                $('#quotation_id3').val(id);

                if((data[3] == null) || (data[3] == 0))
                {
                    $("[name='mail_to']").val(data[0]);
                }
                else
                {
                    $("[name='mail_to']").val("");
                }
                    
                $("[name='mail_subject']").val(data[1]);
                $("[name='mail_body']").val(data[2]);
                $('#quotationModal').find(".custom-file-upload-hidden").val("");
                $('#quotationModal').find(".file-upload-input").val("");
                $('#quotationModal').find(".file-upload-input").attr("title","");
                $('#quotationModal').find(".note-editable").html(data[2]);
                $('#quotationModal').modal('toggle');
                $('.modal-backdrop').hide();

            },
            error: function (data) {}

        });

    });

    $(document).on('click', '.submit-form', function () {

        var flag = 0;
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

        if(!$("[name='mail_to']").val())
        {
            $("[name='mail_to']").css('border','1px solid red');
            flag = 1;
        }
        else{
            if(regex.test($("[name='mail_to']").val()))
            {
                $("[name='mail_to']").css('border','');
            }
            else{
                $("[name='mail_to']").css('border','1px solid red');
                flag = 1;
            }
        }

        $("#quotationModal").find("[name='mail_cc[]']").each( function ( target ) {
                    
            if(!$(this).val() || regex.test($(this).val()))
            {
                $(this).css('border','');
            }
            else{
                $(this).css('border','1px solid red');
                flag = 1;
            }

        });

        if(!$("[name='mail_subject']").val())
        {
            $("[name='mail_subject']").css('border','1px solid red');
            flag = 1;
        }
        else{
            $("[name='mail_subject']").css('border','');
        }

        if(!$("[name='mail_body']").val())
        {
            $('#quotationModal').find(".note-editable").css('border','1px solid red');
            flag = 1;
        }
        else{
            $('#quotationModal').find(".note-editable").css('border','');
        }

        if(!flag)
        {
            $('#send-quotation-form').submit();
        }

    });

</script>