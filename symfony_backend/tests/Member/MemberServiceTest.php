<?php

use App\Member\MemberDTO;
use App\Member\MemberService;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberServiceTest extends KernelTestCase
{
    public function testRegisterReturnsFalseWhenMailerThrows(): void
    {
        $mockMailer = $this->createStub(MailerInterface::class);
        $mockMailer->method('send')->willThrowException(new TransportException());
        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger
            ->expects($this->once())
            ->method('error');

        $service = new MemberService($mockMailer, $mockLogger);
        $result = $service->register(new MemberDTO("unit@test.com", "Unit", "Test", "addr1", "Addr2"));

        $this->assertFalse($result);
    }

    public function testReturnsTrueWhenMailingWorks(): void
    {
        $mockMailer = $this->createStub(MailerInterface::class);
        $mockLogger = $this->createStub(LoggerInterface::class);

        $service = new MemberService($mockMailer, $mockLogger);
        $result = $service->register(new MemberDTO("unit@test.com", "Unit", "Test", "addr1", "Addr2"));

        $this->assertTrue($result);
    }

    public function testRegisterTransformsInputcorrectly(): void
    {
        $expectedMessage = <<<TEST

         Name: Unit Test
         Adresse: Teststreet
         Plz + Wohnort: Testcity
        E-Mail: unit@test.com

        Nachricht
         Hello
        TEST;
        $mailer = $this->getContainer()->get(MailerInterface::class);
        $service = new MemberService($mailer, $this->createStub(LoggerInterface::class));
        $service->register(new MemberDTO("unit@test.com", "Unit", "Test", "Teststreet", "Testcity", "Hello"));

        self::assertEmailCount(1);
        $sentMail = self::getMailerMessage();
        self::assertEmailTextBodyContains($sentMail, $expectedMessage);
        self::assertEmailHeaderSame($sentMail, "From", "unit@test.com");
        self::assertEmailHeaderSame($sentMail, "To", "Versand Brettspielverein Zofingen <versand@brettspiel-zofingen.ch>");
        self::assertEmailSubjectContains($sentMail, "Registrierung von Unit Test");
    }
}
