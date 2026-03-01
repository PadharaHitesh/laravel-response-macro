# Laravel Response Macro

[![Latest Version](https://img.shields.io/packagist/v/hiteshpadhara/laravel-response-macro.svg?style=flat-square)](https://packagist.org/packages/hiteshpadhara/laravel-response-macro)
[![Total Downloads](https://img.shields.io/packagist/dt/hiteshpadhara/laravel-response-macro.svg?style=flat-square)](https://packagist.org/packages/hiteshpadhara/laravel-response-macro)
[![Build Status](https://img.shields.io/github/actions/workflow/status/hiteshpadhara/laravel-response-macro/tests.yml?branch=main&style=flat-square)](https://github.com/hiteshpadhara/laravel-response-macro/actions)
[![License](https://img.shields.io/packagist/l/hiteshpadhara/laravel-response-macro.svg?style=flat-square)](LICENSE)

> A simple, elegant way to create uniform JSON API responses in Laravel. Provides helper methods, exception handling, pagination support, and testing traits.

## Features

- Uniform JSON response structure for all API endpoints
- Convenient helper methods for common HTTP status codes
- Automatic validation error formatting
- Built-in exception handler for API responses
- Laravel Paginator support with meta and links
- PHPUnit testing traits for easier API testing
- Configurable response keys
- Laravel 10 and 11 support

## Installation

Install the package via Composer:

```bash
composer require hiteshpadhara/laravel-response-macro
```

The service provider will be auto-registered for Laravel 10+.

## Usage

### Basic Response

```php
use Illuminate\Support\Facades\Response;

return Response::api(200, 'Success', ['user' => $user]);
```

**Response:**
```json
{
  "status": 200,
  "message": "Success",
  "data": {
    "user": { "id": 1, "name": "John" }
  }
}
```

### Helper Methods

```php
// Success (200)
return Response::apiSuccess($data, 'Operation successful');

// Created (201)
return Response::apiCreated($resource, 'User created successfully');

// Accepted (202)
return Response::apiAccepted(null, 'Request accepted for processing');

// No Content (204)
return Response::apiNoContent();

// Not Found (404)
return Response::apiNotFound('User not found');

// Unauthorized (401)
return Response::apiUnauthorized('Please log in');

// Forbidden (403)
return Response::apiForbidden('Access denied');

// Too Many Requests (429)
return Response::apiTooManyRequests('Rate limit exceeded');

// Server Error (500)
return Response::apiServerError('Something went wrong');
```

### Validation Errors

```php
// With a Validator instance
return Response::apiValidationError($validator);

// Or with an errors array
return Response::apiValidationError([
    'email' => ['Email is required'],
    'password' => ['Password must be at least 8 characters']
]);
```

**Response:**
```json
{
  "status": 422,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Email is required"],
    "password": ["Password must be at least 8 characters"]
  }
}
```

### Custom Error Responses

```php
return Response::apiError('Custom error message', 418, ['details' => 'I\'m a teapot']);
```

### Pagination Support

When you pass a Laravel paginator, it automatically includes meta and links:

```php
$users = User::paginate(15);
return Response::apiSuccess($users);
```

**Response:**
```json
{
  "status": 200,
  "message": "Success",
  "data": [
    { "id": 1, "name": "John" },
    { "id": 2, "name": "Jane" }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
  },
  "links": {
    "first": "https://example.com/users?page=1",
    "last": "https://example.com/users?page=7",
    "prev": null,
    "next": "https://example.com/users?page=2"
  }
}
```

## Exception Handling

Add the trait to your exception handler to automatically convert exceptions to JSON API responses:

```php
namespace App\Exceptions;

use Hiteshpadhara\ResponseMacro\HandlesApiExceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use HandlesApiExceptions;

    // All exceptions will now return JSON responses
}
```

**Supported Exceptions:**
- `AuthenticationException` → 401
- `ModelNotFoundException` → 404
- `NotFoundHttpException` → 404
- `ValidationException` → 422
- `TokenMismatchException` → 419
- `HttpException` → Uses exception's status code

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

The package includes a trait to make testing API responses easier:

```php
use Hiteshpadhara\ResponseMacro\Tests\AssertsApiResponses;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use AssertsApiResponses;

    /** @test */
    public function it_returns_user_list()
    {
        $response = $this->getJson('/api/users');

        $this->assertApiSuccess($response);
        $this->assertApiHasData($response);
    }

    /** @test */
    public function it_returns_validation_errors()
    {
        $response = $this->postJson('/api/users', []);

        $this->assertApiError($response, 422);
        $this->assertApiHasErrors($response);
    }

    /** @test */
    public function it_returns_paginated_response()
    {
        $response = $this->getJson('/api/users?page=1');

        $this->assertApiHasPagination($response);
    }
}
```

## Testing the Package

Run the package tests:

```bash
composer install
vendor/bin/phpunit
```

## Requirements

- PHP 8.1 or higher
- Laravel 10 or 11

## Contributing

1. Fork the repository.
2. Create your feature branch: `git checkout -b feature/YourFeature`.
3. Commit your changes: `git commit -m 'Add some feature'`.
4. Push to the branch: `git push origin feature/YourFeature`.
5. Submit a pull request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
