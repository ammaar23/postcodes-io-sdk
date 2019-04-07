<?php

use PHPUnit\Framework\TestCase;
use Ammaar23\Postcodes\Postcode;
use Ammaar23\Postcodes\PostcodeException;
use GuzzleHttp\Psr7\Response;

class PostcodeTest extends TestCase
{

    const POSTCODE_VALID = 'M602LA';
    const POSTCODE_INVALID = 'M6002LA';
    const POSTCODE_PARTIAL = 'M60';
    const POSTCODES = ['OX49 5NU', 'NE30 1DP'];
    const ATTRIBUTES = ['postcode', 'longitude', 'latitude'];
    const LATITUDE_VALID = 51.7923246977375;
    const LONGITUDE_VALID = 0.629834723775309;
    const GEOLOCATIONS = [
        ['latitude' => self::LATITUDE_VALID, 'longitude' => self::LONGITUDE_VALID],
        ['latitude' => 53.5351312861402, 'longitude' => -2.49690382054704, 'radius' => 1000, 'limit' => 5]
    ];

    /**
     * A postcode lookup method test case.
     * 
     * @param string $attribute
     * 
     * @return void
     * @dataProvider lookupDataProvider
     */
    public function testLookup(string $attribute)
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'lookup', [self::POSTCODE_VALID]);
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasAttribute($attribute, $response);
    }

    /**
     * A postcode bulk lookup method test case.
     * 
     * @return void
     */
    public function testLookupBulk()
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'lookupBulk', [self::POSTCODES, self::ATTRIBUTES]);
        $this->assertTrue(is_array($response));
    }

    /**
     * A postcode reverse Geocoding method test case.
     * 
     * @return void
     */
    public function testReverseGeocode()
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'reverseGeocode', [
            self::LATITUDE_VALID, self::LONGITUDE_VALID
        ]);
        $this->assertTrue(is_array($response));
    }

    /**
     * A postcode bulk reverse Geocoding method test case.
     * 
     * @return void
     */
    public function testReverseGeocodeBulk()
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'reverseGeocodeBulk', [
            self::GEOLOCATIONS, self::ATTRIBUTES, 2000
        ]);
        $this->assertTrue(is_array($response));
    }

    /**
     * A postcode random method test case.
     * 
     * @param string $attribute
     * 
     * @return void
     * @dataProvider lookupDataProvider
     */
    public function testRandom(string $attribute)
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'random');
        $this->assertInstanceOf(stdClass::class, $response);
        $this->assertObjectHasAttribute($attribute, $response);
    }

    /**
     * A postcode validate method test case.
     * 
     * @return void
     */
    public function testValidate()
    {
        $postcodeClass = new Postcode();
        $this->assertTrue($this->invokeMethod($postcodeClass, 'validate', [self::POSTCODE_VALID]));
        $this->assertFalse($this->invokeMethod($postcodeClass, 'validate', [self::POSTCODE_INVALID]));
    }

    /**
     * A postcode validate method test case.
     * 
     * @return void
     */
    public function testNearest()
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'nearest', [self::POSTCODE_VALID]);
        $this->assertTrue(is_array($response));
    }

    /**
     * A postcode auto complete method test case.
     * 
     * @return void
     */
    public function testAutocomplete()
    {
        $postcodeClass = new Postcode();
        $response = $this->invokeMethod($postcodeClass, 'autocomplete', [self::POSTCODE_PARTIAL]);
        $this->assertTrue(is_array($response));
    }

    /**
     * A postcode query method test case.
     * 
     * @return void
     */
    public function testQuery()
    {
        $postcodeClass = new Postcode();
        $responseArray = $this->invokeMethod($postcodeClass, 'query', [self::POSTCODE_VALID]);
        $responseNull = $this->invokeMethod($postcodeClass, 'query', [self::POSTCODE_INVALID]);
        $this->assertTrue(is_array($responseArray));
        $this->assertNull($responseNull);
    }

    /**
     * A postcode exception test case.
     * 
     * @return void
     */
    public function testMakeRequest()
    {
        $this->expectException(PostcodeException::class);
        $postcodeClass = new Postcode();
        $this->invokeMethod($postcodeClass, 'makeRequest', ['/exception']);
    }

    /**
     * A postcode exception test case.
     * 
     * @return void
     */
    public function testHandleResponse()
    {
        $this->expectException(PostcodeException::class);
        $postcodeClass = new Postcode();
        $response = new Response(404, [
            'Content-Type' => 'application/json'
        ], json_encode(['status' => 404, 'error' => 'Postcode not found']));
        $this->invokeMethod($postcodeClass, 'handleResponse', [$response]);
    }

    /**
     * Data provider for lookup test case.
     * 
     * @return array
     */
    public function lookupDataProvider()
    {
        return [
            ['postcode'], ['quality'], ['eastings'], ['northings'], ['country'],
            ['nhs_ha'], ['longitude'], ['latitude'], ['european_electoral_region'],
            ['primary_care_trust'], ['region'], ['lsoa'], ['msoa'], ['incode'],
            ['outcode'], ['parliamentary_constituency'], ['admin_district'], ['parish'],
            ['admin_county'], ['admin_ward'], ['ced'], ['ccg'], ['nuts'], ['codes']
        ];
    }

    /**
     * Invoke a given class method.
     * 
     * @param mixed $object
     * @param string $methodName
     * @param array $parameters
     * 
     * @return mixed
     */
    private function invokeMethod(&$object, string $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
