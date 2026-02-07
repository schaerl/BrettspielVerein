<?php

namespace BVZ;

use PHPMailer\PHPMailer\PHPMailer;
use BVZ\Env;
use BVZ\Logging\LoggerFactory;
use Monolog\Logger;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . "/../vendor/autoload.php";

class MailConfigurator
{
    private readonly Logger $logger;

    public function __construct(
        LoggerFactory $loggerFactory = new LoggerFactory()
    )
    {
        $this->logger = $loggerFactory->getLogger('MailConfigurator');
    }

    private function baseConfigure(): PHPMailer
    {
        $mail = new PHPMailer();
        $mail->Debugoutput = $this->logger;

        $mail->isSMTP();
        $mail->Host = Env::get(Env::MAIL_HOST); // Specify main and backup SMTP servers
        $mail->Port = intval(Env::get(Env::MAIL_PORT)); // TCP port to connect to

        $mail->addAddress('versand@brettspiel-zofingen.ch', 'Versand Brettspielverein Zofingen'); // Add a recipient
        $mail->isHTML(false);
        return $mail;
    }


    function configureMail(string $subject, string $message, string $from): PHPMailer
    {
        $mail = MailConfigurator::baseConfigure();
        $profile = Env::get(Env::PROFILE);
        if ($profile == Env::DEV_PROFILE) {
            $this->configureDummy($mail);
        } else if ($profile == Env::PRODUCTION_PROFILE) {
            $this->configureProduction($mail);
        } else {
            throw new \Exception("Unexpected profile: $profile");
        }
        //Recipients
        $mail->setFrom($from, 'BVZ Mailer');
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail;
    }

    private function configureDummy(PHPMailer $mail): void
    {
        $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
    }

    private function configureProduction(PHPMailer $mail): void
    {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'versand@brettspiel-zofingen.ch'; // SMTP username
        $mail->Password = Env::get(Env::MAIL_PW); // SMTP password
        $mail->SMTPSecure = 'ssl'; // Enable TLS encryption, [ICODE]ssl[/ICODE] also accepted
    }
}
