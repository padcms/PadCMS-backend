/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldVideo = {
    pageId: null,
    fieldId: null,
    uploader: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#field-video')[0];
        context.domRootAudio = $('#field-id-sound').parent()[0];

        if (!context.domRoot) {
            return;
        }

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();
        if (context.domRootAudio) {
            context.fieldIdSound = $("input[name='field-id']", context.domRootAudio).val();
            $('a.delete-btn', context.domRootAudio).bind('click', context, function(event){
                return event.data.onDelete(event.originalEvent);
            });
            $('a.enable-loop-btn', context.domRootAudio).bind('click', context, function(event){
                event.data.onEnableLoop(event);
                return false;
            });
            $('a.disable-ui-btn', context.domRootAudio).bind('click', context, function(event){
                event.data.onDisableUi(event);
                return false;
            });
            $(".gallery", context.domRootAudio).sortable({
                stop: function(event, ui) {
                    $(event.originalEvent.target).addClass('prevent-select');
                    context.onChangeWeight(event, ui);
                }
            }).disableSelection();
        }

//        $('input', '#video-type-stream').bind('keypress', context, function(event) {
//            if (event.which == 13) {
//                event.data.onSave('stream', $(event.originalEvent.target).val());
//            }
//        });

        $('a.cbutton', '#video-type-stream').bind('click', context, function(event) {
            var idElem = $(this).prev().children('input').attr('id');
            event.data.onSave('stream', $(this).prev().children('input').val(), idElem);
        });

        $('a.delete-btn', context.domRoot).bind('click', context, function(event){
            return event.data.onDelete(event.originalEvent);
        });

        $('a.enable-loop-btn', context.domRoot).bind('click', context, function(event){
            event.data.onEnableLoop(event);
            return false;
        });
        $('a.disable-ui-btn', context.domRoot).bind('click', context, function(event){
            event.data.onDisableUi(event);
            return false;
        });

        $(".gallery", context.domRoot).sortable({
            stop: function(event, ui) {
                $(event.originalEvent.target).addClass('prevent-select');
                context.onChangeWeight(event, ui);
            }
        }).disableSelection();

        $(".stream-sort", context.domRoot).sortable({
            stop: function(event, ui) {
                $(event.originalEvent.target).addClass('prevent-select');
                context.onChangeWeight(event, ui);
            }
        }).disableSelection();

        $('input.resource-video').change(function(event){
            $('.upload-form-video').ajaxSubmit({
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
                        var file    = responseJSON.file;
                        var element = responseJSON.element;

                        //Unset field value
                        $('input.resource-video').val('');
                        $('input', '#video-type-stream').val('');

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="single_image" href="' + file.bigUri + '">' +
                            '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                            '<li id="element-' + element + '">' +
                                '<div class="data-item">' +
                                image +
                                '<div class="actions">' +
                                '<a class="action-2-disabled enable-loop-btn" href="#" title="Enable loop"></a>' +
                                '<a class="action-2-disabled disable-ui-btn" href="#" title="Disable UI"></a>' +
                                '</div>' +
                                '<span class="name" title="' + file.fileName + '">' + file.fileNameShort + '</span>' +
                                '<a href="#" title="Delete video" class="close delete-btn"></a>' +
                                '</div>' +
                                '</li>';

                        $('ul#video', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#element-' + element);
                        $('a.delete-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });
                        $('a.enable-loop-btn', domElement).bind('click', context, function(event){
                            event.data.onEnableLoop(event);
                            return false;
                        });
                        $('a.disable-ui-btn', domElement).bind('click', context, function(event){
                            event.data.onDisableUi(event);
                            return false;
                        });

                        $('ul.stream-sort').empty();

//                        var divPicture = $('div.picture', context.domRoot);
//                        $(divPicture).html(image);
//
//                        $('a.close', $(divPicture).parent())
//                                .attr('href', '/field/delete/key/resource/element/' + responseJSON.element)
//                                .show();
//                        $('span.name', $(divPicture).parent())
//                                .html(file.fileNameShort)
//                                .attr('title', file.fileName);

                        $("a.single_image", context.domRoot).fancybox();

                        if (context.domRootAudio) {
                            $("a.single_image", context.domRootAudio).fancybox();
                        }
                    }
                }
            });
        });

        $('input.resource-sound').change(function(event){
            $('.upload-form-sound').ajaxSubmit({
                data: {
                    page_id:  context.pageId,
                    field_id: context.fieldIdSound
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
                        var file    = responseJSON.file;
                        var element = responseJSON.element;

                        //Unset field value
                        $('input.resource-sound').val('');
                        $('input', '#sound-type-stream').val('');

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                                '<a class="single_image" href="' + file.bigUri + '">' +
                                    '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>' +
                                    '</a>';
                        } else {
                            image = '<img alt="' + file.fileName + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                            '<li id="element-' + element + '">' +
                                '<div class="data-item">' +
                                image +
                                '<div class="actions">' +
                                '<a class="action-2-disabled enable-loop-btn" href="#" title="Enable loop"></a>' +
                                '<a class="action-2-disabled disable-ui-btn" href="#" title="Disable UI"></a>' +
                                '</div>' +
                                '<span class="name" title="' + file.fileName + '">' + file.fileNameShort + '</span>' +
                                '<a href="#" title="Delete sound" class="close delete-btn"></a>' +
                                '</div>' +
                                '</li>';


                        if (context.domRootAudio) {
                            $('ul#sound', context.domRootAudio).append(html);
                        }

                        // Bind events
                        var domElement = $('#element-' + element);
                        $('a.delete-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });
                        $('a.enable-loop-btn', domElement).bind('click', context, function(event){
                            event.data.onEnableLoop(event);
                            return false;
                        });
                        $('a.disable-ui-btn', domElement).bind('click', context, function(event){
                            event.data.onDisableUi(event);
                            return false;
                        });

                        $('ul.stream-sort').empty();

//                        var divPicture = $('div.picture', context.domRoot);
//                        $(divPicture).html(image);
//
//                        $('a.close', $(divPicture).parent())
//                                .attr('href', '/field/delete/key/resource/element/' + responseJSON.element)
//                                .show();
//                        $('span.name', $(divPicture).parent())
//                                .html(file.fileNameShort)
//                                .attr('title', file.fileName);

                        $("a.single_image", context.domRoot).fancybox();

                        if (context.domRootAudio) {
                            $("a.single_image", context.domRootAudio).fancybox();
                        }
                    }
                }
            });
        });
    },

