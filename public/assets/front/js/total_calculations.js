function calculate_total() {

    var total = 0;
    var total_net_amount = 0;
    var vat_total = 0;
    var price_before_labor_total = 0;
    var vat_data = [];
    var vat_index = 0;

    $("input[name='total[]']").each(function (i, obj) {

        var rate = 0;
        var row_id = $(this).parent().data('id');
        var qty = $('#products_table').find(`[data-id='${row_id}']`).find('input[name="qty[]"]').val();
        var vat_percentage = $('#products_table').find(`[data-id='${row_id}']`).find(".vat option:selected").data('percentage');
        vat_percentage = vat_percentage == undefined ? 0 : vat_percentage;
        
        var qty_negative = qty.includes("-");
        // qty = qty.replace(/\-/g, '');

        if (qty.slice(qty.length - 1) == ',') {
            qty = qty + '00';
        }

        qty = qty.replace(/\,/g, '.');

        if (!qty || qty == "-") {
            qty = 0;
        }

        // if (!obj.value || isNaN(obj.value)) {
        //     rate = 0;
        // }
        // else {
        //     rate = obj.value;
        // }

        // rate = rate * qty;

        // var labor_impact = $('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact_old').val();
        // labor_impact = labor_impact * qty;
        // labor_impact = parseFloat(labor_impact).toFixed(2);

        // if(labor_changed == 0)
        // {
        // 	$('#products_table').find(`[data-id='${row_id}']`).find('.labor_impact').val(labor_impact.replace(/\./g, ','));
        // }

        var price_before_labor = $('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor_old').val();
        // price_before_labor = price_before_labor * qty;
        // price_before_labor = parseFloat(price_before_labor).toFixed(2);

        if(isNaN(price_before_labor))
        {
            price_before_labor = 0;
            // $('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val(price_before_labor);
        }
        // else
        // {
        // 	$('#products_table').find(`[data-id='${row_id}']`).find('.price_before_labor').val(price_before_labor.replace(/\./g, ','));
        // }

        // if(qty_changed == 0)
        // {
        // 	var old_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val();
        // 	old_discount = old_discount * qty;
        // 	// old_discount = old_discount.replace(/\,/g, '.');
        // 	// old_discount = parseFloat(old_discount).toFixed(2);

        // 	rate = rate - old_discount;

        // 	var discount_option = $('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_option_values').val();
        // 	var discount = $('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val();
        // 	// var labor_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.labor-discount-box').find('.labor_discount_values').val();

        // 	if(!discount)
        // 	{
        // 		discount = 0;
        // 	}

        // 	// if(!labor_discount)
        // 	// {
        // 	// 	labor_discount = 0;
        // 	// }

        // 	if(discount_option == 1)
        // 	{
        // 		var discount_val = discount;
        // 	}
        // 	else
        // 	{
        // 		var discount_val = parseFloat(rate) * (discount/100);
        // 	}
            
        // 	// var labor_discount_val = parseFloat(labor_impact) * (labor_discount/100);

        // 	// var total_discount = discount_val + labor_discount_val;
        // 	var total_discount = discount_val;

        // 	if(isNaN(total_discount))
        // 	{
        // 		total_discount = 0;
        // 	}

        // 	total_discount = parseFloat(total_discount).toFixed(2);

        // 	var old_discount = total_discount / qty;
        // 	old_discount = parseFloat(old_discount).toFixed(2);

        // 	if(isNaN(old_discount))
        // 	{
        // 		old_discount = 0;
        // 	}

        // 	$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val('-' + total_discount.replace(/\./g, ','));
        // 	$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val('-' + old_discount);

        // 	rate = parseFloat(rate) - parseFloat(total_discount);
        // 	var price = rate / qty;

        // 	if(isNaN(price))
        // 	{
        // 		price = 0;
        // 	}

        // 	price = parseFloat(price).toFixed(2);

        // 	if(qty != 0)
        // 	{
        // 		$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(price);
        // 	}

        // }
        // else
        // {
        // 	var price = rate / qty;

        // 	if(isNaN(price))
        // 	{
        // 		price = 0;
        // 	}

        // 	price = parseFloat(price).toFixed(2);

        // 	if(qty != 0)
        // 	{
        // 		$('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(price);
        // 	}

        // 	var old_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val();
        // 	old_discount = old_discount * qty;
        // 	old_discount = parseFloat(old_discount).toFixed(2);

        // 	if(isNaN(old_discount))
        // 	{
        // 		old_discount = 0;
        // 	}

        // 	$('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val(old_discount.replace(/\./g, ','));
        // }

        // var old_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val();
        // old_discount = old_discount * qty;
        // old_discount = old_discount.replace(/\,/g, '.');
        // old_discount = parseFloat(old_discount).toFixed(2);

        // rate = rate - old_discount;

        var discount_option = $('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_option_values').val();
        var discount = $('#products_table').find(`[data-id='${row_id}']`).find('.discount-box').find('.discount_values').val();
        // var labor_discount = $('#products_table').find(`[data-id='${row_id}']`).find('.labor-discount-box').find('.labor_discount_values').val();

        if (discount.slice(discount.length - 1) == ',') {
            discount = discount + '00';
        }

        discount = discount != '' ? discount.replace(/\,/g, '.') : '';

        if (!discount || discount == "-") {
            discount = 0;
        }

        discount = parseFloat(discount).toFixed(2);

        // if(!labor_discount)
        // {
        // 	labor_discount = 0;
        // }

        rate = price_before_labor * (qty < 0 ? qty * -1 : qty);
        
        if(discount_option == 1)
        {
            var discount_val = discount;
        }
        else
        {
            var discount_val = parseFloat(rate) * (discount/100);
            // discount_val = discount_val < 0 ? discount_val * -1 : discount_val;
        }

            
        // var labor_discount_val = parseFloat(labor_impact) * (labor_discount/100);

        // var total_discount = discount_val + labor_discount_val;
        var total_discount = discount_val;

        if(isNaN(total_discount))
        {
            total_discount = 0;
        }

        var discount_disp = total_discount * -1;
        discount_disp = parseFloat(discount_disp).toFixed(2);
        total_discount = parseFloat(total_discount).toFixed(2);

        $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount').val(discount_disp.replace(/\./g, ','));

        if(qty != 0)
        {
            var old_discount = discount_disp / qty;

            if(isNaN(old_discount))
            {
                old_discount = 0;
            }

            old_discount = parseFloat(old_discount).toFixed(2);
            $('#products_table').find(`[data-id='${row_id}']`).find('.total_discount_old').val(old_discount);
        }

        var actual_total = price_before_labor * qty;
        rate = parseFloat(actual_total) - parseFloat(total_discount);

        rate = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: false
        }).format(rate); // to round it off into 2 decimal places correctly

        if(isNaN(rate))
        {
            rate = 0;
        }

        var total_org = rate;
        var vat = vat_percentage == 0 ? 0 : vat_percentage/100;
        var net_amount = total_org/(1 + vat);
        net_amount = parseFloat(net_amount.toFixed(2));
        var vat = total_org - net_amount;
        vat = parseFloat(vat.toFixed(2));
        total_net_amount = total_net_amount + net_amount;
        vat_total = vat_total + vat;
        var rows_total = parseFloat(rate);

        var vat_flag = 0;

        vat_data.filter(function(element,index,self){
            if(element["percentage"] == vat_percentage)
            {
                element["tax"] = element["tax"] + vat;
                element["rows_total"] = element["rows_total"] + rows_total;
                vat_flag = 1;
            }
        });

        if(!vat_flag)
        {
            vat_data[vat_index] = {"percentage":vat_percentage,"tax":vat,"rows_total":rows_total};
            vat_index++;
        }
        
        var price = rate / qty;

        if(isNaN(price))
        {
            price = 0;
        }

        price = parseFloat(price).toFixed(2);

        if(qty != 0)
        {
            $('#products_table').find(`[data-id='${row_id}']`).find('#row_total').val(price);
        }

        total = parseFloat(total) + parseFloat(rate);
        total = total.toFixed(2);

        $(this).parent().find('#rate').val(rate);
        $('#products_table').find(`[data-id='${row_id}']`).find('.price').text('€ ' + new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(rate));

        var art = price_before_labor ? price_before_labor : 0;
        price_before_labor_total = parseFloat(price_before_labor_total) + parseFloat(art);
        price_before_labor_total = parseFloat(price_before_labor_total).toFixed(2);

        // var arb = labor_impact;
        // labor_cost_total = parseFloat(labor_cost_total) + parseFloat(arb);
        // labor_cost_total = parseFloat(labor_cost_total).toFixed(2);

    });

    if(isNaN(total))
    {
        total = 0;
    }

    total_net_amount = parseFloat(total_net_amount).toFixed(2);
    vat_total = parseFloat(vat_total).toFixed(2);

    // var net_amount = (total / 121) * 100;
    // net_amount = parseFloat(net_amount).toFixed(2);

    // var tax_amount = total - net_amount;
    // tax_amount = parseFloat(tax_amount).toFixed(2);

    $('#total_amount').val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(total));
    $('#price_before_labor_total').val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(price_before_labor_total));
    // $('#labor_cost_total').val(labor_cost_total.replace(/\./g, ','));
    $('#net_amount').val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(total_net_amount));
    $('#tax_amount').val(new Intl.NumberFormat('nl-NL',{minimumFractionDigits: 2,maximumFractionDigits: 2}).format(vat_total));

    $(".dynamic_tax_boxes").remove();
    vat_data.filter(function(element,index,self){
        var over = element["rows_total"] - element["tax"];
        $("#tax_box").after('<div class="dynamic_tax_boxes" style="display: flex;justify-content: flex-end;margin-top: 20px;">\n' +
        '<div class="headings1" style="width: 70%;"></div>\n' +
        '<div class="headings2" style="width: 30%;">\n' +
        '	<div style="display: flex;align-items: center;justify-content: flex-end;width: 100%;">\n' +
        '		<span style="font-size: 14px;font-weight: 500;font-family: monospace;">BTW '+ (element["percentage"] != 0 ? '('+element["percentage"]+'%) ' : '') + (element["percentage"] == 0 ? 'verlegd over ' : 'over ') + over.toFixed(2).replace(/\./g, ',') +':  €</span>\n' +
        '		<input style="border: 0;font-size: 14px;font-weight: 500;width: 75px;outline: none;text-align: right;" type="text" readonly="" value="'+element["tax"].toFixed(2).replace(/\./g, ',')+'">\n' +
        '	</div>\n' +
        '</div></div>');
    });

    $("#taxes_json").val(vat_data.length ? JSON.stringify(vat_data) : null);

    payment_calculations(1);
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(function() {
        AutoSave();
    }, 2000); // Autosave after 5 second of inactivity
}