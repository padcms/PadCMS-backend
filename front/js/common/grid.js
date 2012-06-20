/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(function() {
    // Initialize BS_Grid pager
    $('div.pager select, div.rpt-pager select').change(function(){
        window.location =
            $('input', $(this).parent().parent().parent()).val()
            + $('option:selected', this).val();
    });
});