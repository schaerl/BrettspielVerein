<?php

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use BVZ\Newsletter\NewsletterController;
use BVZ\Newsletter\NewsletterParser;
use BVZ\Newsletter\NewsletterService;
use BVZ\Request\PostRequest;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterIT extends TestCase
{
    public function testSuccessfulRequest()
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com')
            ->willReturn($mockMail);


        $parser = new NewsletterParser();
        $service = new NewsletterService($mockMailer, new LoggerFactory(true));

        $controller = new NewsletterController($parser, $service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(204, http_response_code());
    }

    public function testReturnsWith500WhenMailingFails()
    {
        $body = json_decode('{"email": "unit@test.com"}');

        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com')
            ->willReturn($mockMail);


        $parser = new NewsletterParser();
        $service = new NewsletterService($mockMailer, new LoggerFactory(true));

        $controller = new NewsletterController($parser, $service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(500, http_response_code());
        $this->assertContains("X-Error-State: Could not process registration request!", xdebug_get_headers());
    }

    public function testFailsWhenMissingData()
    {
        $body = json_decode('{"notemail": "unit@test.com"}');

        $mockMailer = $this->createMock(MailConfigurator::class);
        $mockMailer->expects($this->never())->method('configureMail');


        $parser = new NewsletterParser();
        $service = new NewsletterService($mockMailer, new LoggerFactory(true));

        $controller = new NewsletterController($parser, $service);

        $controller->handle(new PostRequest("dummy", body: $body));

        $this->assertEquals(400, http_response_code());
        $this->assertContains("X-Error-State: Email address not found!", xdebug_get_headers());
    }
}
