<?php
namespace WPPsychSearch\Admin;

/**
 * Manages the admin interface and settings for the plugin
 */
class AdminManager {
    /**
     * AdminManager constructor.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Psych Search Settings', 'wp-psych-search'),
            __('Psych Search', 'wp-psych-search'),
            'manage_options',
            'wp-psych-search',
            [$this, 'settings_page'],
            'dashicons-admin-generic'
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('wp_psych_search_options', 'opencage_api_key');

        add_settings_section(
            'wp_psych_search_section',
            __('API Settings', 'wp-psych-search'),
            null,
            'wp-psych-search'
        );

        add_settings_field(
            'opencage_api_key',
            __('OpenCage API Key', 'wp-psych-search'),
            [$this, 'api_key_field'],
            'wp-psych-search',
            'wp_psych_search_section'
        );
    }

    /**
     * Display API key field
     */
    public function api_key_field() {
        $api_key = get_option('opencage_api_key');
        echo '<input type="text" name="opencage_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
    }

    /**
     * Display settings page
     */
    public function settings_page() {
        echo '<div class="wrap">';
        echo '<h1>' . __('Psych Search Settings', 'wp-psych-search') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('wp_psych_search_options');
        do_settings_sections('wp-psych-search');
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}
