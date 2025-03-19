<?php
/**
 * Plugin Name: WP Psych Search
 * Description: Wordpress plugin for a searchable directory of psychotherapists with advanced geolocation and filtering capabilities, specifically tailored for the German market.
 * Version: 1.0.0
 * Author: Twinpictures
 * Author URI: https://twinpictures.de
 * Text Domain: wp-psych-search
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('WP_PSYCH_SEARCH_VERSION', '1.0.0');
define('WP_PSYCH_SEARCH_PATH', plugin_dir_path(__FILE__));
define('WP_PSYCH_SEARCH_URL', plugin_dir_url(__FILE__));

// Autoloader setup
require_once WP_PSYCH_SEARCH_PATH . 'vendor/autoload.php';

// Initialize the plugin
function wp_psych_search_init() {
    // Initialize core components
    new \WPPsychSearch\Core\Plugin();
}
add_action('plugins_loaded', 'wp_psych_search_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once WP_PSYCH_SEARCH_PATH . 'includes/class-activator.php';
    WPPsychSearch\Core\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    require_once WP_PSYCH_SEARCH_PATH . 'includes/class-deactivator.php';
    WPPsychSearch\Core\Deactivator::deactivate();
});
