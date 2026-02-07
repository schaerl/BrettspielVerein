<?php

namespace BVZ\Question;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpecParser;
use BVZ\Request\RequestHandler;
use BVZ\Request\RequestSpec;

require_once __DIR__ . "/../../vendor/autoload.php";

class QuestionController extends RequestHandler {

    function __construct(
        private QuestionService $service = new QuestionService(),
        RequestSpecParser $requestSpecParser = new RequestSpecParser()
    )
    {
        parent::__construct($requestSpecParser);
    }

    function handleGet(GetRequest $get): void
    {
        http_response_code(405);
        header("X-Error-State: GET not supported", false);
        return;
    }

    function handlePost(PostRequest $post): void
    {
        // We only have one operation for now, therefore we don't do any explicit
        // checks for what to call.
        $this->ask($post);
    }

    private function ask(PostRequest $request): void
    {
        $params = $this->parseRequestBody(
            (new RequestSpec())
                ->withString('email', true)
                ->withString('fullName', true)
                ->withString('message', true),
            $request);
        $this->service->ask($params->email, $params->fullName, $params->message);
        return;
    }
}
