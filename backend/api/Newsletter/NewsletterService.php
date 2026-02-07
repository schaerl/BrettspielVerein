<?php

namespace BVZ\Newsletter;

use BVZ\BvzRepositoryException;
use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use Monolog\Logger;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterService
{
    private readonly Logger $logger;

    function __construct(private MailConfigurator $mailConfigurator = new MailConfigurator(),
        private readonly NewsletterRepository $repository = new NewsletterRepository(),
        LoggerFactory $loggerFactory = new LoggerFactory()
    )
    {
        $this->logger = $loggerFactory->getLogger('NewsletterService');
    }

    public function subscribe(NewsletterDTO $dto): void
    {
        try
        {
            $success = $this->repository->signUp($dto->email, Uuid::uuid4()->toString());
            if (!$success){
                header("X-Error-State: Email already registered");
                http_response_code(409);
                return;
            }
        }
        catch (BvzRepositoryException)
        {
            header("X-Error-State: Could not process signup request!");
            http_response_code(500);
            return;
        }

        $transformArray = array("{mail}" => $dto->email);
        $subject = strtr("Newsletter-Abo von {mail}", $transformArray);
        $message = "Ich melde mich hiermit an :)";
        $mail = $this->mailConfigurator->configureMail($subject, $message, $dto->email);

        $success = $mail->send();
        if (!$success) {
            $this->logger->error("Message could not be sent. Mailer Error: " . $mail->ErrorInfo);
        }
        http_response_code(204);
    }

    public function unsubscribe(string $email, string $token): void
    {
        switch($this->repository->unsubscribe($email, $token))
        {
            case UnsubscribeStatus::SUCCESSFULLY_DELETED:
                http_response_code(200);
                return;
            case UnsubscribeStatus::ALREADY_DELETED:
                http_response_code(410);
                return;
            case UnsubscribeStatus::TOKEN_WRONG:
                http_response_code(403);
                return;
        }
    }
}
