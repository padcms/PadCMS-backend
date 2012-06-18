window.ui = {

    ajaxInProcess: false,

    ajaxIndicatorCount: 0,

    ajaxStart: function () {
        var context = this;
        if (context.ajaxIndicatorCount == 0) {
            context.ajaxIndicatorCount++;
            context.showAjaxInicator();
        } else {
            context.ajaxIndicatorCount++;
        }
    },

    ajaxStop: function () {
        var context = this;
        if (context.ajaxIndicatorCount > 0) {
            context.ajaxIndicatorCount--;
        }
        if (context.ajaxIndicatorCount == 0) {
            context.hideAjaxInicator();
        }
    },

    ajaxError: function (event, XMLHttpRequest, ajaxOptions, thrownError) {
        var context = this;
        alert(translate('unexpected_ajax_error'));
        context.log(event, XMLHttpRequest, ajaxOptions, thrownError);
    },

    showAjaxInicator: function () {
        var context = this;
        $(context.indicator).css('top', 0)
                .css('left', $(window).width() / 2 - ($(context.indicator).width() / 2))
                .show();
    },

    hideAjaxInicator: function () {
        var context = this;
        $(context.indicator).hide();
    },

    init: function () {
        var context = this;

        if (!$('#ajax-indicator').length) {
            $('body').append('<div id="ajax-indicator" style="display:none;">Loading...</div>');
        }

        context.indicator = $('#ajax-indicator');

        $(context.indicator).ajaxStart(function(){
            context.ajaxStart();
        });

        $(context.indicator).ajaxStop(function(){
            context.ajaxStop();
        });

        $(context.indicator).ajaxError(function(event, XMLHttpRequest, ajaxOptions, thrownError){
            context.ajaxError(event, XMLHttpRequest, ajaxOptions, thrownError);
        });

        $(window).resize(function(){
            if (context.ajaxIndicatorCount > 0)
                context.showAjaxInicator();
        });

        $("body").ajaxError(function(evt, request, settings){
            if (request.status == 401) {
                window.location.reload();
            }
        });
    },

    log: function () {
        if (!window.console) return;

        window.console.log.apply(console, arguments);
    },

    popupMesage: function (message) {
        alert(translate(message));

        return false;
    }
};

window.ui.init();
