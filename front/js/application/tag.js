/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
    $(function() {
        $( "#existing-tags, #possible-tags" ).sortable({
            connectWith: ".connectedSortable",
            stop: function( event, ui ) {
                var existingTags = $( "#existing-tags" ).sortable( "toArray" ) || [];

                $.ajax({
                    url: '/application/tag-update',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        existingTags: existingTags,
                        aid: window.appId
                    },
                    success: function(data) {
                        try {
                            $.each( data, function( key, value ) {
                                if (key == 'existingTags') {
                                    var $existingTags = $( "#existing-tags" );
                                    var $existingTagsToAppend = $();
                                    $existingTags.empty();
                                    $.each(value, function( key, tag ) {
                                        $existingTagsToAppend = $existingTagsToAppend.add(
                                            $('<li>' + tag.value + '</li>').attr({
                                                'id': tag.te_id,
                                                'class': 'ui-state-default'
                                            })
                                        );
                                    });
                                    $existingTags.append($existingTagsToAppend);
                                }
                                else if (key == 'possibleTags') {
                                    var $possibleTags = $( "#possible-tags" );
                                    $possibleTags.empty();
                                    var $possibleTagsToAppend = $();

                                    $.each(value, function( key, tag ) {
                                        $possibleTagsToAppend = $possibleTagsToAppend.add(
                                            $('<li>' + tag.value + '</li>').attr({
                                                'id': tag.te_id,
                                                'class': 'ui-state-highlight'
                                            })
                                        );
                                    });
                                    $possibleTags.append($possibleTagsToAppend);
                                }
                        });

                        } catch (e) {
                            window.ui.log(e);
                            alert(translate('unexpected_ajax_error'));
                        }
                    }
                });
            }
        }).disableSelection();
    });
});
