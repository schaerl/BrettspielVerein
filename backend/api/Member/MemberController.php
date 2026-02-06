<?php

namespace BVZ\Member;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpecParser;
use BVZ\Request\RequestHandler;

require_once __DIR__ . "/../../vendor/autoload.php";

class MemberController extends RequestHandler {

    function __construct(private MemberParser $parser = new MemberParser(),
        private MemberService $service = new MemberService(),
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
        $this->register($post->body);
    }

    private function register(object $body)
    {
        $parsed = $this->parser->parse($body);
        if (empty($parsed->errors))
        {
            $this->service->register($parsed);
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
