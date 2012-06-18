$(document).ready(function (){
    $('a.cbutton-copy-move').click(function(event) {
        var obj = event.currentTarget;
        dialog.init(obj);
    });
});

var dialog = {
    init: function(obj) {
        var context = this;

        var objIdSplitted = $(obj).attr("id").split("-");

        if(objIdSplitted.length != 4) {
            alert(translate('unexpected_error'));
        }

        this.action         = objIdSplitted.shift();
        this.entity         = objIdSplitted.shift();
        this.entityId       = objIdSplitted.shift();
        this.entityParentId = objIdSplitted.shift();
        this.actionTitle = ucfirst(this.action);

        dialogTitle = translate("Chose destination to " + this.action + " " + this.entity );

        //Loading dialog
        $.ajax({
            url: '/admin/transfer-dialog',
            type: 'POST',
            dataType: 'html',
            data: {
                entity: this.entity
            },
            success: function(data) {
                $('#dialog').dialog('destroy');
                $('#dialog').html(data);

                $('#dialog').dialog({
                    resizable: false,
                    height: 'auto',
                    width: 'auto',
                    modal: true,
                    title: dialogTitle
                });

                buttons.init();
                if (!document.transferError) {
                    lists.init();
                }
            }
        });
    }
}

var buttons = {
    init: function() {
        $("#cancel-button").click(function(event){
            $('#dialog').dialog("close");
        });

        actionUrl = "/" + dialog.entity + "/transfer";

        $("#transfer-button").click(function(event){
            $.ajax({
                url: actionUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    method:   dialog.action,
                    entityId: dialog.entityId,
                    clientId: $("#client-select").val(),
                    userId:   $("#user-select").val(),
                    iid:      $("#issue-select").val(),
                    aid:      $("#app-select").val()
                },
                success: function(data) {
                    if(!data.status || data.status == 0) {
                        initErrorMessage("501", data.message, "transfer");
                    } else {
                        switch (dialog.entity) {
                            case 'revision':
                                    newTargetId = $("#issue-select").val();
                                    if(newTargetId != dialog.entityParentId) {
                                        popup = window.open("/revision/list/iid/" + newTargetId);
                                        self.focus();
                                    }
                                    window.location.replace("/revision/list/iid/" + dialog.entityParentId);
                                    return false;
                                break;
                            case 'issue':
                                    newTargetId = $("#app-select").val();
                                    if(newTargetId != dialog.entityParentId) {
                                        popup = window.open("/issue/list/aid/" + newTargetId);
                                        self.focus();
                                    }
                                    window.location.replace("/issue/list/aid/" + dialog.entityParentId);
                                    return false;
                                break;
                            case 'application':
                            default:
                                    newTargetId = $("#client-select").val();
                                    if(newTargetId != dialog.entityParentId) {
                                        popup = window.open("/application/list/cid/" + newTargetId);
                                        self.focus();
                                    }
                                    window.location.replace("/application/list/cid/" + dialog.entityParentId);
                                    return false;
                        }
                    }
                }
            });
        });

        $("#transfer-button").attr("value", translate(dialog.actionTitle));

        this.hideSubmitButton();
    },
    hideSubmitButton: function() {
        $("#transfer-button").hide();
    },
    showSubmitButton: function() {
        $("#transfer-button").show();
    }
}

var lists = {
    init: function() {
        clientsList.init();
        usersList.disable();

        switch (dialog.entity) {
            case 'revision':
                issuesList.disable();
                applicationsList.disable();
                break;
            case 'issue':
                issuesList.hide();
                applicationsList.disable();
                break;
            case 'application':
            default:
                issuesList.hide();
                applicationsList.hide();
        }
    }
}

var clientsList = {
    init: function() {
        $('select').selectBox('destroy');
        this.select = $("#client-select");
        $('select').selectBox();
    },
    onChange: function() {
        clearErrorMessage("client");
        clearErrorMessage("common");
        usersList.disable();
        applicationsList.disable();
        issuesList.disable();
        if (this.select && $(this.select).val() != 0) {
            $.ajax({
                url: '/admin/transfer-dialog-users',
                type: 'POST',
                dataType: 'json',
                data: {
                    entity: dialog.entity,
                    clientId: $(this.select).val()
                },
                success: function(data) {
                    if (!data.error) {
                        usersList.init(data);
                    } else {
                        initErrorMessage(data.error, data.message, "client");
                    }
                }
            });
        }
    }
}

