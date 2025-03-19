<?php
namespace WPPsychSearch\Core;

/**
 * Fired during plugin activation
 */
class Activator {
    /**
     * Activate the plugin
     */
    public static function activate() {
        self::create_tables();
        self::add_capabilities();
        flush_rewrite_rules();
    }

    /**
     * Create required database tables
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'psych_geo';

        // Only create the table if it doesn't exist
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            // Check if we have spatial support
            $has_spatial = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.plugins WHERE plugin_name = 'mysql_native_password' AND plugin_status = 'ACTIVE'") > 0;

            $sql = "CREATE TABLE $table_name (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `post_id` bigint(20) unsigned NOT NULL,
                `address` text NOT NULL,
                `lat` decimal(10,8) NOT NULL,
                `lng` decimal(11,8) NOT NULL,
                `filter_data` LONGTEXT DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `post_id` (`post_id`)
            ) $charset_collate;";

            // Add spatial index if supported
            if ($has_spatial) {
                $sql = "CREATE TABLE $table_name (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `post_id` bigint(20) unsigned NOT NULL,
                    `address` text NOT NULL,
                    `lat` decimal(10,8) NOT NULL,
                    `lng` decimal(11,8) NOT NULL,
                    `geometry` POINT NOT NULL,
                    `filter_data` LONGTEXT DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `post_id` (`post_id`),
                    SPATIAL KEY `geometry` (`geometry`)
                ) $charset_collate;";
            }

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            // Try to add foreign key constraint
            $wpdb->query("ALTER TABLE $table_name
                ADD CONSTRAINT `fk_post` FOREIGN KEY (`post_id`)
                REFERENCES `{$wpdb->posts}` (`ID`) ON DELETE CASCADE");
        }
    }

    /**
     * Add capabilities for psychotherapist post type
     */
    private static function add_capabilities() {
        // Get administrator role
        $admin = get_role('administrator');

        // Add capabilities for psychotherapist post type
        $capabilities = [
            'edit_psychotherapist',
            'read_psychotherapist',
            'delete_psychotherapist',
            'edit_psychotherapists',
            'edit_others_psychotherapists',
            'publish_psychotherapists',
            'read_private_psychotherapists',
            'delete_psychotherapists',
            'delete_private_psychotherapists',
            'delete_published_psychotherapists',
            'delete_others_psychotherapists',
            'edit_private_psychotherapists',
            'edit_published_psychotherapists',
        ];

        foreach ($capabilities as $cap) {
            $admin->add_cap($cap);
        }
    }
}
