<div id="checklist_quotationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <button style="background-color: white !important;color: black !important;" type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">{{__('text.Select Quotation')}}</h3>
            </div>

            <div class="modal-body" id="myWizard" style="display: inline-block;width: 100%;">

                <div style="margin-top: 15px;" class="col-sm-12 checklist_quotations_box">
                    <select class="checklist_quotations">
                        <option value="">{{__("text.Select Quotation")}}</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" style="border: 0;outline: none;background-color: #5cb85c !important;"
                        class="btn btn-primary submit-checklist-quotation">{{__('text.Submit')}}</button>
            </div>

        </div>

    </div>
</div>

<script>

    $(".checklist_quotations").select2({
        width: '100%',
        height: '200px',
        placeholder: '{{__("text.Select Quotation")}}',
        allowClear: true,
        dropdownParent: $('#checklist_quotationModal'),
        "language": {
            "noResults": function () {
                return "{{__('text.No results found')}}";
            }
        },
    });

</script>