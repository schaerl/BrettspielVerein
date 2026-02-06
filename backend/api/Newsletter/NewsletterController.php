<?php

namespace BVZ\Newsletter;

use BVZ\Request\DeleteRequest;
use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpec;
use BVZ\Request\RequestSpecParser;
use BVZ\Request\RequestHandler;
use Override;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterController extends RequestHandler {

    function __construct(private NewsletterParser $parser = new NewsletterParser(),
        private NewsletterService $service = new NewsletterService(),
        RequestSpecParser $requestSpecParser = new RequestSpecParser()
    )
    {
        parent::__construct($requestSpecParser);
    }

    function handleGet(GetRequest $get)
    {
        http_response_code(405);
        header("X-Error-State: GET not supported", false);
        return;
    }

    function handlePost(PostRequest $post)
    {
        // We only have one operation for now, therefore we don't do any explicit
        // checks for what to call.
        $this->subscribe($post->body);
    }

    #[Override]
    function handleDelete(DeleteRequest $delete)
    {
        $params = $this->parseRequest(
            (new RequestSpec())
                ->withString('email', true)
                ->withString('token', true),
            $delete);
        $this->unsubscribe($params->email, $params->token);
    }

    private function subscribe(object $body)
    {
        $parsed = $this->parser->parse($body);
        if (empty($parsed->errors))
        {
            $this->service->subscribe($parsed);
            return;
        }
        else
        {
            foreach($parsed->errors as $error)
            {
                header("X-Error-State: $error", false);
            }
            http_response_code(400);
            return;
        }
    }

    private function unsubscribe(string $email, string $token)
    {
        $this->service->unsubscribe($email, $token);
    }
}
