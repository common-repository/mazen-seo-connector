<?php
namespace Optimizme\Mazen;

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Class OptimizmeMazenInstall
 * @package Optimizme\Mazen
 */
class OptimizmeMazenInstall
{
    /**
     * Plugin activation: create tables in database
     */
    public static function mazenInstallScript()
    {
        global $wpdb;

        if (get_site_option('optimizme_mazen_db_version') != OPTIMIZME_MAZEN_DB_VERSION) {
            // install
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE ". OPTIMIZME_MAZEN_TABLE_REDIRECTIONS ." (
                    `id` mediumint(11) NOT NULL AUTO_INCREMENT,
                    `url_base` varchar(255) DEFAULT '' NOT NULL,
                    `url_redirect` varchar(255) DEFAULT '' NOT NULL,
                    `is_disabled` smallint(1) DEFAULT '0' NOT NULL,
					`created_at` datetime NOT NULL,
                    `updated_at` datetime NOT NULL,
                    PRIMARY KEY  (id)
              ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // update db version in options
            update_option('optimizme_mazen_db_version', OPTIMIZME_MAZEN_DB_VERSION);
        }
    }

    /**
     * Update database if update
     */
    public static function mazenUpdateDbCheck()
    {
        if (get_site_option('optimizme_mazen_db_version') != OPTIMIZME_MAZEN_DB_VERSION) {
            OptimizmeMazenInstall::mazenInstallScript();
        }
    }

    /**
     * Plugin deactivation
     */
    public static function mazenDeactivatePlugin()
    {
        // TODO
    }
}
