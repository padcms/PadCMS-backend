$(document).ready(function() {
    watermarkInit();
    $('select').selectBox();
});

var editor = {
    init: function() {
	// hide/show panels
        $('#accordion .head').click(function() {
            if (!$(this).hasClass('hide-ico')) {
                $('#accordion .head.hide-ico').click();
                $('div.page-panel a.hide:not(.plus)').click();
            }

            $(this).next().toggle();
            $(this).toggleClass('hide-ico');
            accordionHeight();
            mapContainerWidth();

            return false;
        }).next().hide();

        $('div.page-panel a.hide').click(function(event) {
            event.stopPropagation();

            if ($(this).hasClass('plus')) {
                $('#accordion .head.hide-ico').click();
            }

            $('div.page-panel .cont').toggle();
            $(this).toggleClass('plus');

            accordionHeight();
            mapContainerWidth();
	});

        $('div.page-panel div.title').click(function() {
            $('a.hide', $(this)).click();
        });

	$('span.move').click(function(event) {
            event.stopPropagation();

            $('#page-map-wrap-a').toggleClass('page-map-toggle-class');
            $('#page-map-wrap-a').toggleClass('side-panel-left');

            if ($(this).hasClass('to-right')) {
                $(this).removeClass('to-right');
                $(this).addClass('to-left');
                $.cookie('editor-panel-place', 'right', {path: "/"});
            } else {
                $(this).addClass('to-right');
                $.cookie('editor-panel-place', 'left', {path: "/"});
            }
	});

	// checkboxes, selectboxes customized
	$(".map-editor input:checkbox, .map-editor input:radio").checkbox();
	$(".map-editor input:checkbox, .map-editor input:radio").css("display", "none");
        $("select").selectBox();

	//nice popup
	var config = {
            over: function() {
                $(this).addClass('hovered');
                var popup_height;

                if ($('a.top.add', $(this)).length || $('a.top.expand', $(this)).length) {
                    popup_height = $('.popup-info', $(this).parent()).height() + 17;
                } else {
                    popup_height = $('.popup-info', $(this).parent()).height();
                }

                $('.popup-info', $(this).parent()).css('top', '-' + (popup_height) + 'px');
            },
            timeout: 0, // number = milliseconds delay before onMouseOut
            out: function() {
                $(this).removeClass('hovered');
            }
	};

	$('.page-wraper').hoverIntent(config);

	accordionHeight();
        mapContainerWidth();
        updateSliders();

        $(window).resize(function() {
            accordionHeight();
            mapContainerWidth();
            updateSliders();
        });
    }
}

function accordionHeight() {
  var a = $(window).height();
  //var b = $('.page-panel').height();
  var c = $('.top-panel').height();
  var needed_height = a - c;
  $('.side-panel').css('height', (needed_height) + 'px');
}

function mapContainerWidth() {
    var x = $(window).width();
    var needed_width = x - 360 - 30 + 11;
    var z = $(window).height();
    var y = $('.top-panel').height();
    var needed_height = z - y;
    $('#page-map-wrap-a').css('width', needed_width);
    $('#page-map-wrap-a').css('height', needed_height - 14);
}

function updateSliders() {
    var containerWidth = $('#page-map-wrap-a').width();
    var containerHeight = $('#page-map-wrap-a').height();

    var mapWidth = $('#page-map-wrap-b').width();
    var mapHeight = $('#page-map-wrap-b').height();

    if (mapWidth > containerWidth) {
        $('#slider-h').width(containerWidth - 45 * 2)
        .css('top', containerHeight - 25)
        .css('left', 45)
        .show()
        .slider('value', (-1 * parseInt($('#page-map-wrap-b').css('left')))
            * 100 / (mapWidth - containerWidth));
    } else {
        $('#slider-h').hide();
    }

    if (mapHeight > containerHeight) {
        $('#slider-v').height(containerHeight - 45 * 2)
        .css('top', 45)
        .css('left', containerWidth - 25)
        .show()
        .slider('value', 100 - ((-1 * parseInt($('#page-map-wrap-b').css('top')))
            * 100 / (mapHeight - containerHeight)));
    } else {
        $('#slider-v').hide();
    }
}

