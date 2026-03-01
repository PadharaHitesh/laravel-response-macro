<?php

namespace Hiteshpadhara\ResponseMacro;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Validator;
use Throwable;

class ResponseMacroServiceProvider extends ServiceProvider
{
    protected array $defaultConfig = [
        'status_key' => 'status',
        'message_key' => 'message',
        'data_key' => 'data',
    ];

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/response.php' => config_path('response.php'),
        ], 'config');

        $this->registerApiMacro();
        $this->registerHelperMacros();
    }

    protected function registerApiMacro(): void
    {
        Response::macro('api', function (
            int $statusCode,
            string $message = '',
            mixed $data = null
        ) {
            $cfg = config('response', [
                'status_key' => 'status',
                'message_key' => 'message',
                'data_key' => 'data',
            ]);

            $body = [
                $cfg['status_key'] => $statusCode,
                $cfg['message_key'] => $message,
            ];

            if ($data instanceof LengthAwarePaginator) {
                $body[$cfg['data_key']] = $data->items();
                $body['meta'] = [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ];
                $body['links'] = [
                    'first' => $data->url(1),
                    'last' => $data->url($data->lastPage()),
                    'prev' => $data->previousPageUrl(),
                    'next' => $data->nextPageUrl(),
                ];
            } elseif (! is_null($data)) {
                $body[$cfg['data_key']] = $data;
            }

            return Response::json($body, $statusCode);
        });
    }

    protected function registerHelperMacros(): void
    {
        Response::macro('apiSuccess', function (mixed $data = null, string $message = 'Success') {
            return Response::api(200, $message, $data);
        });

        Response::macro('apiError', function (string $message = 'Error', int $statusCode = 400, mixed $data = null) {
            return Response::api($statusCode, $message, $data);
        });

        Response::macro('apiCreated', function (mixed $data = null, string $message = 'Resource created successfully') {
            return Response::api(201, $message, $data);
        });

        Response::macro('apiAccepted', function (mixed $data = null, string $message = 'Request accepted') {
            return Response::api(202, $message, $data);
        });

        Response::macro('apiNoContent', function (string $message = 'No content') {
            return Response::api(204, $message);
        });

        Response::macro('apiNotFound', function (string $message = 'Resource not found') {
            return Response::api(404, $message);
        });

        Response::macro('apiUnauthorized', function (string $message = 'Unauthorized') {
            return Response::api(401, $message);
        });

        Response::macro('apiForbidden', function (string $message = 'Forbidden') {
            return Response::api(403, $message);
        });

        Response::macro('apiValidationError', function ($errors, string $message = 'The given data was invalid.') {
            $cfg = config('response', [
                'status_key' => 'status',
                'message_key' => 'message',
                'data_key' => 'data',
            ]);

            if ($errors instanceof Validator) {
                $errors = $errors->errors();
            }

            $body = [
                $cfg['status_key'] => 422,
                $cfg['message_key'] => $message,
                'errors' => $errors,
            ];

            return Response::json($body, 422);
        });

        Response::macro('apiTooManyRequests', function (string $message = 'Too many requests') {
            return Response::api(429, $message);
        });

        Response::macro('apiServerError', function (string $message = 'Internal server error', ?Throwable $e = null) {
            $cfg = config('response', [
                'status_key' => 'status',
                'message_key' => 'message',
                'data_key' => 'data',
            ]);

            $body = [
                $cfg['status_key'] => 500,
                $cfg['message_key'] => $message,
            ];

            if ($e && config('app.debug')) {
                $body['exception'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }

            return Response::json($body, 500);
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/response.php', 'response');
    }
}
