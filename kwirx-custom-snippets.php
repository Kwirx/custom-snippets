<?php
/*
Plugin Name: Kwirx Custom Snippets
Description: A plugin to insert custom PHP code snippets.
Version: 1.0
Author: Kwirx Creative
*/

// Add menu page
add_action('admin_menu', 'kwirx_cs_add_admin_page');
function kwirx_cs_add_admin_page() {
    add_menu_page('Kwirx Custom Snippets', 'Code Snippet', 'manage_options', 'kwirx-custom-snippets', 'kwirx_cs_admin_page', 'dashicons-editor-code', 110);
}

// Enqueue code editor scripts and styles
add_action('admin_enqueue_scripts', 'kwirx_cs_enqueue_code_editor');
function kwirx_cs_enqueue_code_editor($hook_suffix) {
    if ($hook_suffix !== 'toplevel_page_kwirx-custom-snippets') {
        return;
    }
    wp_enqueue_code_editor(array('type' => 'text/x-php'));
    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
    wp_enqueue_script('kwirx_cs_custom_js', plugin_dir_url(__FILE__) . 'src/kwirx-cs.js', array('jquery'), null, true);
    wp_localize_script('kwirx_cs_custom_js', 'kwirx_cs_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    add_action('admin_footer', 'kwirx_cs_add_editor_resize_script');
}

// Admin page content
function kwirx_cs_admin_page() {
    ?>
    <div class="wrap">
        <h1>Kwirx Custom Snippets</h1>
        <p>Insert your custom PHP code snippet below. It will be executed in the WordPress environment.</p>
        <div id="kwirx-cs-notice"></div>
        <form id="kwirx-cs-form" method="post" action="">
            <?php wp_nonce_field('kwirx_cs_save_code_snippet', 'kwirx_cs_nonce'); ?>
            <textarea id="kwirx_cs_code_snippet" name="kwirx_cs_code_snippet"><?php echo esc_textarea(get_option('kwirx_cs_code_snippet', '')); ?></textarea>
            <input type="submit" class="button-primary" value="Save Changes" />
        </form>
    </div>
    <?php
}

// Register AJAX handler for saving code snippet
add_action('wp_ajax_kwirx_cs_save_code_snippet', 'kwirx_cs_save_code_snippet');
function kwirx_cs_save_code_snippet() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kwirx_cs_save_code_snippet')) {
        wp_send_json_error(array('message' => 'Invalid nonce'), 400);
    }

    $code = isset($_POST['code']) ? wp_unslash($_POST['code']) : '';

    // Check for syntax errors
    $check_result = kwirx_cs_check_syntax($code);
    if (!$check_result['success']) {
        wp_send_json_error($check_result['data'], 400);
    }

    // Sanitize and save the code
    $code = kwirx_cs_sanitize_code($code);
    update_option('kwirx_cs_code_snippet', $code);

    wp_send_json_success(array('message' => 'Code snippet saved successfully', 'code' => $code));
}

// Execute custom code snippet
add_action('init', 'kwirx_cs_execute_code_snippet');
function kwirx_cs_execute_code_snippet() {
    $code = get_option('kwirx_cs_code_snippet', '');
    if (!empty($code)) {
        eval($code);
    }
}

// Uninstall hook to remove saved data
register_uninstall_hook(__FILE__, 'kwirx_cs_uninstall');
function kwirx_cs_uninstall() {
    delete_option('kwirx_cs_code_snippet');
}

// Adjust code editor height
function kwirx_cs_add_editor_resize_script() {
    ?>
    <style>
        .CodeMirror {
            height: calc(100vh - 300px) !important; /* Adjust as needed */
        }
        #kwirx_cs_notice {
            margin-top: 20px;
        }
    </style>
    <?php
}

// Function to check for syntax errors in PHP code
function kwirx_cs_check_syntax($code) {
    $filename = tempnam(sys_get_temp_dir(), 'php');
    file_put_contents($filename, "<?php\n" . $code);
    $output = null;
    $result_code = null;
    exec("php -l " . escapeshellarg($filename) . " 2>&1", $output, $result_code);
    unlink($filename);
    if ($result_code !== 0) {
        // Extract the error message and line number
        $error_message = implode("\n", $output);
        preg_match('/on line (\d+)/', $error_message, $line_matches);
        preg_match('/error:(.*?) in/', $error_message, $message_matches);
        
        $line_number = isset($line_matches[1]) ? intval($line_matches[1]) : 'N/A';
        $error_message = isset($message_matches[1]) ? trim($message_matches[1]) : $error_message;
        
        return array('success' => false, 'data' => array('message' => $error_message, 'line' => $line_number));
    }
    return array('success' => true);
}

// Sanitize code input
function kwirx_cs_sanitize_code($input) {
    $input = str_replace('<?php', '', $input);
    $input = str_replace('?>', '', $input);
    return $input;
}
?>
