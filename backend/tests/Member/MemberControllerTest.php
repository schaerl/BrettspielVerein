<?php

use BVZ\Member\MemberController;
use BVZ\Member\MemberDTO;
use BVZ\Member\MemberParser;
use BVZ\Member\MemberService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberControllerTest extends TestCase
{

    public function testThrowsWhenCalledWithAGetRequest()
    {
        $mockParser = $this->createStub(MemberParser::class);
        $mockService = $this->createMock(MemberService::class);
        $mockService->expects($this->never())->method('register');

        $controller = new MemberController($mockParser, $mockService);

        $controller->handle(new GetRequest("dummy"));

        $this->assertEquals(405, http_response_code());
        $this->assertContains('X-Error-State: GET not supported', xdebug_get_headers());
    }

    public function testReturnsWithoutCallingServiceIfParserReturnsInvalid()
    {
        $mockParser = $this->createStub(MemberParser::class);
        $mockParser->method('parse')->willReturn(MemberDTO::error(["Unit Test","Another"]));
        $mockService = $this->createMock(MemberService::class);
        $mockService->expects($this->never())->method('register');


        $controller = new MemberController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));

        $this->assertEquals(400, http_response_code());
        $this->assertContains('X-Error-State: Unit Test', xdebug_get_headers());
        $this->assertContains('X-Error-State: Another', xdebug_get_headers());
    }

    public function testHandleCallsServiceWithResultFromRequestHandler()
    {
        $mockParser = $this->createStub(MemberParser::class);
        $mockParser->method('parse')->willReturn(MemberDTO::create('unit@test.com', "Unit", "Test", "Teststreet", "Testcity", "Hello there!"));

        $mockService = $this->createMock(MemberService::class);
        $mockService->expects($this->once())
                    ->method('register')
                    ->willReturnCallback(function(MemberDTO $dto)
                    {
                        $this->assertEquals('unit@test.com', $dto->email);
                        $this->assertEquals('Unit', $dto->firstName);
                        $this->assertEquals('Test', $dto->lastName);
                        $this->assertEquals('Teststreet', $dto->address1);
                        $this->assertEquals('Testcity', $dto->address2);
                        $this->assertEquals('Hello there!', $dto->message);
                    });

        $controller = new MemberController($mockParser, $mockService);

        $controller->handle(new PostRequest("dummy", new stdClass()));
    }
}
