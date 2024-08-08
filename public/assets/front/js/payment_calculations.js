function calculate_percentage_amounts(last_row,current,exclude = null)
{
    var total_percentage_amount = 0;
    var total_amount = $("#total_amount").val();
    total_amount = total_amount.replace(/\./g, '');
    total_amount = total_amount.replace(/\,/g, '.');

    $("input[name='pc_amount[]']").each(function (i, obj) {

        var id = $(this).parents(".pc-content-div").data("id");
        
        if(id != exclude)
        {
            var amount = $(this).val() ? $(this).val() : "0,00";
            amount = amount.replace(/\,/g, '.');
            total_percentage_amount = parseFloat(total_percentage_amount) + parseFloat(amount);
        }

    });

    var rem_amount = total_amount - total_percentage_amount;
    rem_amount = parseFloat(rem_amount);

    if(last_row)
    {
        // if(rem_amount < 0)
        // {
        // 	rem_amount = 0;
        // }

        rem_amount = rem_amount.toFixed(2);
        rem_amount = isNaN(rem_amount) ? "0.00" : rem_amount;
        rem_amount = rem_amount.replace(/\./g, ',');
        last_row.find('.pc_amount').val(rem_amount);
    }
    else
    {
        var flag = 0;

        if(current)
        {
            if(current.index() == 1)
            {
                flag = 1;
            }
        }

        if(!flag)
        {
            var amount = $('.pc_table .pc-content-div:first').find(".pc_amount").val();
            amount = amount.replace(/\,/g, '.');
            rem_amount = parseFloat(amount) + parseFloat(rem_amount);

            // if(rem_amount < 0)
            // {
            // 	rem_amount = 0;
            // }

            rem_amount = rem_amount.toFixed(2);
            rem_amount = isNaN(rem_amount) ? "0.00" : rem_amount;
            rem_amount = rem_amount.replace(/\./g, ',');
        
            $('.pc_table .pc-content-div:first').find(".pc_amount").val(rem_amount);
        }
    }

}

function calculate_percentages(last_row,current,exclude = null)
{
    var total_percentage = 0;

    $("input[name='pc_percentage[]']").each(function (i, obj) {

        var id = $(this).parents(".pc-content-div").data("id");
        
        if(id != exclude)
        {
            var percentage = $(this).val() ? $(this).val() : "0,00";
            percentage = percentage.replace(/\,/g, '.');
            total_percentage = parseFloat(total_percentage) + parseFloat(percentage);
        }

    });

    var rem_percentage = 100 - total_percentage;
    rem_percentage = parseFloat(rem_percentage);

    if(last_row)
    {
        // if(rem_percentage < 0)
        // {
        // 	rem_percentage = 0;
        // }

        rem_percentage = rem_percentage.toFixed(2);
        rem_percentage = rem_percentage.replace(/\./g, ',');
        last_row.find('.pc_percentage').val(rem_percentage);
    }
    else
    {
        var flag = 0;

        if(current)
        {
            if(current.index() == 1)
            {
                flag = 1;
            }
        }

        if(!flag)
        {
            var percentage = $('.pc_table .pc-content-div:first').find(".pc_percentage").val();
            percentage = percentage.replace(/\,/g, '.');
            rem_percentage = parseFloat(percentage) + parseFloat(rem_percentage);

            if(rem_percentage < 0)
            {
                rem_percentage = 0;
            }

            rem_percentage = rem_percentage.toFixed(2);
            rem_percentage = rem_percentage.replace(/\./g, ',');
        
            $('.pc_table .pc-content-div:first').find(".pc_percentage").val(rem_percentage);
        }
    }

}

