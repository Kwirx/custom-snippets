<?php
/*
Plugin Name: Kwirx Custom Snippets
Description: A plugin to insert custom PHP code snippets.
Version: 1.2
Author: Kwirx Creative
*/

/**
 * Adds admin menu page.
 */
function kwirx_cs_add_admin_page() {
    add_menu_page(
        'Kwirx Custom Snippets',
        'Code Snippet',
        'manage_options',
        'kwirx-custom-snippets',
        'kwirx_cs_admin_page',
        'dashicons-editor-code',
        110
    );
}
add_action('admin_menu', 'kwirx_cs_add_admin_page');

/**
 * Enqueues CodeMirror scripts and styles.
 *
 * @param string $hook_suffix The current admin page.
 */
function kwirx_cs_enqueue_code_editor($hook_suffix) {
    if ($hook_suffix !== 'toplevel_page_kwirx-custom-snippets') {
        return;
    }
    // Enqueue CodeMirror editor and required scripts/styles
    wp_enqueue_code_editor(array('type' => 'application/x-httpd-php'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    // Enqueue custom JavaScript file for handling form submission and AJAX
    wp_enqueue_script('kwirx_cs_custom_js', plugin_dir_url(__FILE__) . 'src/kwirx-cs.js', array('jquery'), null, true);
    // Pass the AJAX URL to the JavaScript file using localization
    wp_localize_script('kwirx_cs_custom_js', 'kwirx_cs_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    // Add a resize script to adjust the CodeMirror editor height
    add_action('admin_footer', 'kwirx_cs_add_editor_resize_script');
}
add_action('admin_enqueue_scripts', 'kwirx_cs_enqueue_code_editor');

/**
 * Displays the admin page content.
 */
function kwirx_cs_admin_page() {
    ?>
    <div class="wrap">
        <h1>Kwirx Custom Snippets</h1>
        <p>Insert your custom PHP code snippet below.</p>
        <div id="kwirx-cs-notice"></div>
        <form id="kwirx-cs-form" method="post" action="">
            <?php wp_nonce_field('kwirx_cs_save_code_snippet', 'kwirx_cs_nonce'); ?>
            <!-- Textarea for entering the code snippet -->
            <textarea id="kwirx_cs_code_snippet" name="kwirx_cs_code_snippet"><?php echo esc_textarea(get_option('kwirx_cs_code_snippet', '')); ?></textarea>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

/**
 * Handles AJAX request for saving code snippet.
 */
function kwirx_cs_save_code_snippet() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kwirx_cs_save_code_snippet')) {
        // Verify the nonce for security purposes
        wp_send_json_error(__('Invalid nonce.'), 400);
    }

    $code = isset($_POST['code']) ? wp_unslash($_POST['code']) : '';

    // Check for syntax errors
    $check_result = kwirx_cs_check_syntax($code);
    if (!$check_result['success']) {
        // Return error response if syntax check fails
        wp_send_json_error($check_result['data'], 400);
    }

    // Sanitize and save the code
    $code = kwirx_cs_sanitize_code($code);
    update_option('kwirx_cs_code_snippet', $code);

    // Return success response
    wp_send_json_success(array('message' => __('Code snippet saved successfully'), 'code' => $code));
}
add_action('wp_ajax_kwirx_cs_save_code_snippet', 'kwirx_cs_save_code_snippet');

/**
 * Executes the custom code snippet.
 */
function kwirx_cs_execute_code_snippet() {
    $code = get_option('kwirx_cs_code_snippet', '');
    if (!empty($code)) {
        // Execute the saved code snippet using eval()
        eval($code);
    }
}
add_action('init', 'kwirx_cs_execute_code_snippet');

/**
 * Removes saved data upon plugin uninstall.
 */
function kwirx_cs_uninstall() {
    delete_option('kwirx_cs_code_snippet');
}
register_uninstall_hook(__FILE__, 'kwirx_cs_uninstall');

/**
 * Adds custom CSS to adjust the CodeMirror editor height.
 */
function kwirx_cs_add_editor_resize_script() {
    ?>
    <style>
        .CodeMirror {
            height: calc(100vh - 300px) !important; /* Adjust as needed */
            margin-bottom: 20px;
        }
        #kwirx_cs_notice {
            margin-top: 20px;
        }
    </style>
    <?php
}

/**
 * Checks for syntax errors in PHP code.
 *
 * @param string $code The PHP code to check.
 * @return array The result of the syntax check.
 */
/**
 * Checks for syntax errors in PHP code.
 *
 * @param string $code The PHP code to check.
 * @return array The result of the syntax check.
 */
function kwirx_cs_check_syntax($code) {
  $tokens = @token_get_all('<?php ' . $code); // Suppress errors using @
  if ($tokens === false) {
      return array('success' => false, 'data' => array('message' => 'Unable to tokenize code', 'line' => 'N/A'));
  }

  try {
      $eval_result = eval('if (0) {' . $code . '}');
      return array('success' => true);
  } catch (ParseError $e) {
      return array('success' => false, 'data' => array('message' => $e->getMessage(), 'line' => $e->getLine()));
  }
}

/**
 * Sanitizes the PHP code input.
 *
 * @param string $input The PHP code to sanitize.
 * @return string The sanitized PHP code.
 */
function kwirx_cs_sanitize_code($input) {
    // Remove PHP opening and closing tags and escape double quotes
    $input = str_replace(array('<?', '?>'), '', $input);
    $input = str_replace('"', '\"', $input);
    return $input;
}
