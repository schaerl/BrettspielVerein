<?php

use BVZ\Newsletter\NewsletterController;
use BVZ\Newsletter\NewsletterDTO;
use BVZ\Newsletter\NewsletterParser;
use BVZ\Newsletter\NewsletterService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterControllerTest extends TestCase
{
    public function testThrowsWhenCalledWithAGetRequest()
    {
        $mockParser = $this->createStub(NewsletterParser::class);
        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->never())->method('subscribe');

        $controller = new NewsletterController($mockParser, $mockService);

        $controller->handle(new GetRequest("dummy"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: GET not supported', xdebug_get_headers());
    }

    public function testReturnsWithoutCallingServiceIfParserReturnsInvalid()
    {
        $mockParser = $this->createStub(NewsletterParser::class);
        $mockParser->method('parse')->willReturn(NewsletterDTO::error(["Unit Test","Another"]));
        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->never())->method('subscribe');


        $controller = new NewsletterController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));

        $this->assertEquals(400, http_response_code());
        $this->assertContains('X-Error-State: Unit Test', xdebug_get_headers());
        $this->assertContains('X-Error-State: Another', xdebug_get_headers());
    }

    public function testHandleCallsServiceWithResultFromRequestHandler()
    {
        $email = 'unit@test.com';
        $mockParser = $this->createStub(NewsletterParser::class);
        $mockParser->method('parse')->willReturn(NewsletterDTO::create($email));

        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->once())
                    ->method('subscribe')
                    ->willReturnCallback(function(NewsletterDTO $dto)
                    {
                        $this->assertEquals('unit@test.com', $dto->email);
                    });

        $controller = new NewsletterController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));
    }
}
