<?php

namespace Ammaar23\Postcodes;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface;
use Exception;
use stdClass;

class Postcode
{

    /**
     * The GuzzleHttp client instance.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * The configuration used for HTTP client.
     *
     * @var array
     */
    protected $config = [
        'base_uri' => 'https://api.postcodes.io'
    ];

    /**
     * Create a new class instance.
     * 
     * @param array $config
     * 
     * @return void
     * @codeCoverageIgnore
     */
    public function __construct(array $config = [])
    {
        $this->httpClient = new HttpClient(array_merge($config, $this->config));
    }

    /**
     * Lookup for a postcode and return it's data.
     * 
     * @param string $postcode
     * 
     * @return stdClass
     */
    public function lookup(string $postcode): stdClass
    {
        return $this->makeRequest('/postcodes/' . $postcode);
    }

    /**
     * Lookup for postcodes in bulk and return their data.
     * 
     * @param array $postcodes
     * @param array $attributes
     * 
     * @return array
     */
    public function lookupBulk(array $postcodes, array $attributes = []): array
    {
        $queryString = empty($attributes) ? null : '?filter=' . implode(',', $attributes);
        return $this->makeRequest('/postcodes' . $queryString, 'POST', ['postcodes' => $postcodes]);
    }

    /**
     * Return nearest postcodes for a given longitude & latitude.
     * 
     * @param float $latitude
     * @param float $longitude
     * @param array $options
     * 
     * @return array
     */
    public function reverseGeocode(float $latitude, float $longitude, array $options = []): array
    {
        return $this->makeRequest('/postcodes', 'GET', array_merge($options, [
            'lat' => $latitude,
            'lon' => $longitude
        ]));
    }

    /**
     * Perform reverse Geocoding in bulk.
     * 
     * @param array $geolocations
     * @param array $attributes
     * @param int $wideSearch
     * 
     * @return array
     */
    public function reverseGeocodeBulk(array $geolocations, array $attributes = [], int $wideSearch = null): array
    {
        $options = [];
        if (!empty($attributes)) {
            $options['filter'] = implode(',', $attributes);
        }

        if ($wideSearch) {
            $options['wideSearch'] = $wideSearch;
        }

        $queryString = empty($options) ? null : '?' . http_build_query($options);
        return $this->makeRequest('/postcodes' . $queryString, 'POST', ['geolocations' => $geolocations]);
    }

    /**
     * Return a random postcode.
     * 
     * @param array $options
     * 
     * @return stdClass
     */
    public function random(array $options = []): stdClass
    {
        return $this->makeRequest('/random/postcodes', 'GET', $options);
    }

    /**
     * Check if the given postcode is a valid UK postcode or not.
     * 
     * @param string $postcode
     * 
     * @return bool
     */
    public function validate(string $postcode): bool
    {
        return $this->makeRequest('/postcodes/' . $postcode . '/validate');
    }

    /**
     * Returns nearest postcodes for a given postcode.
     * 
     * @param string $postcode
     * @param array $options
     * 
     * @return array
     */
    public function nearest(string $postcode, array $options = []): array
    {
        return $this->makeRequest('/postcodes/' . $postcode . '/nearest', 'GET', $options);
    }

    /**
     * Auto complete a postcode partial with suggestions.
     * 
     * @param string $postcode
     * @param array $options
     * 
     * @return array
     */
    public function autocomplete(string $postcode, array $options = []): array
    {
        return $this->makeRequest('/postcodes/' . $postcode . '/autocomplete', 'GET', $options);
    }

    /**
     * Query for postcode.
     * 
     * @param string $query
     * @param array $options
     * 
     * @return array|null
     */
    public function query(string $query, array $options = [])
    {
        return $this->makeRequest('/postcodes', 'GET', array_merge($options, ['q' => $query]));
    }

    /**
     * Make a request to the Postcode API.
     * 
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * 
     * @return mixed
     */
    private function makeRequest(string $endpoint, string $method = 'GET', array $data = [])
    {
        $options = [];
        if (!empty($data)) {
            $options[$method === 'GET' ? 'query' : 'json'] = $data;
        }

        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
        } catch (Exception $e) {
            throw new PostcodeException($e->getMessage());
        }

        return $this->handleResponse($response);
    }

    /**
     * Handle response from the Postcode API.
     * 
     * @param ResponseInterface $response
     * 
     * @return mixed
     * @throws PostcodeException
     */
    private function handleResponse(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents());
        if ($response->getStatusCode() === 200 && $body->status === 200) {
            return $body->result;
        }

        throw new PostcodeException($body->error, $body->status);
    }
}
