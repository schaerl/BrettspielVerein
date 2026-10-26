<?php

use App\Question\QuestionDTO;
use App\Question\QuestionService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\Exception\TransportException; use Symfony\Component\Mailer\MailerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionServiceTest extends KernelTestCase
{
    use MailerAssertionsTrait;
    public function testAskReturnsTrueWhenMailerDoesNotThrow(): void
    {
        $mockMailer = $this->createStub(MailerInterface::class);

        $service = new QuestionService($mockMailer, $this->createStub(LoggerInterface::class));
        $result = $service->ask(new QuestionDTO("unit@test.com", "Unit Test", "Hello"));

        $this->assertTrue($result);
    }

    public function testAskReturnsFalseWhenMailerThrows(): void
    {
        $mockMailer = $this->createStub(MailerInterface::class);
        $mockMailer->method('send')->willThrowException(new TransportException());
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger
            ->expects($this->once())
            ->method('error');

        $service = new QuestionService($mockMailer, $mockLogger);
        $result = $service->ask(new QuestionDTO("unit@test.com", "Unit Test", "Hello"));

        $this->assertFalse($result);
    }

    public function testAskTransformsInputCorrectly(): void
    {
        $mailer = $this->getContainer()->get(MailerInterface::class);
        $service = new QuestionService($mailer, $this->createStub(LoggerInterface::class));

        $service->ask(new QuestionDTO("unit@test.com", "Unit Test", "Hello"));

        self::assertEmailCount(1);
        $sentMail = self::getMailerMessage();
        self::assertEmailTextBodyContains($sentMail, "Hello");
        self::assertEmailHeaderSame($sentMail, "From", "unit@test.com");
        self::assertEmailHeaderSame($sentMail, "To", "Versand Brettspielverein Zofingen <versand@brettspiel-zofingen.ch>");
        self::assertEmailSubjectContains($sentMail, "Frage von Unit Test");
    }
}
