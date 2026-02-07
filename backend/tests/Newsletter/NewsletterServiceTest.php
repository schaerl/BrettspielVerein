<?php

use BVZ\BvzRepositoryException;
use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use BVZ\Newsletter\NewsletterRepository;
use PHPUnit\Framework\TestCase;

use BVZ\Newsletter\NewsletterService;
use BVZ\Newsletter\UnsubscribeStatus;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterServiceTest extends TestCase
{
    public function testReturns204ButLogsWhenMailingDoesntWork(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);
        $mailConfigurator = $this->createStub(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);

        $mockRepository = $this->createStub(NewsletterRepository::class);
        $mockRepository->method('signUp')->willReturn(true);
        $loggerFactory = new LoggerFactory(true);

        $service = new NewsletterService($mailConfigurator, $mockRepository, $loggerFactory);
        $service->subscribe('unit@test.com');

        $this->assertEquals(204, http_response_code());

        $handler = $loggerFactory->getTestHandler();
        $this->assertTrue($handler->hasErrorThatPasses(fn ($record) => 
            str_contains($record->formatted, 'Message could not be sent. Mailer Error:')
        ));
    }

    public function testReturns500WhenDbSignupDoesntWork(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);
        $mailConfigurator = $this->createStub(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);

        $mockRepository = $this->createStub(NewsletterRepository::class);
        $mockRepository->method('signUp')->willThrowException(new BvzRepositoryException("Dummy"));

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->subscribe('unit@test.com');

        $this->assertEquals(500, http_response_code());
        $this->assertContains('X-Error-State: Could not process signup request!', xdebug_get_headers());
    }
    
    public function testReturns204SignupWorks(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mailConfigurator = $this->createMock(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);
        $mailConfigurator->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com');

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')->willReturn(true);

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->subscribe('unit@test.com');
        $this->assertEquals(204, http_response_code());
    }

    public function testReturns409WhenEmailAlreadyExists(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mailConfigurator = $this->createMock(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);
        $mailConfigurator->expects($this->once())->method('configureMail')
            ->with('Newsletter-Abo von unit@test.com', 'Ich melde mich hiermit an :)', 'unit@test.com');

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('signUp')->willReturn(true);

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->subscribe('unit@test.com');
        $this->assertEquals(204, http_response_code());
    }

    public function testReturns204WhenEmailDeleted(): void
    {
        $mailConfigurator = $this->createStub(MailConfigurator::class);

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('unsubscribe')->willReturn(UnsubscribeStatus::SUCCESSFULLY_DELETED);

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->unsubscribe("unit@test.com", "uuid4");
        $this->assertEquals(200, http_response_code());
    }

    public function testReturns410WhenEmailAlreadyDeleted(): void
    {
        $mailConfigurator = $this->createStub(MailConfigurator::class);

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('unsubscribe')->willReturn(UnsubscribeStatus::ALREADY_DELETED);

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->unsubscribe("unit@test.com", "uuid4");
        $this->assertEquals(410, http_response_code());
    }

    public function testReturns403WhenTokenWrong(): void 
    {
        $mailConfigurator = $this->createStub(MailConfigurator::class);

        $mockRepository = $this->createMock(NewsletterRepository::class);
        $mockRepository->expects($this->once())->method('unsubscribe')->willReturn(UnsubscribeStatus::TOKEN_WRONG);

        $service = new NewsletterService($mailConfigurator, $mockRepository, new LoggerFactory(true));
        $service->unsubscribe("unit@test.com", "uuid4");
        $this->assertEquals(403, http_response_code());
    }
}