function initMapDragging() {
    $("#slider-v").slider({
        orientation: "vertical",
        slide: function(event, ui) {
            var containerHeight = $('#page-map-wrap-a').height();
            var mapHeight = $('#page-map-wrap-b').height() + 22;

            $('#page-map-wrap-b').css('top',
                    -1 * ((mapHeight - containerHeight) * ((100 - ui.value) / 100)) );
        }})
        .css('position', 'absolute')
        .css('z-index', 100);

    $("#slider-h").slider({
        slide: function(event, ui) {
            var containerWidth = $('#page-map-wrap-a').width();
            var mapWidth = $('#page-map-wrap-b').width() + 22;

            $('#page-map-wrap-b').css('left',
                    -1 * ((mapWidth - containerWidth) * (ui.value / 100)));
        }})
        .css('position', 'absolute')
        .css('z-index', 100);

    var ui = $('#page-map-wrap-b');
    var container = $('#page-map-wrap-a');
    var drag = false;
    var current = {left: 0, top: 0};
    var original = {left: 0, top: 0};
    var client = {x:0, y:0};

    $('#page-map-wrap-a').mousedown(function(event) {
        drag = true;

        var left = ui.css('left');
        var top = ui.css('top');
        left = parseInt(left.substr(0, left.length - 2));
        top = parseInt(top.substr(0, top.length - 2));

        original.left = left;
        original.top = top;

        current.left = left;
        current.top = top;

        client.x = event.clientX;
        client.y = event.clientY;

    }).mouseup(function(event) {
        drag = false;
//    }).mouseout(function(event) {
//        if (!drag) return;
//
//        var id = $(event.relatedTarget).attr('id');
//        var element = $(event.relatedTarget);
//
//        if (id == 'page-map-wrap-a'
//            || id == 'page-map-wrap-c'
//            || id == 'page-map-wrap-b'
//            || id == 'slider-v'
//            || id == 'slider-h'
//            || element.is('a')
//            || element.is('td')
//            || element.hasClass('page-inner')) {
//            return;
//        }
//
////        expand right
////
//        console.log(element);
//
//        drag = false;
    }).mousemove(function(event) {
        if (!drag) return;

        var left = ui.css('left');
        var top = ui.css('top');
        left = parseInt(left.substr(0, left.length));
        top = parseInt(top.substr(0, top.length));

        current.left = left;
        current.top = top;

        var x = event.clientX - client.x;
        var y = event.clientY - client.y;

        var ui_height = ui.height() + 22;
        var container_height = container.height();

        var ui_width = ui.width() + 22;
        var container_width = container.width();

        if (y) {
            if (ui_height < container_height) {
                if (y < 0) {
                    if (top + y > 0) {
                        ui.css('top', top + y);
                    } else {
                        ui.css('top', 0);
                    }
                } else {
                    if (top + y + ui_height <= container_height) {
                        ui.css('top', top + y);
                    } else {
                        ui.css('top', container_height - ui_height);
                    }
                }
            } else {
                if (y < 0) {
                    if (top + y + ui_height <= container_height) {
                        ui.css('top', container_height - ui_height);
                    } else {
                        ui.css('top', top + y);
                    }
                } else {
                    if (top + y > 0) {
                        ui.css('top', 0);
                    } else {
                        ui.css('top', top + y);
                    }
                }
            }
        }

        if (x) {
            if (ui_width < container_width) {
                if (x < 0) {
                    if (left + x > 0) {
                        ui.css('left', left + x);
                    } else {
                        ui.css('left', 0);
                    }
                } else {
                    if (left + x + ui_width <= container_width) {
                        ui.css('left', left + x);
                    } else {
                        ui.css('left', container_width - ui_width);
                    }
                }
            } else {
                if (x < 0) {
                    if (left + x + ui_width <= container_width) {
                        ui.css('left', container_width - ui_width);
                    } else {
                        ui.css('left', left + x);
                    }
                } else {
                    if (left + x > 0) {
                        ui.css('left', 0);
                    } else {
                        ui.css('left', left + x);
                    }
                }
            }
        }

        client.x = event.clientX;
        client.y = event.clientY;

        updateSliders();
    });
}

function watermarkInit() {
  setTimeout(function(){
    $("#description")
    .not("input.processed, input.watermarkPluginCustomClass, textarea.watermarkPluginCustomClass, input:file, input.form-submit, input:hidden")
    .watermark({watermarkCssClass: 'form-textarea'})
    .addClass("processed");
  }, 200);
}

function toggleFieldset(el) {
  var fieldset = $(el).parents('fieldset');
  fieldset.toggleClass('collapsed');
  $('div', el).toggle('slide', {duration:0.2});
}

function ucfirst(str,force){
    str=force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/,
        function(firstLetter){
            return   firstLetter.toUpperCase();
        });
}