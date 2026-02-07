<?php

namespace BVZ\Member;

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use Monolog\Logger;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberService
{
    private readonly Logger $logger;

    function __construct(private MailConfigurator $mailConfigurator = new MailConfigurator(),
        LoggerFactory $loggerFactory = new LoggerFactory()
    )
    {
        $this->logger = $loggerFactory->getLogger('MemberService');
    }

    public function register(MemberDTO $dto): void
    {
        $transformArray = array(
            "{fullName}" => $dto->{"firstName"} . ' ' . $dto->{"lastName"}, "{addr1}" => $dto->{"address1"},
            "{addr2}" => $dto->{"address2"}, "{email}" => $dto->{"email"}, "{message}" => $dto->{"message"}
        );
        $templateString =
            "\n Name: {fullName}\n Adresse: {addr1}\n Plz + Wohnort: {addr2}\nE-Mail: {email}\n\nNachricht\n {message}";
        $subject = strtr("Registrierung von {fullName}", $transformArray);
        $mail = $this->mailConfigurator->configureMail($subject, strtr($templateString, $transformArray), $dto->{"email"});
        

        $success = $mail->send();
        if (!$success) {
            $this->logger->error("Message could not be sent. Mailer Error: " . $mail->ErrorInfo);
            header("X-Error-State: Could not process member signup!");
            http_response_code(500);
        } else {
            http_response_code(204);
        }
    }
}
