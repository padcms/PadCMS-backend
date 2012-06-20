/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldDragAndDrop = {
    pageId: null,
    fieldId: null,
    uploader: null,
    domRoot: null,
    topAreaId: null,

    init: function() {
        var context = this;

        context.domRoot = $('#field-drag_and_drop')[0];

        if (!context.domRoot) {
            return;
        }

        var wrapper = $('#edit-top-wrapper', context.domRoot);
        var value = $('input', wrapper);

        $('a', wrapper).bind('click', {context: context, value: value}, function(event) {
            event.data.context.onSave('top_area', $(event.data.value).val());
        });

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();
        context.topAreaId = $("input[name='top-area-id']", context.domRoot).val();

        $('a.delete-btn', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event.originalEvent);
        });

        $('a.add-video-btn', context.domRoot).bind('click', context, function(event){
            event.data.onAddVideo(event);
            return false;
        });

        $('a.add-thumbnail-btn', context.domRoot).bind('click', context, function(event){
            event.data.onAddThumbnail(event);
            return false;
        });

        $('a.add-thumbnail-selected-btn', context.domRoot).bind('click', context, function(event){
            event.data.onAddThumbnailSelected(event);
            return false;
        });

        context.initFancybox($("a.single_image", context.domRoot));

        $(".gallery", context.domRoot).sortable({
            stop: function(event, ui) {
                $(event.originalEvent.target).addClass('prevent-select');
                context.onChangeWeight(event, ui);
            }
        }).disableSelection();

        $('input.resource-drag-and-drop').change(function(event){
            $('.upload-form-drag-and-drop').ajaxSubmit({
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
                        var file           = responseJSON.file;
                        var element        = responseJSON.element;
                        var fieldTypeTitle = responseJSON.fieldTypeTitle;

                        //Unset field value
                        $('input.resource-drag-and-drop').val(null);

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                                '<a class="single_image" href="' + file.bigUri + '" rel="' + fieldTypeTitle + '">' +
                                    '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>' +
                                '</a>';
                        } else {
                            image = '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                            '<li id="element-' + element + '">'
                                + '<div class="data-item">'
                                    + image
                                    + '<span class="name" title="' + file.fileName + '">' + file.fileNameShort + '</span>'
                                    + '<div class="actions">'
                                       + '<a rel="" title="Add video" href="#" class="action-1-disabled add-video-btn"></a>'
                                       + '<a rel="" title="Add thumbnail" href="#" class="action-2-disabled add-thumbnail-btn"></a>'
                                       + '<a rel="" title="Add selected thumbnail" href="#" class="action-2-disabled add-thumbnail-selected-btn"></a>'
                                    + '</div>'
                                    + '<a href="#" title="Delete image" class="close delete-btn"></a>'
                                '</div>'
                            + '</li>';

                        $('ul.gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#element-' + element);
                        $('a.delete-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });
                        $('a.add-video-btn', domElement).bind('click', context, function(event){
                            event.data.onAddVideo(event);
                        });
                        $('a.add-thumbnail-btn', domElement).bind('click', context, function(event){
                            event.data.onAddThumbnail(event);
                        });
                        $('a.add-thumbnail-selected-btn', domElement).bind('click', context, function(event){
                            event.data.onAddThumbnailSelected(event);
                        });

                        context.initFancybox($('a.single_image', domElement));
                    }
                }
            });
        });
    },

    initFancybox: function(elements) {
        $(elements).fancybox({
            onStart: function() {
                if ($(this.orig).hasClass('prevent-select')) {
                    $(this.orig).removeClass('prevent-select');
                    return false;
                }
                return true;
            }
        });
    },

    onChangeWeight: function(event, ui) {
        var context = this;
        var data = {};
        $('li', event.target).each(function(index) {
            data[$(this).attr('id').split('-').pop()] = index;
        });

        $.ajax({
            url: '/field/save-weight',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                weight: data
            },
            success: function(data) {
                try {
                    if (data.status != 1) {
                        alert(data.message);
                    } else {
                        $('#page-' + context.pageId).closest('td').attr('background', data.background);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    },

    onAddVideo: function(event) {
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();
        if (!elementId)
            return false;
        $('#add-file-dialog').dialog('option', 'elementId', elementId)
            .dialog('option', 'key', 'video')
            .dialog('option', 'sourceEvent', event)
            .dialog('option', 'title', 'Add video file to element')
            .dialog('open');
    },

    onAddThumbnail: function(event) {
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();
        if (!elementId)
            return false;
        $('#add-file-dialog').dialog('option', 'elementId', elementId)
            .dialog('option', 'key', 'thumbnail')
            .dialog('option', 'sourceEvent', event)
            .dialog('option', 'title', 'Add thumbnail to element')
            .dialog('open');
    },

    onAddThumbnailSelected: function(event) {
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();
        if (!elementId)
            return false;
        $('#add-file-dialog').dialog('option', 'elementId', elementId)
            .dialog('option', 'key', 'thumbnail_selected')
            .dialog('option', 'sourceEvent', event)
            .dialog('option', 'title', 'Add selected thumbnail to element')
            .dialog('open');
    },

    onDelete: function(event) {
        var context = this;
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();

        if (!elementId) {
            return false;
        }

        $.ajax({
            url: '/field/delete',
            type: 'POST',
            dataType: 'json',
            data: {
                element: elementId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('#element-' + elementId).remove();
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

    clear: function() {
        $(this.domRoot).empty();
    },

    update: function() {
        var context = this;
        $.ajax({
            url: '/field-mini-art/show',
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
            url: '/field-drag-and-drop/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: context.fieldId,
                key: key,
                value: value,
                topAreaId: context.topAreaId
            },
            success: function(data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                    }
                    if (data.topAreaId != context.topAreaId) {
                        context.topAreaId = data.topAreaId;
                        $("input[name='top-area-id']", context.domRoot).val(context.topAreaId);
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