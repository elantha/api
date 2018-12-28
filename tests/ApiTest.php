<?php

namespace Grizmar\Api\Tests;

use Grizmar\Api\Tests\Errors\CodeRegistry;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ApiTest extends BaseApiTestCase
{
    public function testResponseContent()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST');

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_OK, $responseBody['status']);
        $this->assertEquals([], $responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $testData = [
            'level1' => [
                'level2' => [
                    'one' => 'value',
                    'two' => 100,
                ],
            ],
        ];

        $this->assertEquals($testData, $responseBody['data']);
    }

    public function testExceptionWithResponse()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST');

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_FORBIDDEN, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_FORBIDDEN, $responseBody['status']);
        $this->assertNotEmpty($responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $testData = [
            'info' => [
                'one' => 'value',
                'two' => 0.15,
            ],
        ];

        $this->assertEquals($testData, $responseBody['data']);
    }

    public function testEmptyException()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST');

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_OK, $responseBody['status']);
        $this->assertEquals([], $responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $this->assertEquals(['param' => 'value'], $responseBody['data']);
    }

    public function testExceptionErrors()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST');

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_NOT_FOUND, $responseBody['status']);

        $this->assertNotEmpty($responseBody['errors']);
        $this->assertArraySubset([CodeRegistry::USER_NOT_FOUND => 'User not found: Jack'], $responseBody['errors']);

        $this->assertEquals([], $responseBody['validation_errors']);
    }

    public function testCustomErrors()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST');

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_NOT_FOUND, $responseBody['status']);

        $this->assertNotEmpty($responseBody['errors']);
        $this->assertArraySubset([CodeRegistry::USER_NOT_FOUND => 'User not found'], $responseBody['errors']);
        $this->assertArraySubset([100 => 'Custom error'], $responseBody['errors']);

        $this->assertEquals([], $responseBody['validation_errors']);
    }

    public function testValidationErrors()
    {
        // given
        $request = Request::create('test/' . __FUNCTION__, 'POST', ['one' => 'Winston']);

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_UNPROCESSABLE_ENTITY, $responseBody['status']);

        $this->assertEquals([], $responseBody['errors']);
        $this->assertNotEmpty($responseBody['validation_errors']);

        $errorSubset = [
            'one' => ['The one may not be greater than 6 characters.'],
            'two' => ['The two field is required.'],
        ];

        $this->assertArraySubset($errorSubset, $responseBody['validation_errors']);

        $this->assertEquals([], $responseBody['data']);
    }

    public function testValidationRules()
    {
        // given
        $params = [
            'one'   => 11,
            'two'   => 'abc1234567',
            'three' => 15,
        ];

        $request = Request::create('test/' . __FUNCTION__, 'POST', $params);

        // when
        $response = $this->app->handle($request);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(HttpResponse::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(HttpResponse::HTTP_OK, $responseBody['status']);

        $this->assertEquals([], $responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $this->assertArraySubset(['one' => 11], $responseBody['data']);
    }
}