//    onDelete: function(event) {
//        var context = this;
//        url = $(event.target).attr('href');
//        $.ajax({
//            url: url,
//            type: 'POST',
//            dataType: 'json',
//            success: function(data) {
//                try {
//                    if (data.status == 1) {
//                        var divPicture = $('div.picture', context.domRoot);
//                        var html = '<img alt="Default image" src="' + data.defaultImageUri + '"/>';
//                        $(divPicture).html(html);
//                        $('a.close', $(divPicture).parent())
//                                .hide()
//                                .attr('href', '');
//                        $('span.name', $(divPicture).parent()).empty();
//                    } else {
//                        alert(data.message);
//                    }
//                } catch (e) {
//                    window.ui.log(e);
//                    alert(translate('unexpected_ajax_error'));
//                }
//            }
//        });
//        return false;
//    },

    onDelete: function(event) {
        var liId = $(event.target).closest('li').attr('id').split('-');
        var elementId = liId.pop();
        var elemType = liId.pop();

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
                        $('#' + elemType + '-' + elementId).remove();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
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
            url: '/field-video/show',
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
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
    },

    onSave: function(key, value, elemId) {
        var context = this;

        if (!key)
            return false;

        $.ajax({
            url: '/field-video/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: context.fieldId,
                element: elemId,
                key: key,
                value: value
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
//                        var divPicture = $('div.picture', context.domRoot);
//                        var html = '<img alt="Default image" src="' + data.defaultImageUri + '"/>';
//                        $(divPicture).html(html);
//                        $('a.close', $(divPicture).parent()).hide();
//                        $('span.name', $(divPicture).parent()).empty();

                        if (elemId == 0) {
                            var element = data.element;
                            var stream = data.stream;
                            var html =
                                '<li id="stream-' + element + '">' +
                                    '<div id="edit-width-wrapper" class="form-item">' +
                                    '<div class="form-item-wrapper stream-url-wrapper">' +
                                    '<div class="sort-weight"></div>' +
                                    '<input id="' + element + '" type="text" class="form-text" value=' + stream + ' />' +
                                    '</div>' +
                                    '<a id="page-additional-data-btn" class="cbutton" href="#"><span><span class="ico">Save</span></span></a>' +
                                    '<a href="#" title="Delete video" class="close delete-btn"></a>' +
                                    '</div>' +
                                    '<div class="clear"></div>' +
                                    '<div class="actions">' +
                                    '<a class="action-2-disabled enable-loop-btn" href="#" title="Enable loop"></a>' +
                                    '<a class="action-2-disabled disable-ui-btn" href="#" title="Disable UI"></a>' +
                                    '</div>' +
                                    '</div>' +
                                    '</li>';

                            $('ul.stream-sort', context.domRoot).append(html);
                            $('input.new-stream', context.domRoot).val(null);

                            // Bind events
                            var domElement = $('#stream-' + element);
                            $('a.cbutton', domElement).bind('click', context, function(event) {
                                var idElem = $(this).prev().children('input').attr('id');
                                event.data.onSave('stream', $('input', domElement).val(), idElem);
                            });
                            $('a.enable-loop-btn', domElement).bind('click', context, function(event){
                                event.data.onEnableLoop(event);
                                return false;
                            });
                            $('a.disable-ui-btn', domElement).bind('click', context, function(event){
                                event.data.onDisableUi(event);
                                return false;
                            });
                            $('a.delete-btn', domElement).bind('click', context, function(event) {
                                return event.data.onDelete(event.originalEvent);
                            });
                            $('ul#video.gallery').empty();
                        }
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
        return false;
    },

    onChangeType: function(type) {
            context = this;
            if (type == 'file') {
                $('#video-type-stream', context.domRoot).hide();
                $('#video-type-file', context.domRoot).show();
                $('div.cont', context.domRoot).css('height', 'auto');
                context.currentType = 'stream';
            } else {
                $('#video-type-file', context.domRoot).hide();
                $('#video-type-stream', context.domRoot).show();
                $('div.cont', context.domRoot).css('height', 'auto');
                context.currentType = 'file';
            }
            return true;
    },

    onEnableLoop: function(event) {
        var context = this;
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();
        context.value = $(event.target).hasClass('action-2-disabled') ? 1 : 0;
        var elemType = $(event.target).closest('ul').attr('id');

        $.ajax({
            url: '/field-video/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: elemType == 'video' ? context.fieldId : context.fieldIdSound,
                element: elementId,
                key: 'loop_' + elemType,
                value: context.value
            },
            success: function(data) {
                try {
                    if (context.value == 1) {
                        $(event.target).removeClass('action-2-disabled').addClass('action-2');
                    } else {
                        $(event.target).removeClass('action-2').addClass('action-2-disabled');
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
        return false;
    },

    onDisableUi: function(event) {
        var context = this;
        var elementId = $(event.target).closest('li').attr('id').split('-').pop();
        context.value = $(event.target).hasClass('action-2-disabled') ? 1 : 0;
        var elemType = $(event.target).closest('ul.gallery').attr('id');

        $.ajax({
            url: '/field-video/save',
            type: 'POST',
            dataType: 'json',
            data: {
                page_id: context.pageId,
                field_id: elemType == 'video' ? context.fieldId : context.fieldIdSound,
                element: elementId,
                key: 'disable_user_interaction',
                value: context.value
            },
            success: function(data) {
                try {
                    if (context.value == 1) {
                        $(event.target).removeClass('action-2-disabled').addClass('action-2');
                    } else {
                        $(event.target).removeClass('action-2').addClass('action-2-disabled');
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
        return false;
    }

}