# postcodes-io-sdk [![Build Status](https://travis-ci.org/ammaar23/postcodes-io-sdk.svg?branch=master)](https://travis-ci.org/ammaar23/postcodes-io-sdk) [![Latest Stable Version](https://poser.pugx.org/ammaar23/postcodes-io-sdk/v/stable.svg)](https://packagist.org/packages/ammaar23/postcodes-io-sdk) [![Total Downloads](https://poser.pugx.org/ammaar23/postcodes-io-sdk/downloads.svg)](https://packagist.org/packages/ammaar23/postcodes-io-sdk) [![License](https://poser.pugx.org/ammaar23/postcodes-io-sdk/license.svg)](https://packagist.org/packages/ammaar23/postcodes-io-sdk)
A simple PHP sdk for [Postcodes.io](https://postcodes.io)

## Install

Install using composer:

```shell
$ composer require ammaar23/postcodes-io-sdk
```

## Documentation

### Basic Usage Example

```php
use Ammaar23\Postcodes\Postcode;
use Ammaar23\Postcodes\PostcodeException;

try {
    $postcodeService = new Postcode();
    $response = $postcodeService->lookup('M60 2LA');
    echo $response->admin_district;
} catch(PostcodeException $e) {
    echo $e->getMessage();
} catch(\Exception $e) {
    echo $e->getMessage();
}
```

> You can catch specific `Ammaar23\Postcodes\PostcodeException` and/or catch general `\Exception` to catch any type.

### Add/Modify Configuration Parameters

You can look at [Guzzle HTTP Request Options](http://docs.guzzlephp.org/en/stable/request-options.html) to find out the availabe options.

```php
$postcodeService = new Postcode([
    'headers' => [
        'User-Agent' => 'testing/1.0',
        'Accept' => 'application/json'
    ],
    'timeout' => 2.0
]);
```

### Methods

#### Lookup a postcode

Returns a single postcode entity for a given postcode (case, space insensitive).

```php
// Definition
function lookup(string $postcode): stdClass;

// Example
$postcodeService->lookup('M60 2LA');
```

#### Bulk lookup postcodes

Returns a list of matching postcodes and respective available data.

```php
// Definition
function lookupBulk(array $postcodes, array $attributes = []): array;

// Examples
$postcodeService->lookupBulk(['OX49 5NU', 'NE30 1DP']);
$postcodeService->lookupBulk(
    ['OX49 5NU', 'NE30 1DP'],
    ['postcode', 'longitude', 'latitude']
);
```

*  `$attributes` (not required) is an array attributes to be returned in the result object(s).

#### Reverse Geocoding

Returns nearest postcodes for a given longitude and latitude.

```php
// Definition
function reverseGeocode(float $latitude, float $longitude, array $options = []): array;

// Examples
$postcodeService->reverseGeocode(51.7923246977375, 0.629834723775309);
$postcodeService->reverseGeocode(51.7923246977375, 0.629834723775309, [
    'limit' => 5,
    'radius' => 1000
]);
```

*  `limit` (not required) Limits number of postcodes matches to return. Defaults to 10. Needs to be less than 100.
*  `radius` (not required) Limits number of postcodes matches to return. Defaults to 100m. Needs to be less than 2,000m.

#### Bulk Reverse Geocoding

Bulk translates geolocations into Postcodes.

```php
// Definition
function reverseGeocodeBulk(array $geolocations, array $attributes = [], int $wideSearch = null): array;

// Examples
$postcodeService->reverseGeocodeBulk([
    ['latitude' => 51.7923246977375, 'longitude' => 0.629834723775309],
    ['latitude' => 53.5351312861402, 'longitude' => -2.49690382054704, 'radius' => 1000, 'limit' => 5]
]);
$postcodeService->reverseGeocodeBulk([
    ['latitude' => 51.7923246977375, 'longitude' => 0.629834723775309],
    ['latitude' => 53.5351312861402, 'longitude' => -2.49690382054704, 'radius' => 1000, 'limit' => 5]
], ['postcode', 'longitude', 'latitude']);
$postcodeService->reverseGeocodeBulk([
    ['latitude' => 51.7923246977375, 'longitude' => 0.629834723775309],
    ['latitude' => 53.5351312861402, 'longitude' => -2.49690382054704, 'radius' => 1000, 'limit' => 5]
], ['postcode', 'longitude', 'latitude'], 1000);
```

*  Maximum of 100 geolocations per request.
*  `$attributes` (not required) is an array attributes to be returned in the result object(s).
*  `$wideSearch` (not required) Search up to 20km radius, but subject to a maximum of 10 results.

#### Random Postcode

Returns a random postcode and all available data for that postcode.

```php
// Definition
function random(array $options = []): stdClass;

// Examples
$postcodeService->random();
$postcodeService->random([
    'outcode' => 'M60'
]);
```

*  `outcode` (not required) Filters random postcodes by outcode. Returns null if invalid outcode.

#### Validate a postcode

Convenience method to validate a postcode.

```php
// Definition
function validate(string $postcode): bool;

// Example
$postcodeService->validate('M60 2LA');
```

#### Validate a postcode format

Convenience method to validate a postcode format.

```php
// Definition
function validateFormat(string $postcode): bool;

// Example
$postcodeService->validateFormat('M60 2LA');
```

> `validateFormat` validates the format only where as `validate` check's if it exists in the Postcodes.io database or not.

#### Nearest postcodes for postcode

Returns nearest postcodes for a given postcode.

```php
// Definition
function nearest(string $postcode, array $options = []): array;

// Examples
$postcodeService->nearest('M60 2LA');
$postcodeService->nearest('M60 2LA', [
    'limit' => 5,
    'radius' => 1000
]);
```

*  `limit` (not required) Limits number of postcodes matches to return. Defaults to 10. Needs to be less than 100.
*  `radius` (not required) Limits number of postcodes matches to return. Defaults to 100m. Needs to be less than 2,000m.

#### Autocomplete a postcode partial

Convenience method to return an list of matching postcodes.

```php
// Definition
function autocomplete(string $postcode, array $options = []): array;

// Examples
$postcodeService->autocomplete('M60');
$postcodeService->autocomplete('M60', ['limit' => 5]);
```

*  `limit` (not required) Limits number of postcodes matches to return. Defaults to 10. Needs to be less than 100.

#### Query for postcode

Submit a postcode query and receive a complete list of postcode matches and all associated postcode data. The result set can either be empty or populated with up to 100 postcode entities.

```php
// Definition
function query(string $query, array $options = []): array|null;

// Examples
$postcodeService->query('M60 2LA');
$postcodeService->query('M60 2LA', ['limit' => 5]);
```

*  `limit` (not required) Limits number of postcodes matches to return. Defaults to 10. Needs to be less than 100.

## Testing

```shell
$ composer test
```

OR with coverage:

```shell
$ composer test-coverage
```

## License
MIT License
&copy; 2019 &ndash; [Ammaar Latif](https://twitter.com/ammaar23)
