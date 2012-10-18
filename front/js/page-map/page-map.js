/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(function() {
    $('.add').unbind('click');
    $('.add').bind('click', pageMap, pageMap.showTemplateDialog);
    $('#page-map div.page-inner').bind('click', pageMap, pageMap.selectPage);
    $('.page-map-main').unbind().bind('click', pageMap.unSelectPage);

    pageMap.unSelectPage();
    window.addFileToElement.init();
    window.tocEditor.init();
    window.pdfPageEditor.init();

    window.editor.init();

    window.initMapDragging();
    window.updateSliders();

    $('#page-map td[class^="jumper-"]').bind('click', pageMap, pageMap.showTemplateDialog);
    $('a.expand').click(function() {
        $('div.page-inner', $(this).closest('td')).click();
    });

    if (window.location.hash.split('_').shift() == '#page') {
        pageMap.getPage(window.location.hash.split('_').pop());
    } else {
        pageMap.setPageMapWrapperPosition();
    }
});

var pageMap = {
    LEFT:   'left',
    RIGHT:  'right',
    TOP:    'top',
    BOTTOM: 'bottom',

    VERTICAL:   'vertical',
    HORIZONTAL: 'horizontal',

    ADD_VERTICAL:   'add_vertical',
    ADD_HORIZONTAL: 'add_horizontal',

    AVERAGE_PAGE_SIZE_HEIGHT: 62.5,
    AVERAGE_PAGE_SIZE_WIDTH: 54,
    MARGIN: 50,

    is_new: false,

    addPage: function (targetPid, tid, type, obj, between, $this) {
        switch (type) {
            case $this.LEFT:
            case $this.RIGHT:
                $this.type = $this.ADD_VERTICAL;
                break;
            case $this.TOP:
            case $this.BOTTOM:
                $this.type = $this.ADD_HORIZONTAL;
                break;
        }
        $.ajax({
            url: '/page-map/add-page',
            type: 'POST',
            dataType: 'json',
            data: {
                pid: targetPid,
                tid: tid,
                rid: document.rid,
                type: type,
                between: between
            },
            success: function(data) {
                try {
                    if (!data.success) {
                        return alert(data.message);
                    } else {
                        $('#dialog').dialog('close');

                        var dataObj = {};
                        var tmp = {};
                        dataObj = {pid: data.pid, pageObj: data.page};

                        tmp.next = $this.getNextObject(dataObj.pageObj);
                        tmp.content = $this.createPage(tmp.next, dataObj, true);

                        $('#page-map').html('<tr><td class="page" background="' + data.page.thumbnailUri + '">' +
                                            tmp.content + '</td></tr>');
                        $this.is_new = true;
                        $('#page-map div.page-inner').bind('click', $this, $this.selectPage);
                        $('#page-map div.page-inner').click();
                    }
                } catch (e) {
                    window.ui.log(e);
                    return alert(translate('unexpected_ajax_error'));
                }
            }
        }, $('#dialog'));
    },

    changePageTemplate: function(pageId, templateId) {
        $.ajax({
            url: '/page-map/change-page-template',
            type: 'POST',
            dataType: 'json',
            data: {
                pageId: pageId,
                templateId: templateId
            },
            success: function(data) {
                try {
                    if (!data.success) {
                        return alert(data.message);
                    } else {
                        $('#page-map tr td.page-selected').attr('background', data.thumbnailUri);
                        $("#page-refresh-button").click();
                    }
                    $('#dialog').dialog('close');
                } catch (e) {
                    window.ui.log(e);
                    return alert(translate('unexpected_ajax_error'));
                }
            }
        }, $('#dialog'));
    },

    setPageMapWrapperPosition: function(pid) {
        var $this = this;

        setTimeout(function() {
            var middle = {};
            var tmp;

            middle.top = $('#page-map-wrap-a').height() / 2 - $('#page-map-wrap-b').height() / 2 + 'px';
            middle.left = $('#page-map-wrap-a').width() / 2 - $('#page-map-wrap-b').width() / 2 + 'px';

            if (pid) {
                var pageEq = $this.getEqObject(pid);
                var pageMapEq = {};

                pageEq.tr++;
                pageEq.td++;
                pageEq.td = pageEq.td * $this.AVERAGE_PAGE_SIZE_WIDTH;
                pageMapEq.tr = $('#page-map tr').length;
                pageMapEq.td = $('#page-map-wrap-a').width();

                if ($('#page-map-wrap-b').height() > $('#page-map-wrap-a').height()) {
                    if (pageEq.tr < pageMapEq.tr / 2) {
                        $('#page-map-wrap-b').css('top', '0px');
                    } else if (pageEq.tr > pageMapEq.tr / 2) {
                        tmp = $('#page-map-wrap-b').height() - $('#page-map-wrap-a').height() + $this.MARGIN;
                        $('#page-map-wrap-b').css('top', -tmp + 'px');
                    } else {
                        $('#page-map-wrap-b').css('top', middle.top);
                    }
                } else {
                    $('#page-map-wrap-b').css('top', middle.top);
                }

                if ($('#page-map-wrap-b').width() > $('#page-map-wrap-a').width()) {
                    if (pageEq.td < pageMapEq.td / 2) {
                        $('#page-map-wrap-b').css('left', '0px');
                    } else if (pageEq.td > pageMapEq.td / 2) {
                        tmp = $('#page-map-wrap-b').width() - $('#page-map-wrap-a').width() + $this.MARGIN;
                        $('#page-map-wrap-b').css('left', -tmp + 'px');
                    } else {
                        $('#page-map-wrap-b').css('left', middle.left);
                    }
                } else {
                    $('#page-map-wrap-b').css('left', middle.left);
                }

            } else {
                $('#page-map-wrap-b').css('left', middle.left);
                $('#page-map-wrap-b').css('top', middle.top);
            }

            if ($.browser.msie && $.browser.version.substr(0,1)<9){
                $('#page-map-wrap-b').css('opacity', '');
            }
            else{
                $('#page-map-wrap-b').css('opacity', 1);
            }
        }, 200);
    },

    getPage: function(pid) {
        var $this = this;

        $.ajax({
            url: '/page-map/get-page',
            type: 'POST',
            dataType: 'json',
            data: {
                pid: pid,
                rid: document.rid
            },
            success: function(data) {
                try {
                    if (!data.success) {
                        return alert(data.message);
                    } else {
                        var dataObj = {};
                        var tmp = {};
                        dataObj = {pid: data.pid, pageObj: data.page};

                        tmp.next = $this.getNextObject(dataObj.pageObj);
                        tmp.content = $this.createPage(tmp.next, dataObj, true);

                        $('#page-map').html('');
                        $('#page-map').html(
                          '<tbody><tr><td class="page" background="' + data.page.thumbnailUri + '">' +
                          tmp.content + '</td></tr></tbody>'
                        );
                        $('#page-map div.page-inner').data('positioning', true);
                        $('#page-map div.page-inner').bind('click', $this, $this.selectPage);
                        $('#page-map div.page-inner').click();
                    }
                } catch (e) {
                    window.ui.log(e);
                    return alert(translate('unexpected_ajax_error'));
                }
            }
        });
    },

    createPage: function(next, data, type) {
        var has     = this._getHasObject(data.pageObj);
        var pageObj = $('#pageTemplate').clone();

        $('div.page-inner', pageObj).text('[' + data.pid + ']').attr('id', 'page-' + data.pid);
        $('div.page-inner', pageObj).attr('id', 'page-' + data.pid);
        $('span.titre', pageObj).html(data.pageObj.tpl_title);
        $('span.page-name', pageObj).html('<b>' + translate('title') + '</b>' + ': ' + data.pageObj.title);
        $('span.cat-name', pageObj).html('<b>#ID</b>: ' + data.pid);

        if (has.left && type != this.RIGHT) {
            if (!next.has_left) {
                $('div.horiz-expand:eq(0)', pageObj).html(this._getAddButton(data.pid, this.LEFT));
                $('div.horiz-expand:eq(0)', pageObj).removeClass('horiz-dots');
            } else if (type && type != this.LEFT) {
                $('div.horiz-expand:eq(0)', pageObj).html(this._getExpandButton(data.pid, this.LEFT));
                $('div.horiz-expand:eq(0)', pageObj).addClass('horiz-dots');
            }
        }

        if (has.right && type != this.LEFT) {
            if (!next.has_right) {
                $('div.horiz-expand:eq(1)', pageObj).html(this._getAddButton(data.pid, this.RIGHT));
                $('div.horiz-expand:eq(1)', pageObj).removeClass('horiz-dots');
            } else if (type && type != this.RIGHT) {
                $('div.horiz-expand:eq(1)', pageObj).html(this._getExpandButton(data.pid, this.RIGHT));
                $('div.horiz-expand:eq(1)', pageObj).addClass('horiz-dots');
            }
        }

        if (has.bottom && type != this.TOP) {
            if (!next.has_bottom) {
                $('div.vert-expand:eq(1)', pageObj).html(this._getAddButton(data.pid, this.BOTTOM));
                $('div.vert-expand:eq(1)', pageObj).removeClass('vert-dots');
            } else if (type && type != this.BOTTOM) {
                $('div.vert-expand:eq(1)', pageObj).html(this._getExpandButton(data.pid, this.BOTTOM));
                $('div.vert-expand:eq(1)', pageObj).addClass('vert-dots');
            }
        }

        if (has.top && type != this.BOTTOM) {
            if (!next.has_top) {
                $('div.vert-expand:eq(0)', pageObj).html(this._getAddButton(data.pid, this.TOP));
                $('div.vert-expand:eq(0)', pageObj).removeClass('vert-dots');
            } else if (type && type != this.TOP) {
                $('div.vert-expand:eq(0)', pageObj).html(this._getExpandButton(data.pid, this.TOP));
                $('div.vert-expand:eq(0)', pageObj).addClass('vert-dots');
            }
        }

        return pageObj.html();
    },

    _getAddButton: function(pid, type) {
        return '<a class="add ' + type + '" id="' + type + '-' + pid + '" href="#">#</a>';
    },

    _getExpandButton: function(pid, type) {
        return '<a class="expand ' + type + '" id="' + type + '-' + pid + '" href="#">#</a>';
    },

    getNextObject: function(pageObj) {
        var next = {};

        next.has_bottom = pageObj.has_bottom;
        next.has_right  = pageObj.has_right;
        next.has_left   = pageObj.has_left;
        next.has_top    = pageObj.has_top;

        return next;
    },

    _getHasObject: function(pageObj) {
        var has = {};

        has.right  = pageObj.tpl.has_right  == 1 ? true : false;
        has.left   = pageObj.tpl.has_left   == 1 ? true : false;
        has.bottom = pageObj.tpl.has_bottom == 1 ? true : false;
        has.top    = pageObj.tpl.has_top    == 1 ? true : false;

        return has;
    },

    setDataObj: function(data, type) {
        var tmp;

        switch (type) {
            case this.LEFT:tmp = data.pageObj.left;break;
            case this.RIGHT:tmp = data.pageObj.right;break;
            case this.TOP:tmp = data.pageObj.top;break;
            case this.BOTTOM:tmp = data.pageObj.bottom;break;
        }

        data.targetPid = data.pid;
        data.pageObj   = this._getPageListObj(data.pageList, tmp);
        data.pid       = tmp;
    },

    getContent: function(next, data, type) {
        var html = '', count = {}, tmp = 0, title;

        title = this.createPage(next, data, type);
        count.before = 0;

        this._getTd(data.targetPid).prevAll().each(function() {
            if ($(this).is(':visible')) {count.before++;}
        });

        $('td', this.getTr(data.targetPid)).each(function() {
            if ($(this).is(':visible')) {tmp++;}
        });

        count.after = tmp - count.before - 1;

        if (type == this.TOP) {
            html = this._getPageInner(count, title, data.pageObj.thumbnailUri) + this._getJumper(count, data, type);
        } else if (type == this.BOTTOM) {
            html = this._getJumper(count, data, type, next) + this._getPageInner(count, title, data.pageObj.thumbnailUri);
        }

        return html;
    },

    _getJumper: function(count, data, type, next) {

        var type_old = type;

        type = !data.pageObj['jumper_' + this._getReverseType(type)] ? type : data.pageObj['jumper_' + this._getReverseType(type)];

        var html = '', counter = 0, _class;

        html += '<tr class="tr-jumper-' + data.pid + '">';

        while (counter < count.before) {
            html += '<td></td>';
            counter++;
        }

        _class = type == this.TOP ? 'up' : 'down';

        var jumper_id = data.pageObj[this._getReverseType(type)];
        if (data.is_new && type_old != type) {
            jumper_id = data.pid;
        }
        html += '<td class="jumper-' + jumper_id + '-' + type + '"><label class="down-line ' + _class + '">|</label></td>';
        counter = 0;

        while (counter < count.after) {
            html += '<td></td>';
            counter++;
        }

        html += '</tr>';

        return html;
    },

    _getPageInner: function(count, title, thumbnail) {
        var html = '', counter = 0;

        html += '<tr>';

        while (counter < count.before) {
            html += '<td></td>';
            counter++;
        }

        html += '<td class="page" background="' + thumbnail + '">' + title + '</td>';
        counter = 0;

        while (counter < count.after) {
            html += '<td></td>';
            counter++;
        }

        html += '</tr>';

        return html;
    },

    _getTd: function(pid) {
        return $('#page-' + pid).closest('td');
    },

    getTr: function(pid) {
        return $('#page-' + pid).closest('tr');
    },

    getEqObject: function(pid) {
        var eq = {};

        eq.td = this._getTd(pid).prevAll().length;
        eq.tr = this.getTr(pid).prevAll().length;

        return eq;
    },

    _getReverseType: function(type) {
        var reverseType;

        switch(type) {
            case this.LEFT:reverseType = this.RIGHT;break;
            case this.RIGHT:reverseType = this.LEFT;break;
            case this.TOP:reverseType = this.BOTTOM;break;
            case this.BOTTOM:reverseType = this.TOP;break;
        }

        return reverseType;
    },

    getDataObj: function(data, pid, pageObj, currentPid, type, is_new) {
        var dataObj = {};

        dataObj.targetPid = pid;
        dataObj.pageList = data[type];
        dataObj.pageObj = pageObj;
        dataObj.pid = currentPid;
        dataObj.initialPid = pid;
        dataObj.is_new = is_new;

        return dataObj;
    },

    checkLinks: function(eq, pid, vertical) {
        var obj;

        if (vertical) {
            obj = this.getTr(pid);

            if ($('td:eq(' + eq.td + ')', obj.prev()).is('td[class^="jumper-"]')) {
                $('div.vert-expand:eq(0)', $('td:eq(' + eq.td + ')', obj)).html('<a href="#" id="top-' + pid + '" class="expand top">#</a>');
                $('div.vert-expand:eq(0)', $('td:eq(' + eq.td + ')', obj)).addClass('vert-dots');
            }

            if ($('td:eq(' + eq.td + ')', obj.next()).is('td[class^="jumper-"]')) {
                $('div.vert-expand:eq(1)', $('td:eq(' + eq.td + ')', obj)).html('<a href="#" id="bottom-' + pid + '" class="expand bottom">#</a>');
                $('div.vert-expand:eq(1)', $('td:eq(' + eq.td + ')', obj)).addClass('vert-dots');
            }
        } else {
            obj = this._getTd(pid);

            if (obj.prev().is('td[class^="jumper-"]')) {
                $('div.horiz-expand:eq(0)', obj).html('<a href="#" id="left-' + pid + '" class="expand left">#</a>');
                $('div.horiz-expand:eq(0)', obj).addClass('horiz-dots');
            }

            if (obj.next().is('td[class^="jumper-"]')) {
                $('div.horiz-expand:eq(1)', obj).html('<a href="#" id="right-' + pid + '" class="expand right">#</a>');
                $('div.horiz-expand:eq(1)', obj).addClass('horiz-dots');
            }
        }
    },

    showChangeTemplateDialog: function (pageId) {
        var context = this;
        $.ajax({
            url: '/page-map/get-templates',
            type: 'POST',
            dataType: 'html',
            data: {
                pid: pageId
            },
            success: function(data) {
                $('#dialog').dialog('destroy');
                $('#dialog').html(data);

                $('a', '#template-list').click(function() {
                    var tamplateId = $(this).attr('id').split('-').pop();
                    context.changePageTemplate(pageId, tamplateId);
                });

                $('#dialog').dialog({
                    resizable: false,
                    height: 'auto',
                    width: 'auto',
                    modal: true,
                    title: translate('choose_template')
                });
            }
        });
    },

    showTemplateDialog: function(event) {
        if ($(event.currentTarget).hasClass('prevent-select')) {
            $(event.currentTarget).removeClass('prevent-select');
            return;
        }

        if ($('label', event.currentTarget).hasClass('prevent-select')) {
            $('label', event.currentTarget).removeClass('prevent-select');
            return;
        }

        var pid, type, between = 0;
        var $this = event.data;
        var obj = event.currentTarget;
        var splitAttrClass = $(obj).attr('class').split(' ').shift().split('-');

        if (splitAttrClass.shift() == 'jumper') {
            pid  = splitAttrClass.shift();
            type = splitAttrClass.shift();
            obj = $('#page-' + pid);
            between = 1;
        } else {
            var splitAttrId    = $(obj).attr('id').split('-');
            pid  = splitAttrId.pop();
            type = splitAttrId.shift();
        }

        $.ajax({
            url: '/page-map/get-templates',
            type: 'POST',
            dataType: 'html',
            data: {
                pid: pid,
                type: type
            },
            success: function(data) {
                $('#dialog').dialog('destroy');
                $('#dialog').html(data);

                $('a', '#template-list').click(function() {
                    var tid = $(this).attr('id').split('-').pop();

                    $this.addPage(pid, tid, type, obj, between, $this);
                });

                $('#dialog').dialog({
                    resizable: false,
                    height: 'auto',
                    width: 'auto',
                    modal: true,
                    title: translate('choose_template')
                });
            }
        });
    },

    selectPage: function(event) {
        if ($(event.currentTarget).hasClass('prevent-select')) {
            $(event.currentTarget).removeClass('prevent-select');
            return;
        }

        event.stopPropagation();

        var current = {};
        var initial = {};
        var tmpObj;
        var selectedObj = $('td.page-selected', '#page-map'), type = 'both';
        var $this = event.data;

        current.pid = $(event.currentTarget).attr('id').split('-')[1];
        current.eq = $this.getEqObject(current.pid);
        current.action = $(event.currentTarget).data('action');
        current.is_new = $(event.currentTarget).data('is_new');

        if (typeof current.is_new !== 'undefined') {
            $this.is_new = current.is_new;
        }

        window.location.hash = 'page_' + current.pid;

        current.top = parseInt($('#page-map-wrap-b').css('top')) +
          current.eq.tr * $this.AVERAGE_PAGE_SIZE_HEIGHT;
        current.left = parseInt($('#page-map-wrap-b').css('left')) +
          current.eq.td * $this.AVERAGE_PAGE_SIZE_WIDTH;

        if (!$(event.currentTarget).data('editor')) {
            if (selectedObj.length) {
                initial.pid = $('div.page-inner', selectedObj).attr('id').split('-')[1];
                initial.eq  = $this.getEqObject(initial.pid);

                if (current.action != 'deleted') {
                    if (current.eq.tr == initial.eq.tr) {
                        type = $this.VERTICAL;
                        $this.checkLinks(initial.eq, initial.pid, true);

                        tmpObj = $('tr:eq(' + initial.eq.tr + ')', '#page-map');
                        tmpObj.prevAll().remove();
                        tmpObj.nextAll().remove();
                    } else {
                        type = $this.HORIZONTAL;
                        $this.checkLinks(initial.eq, initial.pid, false);

                        $('tr', '#page-map').each(function() {
                            tmpObj = $('td:eq(' + initial.eq.td + ')', $(this));
                            tmpObj.prevAll().remove();
                            tmpObj.nextAll().remove();
                        });
                    }
                } else {
                    tmpObj = $('tr:eq(' + current.eq.tr + ')', '#page-map');
                    tmpObj.prevAll().remove();
                    tmpObj.nextAll().remove();

                    $('tr', '#page-map').each(function() {
                        tmpObj = $('td:eq(' + current.eq.td + ')', $(this));
                        tmpObj.prevAll().remove();
                        tmpObj.nextAll().remove();
                    });
                }

                $('#page-map-wrap-b').addClass('hidden-page');
                selectedObj.removeClass('page-selected');
            } else {
                type = $this.type;
            }

            $.ajax({
                url: '/page-map/full-expand',
                type: 'POST',
                dataType: 'json',
                data: {
                    pid: current.pid,
                    rid: document.rid,
                    type: type
                },
                success: function(data) {
                    if (data.result == true) {
                        if (current.action == 'deleted') {
                            var tmpData = {pid: current.pid, pageObj: data.page};
                            $(event.currentTarget).closest('td').html($this.createPage($this.getNextObject(data.page), tmpData, false));
                        }

                        if ($(event.currentTarget).data('positioning')) {
                            $(event.currentTarget).data('positioning', false);

                            var count = 0;

                            $.each(data[$this.LEFT], function(key, value) {
                                count++;
                            });

                            $this.setPageMapWrapperPosition(current.pid);
                        }

                        expand.invoke(data, current.pid, type, $this.is_new);
                        $('#page-map-wrap-b').removeClass('hidden-page');

                        current.eq = $this.getEqObject(current.pid);

                        switch (type) {
                            case $this.VERTICAL:
                                break;
                            case $this.HORIZONTAL:
                                current.new_left = $('#page-map-wrap-a').width() / 2 - current.eq.td * $this.AVERAGE_PAGE_SIZE_WIDTH;
                                $('#page-map-wrap-b').css('left', current.new_left + 'px');
                                break;
                            case $this.ADD_VERTICAL:
                                current.new_left = parseInt($('#page-map-wrap-b').css('left')) - 2 * $this.AVERAGE_PAGE_SIZE_WIDTH;
                                $('#page-map-wrap-b').css('left', current.new_left + 'px');
                                break;
                            case $this.ADD_HORIZONTAL:
                                current.new_left = $('#page-map-wrap-a').width() / 2 - current.eq.td * $this.AVERAGE_PAGE_SIZE_WIDTH;
                                $('#page-map-wrap-b').css('left', current.new_left + 'px');
                                current.new_top = parseInt($('#page-map-wrap-b').css('top')) - $this.AVERAGE_PAGE_SIZE_HEIGHT;
                                $('#page-map-wrap-b').css('top', current.new_top + 'px');
                                break;
                        }

                        current.td = $this._getTd(current.pid);
                        current.td.addClass('page-selected');
                        $('a.expand', current.td).remove();

                        $('div.page-inner', '#page-map').unbind('click');
                        $('div.page-inner', '#page-map').bind('click', $this, $this.selectPage);
                        $('td[class^="jumper-"]', '#page-map').bind('click', $this, $this.showTemplateDialog);
                        $('a.add').unbind('click');
                        $('a.add').bind('click', $this, $this.showTemplateDialog);
                    }
                }
            });
        }

        $(event.currentTarget).data('editor', false);
        $(event.currentTarget).data('action', false);
        updateSliders();

        $this.showEditor(current.pid);
    },

    unSelectPage: function() {
        $('#page-editor').hide();
    },

    rebindFields: function() {
        window.bindPageDelete();
        window.bindRefreshEditor();
        window.bindChangeTemplateEditor();
        window.bindIframeDialog();

        window.editor.init();
        window.pageInfo.init();

        window.fieldBackground.init();
        window.fieldSlide.init();
        window.fieldBody.init();
        window.fieldMiniArt.init();
        window.fieldScrollPane.init();
        window.fieldVideo.init();
        window.fieldHtml.init();
        window.fieldGallery.init();
        window.fieldSound.init();
        window.fieldAdvert.init();
        window.fieldDragAndDrop.init();
        window.fieldPopup.init();
        window.fieldHtml5.init();
        window.fieldGames.init();
        window.field3d.init();

        //window.smartInput.init();
        $('a.expand').unbind('click');
        $('a.expand').click(function() {
            $('div.page-inner', $(this).closest('td')).click();
        });

        updateSliders();
    },

    showEditor: function(pid) {
        var $this = this;
        $('select').selectBox('destroy');

        $.ajax({
            url: '/editor/show/',
            dataType: 'html',
            data: {
                pid: pid
            },
            type: 'POST',
            success: function(data) {
                try {
                    if (data.status != undefined && !data.status) {
                        return alert(data.message);
                    } else {
                        $('.page-editor-main').html(data);
                        $this.rebindFields();
                    }
                } catch (e) {
                    window.ui.log(e);
                    return alert(translate('unexpected_ajax_error'));
                }
            },
            error: function() {
                window.ui.log(e);
                alert(translate('unexpected_ajax_error'));
            }
        });
    },

    _getPageListObj: function(pageList, pid) {
      var result = null;

      $.each(pageList, function(key, value) {
        if (value.id == pid) {
          result = value;
          return false;
        }
      });

      return result;
    }
}