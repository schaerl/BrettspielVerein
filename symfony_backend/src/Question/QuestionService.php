<?php

namespace App\Question;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionService
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function ask(QuestionDTO $dto): bool
    {
        $transformArray = array("{fullName}" => $dto->fullName);
        $subject = strtr("Frage von {fullName}", $transformArray);

        $email = new Email()
            ->from($dto->email)
            ->subject($subject)
            ->text($dto->message);

        try {
            $this->mailer->send($email);
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
