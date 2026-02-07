<?php

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use BVZ\Member\MemberDTO;
use PHPUnit\Framework\TestCase;

use BVZ\Member\MemberService;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberServiceTest extends TestCase
{
    public function testReturns500WhenMailingDoesntWork(): void 
    {
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(false);
        $mailConfigurator = $this->createStub(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);

        $service = new MemberService($mailConfigurator, new LoggerFactory(true));
        $service->register(MemberDTO::create("unit@test.com", "Unit", "Test", "Teststreet", "Testcity", "Hello"));

        $this->assertEquals(500, http_response_code());
        $this->assertContains('X-Error-State: Could not process member signup!', xdebug_get_headers());
    }
    
    public function testReturns204WhenMailingWorks(): void 
    {
        $expectedMessage = <<<TEST
        
         Name: Unit Test
         Adresse: Teststreet
         Plz + Wohnort: Testcity
        E-Mail: unit@test.com

        Nachricht
         Hello
        TEST;
        $mockMail = $this->createStub(PHPMailer::class);
        $mockMail->method('send')->willReturn(true);

        $mailConfigurator = $this->createMock(MailConfigurator::class);
        $mailConfigurator->method('configureMail')
            ->willReturn($mockMail);
        $mailConfigurator->expects($this->once())->method('configureMail')
            ->with('Registrierung von Unit Test', $expectedMessage, 'unit@test.com');

        $service = new MemberService($mailConfigurator, new LoggerFactory(true));
        $service->register(MemberDTO::create("unit@test.com", "Unit", "Test", "Teststreet", "Testcity", "Hello"));
        $this->assertEquals(204, http_response_code());
    }
}
