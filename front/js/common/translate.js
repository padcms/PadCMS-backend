/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
function translate(msg) {
    var args = new Array();
    for(var i=1; i < translate.arguments.length; i++) {
        args.push(translate.arguments[i]);
    }
    return sprintf.call(this, locale[msg] ? locale[msg] : msg, args);
}