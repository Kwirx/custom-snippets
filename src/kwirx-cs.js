/**
* Plugin Name: Kwirx Custom Snippets
* Description: A plugin to insert custom PHP code snippets.
* Version: 1.2
* Author: Kwirx Creative
 *
 * This JavaScript file is used to handle the CodeMirror editor initialization
 * and the AJAX form submission for saving custom PHP code snippets within the
 * WordPress admin panel.
 */

jQuery(document).ready(function($) {
  // Initialize CodeMirror settings
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

  // Initialize the CodeMirror editor
  var editor = wp.codeEditor.initialize($('#kwirx_cs_code_snippet'), editorSettings);

  // Handle form submission
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

  // Dismiss the notice
  $(document).on('click', '.notice-dismiss', function() {
      $(this).closest('.notice').remove();
  });
});
