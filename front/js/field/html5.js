var fieldHtml5 = {
    pageId: null,
    fieldId: null,
    domRoot: null,

    init: function() {
        var context = this;
        context.domRoot = $('#field-html5')[0];

        if (!context.domRoot) {
            return;
        }

        $('.cont', context.domRoot).show();

        $('#options li', context.domRoot).hide();

        // Process BODY select and show field, that refers to it
        var selectedItem = $('select[name=html5_body]', context.domRoot).val();
        $('#options #' + selectedItem, context.domRoot).show();
        $('.cont', context.domRoot).hide();

        // Save button
        $('#page-additional-data-btn', context.domRoot).bind('click', {context: context}, function(event) {
            event.data.context.onSave();
        });

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();

        // Preview dialog. On  POST_CODE when user click on PREVIEW button
        // we show this dialog with HTML code from textarea
        $("#html5-dialog-post-code-preview", context.domRoot).dialog({
            height: 'auto',
            width:  'auto',
            title:  'Preview',
            modal: true,
            autoOpen: false
        });

        $("#html5-post-code-preview", context.domRoot).click(function() {
            var html = $("textarea[name=post_code]", context.domRoot).val();
            $("#html5-dialog-post-code-preview").html(html);
            $("#html5-dialog-post-code-preview").dialog('open');
        });

        $('a.close', context.domRoot).bind('click', context, function(event){
            return event.data.onDelete(event.originalEvent);
        });

        $('input.resource-html5').change(function(event){
            $('.upload-form-html5').ajaxSubmit({
                data: {
                    page_id:  context.pageId,
                    field_id: context.fieldId
                },
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t upload file'));
                        }
                    } else {
                        var file = responseJSON.file;

                        //Unset field value
                        $('input.resource-html5').val('');

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="single_image" href="' + file.bigUri + '">' +
                            '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>';
                        }

                        var divPicture = $('div.picture', context.domRoot);
                        $(divPicture).html(image);

                        $('a.close', $(divPicture).parent())
                                .attr('href', '/field/delete/key/resource/element/' + responseJSON.element)
                                .show();
                        $('span.name', $(divPicture).parent())
                                .html(file.fileNameShort)
                                .attr('title', file.fileName);

                        $("a.single_image", context.domRoot).fancybox();
                    }
                }
            });
        });

    },
    bodySelected: function() {
        var body = $("#body-selector").val();
        var context = this;

        $('#options li', context.domRoot).slideUp();
        if (body == 0)
            return;

        $('#options #'+body, context.domRoot).slideDown();
    },
    onSave: function() {
        var context = this;

        var body = $('select[name=html5_body]', context.domRoot).val();
        var options = {};
        // This construction becouse of dynamic fields. We send to the server
        // only selected in BODY fields
        if (body != 0) {
            $('#'+body+' input[type=text], #'+body+' textarea').each(function() {
                options[$(this).attr('name')] = $(this).val();
            });
        }
        var data = {
            page_id: context.pageId,
            field_id: context.fieldId,
            html5_body: body,
            html5_position: $('input[name=html5_position]', context.domRoot).val()
        };

        $.ajax({
            url: '/field-html5/save',
            type: 'POST',
            dataType: 'json',
            data: $.extend(data, options),
            success: function(data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
        return false;
    },

    onDelete: function(event) {
        var context = this;
        url = $(event.target).attr('href');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                try {
                    if (data.status == 1) {
                        var divPicture = $('div.picture');
                        var html = '<img alt="Default image" src="' + data.defaultImageUri + '"/>';
                        $(divPicture).html(html);
                        $('a.close', $(divPicture).parent())
                                .hide()
                                .attr('href', '');
                        $('span.name', $(divPicture).parent()).empty();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
        return false;
    }
}