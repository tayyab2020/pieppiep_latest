function show_action_btns_loader(disable = 0)
{
    if(disable)
    {
        $(".actions-sec").find(".icon-container1").addClass("hide");
        $(".action-btns").show();
    }
    else
    {
        $(".action-btns").hide();
        $(".actions-sec").find(".icon-container1").removeClass("hide");
    }
}

var auto_save_flag = 0;

function AutoSave(){

    show_action_btns_loader();
    auto_save_flag = 0;
    var data = $("#form-quote").serialize();
    var is_invoice = $('input[name="is_invoice"]').val();
    var negative_invoice = $('input[name="negative_invoice"]').val();

    $.ajax({
        url: auto_save_route.store_new_quotation,
        type: 'POST',
        data: data,
        dataType: "json",
        // async: false,
        success: function(data){

            var parts = data[0].split("-");
            var lastPart = parts[parts.length - 1];

            $(".q_i_number").text(auto_save_route.document_number + ": " + data[0]);
            $(".document_number").val(lastPart);
            $("#document_number").val(lastPart);
            $(".document_date_text").text(auto_save_route.document_date + ": " + data[1]);

            if(is_invoice == 0)
            {
                $('input[name="quotation_id"]').val(data[2]);
                
                if(!$(".con-panel").find(".send-quote-btn").length)
                {
                    if($("#quote_request_id").val())
                    {
                        $(".con-panel").prepend('<a style="margin-right: 10px;border-radius: 10px;line-height: 22px;" class="btn btn-success send-quote-btn" href="'+auto_save_route.send_quotation_url+'/'+data[2]+'">'+auto_save_route.send_quotation+'</a>');
                    }
                    else
                    {
                        $(".con-panel").prepend('<a style="margin-right: 10px;border-radius: 10px;line-height: 22px;background-color: #5cb85c;border-color: #4cae4c;color: white;" class="btn send-new-quotation send-quote-btn" data-id="'+data[2]+'" href="javascript:void(0)">'+auto_save_route.send_quotation+'</a>');
                    }
                }
            }
            else
            {
                if(negative_invoice == 1)
                {
                    $('input[name="negative_invoice_id"]').val(data[2]);
                }
            }
        },
        complete: function(){
            // auto_save_flag = 1;
            show_action_btns_loader(1);
        }
    });

}