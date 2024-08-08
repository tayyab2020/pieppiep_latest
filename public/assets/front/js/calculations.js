function add_attribute_row(copy = false, product_row, menu2 = null, turn = 0, row_id = null) {

    var check_length = $(`.attributes_table[data-id='${product_row}']`).find('.attribute-content-div[data-main-id="0"]').length;

    if(check_length == 0)
    {
        var rowCount = 1;
    }
    else
    {
        var rowCount = $(`.attributes_table[data-id='${product_row}']`).find('.attribute-content-div[data-main-id="0"]:last').data('id');
        rowCount = rowCount + 1;
    }

    if (!copy) {

        var box_quantity = $('#products_table').find(`[data-id='${product_row}']`).find('#estimated_price_quantity').val();
        box_quantity = box_quantity != '' ? box_quantity.replace(/\./g, ',') : '';
        var max_width = $('#products_table').find(`[data-id='${product_row}']`).find('#max_width').val();
        var measure = $('#products_table').find(`[data-id='${product_row}']`).find('#measure').val();

        var check_m2_totals_length = $(`.attributes_table[data-id='${product_row}']`).find('.m2_totals').length;

        if(check_m2_totals_length == 0)
        {
            $(`.attributes_table[data-id='${product_row}']`).append((measure == "M1" ? '<div class="m2_totals" style="display: none;">\n' : '<div class="m2_totals">\n') +
            '                                                         <div style="display: flex;margin: 10px 0;">\n' +
            '\n' +
            '                                                            <div style="width: 32%;"></div>\n' +
            '\n' +
            '                                                            <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">'+calculation_config.grand_total+'</div>\n' +
            '\n' +
            '                       									 <div style="width: 20%;">\n' +
            '\n' +
            '                                                            	<div class="m-box">\n' +
            '\n' +
            '                                                               	<input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal m-input" name="grand_totaal[]" type="text">\n' +
            '\n' +
            '                                                               </div>\n' +
            '\n' +
            '                                                            </div>\n' +
            '\n' +
            '                                                            <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">'+calculation_config.grand_total_st+'</div>\n' +
            '\n' +
            '                       									 <div style="width: 20%;">\n' +
            '\n' +
            '                                                            	<div class="m-box">\n' +
            '\n' +
            '                                                               	<input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal_st m-input" name="grand_totaal_st[]" type="text">\n' +
            '\n' +
            '                                                               </div>\n' +
            '\n' +
            '                                                            </div>\n' +
            '\n' +
            '                                                            <div style="width: 8%;"></div>\n' +
            '\n' +
            '														  </div>\n' +
            '\n' +
            '														  <div style="display: flex;margin: 20px 0;">\n' +
            '\n' +
            '                                                            <div style="width: 62%;"></div>\n' +
            '\n' +
            '                                                            <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">'+calculation_config.box_quantity+'</div>\n' +
            '\n' +
            '                       									 <div style="width: 20%;">\n' +
            '\n' +
            '                                                            	<div class="m-box">\n' +
            '\n' +
            '                                                               	<input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control box_qty_total m-input" name="box_qty_total[]" type="text">\n' +
            '\n' +
            '                                                               </div>\n' +
            '\n' +
            '                                                            </div>\n' +
            '\n' +
            '                                                            <div style="width: 8%;"></div>\n' +
            '\n' +
            '														  </div>\n' +
            '\n' +
            '                                                         <div style="display: flex;margin: 10px 0;" class="attribute-total-boxes-div">\n' +
            '\n' +
            '                                                            <div style="width: 62%;"></div>\n' +
            '\n' +
            '                                                            <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">'+calculation_config.total_boxes+'</div>\n' +
            '\n' +
            '                       									 <div style="width: 20%;">\n' +
            '\n' +
            '                                                            	<div class="m-box">\n' +
            '\n' +
            '                                                               	<input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes_total m-input" name="total_boxes_total[]" type="text">\n' +
            '\n' +
            '                                                               </div>\n' +
            '\n' +
            '                                                            </div>\n' +
            '\n' +
            '                                                            <div style="width: 8%;"></div>\n' +
            '\n' +
            '														  </div>\n' +
            '\n' +
            '</div>\n');
        }

        $(`.attributes_table[data-id='${product_row}']`).find('.m2_totals').before((measure == "Per Piece" ? '<div style="display: none;" class="attribute-content-div" data-id="' + rowCount + '" data-main-id="0">\n' : '<div class="attribute-content-div" data-id="' + rowCount + '" data-main-id="0">\n') +
                '\n' +
                '                                                            <div class="attribute full-res item1" style="width: 22%;">\n' +
                '                       									 	<div style="display: flex;align-items: center;height: 100%;"><input type="hidden" class="calculator_row" name="calculator_row'+product_row+'[]" value="'+rowCount+'"><span style="width: 10%">'+rowCount+'</span><div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description'+product_row+'[]"></textarea></div></div>\n' +
                '                       									 </div>\n' +
                '\n' +
                '                                                            <div class="attribute item2 width-box" style="width: 10%;">\n' +
                '\n' +
                '                       									 	<div style="flex-wrap: wrap;" class="m-box">\n' +
                '\n' +
                '                                                                <input style="border: 1px solid #ccc;" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width'+product_row+'[]" type="text">\n' +
                '\n' +
                '                                                                <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="width_unit'+product_row+'[]" class="measure-unit">\n' +
                '\n' +
                '                                                                <div class="m1-wbox">\n' +
                '\n' +
                '                                                                	<span class="m1-w"></span>\n' +
                '                                                                	<span class="m1-x">X</span>\n' +
                '\n' +
                '                                                                </div>\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div class="attribute item3 height-box" style="width: 10%;">\n' +
                '\n' +
                '                       									 	<div style="flex-wrap: wrap;" class="m-box">\n' +
                '\n' +
                '                                                                <input style="border: 1px solid #ccc;" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height'+product_row+'[]" type="text">\n' +
                '\n' +
                '                                                                <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="height_unit'+product_row+'[]" class="measure-unit">\n' +
                '\n' +
                '                                                                <div class="m1-hbox">\n' +
                '\n' +
                '                                                                	<span class="m1-h"></span>\n' +
                '\n' +
                '                                                                </div>\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                (measure == "M1" ? '<div class="attribute item5 m2_box" style="width: 20%;display: none;">\n' : '<div class="attribute item5 m2_box" style="width: 20%;">\n') +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <input style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes m-input" autocomplete="off" name="total_boxes'+product_row+'[]" maskedformat="9,1" type="text">\n' +
                '\n' +
                '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div class="attribute item4" style="width: 10%;">\n' +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <input style="border: 1px solid #ccc;" id="cutting_lose_percentage" class="form-control cutting_lose_percentage m-input" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage'+product_row+'[]" type="text">\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                (measure == "M1" ? '<div class="attribute m2_box" style="width: 20%;display: none;">\n' : '<div class="attribute item5 m2_box" style="width: 20%;">\n') +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_inc_cuttinglose m-input" name="total_inc_cuttinglose'+product_row+'[]" type="text">\n' +
                '\n' +
                '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                '\n' +
                '                                                                <input value="'+box_quantity+'" class="box_quantity_supplier" name="box_quantity_supplier'+product_row+'[]" type="hidden">\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                (measure == "M1" ? '<div class="attribute item5 m1_box" style="width: 10%;">\n' : '<div class="attribute item5 m1_box" style="width: 10%;display: none;">\n') +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <select style="border-radius: 5px;width: 70%;height: 30px;" class="form-control turn" name="turn'+product_row+'[]">\n' +
                '\n' +
                '                                                                	<option value="0">'+calculation_config.no+'</option>\n' +
                '                                                                	<option value="1">'+calculation_config.yes+'</option>\n' +
                '\n' +
                '                                                                </select>\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                (measure == "M1" ? '<div class="attribute item6 m1_box" style="width: 10%;">\n' : '<div class="attribute item6 m1_box" style="width: 10%;display: none;">\n') +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <input type="number" name="max_width'+product_row+'[]" value="'+max_width+'" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                (measure == "M1" ? '<div class="attribute item7 m1_box" style="width: 20%;">\n' : '<div class="attribute item7 m1_box" style="width: 20%;display: none;">\n') +
                '\n' +
                '                       									 	<div class="m-box">\n' +
                '\n' +
                '                                                                <input type="text" name="box_quantity'+product_row+'[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">\n' +
                '\n' +
                '                                                               </div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                            <div class="attribute item8 last-content" style="padding: 0;width: 8%;">\n' +
                '\n' +
                '                       									 	<div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;height: 100%;">\n' +
                '\n' +
                '																	<span id="next-row-span" class="tooltip1 add-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
                '\n' +
                '																		<i id="next-row-icon" class="fa fa-fw fa-plus"></i>\n' +
                '\n' +
                '																		<span class="tooltiptext">'+calculation_config.add+'</span>\n' +
                '\n' +
                '																	</span>\n' +
                '\n' +
                '																	<span id="next-row-span" class="tooltip1 remove-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
                '\n' +
                '																		<i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>\n' +
                '\n' +
                '																		<span class="tooltiptext">'+calculation_config.remove+'</span>\n' +
                '\n' +
                '																	</span>\n' +
                '\n' +
                '																	<span id="next-row-span" class="tooltip1 copy-attribute-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">\n' +
                '\n' +
                '																		<i id="next-row-icon" class="fa fa-fw fa-copy"></i>\n' +
                '\n' +
                '																		<span class="tooltiptext">'+calculation_config.copy+'</span>\n' +
                '\n' +
                '																	</span>\n' +
                '\n' +
                '                                                            	</div>\n' +
                '\n' +
                '                                                            </div>\n' +
                '\n' +
                '                                                        </div>');
    }
    else {

        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.m2_totals').before('<div class="attribute-content-div" data-id="'+rowCount+'" data-main-id="0"></div>\n');
        menu2.appendTo(`#menu2 .attributes_table[data-id='${product_row}'] .attribute-content-div[data-id='${rowCount}']`);
        $('#menu2').find(`.attributes_table[data-id='${product_row}'] .attribute-content-div[data-id='${rowCount}']`).find('.calculator_row').val(rowCount);
        $('#menu2').find(`.attributes_table[data-id='${product_row}'] .attribute-content-div[data-id='${rowCount}']`).find('.item1 span').text(rowCount);
        $('#menu2').find(`.attributes_table[data-id='${product_row}'] .attribute-content-div[data-id='${rowCount}']`).find('.turn').val(turn);

        calculator(product_row,rowCount);
        // if($('#menu2').find(`.attributes_table[data-id='${product_row}'] .attribute-content-div[data-main-id='${row_id}']`).length > 0)
        // {
        // 	calculator(product_row,rowCount);
        // }
        // else
        // {
        // 	calculate_qty(product_row);
        // }
    }
}

