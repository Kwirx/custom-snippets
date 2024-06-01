# Kwirx Custom Snippets

**Contributors:** Kwirx Creative  
**Tags:** snippets, code, custom, php, codemirror  
**Requires at least:** 5.0  
**Tested up to:** 6.5 
**Requires PHP:** 7.0  
**Stable tag:** 1.2 
**License:** GPLv3 or later  
**License URI:** https://www.gnu.org/licenses/gpl-3.0.html  

A plugin to insert custom PHP code snippets directly into your WordPress environment. The plugin allows you to write, save, and execute PHP code snippets through the WordPress admin panel.

## Description

Kwirx Custom Snippets provides an easy-to-use interface for inserting custom PHP code snippets directly in your WordPress site. The plugin uses the CodeMirror editor to provide syntax highlighting and code validation for PHP snippets. 

Once a snippet is saved, it will be executed in the WordPress environment, which is helpful for customizations and quick code experiments.

### Features

- Syntax highlighting with CodeMirror
- Code validation before saving
- Secure nonce verification
- Configuration via WordPress admin panel
- Safe execution environment for code snippets

### Screenshots

1. **Admin Panel Interface**  
   ![Admin Panel Interface](assets/screenshot-1.png)

## Installation

1. Upload the `kwirx-custom-snippets` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the "Kwirx Custom Snippets" menu in the WordPress admin panel.

## Usage

1. Go to **Code Snippet** from the WordPress admin menu.
2. Enter your PHP code snippet in the provided CodeMirror editor.
3. Click "Save Changes" to save and execute the snippet in the WordPress environment.

## Frequently Asked Questions

### Is it safe to use custom PHP code snippets?

While the plugin ensures basic security through input sanitization and nonce verification, it is important to carefully review and validate the PHP code you execute. Malicious or poorly written code can break your site or introduce security vulnerabilities.

### How can I remove a custom PHP snippet?

Simply clear the textarea containing the PHP code snippet and click "Save Changes." This will delete the stored snippet from the database.

### How can I uninstall the plugin?

Deactivate the plugin from the 'Plugins' menu in WordPress. After deactivation, you can delete the plugin files. All saved snippets will be removed upon plugin uninstall.

## Contributing

To contribute to the plugin, please fork the repository on GitHub and submit a pull request.

## Changelog

### For detailed change logs, please refer to the [GitHub Commits](https://github.com/Kwirx/custom-snippets/commits/main).

## License

This plugin is licensed under the GPLv3 or later. See https://www.gnu.org/licenses/gpl-3.0.html for more information.