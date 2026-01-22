<?php

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestHandler;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class RequestTest extends TestCase
{
    public function testGetRequestTriggersHandlersHandleGet() {
        $mockHandler = $this->createMock(RequestHandler::class);
        $mockHandler->expects($this->once())->method('handleGet');

        (new GetRequest("dummy"))->trigger($mockHandler);
    }

    public function testPostRequestTriggersHandlersHandlePost() {
        $mockHandler = $this->createMock(RequestHandler::class);
        $mockHandler->expects($this->once())->method('handlePost');

        (new PostRequest("dummy"))->trigger($mockHandler);
    }
}
