<?php

use App\Newsletter\NewsletterService;
use App\Newsletter\NewsletterDTO;
use App\Newsletter\SubscribeStatus;
use App\Newsletter\SubscriberRepository;
use App\Newsletter\UnsubscribeDTO;
use App\Newsletter\UnsubscribeStatus;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterServiceTest extends KernelTestCase
{
    public function testReturnsSuccessButLogsWhenMailingDoesntWork(): void
    {
        $mockRepository = $this->createMock(SubscriberRepository::class);
        $mockRepository->expects($this->once())->method('signUp');

        $mailer = $this->createStub(MailerInterface::class);
        $mailer->method('send')->willThrowException(new TransportException());

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->subscribe(new NewsletterDTO("unit@test.com"));

        $this->assertEquals(SubscribeStatus::SUCCESS, $result);
    }

    public function testReturnsErrorWhenDbSignupThrows(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('signUp')->willThrowException(new Exception("Dummy"));

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->never())->method('send');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->subscribe(new NewsletterDTO("unit@test.com"));

        $this->assertEquals(SubscribeStatus::ERROR, $result);
    }

    public function testReturnsSuccessWhenSignupWorks(): void
    {
        $mockRepository = $this->createMock(SubscriberRepository::class);
        $mockRepository->expects($this->once())->method('signUp');

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $logger = $this->createStub(LoggerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->subscribe(new NewsletterDTO("unit@test.com"));

        $this->assertEquals(SubscribeStatus::SUCCESS, $result);
    }

    public function testReturnsAlreadyRegisteredWhenEmailAlreadyExists(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('signUp')->willThrowException($this->createStub(UniqueConstraintViolationException::class));

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->never())->method('send');

        $logger = $this->createStub(LoggerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->subscribe(new NewsletterDTO("unit@test.com"));

        $this->assertEquals(SubscribeStatus::ALREADY_REGISTERED, $result);
    }

    public function testReturnsSuccessWhenEmailDeleted(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('unsubscribe')->willReturn(UnsubscribeStatus::SUCCESSFULLY_DELETED);

        $mailer = $this->createStub(MailerInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->unsubscribe(new UnsubscribeDTO("unit@test.com", "token"));

        $this->assertEquals(UnsubscribeStatus::SUCCESSFULLY_DELETED, $result);
    }

    public function testReturnsTokenAlreadyDeltedWhenEmailAlreadyDeleted(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('unsubscribe')->willReturn(UnsubscribeStatus::ALREADY_DELETED);

        $mailer = $this->createStub(MailerInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->unsubscribe(new UnsubscribeDTO("unit@test.com", "token"));

        $this->assertEquals(UnsubscribeStatus::ALREADY_DELETED, $result);
    }

    public function testReturnsTokenWrongWhenTokenWrong(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('unsubscribe')->willReturn(UnsubscribeStatus::TOKEN_WRONG);

        $mailer = $this->createStub(MailerInterface::class);
        $logger = $this->createStub(LoggerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $result = $service->unsubscribe(new UnsubscribeDTO("unit@test.com", "token"));

        $this->assertEquals(UnsubscribeStatus::TOKEN_WRONG, $result);
    }

    public function testUnsubscribeThrowsWhenRepositoryThrows(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mockRepository->method('unsubscribe')->willThrowException(new \Exception("Dummy"));

        $mailer = $this->createStub(MailerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error');

        $service = new NewsletterService($mockRepository, $mailer, $logger);

        $this->expectException(\Exception::class);
        $service->unsubscribe(new UnsubscribeDTO("unit@test.com", "token"));
    }

    public function testAskTransformsInputCorrectly(): void
    {
        $mockRepository = $this->createStub(SubscriberRepository::class);
        $mailer = $this->getContainer()->get(MailerInterface::class);

        $service = new NewsletterService($mockRepository, $mailer, $this->createStub(LoggerInterface::class));

        $service->subscribe(new NewsletterDTO("unit@test.com"));

        self::assertEmailCount(1);
        $sentMail = self::getMailerMessage();
        self::assertEmailTextBodyContains($sentMail, "Ich melde mich hiermit an :)");
        self::assertEmailHeaderSame($sentMail, "From", "unit@test.com");
        self::assertEmailHeaderSame($sentMail, "To", "Versand Brettspielverein Zofingen <versand@brettspiel-zofingen.ch>");
        self::assertEmailSubjectContains($sentMail, "Newsletter-Abo von unit@test.com");
    }
}
