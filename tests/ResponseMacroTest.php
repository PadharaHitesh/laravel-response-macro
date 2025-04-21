<?php
// tests/ResponseMacroTest.php

namespace YourVendor\ResponseMacro\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Response;
use YourVendor\ResponseMacro\ResponseMacroServiceProvider;

class ResponseMacroTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ResponseMacroServiceProvider::class];
    }

    /** @test */
    public function it_returns_standard_api_format()
    {
        $response = Response::api(201, 'Created', ['foo' => 'bar']);
        $payload  = $response->getData(true);

        $this->assertEquals(201, $payload['status']);
        $this->assertEquals('Created', $payload['message']);
        $this->assertEquals(['foo' => 'bar'], $payload['data']);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /** @test */
    public function it_hides_data_if_null()
    {
        $response = Response::api(404, 'Not Found');
        $payload  = $response->getData(true);

        $this->assertArrayNotHasKey('data', $payload);
        $this->assertEquals(404, $payload['status']);
    }
}