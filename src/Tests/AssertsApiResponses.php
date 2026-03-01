<?php

namespace Hiteshpadhara\ResponseMacro\Tests;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;

trait AssertsApiResponses
{
    protected function assertApiSuccess(TestResponse $response, int $expectedStatusCode = 200): void
    {
        $response->assertStatus($expectedStatusCode);
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }

    protected function assertApiError(TestResponse $response, int $expectedStatusCode): void
    {
        $response->assertStatus($expectedStatusCode);
        $response->assertJsonStructure([
            'status',
            'message',
        ]);
    }

    protected function assertApiStructure(TestResponse $response, array $structure): void
    {
        $data = $response->json();

        foreach ($structure as $key) {
            PHPUnit::assertArrayHasKey($key, $data, "Response missing key: {$key}");
        }
    }

    protected function assertApiHasData(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
    }

    protected function assertApiHasErrors(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'status',
            'message',
            'errors',
        ]);
    }

    protected function assertApiHasPagination(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
            'meta' => [
                'current_page',
                'per_page',
                'total',
                'last_page',
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
        ]);
    }
}
