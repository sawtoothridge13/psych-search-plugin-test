<?php
namespace WPPsychSearch\Search;

use WP_REST_Request;
use WP_Query;

/**
 * Manages search functionality for the plugin
 */
class SearchManager {
    /**
     * Handle search requests
     *
     * @param WP_REST_Request $request
     * @return array
     */
    public function handle_search(WP_REST_Request $request) {
        $lat = $request->get_param('lat');
        $lng = $request->get_param('lng');
        $radius = $request->get_param('radius');
        $page = $request->get_param('page');
        $per_page = $request->get_param('per_page');

        // Perform search query
        $query = new WP_Query([
            'post_type' => 'psychotherapist',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'meta_query' => [
                [
                    'key' => 'lat',
                    'value' => [$lat - $radius, $lat + $radius],
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL(10,8)'
                ],
                [
                    'key' => 'lng',
                    'value' => [$lng - $radius, $lng + $radius],
                    'compare' => 'BETWEEN',
                    'type' => 'DECIMAL(11,8)'
                ]
            ]
        ]);

        $results = [];
        while ($query->have_posts()) {
            $query->the_post();
            $results[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'link' => get_permalink(),
                'lat' => get_post_meta(get_the_ID(), 'lat', true),
                'lng' => get_post_meta(get_the_ID(), 'lng', true),
            ];
        }
        wp_reset_postdata();

        return $results;
    }
}
