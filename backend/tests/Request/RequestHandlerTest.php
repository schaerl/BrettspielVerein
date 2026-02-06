<?php

use BVZ\Request\RequestSpecParser;
use BVZ\Request\Request;
use BVZ\Request\RequestHandler;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class RequestHandlerTest extends TestCase
{
    public function testHandleTriggersRequest() {
        $handler = new class ($this->createStub(RequestSpecParser::class)) extends RequestHandler {
            public function __construct(RequestSpecParser $requestSpecParser)
            {
                parent::__construct($requestSpecParser);
            }
            function handleGet($dummy){}
            function handlePost($dummy){}
        };

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->expects($this->once())->method('trigger')->with($handler);

        $handler->handle($mockRequest);
    }
}
