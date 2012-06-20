/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldPopup = {
    pageId: null,
    fieldId: null,
    uploader: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#field-popup')[0];

        if (!context.domRoot) {
            return;
        }

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();

        $('a.delete-btn', context.domRoot).bind('click', context, function(event){
            return event.data.onDelete(event.originalEvent);
        });

        context.initFancybox($("a.single_image", context.domRoot));

        $(".gallery", context.domRoot).sortable({
            stop: function(event, ui) {
                $(event.originalEvent.target).addClass('prevent-select');
                context.onChangeWeight(event, ui);
            }
        }).disableSelection();

        $('input.resource-popup').change(function(event){
            $('.upload-form-popup').ajaxSubmit({
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
                        $('input.resource-popup').val(null);

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
                            '<li id="element-' + element + '">' +
                                '<div class="data-item">' +
                                    image +
                                    '<span class="name" title="' + file.fileName + '">' + file.fileNameShort + '</span>' +
                                        '<a href="#" title="Delete image" class="close delete-btn"></a>' +
                                '</div>' +
                            '</li>';

                        $('ul.gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#element-' + element);
                        $('a.delete-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
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

    onDelete: function(event) {
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();

        if (!elementId)
            return false;

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
            url: '/field-popup/show',
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
    }
}