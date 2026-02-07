<?php

namespace BVZ\Question;

use BVZ\Logging\LoggerFactory;
use BVZ\MailConfigurator;
use Monolog\Logger;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionService
{
    private readonly Logger $logger;

    function __construct(private MailConfigurator $mailConfigurator = new MailConfigurator(),
        LoggerFactory $loggerFactory = new LoggerFactory()
    )
    {
        $this->logger = $loggerFactory->getLogger('QuestionService');
    }

    public function ask(QuestionDTO $dto): void
    {
        $transformArray = array("{fullName}" => $dto->fullName);
        $subject = strtr("Frage von {fullName}", $transformArray);
        $mail =  $this->mailConfigurator->configureMail($subject, $dto->message, $dto->email);

        $success = $mail->send();
        if (!$success) {
            $this->logger->error("Message could not be sent. Mailer Error: " . $mail->ErrorInfo);
            header("X-Error-State: Could not process question!");
            http_response_code(500);
        } else {
            http_response_code(204);
        }
    }
}
