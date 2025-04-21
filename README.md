# Laravel Response Macro

[![Packagist Version](https://img.shields.io/packagist/v/hiteshpadhara/laravel-response-macro.svg?style=flat-square)](https://packagist.org/packages/hiteshpadhara/laravel-response-macro)
[![Build Status](https://img.shields.io/github/actions/workflow/status/hiteshpadhara/laravel-response-macro/run-tests.yml?branch=main&style=flat-square)](https://github.com/hiteshpadhara/laravel-response-macro/actions)
[![License](https://img.shields.io/packagist/l/hiteshpadhara/laravel-response-macro.svg?style=flat-square)](LICENSE)

> Add a `Response::api()` macro for uniform JSON API responses in Laravel.

## Installation

Install the package via Composer:

```bash
composer require hiteshpadhara/laravel-response-macro
```

Laravel ≥10 will auto-discover the service provider. For older versions, register it manually:

```php
// config/app.php
'providers' => [
    // ...
    hiteshpadhara\ResponseMacro\ResponseMacroServiceProvider::class,
];
```

## Usage

Use the macro in your controllers or routes:

```php
use Illuminate\Support\Facades\Response;

return Response::api(200, 'Success', [
    'user' => $user
]);
```

Generates:

```json
{
  "status": 200,
  "message": "Success",
  "data": {
    "user": { /* ... */ }
  }
}
```

If the `$data` parameter is `null`, the `data` key is omitted.

## Configuration

Publish the config to customize response keys:

```bash
php artisan vendor:publish --tag=config
```

Modify `config/response.php`:

```php
return [
    'status_key'  => 'status',
    'message_key' => 'message',
    'data_key'    => 'data',
];
```

## Testing

Run the package tests using PHPUnit and Testbench:

```bash
composer install
vendor/bin/phpunit
```

## Contributing

1. Fork the repository.
2. Create your feature branch: `git checkout -b feature/YourFeature`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/YourFeature`.
5. Submit a pull request.

## Licensing

This package is open-sourced software licensed under the [MIT license](LICENSE).

