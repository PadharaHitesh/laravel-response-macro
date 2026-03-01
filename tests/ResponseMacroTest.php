<?php

namespace Hiteshpadhara\ResponseMacro\Tests;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Validator;
use Orchestra\Testbench\TestCase;
use Hiteshpadhara\ResponseMacro\ResponseMacroServiceProvider;

class ResponseMacroTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ResponseMacroServiceProvider::class];
    }

    /** @test */
    public function it_returns_standard_api_format()
    {
        $response = Response::api(200, 'Success', ['foo' => 'bar']);
        $payload = $response->getData(true);

        $this->assertEquals(200, $payload['status']);
        $this->assertEquals('Success', $payload['message']);
        $this->assertEquals(['foo' => 'bar'], $payload['data']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function it_hides_data_if_null()
    {
        $response = Response::api(404, 'Not Found');
        $payload = $response->getData(true);

        $this->assertArrayNotHasKey('data', $payload);
        $this->assertEquals(404, $payload['status']);
    }

    /** @test */
    public function api_success_returns_200()
    {
        $response = Response::apiSuccess(['user' => 'John']);
        $payload = $response->getData(true);

        $this->assertEquals(200, $payload['status']);
        $this->assertEquals('Success', $payload['message']);
        $this->assertEquals(['user' => 'John'], $payload['data']);
    }

    /** @test */
    public function api_created_returns_201()
    {
        $response = Response::apiCreated(['id' => 1]);
        $payload = $response->getData(true);

        $this->assertEquals(201, $payload['status']);
        $this->assertEquals('Resource created successfully', $payload['message']);
        $this->assertEquals(['id' => 1], $payload['data']);
    }

    /** @test */
    public function api_not_found_returns_404()
    {
        $response = Response::apiNotFound();
        $payload = $response->getData(true);

        $this->assertEquals(404, $payload['status']);
        $this->assertEquals('Resource not found', $payload['message']);
    }

    /** @test */
    public function api_unauthorized_returns_401()
    {
        $response = Response::apiUnauthorized();
        $payload = $response->getData(true);

        $this->assertEquals(401, $payload['status']);
        $this->assertEquals('Unauthorized', $payload['message']);
    }

    /** @test */
    public function api_forbidden_returns_403()
    {
        $response = Response::apiForbidden();
        $payload = $response->getData(true);

        $this->assertEquals(403, $payload['status']);
        $this->assertEquals('Forbidden', $payload['message']);
    }

    /** @test */
    public function api_validation_error_returns_422()
    {
        $validator = $this->createMockValidator([
            'email' => ['The email field is required.'],
            'password' => ['The password must be at least 8 characters.'],
        ]);

        $response = Response::apiValidationError($validator);
        $payload = $response->getData(true);

        $this->assertEquals(422, $payload['status']);
        $this->assertEquals('The given data was invalid.', $payload['message']);
        $this->assertArrayHasKey('errors', $payload);
        $this->assertArrayHasKey('email', $payload['errors']);
        $this->assertArrayHasKey('password', $payload['errors']);
    }

    /** @test */
    public function api_validation_error_with_array()
    {
        $errors = [
            'name' => ['Name is required'],
        ];

        $response = Response::apiValidationError($errors);
        $payload = $response->getData(true);

        $this->assertEquals(422, $payload['status']);
        $this->assertEquals($errors, $payload['errors']);
    }

    /** @test */
    public function api_server_error_returns_500()
    {
        $response = Response::apiServerError('Something went wrong');
        $payload = $response->getData(true);

        $this->assertEquals(500, $payload['status']);
        $this->assertEquals('Something went wrong', $payload['message']);
    }

    /** @test */
    public function api_too_many_requests_returns_429()
    {
        $response = Response::apiTooManyRequests();
        $payload = $response->getData(true);

        $this->assertEquals(429, $payload['status']);
        $this->assertEquals('Too many requests', $payload['message']);
    }

    /** @test */
    public function api_with_custom_message()
    {
        $response = Response::apiSuccess([], 'Custom message');
        $payload = $response->getData(true);

        $this->assertEquals('Custom message', $payload['message']);
    }

    /** @test */
    public function api_with_custom_error_code()
    {
        $response = Response::apiError('Custom error', 418);
        $payload = $response->getData(true);

        $this->assertEquals(418, $payload['status']);
        $this->assertEquals('Custom error', $payload['message']);
    }

    /** @test */
    public function api_with_pagination_includes_meta_and_links()
    {
        $paginator = $this->createMockPaginator();

        $response = Response::apiSuccess($paginator);
        $payload = $response->getData(true);

        $this->assertArrayHasKey('data', $payload);
        $this->assertArrayHasKey('meta', $payload);
        $this->assertArrayHasKey('links', $payload);
        $this->assertEquals(1, $payload['meta']['current_page']);
        $this->assertEquals(15, $payload['meta']['per_page']);
        $this->assertEquals(100, $payload['meta']['total']);
    }

    protected function createMockPaginator()
    {
        $paginator = $this->createMock(LengthAwarePaginator::class);

        $paginator->method('items')->willReturn([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);
        $paginator->method('currentPage')->willReturn(1);
        $paginator->method('perPage')->willReturn(15);
        $paginator->method('total')->willReturn(100);
        $paginator->method('lastPage')->willReturn(7);
        $paginator->method('url')->willReturnMap([
            [1, 'https://example.com?page=1'],
            [7, 'https://example.com?page=7'],
        ]);
        $paginator->method('previousPageUrl')->willReturn(null);
        $paginator->method('nextPageUrl')->willReturn('https://example.com?page=2');

        return $paginator;
    }

    protected function createMockValidator(array $errors)
    {
        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn($errors);

        return $validator;
    }
}
