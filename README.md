# postcodes-io-sdk
A simple PHP sdk for [Postcodes.io](https://postcodes.io)

# Install

Install using composer:

```
$ composer require ammaar23/postcodes-io-sdk
```

# Documentation

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
> `$attributes` (not required) is an array attributes to be returned in the result object(s).
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
> Maximum of 100 postcodes per request.
`limit` (not required) Limits number of postcodes matches to return. Defaults to 10. Needs to be less than 100.
`radius` (not required) Limits number of postcodes matches to return. Defaults to 100m. Needs to be less than 2,000m.

# Testing

```
$ composer test
```
OR with coverage:
```
$ composer test-coverage
```

# License
MIT License
&copy; 2019 &ndash; Ammaar Latif
