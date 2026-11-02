<?php

namespace App\Newsletter;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterService
{

    function __construct(
        private readonly SubscriberRepository $repository,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function subscribe(NewsletterDTO $dto): SubscribeStatus
    {
        try
        {
            $this->repository->signUp($dto->email, Uuid::uuid4()->toString());
        }
        catch (UniqueConstraintViolationException $e)
        {
            $this->logger->error("Persisting the subscriber failed. Error: {error}", ['error' => $e->getMessage()]);
            return SubscribeStatus::ALREADY_REGISTERED;
        }

        $transformArray = array("{mail}" => $dto->email);
        $subject = strtr("Newsletter-Abo von {mail}", $transformArray);
        $message = "Ich melde mich hiermit an :)";
        $email = new Email()
            ->from($dto->email)
            ->subject($subject)
            ->text($message);

        try {
            $this->mailer->send($email);
        }
        catch (TransportExceptionInterface $e)
        {
            $this->logger->error("Message could not be sent. Mailer Error: {error}", [
                'error' => $e->getMessage()
            ]);
            return SubscribeStatus::ERROR;
        }
        return SubscribeStatus::SUCCESS;
    }

    public function unsubscribe(UnsubscribeDTO $unsubsribeDTO): UnsubscribeStatus
    {
        try
        {
            return $this->repository->unsubscribe($unsubsribeDTO->email, $unsubsribeDTO->token);
        }
        catch (\Exception $e)
        {
            $this->logger->error("Deleting the subscriber failed. Error: {error}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
