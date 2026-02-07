<?php

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpecParser;
use BVZ\Request\Request;
use BVZ\Request\RequestHandler;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class RequestHandlerTest extends TestCase
{
    public function testHandleTriggersRequest(): void {
        $handler = new class ($this->createStub(RequestSpecParser::class)) extends RequestHandler {
            public function __construct(RequestSpecParser $requestSpecParser)
            {
                parent::__construct($requestSpecParser);
            }
            function handleGet(GetRequest $dummy): void {}
            function handlePost(PostRequest $dummy): void {}
        };

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->once())->method('trigger')->with($handler);

        $handler->handle($mockRequest);
    }
}
