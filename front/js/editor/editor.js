/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var bindPageDelete = function() {
    $('a.ico.trash').click(function(event) {
        var pid;

        event.preventDefault();

        if ($(event.currentTarget).closest('td').length) {
            pid = $('.page-inner', event.closest('td')).attr('id').split('-').pop();
        } else {
            pid = document.pid;
        }

        if (!confirm(translate('page_delete_confirm'))) {
            return false;
        }

        $.ajax({
            url: '/editor/delete-page',
            dataType: 'json',
            type: 'POST',
            data: {
                pid: pid
            },
            success: function(data) {
                if (data.result != undefined && data.result == true) {
                    $('div.page-inner', pageMap._getTd(data.linkedPid)).data('action', 'deleted');
                    $('div.page-inner', pageMap._getTd(data.linkedPid)).click();
                } else {
                    if (data.message) {
                        alert(data.message);
                    } else {
                        alert(translate('Error. Can\'t delete page'));
                    }
                }
            },
            error: function(e) {
                alert(translate('unexpected_error'));
            }
        });
    });
}

var bindRefreshEditor = function() {
    $("#page-refresh-button").click(function(event) {
        event.stopPropagation();
        $('#page-' + document.pid).data('editor', true);
        $('#page-' + document.pid).click();
    });
}

var bindChangeTemplateEditor = function() {
    $("#page-change-template-button").click(function(event) {
        //.bind('click', pageMap, pageMap.showTemplateDialog);
        event.stopPropagation();
        pageMap.showChangeTemplateDialog(document.pid);
    });
}

var tocEditor = {
    init: function() {
        var $this = this;

        $('#toc-dialog').dialog({
            title: translate('toc_edit'),
            autoOpen: false,
            resizable: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            open: function(event, ui) {
                if ($('#toc-tabs').tabs('option', 'selected') == 1) {
                    $this.currentTree.onOpen(event, ui);
                } else {
                    $this.permanentTree.onOpen(event, ui);
                }
            },
            close: function(event, ui) {
                $('#toc-current-edit-item').hide();

                $('#toc-permanent-tree').empty();
                $('#toc-current-tree').empty();

                $('#toc-permanent-create').addClass('disabled');
                $('#toc-permanent-edit').addClass('disabled');
                $('#toc-permanent-delete').addClass('disabled');
                $('#toc-current-create').addClass('disabled');
                $('#toc-current-edit').addClass('disabled');
                $('#toc-current-delete').addClass('disabled');

                $this.currentTree.onClose(event, ui);
            }
        });

        $("#toc-tabs").tabs({
            selected: 1,
            select: function(event, ui) {
                if (ui.index == 1) {
                    $this.currentTree.onOpen(event, ui);
                    $('#toc-permanent-create').addClass('disabled');
                    $('#toc-permanent-edit').addClass('disabled');
                    $('#toc-permanent-delete').addClass('disabled');
                    $('#toc-permanent-tree').empty();
                } else {
                    $this.permanentTree.onOpen(event, ui);
                    $('#toc-current-create').addClass('disabled');
                    $('#toc-current-edit').addClass('disabled');
                    $('#toc-current-delete').addClass('disabled');
                    $('#toc-current-tree').empty();
                }
            }
        });

        $this.currentTree.init();
        $this.permanentTree.init();
    }
}

