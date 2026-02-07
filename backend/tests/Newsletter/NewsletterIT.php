<?php

use BVZ\BvzRepositoryException;
use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use BVZ\Newsletter\NewsletterController;
use BVZ\Newsletter\NewsletterRepository;
use BVZ\Newsletter\NewsletterService;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterIT extends TestCase
{
    public function testSuccessfulRequest(): void
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com')
            ->willReturn($mockMail);

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')->willReturn(true);

        $service = new NewsletterService($mockMailer, $mockRepository, new LoggerFactory(true));

        $controller = new NewsletterController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(204, http_response_code());
    }

    public function testStillSucceedsWith204WhenMailingFails(): void
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com')
            ->willReturn($mockMail);

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')->willReturn(true);


        $service = new NewsletterService($mockMailer, $mockRepository, new LoggerFactory(true));

        $controller = new NewsletterController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(204, http_response_code());
    }

    public function testFailsWith500WhenDbSignupFails(): void
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->never())->method('configureMail');

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')
            ->willThrowException(new BvzRepositoryException("Dummy"));


        $service = new NewsletterService($mockMailer, $mockRepository, new LoggerFactory(true));

        $controller = new NewsletterController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(500, http_response_code());
        $this->assertContains('X-Error-State: Could not process signup request!', xdebug_get_headers());
    }

    public function testFailsWith409WhenEmailAlreadyExists(): void
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->never())->method('configureMail');

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')
            ->willReturn(false);


        $service = new NewsletterService($mockMailer, $mockRepository, new LoggerFactory(true));

        $controller = new NewsletterController($service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(409, http_response_code());
    }

    public function testFailsWhenMissingData(): void
    {
        $body = json_decode('{"notemail": "unit@test.com"}');

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->never())->method('configureMail');

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->never())->method('signUp');


        $service = new NewsletterService($mockMailer, $mockRepository, new LoggerFactory(true));

        $controller = new NewsletterController($service);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage("Required parameter 'email' is missing!");
        $controller->handle(new PostRequest("dummy", body: $body));

    }
}
