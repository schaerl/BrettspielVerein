<?php

use App\Question\QuestionService as QuestionQuestionService;
use BVZ\Question\QuestionController;
use BVZ\Question\QuestionDTO;
use BVZ\Question\QuestionParser;
use BVZ\Question\QuestionService;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionControllerTest extends WebTestCase
{
    public function testThrowsWhenCalledWithAGetRequest(): void
    {
        $client = static::createClient();

        $client->request('GET', 'api/question');

        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testThrowsIfRequestBodyInvalid(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', 'api/question', ["email" => "unit@test.com", "wrong-param" => "Unit Test", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testProducesMailAndReturns204WhenValid(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', 'api/question', ["email" => "unit@test.com", "fullName" => "Unit Test", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        self::assertEmailCount(1);
    }

    public function testReturns500WhenServiceFails(): void
    {
        $client = static::createClient();

        $serviceMock = $this->createStub(QuestionQuestionService::class);
        $serviceMock->method('ask')->willReturn(false);
        $this->getContainer()->set('App\Question\QuestionService', $serviceMock);

        $client->jsonRequest('POST', 'api/question', ["email" => "unit@test.com", "fullName" => "Unit Test", "message" => "Hello"]);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
