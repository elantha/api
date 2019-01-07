<?php

namespace Elantha\Api\Tests;

use Elantha\Api\Tests\Errors\CodeRegistry;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiTest extends BaseApiTestCase
{
    public function testResponseContent()
    {
        // given and when
        $response = $this->invokeRequest(__FUNCTION__);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_OK, $responseBody['status']);
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
        // given and when
        $response = $this->invokeRequest(__FUNCTION__);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $responseBody['status']);
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
        // given and when
        $response = $this->invokeRequest(__FUNCTION__);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_OK, $responseBody['status']);
        $this->assertEquals([], $responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $this->assertEquals(['param' => 'value'], $responseBody['data']);
    }

    public function testExceptionErrors()
    {
        // given and when
        $response = $this->invokeRequest(__FUNCTION__);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseBody['status']);

        $this->assertNotEmpty($responseBody['errors']);
        $this->assertArraySubset([CodeRegistry::USER_NOT_FOUND => 'User not found: Jack'], $responseBody['errors']);

        $this->assertEquals([], $responseBody['validation_errors']);
    }

    public function testCustomErrors()
    {
        // given and when
        $response = $this->invokeRequest(__FUNCTION__);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $responseBody['status']);

        $this->assertNotEmpty($responseBody['errors']);
        $this->assertArraySubset([CodeRegistry::USER_NOT_FOUND => 'User not found'], $responseBody['errors']);
        $this->assertArraySubset([100 => 'Custom error'], $responseBody['errors']);

        $this->assertEquals([], $responseBody['validation_errors']);
    }

    public function testValidationErrors()
    {
        // given and when
        $response = $this->invokeRequest(__FUNCTION__, ['one' => 'Winston']);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $responseBody['status']);

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
        // given and when
        $response = $this->invokeRequest(__FUNCTION__, [
            'one'   => 11,
            'two'   => 'abc1234567',
            'three' => 15,
        ]);

        // then
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $responseBody = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseBody);
        $this->assertEquals(Response::HTTP_OK, $responseBody['status']);

        $this->assertEquals([], $responseBody['errors']);
        $this->assertEquals([], $responseBody['validation_errors']);

        $this->assertArraySubset(['one' => 11], $responseBody['data']);
    }
}
