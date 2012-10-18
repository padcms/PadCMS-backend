/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(function() {
    bindIframeDialog();
});

var bindIframeDialog = function() {
	var bind_func = function() {
        context = this;
        $.ajax({
            url: '/java-script/check-auth-ajax',
            dataType: 'json',
            type: 'POST',
            success: function(data) {
                if (data && data.result) {
                    iframeDialogOnClick.call(context);
                } else {
                   window.location.reload();
                }
            },
            error: function() {
                alert('unexpected_ajax_error');
            }
        });

        return false;
    }

    $('a.iframe').bind('click', null, bind_func);
    $('a.iframe-dblclick').bind('click', null, function() { return false } )
                          .bind('dblclick', null, bind_func);
}

function iframeDialogOnClick() {
    var iframe = $('#iframe-dialog');
    if (!iframe || iframe.length <= 0) {
        iframe = $('<iframe id="iframe-dialog" style="display:none">');
        $('body').append(iframe);
    }

    iframe.dialog("destroy");
    a = $(this);

    if (a.hasClass('vocabulary_editor')) {
        iframe.dialog({
            width: 545,
            height: 400,
            modal: true,
            title: translate('toc_editor')
        });

        iframe.bind("dialogclose", function(event, ui) {
            try {
                $('#page-' + document.pid).click();
            } catch (e) {
                alert(translate('unexpected_error'));
            }
            return true;
        });

    } else if (a.hasClass('load_template')) {

        iframe.dialog({
            width: 540,
            height: 445,
            modal: true,
            title: 'load_template'
        });
    } else if (a.hasClass('add_forecast')) {

        iframe.dialog({
            width: 570,
            height: 445,
            modal: true,
            title: 'add_forecast'
        });

    } else if (a.hasClass('add_event')) {

        iframe.dialog({
            width: 525,
            height: 420,
            modal: true,
            title: 'add_event'
        });

    } else if (a.hasClass('refund')) {

        iframe.dialog({
            width: 600,
            height: 550,
            modal: true,
            title: 'refund'
        });

    } else if (a.hasClass('shift_editer')) {

        iframe.dialog({
            width: 600,
            height: 460,
            modal: true,
            title: 'shift_editer',
            resizable: false
        });
    } else if (a.hasClass('shift-offer')) {

        iframe.dialog({
            width: 300,
            height: 260,
            modal: true,
            title: 'Offer this shift',
            resizable: false
        });

    } else if (a.hasClass('link_video')) {

        iframe.dialog({
            width: 400,
            height: 200,
            modal: true,
            title: 'link_video_file',
            resizable: false
        });

        iframe.bind("dialogclose", function(event, ui) {
            try {
                $('#page-' + document.pid).click();
            } catch (e) {
                alert(translate('unexpected_error'));
            }

            return true;
        });

    } else if (a.hasClass('link_thumbnail')) {

        iframe.dialog({
            width: 400,
            height: 200,
            modal: true,
            title: 'link_thumbnail',
            resizable: false
        });

        iframe.bind("dialogclose", function(event, ui) {
            try {
                $('#page-' + document.pid).click();
            } catch (e) {
                alert(translate('unexpected_error'));
            }

            return true;
        });
    } else {
        iframe.dialog();
    }

    iframe.css('width', (iframe.dialog( 'option', 'width' ) - 60) + 'px');
    iframe.css('height', (iframe.dialog( 'option', 'height' ) - 60) + 'px');

    iframe.attr('src', $(this).attr('href'));

    return false;
}
