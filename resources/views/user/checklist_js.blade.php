<script type="text/javascript">

    $(document).ready(function() {

        $('body').on('focus',".delivery_date, .order_date", function(){
            $(this).datetimepicker({
                format: 'DD-MM-YYYY',
                //minDate: new Date(),
                locale:'du',
                ignoreReadonly: true
            });
        });

        $(document).on('click', '.save-data', function(){

            var flag = 0;

            $(".dd").each(function(i, obj) {

                if(!obj.value)
                {
                    flag = 1;
                    $(this).parent().css('border','1px solid red');
                }
                else
                {
                    $(this).parent().css('border','');
                }

            });

            if(flag == 1)
            {
                Swal.fire({
                    icon: 'error',
                    title: '{{__('text.Oops...')}}',
                    text: '{{__('text.Delivery date should not be left empty!')}}',
                });
            }

            var rowCount = $('#checklist_table tbody tr').length;

            if(rowCount == 0)
            {
                flag = 1;
            }

            if(!flag)
            {
                $('#form-checklist').submit();
            }

        });

    });
</script>