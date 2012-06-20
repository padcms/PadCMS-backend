/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
var fieldGames = {
    pageId: null,
    fieldId: null,
    domRoot: null,

    init: function() {
        var context = this;
        context.domRoot = $('#field-games')[0];

        if (!context.domRoot) {
            return;
        }

        $('.cont', context.domRoot).show();

        // Process BODY select and show field, that refers to it
        var selectedItem = $('select[name=game_type]', context.domRoot).val();

        if (selectedItem == '0') {
            $('.edit-game-button', context.domRoot).hide();
        }

        $('.cont', context.domRoot).hide();

        // Save button
        $('#page-additional-data-btn', context.domRoot).bind('click', {context: context}, function(event) {
            event.data.context.onEdit();
        });

        context.pageId = document.pid;
        context.fieldId = $("input[name='field-id']", context.domRoot).val();
    },

    typeSelected: function() {
        var context      = this;
        var selectedItem = $('select[name=game_type]', context.domRoot).val();

        if (selectedItem != '0'){
            $('.edit-game-button', context.domRoot).show();
        } else {
            $('.edit-game-button', context.domRoot).hide();
        }
    },

    onEdit: function() {
        var context = this;

        var selectedItemType = $('select[name=game_type]', context.domRoot).val();

        //Loading dialog
        $.ajax({
            url: '/field-games-' + selectedItemType + '/builder',
            type: 'POST',
            dataType: 'html',
            data: {
                entity: this.entity
            },
            success: function(data) {
                $('#dialog').dialog('destroy');
                $('#dialog').html(data);

                $('#dialog').dialog({
                    resizable: true,
                    height: 'auto',
                    width: 'auto',
                    modal: true,
                    title: selectedItemType.charAt(0).toUpperCase() + selectedItemType.slice(1) + ' Game'
                });

                var game = window[selectedItemType.charAt(0).toUpperCase() + selectedItemType.slice(1)+'Game'];
                game.init();
            },
            error: function() {
                console.log('Error');
            }
        });

        return false;
    }
}