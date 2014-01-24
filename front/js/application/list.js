/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function (){
    $('a.cbutton-clear-cache').click(function(event) {

        var objIdSplitted = $(this).attr("id").split("-");

        if(objIdSplitted.length != 3) {
            alert(translate('unexpected_error'));
        }

        var action        = objIdSplitted.shift();
        var applicationId = objIdSplitted.shift();
        var clientId      = objIdSplitted.shift();

        //clear Cache
        $.ajax({
            url: '/application/clear-cache',
            type: 'POST',
            dataType: 'json',
            data: {
                cid: clientId,
                aid: applicationId
            },
            success: function(data) {
                if (data.status) {
                    alert("Cache has been cleared");
                }
            }
        });
    });
});