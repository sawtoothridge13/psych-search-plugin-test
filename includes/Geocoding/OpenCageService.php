<?php
namespace WPPsychSearch\Geocoding;

use OpenCage\Geocoder\Geocoder;

/**
 * Service for handling geocoding tasks using OpenCage Data API
 */
class OpenCageService {
    /**
     * @var Geocoder
     */
    private $geocoder;

    /**
     * OpenCageService constructor.
     */
    public function __construct() {
        $this->geocoder = new Geocoder(get_option('opencage_api_key'));
    }

    /**
     * Geocode an address
     *
     * @param string $address
     * @return array|null
     */
    public function geocode($address) {
        try {
            $result = $this->geocoder->geocode($address);
            if (!empty($result['results'])) {
                return $result['results'][0]['geometry'];
            }
        } catch (\Exception $e) {
            error_log('Geocoding error: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * Reverse geocode coordinates
     *
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
    public function reverseGeocode($lat, $lng) {
        try {
            $result = $this->geocoder->reverse($lat, $lng);
            if (!empty($result['results'])) {
                return $result['results'][0]['formatted'];
            }
        } catch (\Exception $e) {
            error_log('Reverse geocoding error: ' . $e->getMessage());
        }
        return null;
    }
}
