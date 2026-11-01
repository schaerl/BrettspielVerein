<?php

use App\Member\MemberService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberControllerTest extends WebTestCase
{
    public function testThrowsWhenCalledWithAGetRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', 'api/member');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testThrowsIfRequestBodyInvalid(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', 'api/member', ["email" => "unit@test.com", "WRONGNAME" => "Unit", "lastName" => "Test", "address1" => "UnitStreet 1", "address2" => "1111 Unity", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testProducesMailAndReturns204WhenValid(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', 'api/member', ["email" => "unit@test.com", "firstName" => "Unit", "lastName" => "Test", "address1" => "UnitStreet 1", "address2" => "1111 Unity", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertEmailCount(1);
    }

    public function testProducesMailAndReturns204WhenMessageEmpty(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', 'api/member', ["email" => "unit@test.com", "firstName" => "Unit", "lastName" => "Test", "address1" => "UnitStreet 1", "address2" => "1111 Unity"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertEmailCount(1);
    }

    public function testReturns500WhenServiceFails(): void
    {
        $client = static::createClient();

        $serviceMock = $this->createStub(MemberService::class);
        $serviceMock->method('register')->willReturn(false);
        $this->getContainer()->set('App\Member\MemberService', $serviceMock);

        $client->jsonRequest('POST', 'api/member', ["email" => "unit@test.com", "firstName" => "Unit", "lastName" => "Test", "address1" => "UnitStreet 1", "address2" => "1111 Unity", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
