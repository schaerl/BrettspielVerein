<?php

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use PHPUnit\Framework\TestCase;

use BVZ\Question\QuestionService;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionServiceTest extends TestCase
{
    public function testReturns500WhenMailingDoesntWork(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);
        $mailConfigurator = $this->createStub(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);

        $service = new QuestionService($mailConfigurator, new LoggerFactory(true));
        $service->ask("unit@test.com", "Unit Test", "Hello");

        $this->assertEquals(500, http_response_code());
        $this->assertContains('X-Error-State: Could not process question!', xdebug_get_headers());
    }
    
    public function testReturns204WhenMailingWorks(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mailConfigurator = $this->createMock(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);
        $mailConfigurator->expects($this->once())->method('configureMail')
            ->with('Frage von Unit Test', 'Hello', 'unit@test.com');

        $service = new QuestionService($mailConfigurator, new LoggerFactory(true));
        $service->ask("unit@test.com", "Unit Test", "Hello");
        $this->assertEquals(204, http_response_code());
    }
}
