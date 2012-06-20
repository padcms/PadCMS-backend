/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
  $('h3.title-inprogress a').click(function() {
      $('#issue-list-actions').dialog()
          .dialog('option', 'context', $(this).closest('h3'))
          .dialog('option', 'application', $(this).attr('id').split('-')[2])
          .dialog('option', 'issue', $(this).attr('id').split('-')[3])
          .dialog('open');
      return false;
  });

  $('#issue-list-action-publish').click(function(event) {
    $('#issue-list-actions').dialog('close');
    if (!confirm(translate('publish_confirm'))) {
      event.preventDefault();
      return false;
    }
    $.ajax({
        type: 'POST',
        url: "/issue/publish",
        dataType: "json",
        data: {
          'aid': $('#issue-list-actions').dialog('option', 'application'),
          'issue': $('#issue-list-actions').dialog('option', 'issue')
        },
        success: function(data) {
            if (!data.status) {
                if (data.message) {
                  alert(data.message);
                } else {
                  alert(translate('unexpected_error'));
                }
            } else {
                $('#issue-list-actions').dialog('option', 'context')
                    .addClass('title-published')
                    .removeClass('title-inprogress')
                    .html(translate('published'));
            }
        }
    });
  });

  $('#issue-list-action-delete').unbind('click').click(function(event) {
    $('#issue-list-actions').dialog('close');
    if (!confirm(translate('delete_confirm'))) {
      event.preventDefault();
      return false;
    }
    window.location.href = "/issue/delete/iid/"
              + $('#issue-list-actions').dialog('option', 'issue')
              + "/aid/" + $('#issue-list-actions').dialog('option', 'application');
  });

  $('#issue-list-actions').dialog({
      title: translate('select_an_action'),
      modal: true,
      autoOpen: false,
      resizable: false
  });
});