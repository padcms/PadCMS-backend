/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
$(document).ready(function() {
  $('h3.title-inprogress a').click(function() {
      $('#revision-list-actions').dialog()
          .dialog('option', 'context', $(this).closest('h3'))
          .dialog('option', 'issue', $(this).attr('id').split('-')[2])
          .dialog('option', 'revision', $(this).attr('id').split('-')[3])
          .dialog('open');
      return false;
  });

  $('#revision-list-action-publish').click(function(event) {
    $('#revision-list-actions').dialog('close');
    if (!confirm(translate('publish_confirm'))) {
      event.preventDefault();
      return false;
    }
    $.ajax({
        type: 'POST',
        url: "/revision/publish",
        dataType: "json",
        data: {
          'iid': $('#revision-list-actions').dialog('option', 'issue'),
          'revision': $('#revision-list-actions').dialog('option', 'revision')
        },
        success: function(data) {
            if (!data.status) {
                if (data.message) {
                  alert(data.message);
                } else {
                  alert(translate('unexpected_ajax_error'));
                }
            } else {
                $('h3.title-published')
                    .removeClass('title-published')
                    .addClass('title-archived')
                    .html(translate('archived'));

                $('#revision-list-actions').dialog('option', 'context')
                    .addClass('title-published')
                    .removeClass('title-inprogress')
                    .html(translate('published'));
            }
        }
    });
  });

  $('#revision-list-action-delete').unbind('click').click(function(event) {
    $('#revision-list-actions').dialog('close');
    if (!confirm(translate('delete_confirm'))) {
      event.preventDefault();
      return false;
    }
    window.location.href = "/revision/delete/rid/"
              + $('#revision-list-actions').dialog('option', 'revision')
              + "/iid/" + $('#revision-list-actions').dialog('option', 'issue');
  });

  $('#revision-list-actions').dialog({
      title: translate('select_an_action'),
      modal: true,
      autoOpen: false,
      resizable: false
  });
});