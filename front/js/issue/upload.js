/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
    staticPdf.init();
    simplePdf.init();
    verticalHelpPage.init();
    horizontalHelpPage.init();
});


var staticPdf = {
    issueId: null,
    appId: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#static-pdf-wrapper')[0];

        if (!context.domRoot) {
            return;
        }

        context.issueId = window.issueId;
        context.appId = window.appId;

        $('a.delete-btn', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event);
        });

        context.initFancybox($("a.single_image", context.domRoot));

        $("ul.horizontal-pdf-gallery", context.domRoot).sortable({
            stop: function(event, ui) {
                $(event.originalEvent.target).addClass('prevent-select');
                context.onChangeWeight(event, ui);
            }
        }).disableSelection();

        $('input.pdf-file').change(function(event){
            $('.upload-form').ajaxSubmit({
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t upload file'));
                        }
                    } else {
                        var staticPdf = responseJSON.staticPdf;
                        var issueStaticPdfMode = responseJSON.issueStaticPdfMode;
                        var file = responseJSON.file;
                        var fieldTypeTitle = responseJSON.fieldTypeTitle;

                        //Unset field value
                        $('input.pdf-file').val(null);

                        if (issueStaticPdfMode == 'issue' || issueStaticPdfMode == '2pages') {
                            $('ul.horizontal-pdf-gallery li').remove();
                        }

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="single_image" href="' + file.bigUri + '" rel="' + fieldTypeTitle + '">' +
                            '<img alt="' + file.name + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img alt="' + file.name + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                        '<li id="static-pdf-' + staticPdf + '">' +
                        '<div class="data-item">' +
                        image +
                        '<span class="name" title="' + file.name + '">' + file.nameShort + '</span>' +
                        '<a href="#" title="Delete image" class="close delete-btn"></a>' +
                        '</div>' +
                        '</li>';

                        $('ul.horizontal-pdf-gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#static-pdf-' + staticPdf);
                        $('a.delete-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });

                        context.initFancybox($('a.single_image', domElement));
                        $('.upload-btn.download').show();
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
        var data = {};
        $('li', event.target).each(function(index) {
            data[$(this).attr('id').split('-').pop()] = index;
        });

        $.ajax({
            url: '/issue/save-weight',
            type: 'POST',
            dataType: 'json',
            data: {
                weight: data,
                aid: window.appId,
                iid: window.issueId
            },
            success: function(data) {
                try {
                    if (data.status != 1) {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_ajax_error'));
                }
            }
        });
    },

    onDelete: function(event) {
        var staticPdfId = $(event.target).closest('li').attr('id').split('-').pop();

        if (!staticPdfId) {
            return false;
        }

        $.ajax({
            url: '/issue/delete-static-pdf',
            type: 'POST',
            dataType: 'json',
            data: {
                staticPdf: staticPdfId,
                issueId: window.issueId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('#static-pdf-' + staticPdfId).remove();
                        if(!$('ul.list.horizontal-pdf-gallery li').length) {
                            $('.upload-btn.download').hide();
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
    }
};

var simplePdf =  {
    issueId: null,
    appId: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#simple-pdf-wrapper')[0];

        if (!context.domRoot) {
            return;
        }

        context.issueId = window.issueId;
        context.appId   = window.appId;

        $('a.delete-simple-pdf-btn', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event);
        });

        context.initFancybox($('a.simple_pdf_image', context.domRoot));

        $('input.simple-pdf-file').change(function(event){
            $('.simple-pdf-upload-form').ajaxSubmit({
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t upload file'));
                        }
                    } else {
                        var simplePdf = responseJSON.simplePdf;
                        var file      = responseJSON.file;

                        //Unset field value
                        $('input.simple-pdf-file').val(null);

                        $('ul.simple-pdf-gallery li').remove();

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="simple_pdf_image" href="' + file.bigUri + '">' +
                            '<img alt="' + file.name + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img alt="' + file.name + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                        '<li id="simple-pdf">' +
                        '<div class="data-item">' +
                        image +
                        '<span class="name" title="' + file.name + '">' + file.nameShort + '</span>' +
                        '<a href="#" title="Delete image" class="close delete-simple-pdf-btn"></a>' +
                        '</div>' +
                        '</li>';

                        $('ul.simple-pdf-gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#simple-pdf');
                        $('a.delete-simple-pdf-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });

                        context.initFancybox($('a.simple_pdf_image', domElement));
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

    onDelete: function(event) {
        $.ajax({
            url: '/issue/delete-simple-pdf',
            type: 'POST',
            dataType: 'json',
            data: {
                issueId: window.issueId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('ul.simple-pdf-gallery li').remove();
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
    }
};

var verticalHelpPage =  {
    issueId: null,
    appId: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#vertical-help-page-wrapper')[0];

        if (!context.domRoot) {
            return;
        }

        context.issueId = window.issueId;
        context.appId   = window.appId;

        $('a.delete-vertical-help-page-btn', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event);
        });

        context.initFancybox($('a.vertical-help-page-image', context.domRoot));

        $('input.vertical-help-page-file').change(function(event){
            $('.vertical-help-page-upload-form').ajaxSubmit({
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t upload file'));
                        }
                    } else {
                        var file      = responseJSON.file;

                        //Unset field value
                        $('input.vertical-help-page-file').val(null);

                        $('ul.vertical-help-page-gallery li').remove();

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="vertical-help-page-image" href="' + file.bigUri + '">' +
                            '<img alt="' + file.name + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img alt="' + file.name + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                        '<li id="vertical-help-page">' +
                        '<div class="data-item">' +
                        image +
                        '<span class="name" title="' + file.name + '">' + file.nameShort + '</span>' +
                        '<a href="#" title="Delete image" class="close delete-vertical-help-page-btn"></a>' +
                        '</div>' +
                        '</li>';

                        $('ul.vertical-help-page-gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#vertical-help-page');
                        $('a.delete-vertical-help-page-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });

                        context.initFancybox($('a.vertical-help-page-image', domElement));
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

    onDelete: function(event) {
        $.ajax({
            url: '/issue/delete-help-page/type/vertical',
            type: 'POST',
            dataType: 'json',
            data: {
                issueId: window.issueId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('ul.vertical-help-page-gallery').remove();
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
    }
};

var horizontalHelpPage =  {
    issueId: null,
    appId: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#horizontal-help-page-wrapper')[0];

        if (!context.domRoot) {
            return;
        }

        context.issueId = window.issueId;
        context.appId   = window.appId;

        $('a.delete-horizontal-help-page-btn', context.domRoot).bind('click', context, function(event) {
            return event.data.onDelete(event);
        });

        context.initFancybox($('a.horizontal-help-page-image', context.domRoot));

        $('input.horizontal-help-page-file').change(function(event){
            $('.horizontal-help-page-upload-form').ajaxSubmit({
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t upload file'));
                        }
                    } else {
                        var file      = responseJSON.file;

                        //Unset field value
                        $('input.horizontal-help-page-file').val(null);

                        $('ul.horizontal-help-page-gallery li').remove();

                        var image = null;
                        if (file.smallUri && file.bigUri) {
                            image =
                            '<a class="horizontal-help-page-image" href="' + file.bigUri + '">' +
                            '<img width="96" height="72" alt="' + file.name + '" src="' + file.smallUri + '"/>' +
                            '</a>';
                        } else {
                            image = '<img width="96" height="72" alt="' + file.name + '" src="' + file.smallUri + '"/>';
                        }

                        var html =
                        '<li id="horizontal-help-page">' +
                        '<div class="data-item">' +
                        image +
                        '<span class="name" title="' + file.name + '">' + file.nameShort + '</span>' +
                        '<a href="#" title="Delete image" class="close delete-horizontal-help-page-btn"></a>' +
                        '</div>' +
                        '</li>';

                        $('ul.horizontal-help-page-gallery', context.domRoot).append(html);

                        // Bind events
                        var domElement = $('#horizontal-help-page');
                        $('a.delete-horizontal-help-page-btn', domElement).bind('click', context, function(event) {
                            return event.data.onDelete(event.originalEvent);
                        });

                        context.initFancybox($('a.horizontal-help-page-image', domElement));
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

    onDelete: function(event) {
        $.ajax({
            url: '/issue/delete-help-page/type/horizontal',
            type: 'POST',
            dataType: 'json',
            data: {
                issueId: window.issueId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $('ul.horizontal-help-page-gallery').remove();
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
    }
};