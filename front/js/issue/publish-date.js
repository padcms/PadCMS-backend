/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
    $issueState = $('.issue-state');
    if ($issueState.val() != 2) {
        $('#publish-date').prop('disabled', true);
        $('.publish-date-wrapper').hide();
    }
    $issueState.change(function() {
        if ($(this).val() == 2) {
            $('#publish-date').prop('disabled', false);
            $('.publish-date-wrapper').show();
        }
        else {
            $('#publish-date').prop('disabled', true);
            $('.publish-date-wrapper').hide();
        }
    });
});
