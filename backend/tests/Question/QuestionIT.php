<?php

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use BVZ\Question\QuestionController;
use BVZ\Question\QuestionService;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionIT extends TestCase
{
    public function testSuccessfulRequest(): void
    {
        $body = json_decode('{"fullName":"Unit Test", "email": "unit@test.com", "message": "Hello there"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Frage von Unit Test', 'Hello there', 'unit@test.com')
            ->willReturn($mockMail);

        $service = new QuestionService($mockMailer, new LoggerFactory(true));

        $controller = new QuestionController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(204, http_response_code());
    }

    public function testReturnsWith500WhenMailingFails(): void
    {
        $body = json_decode('{"fullName":"Unit Test", "email": "unit@test.com", "message": "Hello there"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Frage von Unit Test', 'Hello there', 'unit@test.com')
            ->willReturn($mockMail);

        $service = new QuestionService($mockMailer, new LoggerFactory(true));

        $controller = new QuestionController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(500, http_response_code());
        $this->assertContains("X-Error-State: Could not process question!", xdebug_get_headers());
    }

    public function testFailsWhenMissingData(): void
    {
        $body = json_decode('{"fullName":"Unit Test", "email": "unit@test.com"}');

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->never())->method('configureMail');

        $service = new QuestionService($mockMailer, new LoggerFactory(true));

        $controller = new QuestionController($service);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("Required parameter 'message' is missing!");
        $controller->handle(new PostRequest("dummy", body: $body));
    }
}
