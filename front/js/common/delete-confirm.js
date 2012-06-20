/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function (){
    $('a.delete-confirm, a.cbutton-delete').click(function(){
        if (!confirm(translate('delete_confirm'))) {
            return false;
        }
        return true;
    });
});