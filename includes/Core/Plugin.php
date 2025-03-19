<?php
namespace WPPsychSearch\Core;

/**
 * Main plugin class responsible for initializing all components
 */
class Plugin {
    /**
     * Plugin constructor.
     */
    public function __construct() {
        $this->init_hooks();
        $this->init_components();
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        add_action('init', [$this, 'register_post_type']);
        add_action('init', [$this, 'register_meta_boxes']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Initialize geocoding service
        new Geocoding\OpenCageService();

        // Initialize search functionality
        new Search\SearchManager();

        // Initialize admin interface
        if (is_admin()) {
            new Admin\AdminManager();
        }
    }

    /**
     * Register the psychotherapist post type
     */
    public function register_post_type() {
        register_post_type('psychotherapist', [
            'labels' => [
                'name' => __('Psychotherapists', 'wp-psych-search'),
                'singular_name' => __('Psychotherapist', 'wp-psych-search'),
            ],
            'public' => true,
            'show_in_rest' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'has_archive' => true,
            'rewrite' => ['slug' => 'psychotherapists'],
            'menu_icon' => 'dashicons-heart',
        ]);
    }

    /**
     * Register meta boxes using MetaBox.io
     */
    public function register_meta_boxes() {
        // Personal details meta box
        add_filter('rwmb_meta_boxes', function($meta_boxes) {
            $meta_boxes[] = [
                'title' => __('Personal Details', 'wp-psych-search'),
                'id' => 'personal_details',
                'post_types' => ['psychotherapist'],
                'fields' => [
                    [
                        'name' => __('Degree', 'wp-psych-search'),
                        'id' => 'degree',
                        'type' => 'text',
                    ],
                    [
                        'name' => __('Gender', 'wp-psych-search'),
                        'id' => 'gender',
                        'type' => 'select',
                        'options' => [
                            'male' => __('Male', 'wp-psych-search'),
                            'female' => __('Female', 'wp-psych-search'),
                            'diverse' => __('Diverse', 'wp-psych-search'),
                        ],
                    ],
                ],
            ];
            return $meta_boxes;
        });
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $namespace = 'psych-search/v1';

        register_rest_route($namespace, '/search', [
            'methods' => 'GET',
            'callback' => [new REST\SearchEndpoint(), 'handle_search'],
            'permission_callback' => '__return_true',
            'args' => [
                'lat' => ['required' => true],
                'lng' => ['required' => true],
                'radius' => ['default' => 50],
                'page' => ['default' => 1],
                'per_page' => ['default' => 10],
            ],
        ]);
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'wp-psych-search',
            WP_PSYCH_SEARCH_URL . 'assets/css/frontend.css',
            [],
            WP_PSYCH_SEARCH_VERSION
        );

        wp_enqueue_script(
            'wp-psych-search',
            WP_PSYCH_SEARCH_URL . 'assets/js/frontend.js',
            ['jquery'],
            WP_PSYCH_SEARCH_VERSION,
            true
        );

        wp_localize_script('wp-psych-search', 'wpPsychSearch', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('psych-search/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
        ]);
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'psychotherapist') {
            wp_enqueue_style(
                'wp-psych-search-admin',
                WP_PSYCH_SEARCH_URL . 'assets/css/admin.css',
                [],
                WP_PSYCH_SEARCH_VERSION
            );

            wp_enqueue_script(
                'wp-psych-search-admin',
                WP_PSYCH_SEARCH_URL . 'assets/js/admin.js',
                ['jquery'],
                WP_PSYCH_SEARCH_VERSION,
                true
            );
        }
    }
}