$(document).on('click', '.add-attribute-row', function () {

    var product_row = $(this).parents('.attributes_table').data('id');

    add_attribute_row(false, product_row);

});

$(document).on('click', '.remove-attribute-row', function () {

    var product_row = $(this).parents('.attributes_table').data('id');
    var row_id = $(this).parents('.attribute-content-div').data('id');
    var rowCount = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.attribute-content-div[data-main-id="0"]').length;
    var current = $(this).parents('.attribute-content-div');

    if (rowCount != 1) {

        $(this).parents('.attributes_table').find('.attribute-content-div[data-main-id="'+row_id+'"]').remove();
        current.remove();
        calculate_qty(product_row);

    }

});

$(document).on('click', '.copy-attribute-row', function () {

    var current = $(this).parents('.attribute-content-div');
    var product_row = current.parents('.attributes_table').data('id');
    var row_id = current.data('id');
    var turn = current.find(".turn").val();
    var menu2 = current.children().clone();

    add_attribute_row(true, product_row, menu2, turn, row_id);

});

$(document).on('keypress', ".cutting_lose_percentage", function (e) {

    e = e || window.event;
    var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
    var val = String.fromCharCode(charCode);

    if (!val.match(/^[0-9]+$/))  // For characters validation
    {
        e.preventDefault();
        return false;
    }

});

