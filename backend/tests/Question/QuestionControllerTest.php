<?php

use BVZ\Question\QuestionController;
use BVZ\Question\QuestionDTO;
use BVZ\Question\QuestionParser;
use BVZ\Question\QuestionService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionControllerTest extends TestCase
{
    public function testThrowsWhenCalledWithAGetRequest()
    {
        $mockParser = $this->createStub(QuestionParser::class);
        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->never())->method('ask');

        $controller = new QuestionController($mockParser, $mockService);

        $controller->handle(new GetRequest("Test"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: GET not supported', xdebug_get_headers());
    }

    public function testReturnsWithoutCallingServiceIfParserReturnsInvalid()
    {
        $mockParser = $this->createStub(QuestionParser::class);
        $mockParser->method('parse')->willReturn(QuestionDTO::error(["Unit Test","Another"]));
        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->never())->method('ask');

        $controller = new QuestionController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));

        $this->assertEquals(400, http_response_code());
        $this->assertContains('X-Error-State: Unit Test', xdebug_get_headers());
        $this->assertContains('X-Error-State: Another', xdebug_get_headers());
    }

    public function testHandleCallsServiceWithResultFromRequestHandler()
    {
        $mockParser = $this->createStub(QuestionParser::class);
        $mockParser->method('parse')->willReturn(QuestionDTO::create('unit@test.com', "Unit Test", "Hello there!"));

        $mockService = $this->createMock(QuestionService::class);
        $mockService->expects($this->once())
                    ->method('ask')
                    ->willReturnCallback(function(QuestionDTO $dto)
                    {
                        $this->assertEquals('unit@test.com', $dto->email);
                        $this->assertEquals('Unit Test', $dto->fullName);
                        $this->assertEquals('Hello there!', $dto->message);
                    });

        $controller = new QuestionController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));
    }
}
