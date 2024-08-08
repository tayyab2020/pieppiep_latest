<script>

    function save_draft(modal)
    {
        var url = modal.find("form").attr('action');
        var form_data = new FormData(modal.find("form")[0]);

        // var body = modal.find("form").serializeArray();
        // body.push({name: "draft", value: 1});

        form_data.append("draft",1);

        $.ajax({

            type: "POST",
            data: form_data,
            processData: false,
            contentType: false,
            dataType: "json",
            url: url,

            success: function (data) {

                $('.alert-box').prepend('<div class="alert alert-simple alert-success alert-dismissible text-left font__family-montserrat font__size-16 font__weight-light brk-library-rendered rendered">\n' +
                '<button type="button" class="close font__size-18" data-dismiss="alert">\n' +
                '  <span aria-hidden="true"><i class="fa fa-times cross"></i></span>\n' +
                '  <span class="sr-only">Close</span>\n' +
                '</button>\n' +
                '<i class="start-icon fa fa-check-circle faa-tada animated"></i>\n' +
                '<span class="alt-msg">' + data.response + '</span>\n' +
                '</div>');
    
                clearTimeout(alertTimeout);
                $('.alert-box .alert .cross').removeClass('fa-times');
                void $('.alert-box .alert .cross')[0].offsetWidth; // Trigger reflow
                $('.alert-box .alert .cross').addClass('fa-times');
                alertTimeout = setTimeout(function() {
                    $('.alert-box .alert-simple').fadeOut(400, function () {
                        // Remove the old alert after it fades out
                        $(this).remove();
                    });
                }, 15000);

            },
            error: function (data) {

                $('.alert-box').prepend('<div class="alert alert-simple alert-danger alert-dismissible text-left font__family-montserrat font__size-16 font__weight-light brk-library-rendered rendered">\n' +
                '<button type="button" class="close font__size-18" data-dismiss="alert">\n' +
                '  <span aria-hidden="true"><i class="fa fa-times cross"></i></span>\n' +
                '  <span class="sr-only">Close</span>\n' +
                '</button>\n' +
                '<i class="start-icon fa fa-times-circle faa-pulse animated"></i>\n' +
                '<span class="alt-msg">Something went wrong...</span>\n' +
                '</div>');

                clearTimeout(alertTimeout);
                $('.alert-box .alert .cross').removeClass('fa-times');
                void $('.alert-box .alert .cross')[0].offsetWidth; // Trigger reflow
                $('.alert-box .alert .cross').addClass('fa-times');
                alertTimeout = setTimeout(function() {
                    $('.alert-box .alert-simple').fadeOut(400, function () {
                        // Remove the old alert after it fades out
                        $(this).remove();
                    });
			    }, 15000);

            }

        });

    }

    $(document).on('click', '.save-draft', function () {

        $(this).parents(".modal").modal('toggle');

    });

    $('#quotationModal, #orderModal, #invoiceModal, #negativeInvoiceModal').on('hidden.bs.modal', function () {

        save_draft($(this));

    });

    ;(function($) {

        $("#deliver_to").select2({
			width: '100%',
			height: '200px',
			placeholder: "{{__('text.Deliver To')}}",
			allowClear: false,
			"language": {
				"noResults": function () {
					return '{{__('text.No results found')}}';
				}
			},
		});

        var todayDate = new Date().getDate();
        var endD = new Date(new Date().setDate(todayDate + 1));

        $.fn.datepicker.dates['du'] = {
            days: ["zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag"],
            daysShort: ["zo", "ma", "di", "wo", "do", "vr", "za"],
            daysMin: ["zo", "ma", "di", "wo", "do", "vr", "za"],
            months: ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"],
            monthsShort: ["jan", "feb", "mrt", "apr", "mei", "jun", "jul", "aug", "sep", "okt", "nov", "dec"],
        };

        $('#delivery_date_picker').datepicker({

            format: 'dd-mm-yyyy',
            startDate: endD,
            language: 'du',
            daysOfWeekDisabled: [0,6]

        });

        // Browser supports HTML5 multiple file?
        var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',isIE = /msie/i.test( navigator.userAgent );

        $.fn.customFile = function() {

            return this.each(function() {

                var $file = $(this).addClass('custom-file-upload-hidden'), // the original file input
                $wrap = $('<div class="file-upload-wrapper">'),
                $input = $('<input type="text" class="file-upload-input" />'),
                // Button that will be used in non-IE browsers
                $button = $('<button type="button" class="file-upload-button">{{__("text.Select a File")}}</button>'),
                // Hack for IE
                $label = $('<label class="file-upload-button" for="'+ $file[0].id +'">{{__("text.Select a File")}}</label>');

                // Hide by shifting to the left so we
                // can still trigger events
                $file.css({
                    position: 'absolute',
                    left: '-9999px'
                });

                $wrap.insertAfter( $file ).append( $file, $input, ( isIE ? $label : $button ) );

                // Prevent focus
                $file.attr('tabIndex', -1);
                $button.attr('tabIndex', -1);

                $button.click(function () {
                    $file.focus().click(); // Open dialog
                });

                $file.change(function() {

                    var files = [], fileArr, filename;

                    // If multiple is supported then extract
                    // all filenames from the file array
                    if ( multipleSupport ) {
                        fileArr = $file[0].files;
                        for ( var i = 0, len = fileArr.length; i < len; i++ ) {
                            files.push( fileArr[i].name );
                        }
                        filename = files.join(', ');

                        // If not supported then just take the value
                        // and remove the path to just show the filename
                    } else {
                        filename = $file.val().split('\\').pop();
                    }

                    $input.val( filename ) // Set the value
                    .attr('title', filename) // Show filename in title tootlip
                    .focus(); // Regain focus

                });

                $input.on({
                    blur: function() { $file.trigger('blur'); },
                    keydown: function( e ) {
                        if ( e.which === 13 ) { // Enter
                            if ( !isIE ) { $file.trigger('click'); }
                        } else if ( e.which === 8 || e.which === 46 ) { // Backspace & Del
                        // On some browsers the value is read-only
                        // with this trick we remove the old input and add
                        // a clean clone with all the original events attached
                        $file.replaceWith( $file = $file.clone( true ) );
                        $file.trigger('change');
                        $input.val('');
                        } else if ( e.which === 9 ){ // TAB
                            return;
                        } else { // All other keys
                            return false;
                        }
                    }
                });

            });

        };

        // Old browser fallback
        if ( !multipleSupport ) {
            $( document ).on('change', 'input.customfile', function() {

            var $this = $(this),
            // Create a unique ID so we
            // can attach the label to the input
            uniqId = 'customfile_'+ (new Date()).getTime(),
            $wrap = $this.parent(),

            // Filter empty input
            $inputs = $wrap.siblings().find('.file-upload-input').filter(function(){ return !this.value }),
            $file = $('<input type="file" id="'+ uniqId +'" name="'+ $this.attr('name') +'"/>');

            // 1ms timeout so it runs after all other events
            // that modify the value have triggered
            setTimeout(function() {
                // Add a new input
                if ( $this.val() ) {
                    // Check for empty fields to prevent
                    // creating new inputs when changing files
                    if ( !$inputs.length ) {
                        $wrap.after( $file );
                        $file.customFile();
                    }
                    // Remove and reorganize inputs
                    } else {
                        $inputs.parent().remove();
                        // Move the input so it's always last on the list
                        $wrap.appendTo( $wrap.parent() );
                        $wrap.find('input').focus();
                    }
                }, 1);

            });
        }

        $('.summernote').summernote({
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['style']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                /*['color', ['color']],*/
                ['fontname', ['fontname']],
                ['forecolor', ['forecolor']],
            ],
            height: 200,   //set editable area's height
            codemirror: { // codemirror options
                theme: 'monokai'
            },
            callbacks: {
                onChange: function(contents, $editable) {
                    $(this).prev('input').val(contents);
                }
            }
        });

    }(jQuery));

    $('.custom-file-upload input[type=file]').customFile();

    function add_cc_row(parent,data = null)
    {
        if(data)
        {
            for(var i=0; i<data.length; i++)
            {
                parent.append('<div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cc_row">\n' +
                                '<label>{{__('text.CC')}}:</label>\n' +
                                '<div style="width: 100%;display: flex;">\n' +
                                    '<input type="text" value="'+data[i]+'" name="mail_cc[]" class="form-control">\n' +
                                    '<div style="display: flex;justify-content: flex-end;padding: 0;">\n' +
                                        '<span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="add-cc"><i style="font-size: 18px;" class="fa fa-fw fa-plus"></i></span>\n' +
                                        '<span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="remove-cc"><i style="font-size: 18px;" class="fa fa-fw fa-trash-o"></i></span>\n' +
                                    '</div>\n' +
                                '</div>\n' +
                            '</div>');  
            }
        }
        else
        {
            parent.append('<div style="display: flex;flex-direction: column;align-items: flex-start;" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cc_row">\n' +
                                '<label>{{__('text.CC')}}:</label>\n' +
                                '<div style="width: 100%;display: flex;">\n' +
                                    '<input type="text" name="mail_cc[]" class="form-control">\n' +
                                    '<div style="display: flex;justify-content: flex-end;padding: 0;">\n' +
                                        '<span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="add-cc"><i style="font-size: 18px;" class="fa fa-fw fa-plus"></i></span>\n' +
                                        '<span style="display: flex;align-items: center;cursor: pointer;padding: 0 3px;" class="remove-cc"><i style="font-size: 18px;" class="fa fa-fw fa-trash-o"></i></span>\n' +
                                    '</div>\n' +
                                '</div>\n' +
                            '</div>');   
        }
    }   

    $("body").on('click','.add-cc',function() {

        var parent = $(this).parents(".cc_container");
        add_cc_row(parent);

    });

    $("body").on('click','.remove-cc',function() {

        var parent = $(this).parents(".cc_container");
        $(this).parents('.cc_row').remove();

        if(parent.find(".cc_row").length == 0)
        {
            add_cc_row(parent);
        }

    });

</script>