<?php
namespace WPPsychSearch\Core;

/**
 * Fired during plugin deactivation
 */
class Deactivator {
    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
