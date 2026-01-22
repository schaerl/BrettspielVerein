<?php

namespace BVZ\Newsletter;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\QuerySpecParser;
use BVZ\Request\RequestHandler;

require_once __DIR__ . "/../../vendor/autoload.php";

class NewsletterController extends RequestHandler {

    function __construct(private NewsletterParser $parser = new NewsletterParser(),
        private NewsletterService $service = new NewsletterService(),
        QuerySpecParser $querySpecParser = new QuerySpecParser()
    )
    {
        parent::__construct($querySpecParser);
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
}
