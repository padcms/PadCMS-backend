/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldBody = {
    pageId: null,
    fieldId: null,
    uploader: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#field-body')[0];

        if (!context.domRoot) {
            return;
        }

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();

        $('a.close', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event.originalEvent);
        });

        var wrapper = $('#edit-top-wrapper', context.domRoot);
        var value = $('input', wrapper);
        $('input', wrapper).bind('keypress', {context: context, value: value}, function(event) {
            if (event.which == 13) event.data.context.onSave('top', $(event.data.value).val());
        });
        $('a', wrapper).bind('click', {context: context, value: value}, function(event) {
            event.data.context.onSave('top', $(event.data.value).val());
        });

        wrapper = $('#edit-width-wrapper', context.domRoot);
        value = $('input', wrapper);
        $('input', wrapper).bind('keypress', {context: context, value: value}, function(event) {
            if (event.which == 13) event.data.context.onSave('width', $(event.data.value).val());
        });
        $('a', wrapper).bind('click', {context: context, value: value}, function(event) {
            event.data.context.onSave('width', $(event.data.value).val());
        });

        $("a.single_image", context.domRoot).fancybox();

        $('input.resource-body').change(function(event){
            $('.upload-form-body').ajaxSubmit({
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
                        $('input.resource-body').val(null);

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

                        $('#page-' + context.pageId).closest('td').attr('background', responseJSON.background);
                    }
                }
            });
        });
    },

    onSave: function(key, value) {
        var context = this;

        if (!key)
            return false;

        $.ajax({
            url: '/field-body/save',
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
            success: function(data) {
                try {
                    if (data.status == 1) {
                        var divPicture = $('div.picture', context.domRoot);
                        var html = '<img alt="Default image" src="' + data.defaultImageUri + '"/>';
                        $(divPicture).html(html);
                        $('a.close', $(divPicture).parent())
                                .hide()
                                .attr('href', '');
                        $('span.name', $(divPicture).parent()).empty();
                        $('#page-' + context.pageId).closest('td').attr('background', data.background);
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

    onChangeHasPhotoGallery: function(element) {
        var context = this;
        var value = $(element).attr('checked');
        context.onSave('hasPhotoGalleryLink', value ? 1 : 0);
    },

    onChangeShowGalleryOnRotate: function(element) {
        var context = this;
        var value = $(element).attr('checked');
        context.onSave('showGalleryOnRotate', value ? 1 : 0);
    },

    onChangeShowTopLayer: function(element) {
        var context = this;
        var value = $(element).attr('checked');
        context.onSave('showTopLayer', value ? 1 : 0);
    },

    onChangeAlign: function(element) {
        var context = this;
        var value = $('option:selected', element).val();
        context.onSave('alignment', value);
    }

}