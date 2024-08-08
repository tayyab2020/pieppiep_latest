<form id="form-checklist" style="padding: 0;" class="form-horizontal" action="{{route('update-details')}}" method="POST" enctype="multipart/form-data">
    {{csrf_field()}}

    <div style="margin: 0;background: #f5f5f5;" class="row">

        <div style="justify-content: center;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 first-row1">
            {{isset($quotation_number) ? $quotation_number : ''}}
        </div>

        <div style="background-color: black;border-radius: 10px;padding: 0 10px;text-align: right;">

            <span class="tooltip1 save-data" style="cursor: pointer;font-size: 20px;margin-right: 10px;color: white;">
                    <i class="fa fa-fw fa-save"></i>
                    <span class="tooltiptext">{{__('text.Save')}}</span>
            </span>

            <a href="{{Route::currentRouteName() == 'plannings' ? route('plannings') : route('customer-quotations')}}" class="tooltip1" style="cursor: pointer;font-size: 20px;color: white;">
                <i class="fa fa-fw fa-close"></i>
                <span class="tooltiptext">{{__('text.Close')}}</span>
            </a>

        </div>
    
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 second-row1" style="padding-bottom: 15px;">
    
            <table id="checklist_table" style="width: 100%;">
                <thead>
                <tr>
                    <th style="padding: 5px;"></th>
                    <th>{{__('text.Product')}}</th>
                    <th>{{__('text.Qty')}}</th>
                    <th>{{__('text.Order Date')}}</th>
                    <th>{{__('text.Ordered')}}</th>
                    <th>{{__('text.Delivery Date')}}</th>
                    <th>{{__('text.Supplier Delivery Date')}}</th>
                    <th>{{__('text.Supplier')}}</th>
                </tr>
                </thead>
    
                <tbody>
    
                    {!! isset($quotation_number) ? $rows : '' !!}
    
                </tbody>
    
            </table>
    
        </div>
    
    </div>
</form>

<style>

    #checklist_table select {
        /*-webkit-appearance: none !important;*/
        /*-moz-appearance: none !important;*/
        /*text-indent: 1px !important;*/
        /*text-overflow: '' !important;*/
        border: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
        box-shadow:none !important;
    }

    .tooltip1 {
        position: relative;
        display: inline-block;
        cursor: pointer;
        font-size: 20px;
    }

    /* Tooltip text */
    .tooltip1 .tooltiptext {
        visibility: hidden;
        width: auto;
        min-width: 60px;
        background-color: #7e7e7e;
        color: #fff;
        text-align: center;
        padding: 5px 0;
        border-radius: 6px;
        position: absolute;
        z-index: 1;
        left: 0;
        top: 55px;
        font-size: 12px;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltip1:hover .tooltiptext {
        visibility: visible;
    }

    .first-row1
    {
        flex-direction: row;
        box-sizing: border-box;
        display: flex;
        background-color: rgb(151, 140, 135);
        height: 50px;
        color: white;
        font-size: 20px;
        align-items: center;
        white-space: nowrap;
        justify-content: space-between;
    }

    .second-row1
    {
        padding: 25px;
        display: flex;
        background: #fff;
    }

    #checklist_table>tr>th
    {
        font-family: system-ui;
        font-weight: 500;
        border-bottom: 1px solid #ebebeb;
        padding-bottom: 15px;
        color: gray;
    }

    #checklist_table>tbody>tr>td
    {
        font-family: system-ui;
        font-weight: 500;
        padding: 0 10px;
        color: #3c3c3c;
        position: relative;
    }

    #checklist_table>tbody>tr.active>td
    {
        border-top: 2px solid #cecece;
        border-bottom: 2px solid #cecece;
    }

    #checklist_table>tbody>tr.active>td:first-child
    {
        border-left: 2px solid #cecece;
        border-bottom-left-radius: 4px;
        border-top-left-radius: 4px;
    }

    #checklist_table>tbody>tr.active>td:last-child {
        border-right: 2px solid #cecece;
        border-bottom-right-radius: 4px;
        border-top-right-radius: 4px;
    }

    #checklist_table {
        border-collapse:separate;
        border-spacing: 0 1em;
    }

</style>