function add_pc_row(calculate = 1) {

    var rowCount = $('.pc_table .pc-content-div:last').data('id');
    rowCount = rowCount + 1;

    $(`.pc_table`).append('<div class="pc-content-div" data-id="' + rowCount + '">\n' +
    '\n' +
    '																<div style="width: 18%;">\n' +
    '																	<div style="display: flex;align-items: center;">\n' +
    '																		<div style="width: 90%;">\n' +
    '																			<input type="text" value="" maskedformat="9,1" class="form-control pc_percentage m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_percentage[]">\n' +
    '																		</div>\n' +
    '																	</div>\n' +
    '																</div>\n' +
    '\n' +
    '																<div style="width: 18%;">\n' +
    '																	<div style="display: flex;align-items: center;">\n' +
    '																		<div style="width: 90%;">\n' +
    '																			<input type="text" value="" maskedformat="9,1" class="form-control pc_amount m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_amount[]">\n' +
    '																		</div>\n' +
    '																	</div>\n' +
    '																</div>\n' +
    '\n' +
    '																<div style="width: 18%;">\n' +
    '																	<div style="display: flex;align-items: center;">\n' +
    '																		<div style="width: 90%;">\n' +
    '																			<input type="text" readonly class="form-control pc_date m-input" style="border: 1px solid #ccc;background: transparent;width: 100%;height: 35px !important;" name="pc_date[]">\n' +
    '																		</div>\n' +
    '																	</div>\n' +
    '																</div>\n' +
    '\n' +
    '																<div style="width: 18%;">\n' +
    '																	<div style="display: flex;align-items: center;">\n' +
    '																		<div style="width: 90%;">\n' +
    '																			<select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_paid_by" name="pc_paid_by[]">\n' +
    '																				<option value="Pending">'+quote_config.pending_payments+'</option>\n' +
    '																				<option value="Mollie">'+quote_config.mollie+'</option>\n' +
    '																				<option value="Betaallink">'+quote_config.pin_device+'</option>\n' +
    '																				<option value="Bank">'+quote_config.bank+'</option>\n' +
    '																				<option value="Cash">'+quote_config.cash+'</option>\n' +
    '																				<option value="Settled">'+quote_config.settled+'</option>\n' +
    '																			</select>\n' +
    '																		</div>\n' +
    '																	</div>\n' +
    '																</div>\n' +
    '\n' +
    '																<div style="width: 18%;">\n' +
    '																	<div style="display: flex;align-items: center;">\n' +
    '																		<div style="width: 90%;">\n' +
    '																			<select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_description" name="pc_description[]">\n' +
    '																				<option value="By accepting">'+quote_config.by_accepting+'</option>\n' +
    '																				<option value="By delivery goods">'+quote_config.by_delivery_goods+'</option>\n' +
    '																				<option value="By finishing work">'+quote_config.by_finishing_work+'</option>\n' +
    '																			</select>\n' +
    '																		</div>\n' +
    '																	</div>\n' +
    '																</div>\n' +
    '\n' +
    '																<div style="padding: 0;width: 10%;display: flex;">\n' +
    '\n' +
    '																	<div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;">\n' +
    '\n' +
    '																		<span id="next-row-span" class="tooltip1 add-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
    '																			<i id="next-row-icon" class="fa fa-fw fa-plus"></i>\n' +
    '																			<span class="tooltiptext">'+quote_config.add+'</span>\n' +
    '																		</span>\n' +
    '\n' +
    '																		<span id="next-row-span" class="tooltip1 remove-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
    '																			<i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>\n' +
    '																			<span class="tooltiptext">'+quote_config.remove+'</span>\n' +
    '																		</span>\n' +
    '\n' +
    '																	</div>\n' +
    '\n' +
    '																</div>\n' +
    '\n' +
    '															</div>');

    var last_row = $('.pc_table .pc-content-div:last');
    calculate_percentage_amounts(last_row);
    
    var now = new Date();

    last_row.find('.pc_date').datetimepicker({
        format: 'DD-MM-YYYY',
        // minDate: now,
        defaultDate: now,
        ignoreReadonly: true,
        locale:'du',
    }).on('dp.change', function(selected){
        clearTimeout(debounceTimeout);
    	debounceTimeout = setTimeout(function() {
			AutoSave();
		}, 2000); // Autosave after 5 second of inactivity
    });

    if(calculate)
    {
        payment_calculations(1,last_row,1);
    }
    else
    {
        return last_row;
    }
}

