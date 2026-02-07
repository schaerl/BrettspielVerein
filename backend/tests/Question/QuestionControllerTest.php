<?php

use BVZ\Question\QuestionController;
use BVZ\Question\QuestionDTO;
use BVZ\Question\QuestionService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestException;
use BVZ\Request\RequestSpecParser;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionControllerTest extends TestCase
{
    public function testThrowsWhenCalledWithAGetRequest(): void
    {
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->never())->method('ask');

        $controller = new QuestionController($mockService, $mockParser);

        $controller->handle(new GetRequest("Test"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: GET not supported', xdebug_get_headers());
    }

    public function testReturnsWithoutCallingServiceIfParserReturnsInvalid(): void
    {
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockParser->method('parseBody')->willReturn(["Unit Test","Another"]);
        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->never())->method('ask');

        $controller = new QuestionController($mockService, $mockParser);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Unit Test|Another');
        $controller->handle(new PostRequest("dummy"));
    }

    public function testHandleCallsServiceWithResultFromRequestHandler(): void
    {
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockResponse = new stdClass();
        $mockResponse->email = 'unit@test.com';
        $mockResponse->fullName = 'Unit Test';
        $mockResponse->message = 'Hello there!';
        $mockParser->method('parseBody')->willReturn($mockResponse);

        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->once())
                    ->method('ask')
                    ->willReturnCallback(function(string $email, string $fullName, string $message)
                    {
                        $this->assertEquals('unit@test.com', $email);
                        $this->assertEquals('Unit Test', $fullName);
                        $this->assertEquals('Hello there!', $message);
                    });

        $controller = new QuestionController($mockService, $mockParser);

        $controller->handle(new PostRequest("dummy"));
    }
}
