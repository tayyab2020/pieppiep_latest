@if(isset($invoice))

    @foreach($invoice as $i => $key)

        <section @if($i == 0) class="attributes_table active" @else class="attributes_table" @endif data-id="{{$i+1}}" style="width: 100%;">

            <div class="header-div">
                <div class="headings" style="width: 22%;">{{__('text.Description')}}</div>
                <div class="headings" style="width: 10%;">{{__('text.Width')}}</div>
                <div class="headings" style="width: 10%;">{{__('text.Height')}}</div>
                <div class="headings m2_box" @if($key->measure == 'M1') style="width: 20%;display: none;" @else style="width: 20%;" @endif>{{__('text.Total')}}</div>
                <div class="headings" style="width: 10%;">{{__('text.Cutting lose')}}</div>
                <div class="headings m2_box" @if($key->measure == 'M1') style="width: 20%;display: none;" @else style="width: 20%;" @endif>{{__('text.Total')}}*</div>
                <div class="headings m1_box" @if($key->measure == 'M1') style="width: 10%;" @else style="width: 10%;display: none;" @endif>{{__('text.Turn')}}</div>
                <div class="headings m1_box" @if($key->measure == 'M1') style="width: 10%;" @else style="width: 10%;display: none;" @endif>{{__('text.Max Width')}}</div>
                <div class="headings m1_box" @if($key->measure == 'M1') style="width: 20%;" @else style="width: 20%;display: none;" @endif>{{__('text.Total')}}</div>
                <div class="headings" style="width: 8%;"></div>
            </div>

            @foreach($key->calculations as $c => $temp)

                <div @if($key->measure == 'Per Piece') style="display: none;" @endif class="attribute-content-div" data-id="{{$temp->calculator_row}}" data-main-id="{{$temp->parent_row ? $temp->parent_row : 0}}">

                    <div class="attribute full-res item1" style="width: 22%;">
                        <div style="display: flex;align-items: center;height: 100%;">
                            <input type="hidden" class="calculator_row" name="calculator_row{{$i+1}}[]" value="{{$temp->calculator_row}}">
                            <span style="width: 10%">{{$temp->calculator_row}}</span>
                            <div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description{{$i+1}}[]">{{$temp->description}}</textarea></div>
                        </div>
                    </div>

                    <div @if($key->measure == 'M1' && $temp->parent_row == NULL && $temp->width && $temp->height && $temp->cutting_lose) class="attribute item2 width-box up-box" @else class="attribute item2 width-box" @endif style="width: 10%;">

                        <div style="flex-wrap: wrap;" class="m-box">
                            <input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif @if($key->measure == 'M1' && $temp->parent_row == NULL && $temp->turn == 1 && $temp->width != NULL) style="border: 1px solid #ccc;background-color: rgb(144, 238, 144);" @else style="border: 1px solid #ccc;" @endif {{$temp->parent_row != NULL ? 'readonly' : null}} value="{{$temp->width ? str_replace('.', ',',floatval($temp->width)) : NULL}}" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width{{$i+1}}[]" type="text">
                            <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="width_unit1[]" class="measure-unit">
                            
                            @if($temp->parent_row == NULL)

                                <div class="m1-wbox">
                                    <span class="m1-w">{{$temp->width ? str_replace('.', ',',floatval($temp->width + $temp->cutting_lose)) : NULL}}</span>
                                    <span class="m1-x">X</span>
                                </div>

                            @endif

                        </div>

                    </div>

                    <div @if($key->measure == 'M1' && $temp->parent_row == NULL && $temp->width && $temp->height && $temp->cutting_lose) class="attribute item3 height-box up-box" @else class="attribute item3 height-box" @endif style="width: 10%;">

                        <div style="flex-wrap: wrap;" class="m-box">
                            <input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif @if($key->measure == 'M1' && $temp->parent_row == NULL && $temp->turn == 0 && $temp->height != NULL) style="border: 1px solid #ccc;background-color: rgb(144, 238, 144);" @else style="border: 1px solid #ccc;" @endif {{$temp->parent_row != NULL ? 'readonly' : null}} value="{{$temp->height ? str_replace('.', ',',floatval($temp->height)) : NULL}}" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height{{$i+1}}[]" type="text">
                            <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="height_unit1[]" class="measure-unit">

                            @if($temp->parent_row == NULL)

                                <div class="m1-hbox">
                                    <span class="m1-h">{{$temp->height ? str_replace('.', ',',floatval($temp->height + $temp->cutting_lose)) : NULL}}</span>
                                </div>

                            @endif
                            
                        </div>

                    </div>

                    <div class="attribute item5 m2_box" @if($key->measure == 'M1') style="width: 20%;display: none;" @else style="width: 20%;" @endif>

                        <div class="m-box">
                            <input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif value="{{$temp->total_boxes ? str_replace('.', ',',floatval($temp->total_boxes)) : null}}" class="form-control total_boxes m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" autocomplete="off" name="total_boxes{{$i+1}}[]" maskedformat="9,1" type="text">
                            <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                        </div>

                    </div>

                    <div class="attribute item4" style="width: 10%;">

                        <div class="m-box">
                            <input @if((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) readonly @endif {{$temp->parent_row != NULL ? 'readonly' : null}} value="{{$temp->cutting_lose}}" class="form-control cutting_lose_percentage m-input" id="cutting_lose_percentage" style="border: 1px solid #ccc;" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage{{$i+1}}[]" type="text">
                        </div>

                    </div>

                    <div class="attribute m2_box" @if($key->measure == 'M1') style="width: 20%;display: none;" @else style="width: 20%;" @endif>

                        <div class="m-box">
                            <input readonly value="{{$temp->total_inc_cutting ? str_replace('.', ',',floatval($temp->total_inc_cutting)) : null}}" class="form-control total_inc_cuttinglose m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" name="total_inc_cuttinglose{{$i+1}}[]" type="text">
                            <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                            <input value="{{str_replace('.', ',',$temp->box_quantity_supplier)}}" class="box_quantity_supplier" name="box_quantity_supplier{{$i+1}}[]" type="hidden">
                        </div>

                    </div>

                    <div class="attribute item5 m1_box" @if($key->measure == 'M1') style="width: 10%;" @else style="width: 10%;display: none;" @endif>

                        <div class="m-box">
                            <select style="border-radius: 5px;width: 70%;height: 30px;" class="form-control turn" {{((Route::currentRouteName() == 'view-new-quotation') && (isset($invoice) && ($invoice[0]->finished == 1))) || $temp->parent_row != NULL ? 'readonly' : null}} name="turn{{$i+1}}[]">
                                <option {{$temp->turn == 0 ? 'selected' : null}} {{$temp->parent_row != NULL && $temp->turn == 1 ? 'disabled' : null}} value="0">{{__('text.No')}}</option>
                                <option {{$temp->turn == 1 ? 'selected' : null}} {{$temp->parent_row != NULL && $temp->turn == 0 ? 'disabled' : null}} value="1">{{__('text.Yes')}}</option>
                            </select>
                        </div>

                    </div>

                    <div class="attribute item6 m1_box" @if($key->measure == 'M1') style="width: 10%;" @else style="width: 10%;display: none;" @endif>

                        <div class="m-box">
                            <input type="number" value="{{$temp->max_width}}" name="max_width{{$i+1}}[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">
                        </div>

                    </div>

                    <div class="attribute item7 m1_box" @if($key->measure == 'M1') style="width: 20%;" @else style="width: 20%;display: none;" @endif>

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',$temp->box_quantity)}}" type="text" name="box_quantity{{$i+1}}[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">
                        </div>

                    </div>

                    <div class="attribute item8 last-content" style="padding: 0;width: 8%;">
                        <div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;height: 100%;">

                            @if(((Route::currentRouteName() != 'view-new-quotation') || (isset($invoice) && ($invoice[0]->finished == 0))) && $temp->parent_row == NULL)

                                <span id="next-row-span" class="tooltip1 add-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                                    <i id="next-row-icon" class="fa fa-fw fa-plus"></i>
                                    <span class="tooltiptext">{{__('text.Add')}}</span>
                                </span>

                                <span id="next-row-span" class="tooltip1 remove-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                                    <i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
                                    <span class="tooltiptext">{{__('text.Remove')}}</span>
                                </span>

                                <span id="next-row-span" class="tooltip1 copy-attribute-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">
                                    <i id="next-row-icon" class="fa fa-fw fa-copy"></i>
                                    <span class="tooltiptext">{{__('text.Copy')}}</span>
                                </span>

                            @endif

                        </div>
                    </div>

                </div>

            @endforeach

            <div @if($key->measure == 'M1') style="display: none;" @endif class="m2_totals">

                <div style="display: flex;margin: 10px 0;">

                    <div style="width: 32%;"></div>

                    <div style="width: 10%;display: flex;align-items: center;font-weight: bold;padding-right: 10px;">{{__("text.Grand Totaal")}}</div>

                    <div style="width: 20%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',$key->grand_totaal)}}" readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal m-input" name="grand_totaal[]" type="text">
                        </div>

                    </div>

                    <div style="width: 10%;display: flex;align-items: center;font-weight: bold;padding-right: 10px;">{{__("text.Grand Totaal*")}}</div>

                    <div style="width: 20%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',$key->grand_totaal_st)}}" readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal_st m-input" name="grand_totaal_st[]" type="text">
                        </div>

                    </div>

                    <div style="width: 8%;"></div>

                </div>
            
                <div style="display: flex;margin: 20px 0;">

                    <div style="width: 62%;"></div>

                    <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">{{__("text.Box quantity")}}</div>

                    <div style="width: 20%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',$key->box_quantity)}}" readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control box_qty_total m-input" name="box_qty_total[]" type="text">
                        </div>

                    </div>

                    <div style="width: 8%;"></div>

                </div>

                <div style="display: flex;margin: 10px 0;">

                    <div style="width: 62%;"></div>

                    <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">{{__("text.Total boxes")}}</div>

                    <div style="width: 20%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',$key->total_boxes_total)}}" readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes_total m-input" name="total_boxes_total[]" type="text">
                        </div>

                    </div>

                    <div style="width: 8%;"></div>

                </div>

            </div>

        </section>

    @endforeach

@else

    <section class="attributes_table active" data-id="1" style="width: 100%;">

        <div class="header-div">
            <div class="headings" style="width: 22%;">{{__('text.Description')}}</div>
            <div class="headings" style="width: 10%;">{{__('text.Width')}}</div>
            <div class="headings" style="width: 10%;">{{__('text.Height')}}</div>
            <div class="headings m2_box" style="width: 20%;">{{__('text.Total')}}</div>
            <div class="headings" style="width: 10%;">{{__('text.Cutting lose')}}</div>
            <div class="headings m2_box" style="width: 20%;">{{__('text.Total')}}*</div>
            <div class="headings m1_box" style="width: 10%;display: none;">{{__('text.Turn')}}</div>
            <div class="headings m1_box" style="width: 10%;display: none;">{{__('text.Max Width')}}</div>
            <div class="headings m1_box" style="width: 20%;display: none;">{{__('text.Total')}}</div>
            <div class="headings" style="width: 8%;"></div>
        </div>

        @if($quote && (count($quote->dimensions) > 0))

            @foreach($quote->dimensions as $d => $key)

                <div class="attribute-content-div" data-id="{{$d+1}}" data-main-id="0">

                    <div class="attribute full-res item1" style="width: 22%;">
                        <div style="display: flex;align-items: center;height: 100%;">
                            <input type="hidden" class="calculator_row" name="calculator_row1[]" value="{{$d+1}}">
                            <span style="width: 10%">{{$d+1}}</span>
                            <div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description1[]">{{$key->description}}</textarea></div>
                        </div>
                    </div>

                    <div class="attribute item2 width-box" style="width: 10%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',floatval($key->width))}}" style="border: 1px solid #ccc;" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width1[]" type="text">
                            <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="width_unit1[]" class="measure-unit">
                        </div>

                    </div>

                    <div class="attribute item3 height-box" style="width: 10%;">

                        <div class="m-box">
                            <input value="{{str_replace('.', ',',floatval($key->height))}}" style="border: 1px solid #ccc;" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height1[]" type="text">
                            <input style="border: 0;outline: none;" value="cm" readonly="" type="text" name="height_unit1[]" class="measure-unit">
                        </div>

                    </div>

                    <div class="attribute item5 m2_box" style="width: 20%;">

                        <div class="m-box">
                            <input class="form-control total_boxes m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" autocomplete="off" name="total_boxes1[]" maskedformat="9,1" type="text">
                            <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                        </div>

                    </div>

                    <div class="attribute item4" style="width: 10%;">

                        <div class="m-box">
                            <input class="form-control cutting_lose_percentage m-input" id="cutting_lose_percentage" style="border: 1px solid #ccc;" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage1[]" type="text">
                        </div>

                    </div>

                    <div class="attribute m2_box" style="width: 20%;">

                        <div class="m-box">
                            <input readonly class="form-control total_inc_cuttinglose m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" name="total_inc_cuttinglose1[]" type="text">
                            <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                            <input class="box_quantity_supplier" name="box_quantity_supplier1[]" type="hidden">
                        </div>

                    </div>

                    <div class="attribute item5 m1_box" style="width: 10%;display: none;">

                        <div class="m-box">
                            <select style="border-radius: 5px;width: 70%;height: 30px;" class="form-control turn" name="turn1[]">
                                <option value="0">{{__('text.No')}}</option>
                                <option value="1">{{__('text.Yes')}}</option>
                            </select>
                        </div>

                    </div>

                    <div class="attribute item6 m1_box" style="width: 10%;display: none;">

                        <div class="m-box">
                            <input type="number" name="max_width1[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">
                        </div>

                    </div>

                    <div class="attribute item7 m1_box" style="width: 20%;display: none;">

                        <div class="m-box">
                            <input type="text" name="box_quantity1[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">
                        </div>

                    </div>

                    <div class="attribute item8 last-content" style="padding: 0;width: 8%;">

                        <div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;height: 100%;">

                            <span id="next-row-span" class="tooltip1 add-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                                <i id="next-row-icon" class="fa fa-fw fa-plus"></i>
                                <span class="tooltiptext">{{__('text.Add')}}</span>
                            </span>	

                            <span id="next-row-span" class="tooltip1 remove-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                                <i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
                                <span class="tooltiptext">{{__('text.Remove')}}</span>
                            </span>

                            <span id="next-row-span" class="tooltip1 copy-attribute-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">
                                <i id="next-row-icon" class="fa fa-fw fa-copy"></i>
                                <span class="tooltiptext">{{__('text.Copy')}}</span>
                            </span>

                        </div>
                    </div>

                </div>

            @endforeach

        @else

            <div class="attribute-content-div" data-id="1" data-main-id="0">

                <div class="attribute full-res item1" style="width: 22%;">
                    <div style="display: flex;align-items: center;height: 100%;">
                        <input type="hidden" class="calculator_row" name="calculator_row1[]" value="1">
                        <span style="width: 10%">1</span>
                        <div style="width: 90%;"><textarea class="form-control attribute_description" style="width: 90%;border-radius: 7px;resize: vertical;height: 40px;outline: none;" name="attribute_description1[]"></textarea></div>
                    </div>
                </div>

                <div class="attribute item2 width-box" style="width: 10%;">

                    <div style="flex-wrap: wrap;" class="m-box">
                        <input style="border: 1px solid #ccc;" id="width" class="form-control width m-input" maskedformat="9,1" autocomplete="off" name="width1[]" type="text">
                        <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="width_unit1[]" class="measure-unit">
                        <div class="m1-wbox">
                            <span class="m1-w"></span>
                            <span class="m1-x">X</span>
                        </div>
                    </div>

                </div>

                <div class="attribute item3 height-box" style="width: 10%;">

                    <div style="flex-wrap: wrap;" class="m-box">
                        <input style="border: 1px solid #ccc;" id="height" class="form-control height m-input" maskedformat="9,1" autocomplete="off" name="height1[]" type="text">
                        <input style="border: 0;outline: none;width: 30%;" value="cm" readonly="" type="text" name="height_unit1[]" class="measure-unit">
                        <div class="m1-hbox">
                            <span class="m1-h"></span>
                        </div>
                    </div>

                </div>

                <div class="attribute item5 m2_box" style="width: 20%;">

                    <div class="m-box">
                        <input class="form-control total_boxes m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" autocomplete="off" name="total_boxes1[]" maskedformat="9,1" type="text">
                        <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                    </div>

                </div>

                <div class="attribute item4" style="width: 10%;">

                    <div class="m-box">
                        <input class="form-control cutting_lose_percentage m-input" id="cutting_lose_percentage" style="border: 1px solid #ccc;" maskedformat="9,1" autocomplete="off" name="cutting_lose_percentage1[]" type="text">
                    </div>

                </div>

                <div class="attribute m2_box" style="width: 20%;">

                        <div class="m-box">
                            <input readonly class="form-control total_inc_cuttinglose m-input" style="background: transparent;border: 1px solid #ccc;width: 85%;" name="total_inc_cuttinglose1[]" type="text">
                            <input style="border: 0;outline: none;width: 15%;" readonly="" type="text" class="measure-unit">
                            <input class="box_quantity_supplier" name="box_quantity_supplier1[]" type="hidden">
                        </div>

                    </div>

                <div class="attribute item5 m1_box" style="width: 10%;display: none;">

                    <div class="m-box">
                        <select style="border-radius: 5px;width: 70%;height: 30px;" class="form-control turn" name="turn1[]">
                            <option value="0">{{__('text.No')}}</option>
                            <option value="1">{{__('text.Yes')}}</option>
                        </select>
                    </div>

                </div>

                <div class="attribute item6 m1_box" style="width: 10%;display: none;">

                    <div class="m-box">
                        <input type="number" name="max_width1[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control max_width res-white m-input">
                    </div>

                </div>

                <div class="attribute item7 m1_box" style="width: 20%;display: none;">

                    <div class="m-box">
                        <input type="text" name="box_quantity1[]" readonly style="border: 1px solid #ccc;background: transparent;" class="form-control box_quantity res-white m-input">
                    </div>

                </div>

                <div class="attribute item8 last-content" style="padding: 0;width: 8%;">

                    <div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;height: 100%;">

                        <span id="next-row-span" class="tooltip1 add-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                            <i id="next-row-icon" class="fa fa-fw fa-plus"></i>
                            <span class="tooltiptext">{{__('text.Add')}}</span>
                        </span>

                        <span id="next-row-span" class="tooltip1 remove-attribute-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                            <i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
                            <span class="tooltiptext">{{__('text.Remove')}}</span>
                        </span>

                        <span id="next-row-span" class="tooltip1 copy-attribute-row" style="cursor: pointer;font-size: 20px;margin: 0 10px;width: 20px;height: 20px;line-height: 20px;">
                            <i id="next-row-icon" class="fa fa-fw fa-copy"></i>
                            <span class="tooltiptext">{{__('text.Copy')}}</span>
                        </span>

                    </div>
                </div>

            </div>

        @endif

        <div class="m2_totals">

            <div style="display: flex;margin: 10px 0;">

                <div style="width: 32%;"></div>

                <div style="width: 10%;display: flex;align-items: center;font-weight: bold;padding-right: 10px;">{{__("text.Grand Totaal")}}</div>

                <div style="width: 20%;">

                    <div class="m-box">
                        <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal m-input" name="grand_totaal[]" type="text">
                    </div>

                </div>

                <div style="width: 10%;display: flex;align-items: center;font-weight: bold;padding-right: 10px;">{{__("text.Grand Totaal*")}}</div>

                <div style="width: 20%;">

                    <div class="m-box">
                        <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control grand_totaal_st m-input" name="grand_totaal_st[]" type="text">
                    </div>

                </div>

                <div style="width: 8%;"></div>

            </div>
            
            <div style="display: flex;margin: 20px 0;">

                <div style="width: 62%;"></div>

                <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">{{__("text.Box quantity")}}</div>

                <div style="width: 20%;">

                    <div class="m-box">
                        <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control box_qty_total m-input" name="box_qty_total[]" type="text">
                    </div>

                </div>

                <div style="width: 8%;"></div>

            </div>

            <div style="display: flex;margin: 10px 0;">

                <div style="width: 62%;"></div>

                <div style="width: 10%;display: flex;align-items: center;font-weight: bold;">{{__("text.Total boxes")}}</div>

                <div style="width: 20%;">

                    <div class="m-box">
                        <input readonly style="border: 1px solid #ccc;background: transparent;width: 85%;" class="form-control total_boxes_total m-input" name="total_boxes_total[]" type="text">
                    </div>

                </div>

                <div style="width: 8%;"></div>

            </div>

        </div>

    </section>

@endif

<script>
	var calculation_config = {
		yes: "{{__('text.Yes')}}",
        no: "{{__('text.No')}}",
        add: "{{__('text.Add')}}",
        remove: "{{__('text.Remove')}}",
        copy: "{{__('text.Copy')}}",
        grand_total: "{{__('text.Grand Totaal')}}",
        grand_total_st: "{{__('text.Grand Totaal*')}}",
        box_quantity: "{{__('text.Box quantity')}}",
        total_boxes: "{{__('text.Total boxes')}}"
    };
</script>
<script src="{{asset('assets/front/js/total_calculations.js?v=2.1')}}"></script>
<script src="{{asset('assets/front/js/calculations.js')}}"></script>

<style>
    
    .up-box
	{
		padding-top: 25px;
	}

	.m1-wbox, .m1-hbox
	{
		width: 100%;
		font-size: 16px;
		font-weight: bold;
		display: none;
	}

	.up-box .m1-wbox, .up-box .m1-hbox
	{
		display: flex;
	}

	.m1-w, .m1-h
	{
		width: 70%;
		text-align: center;
	}

	.m1-x
	{
		width: 30%;
		text-align: center;
	}
    
</style>