tocEditor.currentTree = {

    selectedItemId: null,
    uploaderStripe: null,
    uploaderSummary: null,

    init: function() {
        var $this = this;

        $('#toc-current-create').click(function() {
            if ($(this).hasClass('disabled')) return;
            $('#toc-current-tree').jstree('create');
        });

        $('#toc-current-edit').click(function(){
            if ($(this).hasClass('disabled')) return;
            $('#toc-current-tree').jstree('rename');
        });

        $('#toc-current-delete').click(function(){
            if ($(this).hasClass('disabled')) return;
            if (!confirm(translate('delete_confirm'))) return;
            $('#toc-current-tree').jstree('remove');
        });

        $('#toc-current-title-input').keypress(function(event) {
            if (event.which == 13) $this.onSave('title', $(event.originalEvent.target).val());
        });
        $('#toc-current-title-btn').click(function() {
            $this.onSave('title', $('#toc-current-title-input').val());
            return false;
        });

        $('#toc-current-description-input').keypress(function(event) {
            if (event.which == 13) $this.onSave('description', $(event.originalEvent.target).val());
        });
        $('#toc-current-description-btn').click(function() {
            $this.onSave('description', $('#toc-current-description-input').val());
            return false;
        });

        $('#toc-current-color-input').keypress(function(event) {
            if (event.which == 13) $this.onSave('color', $(event.originalEvent.target).val());
        });
        $('#toc-current-color-btn').click(function() {
            $this.onSave('color', $('#toc-current-color-input').val());
            return false;
        });

        $('#toc-current-stripe-delete').bind('click', $this, function(event) {
            event.data.onDeleteStripe(event.originalEvent);
            return false;
        });

        $('#toc-current-summary-delete').bind('click', $this, function(event) {
            event.data.onDeleteSummary(event.originalEvent);
            return false;
        });

        $this.rid = $('#toc-dialog').dialog('option', 'page').context.rid;

        $('input.resource-stripe').change(function(event){
            $('.upload-form-toc-stripe').ajaxSubmit({
                data: {
                    id: $this.selectedItemId,
                    rid: $this.rid,
                    key: 'stripe'
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
                        var file = responseJSON.file;
                        var html =
                            '<a id="toc-current-stripe-fancy" href="' + file.bigUri + '">' +
                                '<img alt="' + file.nameFull + '" src="' + file.smallUri + '">' +
                            '</a>';

                        $('#toc-current-stripe-thumb').html(html);

                        $('#toc-current-stripe-delete').show();
                        $('#toc-current-stripe-title').attr('title', file.name)
                            .html(file.nameShort);

                        $('#toc-current-stripe-fancy').fancybox();
                    }
                }
            });
        });

        $('input.resource-summary').change(function(event){
            $('.upload-form-toc-summary').ajaxSubmit({
                data: {
                    id: $this.selectedItemId,
                    rid: $this.rid,
                    key: 'summary'
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
                        var file = responseJSON.file;
                        var html =
                            '<a id="toc-current-summary-fancy" href="' + file.bigUri + '">' +
                                '<img alt="' + file.nameFull + '" src="' + file.smallUri + '">' +
                            '</a>';

                        $('#toc-current-summary-thumb').html(html);

                        $('#toc-current-summary-delete').show();
                        $('#toc-current-summary-title').attr('title', file.name)
                            .html(file.nameShort);

                        $('#toc-current-summary-fancy').fancybox();
                    }
                }
            });
        });
    },

    onDeleteStripe: function(event) {
        var $this = this;
        $.ajax({
            url: '/editor/toc-delete-stripe',
            type: 'POST',
            dataType: 'json',
            data: {
                rid: this.rid,
                id:  $this.selectedItemId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        var divPicture = $('#toc-current-stripe-thumb');
                        var html = '<img alt="Default image" src="' + data.file.defaultUri + '"/>';
                        $(divPicture).html(html);
                        $('#toc-current-stripe-delete').hide();
                        $('#toc-current-stripe-title').empty();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    },

    onDeleteSummary: function(event) {
        var $this = this;
        $.ajax({
            url: '/editor/toc-delete-summary',
            type: 'POST',
            dataType: 'json',
            data: {
                rid: this.rid,
                id:  $this.selectedItemId
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        var divPicture = $('#toc-current-summary-thumb');
                        var html = '<img alt="Default image" src="' + data.file.defaultUri + '"/>';
                        $(divPicture).html(html);
                        $('#toc-current-summary-delete').hide();
                        $('#toc-current-summary-title').empty();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    },

    onSave: function(key, value) {
        var $this = this;

        var id = $this.selectedItemId;

        if (!id || !key) return false;

        var _key = key;
        var _value = value;

        $.ajax({
            url: '/editor/toc-save',
            type: 'POST',
            dataType: 'json',
            data: {
                page: $('#toc-dialog').dialog('option', 'page'),
                id: id,
                key: _key,
                value: _value
            },
            success: function(data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                    } else {
                        if (_key == 'title') {
                            $('#' + id + ' a', '#toc-current-tree').html('<ins class="jstree-icon">&nbsp;</ins>' + _value);
                        }
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
        return false;
    },

    onOpen: function(event, ui) {
        var $this = this;

        $('#toc-current-edit-item').hide();
        $('#toc-current-create').addClass('disabled');
        $('#toc-current-edit').addClass('disabled');
        $('#toc-current-delete').addClass('disabled');

        var page = $('#toc-dialog').dialog('option', 'page');

        $.ajax({
            url: '/editor/toc-get-tree',
            type: 'POST',
            dataType: 'json',
            data: {
                page: page,
                onlyPermanent: 0
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $this.initTree(data.tree);
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    },

    initTree: function(jsonData) {
        var $this = this;
        $('#toc-current-create').addClass('disabled');
        $('#toc-current-edit').addClass('disabled');
        $('#toc-current-delete').addClass('disabled');

        $("#toc-current-tree").show();
        $("#toc-current-tree").jstree({
            core: {
                initially_open: ['root']
            },
            "json_data" : {
                "data" : [{
                    "attr" : {
                        id:'root',
                        rel:'root'
                    },
                    "data" : "TOC",
                    "children" : jsonData
                }]
            },
            "plugins" : [ "themes", "json_data", "dnd", "ui", "types", /*"contextmenu",*/ "crrm"/*, 'themeroller'*/],
            "types" : {
                "valid_children" : [ "root" ],
                "types" : {
                    "root" : {
                        "icon" : {
                          "image" : "/images/icons/term_folder_disabled.png"
                        },
                        "valid_children" : [ "default" ],
                        "hover_node" : false,
                        "start_drag" : false,
                        "move_node" : false,
                        "delete_node" : false,
                        "remove" : false,
                        "rename" : false
                        //"select_node" : function () {return false;}
                    },
                    "permanent" : {
                        "icon" : {
                            "image" : "/images/icons/term_folder_disabled.png"
                        },
                        "valid_children" : [ "default" ],
                        "hover_node" : false,
                        "start_drag" : false,
                        "move_node" : false,
                        "delete_node" : false,
                        "remove" : false,
                        "rename" : false
                        //"select_node" : function () {return false;}
                    },
                    "default" : {
                        "valid_children" : [ "default" ],
                        "icon" : {
                           "image" : "/images/icons/term_folder.png"
                        },
                        "start_drag" : false,
                        "move_node" : false
                    }
                }
            }
        })

        .bind("select_node.jstree", function (e, data) {
            if (data.inst._get_type() == 'root' || data.inst._get_type() == 'permanent') {
                $('#toc-current-edit').addClass('disabled');
                $('#toc-current-delete').addClass('disabled');
                $('#toc-current-create').removeClass('disabled');
                $('#toc-current-edit-item').hide();
            } else if (data.inst._get_type() == 'default') {
                $('#toc-current-create').removeClass('disabled');
                $('#toc-current-edit').removeClass('disabled');
                $('#toc-current-delete').removeClass('disabled');
                $this.onSelectItem(e, data);
            }
        })

        .bind("create.jstree", function (e, obj) {
            $this.onCreateItem(e, obj);
        })

        .bind("remove.jstree", function (e, obj) {
            $this.onDeleteItem(e, obj);
        })

        .bind("rename.jstree", function (e, obj) {
            $this.onRenameItem(e, obj);
        });
    },

    onSelectItem: function(event, data) {

        var $this = this;
        var itemId = data.rslt.obj[0].id;

        $.post("/editor/toc-get-item/",
            {
                id: itemId
            },
            function (data) {
                try {
                    if (data.status == 1) {
                        $this.selectedItemId = itemId;
                        $this.updateEditor(data.tocItem);
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        );
    },

    updateEditor: function(data) {
        $('input', '#toc-current-edit-item').val('');

        $('#toc-current-title-input').val(data.title);
        $('#toc-current-description-input').val(data.description);
        $('#toc-current-pdf-page-input').val(data.pdf_page);
        $('#toc-current-color-input').val(data.color);

        colorPicker.init();

        $('#colorSelectorBg').css('background-color', '#' + data.color);

        var thumbStripe = data.thumbStripe;
        var thumbSummary = data.thumbSummary;

        var html = '';

        if (thumbStripe.name) {
            html =
                '<a id="toc-current-stripe-fancy" href="' + thumbStripe.bigUri + '">' +
                    '<img alt="' + thumbStripe.name + '" src="' + thumbStripe.smallUri + '">' +
                '</a>';
            $('#toc-current-stripe-delete').show();
            $('#toc-current-stripe-title').attr('title', thumbStripe.name).html(thumbStripe.nameShort);
        } else {
            html = '<img alt="" src="' + thumbStripe.smallUri + '">';
            $('#toc-current-stripe-delete').hide();
            $('#toc-current-stripe-title').attr('title', thumbStripe.name).html('');
        }
        $('#toc-current-stripe-thumb').html(html);

        if (thumbSummary.name) {
            html =
                '<a id="toc-current-summary-fancy" href="' + thumbSummary.bigUri + '">' +
                    '<img alt="' + thumbSummary.name + '" src="' + thumbSummary.smallUri + '">' +
                '</a>';
            $('#toc-current-summary-delete').show();
            $('#toc-current-summary-title').attr('title', thumbSummary.name).html(thumbSummary.nameShort);
        } else {
            html = '<img alt="" src="' + thumbSummary.smallUri + '">';
            $('#toc-current-summary-delete').hide();
            $('#toc-current-summary-title').attr('title', thumbSummary.name).html('');
        }
        $('#toc-current-summary-thumb').html(html);

        $('#toc-current-stripe-fancy').fancybox();
        $('#toc-current-summary-fancy').fancybox();

        $('#toc-current-edit-item').show();
    },

    onCreateItem: function(e, obj) {
        $.post("/editor/toc-add/",
            {
                page: $('#toc-dialog').dialog('option', 'page'),
                parent_id : obj.rslt.parent.attr('id') ? obj.rslt.parent.attr('id') : null,
                title : obj.rslt.name,
                permanent : 0
            },
            function (data) {
                try {
                    if (data.status == 1) {
                        $(obj.rslt.obj).attr('id', data.id);
                    } else {
                        alert(data.message);
                        $.jstree.rollback(obj.rlbk);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                    $.jstree.rollback(obj.rlbk);
                }
            }
        );
    },

    onDeleteItem: function(e, obj) {
        obj.rslt.obj.each(function () {
            $.ajax({
                async : false,
                type: 'POST',
                url: "/editor/toc-delete/",
                data : {
                    page: $('#toc-dialog').dialog('option', 'page'),
                    id : this.id
                },
                success : function (data) {
                    try {
                        if (!data.status) {
                            alert(data.message);
                            $.jstree.rollback(obj.rlbk);
                        }
                    } catch (e) {
                        window.ui.log(e);
                        alert(translate('unexpected_error'));
                        $.jstree.rollback(obj.rlbk);
                    }
                }
            });
        });
    },

    onRenameItem: function(e, obj) {
        var title = obj.rslt.new_name;
        $.post("/editor/toc-rename/",
            {
                page: $('#toc-dialog').dialog('option', 'page'),
                id: obj.rslt.obj.attr('id'),
                title : obj.rslt.new_name
            },
            function (data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                        $.jstree.rollback(obj.rlbk);
                    } else {
                        $('#toc-current-title-input').val(title);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                    $.jstree.rollback(obj.rlbk);
                }
            }
        );
    },

    onClose: function(event, ui) {
        $.ajax({
            url: '/editor/toc-get-list',
            type: 'POST',
            dataType: 'json',
            data: {
                page: $('#toc-dialog').dialog('option', 'page')
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        var html = '';
                        $.each(data.list, function(index, value){
                            if (data.current && index == data.current.id) {
                                html += '<option selected="selected" value="' + index + '">' + value + '</option>';
                            } else {
                                html += '<option value="' + index + '">' + value + '</option>';
                            }
                        });

                        $("#page-toc-select").selectBox('destroy');

                        $('select', $('#page-toc-select').parent()).remove();

                        $('#show-toc-dialog').parent().prepend(
                            '<select id="page-toc-select" onchange="pageInfo.onSaveToc();" >'
                            + html +
                            '</select>'
                        );

                        $("#page-toc-select").selectBox();
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    }
}

tocEditor.permanentTree = {

    init: function() {
        $('#toc-permanent-create').click(function() {
            if ($(this).hasClass('disabled')) return;
            $('#toc-permanent-tree').jstree('create');
        });

        $('#toc-permanent-edit').click(function(){
            if ($(this).hasClass('disabled')) return;
            $('#toc-permanent-tree').jstree('rename');
        });

        $('#toc-permanent-delete').click(function(){
            if ($(this).hasClass('disabled')) return;
            if (!confirm(translate('delete_confirm'))) return;
            $('#toc-permanent-tree').jstree('remove');
        });
    },

    onOpen: function(event, ui) {
        var $this = this;

        $('#toc-permanent-create').addClass('disabled');
        $('#toc-permanent-edit').addClass('disabled');
        $('#toc-permanent-delete').addClass('disabled');

        var page = $('#toc-dialog').dialog('option', 'page');

        $.ajax({
            url: '/editor/toc-get-tree',
            type: 'POST',
            dataType: 'json',
            data: {
                page: page,
                onlyPermanent: 1
            },
            success: function(data) {
                try {
                    if (data.status == 1) {
                        $this.initTree(data.tree);
                    } else {
                        alert(data.message);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                }
            }
        });
    },

    onCreateItem: function(e, obj) {
        var context = this;
        $.post("/editor/toc-add/",
            {
                page: $('#toc-dialog').dialog('option', 'page'),
                parent_id : obj.rslt.parent.attr('id') ? obj.rslt.parent.attr('id') : null,
                title : obj.rslt.name,
                permanent : 1
            },
            function (data) {
                try {
                    if (data.status == 1) {
                        $(obj.rslt.obj).attr('id', data.id);
                    } else {
                        alert(data.message);
                        $.jstree.rollback(obj.rlbk);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                    $.jstree.rollback(obj.rlbk);
                }
            }
        );
    },

    onDeleteItem: function(e, obj) {
        var context = this;

        obj.rslt.obj.each(function () {
            $.ajax({
                async : false,
                type: 'POST',
                url: "/editor/toc-delete/",
                data : {
                    page: $('#toc-dialog').dialog('option', 'page'),
                    id : this.id
                },
                success : function (data) {
                    try {
                        if (!data.status) {
                            alert(data.message);
                            $.jstree.rollback(obj.rlbk);
                        }
                    } catch (e) {
                        window.ui.log(e);
                        alert(translate('unexpected_error'));
                        $.jstree.rollback(obj.rlbk);
                    }
                }
            });
        });
    },

    onRenameItem: function(e, obj) {
        var context = this;
        $.post("/editor/toc-rename/",
            {
                page: $('#toc-dialog').dialog('option', 'page'),
                id: obj.rslt.obj.attr('id'),
                title : obj.rslt.new_name
            },
            function (data) {
                try {
                    if (!data.status) {
                        alert(data.message);
                        $.jstree.rollback(obj.rlbk);
                    }
                } catch (e) {
                    window.ui.log(e);
                    alert(translate('unexpected_error'));
                    $.jstree.rollback(obj.rlbk);
                }
            }
        );
    },

    initTree: function(jsonData) {
        var context = this;
        $('#toc-permanent-create').addClass('disabled');
        $('#toc-permanent-edit').addClass('disabled');
        $('#toc-permanent-delete').addClass('disabled');

        $("#toc-permanent-tree").show();
        $("#toc-permanent-tree").jstree({
            core: {
                initially_open: ['root']
            },
            "json_data" : {
                "data" : [{
                    "attr" : {
                        id:'root',
                        rel:'root'
                    },
                    "data" : "TOC",
                    "children" : jsonData
                }]
            },
            "plugins" : [ "themes", "json_data", "dnd", "ui", "types", /*"contextmenu",*/ "crrm"/*, 'themeroller'*/],
            "types" : {
                "valid_children" : [ "root" ],
                "types" : {
                    "root" : {
                        "icon" : {
                          "image" : "/images/icons/term_folder_disabled.png"
                        },
                        "valid_children" : [ "default" ],
                        "hover_node" : false,
                        "start_drag" : false,
                        "move_node" : false,
                        "delete_node" : false,
                        "remove" : false,
                        "rename" : false
                        //"select_node" : function () {return false;}
                    },
                    "default" : {
                        "valid_children" : [ "default" ],
                        "icon" : {
                           "image" : "/images/icons/term_folder.png"
                        },
                        "start_drag" : false,
                        "move_node" : false
                    }
                }
            }
        })

        .bind("select_node.jstree", function (e, data) {
            if (data.inst._get_type() == 'root') {
                $('#toc-permanent-edit').addClass('disabled');
                $('#toc-permanent-delete').addClass('disabled');
            } else {
                $('#toc-permanent-edit').removeClass('disabled');
                $('#toc-permanent-delete').removeClass('disabled');
            }
            $('#toc-permanent-create').removeClass('disabled');
        })

        .bind("create.jstree", function (e, obj) {
            context.onCreateItem(e, obj);
        })

        .bind("remove.jstree", function (e, obj) {
            context.onDeleteItem(e, obj);
        })

        .bind("rename.jstree", function (e, obj) {
            context.onRenameItem(e, obj);
        });

    }
}

var addFileToElement = {
    uploader: null,
    domRoot: null,

    init: function() {
        var context = this;

        context.domRoot = $('#add-file-dialog')[0];
        if (!this.domRoot)
            return;

        $(context.domRoot).dialog({
            autoOpen: false,
            resizable: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            open: function(event, ui) {
                context.onOpen(event, ui);
            },
            close: function(event, ui) {
                context.onClose(event, ui);
            }
        });

        $('a.delete-file', context.domRoot).bind('click', context, function(event){
            return event.data.onDelete(event.originalEvent);
        });

        $('input.resource-extra').change(function(event){
            $('.upload-form-extra').ajaxSubmit({
                data: {
                    element:  $(context.domRoot).dialog('option', 'elementId'),
                    key: $(context.domRoot).dialog('option', 'key')
                },
                dataType: 'json',
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            $('.status', context.domRoot).html(responseJSON.message).show();
                        } else {
                            $('.status', context.domRoot).html(translate('Error. Can\'t upload file')).show();
                        }
                    } else {
                        var key         = $(context.domRoot).dialog('option', 'key');
                        var removeClass = null;
                        var addClass    = null;
                        if (key == 'video') {
                            removeClass = 'action-1-disabled';
                            addClass = 'action-1';
                        } else {
                            removeClass = 'action-2-disabled';
                            addClass = 'action-2';
                        }
                        $($(context.domRoot).dialog('option', 'sourceEvent').target)
                                .removeClass(removeClass)
                                .addClass(addClass)
                                .attr('rel', responseJSON.fileName)
                                .attr('href', responseJSON.fileUri);
                        $(context.domRoot).dialog('close');
                    }
                }
            });
        });
    },

    onOpen: function(event, ui) {
        var context = this;
        var elementId = $(context.domRoot).dialog('option', 'elementId');
        var key = $(context.domRoot).dialog('option', 'key');

        if (!elementId)
            $(context.domRoot).dialog('close');


        $('input.resource-extra', context.domRoot).attr('name', key);

        var file = $($(context.domRoot).dialog('option', 'sourceEvent').target)
                .attr('rel');

        var href = $($(context.domRoot).dialog('option', 'sourceEvent').target)
                .attr('href');

        if (file) {
            var html = '';
            if (href && href != '#') {
                html = '<a href="' + href + '">' + file + '</a>'
            } else {
                html = file;
            }
            $('.current-file', context.domRoot).html(html);
            $('.delete-file', context.domRoot).show();

            $('a', $('.current-file', context.domRoot)).fancybox();
        } else {
            $('.current-file', context.domRoot).html('no file');
            $('.delete-file', context.domRoot).hide();
        }
    },

    onClose: function(event, ui) {
        var context = this;
        $(context.domRoot).dialog('option', 'elementId', null);
        $('.status', context.domRoot).hide().empty();
        $('.delete-file', context.domRoot).hide();
        $('.current-file', context.domRoot).html(translate('no file'));
    },

    onDelete: function(event) {
        var context = this;
        $('.status', context.domRoot).hide().empty();

        var elementId = $(context.domRoot).dialog('option', 'elementId');
        var key = $(context.domRoot).dialog('option', 'key');

        $.ajax({
            url: '/field/delete',
            type: 'POST',
            dataType: 'json',
            data:{
                element: elementId,
                key: key
            },
            success: function(data) {
                if (data.status == 1) {
                    $('.current-file', context.domRoot).html('no file');
                    $('.delete-file', context.domRoot).hide();
                    var key = $(context.domRoot).dialog('option', 'key');
                    var removeClass = null;
                    var addClass = null;
                    if (key == 'video') {
                        removeClass = 'action-1';
                        addClass = 'action-1-disabled';
                    } else {
                        removeClass = 'action-2';
                        addClass = 'action-2-disabled';
                    }
                    $($(context.domRoot).dialog('option', 'sourceEvent').target)
                            .removeClass(removeClass)
                            .addClass(addClass)
                            .attr('rel', '')
                            .attr('href', '#');
                    $(context.domRoot).dialog('close');
                } else {
                    if (data.message)
                        $('.status', context.domRoot).html(data.message).show();
                    else
                        $('.status', context.domRoot).html(translate('unexpected_error')).show();
                }
            }
        });
        return false;
    }
}

var pdfPageEditor = {
    init: function() {
        var $this = this;

        this._popup = $('#select-pdf-page-dialog');

        this._popup.dialog({
            title: translate('select_pdf_page'),
            autoOpen: false,
            resizable: false,
            height: 'auto',
            width: 'auto',
            modal: true,
            open: function(event, ui) {

            },
            close: function(event, ui) {
            }
        });
    },

    load : function() {
      var $this = this;

      $.ajax({url: '/editor/get-static-pdf-list/',
              dataType: 'json',
              type: 'POST',
              data: {
                  pid: $(this._popup).dialog('option', 'page')
              },
              success: function(data) {
                  if(data.status && data.list) {
                      $this.buildSelect(data.list, data.app, data.issue);
                  }
                  $('#select-pdf-page-dialog').dialog('option', 'page', window.pid).dialog('open');
              },
              error: function(e) {
                  alert(translate('unexpected_error'));
              }
          });
    },

    buildSelect : function(list_data, app, issue) {
        var $this = this,
        $_wraper = $('#select-pdf-page-dialog .pdf-page-list-wraper').empty();

        if(list_data.length < 1) {
            $('.pdf-page-message-wraper').show();
            $('.pdf-page-list-wraper').hide();

            $('#select-pdf-page-dialog .pdf-page-message-wraper strong')
              .empty()
              .html(
                translate('pdf_page_list_is_empty') + '. <a href="/issue/edit/iid/' + issue + '/">' + translate('for_add_pdf_pages_go_to_issue_edit') + ' &raquo;</a>'
              );

        } else {
            $('.pdf-page-message-wraper').hide();
            $('.pdf-page-list-wraper').show();

            var $_ul = $('<ul class="pdf-page-list"></ul>').appendTo($_wraper);

            for(page in list_data) {
                var $_li = $('<li id="pdf_page_' + list_data[page].id + '" class="pdf-page"></li>')
                           .appendTo($_ul)
                           .data('pdf-page', list_data[page]),

                    $_image = $('<img src="' + list_data[page].url + '" />')
                              .appendTo($_li),

                    $_preview_link = $('<a href="' + list_data[page].preview_url + '" class="preview_pdf_page"></a>')
                              .appendTo($_li);
            }

            $this.initFancybox($('a.preview_pdf_page', $_wraper));

            $('li', $_ul).bind('click', function() {
                $_data = $(this).data();
                $('#page-pdf-page-input').val($_data.pdfPage.id);
                pageInfo.onSave('pdf_page', $_data.pdfPage.id);
                $( '#select-pdf-page-dialog' ).dialog( "close" );
            })
        }
    },

    initFancybox: function(elements) {
        $(elements).click(function(){return false;});

        $(elements).fancybox({
            onStart: function() {
                if ($(this.orig).hasClass('prevent-select')) {
                    $(this.orig).removeClass('prevent-select');
                    return false;
                }
                return true;
            }
        });
    }
}