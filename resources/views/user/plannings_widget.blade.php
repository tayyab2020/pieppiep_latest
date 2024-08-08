<div class="row" style="width: 90%;margin: 20px auto;display: flex;">
    <button type="button" class="btn btn-success add-appointment"><i style="margin-right: 5px;" class="fa fa-plus"></i> {{__('text.Add Appointment')}}</button>
    @if(Route::currentRouteName() == "plannings")
        <button type="submit" style="margin-left: 10px;background-color: #0e720e !important;border-color: #0e720e !important;color: white !important;" class="btn btn-success"><i style="margin-right: 5px;" class="fa fa-save"></i> {{__('text.Save')}}</button>
    @endif
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 filter_responsible_box">
        <select class="filter_responsible">
            <option value="">{{__('text.Select Responsible Person')}}</option>

            @foreach($responsible_persons as $key)
                <option value="{{$key->id}}">{{$key->name . ' ' . $key->family_name}}</option>
            @endforeach
        </select>
    </div>
</div>

<input type="hidden" id="logged_user" value="{{Auth::guard('user')->user()->id}}">
<input type="hidden" id="widget_type" value="{{Route::currentRouteName() != 'plannings' ? 2 : 1}}">

<div id='calendar'></div>

<script>
    // global app configuration object
    var config = {
        objects: {
            store_plannings: '{{route("store-plannings")}}',
            remove_plannings: '{{route("remove-plannings")}}',
            get_plannings: '{{route("get-plannings")}}',
            alert1: '{{__("text.For non-continuous events end time should be greater than start time.")}}',
            delivery_date_text: "{{__('text.Delivery Date')}}",
            installation_date_text: "{{__('text.Installation Date')}}",
            are_you_sure: '{{__("text.Are you sure?")}}',
            related_events_confirmation: '{{__("text.Related events will also be deleted with this!")}}',
            yes_text: '{{__("text.Yes")}}!',
            cancel_text: '{{__("text.Cancel")}}',
            all_day_text: '{{__("text.all-day")}}',
            today_text: "{{__('text.today')}}",
            day_text: "{{__('text.day')}}",
            week_text: "{{__('text.week')}}",
            month_text: "{{__('text.month')}}",
            select_event_title_text: "{{__('text.Select Event Title')}}",
            no_results_text: "{{__('text.No results found')}}",
            select_event_status_text: "{{__('text.Select Event Status')}}",
            select_event_type_text: "{{__('text.Select Event Type')}}",
            select_quotation_text: "{{__('text.Select Quotation')}}",
            select_customer_text: "{{__('text.Select Customer')}}",
            select_supplier_text: "{{__('text.Select Supplier')}}",
            select_employee_text: "{{__('text.Select Employee')}}",
            select_responsible_text: "{{__('text.Select Responsible Person')}}",
            view_details: "{{route('view-details')}}",
            fetch_customer_quotations: "{{route('fetch-customer-quotations')}}",
        }
    };
</script>