<?php

namespace App\Member;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberService
{
    function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function register(MemberDTO $dto): bool
    {
        $transformArray = array(
            "{fullName}" => $dto->firstName . ' ' . $dto->lastName, "{addr1}" => $dto->address1,
            "{addr2}" => $dto->address2, "{email}" => $dto->email
        );
        $templateString =
            "\n Name: {fullName}\n Adresse: {addr1}\n Plz + Wohnort: {addr2}\nE-Mail: {email}\n";
        if ($dto->message != null)
        {
            $transformArray['{message}'] = $dto->message;
            $templateString = $templateString . "\nNachricht\n {message}";
        }
        $subject = strtr("Registrierung von {fullName}", $transformArray);
        $mail = new Email()
            ->from($dto->email)
            ->subject($subject)
            ->text(strtr($templateString, $transformArray));

        try {
            $this->mailer->send($mail);
        }
        catch (TransportExceptionInterface $e)
        {
            $this->logger->error("Message could not be sent. Mailer Error: {error}", [
                'error' => $e->getMessage()
            ]);
            return false;
        }
        return true;
    }
}
