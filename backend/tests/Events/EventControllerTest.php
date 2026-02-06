<?php

use BVZ\Events\EventController;
use BVZ\Events\EventService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpecParser;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class EventControllerTest extends TestCase
{

    public function testThrowsWhenCalledWithAPostRequest()
    {
        $mockService = $this->createMock(EventService::class);
        $mockService->expects($this->never())->method($this->anything());

        $controller = new EventController($mockService);

        $controller->handle(new PostRequest("dummy"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: POST not supported', xdebug_get_headers());
    }

    public function testGetEventCallsServiceWithCorrectParams()
    {
        $mockSpecParser = $this->createStub(RequestSpecParser::class);
        $mockRequest = new stdClass();
        $mockRequest->page = 0;
        $mockRequest->pageSize = 10;
        $mockSpecParser->method('parse')->willReturn($mockRequest);

        $mockService = $this->createMock(EventService::class);
        $mockService->expects($this->once())->method('getEvents')->with(0, 10);


        $controller = new EventController($mockService, $mockSpecParser);

        $controller->handle(new GetRequest("dummy"));
    }
}
