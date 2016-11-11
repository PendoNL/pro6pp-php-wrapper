<?php

namespace PendoNL\Pro6pp;

class Pro6pp
{

    private $api_format = '';

    private $api_pretty = false;

    private $api_key = '';

    private $api_host = 'http://api.pro6pp.nl/v1/';

    private $data = [];

    /**
     * Pro6pp constructor.
     *
     * @param $api_key
     * @param $format
     * @param boolean $pretty
     */
    public function __construct($api_key, $format = 'json', $pretty = false) {
        $this->api_key = $api_key;
        $this->api_format = $format;
        $this->api_pretty = $pretty;
    }

    /**
     * @return string
     */
    public function getApikey() {
        return $this->api_key;
    }

    /**
     * Get a full address after providing a postalcode and number, the results also
     * include the coordinates from the address.
     *
     * @param $postalcode
     * @param $number
     *
     * @return mixed
     */
    public function autocomplete($postalcode, $number = '', $extension = '') {
        $postalcode = $this->determinePostalType($postalcode);

        $this->data = array_merge($postalcode, ['streetnumber' => $number, 'extension' => $extension]);

        return $this->call('autocomplete', $this->prepareData($this->data));
    }

    /**
     * Find an address by their coordinates, returns a full address if found.
     *
     * @param $lat
     * @param $lng
     *
     * @return mixed
     */
    public function reverse($lat, $lng) {
        $this->data = ['lat' => $lat, 'lng' => $lng];

        return $this->call('reverse', $this->prepareData($this->data));
    }

    /**
     * Returns a list of postalcodes sorted from nearest to farest. Either include a lat/lng
     * combination or a nl_fourpp in the optional array.
     *
     * @param $target_nl_fourpps
     * @param array $optional
     *
     * @return mixed
     */
    public function locator($target_nl_fourpps, $optional = []) {
        $this->data['target_nl_fourpps'] = implode(",", $target_nl_fourpps);

        $this->data = array_merge($this->data, $optional);

        return $this->call('locator', $this->prepareData($this->data));
    }

    /**
     * Returns a list of postalcodes within a given range.
     *
     * @param $nl_fourpp
     * @param int $range
     * @param int $per_page
     * @param int $page
     *
     * @return mixed
     */
    public function range($nl_fourpp, $range = 5000, $per_page = 10, $page = 1) {
        $this->data = ['nl_fourpp' => $nl_fourpp, 'range' => $range, 'per_page' => $per_page, 'page' => $page];

        return $this->call('range', $this->prepareData($this->data));
    }

    /**
     * Autocompletes a city name
     *
     * @param $nl_city
     * @param int $per_page
     *
     * @return mixed
     */
    public function suggest($nl_city, $per_page = 10) {
        $this->data = ['nl_city' => $nl_city, 'per_page' => $per_page];

        return $this->call('suggest', $this->prepareData($this->data));
    }

    /**
     * Calculates the distance between two nl_fourpp postalcodes. Optional you can choose between
     * road or straight distance.
     *
     * @param $from_nl_fourpp
     * @param $to_nl_fourpp
     * @param $algorithm
     *
     * @return mixed
     */
    public function distance($from_nl_fourpp, $to_nl_fourpp, $algorithm = 'road') {
        $this->data = ['from_nl_fourpp' => $from_nl_fourpp, 'to_nl_fourpp' => $to_nl_fourpp, 'algorithm' => $algorithm];

        return $this->call('distance', $this->prepareData($this->data));
    }

    /**
     * Helper method to calculate the distance between two coordinates.
     *
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     *
     * @param bool $miles
     *
     * @return float
     */
    public function coordinatesDistance($lat1, $lng1, $lat2, $lng2, $miles = false)
    {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lng1 *= $pi80;
        $lat2 *= $pi80;
        $lng2 *= $pi80;

        $r = 6372.797;
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return ($miles ? ($km * 0.621371192) : $km);
    }

    /**
     * Checks if one of two valid postal types was sent along.
     *
     * @param $postalcode
     *
     * @return array
     *
     * @throws \Exception
     */
    public function determinePostalType($postalcode) {
        $postalcode = str_replace(" ", "", $postalcode);

        if(strlen($postalcode) == 6) {
            return ['nl_sixpp' => $postalcode];
        }

        if(strlen($postalcode) == 4) {
            return ['nl_fourpp' => $postalcode];
        }

        throw new \Exception('No valid postalcode was found (nl_sixpp or nl_fourpp)');
    }

    /**
     * Make sure the data contains the api_key and desired format.
     *
     * @param $data
     *
     * @return mixed
     */
    protected function prepareData($data) {
        $data['auth_key'] = $this->api_key;
        $data['format'] = $this->api_format;
        $data['pretty'] = $this->api_pretty;

        return $data;
    }

    /**
     * Method to do the actual call to Pro6PP's API and return the data
     * in the given format.
     *
     * @param $path
     * @param $data
     *
     * @return mixed
     */
    protected function call($path, $data) {
        $url = $this->api_host . $path .'?'. http_build_query($data);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $return = curl_exec($ch);

        curl_close($ch);

        return $return;
    }

}
