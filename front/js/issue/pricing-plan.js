/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
    $issuePricing = $('.issue-pricing');
    if ($issuePricing.val() != 3 && $issuePricing.val() != 4) {
        $('.itunes-id').prop('disabled', true);
        $('.itunes-id-wrapper').hide();
        $('.google-play-id').prop('disabled', true);
        $('.google-play-id-wrapper').hide();
    }
    $issuePricing.change(function() {
        if ($(this).val() == 3 || $(this).val() == 4) {
            $('.itunes-id').prop('disabled', false);
            $('.itunes-id-wrapper').show();
            $('.google-play-id').prop('disabled', false);
            $('.google-play-id-wrapper').show();
        }
        else {
            $('.itunes-id').prop('disabled', true);
            $('.itunes-id-wrapper').hide();
            $('.google-play-id').prop('disabled', true);
            $('.google-play-id-wrapper').hide();
        }
    });
});