var usersList = {
    init: function(data) {
        $('select').selectBox('destroy');
        this.select = $("#user-select");
        this.select.removeAttr("disabled");
        this.clearSelect();
        for (key in data) {
            var user = data [key];
            this.select.append("<option value=\"" + user.id + "\">" + user.login + "</option>");
        }
        $('select').selectBox();
    },
    disable: function() {
        $('select').selectBox('destroy');
        clearErrorMessage("user");
        this.clearSelect();
        $("#user-select").attr("disabled", true);
        $('select').selectBox();
    },
    clearSelect: function() {
        $("#user-select").children().remove();
        $("#user-select").append("<option value=\"0\">" + translate('Nothing selected') +"</option>");
    },
    onChange: function() {
        if(dialog.entity != "application") {
            clearErrorMessage("user");
            clearErrorMessage("common");
            applicationsList.disable();
            issuesList.disable();
            if (this.select && $(this.select).val() != 0) {
                $.ajax({
                    url: '/admin/transfer-dialog-apps',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        entity:   dialog.entity,
                        clientId: $(clientsList.select).val(),
                        userId:   $(this.select).val()
                    },
                    success: function(data) {
                        if (!data.error) {
                            applicationsList.init(data);
                        } else {
                            initErrorMessage(data.error, data.message, "user");
                        }
                    }
                });
            }
        } else {
            if (!this.select || $(this.select).val() != 0) {
                buttons.showSubmitButton();
            } else {
                buttons.hideSubmitButton();
            }
        }
    }
}

var applicationsList = {
    init: function(data) {
        $('select').selectBox('destroy');
        this.select = $("#app-select");
        this.select.removeAttr("disabled");
        this.clearSelect();
        for (key in data) {
            var app = data [key];
            this.select.append("<option value=\"" + app.id + "\">" + app.title + "</option>");
        }
        $('select').selectBox();
    },
    clearSelect: function() {
        $("#app-select").children().remove();
        $("#app-select").append("<option value=\"0\">" + translate('Nothing selected') +"</option>");
    },
    disable: function() {
        $('select').selectBox('destroy');
        clearErrorMessage("application");
        this.clearSelect();
        $("#app-select").attr("disabled", true);
        $('select').selectBox();
    },
    hide: function() {
        this.disable();
        $(".app-select").hide();
    },
    onChange: function() {
        if(dialog.entity != "issue") {
           clearErrorMessage("application");
           clearErrorMessage("common");
           issuesList.disable();
            if (this.select && $(this.select).val() != 0) {
               $.ajax({
                    url: '/admin/transfer-dialog-issues',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        entity: dialog.entity,
                        appId:  $(this.select).val(),
                        userId: $("#user-select").val()
                    },
                    success: function(data) {
                        if (!data.error) {
                            issuesList.init(data);
                        } else {
                            initErrorMessage(data.error, data.message, "application");
                        }
                    }
                });
            }
        } else {
            if ($(this.select).val() != 0) {
                buttons.showSubmitButton();
            } else {
                buttons.hideSubmitButton();
            }
        }
    }
}
var issuesList = {
    init: function(data) {
        $('select').selectBox('destroy');
        this.select = $("#issue-select");
        this.select.removeAttr("disabled");
        this.clearSelect();
        for (key in data) {
            var issue = data [key];
            this.select.append("<option value=\"" + issue.id + "\">" + issue.title + "</option>");
        }
        $('select').selectBox();
    },
    clearSelect: function() {
        $("#issue-select").children().remove();
        $("#issue-select").append("<option value=\"0\">" + translate('Nothing selected') +"</option>");
    },
    disable: function() {
        $('select').selectBox('destroy');
        this.clearSelect();
        $("#issue-select").attr("disabled", true);
        $('select').selectBox();
    },
    hide: function() {
        this.disable();
        $(".issue-select").hide();
    },
    onChange: function() {
        if (!this.select || $(this.select).val() == 0) {
            buttons.hideSubmitButton();
        } else {
            buttons.showSubmitButton();
        }
    }
}

function initErrorMessage(errorCode, errorMessage, initiator) {
    errorHtml = '<div class="ui-widget dialog-error-' + initiator + '">'
        + '<div style="margin-top: 20px; padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">'
        + '<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>'
        + '<strong>'
        + translate(errorMessage)
        + '</strong>'
        + '</p>'
        + '</div>'
        + '</div>';
    $(".dialog-error-" + initiator).remove();
    $(".selects").prepend(errorHtml);
}

function clearErrorMessage(initiator) {
    $(".dialog-error-" + initiator).remove();
}