function payment_calculations(type = 0,current = null,check = 0)
{
    var total_amount = $("#total_amount").val();
    total_amount = total_amount.replace(/\./g, '');
    total_amount = total_amount.replace(/\,/g, '.');

    if(type != 2)
    {
        if(type == 0)
        {
            // $("input[name='pc_percentage[]']").each(function (i, obj) {

            // 	var percentage = $(this).val();
            // 	percentage = percentage.replace(/\,/g, '.');
            // 	percentage = percentage ? percentage : 0;

            // 	var amount = (percentage/100) * total_amount;
            // 	$(this).parents(".pc-content-div").find(".pc_amount").val(amount.toFixed(2).replace(/\./g, ','));

            // });

            var percentage = current.find(".pc_percentage").val();
            percentage = percentage.replace(/\,/g, '.');
            percentage = percentage ? percentage : 0;

            var amount = (percentage/100) * total_amount;
            amount = amount.toFixed(2);
            amount = isNaN(amount) ? "0.00" : amount;
            current.find(".pc_amount").val(amount.replace(/\./g, ','));
        }
        else
        {
            if(!current)
            {
                $("input[name='pc_amount[]']").each(function (i, obj) {

                    var amount = $(this).val();
                    amount = amount.replace(/\./g, '');
                    amount = amount.replace(/\,/g, '.');
                    amount = amount ? amount : 0;

                    var percentage = total_amount != 0 ? (amount/total_amount) * 100 : 0;
                    // percentage = percentage.toFixed(2);
                    percentage = Math.round(percentage);
                    percentage = isNaN(percentage) ? 0 : percentage;
                    // $(this).parents(".pc-content-div").find(".pc_percentage").val(percentage.replace(/\./g, ','));
                    $(this).parents(".pc-content-div").find(".pc_percentage").val(percentage);
            
                });
            }
            else
            {
                var amount = current.find(".pc_amount").val();
                amount = amount.replace(/\./g, '');
                amount = amount.replace(/\,/g, '.');
                amount = amount ? amount : 0;

                var percentage = total_amount != 0 ? (amount/total_amount) * 100 : 0;
                // percentage = percentage.toFixed(2);
                percentage = Math.round(percentage);
                percentage = isNaN(percentage) ? 0 : percentage;
                current.find(".pc_percentage").val(percentage);
            }
        }
    }

    var pc_total_percentage = 0;
    var pc_total_amount = 0;

    $("input[name='pc_percentage[]']").each(function (i, obj) {

        var percentage = $(this).val();
        percentage = parseFloat(percentage.replace(/\,/g, '.'));
        percentage = percentage ? percentage : 0;
        pc_total_percentage = pc_total_percentage + percentage;

    });

    $("input[name='pc_amount[]']").each(function (i, obj) {
    
        var amount = $(this).val();
        amount = amount.replace(/\./g, '');
        amount = parseFloat(amount.replace(/\,/g, '.'));
        amount = amount ? amount : 0;
        pc_total_amount = pc_total_amount + amount;

    });

    pc_total_percentage = isNaN(pc_total_percentage) ? 0.00 : (pc_total_percentage == 100.01 ? Math.round(pc_total_percentage) : pc_total_percentage);
    pc_total_amount = isNaN(pc_total_amount) ? 0.00 : pc_total_amount;
    $(".pc_percentages_total").val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(pc_total_percentage));
    $(".pc_amounts_total").val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(pc_total_amount));

    if(pc_total_percentage.toFixed(2) == 100)
    {
        $(".pc_percentages_total").css("border","");
    }
    else
    {
        $(".pc_percentages_total").css("border","1px solid red");
    }

    if(pc_total_amount.toFixed(2) == total_amount)
    {
        $(".pc_amounts_total").css("border","");
    }
    else
    {
        $(".pc_amounts_total").css("border","1px solid red");
    }

    if(!check && (pc_total_percentage != 100))
    {
        var last_pending = "";
        var exclude = "";

        $("select[name='pc_paid_by[]']").each(function() { 
            if($(this).val() == "Pending")
            {
                last_pending = $(this);
            }
        });

        if(last_pending)
        {
            var last_row = last_pending.parents(".pc-content-div");
            exclude = last_row.data("id");
            calculate_percentage_amounts(last_row,null,exclude);
        }
        else
        {
            var last_row = add_pc_row(0);
        }

        calculate_percentages(last_row,null,exclude);
        payment_calculations(0,last_row,1);
    }

    // AutoSave();
}

$(document).ready(function () {

    var now = new Date();

    $('.pc_date').datetimepicker({
        format: 'DD-MM-YYYY',
        // minDate: now,
        defaultDate: now,
        ignoreReadonly: true,
        locale:'du',
    }).on('dp.change', function(selected){
        clearTimeout(debounceTimeout);
    	debounceTimeout = setTimeout(function() {
			AutoSave();
		}, 2000); // Autosave after 5 second of inactivity
    });

});