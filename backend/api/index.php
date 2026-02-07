<?php

use BVZ\Env;
use BVZ\Events\EventController;
use BVZ\Logging\LoggerFactory;
use BVZ\Member\MemberController;
use BVZ\Newsletter\NewsletterController;
use BVZ\Question\QuestionController;
use BVZ\Request\RequestException;
use BVZ\Request\RequestFactory;

require_once __DIR__ . "/../vendor/autoload.php";

$logger = (new LoggerFactory())->getLogger('index.php');

if (Env::isDevEnv()){
    // Makes errors not output an error text, but instead return 500 when set to false
    ini_set('display_errors', Env::isDevEnv());
    header('Access-Control-Expose-Headers: X-Total-Count');
}

try {
    $request = (new RequestFactory())->getRequest();
    switch ($request->url)
    {
        case '/api/newsletter':
            (new NewsletterController())->handle($request);
            return;
        case '/api/question':
            (new QuestionController())->handle($request);
            return;
        case '/api/member':
            (new MemberController())->handle($request);
            return;
        case '/api/events':
            (new EventController())->handle($request);
            return;
        default:
            http_response_code(404);
            return;
    }
}
catch (RequestException $e)
{
    $message = $e->getMessage();
    $logger->error("Error processing request: $message");
    header("X-Error-State: $message");
    http_response_code(400);
    return;
}
catch (\Exception $e)
{
    $logger->error($e->getMessage());
    $logger->error($e->getTraceAsString());
}
