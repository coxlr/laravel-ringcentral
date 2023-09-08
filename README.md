# A Laravel package for the RingCentral SDK for PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/coxlr/laravel-ringcentral.svg?style=flat-square)](https://packagist.org/packages/coxlr/laravel-ringcentral)
[![Tests](https://github.com/coxlr/laravel-ringcentral/actions/workflows/run-tests.yml/badge.svg)](https://github.com/coxlr/laravel-ringcentral/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/coxlr/laravel-ringcentral.svg?style=flat-square)](https://packagist.org/packages/coxlr/laravel-ringcentral)


This is a simple Laravel Service Provider providing access to the [RingCentral SDK for PHP][client-library].

## Installation

This package requires PHP 8.0 and Laravel 8 or higher.

To install the PHP client library using Composer:

```bash
composer require coxlr/laravel-ringcentral
```

The package will automatically register the `RingCentral` provider and facade.


You can publish the config file with:
```bash
php artisan vendor:publish --provider="Coxlr\RingCentral\RingCentralServiceProvider" --tag="config"
```


Then update `config/ringcentral.php` with your credentials. Alternatively, you can update your `.env` file with the following:

```dotenv
RINGCENTRAL_CLIENT_ID=my_client_id
RINGCENTRAL_CLIENT_SECRET=my_client_secret
RINGCENTRAL_SERVER_URL=my_server_url
RINGCENTRAL_USERNAME=my_username
RINGCENTRAL_OPERATOR_TOKEN=my_operator_jwt

#If admin details are a different extension to the operator
RINGCENTRAL_ADMIN_TOKEN=my_admin_jwt
```
This package uses the JWT autentication method. You can learn more about setting up JWT for your RingCentral account [here](https://developers.ringcentral.com/guide/authentication/jwt/quick-start).

## Usage

To use the RingCentral Client Library you can use the facade, or request the instance from the service container.

### Sending an SMS message (requires login in extension to be company operator)

```php
RingCentral::sendMessage([
    'to'   => '18042221111',
    'text' => 'Using the facade to send a message.'
]);
```

Or

```php
$ringcentral = app('ringcentral');

$ringcentral->sendMessage([
    'to'   => '18042221111',
    'text' => 'Using the instance to send a message.'
]);
```


#### Properties

| Name      | Required | Type          | Default     | Description |
| ---       | ---      | ---           | ---         | ---         |
| to        | true      | String     |             | The number to send the message to, must include country code |
| text        | true      | String   |             | The text of the message to send |

### Retrieving Extensions (requires admin access)

```php
RingCentral::getExtensions();
```

Or

```php
$ringcentral = app('ringcentral');

$ringcentral->getExtensions();
```

### Get messages sent and received for the operator

```php
RingCentral::getOperatorMessages();
```

Or

```php
$ringcentral = app('ringcentral');

$ringcentral->getOperatorMessages();
```

The default from date is the previous 24 hours, to specify the date to search from pass the require date as a parameter.

```php
RingCentral::getOperatorMessages((new \DateTime())->modify('-1 hours'));
```

#### Parameters

| Name      | Required | Type          | Default     | Description |
| ---       | ---      | ---           | ---         | ---         |
| fromDate  | false    | Object      |             | The date and time to start the search from must be a PHP date object |
| toDate  | false    | Object      |             | The date and time to end the search must be a PHP date object |
| perPage  | false    | Int      |  100           | The number of records to return per page |

### Get messages sent and received for a given extension (Needs admin access)

```php
RingCentral::getMessagesForExtensionId(12345678);
```

Or

```php
$ringcentral = app('ringcentral');

$ringcentral->getMessagesForExtensionId(12345678);
```

The default from date is the previous 24 hours, to specficy the date to search from pass the require date as a parameter.

```php
RingCentral::getMessagesForExtensionId(12345678, (new \DateTime())->modify('-1 hours'));
```

#### Parameters

| Name      | Required | Type          | Default     | Description |
| ---       | ---      | ---           | ---         | ---         |
| extensionId  | true    | String      |             | The RingCentral extension Id of the extension to retrieve the messages for |
| fromDate  | false    | Object      |             | The date and time to start the search from must be a PHP date object|
| toDate  | false    | Object      |             | The date and time to end the search must be a PHP date object |
| perPage  | false    | Int      |  100           | The number of records to return per page |


### Get a messages attachment (requires admin access)

```php
RingCentral::getMessageAttachmentById(12345678, 910111213, 45678910);
```

Or

```php
$ringcentral = app('ringcentral');

$ringcentral->getMessageAttachmentById(12345678, 910111213, 45678910);
```


#### Parameters

| Name      | Required | Type          | Default     | Description |
| ---       | ---      | ---           | ---         | ---         |
| extensionId  | true    | String      |             | The RingCentral extension Id of the extension the messages belongs to |
| messageId  | true    | String      |             | The id of the message of the the attachment belongs to |
| attachmentId  | true    | String      |             | The id of the attachment |



For more information on using the RingCentral client library, see the [official client library repository][client-library].

[client-library]: https://github.com/ringcentral/ringcentral-php


## Testing

``` bash
composer test
```
If using the RingCentral sandbox environment when testing set the following environment variable to true to handle sandbox message prefix.

```dotenv
RINGCENTRAL_IS_SANDBOX=true
```
An optional environment value can be set to prevent hitting RingCentral rate limits when testing. This will add a delay for the set seconds before each test.

```dotenv
RINGCENTRAL_DELAY_REQUEST_SECONDS=20
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hey@leecox.me instead of using the issue tracker.

## Credits

- [Lee Cox](https://github.com/coxlr)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
