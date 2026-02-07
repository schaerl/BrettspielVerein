<?php

use BVZ\Newsletter\NewsletterController;
use BVZ\Newsletter\NewsletterDTO;
use BVZ\Newsletter\NewsletterService;
use BVZ\Request\DeleteRequest;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestException;
use BVZ\Request\RequestSpecParser;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterControllerTest extends TestCase
{
    public function testThrowsWhenCalledWithAGetRequest(): void
    {
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->never())->method('subscribe');

        $controller = new NewsletterController($mockService, $mockParser);

        $controller->handle(new GetRequest("dummy"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: GET not supported', xdebug_get_headers());
    }

    public function testReturnsWithoutCallingServiceIfParserReturnsInvalid(): void
    {
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockParser->method('parseBody')->willReturn(["Unit Test","Another"]);
        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->never())->method('subscribe');


        $controller = new NewsletterController($mockService, $mockParser);

        $this->expectException(RequestException::class);
        $controller->handle(new PostRequest("dummy"));
    }

    public function testHandleCallsServiceWithResultFromRequestHandler(): void
    {
        $email = 'unit@test.com';
        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockParams = new stdClass();
        $mockParams->email = $email;
        $mockParser->method('parseBody')->willReturn($mockParams);

        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->once())
                    ->method('subscribe')
                    ->willReturnCallback(function(string $email)
                    {
                        $this->assertEquals('unit@test.com', $email);
                    });

        $controller = new NewsletterController($mockService, $mockParser);

        $controller->handle(new PostRequest("dummy"));
    }

    public function testHandleCallsServiceToUnsubscribeWithResultFromRequestHandler(): void
    {
        $email = 'unit@test.com';
        $uuid = 'uuid4';

        $mockParser = $this->createStub(RequestSpecParser::class);
        $mockRequest = new stdClass();
        $mockRequest->email = $email;
        $mockRequest->token = $uuid;
        $mockParser->method('parseQuery')->willReturn($mockRequest);

        $mockService = $this->createMock(NewsletterService::class);
        $mockService->expects($this->once())
                    ->method('unsubscribe')
                    ->with($email, $uuid);

        $controller = new NewsletterController($mockService, $mockParser);

        $controller->handle(new DeleteRequest("dummy"));
    }
}
