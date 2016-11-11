<?php

require __DIR__ . '/vendor/autoload.php';

$Pro6pp = new \PendoNL\Pro6pp\Pro6pp('api_code', 'json');

/**
 * Autocomplete an address. This can be achieved in different ways
 * (1) With a nl_fourpp postalcode
 * (2) With a nl_sixpp postalcode
 * (3) With a nl_sixpp postalcode housenumber and extension
 */

$Pro6pp->autocomplete(6225);
$Pro6pp->autocomplete('6225XS');
$Pro6pp->autocomplete('6225XS', 7);
$Pro6pp->autocomplete('6225XS', 7, 'c');

/**
 * Reverse address look-up. Provide the method with valid lat/lng
 * and you'll be presented with an address if found.
 */

$Pro6pp->reverse(50.858030, 5.717376);

/**
 * Find the nearest postalcodes from a given set of postalcodes compared
 * to a single postalcode. The second parameter takes an array with either
 * a nl_fourpp, a nl_sixpp or a set of lat/lng.
 */

$Pro6pp->locator(['6220','6221','6223','6224'], ['nl_fourpp' => 6216]);

/**
 * Find all postals within a given range. The second parameter is in meters.
 */

$Pro6pp->range(6225, 2500);

/**
 * Get autocomplete suggestions for city names, takes a second parameter for
 * the maximum number of results.
 */

$Pro6pp->suggest('Maast', 10);

/**
 * Calculate the distance between two nl_fourpp, 3rd parameter can be set to 'road',
 * be carefull: this is for supported account only.
 */

$Pro6pp->distance(6225, 6210, 'straight');

/**
 * Calculate distance between two coordinates
 */

$Pro6pp->coordinate_distance(50.858030, 5.717376, 50.840078, 5.659258);