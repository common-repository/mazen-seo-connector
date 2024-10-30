<?php
/*
Plugin Name: Mazen SEO Connector
Description: Interaction with MAZEN, the first SEO software that saves your time
Version: 1.6.7
Author: Optimiz.me
Author URI: http://mazen-app.com/
Text Domain: mazen-seo-connector
Domain Path: /languages/
*/

defined('ABSPATH') or die('No script kiddies please!');

global $wpdb;
require __DIR__ . '/vendor/autoload.php';

// constants
define('OPTIMIZME_MAZEN_VERSION', '1.6.7');
define('OPTIMIZME_MAZEN_DB_VERSION', '1.1');
define('OPTIMIZME_MAZEN_FOR_WP_URL', plugin_dir_url(__FILE__));
define('OPTIMIZME_MAZEN_ENABLE_LOGS', 1);
define('OPTIMIZME_MAZEN_LOGS', dirname(__FILE__) . '/logs/actions.txt');
define('OPTIMIZME_MAZEN_TABLE_REDIRECTIONS', $wpdb->prefix . 'mazen_redirections');

// i18n
load_plugin_textdomain('mazen-seo-connector', false, OPTIMIZME_MAZEN_FOR_WP_URL . '/languages/');

// Load all required files
function require_all($dir, $depth = 0)
{
    // require all php files
    $scan = glob($dir . DIRECTORY_SEPARATOR . "*");
    foreach ($scan as $path) {
        if (preg_match('/\.php$/', $path)) {
            require_once($path);
        } elseif (is_dir($path)) {
            require_all($path, $depth + 1);
        }
    }
}
require_all(dirname(__FILE__) . '/classes');
require_all(dirname(__FILE__) . '/filters');

// don't texturize content
add_filter('run_wptexturize', '__return_false');

// Trigger after wordpress init
add_action('wp', array('\Optimizme\Mazen\OptimizmeMazenCore', 'mazenProcessCore'));

// plugin activation: database create/update
add_action('wpmu_new_blog', array('\Optimizme\Mazen\OptimizmeMazenInstall', 'mazenInstallScript'));                 // add table if a new blog is created
add_action('plugins_loaded', array('\Optimizme\Mazen\OptimizmeMazenInstall', 'mazenUpdateDbCheck'));                // check if db update is needed

// plugin activated/deactivated
register_activation_hook(__FILE__, array('\Optimizme\Mazen\OptimizmeMazenInstall', 'mazenInstallScript'));          // add table on plugin activation
register_deactivation_hook(__FILE__, array('\Optimizme\Mazen\OptimizmeMazenInstall', 'mazenDeactivatePlugin'));

// Mazen SEO Connector automatic update
add_filter('auto_update_plugin', array('\Optimizme\Mazen\OptimizmeMazenAutoUpdator', 'mazenAutomaticPluginUpdate'), 10, 2);

// Page Builders add-ons
add_filter('mazen_get_generated_html_in_shortcodes', array('\Optimizme\Mazen\OptimizmeMazenPluginsFilters', 'getGeneratedHtmlInShortcodes'), 10, 3);
add_filter('mazen_get_page_builder', array('\Optimizme\Mazen\OptimizmeMazenPluginsFilters', 'getPageBuilder'), 10, 0);

// wp-spamshield support
add_filter('wpss_misc_form_spam_check_bypass', array('\Optimizme\Mazen\OptimizmeMazenPluginsInteraction', 'disableWpSpamshieldForMazen'), 10);