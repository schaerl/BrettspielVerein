<?php

namespace BVZ\Events;

use BVZ\Request\GetRequest;
use BVZ\Request\PostRequest;
use BVZ\Request\RequestSpec;
use BVZ\Request\RequestSpecParser;
use BVZ\Request\RequestHandler;

require_once __DIR__ . "/../../vendor/autoload.php";

class EventController extends RequestHandler {

    function __construct(
        private EventService $service = new EventService(),
        RequestSpecParser $requestSpecParser = new RequestSpecParser()
    )
    {
        parent::__construct($requestSpecParser);
    }

    function handleGet(GetRequest $get): void
    {
        // We only have one operation for now, therefore we don't do any explicit
        // checks for what to call.
        $params = $this->parseRequest(
            (new RequestSpec())
                ->withNumber('page', default: 1)
                ->withNumber('pageSize', default: 3),
            $get);
        $this->getEvents($params->page, $params->pageSize);
    }

    function handlePost(PostRequest $post): void
    {
        http_response_code(405);
        header("X-Error-State: POST not supported", false);
        return;
    }

    private function getEvents(int $page, int $pageSize): void
    {
        $this->service->getEvents($page, $pageSize);
    }
}
