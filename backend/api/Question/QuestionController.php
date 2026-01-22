<?php

namespace BVZ\Question;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\QuerySpecParser;
use BVZ\Request\RequestHandler;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionController extends RequestHandler {

    function __construct(private QuestionParser $parser = new QuestionParser(),
        private QuestionService $service = new QuestionService(),
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
        $this->ask($post->body);
    }

    private function ask(object $body)
    {
        $parsed = $this->parser->parse($body);
        if (empty($parsed->errors))
        {
            $this->service->ask($parsed);
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
