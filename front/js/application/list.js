$(document).ready(function() {
    importArchive.init();
});

var importArchive = {
    init: function() {
        $('input[id^="import-file"]').change(function(event){
            var obj = event.currentTarget;
            var objIdSplitted = $(obj).attr("id").split("-");

            if(objIdSplitted.length != 3) {
                alert(translate('Unexpected error'));
            }

            id = objIdSplitted[2];

            $('#import-form-' + id).ajaxSubmit({
                dataType: 'json',
                success: function(objResponse) {
                    if (!objResponse.status) {
                        if (objResponse.message) {
                            alert(objResponse.message);
                        } else {
                            alert(translate("Error!"));
                        }
                    } else {
                        if (objResponse.message) {
                            window.location.replace("/revision/list/iid/" + objResponse.message);
                        } else {
                            alert(translate("Error!"));
                        }
                    }
                }
            });
        });
    }
}