function translate(msg) {
    var args = new Array();
    for(var i=1; i < translate.arguments.length; i++) {
        args.push(translate.arguments[i]);
    }
    return sprintf.call(this, locale[msg] ? locale[msg] : msg, args);
}