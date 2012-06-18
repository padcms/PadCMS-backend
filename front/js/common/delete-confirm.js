$(document).ready(function (){
    $('a.delete-confirm, a.cbutton-delete').click(function(){
        if (!confirm(translate('delete_confirm'))) {
            return false;
        }
        return true;
    });
});