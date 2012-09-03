/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var expand = {
    invoke: function (data, pid, type, is_new) {
        var $this = this;

        if (type != pageMap.HORIZONTAL) {
            $.each(data[pageMap.BOTTOM], function(currentPid, pageObj) {
                $this._expandBottomBranch(pageMap.getDataObj(data, pid, pageObj, pageObj.id, pageMap.BOTTOM, is_new));
                return false;
            });

            $.each(data[pageMap.TOP], function(currentPid, pageObj) {
                $this._expandTopBranch(pageMap.getDataObj(data, pid, pageObj, pageObj.id, pageMap.TOP, is_new));
                return false;
            });
        }

        if (type != pageMap.VERTICAL) {
            $.each(data[pageMap.RIGHT], function(currentPid, pageObj) {
                $this._expandRightBranch(pageMap.getDataObj(data, pid, pageObj, pageObj.id, pageMap.RIGHT, is_new));
                return false;
            });

            $.each(data[pageMap.LEFT], function(currentPid, pageObj) {
                $this._expandLeftBranch(pageMap.getDataObj(data, pid, pageObj, pageObj.id, pageMap.LEFT, is_new));
                return false;
            });
        }
    },

    _expandBottomBranch: function(data) {
        var html = '', next = {}, dataNext = {};

        next = pageMap.getNextObject(data.pageObj);

        html = pageMap.getContent(next, data, pageMap.BOTTOM);
        pageMap.getTr(data.targetPid).after(html);

        if (next.has_bottom && this._exists(data.pageList, data.pageObj.bottom)) {
            dataNext = $.extend({}, data);
            pageMap.setDataObj(dataNext, pageMap.BOTTOM);
            this._expandBottomBranch(dataNext);
        }
    },

    _expandTopBranch: function(data) {
        var html = '', next = {}, dataNext = {};

        next = pageMap.getNextObject(data.pageObj);

        html = pageMap.getContent(next, data, pageMap.TOP);
        pageMap.getTr(data.targetPid).before(html);

        if (next.has_top && this._exists(data.pageList, data.pageObj.top)) {
            dataNext = $.extend({}, data);
            pageMap.setDataObj(dataNext, pageMap.TOP);
            this._expandTopBranch(dataNext);
        }
    },

    _expandRightBranch: function(data) {
        var html = '', content, next = {}, eq = {}, tmp = {}, dataNext = {};

        next = pageMap.getNextObject(data.pageObj);

        content = pageMap.createPage(next, data, pageMap.RIGHT);
        eq = pageMap.getEqObject(data.targetPid);

        tmp.type = pageMap.RIGHT;
        if (data.pageObj['link_type'] == pageMap.RIGHT || data.pageObj['link_type'] == pageMap.LEFT) {
            tmp.type = data.pageObj['link_type'];
        }

        $('tr:visible', $('#page-map')).each(function() {
            tmp.lgt = $(this).prevAll().length;

            if (tmp.lgt == eq.tr) {
                html = '<td class="jumper-' + data.targetPid + '-' + tmp.type + '"><label class="' + tmp.type + '">' +
                       '</label></td><td class="page" background="' + data.pageObj.thumbnailUri + '">'  + content + '</td>';
            } else {
                html = '<td class="void-' + data.pid + '"></td><td class="void-' + data.pid + '"></td>';
            }

            $('td:eq(' + eq.td + ')', $(this)).after(html);
        });

        if (next.has_right && this._exists(data.pageList, data.pageObj.right)) {
            dataNext = $.extend({}, data);
            pageMap.setDataObj(dataNext, pageMap.RIGHT);
            this._expandRightBranch(dataNext);
        }
    },

    _expandLeftBranch: function(data) {
        var html = '', title, next, eq = {}, tmp = {}, dataNext = {};

        next = pageMap.getNextObject(data.pageObj);

        eq = pageMap.getEqObject(data.targetPid);
        title = pageMap.createPage(next, data, pageMap.LEFT);

        tmp.type = pageMap.RIGHT;
        if (data.pageObj['link_type'] == pageMap.RIGHT || data.pageObj['link_type'] == pageMap.LEFT) {
            tmp.type = data.pageObj['link_type'];
        }
        $('tr:visible', $('#page-map')).each(function() {
            tmp.lgt = $(this).prevAll().length;

            if (tmp.lgt == eq.tr) {
                //Using data.pid to save correct numeration of jumpers
                html = '<td class="page" background="' + data.pageObj.thumbnailUri + '">' + title + '</td>' +
                       '<td class="jumper-' + data.pid + '-' + tmp.type + '"><label class="' + tmp.type + '"></label></td>';
            } else {
                html = '<td class="void-' + data.pid + '"></td><td class="void-' + data.pid + '"></td>';
            }

            $('td:eq(' + eq.td + ')', $(this)).before(html);
        });

        if (next.has_left && this._exists(data.pageList, data.pageObj.left)) {
            dataNext = $.extend({}, data);
            pageMap.setDataObj(dataNext, pageMap.LEFT);
            this._expandLeftBranch(dataNext);
        }
    },

    _exists: function(pageList, pid) {
      var exists = false;

      $.each(pageList, function(key, value) {
        if (value.id == pid) {
          exists = true;
          return false;
        }
      });

      return exists;
    }
}