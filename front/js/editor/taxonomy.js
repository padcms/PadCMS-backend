/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
window.taxonomy = {

    bindDeleteTag: function() {
        $('img', '#page-tag-list').bind('click', this, function(event) {
            event.data.onDeleteTag(event.originalEvent);
        });
    },

    onAddTag: function() {
        var context = this;
        var tag = $('#page-tag-input').val().trim();
        if (!tag) return;
        $.ajax({
            url: '/editor/page-ajax',
            type: 'POST',
            dataType: 'json',
            data: {
                form: 'pageInfo',
                pid: document.pid,
                tag: tag
            },
            success: function(data) {
                context.showPageTags(data.tags);
                $('#page-tag-input').val('');
            }
        });
    },

    onDeleteTag: function(event) {
        var tid = $(event.currentTarget).parent().attr('id').split('-').pop();
        if (!tid) return;
        var context = this;
        $.ajax({
            url: '/editor/delete-tag',
            type: 'POST',
            dataType: 'json',
            data: {
                pid: document.pid,
                tid: tid
            },
            success: function(data) {
                context.showPageTags(data.tags);
            }
        });
    },

    showPageTags: function(tags) {
        tagsList = $('#page-tag-list').empty();
        $.each(tags, function(index, value) {
            tagsList.append('<div id="term-' + value.id + '">' + value.title + '<img src="/img/editor/delete-btn.gif" alt="" /></div>');
        });
        this.bindDeleteTag();
    },

    onSaveTitle: function() {
        var title = $('#page-title-input').val().trim();
        if (!title) return;
        $.ajax({
            url: '/editor/page-ajax',
            type: 'POST',
            dataType: 'json',
            data: {
                form: 'pageInfo',
                pid: document.pid,
                title: title
            },
            success: function(data) {
                if (data.controls)
                    $('#page-title-input').val(data.controls.title.value);
            }
        });
    },

    onSaveToc: function() {
        var toc = $('#page-toc-select').val();
        if (!toc) return;
        $.ajax({
            url: '/editor/page-ajax',
            type: 'POST',
            dataType: 'json',
            data: {
                form: 'pageInfo',
                pid: document.pid,
                toc: toc
            }
        });
    }

};