$(document).on('focusout', ".width, .height", function (e) {

    if ($(this).val().slice($(this).val().length - 1) == ',') {
        var val = $(this).val();
        val = val + '00';
        $(this).val(val);
    }

});

function calculate_qty(product_row)
{
	var measure = $("#products_table").find(`.content-div[data-id='${product_row}']`).find('#measure').val();
				
	if(measure == "M1")
	{
        var total_qty = 0;
        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-main-id='0']`).find('.box_quantity').each(function (i, obj) {
            var qty = 0;
            if($(obj).val())
            {
                var box_quantity = $(obj).val();
                box_quantity = box_quantity.replace(/\,/g, '.');
                qty = box_quantity;
            }
            
            total_qty = parseFloat(total_qty) + parseFloat(qty);
        });

        total_qty = total_qty.toFixed(2);
        total_qty = total_qty.replace(/\./g, ',');
    }
    else
    {
		var total_qty = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.total_boxes_total').val();
	}

	$("#products_table").find(`.content-div[data-id='${product_row}']`).find('.qty').val(total_qty);

    if(!$("#order_page").val())
    {
        calculate_total();
    }
}

function calculator(product_row, row_id, cutting_change = 0, total_boxes_change = 0) {

    var measure = $("#products_table").find(`.content-div[data-id='${product_row}']`).find('#measure').val();
    var width = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.width').val();
    width = width.replace(/\,/g, '.');
    var height = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.height').val();
    height = height.replace(/\,/g, '.');
    var cutting_lose_percentage = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.cutting_lose_percentage').val();
    var total_quantity = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.total_boxes').val();
    total_quantity = total_quantity != '' ? total_quantity.replace(/\,/g, '.') : '';
    total_quantity = total_quantity == "." ? 0 : total_quantity;

    if (measure == "M1") {
        
        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-main-id='${row_id}']`).remove();
        calculate_qty(product_row);

        if (cutting_lose_percentage) {
            width = parseFloat(width) + parseFloat(cutting_lose_percentage);
            height = parseFloat(height) + parseFloat(cutting_lose_percentage);
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.m1-w').text(width.toFixed(2).replace(/\./g, ','));
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.m1-h').text(height.toFixed(2).replace(/\./g, ','));
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.width-box').addClass("up-box");
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.height-box').addClass("up-box");
        }
        else {
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.m1-w').text("");
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.m1-h').text("");
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.width-box').removeClass("up-box");
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.height-box').removeClass("up-box");
        }

        var org_width = width;
        var org_height = height;
        var retailer_width = width;
        var retailer_height = height;
        var turn = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.turn').val();
        var max_width = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.max_width').val();
        var description = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.attribute_description').val();

        if (!max_width) {
            max_width = 0;
        }

        if (width && height && cutting_lose_percentage) {

            if (turn == 0) {
                if (max_width > parseFloat(width)) {
                    var total_boxes = (parseFloat(height)) / 100;
                    // total_boxes = (Math.round(total_boxes * 10)) / 10;
                    // total_boxes = Math.ceil(parseFloat(total_boxes).toFixed(2));
                    total_boxes = parseFloat(total_boxes).toFixed(2);

                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val(total_boxes.replace(/\./g, ','));
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#width').css('background-color', '');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#height').css('background-color', '#90ee90');

                    var total_rows = 0;
                    var flag = 0;
                }
                else {
                    var flag = 1;

                    if (max_width == 0) {
                        var total_rows = 0;
                    }
                    else {
                        var total_rows = parseFloat(width) / max_width;
                        total_rows = Math.ceil(total_rows);
                    }

                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val('');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#width').css('background-color', '');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#height').css('background-color', '#90ee90');
                }

                var content = '';
                var width_array = [];
                var total_boxes = 0;

                for (i = 0; i <= total_rows; i++) {
                    if (flag) {
                        if (i != total_rows) {
                            var total = (parseFloat(height)) / 100;
                            // total = (Math.round(total * 10)) / 10;
                            total = parseFloat(total).toFixed(2);
                        }
                        else {
                            var total = 0;
                        }

                        total_boxes = total_boxes + parseFloat(total);
                        total = total != '' ? total.replace(/\./g, ',') : '';

                        if (i == 0) {
                            width = max_width;
                            width_array[i] = width;
                        }
                        else {
                            width = parseFloat(org_width) - parseFloat(width_array[width_array.length - 1]);

                            if (width > max_width) {
                                width = max_width
                            }
                            else if (width == 0) {
                                var sum = 0;

                                for (j = 0; j < width_array.length; j++) {

                                    sum = sum + parseFloat(width_array[j]);

                                }

                                width = parseFloat(retailer_width) - parseFloat(sum);
                            }

                            org_width = width_array[width_array.length - 1];
                            width_array[i] = width;
                        }

                        // var rowCount = $(`.attributes_table[data-id='${product_row}']`).find('.attribute-content-div:last').data('id');
                        // rowCount = rowCount + 1;
                    }
                    else {
                        var total = "";
                        width = max_width - width;
                    }

                    width = parseFloat(width).toFixed(2);
                    width = width.replace(/\./g, ',');
                    height = parseFloat(height).toFixed(2);
                    height = height.replace(/\./g, ',');

                    content = content + '<div class="attribute-content-div" data-id="' + row_id + '.' + (i + 1) + '" data-main-id="' + row_id + '">\n' +
                        '\n' +
                        '                                                            <div class="attribute full-res item1" style="width: 22%;">\n' +
                        '                       									 	<div style="display: flex;align-items: center;height: 100%;"><input type="hidden" class="calculator_row" name="calculator_row' + product_row + '[]" value="' + row_id + '.' + (i + 1) + '"><span style="width: 10%">' + row_id + '.' + (i + 1) + '</span><div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description' + product_row + '[]">' + (i != total_rows ? description : 'Restant rol') + '</textarea></div></div>\n' +
                        '                       									 </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item2 width-box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;" readonly value="' + width + '" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="width_unit' + product_row + '[]" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item3 height-box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;" readonly value="' + height + '" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="height_unit' + product_row + '[]" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item5 m2_box" style="width: 20%;display: none;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes m-input" autocomplete="off" name="total_boxes' + product_row + '[]" maskedformat="9,1" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item4" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        (i != total_rows ? '<input style="border: 1px solid #ccc;" readonly value="' + cutting_lose_percentage + '" id="cutting_lose_percentage" class="form-control cutting_lose_percentage m-input" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage' + product_row + '[]" type="text">\n' : '<input style="border: 1px solid #ccc;" readonly id="cutting_lose_percentage" class="form-control cutting_lose_percentage m-input" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage' + product_row + '[]" type="text">\n') +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute m2_box" style="width: 20%;display: none;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_inc_cuttinglose m-input" name="total_inc_cuttinglose' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                        '\n' +
                        '                                                                <input class="box_quantity_supplier" name="box_quantity_supplier' + product_row + '[]" type="hidden">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item5 m1_box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <select style="border-radius: 5px;width: 70%;height: 30px;" readonly class="form-control turn" name="turn' + product_row + '[]">\n' +
                        '\n' +
                        '                                                                	<option value="0">'+calculation_config.no+'</option>\n' +
                        '                                                                	<option disabled value="1">'+calculation_config.yes+'</option>\n' +
                        '\n' +
                        '                                                                </select>\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item6 m1_box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        (i != total_rows ? '<input type="number" value="' + max_width + '" name="max_width' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">\n' : '<input type="number" name="max_width' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">\n') +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item7 m1_box" style="width: 20%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input type="text" value="' + total + '" name="box_quantity' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item8 last-content" style="padding: 0;width: 8%;">\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                        </div>';
                }

                if (flag) {
                    total_boxes = total_boxes.toFixed(2).replace(/\./g, ',');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val(total_boxes);
                }

                $(`.attributes_table[data-id='${product_row}']`).find('.m2_totals').before(content);
            }
            else {
                if (max_width > parseFloat(height)) {
                    var total_boxes = (parseFloat(width)) / 100;
                    // total_boxes = (Math.round(total_boxes * 10)) / 10;
                    // total_boxes = Math.ceil(parseFloat(total_boxes).toFixed(2));
                    total_boxes = parseFloat(total_boxes).toFixed(2);

                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val(total_boxes.replace(/\./g, ','));
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#height').css('background-color', '');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#width').css('background-color', '#90ee90');

                    var total_rows = 0;
                    var flag = 0;
                }
                else {
                    var flag = 1;

                    if (max_width == 0) {
                        var total_rows = 0;
                    }
                    else {
                        var total_rows = parseFloat(height) / max_width;
                        total_rows = Math.ceil(total_rows);
                    }

                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val('');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#height').css('background-color', '');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('#width').css('background-color', '#90ee90');
                }

                var content = '';
                var height_array = [];
                var total_boxes = 0;

                for (i = 0; i <= total_rows; i++) {
                    if (flag) {
                        if (i != total_rows) {
                            var total = (parseFloat(width)) / 100;
                            // total = (Math.round(total * 10)) / 10;
                            total = parseFloat(total).toFixed(2);
                        }
                        else {
                            var total = 0;
                        }

                        total_boxes = total_boxes + parseFloat(total);
                        total = total != '' ? total.replace(/\./g, ',') : '';

                        if (i == 0) {
                            if (parseFloat(retailer_height) < max_width) {
                                height = retailer_height;
                            }
                            else {
                                height = max_width;
                            }
                            height_array[i] = height;
                        }
                        else {
                            if (i == total_rows) {
                                height = max_width - parseFloat(height_array[height_array.length - 1]);
                            }
                            else {
                                height = parseFloat(org_height) - parseFloat(height_array[height_array.length - 1]);

                                if (height > max_width) {
                                    height = max_width
                                }
                                else if (height == 0) {
                                    var sum = 0;

                                    for (j = 0; j < height_array.length; j++) {

                                        sum = sum + parseFloat(height_array[j]);

                                    }

                                    height = parseFloat(retailer_height) - parseFloat(sum);
                                }
                            }

                            org_height = height_array[height_array.length - 1];
                            height_array[i] = height;
                        }

                        // var rowCount = $(`.attributes_table[data-id='${product_row}']`).find('.attribute-content-div:last').data('id');
                        // rowCount = rowCount + 1;
                    }
                    else {
                        var total = "";
                        height = max_width - height;
                    }

                    width = parseFloat(width).toFixed(2);
                    width = width.replace(/\./g, ',');
                    height = parseFloat(height).toFixed(2);
                    height = height.replace(/\./g, ',');

                    content = content + '<div class="attribute-content-div" data-id="' + row_id + '.' + (i + 1) + '" data-main-id="' + row_id + '">\n' +
                        '\n' +
                        '                                                            <div class="attribute full-res item1" style="width: 22%;">\n' +
                        '                       									 	<div style="display: flex;align-items: center;height: 100%;"><input type="hidden" class="calculator_row" name="calculator_row' + product_row + '[]" value="' + row_id + '.' + (i + 1) + '"><span style="width: 10%">' + row_id + '.' + (i + 1) + '</span><div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description' + product_row + '[]">' + (i != total_rows ? description : 'Restant rol') + '</textarea></div></div>\n' +
                        '                       									 </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item2 width-box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;" readonly value="' + width + '" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="width_unit' + product_row + '[]" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item3 height-box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;" readonly value="' + height + '" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="height_unit' + product_row + '[]" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item5 m2_box" style="width: 20%;display: none;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes m-input" autocomplete="off" name="total_boxes' + product_row + '[]" maskedformat="9,1" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item4" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        (i != total_rows ? '<input style="border: 1px solid #ccc;" readonly value="' + cutting_lose_percentage + '" id="cutting_lose_percentage" class="form-control cutting_lose_percentage m-input" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage' + product_row + '[]" type="text">\n' : '<input style="border: 1px solid #ccc;" readonly id="cutting_lose_percentage" class="form-control cutting_lose_percentage m-input" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage' + product_row + '[]" type="text">\n') +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute m2_box" style="width: 20%;display: none;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_inc_cuttinglose m-input" name="total_inc_cuttinglose' + product_row + '[]" type="text">\n' +
                        '\n' +
                        '                                                                <input style="border: 0;outline: none;width: 15%;" readonly type="text" class="measure-unit">\n' +
                        '\n' +
                        '                                                                <input class="box_quantity_supplier" name="box_quantity_supplier' + product_row + '[]" type="hidden">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item5 m1_box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <select style="border-radius: 5px;width: 70%;height: 30px;" readonly class="form-control turn" name="turn' + product_row + '[]">\n' +
                        '\n' +
                        '                                                                	<option disabled value="0">'+calculation_config.no+'</option>\n' +
                        '                                                                	<option value="1">'+calculation_config.no+'</option>\n' +
                        '\n' +
                        '                                                                </select>\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item6 m1_box" style="width: 10%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        (i != total_rows ? '<input type="number" value="' + max_width + '" name="max_width' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">\n' : '<input type="number" name="max_width' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">\n') +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item7 m1_box" style="width: 20%;">\n' +
                        '\n' +
                        '                       									 	<div class="m-box">\n' +
                        '\n' +
                        '                                                                <input type="text" value="' + total + '" name="box_quantity' + product_row + '[]" readonly="" style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">\n' +
                        '\n' +
                        '                                                               </div>\n' +
                        '\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                            <div class="attribute item8 last-content" style="padding: 0;width: 8%;">\n' +
                        '                                                            </div>\n' +
                        '\n' +
                        '                                                        </div>';
                }

                if (flag) {
                    total_boxes = total_boxes.toFixed(2).replace(/\./g, ',');
                    $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val(total_boxes);
                }

                $(`.attributes_table[data-id='${product_row}']`).find('.m2_totals').before(content);
            }
        }
        else {
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val('');
        }
    }
    else {
        var box_quantity = $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity_supplier').val();
        box_quantity = box_quantity != '' ? box_quantity.replace(/\,/g, '.') : '';

        if (!cutting_change && !total_boxes_change) {
            if (width && height) {
                total_quantity = (width / 100) * (height) / 100;
                total_quantity = parseFloat(total_quantity).toFixed(2);
                $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.total_boxes').val(total_quantity.replace(/\./g, ','));
            }
        }
        else if (total_boxes_change) {
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.width').val("");
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.height').val("");
        }

        if (box_quantity && total_quantity) {
            var total_inc_cuttinglose = (total_quantity * (1 + (cutting_lose_percentage / 100)));
            total_inc_cuttinglose = parseFloat(total_inc_cuttinglose).toFixed(2);

            var total_boxes = total_inc_cuttinglose / box_quantity;
            // total_boxes = Math.ceil(parseFloat(total_boxes).toFixed(2));
            total_boxes = parseFloat(total_boxes).toFixed(2);

            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.total_inc_cuttinglose').val(total_inc_cuttinglose != '' ? total_inc_cuttinglose.replace(/\./g, ',') : '');
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val(total_boxes.replace(/\./g, ','));
        }
        else {
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.total_inc_cuttinglose').val('');
            $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find(`.attribute-content-div[data-id='${row_id}']`).find('.box_quantity').val('');
        }

        var total_boxes_total = 0;
        var grand_totaal = 0;
        var grand_totaal_st = 0;

        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.total_boxes').each(function (i, obj) {

            var totaal = $(this).val();
            totaal = totaal == "," ? 0 : totaal;
            totaal = totaal != '' ? parseFloat(totaal.replace(/\,/g, '.')) : '';
            grand_totaal = grand_totaal + totaal;

        });

        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.total_inc_cuttinglose').each(function (i, obj) {

            var totaal_st = $(this).val();
            totaal_st = totaal_st != '' ? parseFloat(totaal_st.replace(/\,/g, '.')) : '';
            grand_totaal_st = grand_totaal_st + totaal_st;

        });

        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.box_quantity').each(function (i, obj) {

            var total_box = $(this).val();
            total_box = total_box != '' ? parseFloat(total_box.replace(/\,/g, '.')) : '';
            total_boxes_total = parseFloat(total_boxes_total) + total_box;

        });

        grand_totaal = parseFloat(grand_totaal).toFixed(2).replace(/\./g, ',');
        grand_totaal_st = parseFloat(grand_totaal_st).toFixed(2).replace(/\./g, ',');
        total_boxes_total = total_boxes_total <= 0.10 ? 0 : Math.ceil(total_boxes_total).toFixed(2).replace(/\./g, ',');
        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.grand_totaal').val(grand_totaal);
        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.grand_totaal_st').val(grand_totaal_st);
        $('#menu2').find(`.attributes_table[data-id='${product_row}']`).find('.total_boxes_total').val(total_boxes_total);
    }

    calculate_qty(product_row);
}

$(document).on('input', ".width, .height", function (e) {

    var current = $(this);
    var product_row = current.parents(".attributes_table").data('id');
    var row_id = current.parents(".attribute-content-div").data('id');

    calculator(product_row, row_id);

});

$(document).on('input', ".cutting_lose_percentage", function (e) {

    var current = $(this);
    var product_row = current.parents(".attributes_table").data('id');
    var row_id = current.parents(".attribute-content-div").data('id');

    calculator(product_row, row_id, 1, 0);

});

$(document).on('input', ".total_boxes", function (e) {

    var current = $(this);
    var product_row = current.parents(".attributes_table").data('id');
    var row_id = current.parents(".attribute-content-div").data('id');

    calculator(product_row, row_id, 0, 1);

});

$(document).on('change', ".turn", function (e) {

    var current = $(this);
    var product_row = current.parents(".attributes_table").data('id');
    var row_id = current.parents(".attribute-content-div").data('id');

    calculator(product_row, row_id);

});