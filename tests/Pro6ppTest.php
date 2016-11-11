<?php

use PendoNL\Pro6pp\Pro6pp;

class Pro6ppTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test if the API key gets set when constructing.
     */
    public function test_it_sets_api_key()
    {
        $pro6pp = new Pro6pp('api_key');

        $this->assertEquals('api_key', $pro6pp->getApiKey());
    }

    /**
     * Test if the autocomplete method works.
     */
    public function test_it_autocompletes()
    {
        $data = '{"status":"ok","results":[{"extension":"C","nl_sixpp":"6225XS","street":"Geusseltweg","city":"Maastricht","municipality":"Maastricht","province":"Limburg","lat":50.8579834,"lng":5.7174217,"areacode":"043","streetnumber":7}]}';

        $stub = $this->createMock(Pro6pp::class);

        $stub->method('autocomplete')
            ->willReturn($data);

        $this->assertEquals($data, $stub->autocomplete('6225XS', 7, 'c'));
    }

    /**
     * Test if the reverse method works.
     */
    public function test_it_reverses()
    {
        $data = '{"status":"ok","results":{"precision":8,"province":"Limburg","municipality":"Maastricht","city":"Maastricht","nl_fourpp":6225,"lat":50.857970000000002,"lng":5.7173999999999996,"nl_sixpp":"6225XS","streets":["Geusseltweg"]}}';

        $stub = $this->createMock(Pro6pp::class);

        $stub->method('reverse')
            ->willReturn($data);

        $this->assertEquals($data, $stub->reverse(50.858030, 5.717376));
    }

    /**
     * Test if the locator method works.
     */
    public function test_it_locates()
    {
        $data = '{"status":"ok","results":[{"nl_fourpp":6225,"distance":0,"lat":50.86277,"lng":5.73217,"city":"Maastricht","municipality":"Maastricht","province":"Limburg"},{"nl_fourpp":6223,"distance":3606,"lat":50.88435,"lng":5.69387,"city":"Maastricht","municipality":"Maastricht","province":"Limburg"}]}';

        $stub = $this->createMock(Pro6pp::class);

        $stub->method('locator')
            ->willReturn($data);

        $this->assertEquals($data, $stub->locator([6223, 6225], ['nl_fourpp' => 6225]));
    }

    /**
     * Test if the range method works.
     */
    public function test_it_finds_within_range()
    {
        $data = '{"status":"ok","results":[{"nl_fourpp":"6225","distance":0,"lat":50.8628,"lng":5.7322},{"nl_fourpp":"6222","distance":1445,"lat":50.8659,"lng":5.7122}]}';

        $stub = $this->createMock(Pro6pp::class);

        $stub->method('range')
            ->willReturn($data);

        $this->assertEquals($data, $stub->range(6225, 1500));
    }

    /**
     * Test if the distance method works.
     */
    public function test_it_calculates_distance()
    {
        $data = '{"status":"ok","results":{"distance":4522}}';

        $stub = $this->createMock(Pro6pp::class);

        $stub->method('distance')
            ->willReturn($data);

        $this->assertEquals($data, $stub->distance(6225, 6214));
    }

    /**
     * Test if the suggest method works.
     */
    public function test_it_suggests()
    {
        $data = '{"status":"ok","results":[{"city_key":"agxlfnBybzZwcC1hcGlyHAsSDENpdHlTdWdnZXN0MiIKbWFhc2JvbW1lbAw","city":"Maasbommel","official_city":"Maasbommel","nl_fourpps":"6627","province":"Gelderland","lat":51.82719,"lng":5.53366}]}';

        // Create a stub for the Pro6pp class.
        $stub = $this->createMock(Pro6pp::class);

        // Configure the stub.
        $stub->method('suggest')
            ->willReturn($data);

        $this->assertEquals($data, $stub->suggest('maas', 1));
    }

    /**
     * Test if the distance between coordiantes method works.
     */
    public function test_it_calculates_distance_by_coordinates()
    {
        $Pro6pp = new Pro6pp('api_key');

        $this->assertEquals(4.5435663553281715, $Pro6pp->coordinate_distance(50.858030, 5.717376, 50.840078, 5.659258));
    }

    /**
     * Test to see if the code can determine a nl_fourpp postal.
     */
    public function test_it_can_determine_fourpp_postal()
    {
        $Pro6pp = new Pro6pp('api_key');

        $this->assertEquals('6225', $Pro6pp->determinePostalType('6225')['nl_fourpp']);
    }

    /**
     * Test to see if the code can determine a nl_fourpp postal.
     */
    public function test_it_can_determine_sixpp_postal()
    {
        $Pro6pp = new Pro6pp('api_key');

        $this->assertEquals('6225XS', $Pro6pp->determinePostalType('6225XS')['nl_sixpp']);
    }
}
