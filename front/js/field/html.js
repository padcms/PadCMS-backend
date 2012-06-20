/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldHtml = {
    pageId: null,
    fieldId: null,
    uploader: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#field-html')[0];

        if (!context.domRoot) {
            return;
        }

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();

//        $('input', '#html-type-url').bind('keypress', context, function(event) {
//            if (event.which == 13) {
//                event.data.onSave('html_url', $(event.originalEvent.target).val());
//            }
//        });

        $('a', '#html-type-url').bind('click', context, function(event) {
            event.data.onSave('html_url', $('input', '#html-type-url').val());
        });

        $('a.close', context.domRoot).bind('click', context, function(event){
            return event.data.onDelete(event.originalEvent);
        });

        $('input.resource-html').change(function(event){
            $('.upload-form-html').ajaxSubmit({
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
                        $('input.resource-html').val('');
                        $('input', '#html-type-url').val('');

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

    onDelete: function(event) {
        var context = this;
        url = $(event.target).attr('href');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
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
    },

    clear: function() {
        $(this.domRoot).empty();
    },

    update: function() {
        var context = this;
        $.ajax({
            url: '/field-html/show',
            type: 'POST',
            dataType: 'html',
            data: {
                page_id: context.pageId,
                field_id: context.fieldId
            },
            success: function(data) {
                context.clear();
                $(context.domRoot).html(data);
                context.init();
            }
        });
    },

    onSave: function(key, value) {
        var context = this;

        if (!key)
            return false;

        $.ajax({
            url: '/field-html/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: context.fieldId,
                key: key,
                value: value
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        var divPicture = $('div.picture', context.domRoot);
                        var html = '<img alt="Default image" src="' + data.defaultImageUri + '"/>';
                        $(divPicture).html(html);
                        $('a.close', $(divPicture).parent()).hide();
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
    },

    onChangeType: function(type) {
            context = this;
            if (type == 'html_archive') {
                $('#html-type-url', context.domRoot).hide();
                $('#html-type-file', context.domRoot).show();
                $('div.cont', context.domRoot).css('height', '90px');
                context.currentType = 'stream';
            } else {
                $('#html-type-file', context.domRoot).hide();
                $('#html-type-url', context.domRoot).show();
                $('div.cont', context.domRoot).css('height', '48px');
                context.currentType = 'file';
            }
            return true;
    },

    onChangeTemplateType: function(type) {
        context = this;
        $.ajax({
            url: '/field-html/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: context.fieldId,
                key: 'template_type',
                value: type
            },
            success: function(data) {
                try {
                    if (data.status != 1) {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    }

}