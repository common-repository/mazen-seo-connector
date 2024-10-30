<?php
/**
 * UNINSTALL PLUGIN
 * Remove tables and options
 */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

global $wpdb;

// delete tables
$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix ."mazen_redirections");

// delete options
delete_option('optimizme_mazen_db_version');
$wpdb->query('DELETE FROM '. $wpdb->options .' WHERE option_name LIKE "optimizme_mazen_jwt_secret_%"');
