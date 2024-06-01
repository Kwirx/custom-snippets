jQuery(document).ready(function($) {
  var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
  editorSettings.codemirror = _.extend(
      {},
      editorSettings.codemirror,
      {
          mode: 'text/x-php',
          indentUnit: 4,
          tabSize: 4,
          lineNumbers: true,
          autoCloseTags: true,
          autoCloseBrackets: true,
          matchBrackets: true,
          viewportMargin: Infinity
      }
  );

  var editor = wp.codeEditor.initialize($('#kwirx_cs_code_snippet'), editorSettings);

  $('#kwirx-cs-form').on('submit', function(e) {
      e.preventDefault();
      var code = editor.codemirror.getValue();
      $.ajax({
          url: kwirx_cs_ajax.ajax_url,
          type: 'POST',
          dataType: 'json',
          data: {
              action: 'kwirx_cs_save_code_snippet',
              code: code,
              nonce: $('#kwirx_cs_nonce').val()
          },
          success: function(response) {
              $('#kwirx-cs-notice').html('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
          },
          error: function(response) {
              var errorData = response.responseJSON ? response.responseJSON.data : {message: 'Unknown error', line: 'N/A'};
              var errorMessage = 'Error: ' + errorData.message + ' on line ' + errorData.line;
              $('#kwirx-cs-notice').html('<div class="notice notice-error is-dismissible"><p>' + errorMessage + '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>');
          }
      });
  });

  $(document).on('click', '.notice-dismiss', function() {
      $(this).closest('.notice').remove();
  });
});
