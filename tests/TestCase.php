<?php

namespace Hiteshpadhara\ResponseMacro\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Hiteshpadhara\ResponseMacro\ResponseMacroServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            ResponseMacroServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup default configuration values
        $app['config']->set('response.status_key', 'status');
        $app['config']->set('response.message_key', 'message');
        $app['config']->set('response.data_key', 'data');
    }
}
