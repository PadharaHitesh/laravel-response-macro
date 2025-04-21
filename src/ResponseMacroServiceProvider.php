<?php
// src/ResponseMacroServiceProvider.php

namespace YourVendor\ResponseMacro;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // publish config
        $this->publishes([
            __DIR__.'/../config/response.php' => config_path('response.php'),
        ], 'config');

        // register the macro
        Response::macro('api', function (
            int    $statusCode,
            string $message  = '',
            mixed  $data     = null
        ) {
            $cfg  = config('response');
            $body = [
                $cfg['status_key']  => $statusCode,
                $cfg['message_key'] => $message,
            ];
            if (! is_null($data)) {
                $body[$cfg['data_key']] = $data;
            }
            return Response::json($body, $statusCode);
        });
    }

    public function register(): void
    {
        // merge default config
        $this->mergeConfigFrom(__DIR__.'/../config/response.php', 'response');
    }
}