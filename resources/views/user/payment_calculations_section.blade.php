<section class="pc_table" style="width: 100%;">

    <div class="header-div">
        <div class="headings" style="width: 18%;">{{__('text.Percentage')}} (%)</div>
        <div class="headings" style="width: 18%;">{{__('text.Amount')}} (€)</div>
        <div class="headings" style="width: 18%;">{{__('text.Date')}}</div>
        <div class="headings" style="width: 18%;">{{__('text.Paid by')}}</div>
        <div class="headings" style="width: 18%;">{{__('text.Description')}}</div>
        <div class="headings" style="width: 10%;"></div>
    </div>

    @php if(Route::currentRouteName() == 'create-new-negative-invoice' || Route::currentRouteName() == 'view-negative-invoice') { $payment_calculations = $negative_payment_calculations; }elseif(isset($invoice)){ $payment_calculations = $check->payment_calculations; } @endphp

    @if(isset($invoice) && count($payment_calculations) > 0)

        @foreach($payment_calculations as $pc => $key)

            <div class="pc-content-div" data-id="{{$pc+1}}">

                <div style="width: 18%;">
                    <div style="display: flex;align-items: center;">
                        <div style="width: 90%;">
                            <input type="text" maskedformat="9,1" value="{{number_format((float)$key->percentage, 2, ',', '')}}" class="form-control pc_percentage m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_percentage[]">
                        </div>
                    </div>
                </div>

                <div style="width: 18%;">
                    <div style="display: flex;align-items: center;">
                        <div style="width: 90%;">
                            <input type="text" maskedformat="9,1" value="{{number_format((float)$key->amount, 2, ',', '')}}" class="form-control pc_amount m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_amount[]">
                        </div>
                    </div>
                </div>

                <div style="width: 18%;">
                    <div style="display: flex;align-items: center;">
                        <div style="width: 90%;">
                            <input type="text" value="{{date('d-m-Y',strtotime($key->date))}}" readonly class="form-control pc_date m-input" style="border: 1px solid #ccc;background: transparent;width: 100%;height: 35px !important;" name="pc_date[]">
                        </div>
                    </div>
                </div>

                <div style="width: 18%;">
                    <div style="display: flex;align-items: center;">
                        <div style="width: 90%;">
                            <select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_paid_by" name="pc_paid_by[]">
                                <option {{$key->paid_by == "Pending" ? "selected" : null}} value="Pending">{{__('text.Pending payments')}}</option>
                                <option {{$key->paid_by == "Mollie" ? "selected" : null}} value="Mollie">{{__('text.Mollie')}}</option>
                                <option {{$key->paid_by == "Betaallink" ? "selected" : null}} value="Betaallink">{{__('text.Pin device')}}</option>
                                <option {{$key->paid_by == "Bank" ? "selected" : null}} value="Bank">{{__('text.Bank')}}</option>
                                <option {{$key->paid_by == "Cash" ? "selected" : null}} value="Cash">{{__('text.Cash')}}</option>
                                <option {{$key->paid_by == "Settled" ? "selected" : null}} value="Settled">{{__('text.Settled')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="width: 18%;">
                    <div style="display: flex;align-items: center;">
                        <div style="width: 90%;">
                            <select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_description" name="pc_description[]">
                                <option {{$key->description == "By accepting" ? "selected" : null}} value="By accepting">{{__('text.By accepting')}}</option>
                                <option {{$key->description == "By delivery goods" ? "selected" : null}} value="By delivery goods">{{__('text.By delivery goods')}}</option>
                                <option {{$key->description == "By finishing work" ? "selected" : null}} value="By finishing work">{{__('text.By finishing work')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="padding: 0;width: 10%;display: flex;">

                    <div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;">

                        <span id="next-row-span" class="tooltip1 add-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                            <i id="next-row-icon" class="fa fa-fw fa-plus"></i>
                            <span class="tooltiptext">{{__('text.Add')}}</span>
                        </span>

                        <span id="next-row-span" class="tooltip1 remove-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                            <i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
                            <span class="tooltiptext">{{__('text.Remove')}}</span>
                        </span>

                    </div>

                </div>

            </div>

        @endforeach

    @else

        <div class="pc-content-div" data-id="1">

            <div style="width: 18%;">
                <div style="display: flex;align-items: center;">
                    <div style="width: 90%;">
                        <input type="text" maskedformat="9,1" value="{{isset($invoice) && count($payment_calculations) == 0 ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? '0,00' : '100,00') : '0,00'}}" class="form-control pc_percentage m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_percentage[]">
                    </div>
                </div>
            </div>

            <div style="width: 18%;">
                <div style="display: flex;align-items: center;">
                    <div style="width: 90%;">
                        <input type="text" maskedformat="9,1" value="{{isset($invoice) && count($payment_calculations) == 0 ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? '0,00' : number_format((float)$invoice[0]->grand_total, 2, ',', '.')) : '0,00'}}" class="form-control pc_amount m-input" style="border: 1px solid #ccc;width: 100%;height: 35px !important;" name="pc_amount[]">
                    </div>
                </div>
            </div>

            <div style="width: 18%;">
                <div style="display: flex;align-items: center;">
                    <div style="width: 90%;">
                        <input type="text" readonly class="form-control pc_date m-input" style="border: 1px solid #ccc;background: transparent;width: 100%;height: 35px !important;" name="pc_date[]">
                    </div>
                </div>
            </div>

            <div style="width: 18%;">
                <div style="display: flex;align-items: center;">
                    <div style="width: 90%;">
                        <select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_paid_by" name="pc_paid_by[]">
                            <option value="Pending">{{__('text.Pending payments')}}</option>
                            <option value="Mollie">{{__('text.Mollie')}}</option>
                            <option value="Betaallink">{{__('text.Pin device')}}</option>
                            <option value="Bank">{{__('text.Bank')}}</option>
                            <option value="Cash">{{__('text.Cash')}}</option>
                            <option value="Settled">{{__('text.Settled')}}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="width: 18%;">
                <div style="display: flex;align-items: center;">
                    <div style="width: 90%;">
                        <select style="border-radius: 5px;width: 100%;height: 35px;" class="form-control pc_description" name="pc_description[]">
                            <option value="By accepting">{{__('text.By accepting')}}</option>
                            <option value="By delivery goods">{{__('text.By delivery goods')}}</option>
                            <option value="By finishing work">{{__('text.By finishing work')}}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="padding: 0;width: 10%;display: flex;">

                <div class="res-white" style="display: flex;justify-content: flex-start;align-items: center;width: 100%;">

                    <span id="next-row-span" class="tooltip1 add-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                        <i id="next-row-icon" class="fa fa-fw fa-plus"></i>
                        <span class="tooltiptext">{{__('text.Add')}}</span>
                    </span>

                    <span id="next-row-span" class="tooltip1 remove-pc-row" style="cursor: pointer;font-size: 20px;margin-left: 10px;width: 20px;height: 20px;line-height: 20px;">
                        <i id="next-row-icon" class="fa fa-fw fa-trash-o"></i>
                        <span class="tooltiptext">{{__('text.Remove')}}</span>
                    </span>

                </div>

            </div>

        </div>

    @endif

</section>

<div style="display: flex;align-items: stretch;flex-flow: wrap;margin-top: 20px;" class="pc-total-div">

    <div style="width: 18%;">
        <div style="display: flex;align-items: center;">
            <div style="width: 90%;">
                <label>{{__("text.Percentages Total")}} (%)</label>
                <input type="text" maskedformat="9,1" value="{{isset($invoice) && count($payment_calculations) > 0 ? $invoice[0]->payment_total_percentage : (isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? '0,00' : '100,00') : '0,00')}}" readonly class="form-control pc_percentages_total m-input" style="background: #f6f6f6;width: 100%;height: 35px !important;" name="pc_percentages_total">
            </div>
        </div>
    </div>

    <div style="width: 18%;">
        <div style="display: flex;align-items: center;">
            <div style="width: 90%;">
                <label>{{__("text.Amounts Total")}} (€)</label>
                <input type="text" maskedformat="9,1" value="{{isset($invoice) && count($payment_calculations) > 0 ? $invoice[0]->payment_total_amount : (isset($invoice) ? (Route::currentRouteName() == 'create-new-negative-invoice' && !$invoice[0]->negative_invoice ? '0,00' : number_format((float)$invoice[0]->grand_total, 2, ',', '.')) : '0,00')}}" readonly class="form-control pc_amounts_total m-input" style="background: #f6f6f6;width: 100%;height: 35px !important;" name="pc_amounts_total">
            </div>
        </div>
    </div>

</div>

<script>
	var quote_config = {
		pending_payments: "{{__('text.Pending payments')}}",
        mollie: "{{__('text.Mollie')}}",
        pin_device: "{{__('text.Pin device')}}",
        bank: "{{__('text.Bank')}}",
        cash: "{{__('text.Cash')}}",
        settled: "{{__('text.Settled')}}",
        by_accepting: "{{__('text.By accepting')}}",
        by_delivery_goods: "{{__('text.By delivery goods')}}",
        by_finishing_work: "{{__('text.By finishing work')}}",
        add: "{{__('text.Add')}}",
        remove: "{{__('text.Remove')}}"
    };
</script>
<script src="{{asset('assets/front/js/payment_calculations.js?v=1.6')}}"></script>